<?php

/**
 * add_ship.php
 *
 * @version 1.0
 * @copyright 2008 By Xire -AlteGarde-
 *
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

// if ($user['authlevel'] < 2)
if ($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$mode = $_POST['mode'];

$PageTpl = gettemplate("admin/add_research", true);
$parse = $lang;

if ($mode == 'addit')
{
  $id = $_POST['id'];
  $spy_tech = $_POST['spy_tech'];
  $computer_tech = $_POST['computer_tech'];
  $military_tech = $_POST['military_tech'];
  $defence_tech = $_POST['defence_tech'];
  $shield_tech = $_POST['shield_tech'];
  $energy_tech = $_POST['energy_tech'];
  $hyperspace_tech = $_POST['hyperspace_tech'];
  $combustion_tech = $_POST['combustion_tech'];
  $impulse_motor_tech = $_POST['impulse_motor_tech'];
  $hyperspace_motor_tech = $_POST['hyperspace_motor_tech'];
  $laser_tech = $_POST['laser_tech'];
  $ionic_tech = $_POST['ionic_tech'];
  $buster_tech = $_POST['buster_tech'];
  $intergalactic_tech = $_POST['intergalactic_tech'];
  $expedition_tech = $_POST['expedition_tech'];
  $graviton_tech = $_POST['graviton_tech'];
  $QryUpdatePlanet = "UPDATE {{users}} SET ";
  $QryUpdatePlanet .= "`spy_tech` = `spy_tech` + '" . $spy_tech . "', ";
  $QryUpdatePlanet .= "`computer_tech` = `computer_tech` + '" . $computer_tech . "', ";
  $QryUpdatePlanet .= "`military_tech` = `military_tech` + '" . $military_tech . "', ";
  $QryUpdatePlanet .= "`defence_tech` = `defence_tech` + '" . $defence_tech . "', ";
  $QryUpdatePlanet .= "`shield_tech` = `shield_tech` + '" . $shield_tech . "', ";
  $QryUpdatePlanet .= "`energy_tech` = `energy_tech` + '" . $energy_tech . "', ";
  $QryUpdatePlanet .= "`hyperspace_tech` = `hyperspace_tech` + '" . $hyperspace_tech . "', ";
  $QryUpdatePlanet .= "`combustion_tech` = `combustion_tech` + '" . $combustion_tech . "', ";
  $QryUpdatePlanet .= "`impulse_motor_tech` = `impulse_motor_tech` + '" . $impulse_motor_tech . "', ";
  $QryUpdatePlanet .= "`hyperspace_motor_tech` = `hyperspace_motor_tech` + '" . $hyperspace_motor_tech . "', ";
  $QryUpdatePlanet .= "`laser_tech` = `laser_tech` + '" . $laser_tech . "', ";
  $QryUpdatePlanet .= "`ionic_tech` = `ionic_tech` + '" . $ionic_tech . "', ";
  $QryUpdatePlanet .= "`buster_tech` = `buster_tech` + '" . $buster_tech . "', ";
  $QryUpdatePlanet .= "`intergalactic_tech` = `intergalactic_tech` + '" . $intergalactic_tech . "', ";
  $QryUpdatePlanet .= "`expedition_tech` = `expedition_tech` + '" . $expedition_tech . "', ";
  $QryUpdatePlanet .= "`graviton_tech` = `graviton_tech` + '" . $graviton_tech . "' ";
  $QryUpdatePlanet .= "WHERE ";
  $QryUpdatePlanet .= "`id` = '" . $id . "' ";
  doquery($QryUpdatePlanet);

  AdminMessage($lang['adm_addresearch2'], $lang['adm_addresearch1']);
}
$Page = parsetemplate($PageTpl, $parse);

display($Page, $lang['adm_am_ttle'], false, '', true);

?>
