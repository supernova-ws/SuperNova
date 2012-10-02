<?php

//require_once('includes/includes/coe_calculate_attack.php');
require_once('includes/includes/ube_attack_calculate.php');

/*
  copyright © 2009-2012 Gorlum for http://supernova.ws
*/

function flt_mission_attack($mission_data, $save_report = true)
{
  global $lang, $sn_data, $time_now;

  $fleet_row          = $mission_data['fleet'];
  $destination_user   = $mission_data['dst_user'];
  $destination_planet = $mission_data['dst_planet'];

  if(!$fleet_row)
  {
    return;
  }

  if(!$destination_user || !$destination_planet || !is_array($destination_user) || !is_array($destination_planet))
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    return;
  }

  $combat_data = ube_attack_prepare($mission_data);

  sn_ube_combat_calculate($combat_data);

  sn_ube_report_save($combat_data);

  sn_ube_combat_result_apply($combat_data);

  sn_ube_message_send($combat_data);

  return $combat_data;
}

?>
