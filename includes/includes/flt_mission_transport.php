<?php

use Fleet\DbFleetStatic;

/**
 * flt_mission_transport.php
 *
 * @version 2.0 return cacher result
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_transport(&$mission_data)
{
  $fleet_row          = &$mission_data['fleet'];
  $source_planet      = &$mission_data['src_planet'];
  $destination_planet = &$mission_data['dst_planet'];

  if(!isset($destination_planet['id']) || !$destination_planet['id_owner'])
  {
    // doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    DbFleetStatic::fleet_send_back($fleet_row);
    return CACHE_FLEET;
  }

  global $lang;
  $Message = sprintf($lang['sys_tran_mess_user'],
    $source_planet['name'], uni_render_coordinates_href($fleet_row, 'fleet_start_', 3, ''),
    $destination_planet['name'], uni_render_coordinates_href($fleet_row, 'fleet_end_', 3, ''),
    $fleet_row['fleet_resource_metal'], $lang['Metal'],
    $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
    $fleet_row['fleet_resource_deuterium'], $lang['Deuterium'] );
  msg_send_simple_message($fleet_row['fleet_target_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);

  if($fleet_row['fleet_target_owner'] <> $fleet_row['fleet_owner'])
  {
    msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
  }

  /*
    $Message = sprintf( $lang['sys_tran_mess_owner'],
                $TargetName, uni_render_coordinates_href($fleet_row, 'fleet_end_', 3, ''),
                $fleet_row['fleet_resource_metal'], $lang['Metal'],
                $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
                $fleet_row['fleet_resource_deuterium'], $lang['Deuterium'] );
    msg_send_simple_message ( $StartOwner, '', $fleet_row['fleet_start_time'], MSG_TYPE_TRANSPORT, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
  */

  return RestoreFleetToPlanet($fleet_row, false, true);
}
