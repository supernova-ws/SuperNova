<?php

use Fleet\DbFleetStatic;

/**
 * MissionCaseColonisation.php
 *
 * @version 1
 * @copyright 2008
 */

// ----------------------------------------------------------------------------------------------------------------
// Mission Case 9: -> Coloniser
//
function flt_mission_colonize(&$mission_data)
{
  $fleet_row          = &$mission_data['fleet'];
  $src_user_row       = &$mission_data['src_user'];

  global $lang;

  $TargetAdress = sprintf ($lang['sys_adress_planet'], $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);

  $fleet_array = sys_unit_str2arr($fleet_row['fleet_array']);

  $TheMessage = $lang['sys_colo_no_colonizer'];
  if($fleet_array[SHIP_COLONIZER] >= 1)
  {
    $TheMessage = $lang['sys_colo_notfree'];
    if (!$mission_data['dst_planet'] || empty($mission_data['dst_planet']))
    {
      $iPlanetCount = get_player_current_colonies($src_user_row);

      // Can we colonize more planets?
      $TheMessage = $lang['sys_colo_maxcolo'];
      if($iPlanetCount < get_player_max_colonies($src_user_row))
      {
        // Yes, we can colonize
        $TheMessage = $lang['sys_colo_badpos'];
        $NewOwnerPlanet = uni_create_planet(
          $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'],
          $fleet_row['fleet_owner'], "{$lang['sys_colo_defaultname']} {$iPlanetCount}", false,
          array('user_row' => $src_user_row));
        if ($NewOwnerPlanet)
        {
          $TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_allisok'];
          msg_send_simple_message ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);

          $fleet_array[SHIP_COLONIZER]--;
          $fleet_row['fleet_amount']--;
          $fleet_row['fleet_array'] = sys_unit_arr2str($fleet_array);

          return RestoreFleetToPlanet($fleet_row, false);
        }
      }
    }
  }

//  doquery("UPDATE `{{fleets}}` SET `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
  DbFleetStatic::fleet_send_back($fleet_row);
  msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], "{$lang['sys_colo_arrival']}{$TargetAdress}{$TheMessage}");

  return CACHE_FLEET;
}
