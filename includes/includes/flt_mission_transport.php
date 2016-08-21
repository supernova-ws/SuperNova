<?php
use Mission\Mission;
use \DBStatic\DBStaticMessages;

/**
 * Fleet mission "Transport"
 *
 * @param $mission_data Mission
 *
 * @copyright 2008 by Gorlum for Project "SuperNova.WS"
 */
function flt_mission_transport($mission_data) {
  $objFleet = $mission_data->fleet;
  $source_planet = &$mission_data->src_planet;
  $destination_planet = &$mission_data->dst_planet;

  if(empty($destination_planet['id_owner'])) {
    $objFleet->markReturnedAndSave();

    return;
  }

  $fleet_resources = $objFleet->resourcesGetList();
  $Message = sprintf(classLocale::$lang['sys_tran_mess_user'],
    $source_planet['name'], uni_render_coordinates_href($objFleet->launch_coordinates_typed(), '', 3),
    $destination_planet['name'], uni_render_coordinates_href($objFleet->target_coordinates_typed(), '', 3),
    $fleet_resources[RES_METAL], classLocale::$lang['Metal'],
    $fleet_resources[RES_CRYSTAL], classLocale::$lang['Crystal'],
    $fleet_resources[RES_DEUTERIUM], classLocale::$lang['Deuterium']);
  DBStaticMessages::msg_send_simple_message($objFleet->target_owner_id, '', $objFleet->time_arrive_to_target, MSG_TYPE_TRANSPORT, classLocale::$lang['sys_mess_tower'], classLocale::$lang['sys_mess_transport'], $Message);

  if($objFleet->target_owner_id <> $objFleet->playerOwnerId) {
    DBStaticMessages::msg_send_simple_message($objFleet->playerOwnerId, '', $objFleet->time_arrive_to_target, MSG_TYPE_TRANSPORT, classLocale::$lang['sys_mess_tower'], classLocale::$lang['sys_mess_transport'], $Message);
  }

  $objFleet->resourcesUnload(false);
  $objFleet->markReturnedAndSave();
}
