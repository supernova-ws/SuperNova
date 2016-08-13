<?php

/**
 * phalanx.php
 *
 * 2.0 copyright (c) 2009-2011 by Gorlum for http://supernova.ws
     [!] Full rewrote using SN functions
 * 1.2 - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1
 * @original made by ????
 * @copyright 2008 by Pada for XNova.project.es
 */

use DBStatic\DBStaticPlanet;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('overview');
lng_include('universe');

$sensorLevel = mrc_get_level($user, $planetrow, STRUC_MOON_PHALANX);
if (!intval($sensorLevel)) {
  message (classLocale::$lang['phalanx_nosensoravailable'], classLocale::$lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

if ($planetrow['planet_type'] != PT_MOON) {
  message (classLocale::$lang['phalanx_onlyformoons'], classLocale::$lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

$scan_galaxy  = sys_get_param_int('galaxy');
$scan_system  = sys_get_param_int('system');
$scan_planet  = sys_get_param_int('planet');
$scan_planet_type  = 1; // sys_get_param_int('planettype');
$id = sys_get_param_id('id');

$source_galaxy = $planetrow['galaxy'];
$source_system = $planetrow['system'];
$source_planet = $planetrow['planet'];

$sensorRange = GetPhalanxRange($sensorLevel);

$system_distance = abs($source_system - $scan_system);
if($system_distance > $sensorRange || $scan_galaxy != $source_galaxy)
{
  message (classLocale::$lang['phalanx_rangeerror'], classLocale::$lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

$cost = $sensorLevel * 1000;

if ($planetrow['deuterium'] < $cost)
{
  message(classLocale::$lang['phalanx_nodeuterium'], "phalanx", '', 3);
}

$planet_scanned = DBStaticPlanet::db_planet_by_gspt($scan_galaxy, $scan_system, $scan_planet, $scan_planet_type);
if(!$planet_scanned['id'])
{
  message(classLocale::$lang['phalanx_planet_not_exists'], classLocale::$lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

if($planet_scanned['destruyed'])
{
  message (classLocale::$lang['phalanx_planet_destroyed'], classLocale::$lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

DBStaticPlanet::db_planet_update_adjust_by_id(
  $user['current_planet'],
  array(
    'deuterium' => - $cost,
  )
);

$template = gettemplate('planet_fleet_list', true);

$objFleetList = FleetList::dbGetFleetListAndMissileByCoordinates($planet_scanned, true);
$fleet_events = flt_parse_objFleetList_to_events($objFleetList, $planet_scanned);
tpl_assign_fleet($template, $fleet_events);

display($template, classLocale::$lang['tech'][STRUC_MOON_PHALANX], false, '', false, false);
