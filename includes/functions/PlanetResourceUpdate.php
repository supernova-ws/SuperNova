<?php

/**
 * PlanetResourceUpdate.php
 *
 * @version 2.0
 * @copyright 2008 By Chlorel for XNova
 * @copyright 2009 By Gorlum for http://ogame.triolan.com.ua
 */

function PlanetResourceUpdate ( $CurrentUser, &$CurrentPlanet, $UpdateTime, $Simul = false ) {
  global $ProdGrid, $resource, $reslist, $game_config, $debug;

  $resList = array ('metal', 'crystal', 'deuterium'); // Just names of the resources

  $incRes = array ('metal' => 0, 'crystal' => 0, 'deuterium' => 0); // Zero increment to each type of resources

  $ProductionTime               = ($UpdateTime - $CurrentPlanet['last_update']); // How much time passes since last update
  $CurrentPlanet['last_update'] = $UpdateTime;

  // Yes
  $Caps = ECO_getPlanetCaps($CurrentUser, $CurrentPlanet); // calculating current resource production data

  if ($CurrentPlanet['planet_type'] != 3) { // Is it a moon?

    foreach($resList as $resName){ // Now, for each type of resource we...
      // ...calculating resource increase (may be negative if resource amount less then storage capacity)
      $incRes[$resName] = ECO_calcResourceIncrease($Caps, $resName, $ProductionTime);

      if($CurrentPlanet[$resName]<0){ // correction for negative resources if any
        // $incRes[$resName] -= $CurrentPlanet[$resName];
        $debug->warning('Player have negative resources on '.$CurrentPlanet['galaxy'].':'.$CurrentPlanet['system'].':'.$CurrentPlanet['planet'].'. Compensating '.$CurrentPlanet[$resName].' of '.$resName, 'Negative Resources', 500);
      }
      // ...changing data in $Caps according to resource increase
      $Caps['planet'][$resName] += $incRes[$resName];
      // ...calculating total planet production per hour - old one counts only units (buildings and fleet ones)
      $Caps['planet'][$resName.'_perhour'] = array_sum($Caps[$resName.'_perhour']);
    }

    /*
    foreach($Caps['planet'] as $fieldName => $fieldValue){
      $CurrentPlanet[$fieldName] = $fieldValue;
    }
    */

  } else {
    // Yes - no production on moon
    $CurrentPlanet['metal_perhour']        = 0;
    $CurrentPlanet['crystal_perhour']      = 0;
    $CurrentPlanet['deuterium_perhour']    = 0;
    $CurrentPlanet['energy_used']          = 0;
    $CurrentPlanet['energy_max']           = 0;
  }
  $CurrentPlanet = array_merge($CurrentPlanet, $Caps['planet']); // replacing old planet data with newly calculated ones

  $Simul = false;
  if (!$Simul) {
    // Now checking fleet/defense production
    $Builded          = HandleElementBuildingQueue ( $CurrentUser, $CurrentPlanet, $ProductionTime );

    // Query to update planet data in DB
    $QryUpdatePlanet  = "UPDATE {{table}} SET ";
    $QryUpdatePlanet .= "`last_update` = '"      . $CurrentPlanet['last_update']         ."', ";
    // Applying resource changes
    $QryUpdatePlanet .= "`metal`     = `metal`     + '". floatval($incRes['metal']     ) ."', ";
    $QryUpdatePlanet .= "`crystal`   = `crystal`   + '". floatval($incRes['crystal']   ) ."', ";
    $QryUpdatePlanet .= "`deuterium` = `deuterium` + '". floatval($incRes['deuterium'] ) ."', ";
    // Updating production per hour info
    $QryUpdatePlanet .= "`metal_perhour` = '"    . $CurrentPlanet['metal_perhour']       ."', ";
    $QryUpdatePlanet .= "`crystal_perhour` = '"  . $CurrentPlanet['crystal_perhour']     ."', ";
    $QryUpdatePlanet .= "`deuterium_perhour` = '". $CurrentPlanet['deuterium_perhour']   ."', ";
    // Updating energy balance
    $QryUpdatePlanet .= "`energy_used` = '"      . $CurrentPlanet['energy_used']        ."', ";
    $QryUpdatePlanet .= "`energy_max` = '"       . $CurrentPlanet['energy_max']         ."', ";

    // Now applying changes that happens with build que
    $QryUpdatePlanet .= "`b_hangar_id` = '"      . $CurrentPlanet['b_hangar_id']         ."', ";
    if ( $Builded != '' ) {
      foreach ( $Builded as $Element => $Count ) {
        if ($Element <> '') {
//          $QryUpdatePlanet .= "`". $resource[$Element] ."` = '". $CurrentPlanet[$resource[$Element]] ."', "; // BAH! Bad code, bad!
          // Adding those elements which was built
          $QryUpdatePlanet .= "`". $resource[$Element] ."` = `". $resource[$Element] ."` + '". $Count ."', ";
        }
      }
    }
    $QryUpdatePlanet .= "`b_hangar` = '". $CurrentPlanet['b_hangar'] ."' ";
    $QryUpdatePlanet .= "WHERE ";
    $QryUpdatePlanet .= "`id` = '". $CurrentPlanet['id'] ."';";

    // doquery("LOCK TABLE {{table}} WRITE", 'planets');
    doquery($QryUpdatePlanet, 'planets');
    // doquery("UNLOCK TABLES", '');
  }
}

// Revision History
// - 1.0 Mise en module initiale
// - 1.1 Mise a jour automatique mines / silos / energie ...
// - 2.0 Full rewrite and optimization by Gorlum


?>