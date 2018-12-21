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

/**
 * @param int   $user_id
 * @param int   $change_type
 * @param float $dark_matter
 * @param bool  $comment
 * @param bool  $already_changed
 *
 * @return bool|int
 */
function rpg_points_change($user_id, $change_type, $dark_matter, $comment = false, $already_changed = false) {
  global $debug, $config, $dm_change_legit, $user;

  if(!$user_id) {
    return false;
  }

  $dm_change_legit = true;
  $sn_data_dark_matter_db_name = pname_resource_name(RES_DARK_MATTER);

  if($already_changed) {
    $rows_affected = 1;
  } else {
    $changeset = array();
    $a_user = db_user_by_id($user_id, true);
    if($dark_matter < 0) {
      $dark_matter_exists = mrc_get_level($a_user, null, RES_DARK_MATTER, false, true);
      $dark_matter_exists < 0 ? $dark_matter_exists = 0 : false;
      $metamatter_to_reduce = -$dark_matter - $dark_matter_exists;
      if($metamatter_to_reduce > 0) {
        $metamatter_exists = mrc_get_level($a_user, null, RES_METAMATTER);
        if($metamatter_exists < $metamatter_to_reduce) {
          $debug->error('Ошибка снятия ТМ - ММ+ТМ меньше, чем сумма для снятия!', 'Ошибка снятия ТМ', LOG_ERR_INT_NOT_ENOUGH_DARK_MATTER);
        }
        if(is_array($comment)) {
          $comment = call_user_func_array('sprintf', $comment);
        }
//        mm_points_change($user_id, $change_type, -$metamatter_to_reduce, 'ММ в ТМ: ' . (-$dark_matter) . ' ТМ = ' . $dark_matter_exists . ' ТМ + ' . $metamatter_to_reduce . ' ММ. ' . $comment);
        SN::$auth->account->metamatter_change($change_type, -$metamatter_to_reduce, 'ММ в ТМ: ' . (-$dark_matter) . ' ТМ = ' . $dark_matter_exists . ' ТМ + ' . $metamatter_to_reduce . ' ММ. ' . $comment);
        $dark_matter = -$dark_matter_exists;
      }
    } else {
      $changeset[] = "`dark_matter_total` = `dark_matter_total` + '{$dark_matter}'";
    }
    $dark_matter ? $changeset[] = "`{$sn_data_dark_matter_db_name}` = `{$sn_data_dark_matter_db_name}` + '{$dark_matter}'" : false;
    !empty($changeset) ? db_user_set_by_id($user_id, implode(',', $changeset)) : false;
    $rows_affected = SN::$db->db_affected_rows();
  }

  if($rows_affected || !$dark_matter) {
    $page_url = db_escape($_SERVER['SCRIPT_NAME']);
    if(is_array($comment)) {
      $comment = call_user_func_array('sprintf', $comment);
    }
    $comment = db_escape($comment);
    $row = db_user_by_id($user_id, false, 'username');
    $row['username'] = db_escape($row['username']);
    doquery(
      "INSERT INTO {{log_dark_matter}} (`log_dark_matter_username`, `log_dark_matter_reason`,
        `log_dark_matter_amount`, `log_dark_matter_comment`, `log_dark_matter_page`, `log_dark_matter_sender`)
      VALUES (
        '{$row['username']}', {$change_type},
        {$dark_matter}, '{$comment}', '{$page_url}', {$user_id}
      );");

    if($user['id'] == $user_id) {
      $user['dark_matter'] += $dark_matter;
    }

    if($dark_matter > 0) {
      $old_referral = doquery("SELECT * FROM {{referrals}} WHERE `id` = {$user_id} LIMIT 1 FOR UPDATE;", '', true);
      if($old_referral['id']) {
        doquery("UPDATE {{referrals}} SET dark_matter = dark_matter + '{$dark_matter}' WHERE `id` = {$user_id} LIMIT 1;");
        $new_referral = doquery("SELECT * FROM {{referrals}} WHERE `id` = {$user_id} LIMIT 1;", '', true);

        $partner_bonus = floor($new_referral['dark_matter'] / $config->rpg_bonus_divisor) - ($old_referral['dark_matter'] >= $config->rpg_bonus_minimum ? floor($old_referral['dark_matter'] / $config->rpg_bonus_divisor) : 0);
        if($partner_bonus > 0 && $new_referral['dark_matter'] >= $config->rpg_bonus_minimum) {
          rpg_points_change($new_referral['id_partner'], RPG_REFERRAL, $partner_bonus, "Incoming From Referral ID {$user_id}");
        }
      }
    }
  } else {
    $debug->warning("Error adjusting Dark Matter for player ID {$user_id} (Player Not Found?) with {$dark_matter}. Reason: {$comment}", 'Dark Matter Change', 402);
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
      $b1 = 50;
      $comment = 'Level Up For Structure Building';
    break;

    case RPG_RAID:
      $field_level = 'lvl_raid';
      $field_xp = 'xpraid';
      $b1 = 10;
      $comment = 'Level Up For Raiding';
    break;

    case RPG_TECH:
      $field_level = 'player_rpg_tech_level';
      $field_xp = 'player_rpg_tech_xp';
      $b1 = 50;
      $comment = 'Level Up For Research';
    break;

    case RPG_EXPLORE:
      $field_level = 'player_rpg_explore_level';
      $field_xp = 'player_rpg_explore_xp';
      $b1 = 10;
      $comment = 'Level Up For Exploration';
      $q = 1.05;
    break;

    default:
      break;

  }

  $xp = &$user[$field_xp];

  if($xp_to_add)
  {
    $xp += $xp_to_add;
    db_user_set_by_id($user['id'], "`{$field_xp}` = `{$field_xp}` + '{$xp_to_add}'");
  }

  $level = $user[$field_level];
  while($xp > rpg_xp_for_level($level + 1, $b1, $q))
  {
    $level++;
  }
  $level -= $user[$field_level];
  if($level > 0)
  {
    db_user_set_by_id($user['id'], "`{$field_level}` = `{$field_level}` + '{$level}'");
    rpg_points_change($user['id'], $type, $level * 1000, $comment);
    $user[$field_level] += $level;
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

function rpg_get_tech_xp($level)
{
  return rpg_xp_for_level($level, 50, 1.03);
}

function rpg_get_explore_xp($level)
{
  return rpg_xp_for_level($level, 10, 1.05);
}
