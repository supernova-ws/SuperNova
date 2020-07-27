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

use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('overview');
lng_include('universe');

$sensorLevel = mrc_get_level($user, $planetrow, STRUC_MOON_PHALANX);
if (!intval($sensorLevel)) {
  SnTemplate::messageBox($lang['phalanx_nosensoravailable'], $lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

if ($planetrow['planet_type'] != PT_MOON) {
  SnTemplate::messageBox($lang['phalanx_onlyformoons'], $lang['tech'][STRUC_MOON_PHALANX], '', 3);
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
  SnTemplate::messageBox($lang['phalanx_rangeerror'], $lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

$cost = $sensorLevel * 1000;

if ($planetrow['deuterium'] < $cost)
{
  SnTemplate::messageBox($lang['phalanx_nodeuterium'], "phalanx", '', 3);
}

$planet_scanned = DBStaticPlanet::db_planet_by_gspt($scan_galaxy, $scan_system, $scan_planet, $scan_planet_type);
if(!$planet_scanned['id'])
{
  SnTemplate::messageBox($lang['phalanx_planet_not_exists'], $lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

if($planet_scanned['destruyed'])
{
  SnTemplate::messageBox($lang['phalanx_planet_destroyed'], $lang['tech'][STRUC_MOON_PHALANX], '', 3);
}

DBStaticPlanet::db_planet_set_by_id($user['current_planet'], "deuterium = deuterium - {$cost}");

$template = SnTemplate::gettemplate('planet_fleet_list', true);

$fleet_list = DbFleetStatic::fleet_and_missiles_list_by_coordinates($planet_scanned, true);
$fleets = flt_parse_fleets_to_events($fleet_list, $planet_scanned);
tpl_assign_fleet($template, $fleets);

$template->assign_vars(array(
  'MENU' => false,
  'NAVBAR' => false,
));

SnTemplate::display($template, $lang['tech'][STRUC_MOON_PHALANX]);
