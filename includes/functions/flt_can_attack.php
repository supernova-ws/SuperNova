<?php

function flt_can_attack($target_planet, $target_mission, $fleet = array(), $flying_fleets = false)
{
  global $time_now, $config, $sn_data, $sn_groups, $user, $planetrow;

  if($user['urlaubs_modus'])
  {
    return ATTACK_OWN_VACATION;
  }

  if(empty($fleet) || !is_array($fleet))
  {
    return ATTACK_NO_FLEET;
  }

  foreach($fleet as $ship_id => $ship_count)
  {
    if($ship_count > $planetrow[$sn_data[$ship_id]['name']])
    {
      return ATTACK_NO_SHIPS;
    }
  }

  if($flying_fleets === false)
  {
    $flying_fleets = doquery("SELECT COUNT(fleet_id) AS `flying_fleets` FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", '', true);
    $flying_fleets = $flying_fleets['flying_fleets'];
  }
  if (GetMaxFleets($user) <= $flying_fleets && $target_mission != MT_MISSILE)
  {
    return ATTACK_NO_SLOTS;
  }

  // Checking for no planet
  if(!$target_planet['id_owner'])
  {
    if($target_mission == MT_COLONIZE && !$fleet[208])
    {
      return ATTACK_NO_COLONIZER;
    }

    if($target_mission == MT_EXPLORE || $target_mission == MT_COLONIZE)
    {
      return ATTACK_ALLOWED;
    }
    return ATTACK_NO_TARGET;
  }

  if($target_mission == MT_RECYCLE)
  {
    if($target_planet['debris_metal'] + $target_planet['debris_crystal'] <= 0)
    {
      return ATTACK_NO_DEBRIS;
    }

    if($fleet[209] <= 0)
    {
      return ATTACK_NO_RECYCLERS;
    }

    return ATTACK_ALLOWED;
  }

  // Got planet. Checking if it is ours
  if($target_planet['id_owner'] == $user['id'])
  {
    if($target_mission == MT_TRANSPORT || $target_mission == MT_RELOCATE)
    {
      return ATTACK_ALLOWED;
    }
    return ATTACK_OWN;
  }

  // No, planet not ours. Cutting mission that can't be send to not-ours planet
  if($target_mission == MT_RELOCATE || $target_mission == MT_COLONIZE || $target_mission == MT_EXPLORE)
  {
    return ATTACK_WRONG_MISSION;
  }

  $enemy = doquery("SELECT * FROM {{users}} WHERE `id` = '{$target_planet['id_owner']}';", '', true);
  // We cannot attack or send resource to users in VACANCY mode
  if ($enemy['urlaubs_modus'] && $target_mission != MT_RECYCLE)
  {
    return ATTACK_VACANCY;
  }

  // Multi IP protection. Here we need a procedure to check proxies
  if(sys_is_multiaccount($user, $enemy))
  {
    return ATTACK_SAME_IP;
  }

  $user_points = doquery("SELECT total_points FROM {{statpoints}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '{$user['id']}';", '', true);
  $user_points = $user_points['total_points'];
  $enemy_points = doquery("SELECT total_points FROM {{statpoints}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '{$enemy['id']}';", '', true);
  $enemy_points = $enemy_points['total_points'];

  // Is it transport? If yes - checking for buffing to prevent mega-alliance destroyer
  if($target_mission == MT_TRANSPORT)
  {
    if($user_points >= $enemy_points)
    {
      return ATTACK_ALLOWED;
    }
    else
    {
      return ATTACK_BUFFING;
    }
  }

  // Only aggresive missions passed to this point. HOLD counts as passive but aggresive

  // Is it admin with planet protection?
  if ($target_planet['id_level'] > $user['authlevel'])
  {
    return ATTACK_ADMIN;
  }

  // Okay. Now skipping protection checks for inactive longer then 1 week
  if (!$enemy['onlinetime'] || $enemy['onlinetime'] >= ($time_now - 60*60*24*7))
  {
    if($enemy_points <= $config->game_noob_points && ($user_points > $config->game_noob_points || $user_points > $enemy_points * $config->game_noob_factor))
    {
      return ATTACK_NOOB;
    }
  }

  // Is it HOLD mission? If yes - there should be ally deposit
  if($target_mission == MT_HOLD)
  {
    if($target_planet[$sn_data[34]['name']])
    {
      return ATTACK_ALLOWED;
    }
    return ATTACK_NO_ALLY_DEPOSIT;
  }

  if($target_mission == MT_SPY)
  {
    if($fleet[210] >= 1)
    {
      return ATTACK_ALLOWED;
    }
    return ATTACK_NO_SPIES;
  }

  // Is it MISSILE mission?
  if($target_mission == MT_MISSILE)
  {
    if($planetrow[$sn_data[44]['name']] < $sn_data[503]['require'][44])
    {
      return ATTACK_NO_SILO;
    }

    if(!$fleet[503])
    {
      return ATTACK_NO_MISSILE;
    }

    $distance = abs($target_planet['system'] - $planetrow['system']);
    $mip_range = get_missile_range();
    if($distance > $mip_range || $target_planet['galaxy'] != $planetrow['galaxy'])
    {
      return ATTACK_MISSILE_TOO_FAR;
    }
  }

  return ATTACK_ALLOWED;
}

?>
