<?php

function flt_can_attack($planet_src, $planet_dst, $fleet = array(), $mission, $options = false)
{
  global $config, $sn_data, $user, $time_now;

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
    if($ship_count > $planet_src[$sn_data[$ship_id]['name']])
    {
      return ATTACK_NO_SHIPS;
    }
  }

  $speed = $options['speed'];
  if($speed && ($speed != intval($speed) || $speed < 1 || $speed > 10))
  {
    return ATTACK_WRONG_SPEED;
  }

  $speed_factor = get_fleet_speed();
  $distance     = GetTargetDistance($planet_src['galaxy'], $planet_dst['galaxy'], $planet_src['system'], $planet_dst['system'], $planet_src['planet'], $planet_dst['planet']);
  $fleet_speed  = min(GetFleetMaxSpeed($fleet, 0, $user));
  $duration     = GetMissionDuration(10, $fleet_speed, $distance, $speed_factor);
  $consumption  = GetFleetConsumption($fleet, $speed_factor, $duration, $distance, $fleet_speed, $user);
  if($planet_src[$sn_data[RES_DEUTERIUM]['name']] < $fleet[RES_DEUTERIUM] + $consumption)
  {
    return ATTACK_NO_FUEL;
  }

  $fleet_start_time = $time_now + $duration;

  $fleet_group = $options['fleet_group'];
  if($fleet_group)
  {
    if($mission != MT_AKS)
    {
      return ATTACK_WRONG_MISISON;
    };

    $acs = doquery("SELECT * FROM {{aks}} WHERE id = '{$fleet_group}' LIMIT 1;", '', true);
    if ($acs['id'])
    {
      return ATTACK_NO_ACS;
    }

    if ($to['galaxy'] != $acs['galaxy'] || $to['system'] != $acs['system'] || $to['planet'] != $acs['planet'] || $to['planet_type'] != $acs['planet_type'])
    {
      return ATTACK_ACS_WRONG_TARGET;
    }

    if ($fleet_start_time>$acs['ankunft'])
    {
      return ATTACK_ACS_TOO_LATE;
    }
//    $fleet_start_time = $aks['ankunft'];
//    $fleet_end_time = $aks['ankunft'] + $duration;
  }

  $flying_fleets = $options('flying_fleets');
  if(!$flying_fleets)
  {
    $flying_fleets = doquery("SELECT COUNT(fleet_id) AS `flying_fleets` FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", '', true);
    $flying_fleets = $flying_fleets['flying_fleets'];
  }
  if (GetMaxFleets($user) <= $flying_fleets && $mission != MT_MISSILE)
  {
    return ATTACK_NO_SLOTS;
  }

  // Checking for no planet
  if(!$planet_dst['id_owner'])
  {
    if($mission == MT_COLONIZE && !$fleet[208])
    {
      return ATTACK_NO_COLONIZER;
    }

    if($mission == MT_EXPLORE || $mission == MT_COLONIZE)
    {
      return ATTACK_ALLOWED;
    }
    return ATTACK_NO_TARGET;
  }

  if($mission == MT_RECYCLE)
  {
    if($planet_dst['debris_metal'] + $planet_dst['debris_crystal'] <= 0)
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
  if($planet_dst['id_owner'] == $user['id'])
  {
    if($mission == MT_TRANSPORT || $mission == MT_RELOCATE)
    {
      return ATTACK_ALLOWED;
    }
    return ATTACK_OWN;
  }

  // No, planet not ours. Cutting mission that can't be send to not-ours planet
  if($mission == MT_RELOCATE || $mission == MT_COLONIZE || $mission == MT_EXPLORE)
  {
    return ATTACK_WRONG_MISSION;
  }

  $enemy = doquery("SELECT * FROM {{users}} WHERE `id` = '{$planet_dst['id_owner']}' LIMIT 1;", '', true);
  // We cannot attack or send resource to users in VACANCY mode
  if ($enemy['urlaubs_modus'] && $mission != MT_RECYCLE)
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
  if($mission == MT_TRANSPORT)
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
  if ($planet_dst['id_level'] > $user['authlevel'])
  {
    return ATTACK_ADMIN;
  }

  // Okay. Now skipping protection checks for inactive longer then 1 week
  if (!$enemy['onlinetime'] || $enemy['onlinetime'] >= ($time_now - 60*60*24*7))
  {
    if(($enemy_points <= $config->game_noob_points && $user_points > $config->game_noob_points) || $user_points > $enemy_points * $config->game_noob_factor)
    {
      return ATTACK_NOOB;
    }
  }

  // Is it HOLD mission? If yes - there should be ally deposit
  if($mission == MT_HOLD)
  {
    if($planet_dst[$sn_data[34]['name']])
    {
      return ATTACK_ALLOWED;
    }
    return ATTACK_NO_ALLY_DEPOSIT;
  }

  if($mission == MT_SPY)
  {
    if($fleet[210] >= 1)
    {
      return ATTACK_ALLOWED;
    }
    return ATTACK_NO_SPIES;
  }

  // Is it MISSILE mission?
  if($mission == MT_MISSILE)
  {
    if($planet_src[$sn_data[44]['name']] < $sn_data[503]['require'][44])
    {
      return ATTACK_NO_SILO;
    }

    if(!$fleet[503])
    {
      return ATTACK_NO_MISSILE;
    }

    $distance = abs($planet_dst['system'] - $planet_src['system']);
    $mip_range = get_missile_range();
    if($distance > $mip_range || $planet_dst['galaxy'] != $planet_src['galaxy'])
    {
      return ATTACK_MISSILE_TOO_FAR;
    }
  }

  return ATTACK_ALLOWED;
}

?>
