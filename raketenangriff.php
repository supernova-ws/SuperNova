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

$ugamela_root_path = './';
include_once($ugamela_root_path . 'extension.inc');
include_once($ugamela_root_path . 'common.'.$phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

$g = intval($_GET['galaxy']);
$s = intval($_GET['system']);
$i = intval($_GET['planet']);
$MIPSent = max(0,intval($_POST['SendMI']));

$targetedStructures = SYS_mysqlSmartEscape($_POST['Target']);
if ($targetedStructures == "all")
  $targetedStructures = 0;
else
  $targetedStructures = intval($targetedStructures);

$currentplanet = doquery("SELECT * FROM `{{table}}` WHERE `id` =  '{$user['current_planet']}'",'planets',true);
$MIPAvailable = $currentplanet['interplanetary_misil'];

$distance = abs($s-$currentplanet['system']);
$mipRange = ($user['impulse_motor_tech'] * 5) - 1;
$tempvar3 = doquery("SELECT * FROM `{{table}}` WHERE `galaxy` = '".$g."' AND system = ".$s." AND planet = ".$i." AND planet_type = 1", 'planets');

includeLang('mip');

$error = 1;

if ($currentplanet['silo'] < 4) {
  message(sprintf($lang['mip_no_silo'],$currentplanet['name']), $lang['sys_error']);
} elseif ($user['impulse_motor_tech'] == 0) {
  message($lang['mip_no_impulse'], $lang['sys_error']);
} elseif ($distance >= $mipRange || $g != $currentplanet['galaxy']) {
  message($lang['mip_too_far'], $lang['sys_error']);
} elseif (mysql_num_rows($tempvar3) != 1) {
  message($lang['mip_planet_error'], $lang['sys_error']);
} elseif ($MIPSent > $MIPAvailable) {
  message($lang['mip_no_rocket'], $lang['sys_error']);
} elseif ($targetedStructures && ($targetedStructures < 401 || $targetedStructures > 409)){
  message($targetedStructures.$lang['mip_hack_attempt'], $lang['sys_error']);
}else{
  $error = 0;
};

if ($error) {
//  message('Опа, ракетные атаки недоступны.', 'Ошибка');
  exit();
};

$planet = doquery("SELECT * FROM `{{table}}` WHERE `galaxy` = '".$g."' AND
      `system` = '".$s."' AND
      `planet` = '".$i."' AND
      `planet_type` = '1'", 'planets', true);

$ziel_id = $planet['id_owner'];

$flugzeit = round((30 + (60 * $distance)) / get_fleet_speed());

doquery("INSERT INTO `{{table}}` SET
   `zeit` = '".(time() + $flugzeit)."',
   `galaxy` = '".$g."',
   `system` = '".$s."',
   `planet` = '".$i."',
   `galaxy_angreifer` = '".$currentplanet['galaxy']."',
   `system_angreifer` = '".$currentplanet['system']."',
   `planet_angreifer` = '".$currentplanet['planet']."',
   `owner` = '".$user['id']."',
   `zielid` = '".$ziel_id."',
   `anzahl` = '".$MIPSent."',
   `primaer` = '".$targetedStructures."'", 'iraks');

doquery("UPDATE `{{table}}` SET `interplanetary_misil` = `interplanetary_misil` - '{$MIPSent}' WHERE `id` = '".$user['current_planet']."'", 'planets');

message(sprintf($lang['mip_launched'], $MIPSent), $lang['mip_h_launched']);

$planetrow = doquery ("SELECT * FROM {{table}} WHERE `id` = '". $user['current_planet'] ."';", 'planets', true);

// Copyright (c) 2007 by -= MoF =- for Deutsches UGamela Forum
// 05.12.2007 - 11:45
// Open Source
?>