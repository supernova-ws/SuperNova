<?php

/**
 * StatFunctions.php
 *
 * @version 2
 *   [*] All planet funcs merged into GetPlanetPoints
 *   [*] GetPlanetPoints optimized to utilize properties of geometrical progression
 *   [*] OldStat-related calculations now made with one SQL-query for all oldstats - not per user!
 *   [*] Planet-related calculations now made with one SQL-query for all planets - not per user
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

  // Rotation des statistiques
  doquery ( "DELETE FROM {{table}} WHERE `stat_code` = '2';" , 'statpoints');
  doquery ( "UPDATE {{table}} SET `stat_code` = `stat_code` + '1';" , 'statpoints');

  $OldStatRecord  = doquery ("SELECT * FROM {{table}} WHERE `stat_type` = '1';", 'statpoints');
  while($o = mysql_fetch_array($OldStatRecord)) {
    // id_owner
    $OldTotalRank[$o['id_owner']] = $o['total_rank'];
    $OldTechRank[$o['id_owner']] = $o['tech_rank'];
    $OldBuildRank[$o['id_owner']] = $o['build_rank'];
    $OldDefsRank[$o['id_owner']] = $o['defs_rank'];
    $OldFleetRank[$o['id_owner']] = $o['fleet_rank'];
  }
  // Suppression de l'ancien enregistrement
  doquery ("DELETE FROM {{table}} WHERE `stat_type` = '1';",'statpoints');

  $UsrFleets      = doquery("SELECT * FROM {{table}};", 'fleets');
  while ($CurFleet = mysql_fetch_assoc($UsrFleets)) {
    $Points       = GetFleetPointsOnTour ( $CurFleet['fleet_array'] );
    $counts[$CurFleet['fleet_owner']]['fleet'] += $Points['FleetCount'];
    $points[$CurFleet['fleet_owner']]['fleet'] += ($Points['FleetPoint'] / 1000);
  }

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

    $QryUpdatePlanet  = "UPDATE {{table}} SET ";
    $QryUpdatePlanet .= "`points` = '". $PlanetPoints ."' ";
    $QryUpdatePlanet .= "WHERE ";
    $QryUpdatePlanet .= "`id` = '". $CurPlanet['id'] ."';";
    doquery ( $QryUpdatePlanet , 'planets');
  }

  $GameUsers  = doquery("SELECT * FROM {{table}}", 'users');
  while ($CurUser = mysql_fetch_assoc($GameUsers)) {
    $Points         = GetTechnoPoints ( $CurUser );
    $counts[$CurUser['id']]['tech']    = $Points['TechCount'];
    $points[$CurUser['id']]['tech']    = $Points['TechPoint'] / 1000;

    $GPoints = array_sum($points[$CurUser['id']]);
    $GCount  = array_sum($counts[$CurUser['id']]);

    $QryInsertStats  = "INSERT INTO {{table}} SET ";
    $QryInsertStats .= "`id_owner` = '". $CurUser['id'] ."', ";
    $QryInsertStats .= "`id_ally` = '". $CurUser['ally_id'] ."', ";
    $QryInsertStats .= "`stat_type` = '1', "; // 1 pour joueur , 2 pour alliance
    $QryInsertStats .= "`stat_code` = '1', "; // de 1 a 2 mis a jour de maniere automatique
    $QryInsertStats .= "`tech_points` = '". $points[$CurUser['id']]['tech'] ."', ";
    $QryInsertStats .= "`tech_count` = '". $counts[$CurUser['id']]['tech'] ."', ";
    $QryInsertStats .= "`tech_old_rank` = '". $OldTechRank[$CurUser['id']] ."', ";
    $QryInsertStats .= "`build_points` = '". $points[$CurUser['id']]['build'] ."', ";
    $QryInsertStats .= "`build_count` = '". $counts[$CurUser['id']]['build'] ."', ";
    $QryInsertStats .= "`build_old_rank` = '". $OldBuildRank[$CurUser['id']] ."', ";
    $QryInsertStats .= "`defs_points` = '". $points[$CurUser['id']]['defs'] ."', ";
    $QryInsertStats .= "`defs_count` = '". $counts[$CurUser['id']]['defs'] ."', ";
    $QryInsertStats .= "`defs_old_rank` = '". $OldDefsRank[$CurUser['id']] ."', ";
    $QryInsertStats .= "`fleet_points` = '". $points[$CurUser['id']]['fleet'] ."', ";
    $QryInsertStats .= "`fleet_count` = '". $counts[$CurUser['id']]['fleet'] ."', ";
    $QryInsertStats .= "`fleet_old_rank` = '". $OldFleetRank[$CurUser['id']] ."', ";
    $QryInsertStats .= "`total_points` = '". $GPoints ."', ";
    $QryInsertStats .= "`total_count` = '". $GCount ."', ";
    $QryInsertStats .= "`total_old_rank` = '". $OldTotalRank[$CurUser['id']] ."', ";
    $QryInsertStats .= "`stat_date` = '". $StatDate ."';";
    doquery ( $QryInsertStats , 'statpoints');
  }

  $qryResetRowNum = 'SET @rownum=0;';
  $qryFormat = 'UPDATE {{table}} SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 ORDER BY `%1$s_points` DESC;';

  doquery ( $qryResetRowNum, 'statpoints');
  doquery ( sprintf($qryFormat, 'tech', 1) , 'statpoints');

  doquery ( $qryResetRowNum, 'statpoints');
  doquery ( sprintf($qryFormat, 'build', 1) , 'statpoints');

  doquery ( $qryResetRowNum, 'statpoints');
  doquery ( sprintf($qryFormat, 'defs', 1) , 'statpoints');

  doquery ( $qryResetRowNum, 'statpoints');
  doquery ( sprintf($qryFormat, 'fleet', 1) , 'statpoints');

  doquery ( $qryResetRowNum, 'statpoints');
  doquery ( sprintf($qryFormat, 'total', 1) , 'statpoints');

  // Updating Allie's stats
  $QryInsertStats  = "
    INSERT INTO {{table}}
      (`tech_points`, `tech_count`, `build_points`, `build_count`, `defs_points`, `defs_count`,
       `fleet_points`, `fleet_count`, `total_points`, `total_count`, `stat_date`,
       `id_owner`, `id_ally`, `stat_type`, `stat_code`, `tech_old_rank`, `build_old_rank`, `defs_old_rank`,
       `fleet_old_rank`, `total_old_rank`)
    SELECT
      SUM(u.`tech_points`), SUM(u.`tech_count`), SUM(u.`build_points`), SUM(u.`build_count`), SUM(u.`defs_points`),
      SUM(u.`defs_count`), SUM(u.`fleet_points`), SUM(u.`fleet_count`), SUM(u.`total_points`), SUM(u.`total_count`),
      {$StatDate}, u.`id_ally`, 0, 2, 1,
      a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.total_rank
    FROM {{table}} as u
      LEFT JOIN {{table}} as a ON a.id_ally=u.id_ally AND a.stat_code = 2 AND a.`stat_type` = 2
    WHERE u.`stat_type` = 1 and u.id_ally<>0
    GROUP BY u.`id_ally`";
  doquery ( $QryInsertStats , 'statpoints');

  // Deleting old stat_code
  doquery ("DELETE FROM {{table}} WHERE `stat_type` = '2' AND stat_code = 2;",'statpoints');

  //
  $userCount = doquery ( "SELECT COUNT(*) FROM {{table}}", 'users', true);
  doquery( "UPDATE {{table}} SET `config_value`='". $userCount[0] ."' WHERE `config_name` = 'users_amount';", 'config' );

//  doquery("COMMIT", 'users');
}
?>