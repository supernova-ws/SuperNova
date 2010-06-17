<?php

/**
 * simulator.php
 *
 * 1.5st - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.5 Heavily modified by Gorlum for http://supernova.ws
 *
 *   [*] Added REPLAY ability - link to simulator results
 *   [*] Many optimizations
 *   [*] Added ACS support
 *   [*] Now fully unified with combat engine and removed duplicate code
 *
 *  @version 1.0
 * @copyright 2008 by Anthony for Darkness fo Evolution
 *
 * Script by Anthony
 *
 * Template for Sonyedorlys converter.
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if($_GET['debug'])
  define("BE_DEBUG", true);

function sys_combatDataPack($combat, $strArray){
  global $reslist;

  foreach($combat as $fleetID => $fleetCompress){
    $strPackedEnd = '';
    if($strArray == 'def')
      $strPacked .= "D";
    else
      $strPacked .= "A";

    $strPacked .= '.';

    foreach($fleetCompress['user'] as $key => $techLevel)
      $strPacked .= $key  . ',' . (empty($techLevel) ? 0 : $techLevel) . ';';

    $strPacked .= '.';

    foreach($fleetCompress[$strArray] as $shipID => $shipCount)
      $strPacked .= $shipCount ? ($shipID . ',' . $shipCount . ';') : '';
  }
  $strPacked .= '.';
  foreach($reslist['resources'] as $resource)
     $strPacked .= intval($combat['resources'][$resource]) . ',';
  $strPacked .= '!';

  return $strPacked;
}

function sys_combatDataUnPack($strData){
  global $reslist;

  $unpacked = array (
    'detail' => array(),
    'def' => array()
  );

  $fleetList = explode('!', $strData);

  foreach($fleetList as $fleet){
    $t = explode('.', $fleet);

    if(!$t[0]) continue;

    if($t[0] == 'A' ){
      $strArray = 'detail';
    }else{
      $strArray = 'def';
    };

    $combat = array();

    $t[1] = explode(';', $t[1]);
    foreach($t[1] as $techInfo)
      if($techInfo){
        $techInfo = explode(',', $techInfo);
        $combat['user'][$techInfo[0]] = $techInfo[1];
      }

    $t[2] = explode(';', $t[2]);
    foreach($t[2] as $shipInfo)
      if($shipInfo){
        $shipInfo = explode(',', $shipInfo);
        $combat[$strArray][$shipInfo[0]] = $shipInfo[1];
      }

    $t[3] = explode(',', $t[2]);
    foreach($t[3] as $resourceID => $resource)
      if($resourceID)
        $combat['resources'][$reslist['resources'][$resourceID]] = $resource;

    $unpacked[$strArray][] = $combat;
  }

  return $unpacked;
}

function coe_simulatorHTMLMake($resToLook){
  global $lang, $resource, $user;

  foreach($resToLook as $unitID){
    if($unitID<200 || $unitID>600 ){
      $parse['fieldNameAtt'] = 'user';
      $parse['fieldNameDef'] = 'user';
      $parse['fieldValue']   = $user[$resource[$unitID]];
    }else{
      $parse['fieldNameAtt'] = 'detail';
      $parse['fieldNameDef'] = 'def';
      $parse['fieldValue']   = 0;
    }
    $parse['unitID'] = $unitID;
    $parse['unitName'] = $lang["tech"][$unitID];
    $parse['hideAttacker'] = $unitID < 400 ? '' : 'class="hide"';

    $tmp = parsetemplate(gettemplate('simulator_row'), $parse);
    $input[floor($unitID/100) * 100] .= $tmp;
  }
  return $input;
}

if(isset($_GET['replay'])) {
  $replay       = $_GET['replay'];
  $unpacked     = sys_combatDataUnPack($replay);

  $_POST['attacker'] = $unpacked['detail'];
  $_POST['defender'] = $unpacked['def'];
  $_POST['submit']   = true;
}

if($_POST['submit']){
  $replay = sys_combatDataPack($_POST['attacker'], 'detail');
  $replay .= sys_combatDataPack($_POST['defender'], 'def');

  foreach(array('attacker', 'defender') as $index)
    foreach($_POST[$index] as &$fleet){
      foreach($fleet['user'] as $key => $value){
        $fleet['user'][$resource[$key]] = $value;
        unset($fleet['user'][$key]);
      }

      if(is_array($fleet['detail']))
        $tmp = 'detail';
      else
        $tmp = 'def';

      foreach($fleet[$tmp] as $key => $value)
        if(!$value)
          unset($fleet[$tmp][$key]);
    }

  // Lets calcualte attack...
  $start = microtime(true);
  $result = calculateAttack($_POST['attacker'], $_POST['defender'], true);
  $totaltime = microtime(true) - $start;

  // calculating loot per attacking fleet
  $loot = BE_calculatePostAttacker($_POST['resources'], $_POST['attacker'], $result, true);

  // Calculating Moon Chance
  $MoonChance = BE_calculateMoonChance($result);

  $formatted_cr = formatCR($result, $loot['looted'], $MoonChance, "", $totaltime);

  // Well lets just copy rw.php code. If I am showing a cr why re-inent the wheel???
  $Page  = "<html>";
  $Page .= "<head>";
  $Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$dpath."/formate.css\">";
  $Page .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\" />";
  $Page .= "</head>";
  $Page .= "<body>";
  $Page .= "<center>";

  //OK, one change, we won't be getting cr from datbase, instead we will be generating it directly, lets skip the database stage, this is what get generated and put in the database.
  $report = stripslashes($formatted_cr['html']);
  foreach ($lang['tech_rc'] as $id => $s_name) {
    $str_replace1  = array("[ship[".$id."]]");
    $str_replace2  = array($s_name);
    $report = str_replace($str_replace1, $str_replace2, $report);
  }
  $no_fleet = "<table border=1 align=\"center\"><tr><th>Тип</th></tr><tr><th>Всего</th></tr><tr><th>Оружие</th></tr><tr><th>Щиты</th></tr><tr><th>Броня</th></tr></table>";
  $destroyed = "<table border=1 align=\"center\"><tr><th><font color=\"red\"><strong>Уничтожены!</strong></font></th></tr></table>";
  $str_replace1  = array($no_fleet);
  $str_replace2  = array($destroyed);
  $report = str_replace($str_replace1, $str_replace2, $report);
  $Page .= $report;

  $Page .= "<br /><br />";

  $Page .= '<a href=simulator.php?replay=' . $replay .'><font color=red>';
  $Page .= "Sorry, this report CAN be shared!";
  $Page .= "</font></a>";

  $Page .= "<br /><br />";
  $Page .= "</center>";
  $Page .= "</body>";
  $Page .= "</html>";

  echo $Page;
}else{
  $parse = $lang;

  $tmp = array_merge($reslist['combat'], array(109, 110, 111));
  $tmp = coe_simulatorHTMLMake($tmp);

  $parse['inputTech'] = $tmp['100'];
  $parse['inputFleet'] = $tmp['200'];
  $parse['inputDefense'] = $tmp['400'];
  $page = parsetemplate(gettemplate('simulator'), $parse);
  display($page, $lang['coe_combatSimulator'], false);
}

function rp($input) {
  return str_replace(".", "", $input);
}
?>