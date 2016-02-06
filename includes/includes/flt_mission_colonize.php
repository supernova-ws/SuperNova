<?php

/**
 * MissionCaseColonisation.php
 *
 * @version 1
 * @copyright 2008
 */

// ----------------------------------------------------------------------------------------------------------------
// Mission Case 9: -> Coloniser
//
function flt_mission_colonize(&$mission_data) {
  global $lang;

  $fleet_row = &$mission_data['fleet'];
  $src_user_row = &$mission_data['src_user'];

  $objFleet = new Fleet();
  $objFleet->parse_db_row($fleet_row);
  $fleet_end_coordinates = $objFleet->extract_end_coordinates_without_type();

  $TargetAddress = sprintf($lang['sys_adress_planet'], $fleet_end_coordinates['galaxy'], $fleet_end_coordinates['system'], $fleet_end_coordinates['planet']);

  $TheMessage = $lang['sys_colo_no_colonizer'];
  if($objFleet->ship_count_by_id(SHIP_COLONIZER) >= 1) {
    $TheMessage = $lang['sys_colo_notfree'];
    if(!$mission_data['dst_planet'] || empty($mission_data['dst_planet'])) {
      $iPlanetCount = get_player_current_colonies($src_user_row);

      // Can we colonize more planets?
      $TheMessage = $lang['sys_colo_maxcolo'];
      if($iPlanetCount < get_player_max_colonies($src_user_row)) {
        // Yes, we can colonize
        $TheMessage = $lang['sys_colo_badpos'];

        $NewOwnerPlanet = uni_create_planet(
          $fleet_end_coordinates['galaxy'], $fleet_end_coordinates['system'], $fleet_end_coordinates['planet'],
          $objFleet->owner_id, "{$lang['sys_colo_defaultname']} {$iPlanetCount}", false,
          array('user_row' => $src_user_row));
        if($NewOwnerPlanet) {
          $TheMessage = $lang['sys_colo_arrival'] . $TargetAddress . $lang['sys_colo_allisok'];
          msg_send_simple_message($objFleet->owner_id, '', $objFleet->time_arrive_to_target, MSG_TYPE_SPY, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);

          $objFleet->update_units(array(SHIP_COLONIZER => -1));
          $fleet_row = $objFleet->make_db_row();

          return RestoreFleetToPlanet($fleet_row, false);
        }
      }
    }
  }

  $objFleet->method_fleet_send_back();
  $objFleet->flush_changes_to_db();
  msg_send_simple_message($objFleet->owner_id, '', $objFleet->time_arrive_to_target, MSG_TYPE_SPY, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], "{$lang['sys_colo_arrival']}{$TargetAddress}{$TheMessage}");

  return CACHE_FLEET;
}
