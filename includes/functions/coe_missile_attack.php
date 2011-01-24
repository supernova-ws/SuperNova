<?php
// Copyright (c) 2009-2010 by Gorlum for http://supernova.ws
// Date 2009-08-08
// Open Source
// V1
//
function COE_missileAttack($defenceTech, $attackerTech, $MIPs, $structures, $targetedStructure = '0') {
  global $CombatCaps, $pricelist, $resource;

  // Here we select which part of defense should take damage: structure or shield
  // $damageTo = 'shield';
  $damageTo = 'structure';

  $MIPDamage = ($MIPs * $CombatCaps[503]['attack']) * (1 + 0.05 * $attackerTech[$resource[109]]);

  foreach ($structures as $key => $structure)
  {
    $structures[$key]['shield'] = $CombatCaps[$key]['shield'] * (1 + 0.05 * $defenceTech[$resource[111]]);
    $structures[$key]['structure'] = ($pricelist[$key]['metal'] + $pricelist[$key]['crystal']) * (1 + 0.05 * $defenceTech[$resource[110]]);
  };

  $startStructs = $structures;

  if ($targetedStructure){
    //attacking only selected structure
    $damageDone = $structures[$targetedStructure][$damageTo];
    $structsDestroyed = min( floor($MIPDamage/$damageDone), $structures[$targetedStructure][0] );
    $structures[$targetedStructure][0] -= $structsDestroyed;
    $MIPDamage -= $structsDestroyed*$damageDone;
  }else{
    // REALLY random attack
    do {
      // finding is there any structure that can be damaged with leftovers of $MIPDamage
      for ($i = 409; $i > 400; $i--){
        if (($structures[$i][0]>0) && ($structures[$i][$damageTo]<=$MIPDamage)){
          break;
        };
      };

      // Selecting random structure of available
      $RandomDefense = rand(401, $i);
      if ($i>400 && $structures[$RandomDefense][0]>0 && $structures[$RandomDefense][$damageTo]<=$MIPDamage){
        $MIPDamage -= $structures[$RandomDefense][$damageTo];
        $structures[$RandomDefense][0]--;
      };
    } while ($i>400); // on $i = 400 - no more structures to damage. Exiting loop
  };

  // 1/2 of metal and 1/4 of crystal of destroyed structures returns to planet
  $metal = 0;
  $crystal = 0;
  foreach ($structures as $key => $structure)
  {
    $destroyed = $startStructs[$key][0]-$structure[0];
    $metal += $destroyed*$pricelist[$key]['metal']/2;
    $crystal += $destroyed*$pricelist[$key]['crystal']/4;
  };

  $return['structures'] = $structures;     // Structures left after attack
  $return['metal']      = floor($metal);   // Metal scraps
  $return['crystal']    = floor($crystal); // Crystal scraps

  return $return;
}

// Copyright (c) 2009-2010 by Gorlum for http://supernova.ws
// Date 2009-08-08
// Open Source

/**
 * Copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *       OpenSource as long as you don't remove this Copyright
 * V3 2009-11-13
 * V2 2009-10-10
 */

function coe_o_missile_calculate()
{
  global $time_now, $resource, $lang;

  $iraks = doquery("SELECT * FROM {{iraks}} WHERE `zeit` <= '{$time_now}';");

  while ($fleetRow = mysql_fetch_array($iraks))
  {
    $targetUser  = doquery('SELECT * FROM {{users}} WHERE `id` = '.$fleetRow['zielid'], '', true);

    $target_planet_row = sys_o_get_updated($targetUser, array('galaxy' => $fleetRow['galaxy'], 'system' => $fleetRow['system'], 'planet' => $fleetRow['planet'], 'planet_type' => PT_PLANET), $time_now);
    $target_planet_row = $target_planet_row['planet'];

    $rowAttacker = doquery("SELECT `military_tech` FROM `{{users}}` WHERE `id` = '{$fleetRow['owner']}' LIMIT 1;", '', true);

    if ($target_planet_row['id'])
    {
      $planetDefense = array(
        400 => array( 0, 'shield' => 0, 'structure' => 0),
        401 => array( $target_planet_row[$resource[401]], 'shield' => 0, 'structure' => 0),
        402 => array( $target_planet_row[$resource[402]], 'shield' => 0, 'structure' => 0),
        403 => array( $target_planet_row[$resource[403]], 'shield' => 0, 'structure' => 0),
        404 => array( $target_planet_row[$resource[404]], 'shield' => 0, 'structure' => 0),
        405 => array( $target_planet_row[$resource[405]], 'shield' => 0, 'structure' => 0),
        406 => array( $target_planet_row[$resource[406]], 'shield' => 0, 'structure' => 0),
        407 => array( $target_planet_row[$resource[407]], 'shield' => 0, 'structure' => 0),
        408 => array( $target_planet_row[$resource[408]], 'shield' => 0, 'structure' => 0),
        409 => array( $target_planet_row[$resource[409]], 'shield' => 0, 'structure' => 0),
      );

      $message = '';
      $interceptors = $target_planet_row[$resource[502]]; // Number of interceptors
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

      $qUpdate .= " WHERE `id` = " . $target_planet_row['id'] . ";";
      doquery($qUpdate, 'planets');

      $sourcePlanet = doquery("SELECT `name` FROM `{{planets}}` WHERE `galaxy` = '{$fleetRow['galaxy_angreifer']}' AND `system` = '{$fleetRow['system_angreifer']}' AND `planet` = '{$fleetRow['planet_angreifer']}' and planet_type = " . PT_PLANET, '', true);

      $message_vorlage = sprintf($lang['mip_body_attack'], $fleetRow['anzahl'],
        addslashes($sourcePlanet['name']), $fleetRow['galaxy_angreifer'], $fleetRow['system_angreifer'], $fleetRow['planet_angreifer'],
        addslashes($target_planet_row['name']), $fleetRow['galaxy'], $fleetRow['system'], $fleetRow['planet']);

      if (empty($message))
        $message = $lang['mip_no_defense'];

      SendSimpleMessage ( $fleetRow['owner'], '', $time_now, 0, $lang['mip_sender_amd'], $lang['mip_subject_amd'], $message_vorlage . $message );
      SendSimpleMessage ( $fleetRow['zielid'], '', $time_now, 0, $lang['mip_sender_amd'], $lang['mip_subject_amd'], $message_vorlage . $message );
    };
    doquery("DELETE FROM {{iraks}} WHERE id = '{$fleetRow['id']}';");
  };
};

?>
