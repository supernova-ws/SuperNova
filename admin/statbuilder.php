<?php

/**
 * StatBuilder.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

include($ugamela_root_path . 'admin/statfunctions.' . $phpEx);

if ($user['authlevel'] < 1) {
  AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
};

includeLang('admin');

$StatDate   = time();
// Rotation des statistiques
doquery ( "DELETE FROM {{table}} WHERE `stat_code` = '2';" , 'statpoints');
doquery ( "UPDATE {{table}} SET `stat_code` = `stat_code` + '1';" , 'statpoints');

$GameUsers  = doquery("SELECT * FROM {{table}}", 'users');

while ($CurUser = mysql_fetch_assoc($GameUsers)) {
  // Recuperation des anciennes statistiques
  $OldStatRecord  = doquery ("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `id_owner` = '".$CurUser['id']."';",'statpoints');
    while($o = mysql_fetch_array($OldStatRecord)) {
        $OldTotalRank = $o['total_rank'];
        $OldTechRank = $o['tech_rank'];
        $OldBuildRank = $o['build_rank'];
        $OldDefsRank = $o['defs_rank'];
        $OldFleetRank = $o['fleet_rank'];
        // Suppression de l'ancien enregistrement
        doquery ("DELETE FROM {{table}} WHERE `stat_type` = '1' AND `id_owner` = '".$CurUser['id']."';",'statpoints');
        }
/*
  if ($OldStatRecord) {
    $OldTotalRank = $OldStatRecord['total_rank'];
    $OldTechRank  = $OldStatRecord['tech_rank'];
    $OldBuildRank = $OldStatRecord['build_rank'];
    $OldDefsRank  = $OldStatRecord['defs_rank'];
    $OldFleetRank = $OldStatRecord['fleet_rank'];
    // Suppression de l'ancien enregistrement
    doquery ("DELETE FROM {{table}} WHERE `stat_type` = '1' AND `id_owner` = '".$CurUser['id']."';",'statpoints');
  } else {
    $OldTotalRank = 0;
    $OldTechRank  = 0;
    $OldBuildRank = 0;
    $OldDefsRank  = 0;
    $OldFleetRank = 0;
  }
*/
  // Total des unitées consommée pour la recherche
  $Points         = GetTechnoPoints ( $CurUser );
  $TTechCount     = $Points['TechCount'];
  $TTechPoints    = ($Points['TechPoint'] / 1000);

  // Totalisation des points accumulés par planete
  $TBuildCount    = 0;
  $TBuildPoints   = 0;
  $TDefsCount     = 0;
  $TDefsPoints    = 0;
  $TFleetCount    = 0;
  $TFleetPoints   = 0;
  $GCount         = $TTechCount;
  $GPoints        = $TTechPoints;
  $UsrPlanets     = doquery("SELECT * FROM {{table}} WHERE `id_owner` = '". $CurUser['id'] ."';", 'planets');
  while ($CurPlanet = mysql_fetch_assoc($UsrPlanets) ) {
    $Points           = GetBuildPoints ( $CurPlanet );
    $TBuildCount     += $Points['BuildCount'];
    $GCount          += $Points['BuildCount'];
    $PlanetPoints     = ($Points['BuildPoint'] / 1000);
    $TBuildPoints    += ($Points['BuildPoint'] / 1000);

    $Points           = GetDefensePoints ( $CurPlanet );
    $TDefsCount      += $Points['DefenseCount'];;
    $GCount          += $Points['DefenseCount'];
    $PlanetPoints    += ($Points['DefensePoint'] / 1000);
    $TDefsPoints     += ($Points['DefensePoint'] / 1000);

    $Points           = GetFleetPoints ( $CurPlanet );
    $TFleetCount     += $Points['FleetCount'];
    $GCount          += $Points['FleetCount'];
    $PlanetPoints    += ($Points['FleetPoint'] / 1000);
    $TFleetPoints    += ($Points['FleetPoint'] / 1000);

    $GPoints         += $PlanetPoints;
    $QryUpdatePlanet  = "UPDATE {{table}} SET ";
    $QryUpdatePlanet .= "`points` = '". $PlanetPoints ."' ";
    $QryUpdatePlanet .= "WHERE ";
    $QryUpdatePlanet .= "`id` = '". $CurPlanet['id'] ."';";
    doquery ( $QryUpdatePlanet , 'planets');
  }

  $UsrFleets      = doquery("SELECT * FROM {{table}} WHERE `fleet_owner` = '". $CurUser['id'] ."';", 'fleets');
  while ($CurFleet = mysql_fetch_assoc($UsrFleets)) {
      $Points       = GetFleetPointsOnTour ( $CurFleet['fleet_array'] );
      $TFleetCount += $Points['FleetCount'];
      $GCount      += $Points['FleetCount'];
      $TFleetPoints+= ($Points['FleetPoint'] / 1000);
      $PlanetPoints = $Points['FleetPoint'] / 1000;

      $GPoints     += $PlanetPoints;
  }

  $QryInsertStats  = "INSERT INTO {{table}} SET ";
  $QryInsertStats .= "`id_owner` = '". $CurUser['id'] ."', ";
  $QryInsertStats .= "`id_ally` = '". $CurUser['ally_id'] ."', ";
  $QryInsertStats .= "`stat_type` = '1', "; // 1 pour joueur , 2 pour alliance
  $QryInsertStats .= "`stat_code` = '1', "; // de 1 a 2 mis a jour de maniere automatique
  $QryInsertStats .= "`tech_points` = '". $TTechPoints ."', ";
  $QryInsertStats .= "`tech_count` = '". $TTechCount ."', ";
  $QryInsertStats .= "`tech_old_rank` = '". $OldTechRank ."', ";
  $QryInsertStats .= "`build_points` = '". $TBuildPoints ."', ";
  $QryInsertStats .= "`build_count` = '". $TBuildCount ."', ";
  $QryInsertStats .= "`build_old_rank` = '". $OldBuildRank ."', ";
  $QryInsertStats .= "`defs_points` = '". $TDefsPoints ."', ";
  $QryInsertStats .= "`defs_count` = '". $TDefsCount ."', ";
  $QryInsertStats .= "`defs_old_rank` = '". $OldDefsRank ."', ";
  $QryInsertStats .= "`fleet_points` = '". $TFleetPoints ."', ";
  $QryInsertStats .= "`fleet_count` = '". $TFleetCount ."', ";
  $QryInsertStats .= "`fleet_old_rank` = '". $OldFleetRank ."', ";
  $QryInsertStats .= "`total_points` = '". $GPoints ."', ";
  $QryInsertStats .= "`total_count` = '". $GCount ."', ";
  $QryInsertStats .= "`total_old_rank` = '". $OldTotalRank ."', ";
  $QryInsertStats .= "`stat_date` = '". $StatDate ."';";
  doquery ( $QryInsertStats , 'statpoints');
}

$Rank           = 1;
$RankQry        = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' ORDER BY `tech_points` DESC;", 'statpoints');
while ($TheRank = mysql_fetch_assoc($RankQry) ) {
  $QryUpdateStats  = "UPDATE {{table}} SET ";
  $QryUpdateStats .= "`tech_rank` = '". $Rank ."' ";
  $QryUpdateStats .= "WHERE ";
  $QryUpdateStats .= " `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $TheRank['id_owner'] ."';";
  doquery ( $QryUpdateStats , 'statpoints');
  $Rank++;
}

$Rank           = 1;
$RankQry        = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' ORDER BY `build_points` DESC;", 'statpoints');
while ($TheRank = mysql_fetch_assoc($RankQry) ) {
  $QryUpdateStats  = "UPDATE {{table}} SET ";
  $QryUpdateStats .= "`build_rank` = '". $Rank ."' ";
  $QryUpdateStats .= "WHERE ";
  $QryUpdateStats .= " `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $TheRank['id_owner'] ."';";
  doquery ( $QryUpdateStats , 'statpoints');
  $Rank++;
}

$Rank           = 1;
$RankQry        = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' ORDER BY `defs_points` DESC;", 'statpoints');
while ($TheRank = mysql_fetch_assoc($RankQry) ) {
  $QryUpdateStats  = "UPDATE {{table}} SET ";
  $QryUpdateStats .= "`defs_rank` = '". $Rank ."' ";
  $QryUpdateStats .= "WHERE ";
  $QryUpdateStats .= " `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $TheRank['id_owner'] ."';";
  doquery ( $QryUpdateStats , 'statpoints');
  $Rank++;
}

$Rank           = 1;
$RankQry        = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' ORDER BY `fleet_points` DESC;", 'statpoints');
while ($TheRank = mysql_fetch_assoc($RankQry) ) {
  $QryUpdateStats  = "UPDATE {{table}} SET ";
  $QryUpdateStats .= "`fleet_rank` = '". $Rank ."' ";
  $QryUpdateStats .= "WHERE ";
  $QryUpdateStats .= " `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $TheRank['id_owner'] ."';";
  doquery ( $QryUpdateStats , 'statpoints');
  $Rank++;
}

$Rank           = 1;
$RankQry        = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' ORDER BY `total_points` DESC;", 'statpoints');
while ($TheRank = mysql_fetch_assoc($RankQry) ) {
  $QryUpdateStats  = "UPDATE {{table}} SET ";
  $QryUpdateStats .= "`total_rank` = '". $Rank ."' ";
  $QryUpdateStats .= "WHERE ";
  $QryUpdateStats .= " `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $TheRank['id_owner'] ."';";
  doquery ( $QryUpdateStats , 'statpoints');
  $Rank++;
}

// Statistiques des alliances ...
$GameAllys  = doquery("SELECT * FROM {{table}}", 'alliance');

while ($CurAlly = mysql_fetch_assoc($GameAllys)) {
  // Recuperation des anciennes statistiques
  $OldStatRecord  = doquery ("SELECT * FROM {{table}} WHERE `stat_type` = '2' AND `id_owner` = '".$CurAlly['id']."';",'statpoints');
  if ($OldStatRecord) {
    $OldTotalRank = $OldStatRecord['total_rank'];
    $OldTechRank  = $OldStatRecord['tech_rank'];
    $OldBuildRank = $OldStatRecord['build_rank'];
    $OldDefsRank  = $OldStatRecord['defs_rank'];
    $OldFleetRank = $OldStatRecord['fleet_rank'];
    // Suppression de l'ancien enregistrement
    doquery ("DELETE FROM {{table}} WHERE `stat_type` = '2' AND `id_owner` = '".$CurAlly['id']."';",'statpoints');
  } else {
    $OldTotalRank = 0;
    $OldTechRank  = 0;
    $OldBuildRank = 0;
    $OldDefsRank  = 0;
    $OldFleetRank = 0;
  }

  // Total des unitées consommée pour la recherche
  $QrySumSelect   = "SELECT ";
  $QrySumSelect  .= "SUM(`tech_points`)  as `TechPoint`, ";
  $QrySumSelect  .= "SUM(`tech_count`)   as `TechCount`, ";
  $QrySumSelect  .= "SUM(`build_points`) as `BuildPoint`, ";
  $QrySumSelect  .= "SUM(`build_count`)  as `BuildCount`, ";
  $QrySumSelect  .= "SUM(`defs_points`)  as `DefsPoint`, ";
  $QrySumSelect  .= "SUM(`defs_count`)   as `DefsCount`, ";
  $QrySumSelect  .= "SUM(`fleet_points`) as `FleetPoint`, ";
  $QrySumSelect  .= "SUM(`fleet_count`)  as `FleetCount`, ";
  $QrySumSelect  .= "SUM(`total_points`) as `TotalPoint`, ";
  $QrySumSelect  .= "SUM(`total_count`)  as `TotalCount` ";
  $QrySumSelect  .= "FROM {{table}} ";
  $QrySumSelect  .= "WHERE ";
  $QrySumSelect  .= "`stat_type` = '1' AND ";
  $QrySumSelect  .= "`id_ally` = '". $CurAlly['id'] ."';";
  $Points         = doquery( $QrySumSelect, 'statpoints', true);

  $TTechCount     = $Points['TechCount'];
  $TTechPoints    = $Points['TechPoint'];
  $TBuildCount    = $Points['BuildCount'];
  $TBuildPoints   = $Points['BuildPoint'];
  $TDefsCount     = $Points['DefsCount'];
  $TDefsPoints    = $Points['DefsPoint'];
  $TFleetCount    = $Points['FleetCount'];
  $TFleetPoints   = $Points['FleetPoint'];
  $GCount         = $Points['TotalCount'];
  $GPoints        = $Points['TotalPoint'];

  $QryInsertStats  = "INSERT INTO {{table}} SET ";
  $QryInsertStats .= "`id_owner` = '". $CurAlly['id'] ."', ";
  $QryInsertStats .= "`id_ally` = '0', ";
  $QryInsertStats .= "`stat_type` = '2', "; // 1 pour joueur , 2 pour alliance
  $QryInsertStats .= "`stat_code` = '1', "; // de 1 a 5 mis a jour de maniere automatique
  $QryInsertStats .= "`tech_points` = '". $TTechPoints ."', ";
  $QryInsertStats .= "`tech_count` = '". $TTechCount ."', ";
  $QryInsertStats .= "`tech_old_rank` = '". $OldTechRank ."', ";
  $QryInsertStats .= "`build_points` = '". $TBuildPoints ."', ";
  $QryInsertStats .= "`build_count` = '". $TBuildCount ."', ";
  $QryInsertStats .= "`build_old_rank` = '". $OldBuildRank ."', ";
  $QryInsertStats .= "`defs_points` = '". $TDefsPoints ."', ";
  $QryInsertStats .= "`defs_count` = '". $TDefsCount ."', ";
  $QryInsertStats .= "`defs_old_rank` = '". $OldDefsRank ."', ";
  $QryInsertStats .= "`fleet_points` = '". $TFleetPoints ."', ";
  $QryInsertStats .= "`fleet_count` = '". $TFleetCount ."', ";
  $QryInsertStats .= "`fleet_old_rank` = '". $OldFleetRank ."', ";
  $QryInsertStats .= "`total_points` = '". $GPoints ."', ";
  $QryInsertStats .= "`total_count` = '". $GCount ."', ";
  $QryInsertStats .= "`total_old_rank` = '". $OldTotalRank ."', ";
  $QryInsertStats .= "`stat_date` = '". $StatDate ."';";
  doquery ( $QryInsertStats , 'statpoints');
}

/*
$Dele_Teme = $StatDate-604800;
$Del_Timeas = $StatDate;
$Spr_Activate = doquery("SELECT * FROM {{table}} WHERE `time_aktyw`<'{$Del_Timeas}' AND `time_aktyw`>'0'","users");

while ($Activater = mysql_fetch_assoc($Spr_Activate)){
  doquery("UPDATE {{table}} SET
    `db_deaktjava` = '1',
    `deleteme` = '{$Dele_Teme}'
    WHERE `id` = '{$Activater['id']}'","users");
}

$Del_TimeS = $StatDate+86400;
$Time_Online = $StatDate-60*60*24*21;
$Spr_Online = doquery("SELECT * FROM {{table}} WHERE `onlinetime`<'{$Time_Online}' AND `onlinetime`>'0' AND `urlaubs_modus`='0' AND `bana`='0'","users");

while ($OnlineS = mysql_fetch_assoc($Spr_Online)){
  doquery("UPDATE {{table}} SET
    `db_deaktjava` = '1',
    `deltime` = '{$Del_TimeS}'
    WHERE `id` = '{$OnlineS['id']}'","users");
}

$Del_Time = $StatDate;
$Spr_Del = doquery("SELECT * FROM {{table}} WHERE `deltime`<'{$Del_Time}' AND `deltime`>'0'","users");
$User_Spra = mysql_num_rows($Spr_Del);
$Useru_Poza = $game_config['users_amount']-$User_Spra;

while ($Del = mysql_fetch_assoc($Spr_Del)){
  $UserID = $Del['id'];

  $TheUser = doquery ( "SELECT * FROM {{table}} WHERE `id` = '" . $UserID . "';", 'users', true );
  if ( $TheUser['ally_id'] != 0 ) {
    $TheAlly = doquery ( "SELECT * FROM {{table}} WHERE `id` = '" . $TheUser['ally_id'] . "';", 'alliance', true );
    $TheAlly['ally_members'] -= 1;
    if ( $TheAlly['ally_members'] > 0 ) {
      doquery ( "UPDATE {{table}} SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';", 'alliance' );
    } else {
      doquery ( "DELETE FROM {{table}} WHERE `id` = '" . $TheAlly['id'] . "';", 'alliance' );
      doquery ( "DELETE FROM {{table}} WHERE `stat_type` = '2' AND `id_owner` = '" . $TheAlly['id'] . "';", 'statpoints' );
    }
  }
  doquery ( "DELETE FROM {{table}} WHERE `stat_type` = '1' AND `id_owner` = '" . $UserID . "';", 'statpoints' );

  $ThePlanets = doquery ( "SELECT * FROM {{table}} WHERE `id_owner` = '" . $UserID . "';", 'planets' );
  while ( $OnePlanet = mysql_fetch_assoc ( $ThePlanets ) ) {
    if ( $OnePlanet['planet_type'] == 1 ) {
      doquery ( "DELETE FROM {{table}} WHERE `galaxy` = '" . $OnePlanet['galaxy'] . "' AND `system` = '" . $OnePlanet['system'] . "' AND `planet` = '" . $OnePlanet['planet'] . "';", 'galaxy' );
    } elseif ( $OnePlanet['planet_type'] == 3 ) {
      doquery ( "DELETE FROM {{table}} WHERE `galaxy` = '" . $OnePlanet['galaxy'] . "' AND `system` = '" . $OnePlanet['system'] . "' AND `lunapos` = '" . $OnePlanet['planet'] . "';", 'lunas' );
    }
    doquery ( "DELETE FROM {{table}} WHERE `id` = '" . $ThePlanets['id'] . "';", 'planets' );
  }
  doquery ( "DELETE FROM {{table}} WHERE `message_sender` = '" . $UserID . "';", 'messages' );
  doquery ( "DELETE FROM {{table}} WHERE `message_owner` = '" . $UserID . "';", 'messages' );
  doquery ( "DELETE FROM {{table}} WHERE `owner` = '" . $UserID . "';", 'notes' );
  doquery ( "DELETE FROM {{table}} WHERE `fleet_owner` = '" . $UserID . "';", 'fleets' );
  doquery ( "DELETE FROM {{table}} WHERE `id_owner1` = '" . $UserID . "';", 'rw' );
  doquery ( "DELETE FROM {{table}} WHERE `id_owner2` = '" . $UserID . "';", 'rw' );
  doquery ( "DELETE FROM {{table}} WHERE `sender` = '" . $UserID . "';", 'buddy' );
  doquery ( "DELETE FROM {{table}} WHERE `owner` = '" . $UserID . "';", 'buddy' );
  doquery ( "DELETE FROM {{table}} WHERE `user` = '" . $UserID . "';", 'annonce' );
  doquery ( "DELETE FROM {{table}} WHERE `id` = '" . $UserID . "';", 'users' );
  // doquery( "UPDATE {{table}} SET `config_value`='". $Useru_Poza ."' WHERE `config_name` = 'users_amount';", 'config' );
  $userCount = doquery ( "SELECT COUNT(*) FROM {{table}}", 'users', true);
  doquery( "UPDATE {{table}} SET `config_value`='". $userCount[0] ."' WHERE `config_name` = 'users_amount';", 'config' );
}
*/

AdminMessage ( $lang['adm_done'], $lang['adm_stat_title'] );
?>
