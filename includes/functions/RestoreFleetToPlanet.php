<?php

/**
 * RestoreFleetToPlanet.php
 *
 * @version 1.0
 * @copyright 2008 Chlorel for XNova
 */

// RestoreFleetToPlanet
//
// $FleetRow -> enregistrement de flotte
// $Start    -> true  = planete de depart
//           -> false = planete d'arrivée
function RestoreFleetToPlanet ( &$FleetRow, $Start = true ) {
  global $resource, $debug;

  $QryUpdatePlanet = "UPDATE {{table}} SET ";

  $FleetRecord = explode(";", $FleetRow['fleet_array']);
  foreach ($FleetRecord as $Item => $Group) {
    if ($Group != '') {
      $Class = explode (",", $Group);
      $QryUpdatePlanet .= "`". $resource[$Class[0]] ."` = `".$resource[$Class[0]]."` + '".$Class[1]."', ";
    }
  }

  $QryUpdatePlanet  .= "`metal` = `metal` + '". $FleetRow['fleet_resource_metal'] ."', ";
  $QryUpdatePlanet  .= "`crystal` = `crystal` + '". $FleetRow['fleet_resource_crystal'] ."', ";
  $QryUpdatePlanet  .= "`deuterium` = `deuterium` + '". $FleetRow['fleet_resource_deuterium'] ."' ";

  $QryPart  = " WHERE ";
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

  doquery( $QryUpdatePlanet, 'planets');

  doquery( "DELETE FROM {{fleets}} WHERE `fleet_id`=".$FleetRow['fleet_id'].";");

  // $qry = 'select metal, crystal, deuterium from {{table}} ' . $QryPart;
  //  $q_before = doquery( $qry, 'planets', true);
  // $q_after = doquery( $qry, 'planets', true);
  // $d_m  = 'Fleet ' . $FleetRow['fleet_id'] . ': ';
  // if ($FleetRow['fleet_resource_metal']+$FleetRow['fleet_resource_crystal']+$FleetRow['fleet_resource_deuterium']==0){
  //   $d_m .= 'No resources on fleet \n<br>';
  // }else{
  //   $d_m .= 'Before: ' . round($q_before['metal']) .'/'. round($q_before['crystal']) .'/'. round($q_before['deuterium']) . '\n<br>';
  //   $d_m .= 'After: ' . round($q_after['metal']) .'/'. round($q_after['crystal']) .'/'. round($q_after['deuterium']) . '\n<br>';
  //   $d_m .= 'Should be: ' . round($q_before['metal']+$FleetRow['fleet_resource_metal']) .'/'. round($q_before['crystal']+$FleetRow['fleet_resource_crystal']) .'/'. round($q_before['deuterium']+$FleetRow['fleet_resource_deuterium']) . '\n<br>';
  // };
  // $d_m .= $QryUpdatePlanet . '\n<br>';
  // $debug->warning($d_m,'Fleet Landed');
}
?>