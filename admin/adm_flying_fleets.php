<?php

/**
 * adm_flying_fleets.php
 *
 * @copyright 2014 by Gorlum for http://supernova.ws/
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$template = gettemplate('admin/adm_flying_fleets', true);

$all_flying_fleets = FleetList::dbGetFleetList();
foreach($all_flying_fleets->_container as $fleet_id => $objFleet) {
  $FleetOwner = DBStaticUser::db_user_by_id($objFleet->playerOwnerId);
  $TargetOwner = DBStaticUser::db_user_by_id($objFleet->target_owner_id);

  $fleet_data = tplParseFleetObject($objFleet, ++$i, $FleetOwner);
  $fleet_data['fleet']['OWNER_NAME'] = htmlentities($FleetOwner['username'], ENT_COMPAT, 'UTF-8');
  $fleet_data['fleet']['TARGET_OWNER_NAME'] = htmlentities($TargetOwner['username'], ENT_COMPAT, 'UTF-8');

  $fleet_data['fleet']['STAY_TIME_INT'] = $objFleet->time_mission_job_complete;

  $template->assign_block_vars('fleets', $fleet_data['fleet']);
  foreach($fleet_data['ships'] as $ship_data) {
    $template->assign_block_vars('fleets.ships', $ship_data);
  }
}

display($template, classLocale::$lang['flt_title'], false, '', true);
