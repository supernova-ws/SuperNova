<?php

/**
 * phalanx.php
 *
 * 1.2 - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1
 * @original made by ????
 * @copyright 2008 by Pada for XNova.project.es
 */

/**
 * BuildFleetEventTable.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

/*

Attaque Groupée
24/01 8:22:15   Une de tes flottes venant de la planète Guernica [9:486:5] atteint la planète RIQUET [9:480:4]. Elle avait pour mission: Attaquer
Une de tes flottes venant de la planète Guernica [9:486:5] atteint la planète RIQUET [9:480:4]. Elle avait pour mission: Attaque groupée

24/01 9:43:19   Une de tes flottes rentre de la planète RIQUET [9:480:4] à la planète Guernica [9:486:5]. Elle avait pour mission: Attaque groupée
24/01 17:54:29  Une de tes flottes rentre de la planète RIQUET [9:480:4] à la planète Guernica [9:486:5]. Elle avait pour mission: Attaquer

retour de l'attaque (degroupé)
22:53:56  Une de tes flottes rentre de la planète RIQUET [9:480:4] à la planète Guernica [9:486:5]. Elle avait pour mission: Attaque groupée
22:55:27  Une de tes flottes rentre de la planète RIQUET [9:480:4] à la planète Guernica [9:486:5]. Elle avait pour mission: Attaquer

// Missiles
aucun popup
uniquement le decompteur de temps de vol
0:01:01
2:37:27   Attaque de missiles (10) de Guernica [9:486:5] à Prison Break [9:487:4] cible primaire Lanceur de plasma.

*/

function BuildFleetEventTable ( $FleetRow, $Status, $Owner, $Label, $Record )
{
  global $lang;

  $FleetStyle  = array (
     1 => 'attack',
     2 => 'federation',
     3 => 'transport',
     4 => 'deploy',
     5 => 'hold',
     6 => 'espionage',
     7 => 'colony',
     8 => 'harvest',
     9 => 'destroy',
    10 => 'missile',
    15 => 'transport',
  );
  $FleetStatus = array ( 0 => 'flight', 1 => 'holding', 2 => 'return' );
  if ( $Owner == true ) {
    $FleetPrefix = 'own';
  } else {
    $FleetPrefix = '';
  }

  $RowsTPL        = gettemplate ('overview_fleet_event');
  $MissionType    = $FleetRow['fleet_mission'];
  $FleetContent   = CreateFleetPopupedFleetLink ( $FleetRow, $lang['ov_fleet'], $FleetPrefix . $FleetStyle[ $MissionType ], $Owner );
  $FleetCapacity  = CreateFleetPopupedMissionLink ( $FleetRow, $lang['type_mission'][ $MissionType ], $FleetPrefix . $FleetStyle[ $MissionType ] );

  $StartPlanet    = doquery("SELECT `name` FROM `{{table}}` WHERE `galaxy` = '".$FleetRow['fleet_start_galaxy']."' AND `system` = '".$FleetRow['fleet_start_system']."' AND `planet` = '".$FleetRow['fleet_start_planet']."' AND `planet_type` = '".$FleetRow['fleet_start_type']."';", 'planets', true);
  $StartType      = $FleetRow['fleet_start_type'];
  $TargetPlanet   = doquery("SELECT `name` FROM `{{table}}` WHERE `galaxy` = '".$FleetRow['fleet_end_galaxy']."' AND `system` = '".$FleetRow['fleet_end_system']."' AND `planet` = '".$FleetRow['fleet_end_planet']."' AND `planet_type` = '".$FleetRow['fleet_end_type']."';", 'planets', true);
  $TargetType     = $FleetRow['fleet_end_type'];

  if       ($Status != 2) {
    if       ($StartType == 1) {
      $StartID  = $lang['ov_planet_to'];
    } elseif ($StartType == 3) {
      $StartID  = $lang['ov_moon_to'];
    }
    $StartID .= $StartPlanet['name'] ." ";
    $StartID .= GetStartAdressLink ( $FleetRow, $FleetPrefix . $FleetStyle[ $MissionType ] );

    if ( $MissionType != 15 ) {
      if       ($TargetType == 1) {
        $TargetID  = $lang['ov_planet_to_target'];
      } elseif ($TargetType == 2) {
        $TargetID  = $lang['ov_debris_to_target'];
      } elseif ($TargetType == 3) {
        $TargetID  = $lang['ov_moon_to_target'];
      }
    } else {
      $TargetID  = $lang['ov_explo_to_target'];
    }
    $TargetID .= $TargetPlanet['name'] ." ";
    $TargetID .= GetTargetAdressLink ( $FleetRow, $FleetPrefix . $FleetStyle[ $MissionType ] );
  } else {
    if       ($StartType == 1) {
      $StartID  = $lang['ov_back_planet'];
    } elseif ($StartType == 3) {
      $StartID  = $lang['ov_back_moon'];
    }
    $StartID .= $StartPlanet['name'] ." ";
    $StartID .= GetStartAdressLink ( $FleetRow, $FleetPrefix . $FleetStyle[ $MissionType ] );

    if ( $MissionType != 15 ) {
      if       ($TargetType == 1) {
        $TargetID  = $lang['ov_planet_from'];
      } elseif ($TargetType == 2) {
        $TargetID  = $lang['ov_debris_from'];
      } elseif ($TargetType == 3) {
        $TargetID  = $lang['ov_moon_from'];
      }
    } else {
      $TargetID  = $lang['ov_explo_from'];
    }
    $TargetID .= $TargetPlanet['name'] ." ";
    $TargetID .= GetTargetAdressLink ( $FleetRow, $FleetPrefix . $FleetStyle[ $MissionType ] );
  }

  if ($Owner == true) {
    $EventString  = $lang['ov_une'];     // 'Une de tes '
    $EventString .= $FleetContent;
  } else {
    $EventString  = $lang['ov_une_hostile']; // 'Une '
    $EventString .= $FleetContent;
    $EventString .= $lang['ov_hostile'];  // ' hostile de '
    $EventString .= BuildHostileFleetPlayerLink ( $FleetRow );
  }

  if       ($Status == 0) {
    $Time         = $FleetRow['fleet_start_time'];
    $Rest         = $Time - time();
    $EventString .= $lang['ov_vennant']; // ' venant '
    $EventString .= $StartID;
    $EventString .= $lang['ov_atteint']; // ' atteint '
    $EventString .= $TargetID;
    $EventString .= $lang['ov_mission']; // '. Elle avait pour mission: '
  } elseif ($Status == 1) {
    $Time         = $FleetRow['fleet_end_stay'];
    $Rest         = $Time - time();
    $EventString .= $lang['ov_vennant']; // ' venant '
    $EventString .= $StartID;
    $EventString .= $lang['ov_explo_stay']; // ' explore '
    $EventString .= $TargetID;
    $EventString .= $lang['ov_explo_mission']; // '. Elle a pour mission: '
  } elseif ($Status == 2) {
    $Time         = $FleetRow['fleet_end_time'];
    $Rest         = $Time - time();
    $EventString .= $lang['ov_rentrant'];// ' rentrant '
    $EventString .= $TargetID;
    $EventString .= $StartID;
    $EventString .= $lang['ov_mission']; // '. Elle avait pour mission: '
  }
  $EventString .= $FleetCapacity;

  $bloc['fleet_status'] = $FleetStatus[ $Status ];
  $bloc['fleet_prefix'] = $FleetPrefix;
  $bloc['fleet_style']  = $FleetStyle[ $MissionType ];
  $bloc['fleet_javai']  = InsertJavaScriptChronoApplet ( $Label, $Record, $Rest, true );
  $bloc['fleet_order']  = $Label . $Record;
  $bloc['fleet_time']   = date(FMT_TIME, $Time);
  $bloc['fleet_descr']  = $EventString;
  $bloc['fleet_javas']  = InsertJavaScriptChronoApplet ( $Label, $Record, $Rest, false );

  return parsetemplate($RowsTPL, $bloc);
}

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