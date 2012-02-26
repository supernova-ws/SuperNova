<?php

/**
 * sys_stat_functions.php
 *
 * @version 6 (c) copyright 2010-2012 by Gorlum for http://supernova.ws
 *   [~] Made statistic non-transactional again to prevent locks
 * @version 5 (c) copyright 2010-2011 by Gorlum for http://supernova.ws
 *   [-] Rid of all procedures - they only called once per loop and make unneeded overhead
 *   [~] Some optimizations. +6% to script speed on test base
 *   [~] Wrapped update in transaction. Decreased script execution time by 98%
 * @version 4 (c) copyright 2010 by Gorlum for http://supernova.ws
 *   [~] Now setting time limit also update end time in the DB
 *   [+] Logging more information about update process to simplify error detection
 *   [+] Implemented locking mechanic that permits launches of more then one update process
 *   [+] Stat update logging statistic about it progress to DB
 * @version 3 (c) copyright 2010 by Gorlum for http://supernova.ws
 *   [+] Added resource stat
 * @version 2 (c) copyright 2010 by Gorlum for http://supernova.ws
 *   [!] Stats calculation greatly increased to 10x times (and even more in certain configurations)
 *   [!] Now all ranks for players/allies counting here - no unnecessary UPDATEs in /stats.php
 *   [*] All planet funcs merged into GetPlanetPoints
 *   [*] GetPlanetPoints optimized to utilize properties of geometrical progression
 *   [*] Planet-related calculations now made within one SQL-query for all planets - not per user
 *   [*] Fleet-On-Fly-related calculations now made within one SQL-query for all fleets - not per user
 *   [*] Greatly improved insertions of new Allie's stat by moving all calculation to single query
 *   [*] Greatly improved speed of updating Player's rank by moving every rank update to single query
 * StatFunctions.php @version 1 copyright 2008 by Chlorel for XNova
 */
function sta_set_time_limit($sta_update_msg = 'updatins something', $next_step = true)
{
  global $config, $debug, $sta_update_step;

  $value = 60;
  set_time_limit($value);
  $config->db_saveItem('var_stat_update_end', time() + $value);

  $sta_update_msg = mysql_real_escape_string($sta_update_msg);

  if($next_step)
  {
    $sta_update_step++;
  }
  $sta_update_msg = "Update in progress. Step {$sta_update_step}/10: {$sta_update_msg}.";

  $config->db_saveItem('var_stat_update_msg', $sta_update_msg);
  if($next_step)
  {
    $debug->warning($sta_update_msg, 'Stat update', 191);
  }
}

function sys_stat_calculate()
{
  global $config, $time_now, $sta_update_step, $sn_data;

  $sn_groups_resources_loot = &$sn_data['groups']['resources_loot'];
  $rate[RES_METAL] = $config->rpg_exchange_metal;
  $rate[RES_CRYSTAL] = $config->rpg_exchange_crystal / $config->rpg_exchange_metal;
  $rate[RES_DEUTERIUM] = $config->rpg_exchange_deterium / $config->rpg_exchange_metal;

  $sta_update_step = -1;

  sta_set_time_limit('starting update');

//  doquery('START TRANSACTION;');

  sta_set_time_limit('archiving old statistic');
  // Statistic rotation
  doquery("DELETE FROM {{statpoints}} WHERE `stat_code` = 10;");
  doquery("UPDATE {{statpoints}} SET `stat_code` = `stat_code` + 1;");

  // Calculation of Fleet-In-Flight
  sta_set_time_limit('calculating flying fleets stats');
  $i = 0;
  $UsrFleets = doquery("SELECT fleet_owner, fleet_array, fleet_resource_metal, fleet_resource_crystal, fleet_resource_deuterium FROM {{fleets}};");
  $row_num = mysql_num_rows($UsrFleets);
  while($fleet_row = mysql_fetch_assoc($UsrFleets))
  {
    if($i % 100 == 0)
    {
      sta_set_time_limit("calculating flying fleets stats (fleet {$i}/{$row_num})", false);
    }
    $i++;

    $split = explode(' ', trim(str_replace(';', ' ', $fleet_row['fleet_array'])));

    $FleetCounts = 0;
    $FleetPoints = 0;
    foreach($split as $ship)
    {
      list($unit_id, $unit_amount) = explode(',', $ship);
      $unit_cost_data = $sn_data[$unit_id]['cost'];
      $FleetPoints += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * $unit_amount;
      $FleetCounts += $unit_amount;
    }
    $fleet_resources = $fleet_row['fleet_resource_metal'] * $rate[RES_METAL] + $fleet_row['fleet_resource_crysal'] * $rate[RES_CRYSTAL] + $fleet_row['fleet_resource_deuterium'] * $rate[RES_DEUTERIUM];
    /*
    foreach($sn_groups_resources_loot as $resource_name)
    {
      $resource_amount = $fleet_row["fleet_resource_{$sn_data[$resource_name]['name']}"];
      if($resource_amount > 0)
      {
        $ResourceCount += $resource_amount;
        $ResourcePoint += $resource_amount;
      }
    }
    */

    $user_id = $fleet_row['fleet_owner'];
    $counts[$user_id]['fleet'] += $FleetCounts;
    $points[$user_id]['fleet'] += $FleetPoints / 1000;
    $counts[$user_id]['resources'] += $fleet_resources;
    $points[$user_id]['resources'] += $fleet_resources / 1000;
  }

  sta_set_time_limit('calculating planets stats');

  $i = 0;
  $UsrPlanets = doquery("SELECT * FROM {{planets}};");
  $row_num = mysql_num_rows($UsrPlanets);
  while($planet_row = mysql_fetch_assoc($UsrPlanets))
  {
    if($i % 100 == 0)
    {
      sta_set_time_limit("calculating planets stats (planet {$i}/{$row_num})", false);
    }
    $i++;

    $user_id = $planet_row['id_owner'];
  
    $planet_points = 0;
    
    $point_counter = $amount_counter = 0;
    foreach($sn_data['groups']['structures'] as $unit_id)
    {
      $unit_level = $planet_row[$sn_data[$unit_id]['name']];
      if($unit_level > 0)
      {
        $unit_cost_data = $sn_data[$unit_id]['cost'];
        $f = $unit_cost_data['factor'];
        $point_counter += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * (pow($f, $unit_level) - $f) / ($f - 1);
        $amount_counter += $unit_level;
      }
    }
    $points[$user_id]['structures'] += $point_counter / 1000;
    $planet_points += $point_counter;
    $counts[$user_id]['structures'] += $amount_counter;

    $point_counter = $amount_counter = 0;
    foreach($sn_data['groups']['defense'] as $unit_id)
    {
      $unit_amount = $planet_row[$sn_data[$unit_id]['name']];
      if($unit_amount > 0)
      {
        $point_counter += ($sn_data[$unit_id]['metal'] * $rate[RES_METAL] + $sn_data[$unit_id]['crystal'] * $rate[RES_CRYSTAL] + $sn_data[$unit_id]['deuterium'] * $rate[RES_DEUTERIUM]) * $unit_amount;
        $amount_counter += $unit_amount;
      }
    }
    $points[$user_id]['defs'] += $point_counter / 1000;
    $planet_points += $point_counter;
    $counts[$user_id]['defs'] += $amount_counter;

    $point_counter = $amount_counter = 0;
    foreach($sn_data['groups']['fleet'] as $unit_id)
    {
      $unit_amount = $planet_row[$sn_data[$unit_id]['name']];
      if($unit_amount > 0)
      {
        $point_counter += ($sn_data[$unit_id]['metal'] * $rate[RES_METAL] + $sn_data[$unit_id]['crystal'] * $rate[RES_CRYSTAL] + $sn_data[$unit_id]['deuterium'] * $rate[RES_DEUTERIUM]) * $unit_amount;
        $amount_counter += $unit_amount;
      }
    }
    $points[$user_id]['fleet'] += $point_counter / 1000;
    $counts[$user_id]['fleet'] += $amount_counter;

    $point_counter = 0;
    foreach($sn_groups_resources_loot as $resource_name)
    {
      $point_counter += ($resource_amount = $planet_row[$sn_data[$resource_name]['name']]) > 0 ? $resource_amount : 0;
    }

    if($planet_row['b_hangar_id'])
    {
//      $ship_list = flt_expand(array('fleet_array' => $planet_row['b_hangar_id']));
      $ship_list = sys_unit_str2arr($planet_row['b_hangar_id']);
      foreach($ship_list as $ship_id => $ship_amount)
      {
        $data = $sn_data[$ship_id]['cost'];
        $point_counter += ($data[RES_METAL] * $rate[RES_METAL] + $data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * $ship_amount;
      }
    }
    // TODO: Also calculate cost of structures and research in ques
    $points[$user_id]['resources'] += $point_counter / 1000;
    $counts[$user_id]['resources'] += $point_counter;
    
//  Disabled planet point update. Didn't see any use for it
//    $planet_points = floor($planet_points / 1000);
//    doquery("UPDATE {{planets}} SET `points` = '{$planet_points}' WHERE `id` = '{$planet_row['id']}';");
  }

  sta_set_time_limit('posting new user stats to DB');
  $GameUsers = doquery("SELECT * FROM {{users}} where user_as_ally IS NULL;");
  while($user_row = mysql_fetch_assoc($GameUsers))
  {
    $user_id = $user_row['id'];
    
    $TechCounts = 0;
    $TechPoints = 0;
    foreach($sn_data['groups']['tech'] as $unit_id)
    {
      $unit_level = $user_row[$sn_data[$unit_id]['name']];
      if($unit_level > 0)
      {
        $unit_cost_data = $sn_data[$unit_id]['cost'];
        $f = $unit_cost_data['factor'];
        $TechPoints += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * (pow($f, $unit_level) - $f) / ($f - 1);
        $TechCounts += $unit_level;
      }
    }
    $points[$user_id]['tech'] = $TechPoints / 1000;
    $counts[$user_id]['tech'] = $TechCounts;

    array_walk($points[$user_id], 'floor');

    $GPoints = array_sum($points[$user_id]);
    $GCount = array_sum($counts[$user_id]);

    $QryInsertStats = "INSERT INTO {{statpoints}} SET ";
    $QryInsertStats .= "`id_owner` = '" . $user_id . "', ";
    $QryInsertStats .= "`id_ally` = " . ($user_row['ally_id'] ? $user_row['ally_id'] : 'NULL') . ", ";
    $QryInsertStats .= "`stat_type` = '1', "; // 1 pour joueur , 2 pour alliance
    $QryInsertStats .= "`stat_code` = '1', ";
    $QryInsertStats .= "`tech_points` = '" . $points[$user_id]['tech'] . "', ";
    $QryInsertStats .= "`tech_count` = '" . $counts[$user_id]['tech'] . "', ";
    $QryInsertStats .= "`build_points` = '" . $points[$user_id]['structures'] . "', ";
    $QryInsertStats .= "`build_count` = '" . $counts[$user_id]['structures'] . "', ";
    $QryInsertStats .= "`defs_points` = '" . $points[$user_id]['defs'] . "', ";
    $QryInsertStats .= "`defs_count` = '" . $counts[$user_id]['defs'] . "', ";
    $QryInsertStats .= "`fleet_points` = '" . $points[$user_id]['fleet'] . "', ";
    $QryInsertStats .= "`fleet_count` = '" . $counts[$user_id]['fleet'] . "', ";
    $QryInsertStats .= "`res_points` = '" . $points[$user_id]['resources'] . "', ";
    $QryInsertStats .= "`res_count` = '" . $counts[$user_id]['resources'] . "', ";
    $QryInsertStats .= "`total_points` = '{$GPoints}', ";
    $QryInsertStats .= "`total_count` = '{$GCount}', ";
    $QryInsertStats .= "`stat_date` = '{$time_now}';";
    doquery($QryInsertStats);
  }

  sta_set_time_limit('setting previous user stats from archive');
  doquery("
    UPDATE {{statpoints}} as new
      LEFT JOIN {{statpoints}} as old ON old.id_owner = new.id_owner AND old.stat_code = 2 AND old.stat_type = 1
    SET
      new.tech_old_rank = old.tech_rank,
      new.build_old_rank = old.build_rank,
      new.defs_old_rank  = old.defs_rank ,
      new.fleet_old_rank = old.fleet_rank,
      new.res_old_rank = old.res_rank,
      new.total_old_rank = old.total_rank
    WHERE
      new.stat_type = 1 AND new.stat_code = 1;
  ");

  // Some variables we need to update ranks
  $qryResetRowNum = 'SET @rownum=0;';
  $qryFormat = 'UPDATE {{statpoints}} SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 ORDER BY `%1$s_points` DESC, `id_owner` ASC, `id_ally` ASC;';
  $rankNames = array('tech', 'build', 'defs', 'fleet', 'res', 'total');

  // Updating player's ranks
  sta_set_time_limit("updating ranks for players");
  foreach($rankNames as $rankName)
  {
    sta_set_time_limit("updating player rank '{$rankName}'", false);
    doquery($qryResetRowNum);
    doquery(sprintf($qryFormat, $rankName, 1));
  }

  // Updating Allie's stats
  sta_set_time_limit('posting new Alliance stats to DB');
  $QryInsertStats = "
    INSERT INTO {{statpoints}}
      (`tech_points`, `tech_count`, `build_points`, `build_count`, `defs_points`, `defs_count`,
        `fleet_points`, `fleet_count`, `res_points`, `res_count`, `total_points`, `total_count`,
        `stat_date`, `id_owner`, `id_ally`, `stat_type`, `stat_code`,
        `tech_old_rank`, `build_old_rank`, `defs_old_rank`, `fleet_old_rank`, `res_old_rank`, `total_old_rank`
      )
      SELECT
        SUM(u.`tech_points`), SUM(u.`tech_count`), SUM(u.`build_points`), SUM(u.`build_count`), SUM(u.`defs_points`),
        SUM(u.`defs_count`), SUM(u.`fleet_points`), SUM(u.`fleet_count`), SUM(u.`res_points`), SUM(u.`res_count`),
        SUM(u.`total_points`), SUM(u.`total_count`),
        {$time_now}, NULL, u.`id_ally`, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.res_rank, a.total_rank
      FROM {{statpoints}} as u
        LEFT JOIN {{statpoints}} as a ON a.id_ally = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
      WHERE u.`stat_type` = 1 AND u.stat_code = 1 AND u.id_ally<>0
      GROUP BY u.`id_ally`";
  doquery($QryInsertStats);

  sta_set_time_limit("updating ranks for Alliances");
  // --- Updating Allie's ranks
  foreach($rankNames as $rankName)
  {
    sta_set_time_limit("updating Alliances rank '{$rankName}'", false);
    doquery($qryResetRowNum);
    doquery(sprintf($qryFormat, $rankName, 2));
  }

  sta_set_time_limit('updating player\'s current rank and points');
  doquery("UPDATE {{users}} AS u JOIN {{statpoints}} AS sp ON sp.id_owner = u.id AND sp.stat_code = 1 AND sp.stat_type = 1 SET u.total_rank = sp.total_rank, u.total_points = sp.total_points;");

  sta_set_time_limit('updating Ally\'s current rank and points');
  doquery("UPDATE {{alliance}} AS a JOIN {{statpoints}} AS sp ON sp.id_ally = a.id AND sp.stat_code = 1 AND sp.stat_type = 2 SET a.total_rank = sp.total_rank, a.total_points = sp.total_points;");

  // Counting real user count and updating values
  $userCount = doquery("SELECT COUNT(*) AS users_online FROM {{users}} WHERE user_as_ally IS NULL;", '', true);
  $config->db_saveItem('users_amount', $userCount['users_online']);

//  doquery('COMMIT');
}

?>
