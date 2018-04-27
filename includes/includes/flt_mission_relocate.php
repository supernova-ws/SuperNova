<?php

// ----------------------------------------------------------------------------------------------------------------
use Fleet\DbFleetStatic;

/**
 * MissionCaseStay.php
 *
 * version 2.0 returns results for new fleet handler
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */
function flt_mission_relocate($mission_data)
{
  $fleet_row          = &$mission_data['fleet'];
  $destination_planet = &$mission_data['dst_planet'];

  if(!$destination_planet || !is_array($destination_planet) || $fleet_row['fleet_owner'] != $destination_planet['id_owner'])
  {
    // doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    DbFleetStatic::fleet_send_back($mission_data['fleet']);
    return CACHE_FLEET;
  }

  global $lang;

  $Message = sprintf($lang['sys_tran_mess_user'],
      $mission_data['src_planet']['name'], uni_render_coordinates_href($fleet_row, 'fleet_start_', 3, ''), $destination_planet['name'], uni_render_coordinates_href($fleet_row, 'fleet_end_', 3, ''),
    $fleet_row['fleet_resource_metal'], $lang['Metal'], $fleet_row['fleet_resource_crystal'], $lang['Crystal'], $fleet_row['fleet_resource_deuterium'], $lang['Deuterium']) .
  '<br />' . $lang['sys_relocate_mess_user'];
  foreach(sys_unit_str2arr($fleet_row['fleet_array']) as $ship_id => $ship_count)
  {
    $Message .= $lang['tech'][$ship_id] . ' - ' . $ship_count . '<br />';
  }
  msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_TRANSPORT, $lang['sys_mess_qg'], $lang['sys_stay_mess_stay'], $Message);

  return RestoreFleetToPlanet($fleet_row, false);
}
