<?php

/**
 * flt_mission_transport.php
 *
 * @version 2.0 return cacher result
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_transport(&$mission_data) {
  $objFleet = new Fleet();
  $objFleet->parse_db_row($mission_data['fleet']);

  $fleet_row = &$mission_data['fleet'];

  $source_planet = &$mission_data['src_planet'];
  $destination_planet = &$mission_data['dst_planet'];

  if(!isset($destination_planet['id']) || !$destination_planet['id_owner']) {
    $objFleet->mark_fleet_as_returned_and_save();

    return CACHE_FLEET;
  }

  global $lang;
  $Message = sprintf($lang['sys_tran_mess_user'],
    $source_planet['name'], uni_render_coordinates_href($objFleet->launch_coordinates_typed(), '', 3),
    $destination_planet['name'], uni_render_coordinates_href($objFleet->target_coordinates_typed(), '', 3),
    $fleet_row['fleet_resource_metal'], $lang['Metal'],
    $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
    $fleet_row['fleet_resource_deuterium'], $lang['Deuterium']);
  msg_send_simple_message($objFleet->target_owner_id, '', $objFleet->time_arrive_to_target, MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);

  if($objFleet->target_owner_id <> $objFleet->owner_id) {
    msg_send_simple_message($objFleet->owner_id, '', $objFleet->time_arrive_to_target, MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
  }

  return $objFleet->RestoreFleetToPlanet(false, true);
}
