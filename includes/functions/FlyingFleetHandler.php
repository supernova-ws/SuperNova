<?php

// Modified by MadnessRed to support ACS

/**
 * FlyingFleetHandler.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function FlyingFleetHandler (&$planet) {
  global $resource, $dbg_msg, $time_now;
  // $dbg_msg .= "&nbsp;&nbsp;FleetHandler for planet ".PrintPlanetCoords($planet)."<br />";
  $process_delay = 10; //seconds

  doquery("LOCK TABLE {{table}}aks WRITE, {{table}}lunas WRITE, {{table}}rw WRITE, {{table}}errors WRITE, {{table}}messages WRITE, {{table}}fleets WRITE, {{table}}planets WRITE, {{table}}galaxy WRITE ,{{table}}users WRITE", "");

  $QryFleet   = "SELECT * FROM `{{table}}` ";
  $QryFleet  .= "WHERE (";
  $QryFleet  .= "( ";
  $QryFleet  .= "`fleet_start_galaxy` = ". $planet['galaxy']      ." AND ";
  $QryFleet  .= "`fleet_start_system` = ". $planet['system']      ." AND ";
  $QryFleet  .= "`fleet_start_planet` = ". $planet['planet']      ." AND ";
  $QryFleet  .= "`fleet_start_type` = ".   $planet['planet_type'] ." ";
  $QryFleet  .= ") OR ( ";
  $QryFleet  .= "`fleet_end_galaxy` = ".   $planet['galaxy']      ." AND ";
  $QryFleet  .= "`fleet_end_system` = ".   $planet['system']      ." AND ";
  $QryFleet  .= "`fleet_end_planet` = ".   $planet['planet']      ." ) AND ";
  $QryFleet  .= "`fleet_end_type`= ".      $planet['planet_type'] ." ) AND ";
  $QryFleet  .= "( `fleet_start_time` <= '". $time_now ."' OR `fleet_end_time` <= '". $time_now ."' )";

  $fleetquery = doquery( $QryFleet, 'fleets' );

  while ($CurrentFleet = mysql_fetch_array($fleetquery)) {
    //$dbg_msg .= "&nbsp;&nbsp;Fleet ".$CurrentFleet['fleet_id'].". Proctime up to ".($time_now-$process_delay)."<br />";
    //$QryFleet   = "UPDATE `{{table}}` SET `processing_start`=".$time_now.";";
    //doquery($QryFleet, 'fleets' );

    // $dbg_msg .= "&nbsp;&nbsp;Fleet ".$CurrentFleet['fleet_id']. " Case: ".$CurrentFleet["fleet_mission"]."<br />";
    switch ($CurrentFleet["fleet_mission"]) {
      case MT_ATTACK:
        // Attaquer
        MissionCaseAttack ( $CurrentFleet );
        break;

      case MT_AKS:
        // Attaque groupée
        MissionCaseAttack ( $CurrentFleet );
        break 2;

      case MT_TRANSPORT:
        // Transporter
        MissionCaseTransport ( $CurrentFleet );
        break;

      case MT_RELOCATE:
        // Stationner
        MissionCaseStay ( $CurrentFleet );
        break;

      case MT_HOLD:
        // Stationner chez un Allié
        MissionCaseACS ( $CurrentFleet );
        break;

      case MT_SPY:
        // Flotte d'espionnage
        MissionCaseSpy ( $CurrentFleet );
        break;

      case MT_COLONIZE:
        // Coloniser
        MissionCaseColonisation ( $CurrentFleet );
        break;

      case MT_RECYCLE:
        // Recyclage
        MissionCaseRecycling ( $CurrentFleet );
        break;

      case MT_DESTROY:
        // Detruire ??? dans le code ogame c'est 9 !!
        MissionCaseDestruction ( $CurrentFleet );
        break;

      case 10:
        // Missiles !!

        break;

      case MT_EXPLORE:
        // Expeditions
        MissionCaseExpedition ( $CurrentFleet );
        break;

      default: {
        // $dbg_msg .= "&nbsp;&nbsp;Fleet ".$CurrentFleet['fleet_id'].". Default Delete<br />";
        doquery("DELETE FROM `{{table}}` WHERE `fleet_id` = '". $CurrentFleet['fleet_id'] ."';", 'fleets');
      }
    }
  }

  // $dbg_msg .= "&nbsp;&nbsp;Fleet ".$CurrentFleet['fleet_id'].". EndOf FleetHandler<br />";
  doquery("UNLOCK TABLES", "");
}

?>
