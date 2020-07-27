<?php

use Fleet\DbFleetStatic;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

global $user, $debug;

lng_include('overview');
lng_include('fleet');

/**
 * @param int|string $userId
 * @param debug      $debug
 *
 * @throws Exception
 */
function flyingFleetsModel($userId, $debug) {
  if (empty($_POST['return']) || !is_array($_POST['return'])) {
    return;
  }

  foreach ($_POST['return'] as $fleet_id) {
    if (empty($fleet_id = idval($fleet_id))) {
      continue;
    }

    sn_db_transaction_start();
    if (empty($fleet = SN::$gc->repoV2->getFleet($fleet_id))) {
      sn_db_transaction_rollback();
      continue;
    }

    if (!$fleet->returnForce($userId)) {
      $debug->warning('Trying to return fleet that not belong to user', 'Hack attempt', 302, ['base_dump' => true, 'fleet_row' => $fleet->asArray()]);
      sn_db_transaction_rollback();
      die('Hack attempt 302');
    }
    sn_db_transaction_commit();
  }
}

/** @noinspection PhpUnhandledExceptionInspection */
flyingFleetsModel($user['id'], $debug);

if (!$planetrow) {
  SnTemplate::messageBox($lang['fl_noplanetrow'], $lang['fl_error']);
}

$template = SnTemplate::gettemplate('flying_fleets', true);

$i = 0;
$fleet_list = DbFleetStatic::fleet_list_by_owner_id($user['id']);
foreach ($fleet_list as $fleet_id => $fleet_row) {
  $i++;
  $fleet_data = tpl_parse_fleet_db($fleet_row, $i, $user);

  $template->assign_block_vars('fleets', $fleet_data['fleet']);

  foreach ($fleet_data['ships'] as $ship_data) {
    $template->assign_block_vars('fleets.ships', $ship_data);
  }
}

$MaxExpeditions = get_player_max_expeditons($user);
$FlyingExpeditions = DbFleetStatic::fleet_count_flying($user['id'], MT_EXPLORE);
$fleet_flying_amount = DbFleetStatic::fleet_count_flying($user['id'], MT_NONE);

$template->assign_vars(array(
  'FLEETS_FLYING'      => $fleet_flying_amount,
  'FLEETS_MAX'         => GetMaxFleets($user),
  'EXPEDITIONS_FLYING' => $FlyingExpeditions,
  'EXPEDITIONS_MAX'    => $MaxExpeditions,
));

SnTemplate::display($template, $lang['fl_title']);
