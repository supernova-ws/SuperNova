<?php

use Fleet\DbFleetStatic;
use Fleet\FleetDispatcher;
use Fleet\FleetDispatchEvent;
use Ube\Ube4_1\Ube4_1Calc;
use Ube\Ube4_1\Ube4_1Prepare;

/** @noinspection PhpIncludeInspection */
require_once(SN_ROOT_PHYSICAL . 'includes/includes/ube_attack_calculate.php');

/*
  copyright © 2009-2014 Gorlum for http://supernova.ws
*/

/**
 * @param $fleet_row
 * @param $combat_data
 *
 * @return mixed
 *
 * @see sn_flt_planet_capture()
 */
function flt_planet_capture(&$fleet_row, &$combat_data) {
  $result = null;

  return sn_function_call('flt_planet_capture', array(&$fleet_row, &$combat_data, &$result));
}

/** @noinspection PhpUnusedParameterInspection */
function sn_flt_planet_capture(&$fleet_row, &$combat_data, $result) {
  return $result;
}

/**
 * @param FleetDispatchEvent $fleetEvent
 *
 * @return array|null
 * @noinspection PhpUnusedParameterInspection
 */
function flt_mission_attack($fleetEvent) {
  $fleet_row = $fleetEvent->fleet;
  if (
    // Nothing to do if fleet row is empty
    empty($fleet_row)
    // Also nothing to do if event is not fleet arrival to destination - i.e. actual attack
    || $fleetEvent->event !== EVENT_FLT_ARRIVE
  ) {
    return null;
  }

  if (
    // Нет данных о планете назначения
    empty($fleetEvent->dstPlanetId)
    // "Уничтожение" не на луну
    || ($fleetEvent->missionId == MT_DESTROY && $fleetEvent->dstPlanetRow['planet_type'] != PT_MOON)
  ) {
    DbFleetStatic::fleet_send_back($fleet_row);

    return null;
  }

  $acs_fleet_list = empty($fleet_row['fleet_group']) ? [$fleet_row] : DbFleetStatic::fleet_list_by_group($fleet_row['fleet_group']);

  $fleet_list_on_hold = DbFleetStatic::fleet_list_on_hold(
    $fleet_row['fleet_end_galaxy'],
    $fleet_row['fleet_end_system'],
    $fleet_row['fleet_end_planet'],
    $fleet_row['fleet_end_type'],
    $fleetEvent->eventTimeStamp
  );

  $ubePrepare  = new Ube4_1Prepare();
  $combat_data = $ubePrepare->prepareFromMissionArray($fleetEvent, $fleet_list_on_hold, $acs_fleet_list);

  $ubeCalc = new Ube4_1Calc();
  $ubeCalc->sn_ube_combat($combat_data);

  flt_planet_capture($fleet_row, $combat_data);

  sn_ube_report_save($combat_data);

  ube_combat_result_apply($combat_data);

  sn_ube_message_send($combat_data);

  return $combat_data;
}
