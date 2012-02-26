<?php

/**
 * MissionCaseStay.php
 *
 * version 2.0 returns results for new fleet handler
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */


// ----------------------------------------------------------------------------------------------------------------
// Mission Case 4: -> Stationner
//
function flt_mission_relocate($mission_data)
{
  $fleet_row          = $mission_data['fleet'];
  $destination_planet = $mission_data['dst_planet'];

  if(!$destination_planet || !is_array($destination_planet))
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    return CACHE_FLEET;
  }

/*
    // flt_mission_relocate
    $TargetAdress         = sprintf ($lang['sys_adress_planet'], $fleet_row['fleet_start_galaxy'], $fleet_row['fleet_start_system'], $fleet_row['fleet_start_planet']);
    $TargetAddedGoods     = sprintf ($lang['sys_stay_mess_goods'],
                      $lang['Metal'], pretty_number($fleet_row['fleet_resource_metal']),
                      $lang['Crystal'], pretty_number($fleet_row['fleet_resource_crystal']),
                      $lang['Deuterium'], pretty_number($fleet_row['fleet_resource_deuterium']));

    $TargetMessage        = $lang['sys_stay_mess_back'] ."<a href=\"galaxy.php?mode=3&galaxy=". $fleet_row['fleet_start_galaxy'] ."&system=". $fleet_row['fleet_start_system'] ."\">";
    $TargetMessage       .= $TargetAdress. "</a>". $lang['sys_stay_mess_bend'] ."<br />". $TargetAddedGoods;

    SendSimpleMessage ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_end_time'], 5, $lang['sys_mess_qg'], $lang['sys_mess_fleetback'], $TargetMessage);
*/

  global $lang;

  $TargetUserID         = $destination_planet['id_owner'];

  $TargetAdress         = sprintf ($lang['sys_adress_planet'], $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);
  $TargetAddedGoods     = sprintf ($lang['sys_stay_mess_goods'],
                    $lang['Metal'], pretty_number($fleet_row['fleet_resource_metal']),
                    $lang['Crystal'], pretty_number($fleet_row['fleet_resource_crystal']),
                    $lang['Deuterium'], pretty_number($fleet_row['fleet_resource_deuterium']));

  $TargetMessage        = $lang['sys_stay_mess_start'] ."<a href=\"galaxy.php?mode=3&galaxy=". $fleet_row['fleet_end_galaxy'] ."&system=". $fleet_row['fleet_end_system'] ."\">";
  $TargetMessage       .= $TargetAdress. "</a>". $lang['sys_stay_mess_end'] ."<br />". $TargetAddedGoods;

  msg_send_simple_message ( $TargetUserID, '', $fleet_row['fleet_start_time'], MSG_TYPE_TRANSPORT, $lang['sys_mess_qg'], $lang['sys_stay_mess_stay'], $TargetMessage);
  return RestoreFleetToPlanet ($fleet_row, false);
}

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 Mise en module initiale
// 1.1 FIX permet un retour de flotte cohérant

?>
