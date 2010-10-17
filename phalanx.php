<?php

/**
 * phalanx.php
 *
 * 1.2 - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1
 * @original made by ????
 * @copyright 2008 by Pada for XNova.project.es
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

includeLang('overview');
includeLang('universe');

function secureNumericGet(){
  if(!$_GET) return false;

  foreach($_GET as $name => $value){
    if(secureNumeric($value) == false){
      unset($_GET[$name]);
    }
  }
  return;
}

function secureNumeric($value){
  if(!$value) return false;
/*
  if(ereg("[0-9]", $value) === false){
    return false;
  }
  return true;
*/
  return is_numeric($value);
}

  secureNumericGet();

  $g  = intval($_GET['galaxy']);
  $s  = intval($_GET['system']);
  $i  = intval($_GET['planet']);
  $id = intval($_GET['id']);

  $galaxy = $planetrow['galaxy'];
  $system = $planetrow['system'];
  $planeta = $planetrow['planet'];
  $sensorLevel = $planetrow['phalanx'];
  $sensorRange = GetPhalanxRange($sensorLevel);

  $systemBack = intval($system + $sensorRange);
  $systemForward = intval($system - $sensorRange);

  if($s > $systemBack){
    message ($lang[phalanx_rangeerror], "phalanx", "", 3);
  }

  if($s < $systemForward){
    message ($lang[phalanx_rangeerror], "phalanx", "", 3);
  }

  if($g != $galaxy){
    message ($lang[phalanx_rangeerror], "phalanx", "", 3);
  }

  if ($planetrow['planet_type'] != '3') {
    message ($lang[phalanx_onlyformoons], "phalanx", "", 3);
  }

  if ($planetrow['sensor_phalax'] == '0') {
    message ($lang[phalanx_nosensoravailable], "phalanx", "", 3);
  }

  $cost = $sensorLevel * 1000;

  if ($planetrow['deuterium'] > $cost){
    doquery("UPDATE {{table}} SET deuterium=deuterium - " . $cost . " WHERE id='" . $user['current_planet'] . "'", 'planets');
  }else{
    message ($lang[phalanx_nodeuterium], "phalanx", "", 3);
  }


$fq = doquery("SELECT * FROM {{table}} WHERE
          ( fleet_start_galaxy='" . $g . "' AND fleet_start_system='" . $s . "' AND fleet_start_planet='" . $i . "' AND fleet_start_type = 1)
          OR
          ( fleet_end_galaxy='" . $g . "' AND fleet_end_system='" . $s . "' AND fleet_end_planet='" . $i . "' AND fleet_start_type = 1)
        ORDER BY `fleet_start_time`", 'fleets');

if (mysql_num_rows($fq) == "0") {
  $page .= "<table width=519>
  <tr>
    <td class=c colspan=7>" . $lang['phalanx_header'] ."</td>
  </tr><th>" . $lang['phalanx_noflotes'] . "</th></table>";
} else {
  $page .= "<center><table>";
  $parse = $lang;

  while ($FleetRow = mysql_fetch_assoc($fq)) {
    $Record++;

    $StartTime   = $FleetRow['fleet_start_time'];
    $StayTime    = $FleetRow['fleet_end_stay'];
    $EndTime     = $FleetRow['fleet_end_time'];

    $Label = "fs";
    if ($StartTime > time()) {
      $fpage[$StartTime] = BuildFleetEventTable ( $FleetRow, 0, false, $Label, $Record );
    }

    if ($FleetRow['fleet_mission'] <> 4) {

      $Label = "ft";
      if ($StayTime > time()) {
        $fpage[$StayTime] = BuildFleetEventTable ( $FleetRow, 1, false, $Label, $Record );
      }

      $Label = "fe";
      if ($EndTime > time()) {
        $fpage[$EndTime]  = BuildFleetEventTable ( $FleetRow, 2, false, $Label, $Record );
      }
    }
  }

  if (count($fpage) > 0) {
    ksort($fpage);
    foreach ($fpage as $time => $content) {
      $fleet .= $content . "\n";
    }
  }

  $parse[fleets] = $fleet;
  $parse[phalanx_header] = $lang[phalanx_header];

  $page = parsetemplate(gettemplate('phalanx_body'), $parse);
}

display($page, "phalanx", false, '');


?>