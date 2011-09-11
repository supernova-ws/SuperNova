<?php

function coe_attack_fleet_fill(&$attackFleets, $fleet, $strField = 'detail')
{
  global $sn_data;

  $attackFleets[$fleet['fleet_id']]['fleet'] = $fleet;

  $attackFleets[$fleet['fleet_id']]['user'] = doquery(
    "SELECT `id`, `username`, `{$sn_data[MRC_ADMIRAL]['name']}`, `defence_tech`, `shield_tech`, `military_tech` FROM `{{users}}` WHERE `id` = '{$fleet['fleet_owner']}';"
  , '', true);

  $attackFleets[$fleet['fleet_id']][$strField] = array();
  $temp = explode(';', $fleet['fleet_array']);
  foreach ($temp as $Element) {
    $Element = explode(',', $Element);

    if ($Element[0] < 100) continue;

    if (!isset($attackFleets[$fleet['fleet_id']][$strField][$Element[0]]))
      $attackFleets[$fleet['fleet_id']][$strField][$Element[0]] = 0;
    $attackFleets[$fleet['fleet_id']][$strField][$Element[0]] += $Element[1];
  }
}

/**
 * BE_calculateTech.php
 * Battle Engine Effective Tech levels calculations
 * "rf" stands for "Rapid Fire"
 * "rp" stands for "ResourcePoints"
*/

function coe_calculate_techs(&$user)
{
  global $sn_data;

  $armor_tech  = mrc_modify_value($user, false, MRC_ADMIRAL, 1 + 0.1 * $user['defence_tech']);
  $shield_tech = mrc_modify_value($user, false, MRC_ADMIRAL, 1 + 0.1 * $user['shield_tech']);
  $weapon_tech = mrc_modify_value($user, false, MRC_ADMIRAL, 1 + 0.1 * $user['military_tech']);

  return array('def' => $armor_tech, 'shield' => $shield_tech, 'att' => $weapon_tech);
}

/**
 * BE_calculateAttack.php
 * Battle Engine Attack calculation
 * "rf" stands for "Rapid Fire"
 * "rp" stands for "ResourcePoints"
*/

function coe_precalc_round_data(&$fleets, &$fleetRoundData, &$fleetArray, $strFieldName, $isSimulated = false){
  global $sn_data;

  foreach ($fleets as $fleetID => $fleet)
  {
    $fleetRoundData['att'][$fleetID] = 0;
    $fleetRoundData['shield'][$fleetID] = 0;
    $fleetRoundData['amount'][$fleetID] = 0;

    if(!is_array($fleet[$strFieldName]))
    {
      $fleet[$strFieldName] = array();
    }
    foreach ($fleet[$strFieldName] as $element => $amount)
    {
      $thisArmor  = ($sn_data[$element]['metal'] + $sn_data[$element]['crystal']) / 10;

      $thisDef    = $amount * $thisArmor * $fleet['techs']['def'];
      $thisShield = $amount * ($sn_data[$element]['shield']) * $fleet['techs']['shield'];
      $thisAtt    = $amount * ($sn_data[$element]['attack']) * $fleet['techs']['att'];

      if (!$isSimulated){
        $thisDef    *= mt_rand(80, 120) / 100;
        $thisShield *= mt_rand(80, 120) / 100;
        $thisAtt    *= mt_rand(80, 120) / 100;
      }

      $fleetArray[$fleetID][$element] = array_map('round', array('def' => $thisDef, 'shield' => $thisShield, 'att' => $thisAtt));

      $fleetRoundData['def'][$fleetID] += $thisDef;
      $fleetRoundData['att'][$fleetID] += $thisAtt;
      $fleetRoundData['shield'][$fleetID] += $thisDef;
      $fleetRoundData['amount'][$fleetID] += $amount;

      foreach ($sn_data[$element]['sd'] as $element2 => $amount2){
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

function coe_calc_round_fleet_harm_percent(&$attArray){
  foreach ($attArray as $fleetID => $fleet) {
    if (!is_numeric($fleetID)) continue;
    $attArray[$fleetID]['HarmPct'] = $attArray[$fleetID]['def'] / $attArray['total']['def'];
  }
}

function BE_calculateRound(&$fleets, &$fleetsAttacking, &$fleet_n, &$fleet_shield, &$fleetDmgPerEnemyFleet, &$attackArray, &$defenseArray, &$attackRoundData, &$defenseRoundData, $strFieldName, $round )
{
  global $sn_data;

  foreach ($fleets as $fleetID => $fleet)
  {
    $fleet_n[$fleetID] = array();

    foreach($fleet[$strFieldName] as $element => $amount)
    {
      if ($amount > 0) {

        $HarmPctIncoming = $attackArray[$fleetID]['HarmPct'] * $attackArray[$fleetID][$element]['def'] / $attackArray[$fleetID]['def']; // which % of damage came to this partial defending ship type

        foreach($defenseArray as $fleetDefID => $fleetDef)
        {
          if (!is_numeric($fleetDefID)) continue;

          foreach($fleetDef as $defenseShipID => $defenseShipData)
          {
            //if ($defenseShipData['def'] <= 0) continue;
            if (!is_numeric($defenseShipID)) continue;
            if ($amount <= 0) continue; // if in loop we destroy all ship of current class - no need in looping more

            // $PctHarmFromThisShip = $defenseShipData['att'] / $fleetDef['att']; // which % of damage of whole fleet came from this partial ship
            // $PctHarmMade = $HarmPctIncoming * $PctHarmFromThisShip; // which % of damage came to defending ship from attacker's ship

            $PctHarmMade = $HarmPctIncoming ; // which % of damage came to defending ship from attacker's ship
            $HarmMade = round($PctHarmMade * $defenseShipData['att']); // which damage came to defending ship from attacker's ship

            $FinalHarm = round($HarmMade * $sn_data[$defenseShipID]['amplify'][$element]); // method 2 - Amplification (RapidFire) applies BEFORE shields
            // $FinalHarm = $HarmMade; // method 3 - Amplification applies AFTER shields

BE_DEBUG_openRow($round, $defenseShipID, $defenseShipData, $element, $attackArray, $fleetID, $HarmPctIncoming, $HarmMade, $FinalHarm, $amount);

            if ($attackArray[$fleetID][$element]['shield'] < $FinalHarm) // Does damage enough to penetrate shields? If yes...
            {
              $FinalHarm = $FinalHarm - $attackArray[$fleetID][$element]['shield']; // How much damage passed through shield
              $fleet_shield += $attackArray[$fleetID][$element]['shield'];          // How much damage was absorbed by shield - add to total fleet absorb
              $attackArray[$fleetID][$element]['shield'] = 0;                       // Shield now 0 'cause they all was destroyed by incoming damage

              //$FinalHarm = $FinalHarm * $sn_data[$defenseShipID]['amplify'][$element]; // method 3 - Amplification (RapidFire) applies AFTER shields

              $calculatedDestroyedShip = floor($FinalHarm / ($attackArray[$fleetID][$element]['def'] / $amount));   // How much ships was destroyed by incoming harm
              $fleet_n[$fleetID][$element] = max(0, ceil($amount - $calculatedDestroyedShip));                      // How much ships left in fleet
              $amount = $fleet_n[$fleetID][$element];                                                               // Changing $amount for in-loop needs

              $attackArray[$fleetID][$element]['def'] = max(0, $attackArray[$fleetID][$element]['def']-$FinalHarm); // Ship structure points reduced by incoming damage
            }
            else
            {                                          //Damage is not enough to penetrate shield
              $fleet_n[$fleetID][$element] = round($amount);     // All ships survive this loop
              $attackArray[$fleetID][$element]['shield'] = max(0, $attackArray[$fleetID][$element]['shield'] - $FinalHarm);  // Shields reduced by incoming damage
              $fleet_shield += $FinalHarm;                       // Adding all incoming damage to fleet's shield
              $calculatedDestroyedShip = 0;                      // No ship was destroyed
            }
BE_DEBUG_closeRow($calculatedDestroyedShip, $fleet_n[$fleetID][$element]);
          }
        }
      }
      else
      {
        $fleet_n[$fleetID][$element] = round($amount);
      }
    }
  }
}

function coe_attack_calculate(&$attackers, &$defenders, $isSimulated = false)
{
  global $sn_data;

  $totalResourcePoints = array('attacker' => 0, 'defender' => 0);

  $attackResourcePoints = array('metal' => 0, 'crystal' => 0);
  foreach ($attackers as $fleetID => $attacker)
  {
    $attackers[$fleetID]['techs'] = coe_calculate_techs($attacker['user']);

    if(!is_array($attacker['detail']))
    {
      $attacker['detail'] = array();
    }
    foreach ($attacker['detail'] as $element => $amount)
    {
      $attackResourcePoints['metal'] += $sn_data[$element]['metal'] * $amount;
      $attackResourcePoints['crystal'] += $sn_data[$element]['crystal'] * $amount ;
    }
  }
  $totalResourcePoints['attacker'] += $attackResourcePoints['metal'];
  $totalResourcePoints['attacker'] += $attackResourcePoints['crystal'];

  $defenseResourcePoints = array('metal' => 0, 'crystal' => 0);
  foreach ($defenders as $fleetID => $defender)
  {
    $defenders[$fleetID]['techs'] = coe_calculate_techs($defender['user']);

    if(!is_array($defender['def']))
    {
      $defender['def'] = array();
    }
    foreach ($defender['def'] as $element => $amount)
    {
      if ($element < 300)
      {
        $defenseResourcePoints['metal'] += $sn_data[$element]['metal'] * $amount;
        $defenseResourcePoints['crystal'] += $sn_data[$element]['crystal'] * $amount;
      }
      else
      {
        if (!isset($originalDef[$element])) $originalDef[$element] = 0;
        $originalDef[$element] += $amount;
      }
    }
  }
  $totalResourcePoints['defender'] += $defenseResourcePoints['metal'];
  $totalResourcePoints['defender'] += $defenseResourcePoints['crystal'];

BE_DEBUG_openTable();

  for ($round = 0, $rounds = array(); $round < MAX_ATTACK_ROUNDS; $round++)
  {
    $attArray = array();
    $attackRoundData = array();
    coe_precalc_round_data($attackers, $attackRoundData, $attArray, 'detail', $isSimulated);

    $defArray = array();
    $defenseRoundData = array();
    coe_precalc_round_data($defenders, $defenseRoundData, $defArray, 'def', $isSimulated);

    $rounds[$round] = array('attackers' => unserialize(serialize($attackers)), 'defenders' => unserialize(serialize($defenders)), 'attack' => $attackRoundData['att'], 'defense' => $defenseRoundData['att'], 'attackA' => $attackRoundData['amount'], 'defenseA' => $defenseRoundData['amount'], 'infoA' => $attArray, 'infoD' => $defArray);

    if ($defenseRoundData['amount']['total'] <= 0 || $attackRoundData['amount']['total'] <= 0)
    {
      break;
    }

    // Calculate hit percentages (ACS only but ok)
    coe_calc_round_fleet_harm_percent($attArray);
    coe_calc_round_fleet_harm_percent($defArray);

    $attackPctPerFleet = array();
    foreach ($attackRoundData['amount'] as $fleetID => $amount)
    {
      if (!is_numeric($fleetID)) continue;
      $attackPctPerFleet[$fleetID] = $amount / $attackRoundData['amount']['total'];
    }

    $defensePctPerFleet = array();
    foreach ($defenseRoundData['amount'] as $fleetID => $amount)
    {
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

    foreach ($attackers as $fleetID => $attacker)
    {
      $attackers[$fleetID]['detail'] = array_map('round', $attacker_n[$fleetID]);
    }

    foreach ($defenders as $fleetID => $defender)
    {
      $defenders[$fleetID]['def'] = array_map('round', $defender_n[$fleetID]);
    }
  }

BE_DEBUG_closeTable();

  if ($attackRoundData['amount']['total'] <= 0)
  {
    $won = 2; // defender
  }
  elseif ($defenseRoundData['amount']['total'] <= 0)
  {
    $won = 1; // attacker
  }
  else
  {
    $won = 0; // draw
    $rounds[count($rounds)] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => $attackRoundData['att'], 'def' => $defenseRoundData['att'], 'attackA' => $attackRoundData['amount'], 'defenseA' => $defenseRoundData['amount']);
  }

  // Debree
  foreach ($attackers as $fleetID => $attacker)
  {
    if(!is_array($attacker['detail']))
    {
      $attacker['detail'] = array();
    }
    foreach ($attacker['detail'] as $element => $amount)
    {
      $totalResourcePoints['attacker'] -= $sn_data[$element]['metal'] * $amount;
      $totalResourcePoints['attacker'] -= $sn_data[$element]['crystal'] * $amount;

      $attackResourcePoints['metal'] -= $sn_data[$element]['metal'] * $amount;
      $attackResourcePoints['crystal'] -= $sn_data[$element]['crystal'] * $amount;
    }
  }

  foreach($defenders as $fleetID => $defender)
  {
    if(!is_array($defender['def']))
    {
      $defender['def'] = array();
    }
    foreach ($defender['def'] as $element => $amount)
    {
      if ($element < 300)
      {
        $defenseResourcePoints['metal'] -= $sn_data[$element]['metal'] * $amount;
        $defenseResourcePoints['crystal'] -= $sn_data[$element]['crystal'] * $amount;

        $totalResourcePoints['defender'] -= $sn_data[$element]['metal'] * $amount;
        $totalResourcePoints['defender'] -= $sn_data[$element]['crystal'] * $amount;
      }
      else
      {
        $lost = $originalDef[$element] - $amount;

        if($isSimulated)
        { // for simulation just return 70% of loss
          $giveback = floor($lost * 0.7);
        }
        else
        {
          if($originalDef[$element] > 10)
          { // if there were more then 10 defense elements - mass-calculating giveback
            $giveback = floor($lost * (mt_rand(70*0.8, 70*1.2) / 100));
          }
          else
          { //if there were less then 10 defense elements - calculating giveback per element
            $giveback = 0;
            for($i = 1; $i <= $lost; $i++)
            {
              if(mt_rand(1,100)<=70)
              {
                $giveback++;
              }
            }
          }
        }
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

  /**
   * This file is under the GPL liscence, which must be included with the file under distrobution (license.txt)
   * this file was made by Xnova, edited to support Toms combat engine by Anthony (MadnessReD) [http://madnessred.co.cc/]
   * Do not edit this comment block
   */

  /*
   * BE_CalculateMoon.php
   * Battle Engine File
   * Calculate moon creation chance with simulation support
   */

  /*
  *
  * Partial copyright (c) 2010 by Gorlum for oGame.triolan.com.ua
  */

function BE_calculateMoonChance($FleetDebris)
{
  $MoonChance = $FleetDebris / 1000000;
  return ($MoonChance < 1) ? 0 : ($MoonChance>30 ? 30 : $MoonChance);
}

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
  global $sn_data;

  foreach ($attackFleets as $fleetID => &$attacker) {
    $fleetArray = '';
    $totalCount = 0;
    if(!is_array($attacker['detail']))
    {
      $attacker['detail'] = array();
    }
    foreach ($attacker['detail'] as $element => $amount)
    {
      if ($amount > 0)
      {
        $fleetArray .= $element.','.$amount.';';
        $totalCount += $amount;
        // !G+ For now we do not count deutrium for return in capacity
        $attacker['loot']['capacity'] += $sn_data[$element]['capacity'] * $amount;
      }
    }

    // !G+ Some misc calculations
    $attacker['totalCount'] = $totalCount;
    if ($totalCount>0)
    {
      $attacker['fleetArray'] = $fleetArray;
      $attackerTotalCapacity += $attacker['loot']['capacity'];
    }
    else
    {
      if (!$isSimulation)
      {
        doquery ('DELETE FROM {{fleets}} WHERE `fleet_id`='.$fleetID);
      }
    }
  }

  $loot['metal'] = max(0, $TargetPlanet['metal'] / 2);
  $loot['crystal'] = max(0, $TargetPlanet['crystal'] / 2);
  $loot['deuterium'] = max(0, $TargetPlanet['deuterium'] / 2);
  $loot['all'] = max($loot['metal'] + $loot['crystal'] + $loot['deuterium'], 1);
  $loot['available'] = min($loot['all'], $attackerTotalCapacity);

  $loot = array_map('round', $loot);

  $loot['looted'] = array( 'deuterium' => 0, 'crystal' => 0, 'metal' => 0);

  if ($result['won'] == 1)
  {
    foreach ($attackFleets as $fleetID => &$attacker)
    {
      if ($attacker['totalCount'] <= 0)
      {
        continue;
      }
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

  return $loot;
}

?>
