<?php

/*
  fleet.php
  Fleet manager

  V3.1 copyright (c) 2009 by Gorlum for http://supernova.ws
    [*] separate independent chunks int INC-files
  V3.0st copyright (c) 2010 by Gorlum for http://supernova.ws
    [*] Security checked & tested
  V3.0 Updated by Gorlum Sep 2009
    [*] extracting templates from code
    [*] some redundant code cleaning
  V2.0 Updated by Chlorel. 16 Jan 2008 (String extraction, bug corrections, code uniformisation
  V1.0 Created by Perberos. All rights reversed (C) 2006
*/

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}
includeLang('fleet');

$fleet_page = intval($_GET['fleet_page']);

$MaxFleets = GetMaxFleets(&$user);
$FlyingFleets = doquery("SELECT COUNT(fleet_id) as Number FROM {{table}} WHERE `fleet_owner`='{$user['id']}'", 'fleets', true);
$FlyingFleets = $FlyingFleets["Number"];
if ($MaxFleets <= $FlyingFleets AND $fleet_page) {
  message($lang['fl_noslotfree'], $lang['fl_error'], "fleet." . $phpEx, 5);
}

if($user[$resource[124]]>0){
  $FlyingExpeditions  = doquery("SELECT COUNT(fleet_owner) AS `expedi` FROM {{table}} WHERE `fleet_owner` = {$user['id']} AND `fleet_mission` = '15';", 'fleets', true);
  $FlyingExpeditions  = $FlyingExpeditions['expedi'];
  $MaxExpeditions = GetMaxExpeditions($user);
}else{
  $MaxExpeditions = 0;
  $FlyingExpeditions = 0;
};

check_urlaubmodus ($user);

if(IsVacationMode($user)){
  $parse['vacation'] = '<table width="525" height="25" cellpadding="0" cellspacing="0" border="2" bordercolor="#FF0000"><tr><td><center><font size=2 color=red><b><blink>  '.$lang['VacationMode'].'  </blink></b></font></center></td></tr></table>';
  message($lang['Vacation_mode'], $lang['Error'], "fleet.php", 1);
}

$galaxy = max(intval($_GET['galaxy']), intval($_POST['galaxy']));
if (!$galaxy) {
  $galaxy = $planetrow['galaxy'];
}

$system = max(intval($_GET['system']), intval($_POST['system']));
if (!$system) {
  $system = $planetrow['system'];
}

$planet = max(intval($_GET['planet']), intval($_POST['planet']));
if (!$planet) {
  $planet = $planetrow['planet'];
}

$planet_type = max(intval($_GET['planet_type']), intval($_POST['planet_type']));
if (!$planet_type)
  $planet_type = max(intval($_GET['planettype']), intval($_POST['planettype']));
if (!$planet_type)
  $planet_type = $planetrow['planet_type'];

$target_mission = max(intval($_GET['target_mission']), intval($_POST['target_mission']));
if ($planet > $config->game_maxPlanet)
  $target_mission = MT_EXPLORE;

$fleet_group_mr = intval($_POST['fleet_group']);

if ($galaxy AND $system AND $planet AND $planet_type){
  $TargetPlanet = doquery("SELECT * FROM {{table}} WHERE galaxy = {$galaxy} and system = {$system} AND planet = {$planet} and planet_type = {$planet_type}", "planets", true);

  if ($TargetPlanet['id_owner']){
    $UsedPlanet = true;
    if ($TargetPlanet['id_owner'] == $user['id']) {
      $YourPlanet = true;
    }else{
      $YourPlanet = false;
    };
  }else{
    $YourPlanet = false;
    $UsedPlanet = false;
  };

  if ($planet > $config->game_maxPlanet) {
    $missiontype = array(MT_EXPLORE => $lang['type_mission'][MT_EXPLORE]);
  }else{
    $missiontype = array();

    if (intval($_POST['ship209'])>=1){
      $isRecycler = true;
      if ($planet_type == PT_DEBRIS)
        $missiontype[MT_RECYCLE] = $lang['type_mission'][MT_RECYCLE];
    };

    if (($planet_type == PT_PLANET) || ($planet_type == PT_MOON)) {
      if ((intval($_POST['ship210'])>=1)){
        if(!$YourPlanet && $UsedPlanet){
          $missiontype[MT_SPY] = $lang['type_mission'][MT_SPY];
        }elseif ($YourPlanet){
          $missiontype[MT_RELOCATE] = $lang['type_mission'][MT_RELOCATE];
        }
      };

      if (intval($_POST['ship208'])>=1){
        $isColonizer = true;
        if (!$UsedPlanet)
          $missiontype[MT_COLONIZE] = $lang['type_mission'][MT_COLONIZE];
      };

      if ((intval($_POST['ship202']) >= 1) || (intval($_POST['ship203'])>=1) || $isColonizer || $isRecycler){
        $isTransport = true;
      };

      if ($isTransport ||
        $_POST['ship204'] >= 1 ||
        $_POST['ship205'] >= 1 ||
        $_POST['ship206'] >= 1 ||
        $_POST['ship207'] >= 1 ||
        $_POST['ship211'] >= 1 ||
        $_POST['ship213'] >= 1 ||
        $_POST['ship214'] >= 1 ||
        $_POST['ship215'] >= 1 ||
        $_POST['ship216'] >= 1)
      {
        if ($UsedPlanet)
          $missiontype[MT_TRANSPORT] = $lang['type_mission'][MT_TRANSPORT];

        if ($YourPlanet) {
          $missiontype[MT_RELOCATE] = $lang['type_mission'][MT_RELOCATE];
        }else{
          if ($UsedPlanet) {
            $missiontype[MT_ATTACK] = $lang['type_mission'][MT_ATTACK];
            $missiontype[MT_HOLD] = $lang['type_mission'][MT_HOLD];

            if (($planet_type == PT_MOON) AND (intval($_POST['ship214']) >= 1))
              $missiontype[MT_DESTROY] = $lang['type_mission'][MT_DESTROY];

            if ($fleet_group_mr > 0)
              $missiontype[MT_AKS] = $lang['type_mission'][MT_AKS];
          };
        };
      };
    };
  };

  if(!$TargetPlanet AND ($planet <= $config->game_maxPlanet) AND (!isColonizer)){
    message ("<font color=\"red\"><b>". $lang['fl_no_planettype'] ."</b></font>", $lang['fl_error']);
  }

  if (!$target_mission)
    $target_mission = MT_ATTACK;

  if (!$UsedPlanet and $isColonizer)
    $target_mission = MT_COLONIZE;

  ksort($missiontype);
};

$SpeedFactor = GetGameSpeedFactor ();

$parse = $lang;

$parse['galaxy'] = $galaxy;
$parse['system'] = $system;
$parse['planet'] = $planet;
$parse['planet_type'] = $planet_type;

$parse['thisgalaxy'] = $planetrow['galaxy'];
$parse['thissystem'] = $planetrow['system'];
$parse['thisplanet'] = $planetrow['planet'];
$parse['thisresource1'] = floor($planetrow['metal']);
$parse['thisresource2'] = floor($planetrow['crystal']);
$parse['thisresource3'] = floor($planetrow['deuterium']);
$parse['speedfactor'] = GetGameSpeedFactor();

$time_now = time();

if (!$fleet_page) {
  include('includes/includes/flt_page0.inc');
}elseif ($fleet_page==1){
  include('includes/includes/flt_page1.inc');
}elseif ($fleet_page==2){
  include('includes/includes/flt_page2.inc');
}elseif ($fleet_page==3){
  include('includes/includes/flt_page3.inc');
}elseif ($fleet_page==4){
  include('includes/includes/flt_page4.inc');
}else{
  message($lang['sys_hackattempt'], $lang['sys_error'], "fleet." . $phpEx, 5);
};
?>