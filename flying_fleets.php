<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

global $debug, $lang, $user, $planetrow;

if(!empty($_POST['return']) && is_array($_POST['return'])) {
  foreach($_POST['return'] as $fleet_id) {
    if($fleet_id = idval($fleet_id)) {
      sn_db_transaction_start();
      $objFleet = new Fleet();
      $objFleet->db_fleet_get_by_id($fleet_id);

      if ($objFleet->owner_id == $user['id'] && $objFleet->is_returning == 0) {
        $objFleet->fleet_command_return();
      } elseif ($objFleet->db_id && $objFleet->owner_id != $user['id']) {
        sn_db_transaction_rollback();
        $debug->warning('Trying to return fleet that not belong to user', 'Hack attempt', 302, array(
          'base_dump' => true,
          'fleet_owner_id' => $objFleet->owner_id,
          'user_id' => $user['id'])
        );
        die('Hack attempt 302');
      }
      sn_db_transaction_commit();
    }
  }
}

lng_include('overview');
lng_include('fleet');

if(!$planetrow) {
  message($lang['fl_noplanetrow'], $lang['fl_error']);
}

$template = gettemplate('flying_fleets', true);

$i = 0;
$objFleetList = FleetList::dbGetFleetListByOwnerId($user['id']);
if(!empty($objFleetList)) {
  foreach($objFleetList->_container as $fleet_id => $objFleet) {
    $i++;
    $fleet_data = tplParseFleetObject($objFleet, $i, $user);

    $template->assign_block_vars('fleets', $fleet_data['fleet']);

    foreach($fleet_data['ships'] as $ship_data) {
      $template->assign_block_vars('fleets.ships', $ship_data);
    }
  }
}

$MaxExpeditions = get_player_max_expeditons($user);
$FlyingExpeditions = FleetList::fleet_count_flying($user['id'], MT_EXPLORE);
$fleet_flying_amount = FleetList::fleet_count_flying($user['id'], MT_EXPLORE);

$template->assign_vars(array(
  'FLEETS_FLYING'      => $fleet_flying_amount,
  'FLEETS_MAX'         => GetMaxFleets($user),
  'EXPEDITIONS_FLYING' => $FlyingExpeditions,
  'EXPEDITIONS_MAX'    => $MaxExpeditions,
));

display($template, $lang['fl_title']);
