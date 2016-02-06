<?php

require_once('includes/includes/ube_attack_calculate.php');

/*
  copyright © 2009-2014 Gorlum for http://supernova.ws
*/


function flt_planet_capture(&$fleet_row, &$combat_data) {return sn_function_call('flt_planet_capture', array(&$fleet_row, &$combat_data, &$result));}
function sn_flt_planet_capture(&$fleet_row, &$combat_data, &$result) {
  return $result;
}

function flt_mission_attack($mission_data, $save_report = true) {
  $fleet_row = $mission_data['fleet'];
  $destination_user = $mission_data['dst_user'];
  $destination_planet = $mission_data['dst_planet'];

  if(!$fleet_row) {
    return null;
  }

  if(
    // Нет данных о планете назначения или её владельце
    empty($destination_user) || empty($destination_planet) || !is_array($destination_user) || !is_array($destination_planet)
    ||
    // "Уничтожение" не на луну
    ($fleet_row['fleet_mission'] == MT_DESTROY && $destination_planet['planet_type'] != PT_MOON)
  ) {
    // doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    Fleet::static_fleet_send_back($fleet_row);

    return null;
  }

  $combat_data = ube_attack_prepare($mission_data);

  sn_ube_combat($combat_data);

  flt_planet_capture($fleet_row, $combat_data);

  sn_ube_report_save($combat_data);

  ube_combat_result_apply($combat_data);

  sn_ube_message_send($combat_data);

  // global $config;sn_db_transaction_rollback();$config->db_saveItem('fleet_update_lock', '');die();


  return $combat_data;
}
