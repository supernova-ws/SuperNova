<?php /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

use Fleet\DbFleetStatic;
use Fleet\FleetDispatchEvent;

/**
 * @param FleetDispatchEvent $fleetEvent
 *
 * @return int|mixed
 */
function flt_mission_colonize($fleetEvent) {
  $fleet_row    = $fleetEvent->fleet;
  /** @noinspection PhpDeprecationInspection */
  $fleetOwnerRow = db_user_by_id($fleetEvent->fleet['fleet_owner'], true);

  global $lang;

  $targetAddress = sprintf($lang['sys_address_planet'], $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);

  $fleet_array = sys_unit_str2arr($fleet_row['fleet_array']);

  $TheMessage = $lang['sys_colo_no_colonizer'];
  if ($fleet_array[SHIP_COLONIZER] >= 1) {
    $TheMessage = $lang['sys_colo_not_free'];
    if (empty($fleetEvent->dstPlanetRow)) {
      $iPlanetCount = get_player_current_colonies($fleetOwnerRow);

      // Can we colonize more planets?
      $TheMessage = $lang['sys_colo_max_colo'];
      if ($iPlanetCount < get_player_max_colonies($fleetOwnerRow)) {
        // Yes, we can colonize
        $TheMessage     = $lang['sys_colo_bad_pos'];
        $NewOwnerPlanet = uni_create_planet(
          $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'],
          $fleet_row['fleet_owner'], "{$lang['sys_colo_default_name']} {$iPlanetCount}", false,
          array('user_row' => $fleetOwnerRow));
        if ($NewOwnerPlanet) {
          $TheMessage = $lang['sys_colo_arrival'] . $targetAddress . $lang['sys_colo_all_is_ok'];
          msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);

          $fleet_array[SHIP_COLONIZER]--;
          $fleet_row['fleet_amount']--;
          $fleet_row['fleet_array'] = sys_unit_arr2str($fleet_array);

          /** @noinspection PhpDeprecationInspection */
          return RestoreFleetToPlanet($fleet_row, false);
        }
      }
    }
  }

  DbFleetStatic::fleet_send_back($fleet_row);
  msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], "{$lang['sys_colo_arrival']}{$targetAddress}{$TheMessage}");

  return CACHE_FLEET;
}
