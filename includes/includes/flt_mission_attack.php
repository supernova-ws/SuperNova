<?php

require_once(SN_ROOT_PHYSICAL . 'includes/includes/ube_attack_calculate.php');

/*
  copyright © 2009-2014 Gorlum for http://supernova.ws
*/


function flt_planet_capture(&$fleet_row, &$combat_data) { return sn_function_call('flt_planet_capture', array(&$fleet_row, &$combat_data, &$result)); }

function sn_flt_planet_capture(&$fleet_row, &$combat_data, &$result) {
  return $result;
}

function flt_mission_attack($mission_data, $save_report = true) {
  $fleet_row = $mission_data['fleet'];
  $destination_user = $mission_data['dst_user'];
  $destination_planet = $mission_data['dst_planet'];

  if (!$fleet_row) {
    return null;
  }

  if (
    // Нет данных о планете назначения или её владельце
    empty($destination_user) || empty($destination_planet) || !is_array($destination_user) || !is_array($destination_planet)
    ||
    // "Уничтожение" не на луну
    ($fleet_row['fleet_mission'] == MT_DESTROY && $destination_planet['planet_type'] != PT_MOON)
  ) {
    fleet_send_back($fleet_row);

    return null;
  }

  $fleet_list_on_hold = fleet_list_on_hold($fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'], $fleet_row['fleet_end_type'], $ube_time);
  $ubePrepare = new \Ube\Ube4_1\Ube4_1Prepare();
  $combat_data = $ubePrepare->ube_attack_prepare($mission_data, $fleet_list_on_hold);
  /**
   * @var \Ube\Ube4_1\Ube4_1Calc $ubeCalc
   */
  $ubeCalc = $combat_data[UBE_OBJ_CALCULATOR];
  $ubeCalc->sn_ube_combat($combat_data);

  flt_planet_capture($fleet_row, $combat_data);

  sn_ube_report_save($combat_data);

  ube_combat_result_apply($combat_data);

  sn_ube_message_send($combat_data);

  return $combat_data;
}
