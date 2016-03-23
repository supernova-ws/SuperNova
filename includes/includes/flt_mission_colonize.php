<?php

/**
 * Fleet mission "Recycle"
 *
 * @param $mission_data Mission
 *
 * @return int
 *
 * @copyright 2008 by Gorlum for Project "SuperNova.WS"
 */
function flt_mission_colonize(&$mission_data) {
  global $lang;

  $objFleet = $mission_data->fleet;
  $src_user_row = &$mission_data->src_user;

  $fleet_end_coordinates = $objFleet->target_coordinates_without_type();

  $TargetAddress = sprintf($lang['sys_adress_planet'], $fleet_end_coordinates['galaxy'], $fleet_end_coordinates['system'], $fleet_end_coordinates['planet']);

  $TheMessage = $lang['sys_colo_no_colonizer'];
  if($objFleet->shipCountById(SHIP_COLONIZER) >= 1) {
    $TheMessage = $lang['sys_colo_notfree'];
    if(empty($mission_data->dst_planet)) {
      $iPlanetCount = get_player_current_colonies($src_user_row);

      // Can we colonize more planets?
      $TheMessage = $lang['sys_colo_maxcolo'];
      if($iPlanetCount < get_player_max_colonies($src_user_row)) {
        // Yes, we can colonize
        $TheMessage = $lang['sys_colo_badpos'];

        $NewOwnerPlanet = uni_create_planet(
          $fleet_end_coordinates['galaxy'], $fleet_end_coordinates['system'], $fleet_end_coordinates['planet'],
          $objFleet->playerOwnerId, "{$lang['sys_colo_defaultname']} {$iPlanetCount}", false,
          array('user_row' => $src_user_row));
        if($NewOwnerPlanet) {
          $TheMessage = $lang['sys_colo_arrival'] . $TargetAddress . $lang['sys_colo_allisok'];
          msg_send_simple_message($objFleet->playerOwnerId, '', $objFleet->time_arrive_to_target, MSG_TYPE_SPY, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);

          $objFleet->unitAdjustCount(SHIP_COLONIZER, -1);
          return $objFleet->RestoreFleetToPlanet(false);
        }
      }
    }
  }

  $objFleet->mark_fleet_as_returned();
  $objFleet->dbSave();
  msg_send_simple_message($objFleet->playerOwnerId, '', $objFleet->time_arrive_to_target, MSG_TYPE_SPY, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], "{$lang['sys_colo_arrival']}{$TargetAddress}{$TheMessage}");

  return CACHE_FLEET;
}
