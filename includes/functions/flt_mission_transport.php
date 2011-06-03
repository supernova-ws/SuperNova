<?php

/**
 * flt_mission_transport.php
 *
 * @version 2.0 return cacher result
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_transport($mission_data)
{
  $fleet_row          = $mission_data['fleet'];
  $source_planet      = $mission_data['src_planet'];
  $destination_planet = $mission_data['dst_planet'];

  if(!$destination_planet || !is_array($destination_planet) || !$destination_planet['id_owner'])
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    return CACHE_FLEET;
  }

/*
  // flt_mission_transport
  $Message = sprintf($lang['sys_tran_mess_back'], $StartName, GetStartAdressLink($fleet_row, ''));
  SendSimpleMessage($StartOwner, '', $fleet_row['fleet_end_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_fleetback'], $Message);
*/

  global $lang;

  $StartName        = $source_planet['name'];
  $StartOwner       = $fleet_row['fleet_owner'];
  $TargetName       = $destination_planet['name'];
  $TargetOwner      = $fleet_row['fleet_target_owner'];

  $Message = sprintf( $lang['sys_tran_mess_owner'],
              $TargetName, uni_render_coordinates_href($fleet_row, 'fleet_end_', 3, ''),
              $fleet_row['fleet_resource_metal'], $lang['Metal'],
              $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
              $fleet_row['fleet_resource_deuterium'], $lang['Deuterium'] );
  msg_send_simple_message ( $StartOwner, '', $fleet_row['fleet_start_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);

  if ($TargetOwner <> $StartOwner)
  {
    $Message = sprintf( $lang['sys_tran_mess_user'],
                $StartName, uni_render_coordinates_href($fleet_row, 'fleet_start_', 3, ''),
                $TargetName, uni_render_coordinates_href($fleet_row, 'fleet_end_', 3, ''),
                $fleet_row['fleet_resource_metal'], $lang['Metal'],
                $fleet_row['fleet_resource_crystal'], $lang['Crystal'],
                $fleet_row['fleet_resource_deuterium'], $lang['Deuterium'] );
    msg_send_simple_message ( $TargetOwner, '', $fleet_row['fleet_start_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
  }

  return RestoreFleetToPlanet($fleet_row, false, true);
}

?>
