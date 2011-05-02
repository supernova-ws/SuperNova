<?php

function flt_bashing_check($user, $enemy, $planet_dst, $mission, $fleet_group = 0)
{
  $time_now = &$GLOBALS['time_now'];
  $config = &$GLOBALS['config'];

  $config_bashing_attacks = $config->fleet_bashing_attacks;
  $config_bashing_interval = $config->fleet_bashing_interval;
  if(!$config_bashing_attacks)
  {
    // Bashing allowed - protection disabled
    return ATTACK_ALLOWED;
  }

  $bashing_result = ATTACK_BASHING;
  if($user['ally_id'] && $enemy['ally_id'])
  {
    $relations = ali_relations($user['ally_id'], $enemy['ally_id']);
    if(!empty($relations))
    {
      $relations = $relations[$enemy['ally_id']];
      switch($relations['alliance_diplomacy_relation'])
      {
        case ALLY_DIPLOMACY_WAR:
          if($time_now - $relations['alliance_diplomacy_time'] <= $config->fleet_bashing_war_delay)
          {
            $bashing_result = ATTACK_BASHING_WAR_DELAY;
          }
          else
          {
            return ATTACK_ALLOWED;
          }
        break;
        // Here goes other relations

/*
        default:
          return ATTACK_ALLOWED;
        break;
*/
      }
    }
  }

  $time_now = $GLOBALS['time_now'];
  $time_limit = $time_now - $config->fleet_bashing_scope;
  $bashing_list = array($time_now);

  // Retrieving flying fleets
  $flying_fleets = array();
  $query = doquery("SELECT fleet_group, fleet_end_time FROM {{fleets}} WHERE
  fleet_end_galaxy = {$planet_dst['galaxy']} AND
  fleet_end_system = {$planet_dst['system']} AND
  fleet_end_planet = {$planet_dst['planet']} AND
  fleet_end_type   = {$planet_dst['planet_type']} AND
  fleet_owner = {$user['id']} AND fleet_mission IN (" . MT_ATTACK . "," . MT_AKS . "," . MT_DESTROY . ") AND fleet_mess = 0;");
  while($bashing_fleets = mysql_fetch_assoc($query))
  {
    // Checking for ACS - each ACS count only once
    if($bashing_fleets['fleet_group'])
    {
      $bashing_list["{$user['id']}_{$bashing_fleets['fleet_group']}"] = $bashing_fleets['fleet_end_time'];
    }
    else
    {
      $bashing_list[] = $bashing_fleets['fleet_end_time'];
    }
  }

  // Check for joining to ACS - if there are already fleets in ACS no checks should be done
  if($mission == MT_AKS && $bashing_list["{$user['id']}_{$fleet_group}"])
  {
    return ATTACK_ALLOWED;
  }

  $query = doquery("SELECT bashing_time FROM {{bashing}} WHERE bashing_user_id = {$user['id']} AND bashing_planet_id = {$planet_dst['id']} AND bashing_time >= {$time_limit};");
  while($bashing_row = mysql_fetch_assoc($query))
  {
    $bashing_list[] = $bashing_row['bashing_time'];
  }

  sort($bashing_list);

  $last_attack = 0;
  $wave = 0;
  $attack = 1;
  foreach($bashing_list as &$bash_time)
  {
    $attack++;
    if($bash_time - $last_attack > $config_bashing_interval || $attack > $config_bashing_attacks)
    {
      $attack = 1;
      $wave++;
    }

    $last_attack = $bash_time;
  }

  return ($wave <= $config->fleet_bashing_waves ? ATTACK_ALLOWED : $bashing_result);
}

function flt_can_attack($planet_src, $planet_dst, $fleet = array(), $mission, $options = false)
{
  global $config, $sn_data, $user, $time_now;

  if($user['vacation'])
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
  $fleet_speed_percent = $options['fleet_speed_percent'] ? $options['fleet_speed_percent'] : 10;
  $duration     = GetMissionDuration($fleet_speed_percent, $fleet_speed, $distance, $speed_factor);
  $consumption  = GetFleetConsumption($fleet, $speed_factor, $duration, $distance, $fleet_speed, $user, $fleet_speed_percent);
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
      return ATTACK_WRONG_MISSION;
    };

    $acs = doquery("SELECT * FROM {{aks}} WHERE id = '{$fleet_group}' LIMIT 1;", '', true);
    if (!$acs['id'])
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
  }

  $flying_fleets = $options['flying_fleets'];
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
    if($mission == MT_COLONIZE && !$fleet[SHIP_COLONIZER])
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

    if($fleet[SHIP_RECYCLER] <= 0)
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
  // We cannot attack or send resource to users in VACATION mode
  if ($enemy['vacation'] && $target_mission != MT_RECYCLE)
  {
    return ATTACK_VACATION;
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
    if(($enemy_points <= $config->game_noob_points && $user_points > $config->game_noob_points) || ($config->game_noob_factor && $user_points > $enemy_points * $config->game_noob_factor))
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
    if($fleet[SHIP_SPY] >= 1)
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

  if($mission == MT_DESTROY && $planet_dst['planet_type'] != PT_MOON)
  {
    return ATTACK_WRONG_MISSION;
  }

  if($mission == MT_ATTACK || $mission == MT_AKS || $mission == MT_DESTROY)
  {
    return flt_bashing_check($user, $enemy, $planet_dst, $mission, $fleet_group);
  }

  return ATTACK_ALLOWED;
}

?>
