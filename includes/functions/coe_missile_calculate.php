<?php

/**
 * Copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *       OpenSource as long as you don't remove this Copyright
 * V3 2009-11-13
 * V2 2009-10-10
 */

function COE_missileCalculate(){
  global $time_now, $resource, $lang;

  $iraks = doquery("SELECT * FROM {{table}} WHERE `zeit` <= '" . $time_now . "'", 'iraks');

  while ($fleetRow = mysql_fetch_array($iraks)) {

    $targetPlanet = doquery("SELECT * FROM `{{table}}` WHERE
              `galaxy` = '{$fleetRow['galaxy']}' AND
              `system` = '{$fleetRow['system']}' AND
              `planet` = '{$fleetRow['planet']}' AND
              `planet_type` = " . PT_PLANET, 'planets', true);
    $targetUser    = doquery('SELECT * FROM {{table}} WHERE `id` = '.$targetPlanet['id_owner'], 'users', true);
    PlanetResourceUpdate( $targetUser, $targetPlanet, $time_now );

    $rowAttacker = doquery("SELECT `military_tech` FROM `{{table}}` WHERE
              `id` = '{$fleetRow['owner']}'", 'users', true);

    if ($targetPlanet['id']) {
      $planetDefense = array(
        400 => array( 0, 'shield' => 0, 'structure' => 0),
        401 => array( $targetPlanet[$resource[401]], 'shield' => 0, 'structure' => 0),
        402 => array( $targetPlanet[$resource[402]], 'shield' => 0, 'structure' => 0),
        403 => array( $targetPlanet[$resource[403]], 'shield' => 0, 'structure' => 0),
        404 => array( $targetPlanet[$resource[404]], 'shield' => 0, 'structure' => 0),
        405 => array( $targetPlanet[$resource[405]], 'shield' => 0, 'structure' => 0),
        406 => array( $targetPlanet[$resource[406]], 'shield' => 0, 'structure' => 0),
        407 => array( $targetPlanet[$resource[407]], 'shield' => 0, 'structure' => 0),
        408 => array( $targetPlanet[$resource[408]], 'shield' => 0, 'structure' => 0),
        409 => array( $targetPlanet[$resource[409]], 'shield' => 0, 'structure' => 0),
      );

      $message = '';
      $interceptors = $targetPlanet[$resource[502]]; // Number of interceptors
      $missiles = $fleetRow['anzahl']; // Number of MIP
      $qUpdate = "UPDATE `{{table}}` SET {$resource[502]} = ";
      if ($interceptors >= $missiles) {
        $message = $lang['mip_all_destroyed'];
        $qUpdate .= "{$resource[502]} - {$missiles} ";
      } else {
        if ($interceptors) {
          $message = sprintf($lang['mip_destroyed'], $interceptors);
        };
        $qUpdate .= "0";
        $message .= $lang['mip_defense_destroyed'];

        $attackResult = COE_missileAttack($targetUser, $rowAttacker, ($missiles - $interceptors), $planetDefense, $fleetRow['primaer']);

        foreach ($attackResult['structures'] as $key => $structure) {
          $destroyed = $planetDefense[$key][0] - $structure[0];
          if ($key > 400 && $destroyed) {
            $message .= "&nbsp;&nbsp;{$lang['tech'][$key]} - {$destroyed} {$lang['quantity']}<br>";
            $qUpdate .= ", `{$resource[$key]}` = {$structure[0]}";
          };
        };

        $qUpdate .= ", `metal`=`metal`+".$attackResult['metal'].", `crystal`=`crystal`+".$attackResult['crystal'];
        $message .= "{$lang['mip_recycled']}{$lang['Metal']}: {$attackResult['metal']}, {$lang['Crystal']}: {$attackResult['crystal']}<br>";
      };

      $qUpdate .= " WHERE `id` = " . $targetPlanet['id'] . ";";
      doquery($qUpdate, 'planets');

      $sourcePlanet = doquery("SELECT `name` FROM `{{table}}` WHERE `galaxy` = '{$fleetRow['galaxy_angreifer']}' AND `system` = '{$fleetRow['system_angreifer']}' AND `planet` = '{$fleetRow['planet_angreifer']}' and planet_type = " . PT_PLANET, 'planets', true);

      $message_vorlage = sprintf($lang['mip_body_attack'], $fleetRow['anzahl'],
        addslashes($sourcePlanet['name']), $fleetRow['galaxy_angreifer'], $fleetRow['system_angreifer'], $fleetRow['planet_angreifer'],
        addslashes($targetPlanet['name']), $fleetRow['galaxy'], $fleetRow['system'], $fleetRow['planet']);

      if (empty($message))
        $message = $lang['mip_no_defense'];

      SendSimpleMessage ( $fleetRow['owner'], '', $time_now, 0, $lang['mip_sender_amd'], $lang['mip_subject_amd'], $message_vorlage . $message );
      SendSimpleMessage ( $fleetRow['zielid'], '', $time_now, 0, $lang['mip_sender_amd'], $lang['mip_subject_amd'], $message_vorlage . $message );
    };
    doquery("DELETE FROM {{table}} WHERE id = '" . $fleetRow['id'] . "'", 'iraks');
  };
};
?>