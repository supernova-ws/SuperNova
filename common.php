<?php
/*
 * common.php
 *
 * Common init file
 *
 * @version 1.0st Tested s-version
 * @version 1.0s Security checks by Gorlum for http://supernova.ws
 */
include_once('includes/init.inc');

if (INSTALL != true) {
  if ($InLogin != true) {
    $Result        = CheckTheUser ( $IsUserChecked );
    $IsUserChecked = $Result['state'];
    $user          = $Result['record'];
  } elseif ($InLogin == false) {
    // Jeux en mode 'clos' ???
    if( $game_config['game_disable']) {
      if ($user['authlevel'] < 1) {
        message ( stripslashes ( $game_config['close_reason'] ), $game_config['game_name'] );
      }
    }
  }

if ( isset ($user) ) {
  $_lastupdate = doquery("SELECT `lastupdate` FROM {{table}} LIMIT 1;", 'update');
  $row = mysql_fetch_row($_lastupdate);

  if(($time_now-$row[0]>8)&&(!$doNotUpdateFleet)){
//    doquery("LOCK TABLE {{table}} WRITE", 'update');
    doquery("UPDATE {{table}} SET `lastupdate` = ".$time_now."", 'update');
//    doquery("UNLOCK TABLES", '');
    $_lastupdate = $time_now;

    $_fleets = doquery("SELECT DISTINCT fleet_start_galaxy, fleet_start_system, fleet_start_planet, fleet_start_type FROM {{table}} WHERE `fleet_start_time` <= '{$time_now}' ORDER BY fleet_start_time;", 'fleets');
    while ($row = mysql_fetch_array($_fleets)) {
      $array = array();
      $array['galaxy'] = $row['fleet_start_galaxy'];
      $array['system'] = $row['fleet_start_system'];
      $array['planet'] = $row['fleet_start_planet'];
      $array['planet_type'] = $row['fleet_start_type'];

      $temp = FlyingFleetHandler ($array);
    }

    $_fleets = doquery("SELECT DISTINCT fleet_end_galaxy, fleet_end_system, fleet_end_planet, fleet_end_type FROM {{table}} WHERE `fleet_end_time` <= '".$time_now."' ORDER BY fleet_end_time;", 'fleets');
    while ($row = mysql_fetch_array($_fleets)) {
      $array = array();
      $array['galaxy'] = $row['fleet_end_galaxy'];
      $array['system'] = $row['fleet_end_system'];
      $array['planet'] = $row['fleet_end_planet'];
      $array['planet_type'] = $row['fleet_end_type'];

      $temp = FlyingFleetHandler ($array);
    }

    unset($_fleets);

    $aks = doquery("SELECT id FROM {{table}};", 'aks');
    while ($aks_row = mysql_fetch_array($aks)) {
      $aks_fleet = doquery("SELECT DISTINCT fleet_group FROM {{table}} WHERE fleet_group = {$aks_row['id']};", 'fleets', true);
      if (!$aks_fleet){
        doquery("DELETE FROM {{table}} WHERE id = {$aks_row['id']};", 'aks');
      }
    };

    COE_missileCalculate();
  };

  if ( defined('IN_ADMIN') ) {
    $UserSkin  = $user['dpath'];
    $local     = stristr ( $UserSkin, "http:");
    if ($local === false) {
      if (!$user['dpath']) {
        $dpath     = "../". DEFAULT_SKINPATH  ;
      } else {
        $dpath     = "../". $user["dpath"];
      }
    } else {
      $dpath     = $UserSkin;
    }
  } else {
    $dpath     = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];
  }

  SetSelectedPlanet ( $user );

  $planetrow = doquery("SELECT * FROM {{table}} WHERE `id` = '".$user['current_planet']."';", 'planets', true);

  CheckPlanetUsedFields($planetrow);
  } else {
    // Bah si déja y a quelqu'un qui passe par là et qu'a rien a faire de pressé ...
    // On se sert de lui pour mettre a jour tout les retardataires !!
  }
} else {
  $dpath     = "../" . DEFAULT_SKINPATH;
}
?>
