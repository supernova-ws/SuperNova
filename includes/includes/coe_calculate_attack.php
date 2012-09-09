<?php

require_once('includes/includes/coe_zi_helpers.php');

function coe_attack_fleet_fill(&$attackFleets, $fleet, $strField = 'detail')
{
  global $sn_data;

  $attackFleets[$fleet['fleet_id']]['fleet'] = $fleet;

  $attackFleets[$fleet['fleet_id']]['user'] = doquery(
    "SELECT `id`, `username`, `defence_tech`, `shield_tech`, `military_tech`, `ally_id`, `user_as_ally` FROM `{{users}}` WHERE `id` = '{$fleet['fleet_owner']}';"
  , '', true);

  $attackFleets[$fleet['fleet_id']]['user'][$sn_data[MRC_ADMIRAL]['name']] = mrc_get_level($attackFleets[$fleet['fleet_id']]['user'], null, MRC_ADMIRAL);

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
  return array(
    'def'    => mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_ARMOR), 1),
    'shield' => mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_SHIELD), 1),
    'att'    => mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_WEAPON), 1),
  );
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
/*
      foreach($sn_data[$element]['sd'] as $element2 => $amount2){
        $aTemp = max($amount, $amount*$amount2);

        $fleetRoundData['rf'][$fleetID][$element2] += $aTemp;
        $fleetRoundData['rf']['total'][$element2] += $aTemp;
      }
*/
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

            $FinalHarm = round($HarmMade * (isset($sn_data[$defenseShipID]['amplify'][$element]) ? $sn_data[$defenseShipID]['amplify'][$element] : 1)); // method 2 - Amplification (RapidFire) applies BEFORE shields
            // $FinalHarm = $HarmMade; // method 3 - Amplification applies AFTER shields

BE_DEBUG_openRow($round, $defenseShipID, $defenseShipData, $element, $attackArray, $fleetID, $HarmPctIncoming, $HarmMade, $FinalHarm, $amount);

            if ($attackArray[$fleetID][$element]['shield'] < $FinalHarm) // Does damage enough to penetrate shields? If yes...
            {
              $FinalHarm = $FinalHarm - $attackArray[$fleetID][$element]['shield']; // How much damage passed through shield
              $fleet_shield += $attackArray[$fleetID][$element]['shield'];          // How much damage was absorbed by shield - add to total fleet absorb
              $attackArray[$fleetID][$element]['shield'] = 0;                       // Shield now 0 'cause they all was destroyed by incoming damage

              //$FinalHarm = $FinalHarm * (isset($sn_data[$defenseShipID]['amplify'][$element]) ? $sn_data[$defenseShipID]['amplify'][$element] : 1); // method 3 - Amplification (RapidFire) applies AFTER shields

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
      if(in_array($element, $sn_data['groups']['fleet']))
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
      if(in_array($element, $sn_data['groups']['fleet']))
      {
        $defenseResourcePoints['metal'] -= $sn_data[$element]['metal'] * $amount;
        $defenseResourcePoints['crystal'] -= $sn_data[$element]['crystal'] * $amount;

        $totalResourcePoints['defender'] -= $sn_data[$element]['metal'] * $amount;
        $totalResourcePoints['defender'] -= $sn_data[$element]['crystal'] * $amount;
      }
      elseif(in_array($element, $sn_data['groups']['defense_active']))
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
  *
  * Partial copyright (c) 2010 by Gorlum for oGame.triolan.com.ua
  */

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

  /**
   * formatCR.php by Anthony (MadnessRed) [http://madnessred.co.cc/]
   *
   * Copyright (c) MadnessRed 2008.
   *
   * made from Scratch by MadnessRed to work with the ACS Combat engine.
   *
   * The files (below line 15) is under the GPL liscence, and the file license.txt must be included with this file.
   *
   * You may not edit this comment block. You may not copy any part of this file into any other file with out copying this comment block with it and placing it above any code there might be.
  */

  /*
    Partial Copyright (c) Gorlum 2010
    Rewrite and optimization by Gorlum for http://ogame.triolan.com.ua
  */

function coe_report_format_fleet(&$dataInc, &$data_prev, $isAttacker, $isLastRound)
{
  global $lang;

  if ($isAttacker){
    $dataA = $dataInc['attackers'];
    $dataB = $dataInc['infoA'];
    $data_prevA = $data_prev['attackers'];
    $strField = 'detail';
    $strType = $lang['sys_attacker'];
  }else{
    $dataA = $dataInc['defenders'];
    $dataB = $dataInc['infoD'];
    $data_prevA = $data_prev['defenders'];
    $strField = 'def';
    $strType = $lang['sys_defender'];
    $Coord = $dataInc['defCoord'];
  };

  foreach($dataA as $fleet_id => $data2)
  {
    //Player Information
    $weap = ($data2['user']['military_tech'] * 10);
    $shie = ($data2['user']['shield_tech'] * 10);
    $armr = ($data2['user']['defence_tech'] * 10);

    //And html output player information
    $fl_info1  = "<table><tr><th>";

    if($isAttacker)
    {
      $Coord = "[".
        intval($data2['fleet']['fleet_start_galaxy']).":".
        intval($data2['fleet']['fleet_start_system']).":".
        intval($data2['fleet']['fleet_start_planet'])."]";
    }

    $fl_info1 .= "{$strType} {$data2['user']['username']} ({$Coord})<br />";
    $fl_info1 .= "{$lang['sys_ship_weapon']}: {$weap}% {$lang['sys_ship_shield']}: {$shie} {$lang['sys_ship_armour']}: {$armr}%";

    //Start the table rows.
    $ships1  = "<tr><th>{$lang['sys_ship_type']}</th>";
    $count1  = "<tr><th>{$lang['sys_ship_count']}</th>";
    $weap1  = "<tr><th>{$lang['sys_ship_weapon']}</th>";
    $shields1  = "<tr><th>{$lang['sys_ship_shield']}</th>";
    $armour1  = "<tr><th>{$lang['sys_ship_armour']}</th>";

    //And now the data columns "foreach" ship
    if(!is_array($data2[$strField]))
    {
      $data2[$strField] = array();
    }
    foreach($data2[$strField] as $ship_id => $ship_count1)
    {
      if ($ship_count1 > 0)
      {
//        $ships1 .= "<th>[ship[".$ship_id."]]</th>";
        $ships_destroyed = !empty($data_prevA) ? $ship_count1 - $data_prevA[$fleet_id][$strField][$ship_id] : 0;
        $ships1 .= "<th>{$lang['tech'][$ship_id]}</th>";
        $count1 .= "<th>".$ship_count1." ".($ships_destroyed ? "<span style=\"color:red\">{$ships_destroyed}</span>" : '')."</th>";

        if (!$isLastRound)
        {
          $ship_points = $dataB[$fleet_id][$ship_id];
          if ($ship_points['def'] > 0)
          {
            $weap1 .= "<th>{$ship_points['att']}</th>";
            $shields1 .= "<th>{$ship_points['shield']}</th>";
            $armour1 .= "<th>{$ship_points['def']}</th>";
          }
        }
      }
    }

    //End the table Rows
    $ships1 .= "</tr>";
    $count1 .= "</tr>";
    $weap1 .= "</tr>";
    $shields1 .= "</tr>";
    $armour1 .= "</tr>";

    //now compile what we have, ok its the first half but the rest comes later.
    $html .= $fl_info1;
    $html .= "<table border=1 align=\"center\">";
    $html .= $ships1.$count1;
    if (!$isLastRound)
      $html .= $weap1.$shields1.$armour1;
    $html .= "</table></th></tr></table><br />";
  }

  return $html;
};

function coe_report_format (&$result_array,&$steal_array,&$moon_int,$moon_string,&$time_float)
{

  global $lang;

  $html = "";
  $bbc = "";

  if (defined('BE_DEBUG'))
  {
    global $be_debug_array;

    if($be_debug_array)
    {
      foreach($be_debug_array as $be_debug_line)
      {
        $html .= $be_debug_line;
      }
    }
  }

  //And lets start the CR. And admin message like asking them to give the cr. Nope, well moving on give the time and date ect.
  $html .= "{$lang['sys_coe_combat_start']} ".date(FMT_DATE_TIME)."<br /><br />";

  $data = $result_array['rw'][0]['attackers'];
  $dataKey = array_keys($data);
  $data = $data[$dataKey[0]]['fleet'];
  $defenderCoord = "[".intval($data['fleet_end_galaxy']).":".intval($data['fleet_end_system']).":".intval($data['fleet_end_planet'])."]";

  $rw_count = count($result_array['rw']);
  for ($round_no = 1; $round_no <= $rw_count; $round_no++) {
    $isLastRound = ($round_no == $rw_count);
    if ($isLastRound){
      $html .= "{$lang['sys_coe_combat_end']}:<br /><br />";
    }else{
      $html .= "{$lang['sys_coe_round']} ".$round_no.":<br /><br />";
    };

    //Now whats that attackers and defenders data
    $data = $result_array['rw'][$round_no-1];
    $data_prev = $round_no == 1 ? false : $result_array['rw'][$round_no-2];
    $data['defCoord'] = $defenderCoord;

    $html .= coe_report_format_fleet($data, $data_prev, true, $isLastRound);
    $html .= coe_report_format_fleet($data, $data_prev, false, $isLastRound);

    //HTML What happens?
    if (!$isLastRound){
      $html .= sprintf($lang['sys_coe_attacker_turn'], $data['attack']['total'], $data['defShield']);
      $html .= sprintf($lang['sys_coe_defender_turn'], $data['defense']['total'], $data['attackShield']);
    }
  }

  //ok, end of rounds, battle results now.

  //Who won?
  if ($result_array['won'] == 2){
    //Defender wins
    $result1  = $lang['sys_coe_outcome_win'];
  }elseif ($result_array['won'] == 1){
    //Attacker wins
    $result1  = $lang['sys_coe_outcome_loss'];
    $result1 .= sprintf($lang['sys_coe_outcome_loot'], $steal_array['metal'], $steal_array['crystal'], $steal_array['deuterium']);
  }else{
    //Battle was a draw
    $result1  = $lang['sys_coe_outcome_draw'];
  }



  //$html .= "<br /><br />";
  $html .= $result1;
  $html .= "<br />";

  $debirs_meta = ($result_array['debree']['att'][0] + $result_array['debree']['def'][0]);
  $debirs_crys = ($result_array['debree']['att'][1] + $result_array['debree']['def'][1]);
  $html .= sprintf($lang['sys_coe_attacker_lost'], $result_array['lost']['att']);
  $html .= sprintf($lang['sys_coe_defender_lost'], $result_array['lost']['def']);
  $html .= sprintf($lang['sys_coe_debris_left'], $debirs_meta, $debirs_crys);
  $html .= sprintf($lang['sys_coe_moon_chance'], $moon_int);
  $html .= "{$moon_string}<br /><br />";

  $html .= sprintf($lang['sys_coe_rw_time'], $time_float);

  return array('html' => $html, 'bbc' => $bbc);
}

?>
