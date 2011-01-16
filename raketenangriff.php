<?php

/**
 * raketenangriff.php
 *
 * 1.0st - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
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

includeLang('fleet');

$g = intval($_GET['galaxy']);
$s = intval($_GET['system']);
$i = intval($_GET['planet']);
$MIPSent = max(0,intval($_POST['SendMI']));

$targetedStructures = intval($_POST['Target']);

doquery("START TRANSACTION;");
$planetrow = doquery("SELECT * FROM `{{planets}}` WHERE `id` = '{$user['current_planet']}' LIMIT 1 FOR UPDATE;", '',true);

$MIPAvailable = $planetrow['interplanetary_misil'];
$distance = abs($s - $planetrow['system']);
$mipRange = ($user['impulse_motor_tech'] * 5) - 1;
$target_planet = doquery("SELECT * FROM `{{planets}}` WHERE `galaxy` = '".$g."' AND system = ".$s." AND planet = ".$i." AND planet_type = 1 LIMIT 1;", '', true);

if ($planetrow['silo'] < 4)
{
  message(sprintf($lang['mip_no_silo'],$planetrow['name']), $lang['sys_error']);
}
elseif ($user['impulse_motor_tech'] == 0)
{
  message($lang['mip_no_impulse'], $lang['sys_error']);
}
elseif ($distance >= $mipRange || $g != $planetrow['galaxy'])
{
  message($lang['mip_too_far'], $lang['sys_error']);
}
elseif (!$target_planet['id'] != 1)
{
  message($lang['mip_planet_error'], $lang['sys_error']);
}
elseif ($MIPSent > $MIPAvailable)
{
  message($lang['mip_no_rocket'], $lang['sys_error']);
}
elseif ($targetedStructures && !in_array($targetedStructures, $sn_groups['defense_ative']))
{
  message($targetedStructures.$lang['mip_hack_attempt'], $lang['sys_error']);
};

$cant_attack = flt_can_attack($planetrow, $target_planet, MT_MISSILE, array(503 => $MIPSent));
if($cant_attack != ATTACK_ALLOWED)
{
  doquery("ROLLBACK;");
  message("<font color=\"red\"><b>{$lang['fl_attack_error'][$cant_attack]}</b></font>", $lang['fl_error'], "fleet.{$phpEx}", 5);
}

$ziel_id = $target_planet['id_owner'];

$flugzeit = round((30 + (60 * $distance)) / get_fleet_speed());

doquery("INSERT INTO `{{table}}` SET
   `zeit` = '".(time() + $flugzeit)."',
   `galaxy` = '".$g."',
   `system` = '".$s."',
   `planet` = '".$i."',
   `galaxy_angreifer` = '".$planetrow['galaxy']."',
   `system_angreifer` = '".$planetrow['system']."',
   `planet_angreifer` = '".$planetrow['planet']."',
   `owner` = '".$user['id']."',
   `zielid` = '".$ziel_id."',
   `anzahl` = '".$MIPSent."',
   `primaer` = '".$targetedStructures."'", 'iraks');

doquery("UPDATE `{{planets}}` SET `interplanetary_misil` = `interplanetary_misil` - '{$MIPSent}' WHERE `id` = '{$user['current_planet']}' LIMIT 1;");

$planetrow['interplanetary_misil'] -= $MIPSent;
doquery("COMMIT;");

message(sprintf($lang['mip_launched'], $MIPSent), $lang['mip_h_launched']);
// Copyright (c) 2007 by -= MoF =- for Deutsches UGamela Forum
// 05.12.2007 - 11:45
// Open Source
?>