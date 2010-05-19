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
function MissionCaseColonisation ( $FleetRow ) {
  global $lang, $resource, $user;
  $iMaxColo = doquery("SELECT `colonisation_tech` + 1 FROM `{{table}}` WHERE `id`='". $FleetRow['fleet_owner']."'",'users', true);

  $iPlanetCount = doquery ("SELECT count(*) FROM `{{table}}` WHERE `id_owner` = '". $FleetRow['fleet_owner'] ."' AND `planet_type` = '1'", 'planets', true);
  if ($FleetRow['fleet_mess'] == 0) {
    // Déjà, sommes nous a l'aller ??
    $iGalaxyPlace = mysql_result(doquery ("SELECT count(*) FROM `{{table}}` WHERE `galaxy` = '". $FleetRow['fleet_end_galaxy']."' AND `system` = '". $FleetRow['fleet_end_system']."' AND `planet` = '". $FleetRow['fleet_end_planet']."'", 'galaxy'), 0);
    $TargetAdress = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
    if ($iGalaxyPlace == 0) {
      // Can we colonize more planets?
      if ($iPlanetCount[0] >= $iMaxColo[0] || $iPlanetCount[0] >= MAX_PLAYER_PLANETS) {
        // No, we can't
        $TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_maxcolo'];
        SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);
        doquery("UPDATE `{{table}}` SET `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
      } else {
        // Yes, we can colonize
        $NewOwnerPlanet = CreateOnePlanetRecord($FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $FleetRow['fleet_owner'], $lang['sys_colo_defaultname']);
        if ( $NewOwnerPlanet ) {
          $TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_allisok'];
          SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);
          // Is there any ships on fleet except Colonizer ID 208?
          if ($FleetRow['fleet_amount'] == 1) {
            // No - just deleting fleet
            doquery("DELETE FROM `{{table}}` WHERE `fleet_id` = '" . $FleetRow["fleet_id"] . "'", 'fleets');
          } else {
            // Yes - this fleet will orbit planet now
            $CurrentFleet = explode(";", $FleetRow['fleet_array']);
            $NewFleet     = "";
            foreach ($CurrentFleet as $Item => $Group) {
              if ($Group != '') {
                $Class = explode (",", $Group);
                if ($Class[0] == 208) {
                  if ($Class[1] > 1) {
                    $NewFleet  .= $Class[0].",".($Class[1] - 1).";";
                  }
                } else {
                  if ($Class[1] <> 0) {
                  $NewFleet  .= $Class[0].",".$Class[1].";";
                  }
                }
              }
            }
            $FleetRow['fleet_array'] = $NewFleet;
            $FleetRow['fleet_amount']--;
            RestoreFleetToPlanet ($FleetRow, false);

//            $QryUpdateFleet  = "UPDATE `{{table}}` SET ";
//            $QryUpdateFleet .= "`fleet_array` = '". $NewFleet ."', ";
//            $QryUpdateFleet .= "`fleet_amount` = `fleet_amount` - 1, ";
//            $QryUpdateFleet .= "`fleet_mess` = '1' ";
//            $QryUpdateFleet .= "WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';";
//            doquery( $QryUpdateFleet, 'fleets');


            doquery("DELETE FROM `{{table}}` WHERE `fleet_id` = '" . $FleetRow["fleet_id"] . "'", 'fleets');
/*
            $CurrentFleet = explode(";", $FleetRow['fleet_array']);
            $NewFleet     = "";
            foreach ($CurrentFleet as $Item => $Group) {
              if ($Group != '') {
                $Class = explode (",", $Group);
                if ($Class[0] == 208) {
                  if ($Class[1] > 1) {
                    $NewFleet  .= $Class[0].",".($Class[1] - 1).";";
                  }
                } else {
                  if ($Class[1] <> 0) {
                  $NewFleet  .= $Class[0].",".$Class[1].";";
                  }
                }
              }
            }
            $QryUpdateFleet  = "UPDATE `{{table}}` SET ";
            $QryUpdateFleet .= "`fleet_array` = '". $NewFleet ."', ";
            $QryUpdateFleet .= "`fleet_amount` = `fleet_amount` - 1, ";
            $QryUpdateFleet .= "`fleet_mess` = '1' ";
            $QryUpdateFleet .= "WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';";
            doquery( $QryUpdateFleet, 'fleets');
*/
          }
        } else {
          $TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_badpos'];
          SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);
          doquery("UPDATE `{{table}}` SET `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."'", 'fleets');
        }
      }
    } else {
      // Pas de bol coiffé sur le poteau !
      $TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_notfree'];
      SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 0, $lang['sys_colo_mess_from'], $lang['sys_colo_mess_report'], $TheMessage);
      // Mettre a jour la flotte pour qu'effectivement elle revienne !
      doquery("UPDATE `{{table}}` SET `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."'", 'fleets');

    }
  } elseif ($FleetRow['fleet_end_time'] <= time()) {
    // Retour de flotte
    RestoreFleetToPlanet ( $FleetRow, true );
    doquery("DELETE FROM `{{table}}` WHERE `fleet_id` = '" . $FleetRow["fleet_id"] . "'", 'fleets');
  }
}
?>