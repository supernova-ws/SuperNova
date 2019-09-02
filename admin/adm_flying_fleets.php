<?php

/**
 * adm_flying_fleets.php
 *
 * @copyright 2014 by Gorlum for http://supernova.ws/
 */

use Fleet\DbFleetStatic;

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

$template = SnTemplate::gettemplate('admin/adm_flying_fleets', true);

//$FlyingFleets = db_fleet_list_all();
//while($CurrentFleet = db_fetch($FlyingFleets))

$all_flying_fleets = DbFleetStatic::db_fleet_list('', DB_SELECT_PLAIN);
foreach($all_flying_fleets as $fleet_id => $CurrentFleet) {
  $FleetOwner = db_user_by_id($CurrentFleet['fleet_owner']);
  $TargetOwner = db_user_by_id($CurrentFleet['fleet_target_owner']);

  $fleet_data = tpl_parse_fleet_db($CurrentFleet, ++$i, $FleetOwner);
  $fleet_data['fleet']['OWNER_NAME'] = htmlentities($FleetOwner['username'], ENT_COMPAT, 'UTF-8');
  $fleet_data['fleet']['TARGET_OWNER_NAME'] = htmlentities($TargetOwner['username'], ENT_COMPAT, 'UTF-8');

  $fleet_data['fleet']['STAY_TIME_INT'] = $CurrentFleet['fleet_end_stay'];

  $template->assign_block_vars('fleets', $fleet_data['fleet']);
  foreach($fleet_data['ships'] as $ship_data) {
    $template->assign_block_vars('fleets.ships', $ship_data);
  }
}

SnTemplate::display($template, $lang['flt_title']);
