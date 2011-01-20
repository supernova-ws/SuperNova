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

function ECO_calcResourceIncrease(&$Caps, $strResource, $ProductionTime){
  $Caps['planet'][$strResource] = max(0, $Caps['planet'][$strResource]);
  $resourceIncrease = ($Caps[$strResource.'_perhour'][0] + $Caps['planet'][$strResource.'_perhour'] * $Caps['production']) * $ProductionTime / 3600 ;

  if ( ($Caps['planet'][$strResource] + $resourceIncrease) > $Caps['planet'][$strResource.'_max'] ) {
    // $resourceIncrease = $Caps['planet'][$strResource.'_max'] - $Caps['planet'][$strResource]; // Drop resource above storage limit
    $resourceIncrease = max(0, $Caps['planet'][$strResource.'_max'] - $Caps['planet'][$strResource]); // Don't drop resource above storage limit
  };

  return $resourceIncrease;
}

function PlanetResourceUpdate ( $user, &$planet, $UpdateTime, $Simul = false ) {
  global $resource, $debug, $sn_data;

  doquery('START TRANSACTION;');

  $ProductionTime        = ($UpdateTime - $planet['last_update']); // How much time passes since last update
  $planet['last_update'] = $UpdateTime;

  $Caps = ECO_getPlanetCaps($user, $planet); // calculating current resource production data
  $incRes = array ('metal' => 0, 'crystal' => 0, 'deuterium' => 0); // Zero increment to each type of resources

  if ($planet['planet_type'] == PT_PLANET) { // Resource calculation for planet
    foreach($incRes as $resName => &$incCount){ // Now, for each type of resource we...
      // ...calculating resource increase (may be negative if resource amount less then storage capacity)
      $incCount = ECO_calcResourceIncrease($Caps, $resName, $ProductionTime);

      if($planet[$resName]<0){ // correction for negative resources if any
        $debug->warning('Player have negative resources on '.$planet['galaxy'].':'.$planet['system'].':'.$planet['planet'].'. Difference '.$planet[$resName].' of '.$resName, 'Negative Resources', 500);
      }
      // ...changing data in $Caps according to resource increase
      $Caps['planet'][$resName] += $incCount;
      // ...calculating total planet production per hour - old one counts only units (buildings and fleet ones)
      $Caps['planet'][$resName.'_perhour'] = $Caps['real'][$resName.'_perhour'];
    }
  } elseif ($planet['planet_type'] == PT_MOON) {
    // Yes - no production on moon
    $planet['metal_perhour']        = 0;
    $planet['crystal_perhour']      = 0;
    $planet['deuterium_perhour']    = 0;
    $planet['energy_used']          = 0;
    $planet['energy_max']           = 0;
  }
  $planet = array_merge($planet, $Caps['planet']); // replacing old planet data with newly calculated ones

  $que = eco_que_process($user, $planet, $ProductionTime);

  if (!$Simul) {
    // Query to update planet data in DB
    $QryUpdatePlanet  = "UPDATE {{planets}} SET ";
    $QryUpdatePlanet .= "`last_update` = '{$planet['last_update']}', ";

    // Applying resource changes
    $QryUpdatePlanet .= "`metal`     = `metal`     + '{$incRes['metal']}', ";
    $QryUpdatePlanet .= "`crystal`   = `crystal`   + '{$incRes['crystal']}', ";
    $QryUpdatePlanet .= "`deuterium` = `deuterium` + '{$incRes['deuterium']}', ";

    // Updating production per hour information
    $QryUpdatePlanet .= "`metal_perhour` = '{$planet['metal_perhour']}', ";
    $QryUpdatePlanet .= "`crystal_perhour` = '{$planet['crystal_perhour']}', ";
    $QryUpdatePlanet .= "`deuterium_perhour` = '{$planet['deuterium_perhour']}', ";

    // Updating energy balance
    $QryUpdatePlanet .= "`energy_used` = '{$planet['energy_used']}', ";
    $QryUpdatePlanet .= "`energy_max` = '{$planet['energy_max']}', ";

    // Now checking fleet/defense production
    $Builded = eco_bld_handle_que($user, $planet, $ProductionTime);
    // Now applying changes that happens with build que
    $QryUpdatePlanet .= "`b_hangar_id` = '{$planet['b_hangar_id']}', ";
    if ( $Builded != '' ) {
      foreach ( $Builded as $Element => $Count ) {
        if ($Element <> '') {
          // Adding those elements which was built
          $QryUpdatePlanet .= "`{$resource[$Element]}` = `{$resource[$Element]}` + '{$Count}', ";
        }
      }
    }
    $QryUpdatePlanet .= "`b_hangar` = '{$planet['b_hangar']}'";

    $QryUpdatePlanet .= $que['query'] != $planet['que'] ? ",{$que['query']} " : '';

    $QryUpdatePlanet .= "WHERE `id` = '{$planet['id']}' LIMIT 1;";

    // doquery("LOCK TABLE {{planets}} WRITE;");
    doquery($QryUpdatePlanet);
    // doquery("UNLOCK TABLES");

    if(!empty($que['xp']))
    {
      foreach($que['xp'] as $xp_type => $xp_amount)
      {
        rpg_level_up($user, $xp_type, $xp_amount);
      }
    }
  }

  doquery('COMMIT;');

  return $que;
}

?>
