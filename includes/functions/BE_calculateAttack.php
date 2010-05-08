<?php
/**
 * BE_calculateAttack.php
 * Battle Engine Attack calculation
 * "rf" stands for "Rapid Fire"
 * "rp" stands for "ResourcePoints"
*/

function BE_preCalcRoundData(&$fleets, &$fleetRoundData, &$fleetArray, $strFieldName, $isSimulated = false){
  global $pricelist, $CombatCaps;

  foreach ($fleets as $fleetID => $fleet) {
    $fleetRoundData['att'][$fleetID] = 0;
    $fleetRoundData['shield'][$fleetID] = 0;
    $fleetRoundData['amount'][$fleetID] = 0;

    foreach ($fleet[$strFieldName] as $element => $amount) {
      $thisArmor  = ($pricelist[$element]['metal'] + $pricelist[$element]['crystal']) / 10;

      $thisDef    = $amount * $thisArmor * $fleet['techs']['def'];
      $thisShield = $amount * ($CombatCaps[$element]['shield']) * $fleet['techs']['shield'];
      $thisAtt    = $amount * ($CombatCaps[$element]['attack']) * $fleet['techs']['att'];

      if (!$isSimulated){
        $thisDef    *= rand(80, 120) / 100;
        $thisShield *= rand(80, 120) / 100;
        $thisAtt    *= rand(80, 120) / 100;
      }

      $fleetArray[$fleetID][$element] = array_map('round', array('def' => $thisDef, 'shield' => $thisShield, 'att' => $thisAtt));

      $fleetRoundData['def'][$fleetID] += $thisDef;
      $fleetRoundData['att'][$fleetID] += $thisAtt;
      $fleetRoundData['shield'][$fleetID] += $thisDef;
      $fleetRoundData['amount'][$fleetID] += $amount;

      foreach ($CombatCaps[$element]['sd'] as $element2 => $amount2){
        $aTemp = max($amount, $amount*$amount2);

        $fleetRoundData['rf'][$fleetID][$element2] += $aTemp;
        $fleetRoundData['rf']['total'][$element2] += $aTemp;
      }
    }
    $fleetRoundData['def']['total'] += $fleetRoundData['def'][$fleetID];
    $fleetRoundData['att']['total'] += $fleetRoundData['att'][$fleetID];
    $fleetRoundData['shield']['total'] += $fleetRoundData['shield'][$fleetID];
    $fleetRoundData['amount']['total'] += $fleetRoundData['amount'][$fleetID];


    $fleetArray[$fleetID]['def'] = $fleetRoundData['def'][$fleetID];
    $fleetArray[$fleetID]['att'] = $fleetRoundData['att'][$fleetID];
    $fleetArray[$fleetID]['shield'] = $fleetRoundData['shield'][$fleetID];
    $fleetArray[$fleetID]['amount'] = $fleetRoundData['amount'][$fleetID];

    $fleetArray['total']['def'] += $fleetRoundData['def'][$fleetID];
    $fleetArray['total']['att'] += $fleetRoundData['att'][$fleetID];
    $fleetArray['total']['shield'] += $fleetRoundData['shield'][$fleetID];
    $fleetArray['total']['amount'] += $fleetRoundData['amount'][$fleetID];
  }
}

function BE_calculateRoundFleetHarmPct(&$attArray){
  foreach ($attArray as $fleetID => $fleet) {
    if (!is_numeric($fleetID)) continue;
    $attArray[$fleetID]['HarmPct'] = $attArray[$fleetID]['def'] / $attArray['total']['def'];
  }
}

function BE_calculateRound(&$fleets, &$fleetsAttacking, &$fleet_n, &$fleet_shield, &$fleetDmgPerEnemyFleet, &$attackArray, &$defenseArray, &$attackRoundData, &$defenseRoundData, $strFieldName, $round ){
  global $pricelist, $CombatCaps;

  foreach ($fleets as $fleetID => $fleet) {
    $fleet_n[$fleetID] = array();

    foreach($fleet[$strFieldName] as $element => $amount) {
      if ($amount > 0) {

        $HarmPctIncoming = $attackArray[$fleetID]['HarmPct'] * $attackArray[$fleetID][$element]['def'] / $attackArray[$fleetID]['def']; // which % of damage came to this partial defending ship type

        foreach($defenseArray as $fleetDefID => $fleetDef){
          if (!is_numeric($fleetDefID)) continue;

          foreach($fleetDef as $defenseShipID => $defenseShipData){
            if (!is_numeric($defenseShipID)) continue;

            // $PctHarmFromThisShip = $defenseShipData['att'] / $fleetDef['att']; // which % of damage of whole fleet came from this partial ship
            // $PctHarmMade = $HarmPctIncoming * $PctHarmFromThisShip; // which % of damage came to defending ship from attacker's ship

            $PctHarmMade = $HarmPctIncoming ; // which % of damage came to defending ship from attacker's ship
            $HarmMade = round($PctHarmMade * $defenseShipData['att']); // which damage came to defending ship from attacker's ship

            $FinalHarm = round($HarmMade * $CombatCaps[$defenseShipID]['amplify'][$element]); // method 2 - Amplification (RapidFire) applies BEFORE shields
            // $FinalHarm = $HarmMade; // method 3 - Amplification applies AFTER shields

BE_DEBUG_openRow($round, $defenseShipID, $defenseShipData, $element, $attackArray, $fleetID, $HarmPctIncoming, $HarmMade, $FinalHarm, $amount);

            if ($attackArray[$fleetID][$element]['shield'] < $FinalHarm) {          // Does damage enough to penetrate shields? If yes...
              $FinalHarm = $FinalHarm - $attackArray[$fleetID][$element]['shield']; // How much damage passed through shield
              $fleet_shield += $attackArray[$fleetID][$element]['shield'];          // How much damage was absorbed by shield - add to total fleet absorb
              $attackArray[$fleetID][$element]['shield'] = 0;                       // Shield now 0 'cause they all was destroyed by incoming damage

              //$FinalHarm = $FinalHarm * $CombatCaps[$defenseShipID]['amplify'][$element]; // method 3 - Amplification (RapidFire) applies AFTER shields

              $calculatedDestroyedShip = floor($FinalHarm / ($attackArray[$fleetID][$element]['def'] / $amount));   // How much ships was destroyed by incoming harm
              $fleet_n[$fleetID][$element] = max(0, ceil($amount - $calculatedDestroyedShip));                      // How much ships left in fleet
              $amount = $fleet_n[$fleetID][$element];                                                               // Changing $amount for in-loop needs

              $attackArray[$fleetID][$element]['def'] = max(0, $attackArray[$fleetID][$element]['def']-$FinalHarm); // Ship structure points reduced by incoming damage
            } else {                                          //Damage is not enough to penetrate shield
              $fleet_n[$fleetID][$element] = round($amount);     // All ships survive this loop
              $attackArray[$fleetID][$element]['shield'] = max(0, $attackArray[$fleetID][$element]['shield']-$FinalHarm);  // Shields reduced by incoming damage
              $fleet_shield += $FinalHarm;                       // Adding all incoming damage to fleet's shield
              $calculatedDestroyedShip = 0;                      // No ship was destroyed
            }
BE_DEBUG_closeRow($calculatedDestroyedShip, $fleet_n[$fleetID][$element]);
          }
        }
      } else {
        $fleet_n[$fleetID][$element] = round($amount);
      }
    }
  }
}

function calculateAttack (&$attackers, &$defenders, $isSimulated = false) {
//  define("BE_DEBUG", true);

  global $pricelist, $CombatCaps, $game_config, $resource;
  global $strBE_Header;

  $totalResourcePoints = array('attacker' => 0, 'defender' => 0);

  $attackResourcePoints = array('metal' => 0, 'crystal' => 0);
  foreach ($attackers as $fleetID => $attacker) {
    $attackers[$fleetID]['techs'] = BE_calculateTechs($attacker['user']);

    foreach ($attacker['detail'] as $element => $amount) {
      $attackResourcePoints['metal'] += $pricelist[$element]['metal'] * $amount;
      $attackResourcePoints['crystal'] += $pricelist[$element]['crystal'] * $amount ;
    }
  }
  $totalResourcePoints['attacker'] += $attackResourcePoints['metal'];
  $totalResourcePoints['attacker'] += $attackResourcePoints['crystal'];

  $defenseResourcePoints = array('metal' => 0, 'crystal' => 0);
  foreach ($defenders as $fleetID => $defender) {
    $defenders[$fleetID]['techs'] = BE_calculateTechs($defender['user']);

    foreach ($defender['def'] as $element => $amount) {
      if ($element < 300) {
        $defenseResourcePoints['metal'] += $pricelist[$element]['metal'] * $amount ;
        $defenseResourcePoints['crystal'] += $pricelist[$element]['crystal'] * $amount ;
      } else {
        if (!isset($originalDef[$element])) $originalDef[$element] = 0;
        $originalDef[$element] += $amount;
      }
    }
  }
  $totalResourcePoints['defender'] += $defenseResourcePoints['metal'];
  $totalResourcePoints['defender'] += $defenseResourcePoints['crystal'];


BE_DEBUG_openTable();

  for ($round = 0, $rounds = array(); $round < MAX_ATTACK_ROUNDS; $round++) {
    $attArray = array();
    $attackRoundData = array();
    BE_preCalcRoundData($attackers, $attackRoundData, $attArray, 'detail', $isSimulated);

    $defArray = array();
    $defenseRoundData = array();
    BE_preCalcRoundData($defenders, $defenseRoundData, $defArray, 'def', $isSimulated);

    $rounds[$round] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => $attackRoundData['att'], 'defense' => $defenseRoundData['att'], 'attackA' => $attackRoundData['amount'], 'defenseA' => $defenseRoundData['amount'], 'infoA' => $attArray, 'infoD' => $defArray);

    if ($defenseRoundData['amount']['total'] <= 0 || $attackRoundData['amount']['total'] <= 0) {
      break;
    }

    // Calculate hit percentages (ACS only but ok)
    BE_calculateRoundFleetHarmPct($attArray);
    BE_calculateRoundFleetHarmPct($defArray);

    $attackPctPerFleet = array();
    foreach ($attackRoundData['amount'] as $fleetID => $amount) {
      if (!is_numeric($fleetID)) continue;
      $attackPctPerFleet[$fleetID] = $amount / $attackRoundData['amount']['total'];
    }

    $defensePctPerFleet = array();
    foreach ($defenseRoundData['amount'] as $fleetID => $amount) {
      if (!is_numeric($fleetID)) continue;
      $defensePctPerFleet[$fleetID] = $amount / $defenseRoundData['amount']['total'];
    }

    $attacker_n = array();
    $attacker_shield = 0;
    BE_calculateRound($attackers, $defenders, $attacker_n, $attacker_shield, $attackPctPerFleet, $attArray, $defArray, $attackRoundData, $defenseRoundData, 'detail', 'A'.$round);

    $defender_n = array();
    $defender_shield = 0;
    BE_calculateRound($defenders, $attackers, $defender_n, $defender_shield, $defensePctPerFleet, $defArray, $attArray, $defenseRoundData, $attackRoundData, 'def', 'D'.$round);

    $rounds[$round]['attackShield'] = $attacker_shield;
    $rounds[$round]['defShield'] = $defender_shield;

    foreach ($attackers as $fleetID => $attacker) {
      $attackers[$fleetID]['detail'] = array_map('round', $attacker_n[$fleetID]);
    }

    foreach ($defenders as $fleetID => $defender) {
      $defenders[$fleetID]['def'] = array_map('round', $defender_n[$fleetID]);
    }
  }

BE_DEBUG_closeTable();

  if ($attackRoundData['amount']['total'] <= 0) {
    $won = 2; // defender

  } elseif ($defenseRoundData['amount']['total'] <= 0) {
    $won = 1; // attacker

  } else {
    $won = 0; // draw
    $rounds[count($rounds)] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => $attackRoundData['att'], 'def' => $defenseRoundData['att'], 'attackA' => $attackRoundData['amount'], 'defenseA' => $defenseRoundData['amount']);
  }

  // Debree
  foreach ($attackers as $fleetID => $attacker) {
    foreach ($attacker['detail'] as $element => $amount) {
      $totalResourcePoints['attacker'] -= $pricelist[$element]['metal'] * $amount ;
      $totalResourcePoints['attacker'] -= $pricelist[$element]['crystal'] * $amount ;

      $attackResourcePoints['metal'] -= $pricelist[$element]['metal'] * $amount ;
      $attackResourcePoints['crystal'] -= $pricelist[$element]['crystal'] * $amount ;
    }
  }

  foreach ($defenders as $fleetID => $defender) {
    foreach ($defender['def'] as $element => $amount) {               //Line271
      if ($element < 300) {
        $defenseResourcePoints['metal'] -= $pricelist[$element]['metal'] * $amount ;
        $defenseResourcePoints['crystal'] -= $pricelist[$element]['crystal'] * $amount ;

        $totalResourcePoints['defender'] -= $pricelist[$element]['metal'] * $amount ;
        $totalResourcePoints['defender'] -= $pricelist[$element]['crystal'] * $amount ;
      } else {
        $lost = $originalDef[$element] - $amount;
        $giveback = $lost * (rand(70*0.8, 70*1.2) / 100);
        $defenders[$fleetID]['def'][$element] += $giveback;
      }
    }
  }

  $totalLost = array('att' => $totalResourcePoints['attacker'], 'def' => $totalResourcePoints['defender']);
  $debAttMet = ($attackResourcePoints['metal'] * 0.3);
  $debAttCry = ($attackResourcePoints['crystal'] * 0.3);
  $debDefMet = ($defenseResourcePoints['metal'] * 0.3);
  $debDefCry = ($defenseResourcePoints['crystal'] * 0.3);

  return array('won' => $won, 'debree' => array('att' => array($debAttMet, $debAttCry), 'def' => array($debDefMet, $debDefCry)), 'rw' => $rounds, 'lost' => $totalLost);
}
?>