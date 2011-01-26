<?php

/**
 * MissionCaseColonisation.php
 *
 * @version 1
 * @copyright 2008
 */

// ----------------------------------------------------------------------------------------------------------------
// Mission Case 9: -> Coloniser
//
function flt_mission_colonize($mission_data)
{
  global $lang;

  $fleet_row = &$mission_data['fleet'];

  $TargetAdress = sprintf ($lang['sys_adress_planet'], $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);

  $fleet_array = sys_unit_str2arr($fleet_row['fleet_array']);

  $TheMessage = $lang['sys_colo_no_colonizer'];
  if($fleet_array[208] >= 1)
  {
    $TheMessage = $lang['sys_colo_notfree'];
    //$iGalaxyPlace = doquery ("SELECT `id` FROM `{{planets}}` WHERE `galaxy` = '{$fleet_row['fleet_end_galaxy']}' AND `system` = '{$fleet_row['fleet_end_system']}' AND `planet` = '{$fleet_row['fleet_end_planet']}' AND `planet_type` = 1 LIMIT 1;", '', true);
    //$iGalaxyPlace = $destination_planet;
    if (!$mission_data['dst_planet'])
    {
      //doquery("SELECT `colonisation_tech` + 1 as tech FROM `{{users}}` WHERE `id`='{$fleet_row['fleet_owner']}' LIMIT 1;", '', true);
      //$iMaxColo = $iMaxColo['tech'];
      $iMaxColo = $mission_data['src_user']['colonisation_tech'] + 1;

      $iPlanetCount = doquery ("SELECT count(*) as `planet_count` FROM `{{planets}}` WHERE `id_owner` = '{$fleet_row['fleet_owner']}' AND `planet_type` = '1';", '', true);
      $iPlanetCount = $iPlanetCount['planet_count'];

      // Can we colonize more planets?
      $TheMessage = $lang['sys_colo_maxcolo'];
      if ($iPlanetCount < $iMaxColo && $iPlanetCount < ($GLOBALS['config']->player_max_colonies + 1) )
      {
        // Yes, we can colonize
        $TheMessage = $lang['sys_colo_badpos'];
        $NewOwnerPlanet = uni_create_planet($fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'], $fleet_row['fleet_owner'], "{$lang['sys_colo_defaultname']} ¹{$iPlanetCount}");
        if ($NewOwnerPlanet)
        {
          $TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_allisok'];
          SendSimpleMessage ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], 0, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);

          $fleet_array[208]--;
          $fleet_row['fleet_amount']--;
          $fleet_row['fleet_array'] = sys_unit_arr2str($fleet_array);

          if($fleet_row['fleet_array'])
          {
            $GLOBALS['debug']->warning("Sending several type of ships with colonizer leads to resource duplication. Resource duplicate X time where X - number of ship type<br>Fleet: {$fleet_row['fleet_array']}", 'Colonization With Fleet', 304);
          }
          return RestoreFleetToPlanet($fleet_row, false);
        }
      }
    }
  }

  doquery("UPDATE `{{fleets}}` SET `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
  SendSimpleMessage(
    $fleet_row['fleet_owner'],
    '',
    $fleet_row['fleet_start_time'],
    0,
    $lang['sys_colo_mess_from'],
    $lang['sys_colo_mess_report'],
    "{$lang['sys_colo_arrival']}{$TargetAdress}{$TheMessage}"
  );

  return CACHE_FLEET;
}

?>
