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

// if ($user['authlevel'] < 2)
if($user['authlevel'] < 3) {
  AdminMessage($lang['adm_err_denied']);
}

$template = gettemplate('admin/adm_flying_fleets', true);

//$FlyingFleets = db_fleet_list_all();
//while($CurrentFleet = db_fetch($FlyingFleets))

$all_flying_fleets = db_fleet_list('');
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

display($template, $lang['flt_title'], false, '', true);
