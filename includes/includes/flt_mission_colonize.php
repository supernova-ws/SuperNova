<?php
use Mission\Mission;
use \DBStatic\DBStaticMessages;

/**
 * Fleet mission "Recycle"
 *
 * @param $mission_data Mission
 *
 * @copyright 2008 by Gorlum for Project "SuperNova.WS"
 */
function flt_mission_colonize(&$mission_data) {
  $classLocale = classLocale::$lang;

  $objFleet = $mission_data->fleet;
  $src_user_row = &$mission_data->src_user;

  $fleet_end_coordinates = $objFleet->target_coordinates_without_type();

  $TargetAddress = sprintf(classLocale::$lang['sys_adress_planet'], $fleet_end_coordinates['galaxy'], $fleet_end_coordinates['system'], $fleet_end_coordinates['planet']);

  $TheMessage = classLocale::$lang['sys_colo_no_colonizer'];
  if($objFleet->shipsGetTotalById(SHIP_COLONIZER) >= 1) {
    $TheMessage = classLocale::$lang['sys_colo_notfree'];
    if(empty($mission_data->dst_planet)) {
      $iPlanetCount = get_player_current_colonies($src_user_row);

      // Can we colonize more planets?
      $TheMessage = classLocale::$lang['sys_colo_maxcolo'];
      if($iPlanetCount < get_player_max_colonies($src_user_row)) {
        // Yes, we can colonize
        $TheMessage = classLocale::$lang['sys_colo_badpos'];

        $NewOwnerPlanet = uni_create_planet(
          $fleet_end_coordinates['galaxy'], $fleet_end_coordinates['system'], $fleet_end_coordinates['planet'],
          $objFleet->playerOwnerId, "{$classLocale['sys_colo_defaultname']} {$iPlanetCount}", false,
          array('user_row' => $src_user_row));
        if($NewOwnerPlanet) {
          $TheMessage = classLocale::$lang['sys_colo_arrival'] . $TargetAddress . classLocale::$lang['sys_colo_allisok'];
          DBStaticMessages::msg_send_simple_message($objFleet->playerOwnerId, '', $objFleet->time_arrive_to_target, MSG_TYPE_SPY, classLocale::$lang['sys_colo_mess_from'], classLocale::$lang['sys_colo_mess_report'], $TheMessage);

          $objFleet->shipAdjustCount(SHIP_COLONIZER, -1);
          $objFleet->shipsLand(false);
          return;
        }
      }
    }
  }

  $objFleet->markReturnedAndSave();
  DBStaticMessages::msg_send_simple_message($objFleet->playerOwnerId, '', $objFleet->time_arrive_to_target, MSG_TYPE_SPY, classLocale::$lang['sys_colo_mess_from'], classLocale::$lang['sys_colo_mess_report'], "{$classLocale['sys_colo_arrival']}{$TargetAddress}{$TheMessage}");
}
