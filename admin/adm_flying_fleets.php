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
if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$template = gettemplate('admin/adm_flying_fleets', true);

$FlyingFleets = doquery("SELECT f.*, u.username FROM `{{fleets}}` AS f LEFT JOIN {{users}} AS u ON u.id = f.fleet_target_owner ORDER BY `fleet_end_time` ASC;");
while($CurrentFleet = mysql_fetch_assoc($FlyingFleets))
{
  $FleetOwner = doquery("SELECT * FROM `{{users}}` WHERE `id` = '" . $CurrentFleet['fleet_owner'] . "';", '', true);

  $fleet_data = tpl_parse_fleet_db($CurrentFleet, ++$i, $FleetOwner);
  $fleet_data['fleet']['OWNER_NAME'] = htmlentities($FleetOwner['username'], ENT_COMPAT, 'UTF-8');
  $fleet_data['fleet']['TARGET_OWNER_NAME'] = htmlentities($CurrentFleet['username'], ENT_COMPAT, 'UTF-8');

  $fleet_data['fleet']['STAY_TIME_INT'] = $CurrentFleet['fleet_end_stay'];

  $template->assign_block_vars('fleets', $fleet_data['fleet']);
  foreach($fleet_data['ships'] as $ship_data)
  {
    $template->assign_block_vars('fleets.ships', $ship_data);
  }
}

display($template, $lang['flt_title'], false, '', true);
