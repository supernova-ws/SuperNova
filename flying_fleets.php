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
//$fleet_query = doquery("SELECT * FROM {{fleets}} WHERE fleet_owner={$user['id']};");
//while ($fleet_row = db_fetch($fleet_query)) {
//  $i++;
//  $fleet_data = tpl_parse_fleet_db($fleet_row, $i, $user);
//
//  $template->assign_block_vars('fleets', $fleet_data['fleet']);
//
//  foreach($fleet_data['ships'] as $ship_data) {
//    $template->assign_block_vars('fleets.ships', $ship_data);
//  }
//}

$fleet_list = FleetList::fleet_list_by_owner_id($user['id']);
foreach($fleet_list as $fleet_id => $fleet_row) {
  $i++;
  $fleet_data = tpl_parse_fleet_db($fleet_row, $i, $user);

  $template->assign_block_vars('fleets', $fleet_data['fleet']);

  foreach($fleet_data['ships'] as $ship_data) {
    $template->assign_block_vars('fleets.ships', $ship_data);
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
