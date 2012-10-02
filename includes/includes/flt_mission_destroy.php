<?php

require_once('includes/includes/flt_mission_attack.php');

/*
 * Partial copyright (c) 2009 by Gorlum for http://supernova.ws

Based on original code:
#############################################################################
#  Filename: MissionCaseDestruction.php
#  Create date: Saturday, April 05, 2008    15:51:35
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/
function flt_mission_destroy($mission_data)
{
  $combat_data = flt_mission_attack($mission_data, false);

  if(empty($combat_data) || $combat_data[UBE_OUTCOME][UBE_COMBAT_RESULT] != UBE_COMBAT_RESULT_WIN)
  {
    return $combat_data;
  }

  $fleet_row          = $mission_data['fleet'];
  $destination_planet = $mission_data['dst_planet'];
  if(!$destination_planet || !is_array($destination_planet) || $destination_planet['planet_type'] != PT_MOON)
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    return $combat_data;
  }

//  sn_ube_combat_analyze_moon_destroy($combat_data);

  sn_ube_report_save($combat_data);
  $combat_data = sn_ube_report_load($combat_data[UBE_REPORT_CYPHER]);
doquery('COMMIT');
//debug($combat_data);

doquery('START TRANSACTION');
  sn_ube_combat_result_apply($combat_data);
doquery('ROLLBACK');

  sn_ube_message_send($combat_data);

//      $combat_data[UBE_OUTCOME][UBE_MOON] = UBE_MOON_DESTROY_SUCCESS;
  global $template_result;
  sn_ube_report_generate($combat_data, $template_result);
  $template = gettemplate('ube_combat_report', true);
  $template->assign_recursive($template_result);
  display($template, '', false, '', false, false, true);
  die();
//      $combat_data[UBE_OUTCOME][UBE_MOON] = UBE_MOON_DESTROY_FAILED;
//      $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS] = UBE_MOON_REAPERS_NONE;
//      $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS] = UBE_MOON_REAPERS_DIED;
//      $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS] = UBE_MOON_REAPERS_RETURNED;
//      $combat_data[UBE_OUTCOME][UBE_MOON_DESTROY_CHANCE] = min(99, round((100 - sqrt($moon_size)) * sqrt($reapers)));
//      $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS_DIE_CHANCE] = round(sqrt($moon_size) / 2);
//      $combat_data[UBE_OUTCOME][UBE_PLANET][PLANET_SIZE]

  return $combat_data;
}

?>
