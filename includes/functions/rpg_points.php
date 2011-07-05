<?php
/**
*
* @package rpg
* @version $Id$
* @copyright (c) 2009-2010 Gorlum for http://supernova.ws
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* This function changes rpg_points for user
* You should ALWAYS use this function and NEVER directly change rpg_points by yourself
* Otherwise refferal system wouldn't work and no logs would be made
* "No logs" means you can never check if the user cheating with DM
*
* @package rpg
*
*/
function rpg_points_change($user_id, $change_type, $dark_matter, $comment = false, $already_changed = false)
{
  global $debug, $config, $dm_change_legit, $sn_data;

  if(!$user_id)
  {
    return false;
  }

  $dm_change_legit = true;
  $sn_data_dark_matter_db_name = $sn_data[RES_DARK_MATTER]['name'];
  if($already_changed)
  {
    $rows_affected = 1;
  }
  else
  {
    doquery("UPDATE {{users}} SET `{$sn_data_dark_matter_db_name}` = `{$sn_data_dark_matter_db_name}` + '{$dark_matter}' WHERE `id` = {$user_id} LIMIT 1;");
    $rows_affected = mysql_affected_rows();
  }

  if($rows_affected)
  {
    $page_url = mysql_real_escape_string($_SERVER['SCRIPT_NAME']);
    $comment = mysql_real_escape_string($comment);
    $row = doquery("SELECT username FROM {{users}} WHERE id = {$user_id} LIMIT 1;", '', true);
    $row['username'] = mysql_real_escape_string($row['username']);
    doquery(
      "INSERT INTO {{log_dark_matter}} (`log_dark_matter_username`, `log_dark_matter_reason`,
        `log_dark_matter_amount`, `log_dark_matter_comment`, `log_dark_matter_page`, `log_dark_matter_sender`)
      VALUES (
        '{$row['username']}',
        {$change_type},
        {$dark_matter},
        '{$comment}',
        '{$page_url}',
        {$user_id}
      );", true
    );


//    $debug->warning("Player ID {$user_id} Dark Matter was adjusted with {$dark_matter}. Reason: {$comment}", 'Dark Matter Change', 102);

    if($dark_matter>0)
    {
      $old_referral = doquery("SELECT * FROM {{referrals}} WHERE `id` = {$user_id} LIMIT 1;", '', true);
      if($old_referral['id'])
      {
        doquery("UPDATE {{referrals}} SET dark_matter = dark_matter + '$dark_matter' WHERE `id` = {$user_id} LIMIT 1;");
        $new_referral = doquery("SELECT * FROM {{referrals}} WHERE `id` = {$user_id} LIMIT 1;", '', true);

        $partner_bonus = floor($new_referral['dark_matter']/$config->rpg_bonus_divisor) - floor($old_referral['dark_matter']/$config->rpg_bonus_divisor);
        if($partner_bonus > 0)
        {
          rpg_points_change($new_referral['id_partner'], RPG_REFERRAL, $partner_bonus, "Incoming From Referral ID {$user_id}");
        }
      }
    }
  }
  else
  {
    $debug->warning("Error adjusting Dark Matter for player ID {$user_id} (Player Not Found) with {$dark_matter}. Reason: {$comment}", 'Dark Matter Change', 402);
  }

  $dm_change_legit = false;
  return $rows_affected;
}

function rpg_level_up(&$user, $type, $xp_to_add = 0)
{
  $q = 1.03;

  switch($type)
  {
    case RPG_STRUCTURE:
      $field_level = 'lvl_minier';
      $field_xp = 'xpminier';
      $xp = &$user['xpminier'];
      $b1 = 50;
      $comment = 'Level Up For Structure Building';
    break;

    case RPG_RAID:
      $field_level = 'lvl_raid';
      $field_xp = 'xpraid';
      $xp = &$user['xpraid'];
      $b1 = 10;
      $comment = 'Level Up For Raiding';
    break;
  }

  if($xp_to_add)
  {
    $xp += $xp_to_add;
    doquery("UPDATE `{{users}}` SET `{$field_xp}` = `{$field_xp}` + '{$xp_to_add}' WHERE `id` = '{$user['id']}' LIMIT 1;");
  }

  $level = $user[$field_level];
  while ($xp >= rpg_xp_for_level($level, $b1, $q))
  {
    $level++;
  }
  $level -= $user[$field_level];
  if($level > 0)
  {
    doquery("UPDATE `{{users}}` SET `{$field_level}` = `{$field_level}` + '{$level}' WHERE `id` = '{$user['id']}' LIMIT 1;");
    rpg_points_change($user['id'], $type, $level, $comment);
    $user[$field_level] += $level;
    $user[$sn_data_dark_matter_db_name] += $level;
  }
}

function rpg_xp_for_level($level, $b1, $q)
{
  return floor($b1 * (pow($q, $level) - 1)/($q - 1));
}

function rpg_get_miner_xp($level)
{
  return rpg_xp_for_level($level, 50, 1.03);
}

function RPG_get_raider_xp($level)
{
  return rpg_xp_for_level($level, 10, 1.03);
}

?>
