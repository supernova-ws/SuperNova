<?php

/**
 * PlanetResourceUpdate.php
 *
 * 2.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Bit more optimization
 * 2.0 - copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *     [+] Full rewrote and optimization
 * 1.1 - @copyright 2008 By Chlorel for XNova
 *     [*] Mise a jour automatique mines / silos / energie ...
 * 1.0 - @copyright 2008 By Chlorel for XNova
 *     [*] Mise en module initiale
 */

function PlanetResourceUpdate ( $CurrentUser, &$CurrentPlanet, $UpdateTime, $Simul = false ) {
  global $resource, $debug;

  $incRes = array ('metal' => 0, 'crystal' => 0, 'deuterium' => 0); // Zero increment to each type of resources

  $ProductionTime               = ($UpdateTime - $CurrentPlanet['last_update']); // How much time passes since last update
  $CurrentPlanet['last_update'] = $UpdateTime;

  // Yes
  $Caps = ECO_getPlanetCaps($CurrentUser, $CurrentPlanet); // calculating current resource production data

  if ($CurrentPlanet['planet_type'] == 1) { // Resource calculation for planet
    foreach($incRes as $resName => &$incCount){ // Now, for each type of resource we...
      // ...calculating resource increase (may be negative if resource amount less then storage capacity)
      $incCount = ECO_calcResourceIncrease($Caps, $resName, $ProductionTime);

      if($CurrentPlanet[$resName]<0){ // correction for negative resources if any
        // $incCount -= $CurrentPlanet[$resName];
        $debug->warning('Player have negative resources on '.$CurrentPlanet['galaxy'].':'.$CurrentPlanet['system'].':'.$CurrentPlanet['planet'].'. Difference '.$CurrentPlanet[$resName].' of '.$resName, 'Negative Resources', 500);
      }
      // ...changing data in $Caps according to resource increase
      $Caps['planet'][$resName] += $incCount;
      // ...calculating total planet production per hour - old one counts only units (buildings and fleet ones)
      $Caps['planet'][$resName.'_perhour'] = $Caps['real'][$resName.'_perhour'];
    }
  } elseif ($CurrentPlanet['planet_type'] == 3) {
    // Yes - no production on moon
    $CurrentPlanet['metal_perhour']        = 0;
    $CurrentPlanet['crystal_perhour']      = 0;
    $CurrentPlanet['deuterium_perhour']    = 0;
    $CurrentPlanet['energy_used']          = 0;
    $CurrentPlanet['energy_max']           = 0;
  }
  $CurrentPlanet = array_merge($CurrentPlanet, $Caps['planet']); // replacing old planet data with newly calculated ones

  if (!$Simul) {
    // Now checking fleet/defense production
    $Builded          = HandleElementBuildingQueue ( $CurrentUser, $CurrentPlanet, $ProductionTime );

    // Query to update planet data in DB
    $QryUpdatePlanet  = "UPDATE {{planets}} SET ";
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
          // Adding those elements which was built
          $QryUpdatePlanet .= "`". $resource[$Element] ."` = `". $resource[$Element] ."` + '". $Count ."', ";
        }
      }
    }
    $QryUpdatePlanet .= "`b_hangar` = '". $CurrentPlanet['b_hangar'] ."' ";
    $QryUpdatePlanet .= "WHERE ";
    $QryUpdatePlanet .= "`id` = '". $CurrentPlanet['id'] ."';";

    // doquery("LOCK TABLE {{table}} WRITE", 'planets');
    doquery($QryUpdatePlanet);
    // doquery("UNLOCK TABLES", '');
  }
}
?>