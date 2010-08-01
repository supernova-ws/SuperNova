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
  global $resource;

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
}
?>