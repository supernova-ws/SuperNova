<?php

use Fleet\DbFleetStatic;
use Fleet\FleetDispatchEvent;
use Planet\DBStaticPlanet;

/**
 * @param FleetDispatchEvent $fleetEvent
 *
 * @return int|mixed
 */
function flt_mission_relocate($fleetEvent) {
  $fleet_row          = $fleetEvent->fleet;
  $destination_planet = $fleetEvent->dstPlanetRow;
  // We didn't have source planet info for this mission - so retrieving it now
  $source_planet = $fleetEvent->updateSrcPlanetRow(DBStaticPlanet::db_planet_by_id($fleetEvent->srcPlanetId));

  if (!$destination_planet || !is_array($destination_planet) || $fleetEvent->fleet['fleet_owner'] != $fleetEvent->dstPlanetOwnerId) {
    DbFleetStatic::fleet_send_back($fleetEvent->fleet);

    return CACHE_FLEET;
  }

  global $lang;

  /** @noinspection PhpRedundantOptionalArgumentInspection */
  $Message = sprintf($lang['sys_tran_mess_user'],
      $source_planet['name'],
      uni_render_coordinates_href($fleet_row, 'fleet_start_', 3, ''),
      $destination_planet['name'],
      uni_render_coordinates_href($fleet_row, 'fleet_end_', 3, ''),
      $fleet_row['fleet_resource_metal'], $lang['Metal'],
      $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
      $fleet_row['fleet_resource_deuterium'], $lang['Deuterium']) .
    '<br />' . $lang['sys_relocate_mess_user'];

  foreach (sys_unit_str2arr($fleet_row['fleet_array']) as $ship_id => $ship_count) {
    $Message .= $lang['tech'][$ship_id] . ' - ' . $ship_count . '<br />';
  }

  msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_TRANSPORT, $lang['sys_mess_qg'], $lang['sys_stay_mess_stay'], $Message);

  /** @noinspection PhpDeprecationInspection */
  return RestoreFleetToPlanet($fleet_row, false);
}
