<?php

/**
 * Fleet mission "Relocate"
 *
 * @param $mission_data Mission
 *
 * @return int
 *
 * @copyright 2008 by Gorlum for Project "SuperNova.WS"
 */
function flt_mission_relocate($mission_data) {
  global $lang;

  $objFleet = $mission_data->fleet;

  $destination_planet = &$mission_data->dst_planet;

  if(empty($destination_planet['id_owner']) || $objFleet->owner_id != $destination_planet['id_owner']) {
    $objFleet->mark_fleet_as_returned_and_save();

    return CACHE_FLEET;
  }

  $fleet_resources = $objFleet->get_resource_list();
  $Message = sprintf($lang['sys_tran_mess_user'],
      $mission_data->src_planet['name'], uni_render_coordinates_href($objFleet->launch_coordinates_typed(), '', 3),
      $destination_planet['name'], uni_render_coordinates_href($objFleet->target_coordinates_typed(), '', 3),
      $fleet_resources[RES_METAL], $lang['Metal'],
      $fleet_resources[RES_CRYSTAL], $lang['Crystal'],
      $fleet_resources[RES_DEUTERIUM], $lang['Deuterium']
    ) . '<br />' . $lang['sys_relocate_mess_user'];
  $fleet_real_array = $objFleet->get_ship_list();
  foreach($fleet_real_array as $ship_id => $ship_count) {
    $Message .= $lang['tech'][$ship_id] . ' - ' . $ship_count . '<br />';
  }
  msg_send_simple_message(
    $objFleet->owner_id, '', $objFleet->time_arrive_to_target, MSG_TYPE_TRANSPORT,
    $lang['sys_mess_qg'], $lang['sys_stay_mess_stay'], $Message
  );

  return $objFleet->RestoreFleetToPlanet(false);
}
