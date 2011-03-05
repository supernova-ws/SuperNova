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

include('common.' . substr(strrchr(__FILE__, '.'), 1));

define('SN_IN_FLEET', true);

includeLang('fleet');

$parse = $lang;

$fleet_page = intval($_GET['fleet_page']);

$galaxy = sys_get_param_int('galaxy', $planetrow['galaxy']);
$system = sys_get_param_int('system', $planetrow['system']);
$planet = sys_get_param_int('planet', $planetrow['planet']);

$target_mission = sys_get_param_int('target_mission');
if ($target_mission == MT_COLONIZE || $target_mission == MT_EXPLORE)
{
  $planet_type = PT_PLANET;
}
elseif ($target_mission == MT_RECYCLE)
{
  $planet_type = PT_DEBRIS;
}
elseif ($target_mission == MT_DESTROY)
{
  $planet_type = PT_MOON;
}
else
{
  $planet_type = sys_get_param_int('planet_type');
  if (!$planet_type)
  {
    $planet_type = sys_get_param_int('planettype', $planetrow['planet_type']);
  }
}

$options = array();
$options['fleets_max'] = GetMaxFleets($user);

$MaxFleets = GetMaxFleets($user);
$FlyingFleets = doquery("SELECT COUNT(fleet_id) as Number FROM {{fleets}} WHERE `fleet_owner`='{$user['id']}'", '', true);
$FlyingFleets = $FlyingFleets['Number'];
if ($MaxFleets <= $FlyingFleets && $fleet_page && $fleet_page != 4)
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

switch ($fleet_page)
{
  case 3:

  case 2:
    $fleet_group_mr = intval($_POST['fleet_group']);
    $fleetarray     = unserialize(base64_decode(str_rot13($_POST["usedfleet"])));

    foreach($fleetarray as $ship_id => $ship_amount)
    {
      if(!in_array($ship_id, $sn_data['groups']['fleet']) || intval($ship_amount) != $ship_amount || $ship_amount < 1)
      {
        $debug->warning('Supplying wrong ship in ship list on fleet page', 'Hack attempt', 302, array('base_dump' => true));
        die();
      }
    }

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
          message ("<font color=\"red\"><b>". $lang['fl_no_planet_type'] ."</b></font>", $lang['fl_error']);
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
    require('includes/fleet/flt_page1.inc');
  break;

  case 2:
    require('includes/fleet/flt_page2.inc');
  break;

  case 3:
    require('includes/fleet/flt_page3.inc');
  break;

  case 4:
    require('includes/fleet/flt_page4.inc');
  break;

  case 5:
    require('includes/fleet/flt_page5.inc');
  break;

  default:
    require('includes/fleet/flt_page0.inc');
  break;
}

?>
