<?php

/*
  copyright © 2009-2016 Gorlum for http://supernova.ws
*/

// Used by game_skirmish
function flt_planet_capture(&$fleet_row, &$combat_data) { return sn_function_call(__FUNCTION__, array(&$fleet_row, &$combat_data, &$result)); }
function sn_flt_planet_capture(&$fleet_row, &$combat_data, &$result) { return $result; }

/**
 * @param Mission $objMission
 * @param array   $mission_data
 *
 * @return array|null
 */
function flt_mission_attack($objMission) {
  $objFleet = $objMission->fleet;
  $fleet_row = $objFleet->make_db_row();

  if(!$fleet_row) {
    return null;
  }

  $destination_user = $objMission->dst_user;
  $destination_planet = $objMission->dst_planet;

  if(
    // Нет данных о планете назначения или её владельце
    empty($destination_user) || empty($destination_planet) || !is_array($destination_user) || !is_array($destination_planet)
    ||
    // "Уничтожение" не на луну
    ($objFleet->mission_type == MT_DESTROY && $destination_planet['planet_type'] != PT_MOON)
  ) {
    // doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    $objFleet->mark_fleet_as_returned_and_save();

    return null;
  }

  require_once('includes/classes/UBE/UBE.php');

  return UBE::flt_mission_attack($objMission, $fleet_row);
}
