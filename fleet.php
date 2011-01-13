<?php

/*
  fleet.php
  Fleet manager

  V3.3 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
    [~] Imploded fleet_back.php code

  V3.2 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
    [~] separate independent chunks in INC-files

  V3.1 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
    [~] Security checked & tested

  V3.0 Updated by Gorlum Sep 2009
    [!] extracting templates from code
    [~] some redundant code cleaning

  V2.0 Updated by Chlorel. 16 Jan 2008 (String extraction, bug corrections, code uniformisation

  V1.0 Created by Perberos. All rights reversed (C) 2006
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
check_urlaubmodus ($user);

includeLang('fleet');

$parse = $lang;

$fleet_page = intval($_GET['fleet_page']);

$galaxy = $_GET['galaxy'] ? intval($_GET['galaxy']) : ($_POST['galaxy'] ? intval($_POST['galaxy']) : $planetrow['galaxy']);
$system = $_GET['system'] ? intval($_GET['system']) : ($_POST['system'] ? intval($_POST['system']) : $planetrow['system']);
$planet = $_GET['planet'] ? intval($_GET['planet']) : ($_POST['planet'] ? intval($_POST['planet']) : $planetrow['planet']);

$target_mission = $_GET['target_mission'] ? intval($_GET['target_mission']) : intval($_POST['target_mission']);
if($target_mission == MT_COLONIZE)
{
  $planet_type = PT_PLANET;
}
else
{
  $planet_type = $_GET['planet_type'] ? intval($_GET['planet_type']) : intval($_POST['planet_type']);
  if (!$planet_type)
  {
    $planet_type = $_GET['planettype'] ? intval($_GET['planettype']) : ($_POST['planettype'] ? intval($_POST['planettype']) : $planetrow['planet_type']);
  }
}

$MaxFleets = GetMaxFleets($user);
$FlyingFleets = doquery("SELECT COUNT(fleet_id) as Number FROM {{fleets}} WHERE `fleet_owner`='{$user['id']}'", '', true);
$FlyingFleets = $FlyingFleets['Number'];
if ($MaxFleets <= $FlyingFleets AND $fleet_page)
{
  message($lang['fl_noslotfree'], $lang['fl_error'], "fleet.{$phpEx}", 5);
}

$MaxExpeditions = GetMaxExpeditions($user);
if($MaxExpeditions){
  $FlyingExpeditions  = doquery("SELECT COUNT(fleet_owner) AS `expedi` FROM {{fleets}} WHERE `fleet_owner` = {$user['id']} AND `fleet_mission` = '15';", '', true);
  $FlyingExpeditions  = $FlyingExpeditions['expedi'];
}else{
  $FlyingExpeditions = 0;
};

$SpeedFactor = get_fleet_speed();

if($_POST['return_fleet'])
{
  $fleet_page = 0;

  $fleetid  = intval($_POST['fleetid']);
  if($fleetid)
  {
    $FleetRow = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id` = '{$fleetid}';", '', true);

    if ($FleetRow['fleet_owner'] == $user['id'] && $FleetRow['fleet_mess'] == 0)
    {
      if ($FleetRow['fleet_end_stay'] != 0)
      {
        if ($FleetRow['fleet_start_time'] > $time_now)
        {
          $CurrentFlyingTime = $time_now - $FleetRow['start_time'];
        }
        else
        {
          $CurrentFlyingTime = $FleetRow['fleet_start_time'] - $FleetRow['start_time'];
        }
      }
      else
      {
        $CurrentFlyingTime = $time_now - $FleetRow['start_time'];
      }
      $ReturnFlyingTime  = $CurrentFlyingTime + $time_now + 1;

      $QryUpdateFleet  = "UPDATE {{fleets}} SET `fleet_start_time` = '{$time_now}', `fleet_end_stay` = '0', ";
      $QryUpdateFleet .= "`fleet_end_time` = '{$ReturnFlyingTime}', `fleet_target_owner` = '{$user['id']}', `fleet_mess` = '1' ";
      $QryUpdateFleet .= "WHERE `fleet_id` = '{$fleetid}';";
      doquery($QryUpdateFleet);
    }
  }
}

switch ($fleet_page)
{
  case 3:


  case 2:
    $fleet_group_mr = intval($_POST['fleet_group']);
    $fleetarray     = unserialize(base64_decode(str_rot13($_POST["usedfleet"])));

    $UsedPlanet = false;
    $YourPlanet = false;

    $missiontype = array();
    if ($planet > $config->game_maxPlanet)
    {
      if(!$fleetarray[210])
      {
        $target_mission = MT_EXPLORE;
        $missiontype[MT_EXPLORE] = $lang['type_mission'][MT_EXPLORE];
      }
    }
    elseif ($galaxy && $system && $planet)
    {
      $check_type = $planet_type == PT_MOON ? PT_MOON : PT_PLANET;

      $TargetPlanet = doquery("SELECT * FROM {{planets}} WHERE galaxy = {$galaxy} AND system = {$system} AND planet = {$planet} AND planet_type = {$check_type};", '', true);

      if ($TargetPlanet['id_owner'])
      {
        $UsedPlanet = true;
        if ($TargetPlanet['id_owner'] == $user['id'])
        {
          $YourPlanet = true;
        }
      }

      if (!$UsedPlanet)
      {
        if ($fleetarray[208])
        {
          $missiontype[MT_COLONIZE] = $lang['type_mission'][MT_COLONIZE];
          $target_mission = MT_COLONIZE;
          $planet_type = PT_PLANET;
        }
        else
        {
          message ("<font color=\"red\"><b>". $lang['fl_no_planettype'] ."</b></font>", $lang['fl_error']);
        }
      }
      else
      {
        if ($fleetarray[209] && $planet_type == PT_DEBRIS)
        {
          $target_mission = MT_RECYCLE;
          $missiontype[MT_RECYCLE] = $lang['type_mission'][MT_RECYCLE];
        }
        elseif ($planet_type == PT_PLANET || $planet_type == PT_MOON)
        {
          if ($YourPlanet)
          {
            $missiontype[MT_RELOCATE] = $lang['type_mission'][MT_RELOCATE];
            $missiontype[MT_TRANSPORT] = $lang['type_mission'][MT_TRANSPORT];
          }
          else // Not Your Planet
          {
            if ($fleetarray[210]) // Only spy missions if any spy
            {
              $missiontype[MT_SPY] = $lang['type_mission'][MT_SPY];
            }
            else // If no spies...
            {
              if ($fleet_group_mr)
              {
                $missiontype[MT_AKS] = $lang['type_mission'][MT_AKS];
              }
              else
              {
                $missiontype[MT_ATTACK] = $lang['type_mission'][MT_ATTACK];
                $missiontype[MT_TRANSPORT] = $lang['type_mission'][MT_TRANSPORT];

                $missiontype[MT_HOLD] = $lang['type_mission'][MT_HOLD];

                if ($planet_type == PT_MOON && $fleetarray[214])
                {
                  $missiontype[MT_DESTROY] = $lang['type_mission'][MT_DESTROY];
                }
              }
            }
          }
        }
      }
    }

    if (!$target_mission && is_array($missiontype))
    {
      $target_mission = MT_ATTACK;
    }

    ksort($missiontype);

    $speed_percent = intval($_POST['speed']);
    $fleet_speed   = min(GetFleetMaxSpeed ($fleetarray, 0, $user));
    $distance      = GetTargetDistance ( $planetrow['galaxy'], $galaxy, $planetrow['system'], $system, $planetrow['planet'], $planet );
    $duration      = GetMissionDuration ( $speed_percent, $fleet_speed, $distance, $SpeedFactor );
    $consumption   = GetFleetConsumption ( $fleetarray, $SpeedFactor, $duration, $distance, $fleet_speed, $user, $speed_percent );
  // No Break

  case 1:
  case 0:
    $parse['thisgalaxy']      = $planetrow['galaxy'];
    $parse['thissystem']      = $planetrow['system'];
    $parse['thisplanet']      = $planetrow['planet'];
    $parse['thisplanet_type'] = $planetrow['planet_type'];
  // no break

}

$parse['galaxy'] = $galaxy;
$parse['system'] = $system;
$parse['planet'] = $planet;
$parse['planet_type'] = $planet_type;

switch($fleet_page)
{
  case 1:
    include('includes/fleet/flt_page1.inc');
  break;

  case 2:
    include('includes/fleet/flt_page2.inc');
  break;

  case 3:
    include('includes/fleet/flt_page3.inc');
  break;

  case 4:
    include('includes/fleet/flt_page4.inc');
  break;

  default:
    include('includes/fleet/flt_page0.inc');
  break;
}

?>
