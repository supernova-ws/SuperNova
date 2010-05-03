<?php
/**
 * BE_calculatePostAttacker.php by Gorlum for http://ogame.triolan.com.ua
 * Copyright (c) Gorlum 2010.
 *
 * Battle Engine Post-Attack Calculations for Attackers (ACS supported)
 * Calculates fleet left and possible loot
 *
 * made from Scratch by Gorlum
 *
 * The files (below line 12) is under the GPL liscence, and the file license.txt must be included with this file.
 *
 * You may not edit this comment block. You may not copy any part of this file into any other file with out copying this comment block with it and placing it above any code there might be.
*/

function BE_calculatePostAttacker($TargetPlanet, &$attackFleets, $result, $isSimulation = true){
  global $pricelist;

  foreach ($attackFleets as $fleetID => &$attacker) {
    $fleetArray = '';
    $totalCount = 0;
    foreach ($attacker['detail'] as $element => $amount) {
      if ($amount)
        $fleetArray .= $element.','.$amount.';';
      $totalCount += $amount;

      // !G+ For now we do not count deutrium for return in capacity
//      $attackFleets[$fleetID]['loot']['capacity'] += $pricelist[$element]['capacity'] * $amount;
      $attacker['loot']['capacity'] += $pricelist[$element]['capacity'] * $amount;
    }

    // !G+ Some misc calculations
//    $attackFleets[$fleetID]['totalCount'] = $totalCount;
    $attacker['totalCount'] = $totalCount;
    if ($totalCount>0) {
//      $attackFleets[$fleetID]['fleetArray'] = $fleetArray;
//      $attackerTotalCapacity += $attackFleets[$fleetID]['loot']['capacity'];
      $attacker['fleetArray'] = $fleetArray;
      $attackerTotalCapacity += $attacker['loot']['capacity'];
    }else{
      if (!$isSimulation)
        doquery ('DELETE FROM {{table}} WHERE `fleet_id`='.$fleetID,'fleets');
    };
  };

  $loot['metal'] = $TargetPlanet['metal'] / 2;
  $loot['crystal'] = $TargetPlanet['crystal'] / 2;
  $loot['deuterium'] = $TargetPlanet['deuterium'] / 2;
  $loot['all'] = $loot['metal'] + $loot['crystal'] + $loot['deuterium'];
  $loot['available'] = min($loot['all'], $attackerTotalCapacity);

  $loot = array_map('round', $loot);

  $loot['looted'] = array( 'deuterium' => 0, 'crystal' => 0, 'metal' => 0);

  if ($result['won'] == 1) {
    foreach ($attackFleets as $fleetID => &$attacker) {
      if ($attacker['totalCount'] > 0) {
        $attacker_part = $attacker['loot']['capacity'] / $attackerTotalCapacity;

        // Variant 1: loot most expensive resources first deu -> cry -> met
        //$attacker['loot']['deuterium'] = min(round($loot['deuterium'] * $attacker_part), $attacker['loot']['capacity']);
        //$attacker['loot']['crystal'] = min(round($loot['crystal'] * $attacker_part), $attacker['loot']['capacity']) - $attacker['loot']['deuterium'];
        //$attacker['loot']['metal'] = min(round($loot['metal'] * $attacker_part), $attacker['loot']['capacity']) - $attacker['loot']['deuterium'] - $attacker['loot']['crystal'];

        // Variant 2: loot divided in proportion to resources on planet (i.e. 2kk deu 4kk cry 6kk met means loot proportion 1:2:3)
        $attacker['loot']['deuterium'] = min($loot['deuterium'] * $attacker_part, $attacker['loot']['capacity'] * $loot['deuterium'] / $loot['all']);
        $attacker['loot']['crystal'] = min($loot['crystal'] * $attacker_part, $attacker['loot']['capacity'] * $loot['crystal'] / $loot['all']);
        $attacker['loot']['metal'] = min($loot['metal'] * $attacker_part, $attacker['loot']['capacity'] * $loot['metal'] / $loot['all']);

        $attacker['loot'] = array_map('round', $attacker['loot']);

        $loot['looted']['metal'] += $attacker['loot']['metal'];
        $loot['looted']['crystal'] += $attacker['loot']['crystal'];
        $loot['looted']['deuterium'] += $attacker['loot']['deuterium'];
      }
    }
  }

  return $loot;
}
?>
