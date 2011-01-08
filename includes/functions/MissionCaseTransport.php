<?php

/**
 * MissionCaseTransport.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

/**
 * StoreGoodsToPlanet
 *
 * @version 1
 * @copyright 2008
 */

function StoreGoodsToPlanet ( $FleetRow, $Start = false ) {
  global $resource;

  $QryUpdatePlanet   = "UPDATE {{table}} SET ";
  $QryUpdatePlanet  .= "`metal` = `metal` + '". $FleetRow['fleet_resource_metal'] ."', ";
  $QryUpdatePlanet  .= "`crystal` = `crystal` + '". $FleetRow['fleet_resource_crystal'] ."', ";
  $QryUpdatePlanet  .= "`deuterium` = `deuterium` + '". $FleetRow['fleet_resource_deuterium'] ."' ";

  $QryPart = "WHERE ";
  if ($Start == true) {
    $QryPart .= "`galaxy` = '". $FleetRow['fleet_start_galaxy'] ."' AND ";
    $QryPart .= "`system` = '". $FleetRow['fleet_start_system'] ."' AND ";
    $QryPart .= "`planet` = '". $FleetRow['fleet_start_planet'] ."' AND ";
    $QryPart .= "`planet_type` = '". $FleetRow['fleet_start_type'] ."' ";
  } else {
    $QryPart .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
    $QryPart .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
    $QryPart .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
    $QryPart .= "`planet_type` = '". $FleetRow['fleet_end_type'] ."' ";
  }
  $QryUpdatePlanet .= $QryPart;
  $QryUpdatePlanet .= "LIMIT 1;";

  $qry = 'select metal, crystal, deuterium from {{table}} ' . $QryPart;
  $q_before = doquery( $qry, 'planets', true);

  doquery( $QryUpdatePlanet, 'planets');
}

function MissionCaseTransport ( $FleetRow ) {
  global $lang, $time_now, $dbg_msg;
  // $time_now = time();

  $QryStartPlanet   = "SELECT * FROM {{table}} ";
  $QryStartPlanet  .= "WHERE ";
  $QryStartPlanet  .= "`galaxy` = '". $FleetRow['fleet_start_galaxy'] ."' AND ";
  $QryStartPlanet  .= "`system` = '". $FleetRow['fleet_start_system'] ."' AND ";
  $QryStartPlanet  .= "`planet` = '". $FleetRow['fleet_start_planet'] ."' AND ";
  $QryStartPlanet  .= "`planet_type` = '". $FleetRow['fleet_start_type'] ."';";
  $StartPlanet      = doquery( $QryStartPlanet, 'planets', true);
  $StartName        = $StartPlanet['name'];
  $StartOwner       = $StartPlanet['id_owner'];

  $QryTargetPlanet  = "SELECT * FROM {{table}} ";
  $QryTargetPlanet .= "WHERE ";
  $QryTargetPlanet .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
  $QryTargetPlanet .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
  $QryTargetPlanet .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
  $QryTargetPlanet .= "`planet_type` = '". $FleetRow['fleet_end_type'] ."';";
  $TargetPlanet     = doquery( $QryTargetPlanet, 'planets', true);
  $TargetName       = $TargetPlanet['name'];
  $TargetOwner      = $TargetPlanet['id_owner'];

  /*
  $dbg_msg .= "&nbsp;&nbsp;&nbsp;Fleet ".$FleetRow['fleet_id'].". MissionCaseTransport. fleet_mess=".$FleetRow['fleet_mess'].
  ' fleet_start_time='.$FleetRow['fleet_start_time'].
  ' time_now='.$time_now.
  '<br />';
  */

  if ($FleetRow['fleet_mess'] == 0) {
    if ($FleetRow['fleet_start_time'] < $time_now) {
      StoreGoodsToPlanet ($FleetRow, false);
      $Message         = sprintf( $lang['sys_tran_mess_owner'],
                  $TargetName, GetTargetAdressLink($FleetRow, ''),
                  $FleetRow['fleet_resource_metal'], $lang['Metal'],
                  $FleetRow['fleet_resource_crystal'], $lang['Crystal'],
                  $FleetRow['fleet_resource_deuterium'], $lang['Deuterium'] );

      SendSimpleMessage ( $StartOwner, '', $FleetRow['fleet_start_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
      if ($TargetOwner <> $StartOwner) {
        $Message         = sprintf( $lang['sys_tran_mess_user'],
                    $StartName, GetStartAdressLink($FleetRow, ''),
                    $TargetName, GetTargetAdressLink($FleetRow, ''),
                    $FleetRow['fleet_resource_metal'], $lang['Metal'],
                    $FleetRow['fleet_resource_crystal'], $lang['Crystal'],
                    $FleetRow['fleet_resource_deuterium'], $lang['Deuterium'] );
        SendSimpleMessage ( $TargetOwner, '', $FleetRow['fleet_start_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_transport'], $Message);
      }

      $QryUpdateFleet  = "UPDATE {{table}} SET ";
      $QryUpdateFleet .= "`fleet_resource_metal` = '0' , ";
      $QryUpdateFleet .= "`fleet_resource_crystal` = '0' , ";
      $QryUpdateFleet .= "`fleet_resource_deuterium` = '0' , ";
      $QryUpdateFleet .= "`fleet_mess` = '1' ";
      $QryUpdateFleet .= "WHERE `fleet_id` = '". $FleetRow['fleet_id'] ."' ";
      $QryUpdateFleet .= "LIMIT 1 ;";
      doquery( $QryUpdateFleet, 'fleets');
    }
  } else {
    if ($FleetRow['fleet_end_time'] < $time_now) {
      $Message             = sprintf ($lang['sys_tran_mess_back'], $StartName, GetStartAdressLink($FleetRow, ''));
      SendSimpleMessage ( $StartOwner, '', $FleetRow['fleet_end_time'], 5, $lang['sys_mess_tower'], $lang['sys_mess_fleetback'], $Message);
      RestoreFleetToPlanet ( $FleetRow, true );
      doquery("DELETE FROM {{table}} WHERE fleet_id=" . $FleetRow["fleet_id"], 'fleets');
    }
  }
}

// -----------------------------------------------------------------------------------------------------------
// History version
?>