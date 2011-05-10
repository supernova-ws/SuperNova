<?php

  /*
  * Partial copyright (c) 2009-2010 by Gorlum for http://supernova.ws
  *  Additions marks with "// !G+"
  *  [*] Includes MissionCaseEvoAttack.php from 'includes' directory
  *  [+] Loot algorithm - No more stinky RANMA code!
  *  [*] Heavy db query optimization (6 queries less!)
  *  [*] time() -> $time_now - no more duplicate fleet attack or lost fleet attack on heavy loaded servers!
  * Based on
  *   Code by TvdW (c) 2008
  *   MadnessRed Code
  */

  /**
   * This file is under the GPL liscence, which must be included with the file under distrobution (license.txt)
   * this file was made by Xnova, edited to support Toms combat engine by Anthony (MadnessReD) [http://madnessred.co.cc/]
   * Do not edit this comment block
   */

function flt_mission_attack($mission_data)
{
  global $lang, $resource, $CombatCaps, $sn_data, $time_now;

  $fleet_row          = $mission_data['fleet'];
  $destination_user   = $mission_data['dst_user'];
  $destination_planet = $mission_data['dst_planet'];

  if(!$fleet_row)
  {
    return;
  }

  if(!$destination_user || !$destination_planet || !is_array($destination_user) || !is_array($destination_planet))
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    return;
  }

  $TargetUserID  = $destination_planet['id_owner'];

  $attackFleets = array();
  // ACS function: put all fleet into an array
  if ($fleet_row['fleet_group'] != 0)
  {
    $fleets = doquery('SELECT * FROM {{fleets}} WHERE fleet_group='.$fleet_row['fleet_group']);
    while ($fleet = mysql_fetch_assoc($fleets))
    {
      BE_attackFleetFill(&$attackFleets, $fleet);
    }
  }
  else
  {
    BE_attackFleetFill(&$attackFleets, $fleet_row);
  }

  $db_admiral_name = $sn_data[MRC_ADMIRAL]['name'];

  $defenseFleets = array(
    0 => array(
      'def' => array(),
      'user' => array(
        'id'             => $destination_user['id'],
        'username'       => $destination_user['username'],
        $db_admiral_name => $destination_user[$db_admiral_name],
        'defence_tech'   => $destination_user['defence_tech'],
        'shield_tech'    => $destination_user['shield_tech'],
        'military_tech'  => $destination_user['military_tech'],
      ),
    )
  );

  foreach($sn_data['groups']['combat'] as $combatUnitID)
  {
    if ($destination_planet[$resource[$combatUnitID]] > 0)
    {
      $defenseFleets[0]['def'][$combatUnitID] = $destination_planet[$resource[$combatUnitID]];
    }
  }

  $fleets = doquery('SELECT * FROM {{fleets}} WHERE `fleet_end_galaxy` = '. $fleet_row['fleet_end_galaxy'] .' AND `fleet_end_system` = '. $fleet_row['fleet_end_system'] .' AND `fleet_end_planet` = '. $fleet_row['fleet_end_planet'] . ' AND `fleet_end_type` = '. $fleet_row['fleet_end_type'] .' AND fleet_start_time<'.$time_now.' AND fleet_end_stay>='.$time_now);
  while ($fleet = mysql_fetch_assoc($fleets))
  {
    BE_attackFleetFill(&$defenseFleets, $fleet, 'def');
  }

  $start = microtime(true);
  $result = coe_attack_calculate($attackFleets, $defenseFleets);
  $totaltime = microtime(true) - $start;


  // Update galaxy (debree)
  if ($destination_user['authlevel'] == 0) {
    doquery('UPDATE {{planets}} SET `debris_metal` = `debris_metal` + '.($result['debree']['att'][0]+$result['debree']['def'][0]).' , debris_crystal = debris_crystal+'.($result['debree']['att'][1]+$result['debree']['def'][1]).' WHERE `galaxy` = '. $fleet_row['fleet_end_galaxy'] .' AND `system` = '. $fleet_row['fleet_end_system'] .' AND `planet` = '. $fleet_row['fleet_end_planet'] . ' AND `planet_type` = 1');
  };

  // !G+ post-calculation for Attackers: fleet left and possible loot
  $loot = BE_calculatePostAttacker($destination_planet, $attackFleets, $result, false);

  // Update fleets & planets
  foreach ($attackFleets as $fleetID => $attacker)
  {
    if ($attacker['totalCount'] > 0)
    {
      $sqlQuery  = 'UPDATE {{fleets}} SET ';
      if ($result['won'] == 1)
      {
        $sqlQuery .= '`fleet_resource_metal` = `fleet_resource_metal` + '. ($attacker['loot']['metal'] + 0) .', ';
        $sqlQuery .= '`fleet_resource_crystal` = `fleet_resource_crystal` + '. ($attacker['loot']['crystal'] + 0) .', ';
        $sqlQuery .= '`fleet_resource_deuterium` = `fleet_resource_deuterium` + '. ($attacker['loot']['deuterium'] + 0) .', ';
      }

      $sqlQuery .= '`fleet_array` = "'.substr($attacker['fleetArray'], 0, -1).'", ';
      $sqlQuery .= '`fleet_amount` = ' . $attacker['totalCount'] . ', `fleet_mess` = 1 WHERE `fleet_id` = '.$fleetID;
      doquery($sqlQuery);
    }
  }

  if($fleet_row['fleet_mission'] == MT_AKS && $fleet_row['fleet_group'])
  {
    doquery("DELETE FROM {{aks}} WHERE id={$fleet_row['fleet_group']} LIMIT 1;");
    doquery("UPDATE {{fleets}} SET fleet_group = 0 WHERE fleet_group = {$fleet_row['fleet_group']} AND fleet_mission = " . MT_AKS . ";");
  };

  foreach ($defenseFleets as $fleetID => $defender)
  {
    $fleetArray = '';
    $totalCount = 0;

    if ($fleetID == 0)
    {
      foreach ($defender['def'] as $element => $amount)
      {
        $fleetArray .= '`'.$resource[$element].'`='.$amount.', ';
      }
      doquery('UPDATE {{planets}} SET '.$fleetArray.' metal=metal-'.$loot['looted']['metal'].', crystal=crystal-'.$loot['looted']['crystal'].', deuterium=deuterium-'.$loot['looted']['deuterium'].' WHERE id='.$destination_planet['id']);
    }
    else
    {
      foreach ($defender['def'] as $element => $amount)
      {
        if ($amount)
        {
          $fleetArray .= $element.','.$amount.';';
        }
        $totalCount += $amount;
      }
      if ($totalCount <= 0)
      {
        doquery ("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleetID}'");
      }
      else
      {
        doquery("UPDATE {{fleets}} SET fleet_array = '{$fleetArray}', fleet_amount = {$totalCount}, fleet_mess = 1 WHERE fleet_id = {$fleetID}");
      }
    }
  }
  // TvdW (c) 2008

  // FROM HERE THE SCRIPT WAS IMPORTED (not TvdW code anymore)
  $StrAttackerUnits = sprintf ($lang['sys_attacker_lostunits'], $result['lost']['att']);
  $StrDefenderUnits = sprintf ($lang['sys_defender_lostunits'], $result['lost']['def']);
  $StrRuins         = sprintf ($lang['sys_gcdrunits'], $result['debree']['def'][0] + $result['debree']['att'][0], $lang['Metal'], $result['debree']['def'][1] + $result['debree']['att'][1], $lang['Crystal']);
  $DebrisField      = $StrAttackerUnits ."<br />". $StrDefenderUnits ."<br />". $StrRuins;

  $MoonChance = BE_calculateMoonChance($result);

  if ( (mt_rand(1, 100) <= $MoonChance) && ($galenemyrow['id_luna'] == 0) )
  {
    $TargetPlanetName = uni_create_moon ( $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'], $TargetUserID, $MoonChance);
    $GottenMoon       = sprintf ($lang['sys_moonbuilt'], $TargetPlanetName, $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);
  }

  // Adjust number of raids made/win/loose and xpraid
  $QryUpdateRaidsCompteur = "UPDATE {{users}} SET `xpraid` = `xpraid` + 1, `raids` = `raids` + 1, ";
  if ($result['won'] == 1)
  {
    $QryUpdateRaidsCompteur .= "`raidswin` = `raidswin` + 1 ";
  }
  elseif ($result['won'] == 2 || $result['won'] == 0)
  {
    $QryUpdateRaidsCompteur .= "`raidsloose` = `raidsloose` + 1 ";
  }
  $QryUpdateRaidsCompteur .= "WHERE id = '{$fleet_row['fleet_owner']}' LIMIT 1;";
  doquery($QryUpdateRaidsCompteur);

  //MadnessRed CR Creation.
  $formatted_cr = formatCR($result,$loot['looted'],$MoonChance,$GottenMoon,$totaltime);
  $raport = $formatted_cr['html'];

  $bashing_list = array();
  foreach ($defenseFleets as $fleetID => $defender)
  {
    $users2[$defender['user']['id']] = $defender['user']['id'];
  }

  foreach ($attackFleets as $fleetID => $attacker)
  {
    $users2[$attacker['user']['id']] = $attacker['user']['id'];
    // Generating attackers list for bashing table
    $bashing_list[$attacker['user']['id']] = "({$attacker['user']['id']}, {$destination_planet['id']}, {$fleet['fleet_end_time']})";
  }
  $bashing_list = implode(',', $bashing_list);
  doquery("INSERT INTO {{bashing}} (bashing_user_id, bashing_planet_id, bashing_time) VALUES {$bashing_list};");

  $rid = md5($raport);
  $QryInsertRapport  = 'INSERT INTO `{{rw}}` SET ';
  $QryInsertRapport .= '`time` = UNIX_TIMESTAMP(), ';
  $QryInsertRapport .= '`owners` = "'.implode(',', $users2).'", ';
  $QryInsertRapport .= '`id_owner1` = "'.$attacker['user']['id'].'", ';
  $QryInsertRapport .= '`id_owner2` = "'.$defender['user']['id'].'", ';
  $QryInsertRapport .= '`rid` = "'. $rid .'", ';
  $QryInsertRapport .= '`raport` = "'. mysql_real_escape_string( $raport ) .'"';
  doquery($QryInsertRapport) or die("Error inserting CR to database".mysql_error()."<br /><br />Trying to execute:".mysql_query());

  // Colorize report.
  $raport  = '<span OnClick=\'f( "rw.php?raport='. $rid .'", "");\' >';
  $raport .= '<center>';
  if ($result['won'] == 1)
  {
    $raport .= '<font color=\'green\'>';
  }
  elseif ($result['won'] == 0)
  {
    $raport .= '<font color=\'orange\'>';
  }
  elseif ($result['won'] == 2)
  {
    $raport .= '<font color=\'red\'>';
  }
  $raport .= $lang['sys_mess_attack_report'] .' ['. $fleet_row['fleet_end_galaxy'] .':'. $fleet_row['fleet_end_system'] .':'. $fleet_row['fleet_end_planet'] .'] </font></span><br /><br />';
  $raport .= '<font color=\'red\'>'. $lang['sys_perte_attaquant'] .': '. $result['lost']['att'] .'</font>';
  $raport .= '<font color=\'green\'>   '. $lang['sys_perte_defenseur'] .': '. $result['lost']['def'] .'</font><br />' ;
  $raport .= $lang['sys_gain'] .' '. $lang['Metal'] .':<font color=\'#adaead\'>'. $loot['looted']['metal'] .'</font>   '. $lang['Crystal'] .':<font color=\'#ef51ef\'>'. $loot['looted']['crystal'] .'</font>   '. $lang['Deuterium'] .':<font color=\'#f77542\'>'. $loot['looted']['deuterium'] .'</font><br />';
  $raport .= $lang['sys_debris'] .' '. $lang['Metal'] .': <font color=\'#adaead\'>'. ($result['debree']['att'][0]+$result['debree']['def'][0]) .'</font>   '. $lang['Crystal'] .': <font color=\'#ef51ef\'>'. ($result['debree']['att'][1]+$result['debree']['def'][1]) .'</font><br /></center>';

  $raport .= $st_1 . $st_2;

  msg_send_simple_message ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $raport );

  // Coloriize report.
  $raport2  = '<span OnClick=\'f( "rw.php?raport='. $rid .'", "");\' >';
  $raport2 .= '<center>';
  if       ($result['won'] == 1)
  {
    $raport2 .= '<font color=\'green\'>';
  }
  elseif ($result['won'] == 0)
  {
    $raport2 .= '<font color=\'orange\'>';
  }
  elseif ($result['won'] == 2)
  {
    $raport2 .= '<font color=\'red\'>';
  }
  $raport2 .= $lang['sys_mess_attack_report'] .' ['. $fleet_row['fleet_end_galaxy'] .':'. $fleet_row['fleet_end_system'] .':'. $fleet_row['fleet_end_planet'] .'] </font></span><br /><br />';

  $raport2 .= $st_1 . $st_2;

  foreach ($users2 as $id)
  {
    if ($id != $fleet_row['fleet_owner'] && $id != 0)
    {
      msg_send_simple_message ( $id, '', $fleet_row['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $raport2 );
    }
  }

  return $result;
}

// MadnessRed 2008
?>
