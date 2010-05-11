<?php

/**
 * StatFunctions.php
 *
 * @version 2
 *   [!] Stats calculation greatly increased to 10x times or even more!
 *   [!] Now all ranks counting inside of this file - no unnecessary UPDATEs in /stats.php
 *   [*] All planet funcs merged into GetPlanetPoints
 *   [*] GetPlanetPoints optimized to utilize properties of geometrical progression
 *   [*] Planet-related calculations now made with one SQL-query for all planets - not per user
 *   [*] Greatly improved insertions of new Allie's stat by moving all calculation to single query
 *   [*] Greatly improved speed of updating Player's rank by moving every rank update to single query
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */
function GetPlanetPoints ( $CurrentPlanet ) {
  global $resource, $pricelist, $reslist;

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
  global $resource, $pricelist, $reslist;

  $FleetCounts = 0;
  $FleetPoints = 0;

  $split = trim(str_replace(';',' ',$CurrentFleet));
  $split = explode(' ',$split);

  foreach($split as $ship) {
    list($typ,$amount) = explode(',',$ship);
    $Units = $pricelist[ $typ ]['metal'] + $pricelist[ $typ ]['crystal'] + $pricelist[ $typ ]['deuterium'];
    $FleetPoints   += ($Units * $amount);
    $FleetCounts   += $amount;
  }

  $RetValue['FleetCount'] = $FleetCounts;
  $RetValue['FleetPoint'] = $FleetPoints;

  return $RetValue;
}

function SYS_statCalculate(){
  $StatDate   = time();
/*
  // Getting old ranks (WHY HERE?!)
  $OldStatRecord  = doquery ("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = 1;", 'statpoints');
  while($o = mysql_fetch_array($OldStatRecord)) {
    // id_owner
    $OldTotalRank[$o['id_owner']] = $o['total_rank'];
    $OldTechRank[$o['id_owner']] = $o['tech_rank'];
    $OldBuildRank[$o['id_owner']] = $o['build_rank'];
    $OldDefsRank[$o['id_owner']] = $o['defs_rank'];
    $OldFleetRank[$o['id_owner']] = $o['fleet_rank'];
  }
*/
  // Rotation des statistiques
  doquery ( "DELETE FROM {{table}} WHERE `stat_code` = '2';" , 'statpoints');
  doquery ( "UPDATE {{table}} SET `stat_code` = `stat_code` + '1';" , 'statpoints');

  $UsrFleets      = doquery("SELECT * FROM {{table}};", 'fleets');
  while ($CurFleet = mysql_fetch_assoc($UsrFleets)) {
    $Points       = GetFleetPointsOnTour ( $CurFleet['fleet_array'] );
    $counts[$CurFleet['fleet_owner']]['fleet'] += $Points['FleetCount'];
    $points[$CurFleet['fleet_owner']]['fleet'] += ($Points['FleetPoint'] / 1000);
  }

  // This is only admin-used as I know so far. Did I really need it as admin?!
  $UsrPlanets     = doquery("SELECT * FROM {{table}};", 'planets');
  while ($CurPlanet = mysql_fetch_assoc($UsrPlanets) ) {
    $Points           = GetPlanetPoints ( $CurPlanet );

    $counts[$CurPlanet['id_owner']]['build'] += $Points['BuildCount'];
    $counts[$CurPlanet['id_owner']]['defs']  += $Points['DefenseCount'];
    $counts[$CurPlanet['id_owner']]['fleet'] += $Points['FleetCount'];

    $points[$CurPlanet['id_owner']]['build'] += $Points['BuildPoint'] / 1000;
    $points[$CurPlanet['id_owner']]['defs']  += $Points['DefensePoint'] / 1000;
    $points[$CurPlanet['id_owner']]['fleet'] += $Points['FleetPoint'] / 1000;

    $PlanetPoints     = ($Points['BuildPoint'] + $Points['DefensePoint'] + $Points['FleetPoint']) / 1000;

    $QryUpdatePlanet  = "UPDATE {{table}} SET `points` = '". $PlanetPoints ."' WHERE `id` = '". $CurPlanet['id'] ."';";
    doquery ( $QryUpdatePlanet , 'planets');
  }

  $GameUsers  = doquery("SELECT * FROM {{table}}", 'users');
  while ($CurUser = mysql_fetch_assoc($GameUsers)) {
    $userID = $CurUser['id'];
    $Points                         = GetTechnoPoints ( $CurUser );
    $counts[$CurUser['id']]['tech'] = $Points['TechCount'];
    $points[$CurUser['id']]['tech'] = $Points['TechPoint'] / 1000;

    $GPoints = array_sum($points[$userID]);
    $GCount  = array_sum($counts[$userID]);

    $QryInsertStats  = "INSERT INTO {{table}} SET ";
    $QryInsertStats .= "`id_owner` = '". $userID ."', ";
    $QryInsertStats .= "`id_ally` = '". $CurUser['ally_id'] ."', ";
    $QryInsertStats .= "`stat_type` = '1', "; // 1 pour joueur , 2 pour alliance
    $QryInsertStats .= "`stat_code` = '1', "; // de 1 a 2 mis a jour de maniere automatique
    $QryInsertStats .= "`tech_points` = '". $points[$userID]['tech'] ."', ";
    $QryInsertStats .= "`tech_count` = '". $counts[$userID]['tech'] ."', ";
//    $QryInsertStats .= "`tech_old_rank` = '". $OldTechRank[$userID] ."', ";
    $QryInsertStats .= "`build_points` = '". $points[$userID]['build'] ."', ";
    $QryInsertStats .= "`build_count` = '". $counts[$userID]['build'] ."', ";
//    $QryInsertStats .= "`build_old_rank` = '". $OldBuildRank[$userID] ."', ";
    $QryInsertStats .= "`defs_points` = '". $points[$userID]['defs'] ."', ";
    $QryInsertStats .= "`defs_count` = '". $counts[$userID]['defs'] ."', ";
//    $QryInsertStats .= "`defs_old_rank` = '". $OldDefsRank[$userID] ."', ";
    $QryInsertStats .= "`fleet_points` = '". $points[$userID]['fleet'] ."', ";
    $QryInsertStats .= "`fleet_count` = '". $counts[$userID]['fleet'] ."', ";
//    $QryInsertStats .= "`fleet_old_rank` = '". $OldFleetRank[$userID] ."', ";
    $QryInsertStats .= "`total_points` = '". $GPoints ."', ";
    $QryInsertStats .= "`total_count` = '". $GCount ."', ";
//    $QryInsertStats .= "`total_old_rank` = '". $OldTotalRank[$userID] ."', ";
    $QryInsertStats .= "`stat_date` = '". $StatDate ."';";
    doquery ( $QryInsertStats , 'statpoints');
  }

  doquery ("
    UPDATE {{table}} as new
      LEFT JOIN {{table}} as old ON old.id_owner = new.id_owner AND old.stat_code = 2 AND old.stat_type = 1
    SET
      new.tech_old_rank = old.tech_rank,
      new.build_old_rank = old.build_rank,
      new.defs_old_rank  = old.defs_rank ,
      new.fleet_old_rank = old.fleet_rank,
      new.total_old_rank = old.total_rank
    WHERE
      new.stat_type = 1 AND new.stat_code = 1;
  " , 'statpoints');

  // some variables we need to update ranks
  $qryResetRowNum = 'SET @rownum=0;';
  $qryFormat = 'UPDATE {{table}} SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 ORDER BY `%1$s_points` DESC;';
  $rankNames = array( 'tech', 'build', 'defs', 'fleet', 'total',);

  // Updating player's ranks
  foreach($rankNames as $rankName){
    doquery ( $qryResetRowNum, 'statpoints');
    doquery ( sprintf($qryFormat, $rankName, 1) , 'statpoints');
  }

  // Updating Allie's stats
  $QryInsertStats  = "
    INSERT INTO {{table}}
      (`tech_points`, `tech_count`, `build_points`, `build_count`, `defs_points`, `defs_count`,
        `fleet_points`, `fleet_count`, `total_points`, `total_count`,
        `stat_date`, `id_owner`, `id_ally`, `stat_type`, `stat_code`,
        `tech_old_rank`, `build_old_rank`, `defs_old_rank`, `fleet_old_rank`, `total_old_rank`
      )
      SELECT
        SUM(u.`tech_points`), SUM(u.`tech_count`), SUM(u.`build_points`), SUM(u.`build_count`), SUM(u.`defs_points`),
        SUM(u.`defs_count`), SUM(u.`fleet_points`), SUM(u.`fleet_count`), SUM(u.`total_points`), SUM(u.`total_count`),
        {$StatDate}, u.`id_ally`, 0, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.total_rank
      FROM {{table}} as u
        LEFT JOIN {{table}} as a ON a.id_owner = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
      WHERE u.`stat_type` = 1 AND u.stat_code = 1 AND u.id_ally<>0
      GROUP BY u.`id_ally`";
  doquery ( $QryInsertStats , 'statpoints');

  // Updating Allie's ranks
  foreach($rankNames as $rankName){
    doquery ( $qryResetRowNum, 'statpoints');
    doquery ( sprintf($qryFormat, $rankName, 2) , 'statpoints');
  }

  // Deleting old stat_code
  // doquery ("DELETE FROM {{table}} WHERE stat_code = 2;",'statpoints');

  // Counting real user count and updating values
  $userCount = doquery ( "SELECT COUNT(*) FROM {{table}}", 'users', true);
  $game_config['users_amount'] = $userCount;
  $config->users_amount = $userCount;
  doquery( "UPDATE {{table}} SET `config_value`='". $userCount[0] ."' WHERE `config_name` = 'users_amount';", 'config' );

//  doquery("COMMIT", 'users');
}
?>