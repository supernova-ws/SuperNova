<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if(!empty($_POST['return']) && is_array($_POST['return'])) {
  foreach($_POST['return'] as $fleet_id) {
    if($fleet_id = idval($fleet_id)) {
      sn_db_transaction_start();
      $FleetRow = db_fleet_get($fleet_id);

      if ($FleetRow['fleet_owner'] == $user['id'] && $FleetRow['fleet_mess'] == 0) {
        fleet_return_forced($FleetRow, $user);
      } elseif ($FleetRow['fleet_id'] && $FleetRow['fleet_owner'] != $user['id']) {
        $debug->warning('Trying to return fleet that not belong to user', 'Hack attempt', 302, array('base_dump' => true, 'fleet_row' => $FleetRow));
        sn_db_transaction_rollback();
        die('Hack attempt 302');
      }
      sn_db_transaction_commit();
    }
  }
}

lng_include('overview');
lng_include('fleet');

if(!$planetrow) {
  messageBox($lang['fl_noplanetrow'], $lang['fl_error']);
}

$template = gettemplate('flying_fleets', true);

$i = 0;
$fleet_list = fleet_list_by_owner_id($user['id']);
foreach($fleet_list as $fleet_id => $fleet_row) {
  $i++;
  $fleet_data = tpl_parse_fleet_db($fleet_row, $i, $user);

  $template->assign_block_vars('fleets', $fleet_data['fleet']);

  foreach($fleet_data['ships'] as $ship_data) {
    $template->assign_block_vars('fleets.ships', $ship_data);
  }
}

$MaxExpeditions = get_player_max_expeditons($user);
$FlyingExpeditions = fleet_count_flying($user['id'], MT_EXPLORE);
$fleet_flying_amount = fleet_count_flying($user['id'], MT_NONE);

$template->assign_vars(array(
  'FLEETS_FLYING'      => $fleet_flying_amount,
  'FLEETS_MAX'         => GetMaxFleets($user),
  'EXPEDITIONS_FLYING' => $FlyingExpeditions,
  'EXPEDITIONS_MAX'    => $MaxExpeditions,
));

display($template, $lang['fl_title']);
