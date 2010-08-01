<?php

/**
 * StoreGoodsToPlanet.php
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
?>