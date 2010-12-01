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

function MissionCaseAttack ( $FleetRow) {
  global $phpEx, $ugamela_root_path, $pricelist, $lang, $resource, $CombatCaps, $debug, $time_now, $reslist, $sn_data;

  // --- This is universal part which should be moved to fleet manager
  // Checking fleet message: if not 0 then we already managed this fleet
  if($FleetRow['fleet_mess'] != 0) {
    // Checking fleet end_time: if less then time_now then restoring fleet to planet
    if($FleetRow['fleet_end_time'] <= $time_now) {
      RestoreFleetToPlanet($FleetRow);
      doquery ('DELETE FROM {{table}} WHERE `fleet_id`='.$FleetRow['fleet_id'],'fleets');
    }
    return;
  }

  // Checking if we should now to proceed this fleet - does it arrive? No - exiting.
  if ($FleetRow['fleet_start_time'] > $time_now) return;

  // Misc checking for missing fleet data. Why we need it anyway?!
  if (!isset($CombatCaps[202]['sd']))
     message("<font color=\"red\">". $lang['sys_no_vars'] ."</font>", $lang['sys_error'], "fleet." . $phpEx, 2);
  // Using to get ownerID, lunaID from PLANETS table and list of resources
  $TargetPlanet = doquery('SELECT * FROM {{table}} WHERE ' .
         '`galaxy` = '. $FleetRow['fleet_end_galaxy'] .
    ' AND `system` = '. $FleetRow['fleet_end_system'] .
    ' AND `planet` = '. $FleetRow['fleet_end_planet'] .
    ' AND `planet_type` = '. $FleetRow['fleet_end_type'] .';',
  'planets', true);

  if (!isset($TargetPlanet['id'])) {
    if ($FleetRow['fleet_group'] > 0) {
      doquery("DELETE FROM {{aks}} WHERE `id` ='{$FleetRow['fleet_group']}';");
      doquery('UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_group` ='.$FleetRow['fleet_group']);
    } else {
      doquery('UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` ='.$FleetRow['fleet_id']);
    }
    return;
  }
  // --- End of Universal part

  $CurrentUserID = $FleetRow['fleet_owner'];
  $TargetUserID  = $TargetPlanet['id_owner'];

  $TargetUser    = doquery('SELECT * FROM {{table}} WHERE `id` ='.$TargetPlanet['id_owner'],'users', true);

  UpdatePlanetBatimentQueueList($TargetPlanet, $TargetUser);
  PlanetResourceUpdate( $TargetUser, $TargetPlanet, $time_now );

  $attackFleets = array();
  // ACS function: put all fleet into an array
  if ($FleetRow['fleet_group'] != 0) {
    $fleets = doquery('SELECT * FROM {{table}} WHERE fleet_group='.$FleetRow['fleet_group'],'fleets');
    while ($fleet = mysql_fetch_assoc($fleets))
      BE_attackFleetFill(&$attackFleets, $fleet);
  } else {
    BE_attackFleetFill(&$attackFleets, $FleetRow);
  }

  $db_admiral_name = $sn_data[MRC_ADMIRAL]['name'];

  $defenseFleets = array(
    0 => array(
      'def' => array(),
      'user' => array(
        'id'             => $TargetUser['id'],
        'username'       => $TargetUser['username'],
        $db_admiral_name => $TargetUser[$db_admiral_name],
        'defence_tech'   => $TargetUser['defence_tech'],
        'shield_tech'    => $TargetUser['shield_tech'],
        'military_tech'  => $TargetUser['military_tech'],
      ),
    )
  );

  foreach($reslist['combat'] as $combatUnitID)
    if ($TargetPlanet[$resource[$combatUnitID]] > 0)
      $defenseFleets[0]['def'][$combatUnitID] = $TargetPlanet[$resource[$combatUnitID]];

  $fleets = doquery('SELECT * FROM {{table}} WHERE `fleet_end_galaxy` = '. $FleetRow['fleet_end_galaxy'] .' AND `fleet_end_system` = '. $FleetRow['fleet_end_system'] .' AND `fleet_end_planet` = '. $FleetRow['fleet_end_planet'] . ' AND `fleet_end_type` = '. $FleetRow['fleet_end_type'] .' AND fleet_start_time<'.$time_now.' AND fleet_end_stay>='.$time_now,'fleets');
  while ($fleet = mysql_fetch_assoc($fleets))
    BE_attackFleetFill(&$defenseFleets, $fleet, 'def');

  $start = microtime(true);
  $result = calculateAttack($attackFleets, $defenseFleets);
  $totaltime = microtime(true) - $start;

  // Update galaxy (debree)
  if ($TargetUser['authlevel'] == 0) {
    doquery('UPDATE {{planets}} SET `debris_metal` = `debris_metal` + '.($result['debree']['att'][0]+$result['debree']['def'][0]).' , debris_crystal = debris_crystal+'.($result['debree']['att'][1]+$result['debree']['def'][1]).' WHERE `galaxy` = '. $FleetRow['fleet_end_galaxy'] .' AND `system` = '. $FleetRow['fleet_end_system'] .' AND `planet` = '. $FleetRow['fleet_end_planet'] . ' AND `planet_type` = 1');
  };

  // !G+ post-calculation for Attackers: fleet left and possible loot
  $loot = BE_calculatePostAttacker($TargetPlanet, $attackFleets, $result, false);

  // Update fleets & planets
  foreach ($attackFleets as $fleetID => $attacker) {
    if ($attacker['totalCount'] > 0) {
      $sqlQuery  = 'UPDATE {{table}} SET ';
      if ($result['won'] == 1) {
        $sqlQuery .= '`fleet_resource_metal` = `fleet_resource_metal` + '. ($attacker['loot']['metal'] + 0) .', ';
        $sqlQuery .= '`fleet_resource_crystal` = `fleet_resource_crystal` + '. ($attacker['loot']['crystal'] + 0) .', ';
        $sqlQuery .= '`fleet_resource_deuterium` = `fleet_resource_deuterium` + '. ($attacker['loot']['deuterium'] + 0) .', ';
      }

      $sqlQuery .= '`fleet_array` = "'.substr($attacker['fleetArray'], 0, -1).'", ';
      $sqlQuery .= '`fleet_amount` = ' . $attacker['totalCount'] . ', `fleet_mess` = 1 WHERE `fleet_id` = '.$fleetID;
      doquery($sqlQuery, 'fleets');
    }
  }

  if($FleetRow['mission_type']==MT_AKS AND $FleetRow['fleet_group']!=0){
    doquery("DELETE FROM {{aks}} WHERE id={$FleetRow['fleet_group']}");
  };

  foreach ($defenseFleets as $fleetID => $defender) {
    $fleetArray = '';
    $totalCount = 0;

    if ($fleetID == 0) {
      foreach ($defender['def'] as $element => $amount) {
        $fleetArray .= '`'.$resource[$element].'`='.$amount.', ';
      }
      doquery('UPDATE {{planets}} SET '.$fleetArray.' metal=metal-'.$loot['looted']['metal'].', crystal=crystal-'.$loot['looted']['crystal'].', deuterium=deuterium-'.$loot['looted']['deuterium'].' WHERE id='.$TargetPlanet['id']);
    } else {
      foreach ($defender['def'] as $element => $amount) {
        if ($amount)
          $fleetArray .= $element.','.$amount.';';
        $totalCount += $amount;
      }
      if ($totalCount <= 0) {
        doquery ("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleetID}'");
      } else {
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

  if ( (mt_rand(1, 100) <= $MoonChance) && ($galenemyrow['id_luna'] == 0) ){
    $TargetPlanetName = CreateOneMoonRecord ( $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $TargetUserID, $FleetRow['fleet_start_time'], '', $MoonChance );
    $GottenMoon       = sprintf ($lang['sys_moonbuilt'], $TargetPlanetName, $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
  }

  //MadnessRed CR Creation.
  $formatted_cr = formatCR($result,$loot['looted'],$MoonChance,$GottenMoon,$totaltime);
  $raport = $formatted_cr['html'];


  $rid   = md5($raport);
  $QryInsertRapport  = 'INSERT INTO `{{table}}` SET ';
  $QryInsertRapport .= '`time` = UNIX_TIMESTAMP(), ';
  foreach ($attackFleets as $fleetID => $attacker) {
    $users2[$attacker['user']['id']] = $attacker['user']['id'];
  }
  foreach ($defenseFleets as $fleetID => $defender) {
    $users2[$defender['user']['id']] = $defender['user']['id'];
  }
  $QryInsertRapport .= '`owners` = "'.implode(',', $users2).'", ';
  $QryInsertRapport .= '`id_owner1` = "'.$attacker['user']['id'].'", ';
  $QryInsertRapport .= '`id_owner2` = "'.$defender['user']['id'].'", ';
  $QryInsertRapport .= '`rid` = "'. $rid .'", ';
  $QryInsertRapport .= '`raport` = "'. mysql_real_escape_string( $raport ) .'"';
  doquery($QryInsertRapport,'rw') or die("Error inserting CR to database".mysql_error()."<br /><br />Trying to execute:".mysql_query());

  // Colorize report.
  $raport  = '<a href # OnClick=\'f( "rw.php?raport='. $rid .'", "");\' >';
  $raport .= '<center>';
  if ($result['won'] == 1) {
    $raport .= '<font color=\'green\'>';
  } elseif ($result['won'] == 0) {
    $raport .= '<font color=\'orange\'>';
  } elseif ($result['won'] == 2) {
    $raport .= '<font color=\'red\'>';
  }
  $raport .= $lang['sys_mess_attack_report'] .' ['. $FleetRow['fleet_end_galaxy'] .':'. $FleetRow['fleet_end_system'] .':'. $FleetRow['fleet_end_planet'] .'] </font></a><br /><br />';
  $raport .= '<font color=\'red\'>'. $lang['sys_perte_attaquant'] .': '. $result['lost']['att'] .'</font>';
  $raport .= '<font color=\'green\'>   '. $lang['sys_perte_defenseur'] .': '. $result['lost']['def'] .'</font><br />' ;
  $raport .= $lang['sys_gain'] .' '. $lang['Metal'] .':<font color=\'#adaead\'>'. $loot['looted']['metal'] .'</font>   '. $lang['Crystal'] .':<font color=\'#ef51ef\'>'. $loot['looted']['crystal'] .'</font>   '. $lang['Deuterium'] .':<font color=\'#f77542\'>'. $loot['looted']['deuterium'] .'</font><br />';
  $raport .= $lang['sys_debris'] .' '. $lang['Metal'] .': <font color=\'#adaead\'>'. ($result['debree']['att'][0]+$result['debree']['def'][0]) .'</font>   '. $lang['Crystal'] .': <font color=\'#ef51ef\'>'. ($result['debree']['att'][1]+$result['debree']['def'][1]) .'</font><br /></center>';

  $raport .= $st_1 . $st_2;

  SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $raport );

  // Coloriize report.
  $raport2  = '<a href # OnClick=\'f( "rw.php?raport='. $rid .'", "");\' >';
  $raport2 .= '<center>';
  if       ($result['won'] == 1) {
    $raport2 .= '<font color=\'green\'>';
  } elseif ($result['won'] == 0) {
    $raport2 .= '<font color=\'orange\'>';
  } elseif ($result['won'] == 2) {
    $raport2 .= '<font color=\'red\'>';
  }
  $raport2 .= $lang['sys_mess_attack_report'] .' ['. $FleetRow['fleet_end_galaxy'] .':'. $FleetRow['fleet_end_system'] .':'. $FleetRow['fleet_end_planet'] .'] </font></a><br /><br />';

  $raport2 .= $st_1 . $st_2;

  foreach ($users2 as $id) {
    if ($id != $FleetRow['fleet_owner'] && $id != 0) {
      SendSimpleMessage ( $id, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $raport2 );
    }
  }

  // Adjust number of raids made/win/loose and xpraid
  $QryUpdateRaidsCompteur = "UPDATE {{table}} SET ";
  $QryUpdateRaidsCompteur .= "`xpraid` = `xpraid` + 1, ";
  $QryUpdateRaidsCompteur .= "`raids` = `raids` + 1, ";
  if ($result['won'] == 1) {
    $QryUpdateRaidsCompteur .= "`raidswin` = `raidswin` + 1 ";
  } elseif ($result['won'] == 2 || $result['won'] == 0) {
    $QryUpdateRaidsCompteur .= "`raidsloose` = `raidsloose` + 1 ";
  }
  $QryUpdateRaidsCompteur .= "WHERE id = '" . $CurrentUserID . "' ";
  $QryUpdateRaidsCompteur .= "LIMIT 1 ;";
  doquery($QryUpdateRaidsCompteur, 'users');

  return $result;
}
// MadnessRed 2008
?>
