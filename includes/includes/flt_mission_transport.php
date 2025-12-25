<?php

use Fleet\DbFleetStatic;
use Fleet\FleetDispatchEvent;
use Planet\DBStaticPlanet;

/**
 * @param FleetDispatchEvent $fleetEvent
 *
 * @return int|mixed
 */
function flt_mission_transport($fleetEvent) {
  if (empty($fleetEvent->dstPlanetId)) {
    // If there is no info on destination planet/moon by these coordinates - just returning fleet back
    DbFleetStatic::fleet_send_back($fleetEvent->fleet);

    return CACHE_FLEET;
  }

  $fleet_row          = $fleetEvent->fleet;
  $destination_planet = $fleetEvent->dstPlanetRow;
  // We didn't have source planet info for this mission - so retrieving it now
  $source_planet = $fleetEvent->updateSrcPlanetRow(DBStaticPlanet::db_planet_by_id($fleetEvent->srcPlanetId));

  global $lang;
  /** @noinspection PhpRedundantOptionalArgumentInspection */
  $Message = sprintf($lang['sys_tran_mess_user'],
    $source_planet['name'], uni_render_coordinates_href($fleet_row, 'fleet_start_', 3, ''),
    $destination_planet['name'], uni_render_coordinates_href($fleet_row, 'fleet_end_', 3, ''),
    $fleet_row['fleet_resource_metal'], $lang['Metal'],
    $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
    $fleet_row['fleet_resource_deuterium'], $lang['Deuterium']);

  $targetOwnerId = $fleetEvent->dstPlanetOwnerId;

  msg_send_simple_message($targetOwnerId, '', $fleet_row['fleet_start_time'], MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);

  if ($targetOwnerId <> $fleet_row['fleet_owner']) {
    msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
  }

  /** @noinspection PhpDeprecationInspection */
  return RestoreFleetToPlanet($fleet_row, false, true);
}
