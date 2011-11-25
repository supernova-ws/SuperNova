<?php

/**
 * sys_stat_functions.php
 *
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
  $sta_update_msg = "Update in progress. Step {$sta_update_step}/9: {$sta_update_msg}.";

  $config->db_saveItem('var_stat_update_msg', $sta_update_msg);
  if($next_step)
  {
    $debug->warning($sta_update_msg, 'Stat update', 191);
  }
}

function SYS_statCalculate()
{
  global $config, $time_now, $sta_update_step, $sn_data;

  $sn_groups_resources_loot = &$sn_data['groups']['resources_loot'];
  $crystal_rate = $config->rpg_exchange_crystal / $config->rpg_exchange_metal;
  $deuterium_rate = $config->rpg_exchange_deterium / $config->rpg_exchange_metal;

  $StatDate   = $time_now;

  $sta_update_step = -1;

  sta_set_time_limit('starting update');

  doquery('START TRANSACTION;');

  sta_set_time_limit('archiving old statistic');
  // Statistic rotation
  doquery ( "DELETE FROM {{statpoints}} WHERE `stat_code` = 2;");
  doquery ( "UPDATE {{statpoints}} SET `stat_code` = `stat_code` + 1;");

  sta_set_time_limit('calculating flying fleets stats');
  // Calculation of Fleet-In-Flight
  $UsrFleets = doquery("SELECT fleet_owner, fleet_array, fleet_resource_metal, fleet_resource_crystal, fleet_resource_deuterium FROM {{fleets}};");
  $i = 0;
  $row_num = mysql_num_rows($UsrFleets);
  while ($fleet_row = mysql_fetch_assoc($UsrFleets))
  {
    if($i % 100 == 0)
    {
      sta_set_time_limit("calculating flying fleets stats (fleet {$i}/{$row_num})", false);
    }
    $i++;

    $split = trim(str_replace(';',' ',$fleet_row['fleet_array']));
    $split = explode(' ',$split);

    $FleetCounts = 0;
    $FleetPoints = 0;
    foreach($split as $ship) {
      list($typ,$amount) = explode(',',$ship);
      $Units = $sn_data[ $typ ]['metal'] + $sn_data[ $typ ]['crystal'] * $crystal_rate + $sn_data[ $typ ]['deuterium'] * $deuterium_rate;
      $FleetPoints   += ($Units * $amount);
      $FleetCounts   += $amount;
    }

    $ResourceCount = 0;
    $ResourcePoint = 0;
    foreach($sn_groups_resources_loot as $resource_name) {
      $resource_amount = $fleet_row["fleet_resource_{$sn_data[$resource_name]['name']}"];
      if ( $resource_amount > 0) {
        $ResourceCount   += $resource_amount;
        $ResourcePoint   += $resource_amount;
      }
    }

    $user_id = $fleet_row['fleet_owner'];

    $counts[$user_id]['fleet'] += $FleetCounts;
    $points[$user_id]['fleet'] += $FleetPoints / 1000;

    $counts[$user_id]['resources'] += $ResourceCount;
    $points[$user_id]['resources'] += $ResourcePoint / 1000;
  }

  sta_set_time_limit('calculating planets stats');
  // This is only admin-used as I know so far. Did I really need it as admin?!
  $UsrPlanets = doquery("SELECT * FROM {{planets}};");
  $i = 0;
  $row_num = mysql_num_rows($UsrPlanets);
  while ($planet_row = mysql_fetch_assoc($UsrPlanets) ) {
    if($i % 100 == 0)
    {
      sta_set_time_limit("calculating planets stats (planet {$i}/{$row_num})", false);
    }
    $i++;

    $BuildCounts = 0;
    $BuildPoints = 0;
    foreach($sn_data['groups']['build'] as $n => $Building)
    {
      $unit_db_name = $sn_data[ $Building ]['name'];
      if ( $planet_row[$unit_db_name] > 0 )
      {
        $f = $sn_data[$Building]['factor'];
        $BuildPoints += ($sn_data[$Building]['metal'] + $sn_data[$Building]['crystal'] * $crystal_rate + $sn_data[$Building]['deuterium'] * $deuterium_rate) * (pow($f, $planet_row[$unit_db_name] ) - $f) / ($f - 1);
        $BuildCounts += $planet_row[$unit_db_name] - 1 ;
      }
    }

    $DefenseCounts = 0;
    $DefensePoints = 0;
    foreach($sn_data['groups']['defense'] as $n => $Defense)
    {
      $unit_db_name = $sn_data[$Defense]['name'];
      if ($planet_row[$unit_db_name] > 0)
      {
        $Units          = $sn_data[ $Defense ]['metal'] + $sn_data[ $Defense ]['crystal'] * $crystal_rate + $sn_data[ $Defense ]['deuterium'] * $deuterium_rate;
        $DefensePoints += ($Units * $planet_row[ $unit_db_name ]);
        $DefenseCounts += $planet_row[ $unit_db_name ];
      }
    }

    $FleetCounts = 0;
    $FleetPoints = 0;
    foreach($sn_data['groups']['fleet'] as $n => $Fleet)
    {
      $unit_db_name = $sn_data[$Fleet]['name'];
      if ($planet_row[$unit_db_name] > 0)
      {
        $Units          = $sn_data[ $Fleet ]['metal'] + $sn_data[ $Fleet ]['crystal'] * $crystal_rate + $sn_data[ $Fleet ]['deuterium'] * $deuterium_rate;
        $FleetPoints   += ($Units * $planet_row[ $unit_db_name ]);
        $FleetCounts   += $planet_row[ $unit_db_name ];
      }
    }

    $ResourceCount = 0;
    $ResourcePoint = 0;
    foreach($sn_groups_resources_loot as $resource_name) 
    {
      $resource_amount = $planet_row[$sn_data[$resource_name]['name']];
      if ( $resource_amount > 0) 
      {
        $ResourceCount   += $resource_amount;
        $ResourcePoint   += $resource_amount;
      }
    }

    if($planet_row['b_hangar_id'])
    {
      $ship_list = flt_expand(array('fleet_array' => $planet_row['b_hangar_id']));
      foreach($ship_list as $ship_id => $ship_amount)
      {
        $data = $sn_data[$ship_id];
        $ResourcePoint += ($data['metal'] + $data['crystal'] * $crystal_rate + $data['deuterium'] * $deuterium_rate) * $ship_amount;
        $ResourceCount += ($data['metal'] + $data['crystal'] * $crystal_rate + $data['deuterium'] * $deuterium_rate) * $ship_amount;
      }
    }

    $userID = $planet_row['id_owner'];

    $counts[$userID]['build'] += $BuildCounts;
    $counts[$userID]['defs']  += $DefenseCounts;
    $counts[$userID]['fleet'] += $FleetCounts;
    $counts[$userID]['resources'] += $ResourceCount;

    $points[$userID]['build'] += $BuildPoints / 1000;
    $points[$userID]['defs']  += $DefensePoints / 1000;
    $points[$userID]['fleet'] += $FleetPoints / 1000;
    $points[$userID]['resources'] += $ResourcePoint / 1000;

    $PlanetPoints = ($RetValue['BuildPoint'] + $RetValue['DefensePoint'] + $RetValue['FleetPoint'] + $RetValue['ResourcePoint']) / 1000;
    doquery ("UPDATE {{planets}} SET `points` = '{$PlanetPoints}' WHERE `id` = '{$planet_row['id']}';");
  }

  sta_set_time_limit('posting new user stats to DB');
  $GameUsers = doquery("SELECT * FROM {{users}};");
  while ($user_row = mysql_fetch_assoc($GameUsers))
  {
    $TechCounts = 0;
    $TechPoints = 0;
    foreach($sn_data['groups']['tech'] as $n => $Techno )
    {
      $unit_db_name = $sn_data[$Techno]['name'];
      if ( $user_row[$unit_db_name] > 0 )
      {
        $f = $sn_data[ $Techno ]['factor'];
//        $Units = $sn_data[ $Techno ]['metal'] + $sn_data[ $Techno ]['crystal'] * $crystal_rate + $sn_data[ $Techno ]['deuterium'] * $deuterium_rate;
        $TechCounts += $user_row[$unit_db_name] - 1 ;
        $TechPoints += ($sn_data[ $Techno ]['metal'] + $sn_data[ $Techno ]['crystal'] * $crystal_rate + $sn_data[ $Techno ]['deuterium'] * $deuterium_rate) * (pow($f, $user_row[$unit_db_name] ) - $f) / ($f - 1);
      }
    }

    $userID = $user_row['id'];
    $counts[$userID]['tech'] = $TechCounts;
    $points[$userID]['tech'] = $TechPoints / 1000;

    array_walk($points[$userID], 'floor');

    $GPoints = array_sum($points[$userID]);
    $GCount  = array_sum($counts[$userID]);

    $QryInsertStats  = "INSERT INTO {{statpoints}} SET ";
    $QryInsertStats .= "`id_owner` = '". $userID ."', ";
    $QryInsertStats .= "`id_ally` = ". ($user_row['ally_id'] ? $user_row['ally_id'] : 'NULL') .", ";
    $QryInsertStats .= "`stat_type` = '1', "; // 1 pour joueur , 2 pour alliance
    $QryInsertStats .= "`stat_code` = '1', "; // de 1 a 2 mis a jour de maniere automatique
    $QryInsertStats .= "`tech_points` = '". $points[$userID]['tech'] ."', ";
    $QryInsertStats .= "`tech_count` = '". $counts[$userID]['tech'] ."', ";
    $QryInsertStats .= "`build_points` = '". $points[$userID]['build'] ."', ";
    $QryInsertStats .= "`build_count` = '". $counts[$userID]['build'] ."', ";
    $QryInsertStats .= "`defs_points` = '". $points[$userID]['defs'] ."', ";
    $QryInsertStats .= "`defs_count` = '". $counts[$userID]['defs'] ."', ";
    $QryInsertStats .= "`fleet_points` = '". $points[$userID]['fleet'] ."', ";
    $QryInsertStats .= "`fleet_count` = '". $counts[$userID]['fleet'] ."', ";
    $QryInsertStats .= "`res_points` = '". $points[$userID]['resources'] ."', ";
    $QryInsertStats .= "`res_count` = '". $counts[$userID]['resources'] ."', ";
    $QryInsertStats .= "`total_points` = '". $GPoints ."', ";
    $QryInsertStats .= "`total_count` = '". $GCount ."', ";
    $QryInsertStats .= "`stat_date` = '". $StatDate ."';";
    doquery ( $QryInsertStats);
  }

  sta_set_time_limit('setting previous user stats from archive');
  doquery ("
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
  " );

  // Some variables we need to update ranks
  $qryResetRowNum = 'SET @rownum=0;';
  $qryFormat = 'UPDATE {{statpoints}} SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 ORDER BY `%1$s_points` DESC, `id_owner` ASC, `id_ally` ASC;';
  $rankNames = array( 'tech', 'build', 'defs', 'fleet', 'res', 'total');

  sta_set_time_limit("updating ranks for players");
  // Updating player's ranks
  foreach($rankNames as $rankName){
    sta_set_time_limit("updating player rank '{$rankName}'", false);
    doquery ( $qryResetRowNum);
    doquery ( sprintf($qryFormat, $rankName, 1));
  }

  sta_set_time_limit('posting new Alliance stats to DB');
  // Updating Allie's stats
  $QryInsertStats  = "
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
        {$StatDate}, NULL, u.`id_ally`, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.res_rank, a.total_rank
      FROM {{statpoints}} as u
        LEFT JOIN {{statpoints}} as a ON a.id_ally = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
      WHERE u.`stat_type` = 1 AND u.stat_code = 1 AND u.id_ally<>0
      GROUP BY u.`id_ally`";
  doquery ( $QryInsertStats );

  sta_set_time_limit("updating ranks for Alliances");
  // --- Updating Allie's ranks
  foreach($rankNames as $rankName){
    sta_set_time_limit("updating Alliances rank '{$rankName}'", false);
    doquery ( $qryResetRowNum);
    doquery ( sprintf($qryFormat, $rankName, 2) );
  }

  sta_set_time_limit('updating player\'s current rank and points');
  doquery("UPDATE {{users}} AS u JOIN {{statpoints}} AS sp ON sp.id_owner = u.id AND sp.stat_code = 1 AND sp.stat_type = 1 SET u.total_rank = sp.total_rank, u.total_points = sp.total_points;");

  sta_set_time_limit('updating Ally\'s current rank and points');
  doquery("UPDATE {{alliance}} AS a JOIN {{statpoints}} AS sp ON sp.id_ally = a.id AND sp.stat_code = 1 AND sp.stat_type = 2 SET a.total_rank = sp.total_rank, a.total_points = sp.total_points;");

  // sta_set_time_limit('purging outdated statistics from archive');
  // Deleting old stat_code
  // doquery ("DELETE FROM {{statpoints}} WHERE stat_code = 2;");

  // Counting real user count and updating values
  $userCount = doquery ( "SELECT COUNT(*) AS users_online FROM {{users}}", '', true);
  $config->db_saveItem('users_amount', $userCount['users_online']);

  doquery('COMMIT');
}

?>
