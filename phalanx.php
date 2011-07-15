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

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('overview');
lng_include('universe');

if (!intval($planetrow['phalanx'])) {
  message ($lang['phalanx_nosensoravailable'], $lang['tech'][42], "", 3);
}

if ($planetrow['planet_type'] != PT_MOON) {
  message ($lang['phalanx_onlyformoons'], $lang['tech'][42], "", 3);
}

$scan_galaxy  = sys_get_param_int('galaxy');
$scan_system  = sys_get_param_int('system');
$scan_planet  = sys_get_param_int('planet');
$scan_planet_type  = 1; // sys_get_param_int('planettype');
$id = sys_get_param_int('id');

$source_galaxy = $planetrow['galaxy'];
$source_system = $planetrow['system'];
$source_planet = $planetrow['planet'];

$sensorLevel = $planetrow['phalanx'];
$sensorRange = GetPhalanxRange($sensorLevel);

$system_distance = abs($source_system - $scan_system);
if($system_distance > $sensorRange || $scan_galaxy != $source_galaxy)
{
  message ($lang['phalanx_rangeerror'], $lang['tech'][42], "", 3);
}

$cost = $sensorLevel * 1000;
/*
debug(GetTargetDistance($source_galaxy, $scan_galaxy, $source_system, $scan_system, $source_planet, $scan_planet) / $sensorLevel * 10);
debug(GetPhalanxRange(1), 1);
debug(GetPhalanxRange(2), 2);
debug(GetPhalanxRange(3), 3);
debug(GetPhalanxRange(4), 4);
debug(GetPhalanxRange(5), 5);
debug(GetPhalanxRange(6), 6);
debug(GetPhalanxRange(7), 7);
debug(GetPhalanxRange(8), 8);
debug(GetPhalanxRange(9), 9);
debug(GetPhalanxRange(10), 10);
*/

if ($planetrow['deuterium'] > $cost)
{
  doquery("UPDATE {{planets}} SET deuterium = deuterium - {$cost} WHERE id='{$user['current_planet']}' LIMIT 1;");
}
else
{
  message($lang['phalanx_nodeuterium'], "phalanx", "", 3);
}


$template = gettemplate('planet_fleet_list', true);

$planet_scanned = doquery("SELECT * FROM {{planets}} WHERE galaxy = {$scan_galaxy} AND system = {$scan_system} AND planet = {$scan_planet} AND planet_type = {$scan_planet_type} LIMIT 1;", '', true);

int_get_fleet_to_planet(flt_get_fleets_to_planet_db($planet_scanned, true), $planet_scanned);
int_get_missile_to_planet("SELECT * FROM `{{iraks}}` WHERE galaxy = {$scan_galaxy} AND system = {$scan_system} AND planet = {$scan_planet};", true);
tpl_assign_fleet($template, $fleets);

$template->assign_vars(array(
  'TIME_NOW'             => $time_now,
));

$page = parsetemplate($template, $parse);

display($page, $lang['tech'][42], false, '', false, false);

?>
