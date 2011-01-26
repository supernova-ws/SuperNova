<?php

/**
 * sys_stat_functions.php
 *
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

function GetPlanetPoints ( $CurrentPlanet ) {
  global $resource, $pricelist, $reslist, $sn_data, $sn_groups;

  $BuildCounts = 0;
  $BuildPoints = 0;
  foreach($reslist['build'] as $n => $Building) {
    if ( $CurrentPlanet[ $resource[ $Building ] ] > 0 ) {
      $f = $pricelist[$Building]['factor'];
      $BuildPoints += ($pricelist[$Building]['metal'] + $pricelist[$Building]['crystal'] + $pricelist[$Building]['deuterium']) * (pow($f, $CurrentPlanet[$resource[$Building]] ) - $f) / ($f - 1);
      $BuildCounts += $CurrentPlanet[$resource[$Building]] - 1 ;
    }
  }
  $RetValue['BuildCount'] = $BuildCounts;
  $RetValue['BuildPoint'] = $BuildPoints;

  $DefenseCounts = 0;
  $DefensePoints = 0;
  foreach($reslist['defense'] as $n => $Defense) {
    if ($CurrentPlanet[ $resource[ $Defense ] ] > 0) {
      $Units          = $pricelist[ $Defense ]['metal'] + $pricelist[ $Defense ]['crystal'] + $pricelist[ $Defense ]['deuterium'];
      $DefensePoints += ($Units * $CurrentPlanet[ $resource[ $Defense ] ]);
      $DefenseCounts += $CurrentPlanet[ $resource[ $Defense ] ];
    }
  }
  $RetValue['DefenseCount'] = $DefenseCounts;
  $RetValue['DefensePoint'] = $DefensePoints;

  $FleetCounts = 0;
  $FleetPoints = 0;
  foreach($reslist['fleet'] as $n => $Fleet) {
    if ($CurrentPlanet[ $resource[ $Fleet ] ] > 0) {
      $Units          = $pricelist[ $Fleet ]['metal'] + $pricelist[ $Fleet ]['crystal'] + $pricelist[ $Fleet ]['deuterium'];
      $FleetPoints   += ($Units * $CurrentPlanet[ $resource[ $Fleet ] ]);
      $FleetCounts   += $CurrentPlanet[ $resource[ $Fleet ] ];
    }
  }
  $RetValue['FleetCount'] = $FleetCounts;
  $RetValue['FleetPoint'] = $FleetPoints;

  $ResourceCount = 0;
  $ResourcePoint = 0;
  foreach($sn_groups['resources_loot'] as $resource_name) {
    $resource_amount = $CurrentPlanet[$resource[$resource_name]];
    if ( $resource_amount > 0) {
      $ResourceCount   += $resource_amount;
      $ResourcePoint   += $resource_amount;
    }
  }

  if($CurrentPlanet['b_hangar_id'])
  {
    $ship_list = flt_expand(array('fleet_array' => $CurrentPlanet['b_hangar_id']));
    foreach($ship_list as $ship_id => $ship_amount)
    {
      $data = $sn_data[$ship_id];
      $ResourcePoint += ($data['metal'] + $data['crystal'] + $data['deuterium']) * $ship_amount;
      $ResourceCount += ($data['metal'] + $data['crystal'] + $data['deuterium']) * $ship_amount;
    }
  }
  $RetValue['ResourceCount'] = $ResourceCount;
  $RetValue['ResourcePoint'] = $ResourcePoint;

  return $RetValue;
}

function GetTechnoPoints ( $CurrentUser ) {
  global $resource, $pricelist, $reslist;

  $TechCounts = 0;
  $TechPoints = 0;
  foreach ( $reslist['tech'] as $n => $Techno ) {
    if ( $CurrentUser[ $resource[ $Techno ] ] > 0 ) {
      $f = $pricelist[ $Techno ]['factor'];
      $Units = $pricelist[ $Techno ]['metal'] + $pricelist[ $Techno ]['crystal'] + $pricelist[ $Techno ]['deuterium'];
      $TechCounts += $CurrentUser[ $resource[ $Techno ] ] - 1 ;
      $TechPoints += ($pricelist[ $Techno ]['metal'] + $pricelist[ $Techno ]['crystal'] + $pricelist[ $Techno ]['deuterium']) * (pow($f, $CurrentUser[$resource[$Techno]] ) - $f) / ($f - 1);
    }
  }
  $RetValue['TechCount'] = $TechCounts;
  $RetValue['TechPoint'] = $TechPoints;

  return $RetValue;
}

function GetFleetPointsOnTour ( $CurrentFleet ) {
  global $resource, $pricelist, $reslist, $sn_groups;

  $FleetCounts = 0;
  $FleetPoints = 0;

  $split = trim(str_replace(';',' ',$CurrentFleet['fleet_array']));
  $split = explode(' ',$split);

  foreach($split as $ship) {
    list($typ,$amount) = explode(',',$ship);
    $Units = $pricelist[ $typ ]['metal'] + $pricelist[ $typ ]['crystal'] + $pricelist[ $typ ]['deuterium'];
    $FleetPoints   += ($Units * $amount);
    $FleetCounts   += $amount;
  }
  $RetValue['FleetCount'] = $FleetCounts;
  $RetValue['FleetPoint'] = $FleetPoints;

  $ResourceCount = 0;
  $ResourcePoint = 0;
  foreach($sn_groups['resources_loot'] as $resource_name) {
    $resource_amount = $CurrentFleet["fleet_resource_{$resource[$resource_name]}"];
    if ( $resource_amount > 0) {
      $ResourceCount   += $resource_amount;
      $ResourcePoint   += $resource_amount;
    }
  }
  $RetValue['ResourceCount'] = $ResourceCount;
  $RetValue['ResourcePoint'] = $ResourcePoint;

  return $RetValue;
}

function SYS_statCalculate(){
  global $config, $time_now, $sta_update_step;

  $StatDate   = $time_now;

  $sta_update_step = 0;

//  doquery('START TRANSACTION;');

  sta_set_time_limit('archiving old statistic');
  // Statistic rotation
  doquery ( "DELETE FROM {{statpoints}} WHERE `stat_code` = 2;");
  doquery ( "UPDATE {{statpoints}} SET `stat_code` = `stat_code` + 1;");

  sta_set_time_limit('calculating flying fleets stats');
  // Calculation of Fleet-In-Flight
  $UsrFleets = doquery("SELECT * FROM {{fleets}};");
  $i = 0;
  $row_num = mysql_num_rows($UsrFleets);
  while ($CurFleet = mysql_fetch_assoc($UsrFleets))
  {
    if($i % 100 == 0)
    {
      sta_set_time_limit("calculating flying fleets stats (fleet {$i}/{$row_num})", false);
    }
    $i++;

    $Points = GetFleetPointsOnTour ( $CurFleet );
    $counts[$CurFleet['fleet_owner']]['fleet'] += $Points['FleetCount'];
    $points[$CurFleet['fleet_owner']]['fleet'] += $Points['FleetPoint'] / 1000;

    $counts[$CurFleet['fleet_owner']]['resources'] += $Points['ResourceCount'];
    $points[$CurFleet['fleet_owner']]['resources'] += $Points['ResourcePoint'] / 1000;
  }

  sta_set_time_limit('calculating planets stats');
  // This is only admin-used as I know so far. Did I really need it as admin?!
  $UsrPlanets = doquery("SELECT * FROM {{planets}};");
  $i = 0;
  $row_num = mysql_num_rows($UsrPlanets);
  while ($CurPlanet = mysql_fetch_assoc($UsrPlanets) ) {
    if($i % 100 == 0)
    {
      sta_set_time_limit("calculating planets stats (planet {$i}/{$row_num})", false);
    }
    $i++;

    $userID = $CurPlanet['id_owner'];
    $Points = GetPlanetPoints ( $CurPlanet );

    $counts[$userID]['build'] += $Points['BuildCount'];
    $counts[$userID]['defs']  += $Points['DefenseCount'];
    $counts[$userID]['fleet'] += $Points['FleetCount'];
    $counts[$userID]['resources'] += $Points['ResourceCount'];

    $points[$userID]['build'] += $Points['BuildPoint'] / 1000;
    $points[$userID]['defs']  += $Points['DefensePoint'] / 1000;
    $points[$userID]['fleet'] += $Points['FleetPoint'] / 1000;
    $points[$userID]['resources'] += $Points['ResourcePoint'] / 1000;

    $PlanetPoints = ($Points['BuildPoint'] + $Points['DefensePoint'] + $Points['FleetPoint'] + $Points['ResourcePoint']) / 1000;
    doquery ("UPDATE {{planets}} SET `points` = '{$PlanetPoints}' WHERE `id` = '{$CurPlanet['id']}';");
  }

  sta_set_time_limit('posting new user stats to DB');
  $GameUsers = doquery("SELECT * FROM {{users}};");
  while ($CurUser = mysql_fetch_assoc($GameUsers)) {
    $userID = $CurUser['id'];

    $Points = GetTechnoPoints ( $CurUser );

    $counts[$userID]['tech'] = $Points['TechCount'];
    $points[$userID]['tech'] = $Points['TechPoint'] / 1000;

    $GPoints = array_sum($points[$userID]);
    $GCount  = array_sum($counts[$userID]);

    $QryInsertStats  = "INSERT INTO {{statpoints}} SET ";
    $QryInsertStats .= "`id_owner` = '". $userID ."', ";
    $QryInsertStats .= "`id_ally` = '". $CurUser['ally_id'] ."', ";
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
  $qryFormat = 'UPDATE {{statpoints}} SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 ORDER BY `%1$s_points` DESC, `id_owner` ASC;';
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
        {$StatDate}, u.`id_ally`, 0, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.res_rank, a.total_rank
      FROM {{statpoints}} as u
        LEFT JOIN {{statpoints}} as a ON a.id_owner = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
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

  sta_set_time_limit('purging outdated statistics from archive');
  // Deleting old stat_code
  // doquery ("DELETE FROM {{statpoints}} WHERE stat_code = 2;");

  // Counting real user count and updating values
  $userCount = doquery ( "SELECT COUNT(*) FROM {{users}}", '', true);
  $config->db_saveItem('users_amount', $userCount[0]);

//  doquery('COMMIT');
}

?>
