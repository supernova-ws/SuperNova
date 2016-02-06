<?php

/**
 * Fleet mission "Transport"
 *
 * @param $mission_data Mission
 *
 * @return int
 *
 * @copyright 2008 by Gorlum for Project "SuperNova.WS"
 */
function flt_mission_transport($mission_data) {
  global $lang;

  $objFleet = $mission_data->fleet;
  $source_planet = &$mission_data->src_planet;
  $destination_planet = &$mission_data->dst_planet;

  if(!isset($destination_planet['id']) || !$destination_planet['id_owner']) {
    $objFleet->mark_fleet_as_returned_and_save();

    return CACHE_FLEET;
  }

  $fleet_resources = $objFleet->get_resource_list();
  $Message = sprintf($lang['sys_tran_mess_user'],
    $source_planet['name'], uni_render_coordinates_href($objFleet->launch_coordinates_typed(), '', 3),
    $destination_planet['name'], uni_render_coordinates_href($objFleet->target_coordinates_typed(), '', 3),
    $fleet_resources[RES_METAL], $lang['Metal'],
    $fleet_resources[RES_CRYSTAL], $lang['Crystal'],
    $fleet_resources[RES_DEUTERIUM], $lang['Deuterium']);
  msg_send_simple_message($objFleet->target_owner_id, '', $objFleet->time_arrive_to_target, MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);

  if($objFleet->target_owner_id <> $objFleet->owner_id) {
    msg_send_simple_message($objFleet->owner_id, '', $objFleet->time_arrive_to_target, MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
  }

  return $objFleet->RestoreFleetToPlanet(false, true);
}
