<?php

/**
 * MissionCaseStay.php
 *
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */


// ----------------------------------------------------------------------------------------------------------------
// Mission Case 4: -> Stationner
//
function MissionCaseStay ( $FleetRow ) {
  global $time_now;

  // --- This is universal part which should be moved to fleet manager
  // Checking if we should now to proceed this fleet - does it arrive? No - exiting.
  if ($FleetRow['fleet_start_time'] > $time_now) return;

  // Checking fleet message: if not 0 then we already managed this fleet
  if($FleetRow['fleet_mess'] != 0) {
    // Checking fleet end_time: if less then time_now then restoring fleet to planet
    if($FleetRow['fleet_end_time'] <= $time_now) {
      RestoreFleetToPlanet($FleetRow);
    }
    return;
  }

  // Using to get ownerID, lunaID from PLANETS table and list of resources
  $TargetPlanet = doquery('SELECT * FROM {{table}} WHERE ' .
         '`galaxy` = '. $FleetRow['fleet_end_galaxy'] .
    ' AND `system` = '. $FleetRow['fleet_end_system'] .
    ' AND `planet` = '. $FleetRow['fleet_end_planet'] .
    ' AND `planet_type` = '. $FleetRow['fleet_end_type'] .';',
  'planets', true);

  if (!$TargetPlanet || !isset($TargetPlanet['id'])) {
    if ($FleetRow['fleet_group'] > 0) {
      doquery("DELETE FROM {{aks}} WHERE `id` ='{$FleetRow['fleet_group']}';");
      doquery('UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_group` = ' . $FleetRow['fleet_group']);
    } else {
      doquery('UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` =' . $FleetRow['fleet_id']);
    }
    return;
  }
  // --- End of Universal part

  global $lang, $resource;

  if ($FleetRow['fleet_mess'] == 0) {
    if ($FleetRow['fleet_start_time'] <= time()) {
      $QryGetTargetPlanet   = "SELECT * FROM {{table}} ";
      $QryGetTargetPlanet  .= "WHERE ";
      $QryGetTargetPlanet  .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
      $QryGetTargetPlanet  .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
      $QryGetTargetPlanet  .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
      $QryGetTargetPlanet  .= "`planet_type` = '". $FleetRow['fleet_end_type'] ."';";
      $TargetPlanet         = doquery( $QryGetTargetPlanet, 'planets', true);
      $TargetUserID         = $TargetPlanet['id_owner'];

      $TargetAdress         = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
      $TargetAddedGoods     = sprintf ($lang['sys_stay_mess_goods'],
                        $lang['Metal'], pretty_number($FleetRow['fleet_resource_metal']),
                        $lang['Crystal'], pretty_number($FleetRow['fleet_resource_crystal']),
                        $lang['Deuterium'], pretty_number($FleetRow['fleet_resource_deuterium']));

      $TargetMessage        = $lang['sys_stay_mess_start'] ."<a href=\"galaxy.php?mode=3&galaxy=". $FleetRow['fleet_end_galaxy'] ."&system=". $FleetRow['fleet_end_system'] ."\">";
      $TargetMessage       .= $TargetAdress. "</a>". $lang['sys_stay_mess_end'] ."<br />". $TargetAddedGoods;

      SendSimpleMessage ( $TargetUserID, '', $FleetRow['fleet_start_time'], 5, $lang['sys_mess_qg'], $lang['sys_stay_mess_stay'], $TargetMessage);
      RestoreFleetToPlanet ( $FleetRow, false );
      doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
    }
  } else {
    if ($FleetRow['fleet_end_time'] <= time()) {
      $TargetAdress         = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet']);
      $TargetAddedGoods     = sprintf ($lang['sys_stay_mess_goods'],
                        $lang['Metal'], pretty_number($FleetRow['fleet_resource_metal']),
                        $lang['Crystal'], pretty_number($FleetRow['fleet_resource_crystal']),
                        $lang['Deuterium'], pretty_number($FleetRow['fleet_resource_deuterium']));

      $TargetMessage        = $lang['sys_stay_mess_back'] ."<a href=\"galaxy.php?mode=3&galaxy=". $FleetRow['fleet_start_galaxy'] ."&system=". $FleetRow['fleet_start_system'] ."\">";
      $TargetMessage       .= $TargetAdress. "</a>". $lang['sys_stay_mess_bend'] ."<br />". $TargetAddedGoods;

      SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 5, $lang['sys_mess_qg'], $lang['sys_mess_fleetback'], $TargetMessage);
      RestoreFleetToPlanet ( $FleetRow, true );
      doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
    }
  }
}

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 Mise en module initiale
// 1.1 FIX permet un retour de flotte cohérant
?>