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
function rpg_points_change($user_id, $dark_matter, $comment = false)
{
  global $debug, $config, $dm_change_legit;

  if(!$user_id)
  {
    return false;
  }

  $dm_change_legit = true;
  doquery("UPDATE {{users}} SET rpg_points = rpg_points + '$dark_matter' WHERE `id` = {$user_id}");
  $rows_affected = mysql_affected_rows();
  if($rows_affected)
  {
    $debug->warning("Player ID {$user_id} Dark Matter was adjusted with {$dark_matter}. Reason: {$comment}", 'Dark Matter Change', 102);

    if($dark_matter>0)
    {
      $old_referral = doquery("SELECT * FROM {{referrals}} WHERE `id` = {$user_id}", '', true);
      if($old_referral['id'])
      {
        doquery("UPDATE {{referrals}} SET dark_matter = dark_matter + '$dark_matter' WHERE `id` = {$user_id}");
        $new_referral = doquery("SELECT * FROM {{referrals}} WHERE `id` = {$user_id}", '', true);

        $partner_bonus = floor($new_referral['dark_matter']/$config->rpg_bonus_divisor) - floor($old_referral['dark_matter']/$config->rpg_bonus_divisor);
        if($partner_bonus > 0)
        {
          rpg_points_change($new_referral['id_partner'], $partner_bonus, "Incoming From Referral ID {$user_id}");
        }
      }
    }
  }else{
    $debug->warning("Error adjusting Dark Matter for player ID {$user_id} with {$dark_matter}. Reason: {$comment}", 'Dark Matter Change', 402);
  }

  $dm_change_legit = false;
  return $rows_affected;
}

function rpg_calc_xp_for_levelup($current_xp, $b1, $q)
{
  return floor($b1 * (pow($q, $current_xp) - 1)/($q - 1));
}

function rpg_get_miner_xp($current_xp)
{
  $miner_b1 = 50;
  $miner_q  = 1.03;

  return rpg_calc_xp_for_levelup($current_xp, $miner_b1, $miner_q);
}

function RPG_get_raider_xp($current_xp)
{
  $raid_b1 = 10;
  $raid_q  = 1.03;

  return rpg_calc_xp_for_levelup($current_xp, $raid_b1, $raid_q);
}
?>