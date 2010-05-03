<?php

  /**
   * This file is under the GPL liscence, which must be included with the file under distrobution (license.txt)
   * this file was made by Xnova, edited to support Toms combat engine by Anthony (MadnessReD) [http://madnessred.co.cc/]
   * Do not edit this comment block
   */

  /*
  *
  * Partial copyright (c) 2009 by Gorlum for oGame.triolan.com.ua
  *  Additions marks with "// !G+"
  *  [*] Includes MissionCaseEvoAttack.php from 'includes' directory
  *  [+] Loot algorithm - No more stinky RANMA code!
  *  [*] Heavy db query optimization (6 queries less!)
  *  [*] time() -> $time_now - no more duplicate fleet attack or lost fleet attack on heavy loaded servers!
  */

function MissionCaseAttack ( $FleetRow) {
  global $phpEx, $ugamela_root_path, $pricelist, $lang, $resource, $CombatCaps, $game_config, $debug;

  // !G+ on heavy loaded server several time() in a row can give different results with unpredicted outcome
  // $time_now = time();
  global $time_now;

  if ($FleetRow['fleet_mess'] == 0 && $FleetRow['fleet_start_time'] <= $time_now) {

    if (!isset($CombatCaps[202]['sd'])) {
      message('<font color=red>'. $lang['sys_no_vars'] .'</font><br />(Error: <font color=red>(!isset($pricelist[202][\'sd\']))</font>. Please report this to an admin.)', $lang['sys_error'], 'fleet.php', 15);
    }

    // TvdW (c) 2008
    $TargetPlanet = doquery('SELECT * FROM {{table}} WHERE `galaxy` = '. $FleetRow['fleet_end_galaxy'] .' AND `system` = '. $FleetRow['fleet_end_system'] .' AND `planet_type` = '. $FleetRow['fleet_end_type'] .' AND `planet` = '. $FleetRow['fleet_end_planet'] .';','planets', true);

    if (!isset($TargetPlanet['id'])) {
      if ($FleetRow['fleet_group'] > 0) {
        //MadnessRed Code
        doquery('DELETE FROM {{table}} WHERE `fleet_group` ='.$FleetRow['fleet_group'],'aks');
        doquery('UPDATE {{table}} SET `fleet_mess` = 1 WHERE `fleet_group` ='.$FleetRow['fleet_group'],'fleets');
      } else {
        doquery('UPDATE {{table}} SET `fleet_mess` = 1 WHERE `fleet_id` ='.$FleetRow['fleet_id'],'fleets');
      }
      return;
    }

    // !G+ Nothing else used from $CurrentUser - will not execute this Q
    $CurrentUserID = $FleetRow['fleet_owner'];

    $TargetUser   = doquery('SELECT * FROM {{table}} WHERE `id` ='.$TargetPlanet['id_owner'],'users', true);
    $TargetUserID = $TargetPlanet['id_owner'];
    PlanetResourceUpdate ( $TargetUser, $TargetPlanet, $time_now );

    // ACS function: put all fleet into an array
    $attackFleets = array(); // attackFleets[id] = array('fleet' => $FleetRow, 'user' => $user);

    // !G+ Here is the place for optimization - always create fleet AKS and put one fleet to it
    if ($FleetRow['fleet_group'] != 0) {
      $fleets = doquery('SELECT * FROM {{table}} WHERE fleet_group='.$FleetRow['fleet_group'],'fleets');
      while ($fleet = mysql_fetch_assoc($fleets)) {
        $attackFleets[$fleet['fleet_id']]['fleet'] = $fleet;

        // !G+ We only need id, techlevels and rpg_amiral - why query whole row?
        // $attackFleets[$fleet['fleet_id']]['user'] = doquery('SELECT * FROM {{table}} WHERE id='.$fleet['fleet_owner'],'users', true);
        $attackFleets[$fleet['fleet_id']]['user'] = doquery('SELECT id, username, defence_tech, rpg_amiral, shield_tech, military_tech FROM {{table}} WHERE id='.$fleet['fleet_owner'],'users', true);

        $attackFleets[$fleet['fleet_id']]['detail'] = array();
        $temp = explode(';', $fleet['fleet_array']);
        foreach ($temp as $temp2) {
          $temp2 = explode(',', $temp2);

          if ($temp2[0] < 100) continue;

          if (!isset($attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]]))
            $attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]] = 0;
          $attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]] += $temp2[1];
        }
      }
    } else {
      $attackFleets[$FleetRow['fleet_id']]['fleet'] = $FleetRow;

      // !G+ We only need id, techlevels and rpg_amiral - why query whole row?
      // $attackFleets[$FleetRow['fleet_id']]['user'] = doquery('SELECT * FROM {{table}} WHERE id='.$FleetRow['fleet_owner'],'users', true);
      $attackFleets[$FleetRow['fleet_id']]['user'] = doquery('SELECT id, username, defence_tech, rpg_amiral, shield_tech, military_tech FROM {{table}} WHERE id='.$FleetRow['fleet_owner'],'users', true);

      $attackFleets[$FleetRow['fleet_id']]['detail'] = array();
      $temp = explode(';', $FleetRow['fleet_array']);
      foreach ($temp as $temp2) {
        $temp2 = explode(',', $temp2);

        if ($temp2[0] < 100) continue;

        if (!isset($attackFleets[$FleetRow['fleet_id']]['detail'][$temp2[0]]))
          $attackFleets[$FleetRow['fleet_id']]['detail'][$temp2[0]] = 0;
        $attackFleets[$FleetRow['fleet_id']]['detail'][$temp2[0]] += $temp2[1];
      }
    }

    $defenseFleets = array();
    $def = doquery('SELECT * FROM {{table}} WHERE `fleet_end_galaxy` = '. $FleetRow['fleet_end_galaxy'] .' AND `fleet_end_system` = '. $FleetRow['fleet_end_system'] .' AND `fleet_end_type` = '. $FleetRow['fleet_end_type'] .' AND `fleet_end_planet` = '. $FleetRow['fleet_end_planet'] .' AND fleet_start_time<'.$time_now.' AND fleet_end_stay>='.$time_now,'fleets');
    while ($defRow = mysql_fetch_assoc($def)) {
      $defRowDef = explode(';', $defRow['fleet_array']);
      foreach ($defRowDef as $Element) {
        $Element = explode(',', $Element);

        if ($Element[0] < 100) continue;

        if (!isset($defenseFleets[$defRow['fleet_id']]['def'][$Element[0]]))
          $defenseFleets[$defRow['fleet_id']][$Element[0]] = 0;
        $defenseFleets[$defRow['fleet_id']]['def'][$Element[0]] += $Element[1];

        // !G+ We only need id, techlevels and rpg_amiral - why query whole row?
        // $defenseFleets[$defRow['fleet_id']]['user'] = doquery('SELECT * FROM {{table}} WHERE id='.$defRow['fleet_owner'],'users', true);
        $defenseFleets[$defRow['fleet_id']]['user'] = doquery('SELECT id, defence_tech, rpg_amiral, shield_tech, military_tech FROM {{table}} WHERE id='.$defRow['fleet_owner'],'users', true);
      }
    }

    $defenseFleets[0]['def'] = array();
    $defenseFleets[0]['user'] = $TargetUser;
    for ($i = 200; $i < 500; $i++) {
      if (isset($resource[$i]) && isset($TargetPlanet[$resource[$i]])) {
        $defenseFleets[0]['def'][$i] = $TargetPlanet[$resource[$i]];
      }
    }

    //Debug
    //echo "<font color=\"red\">A combat report has been generated. Please post any errors below on the forums. Thanks</font><br />";

    $start = microtime(true);
    $result = calculateAttack($attackFleets, $defenseFleets);
    $totaltime = microtime(true) - $start;

    // Update galaxy (debree)
    if ($TargetUser['authlevel'] == 0) {
      $sqlQuery = 'UPDATE {{table}} SET metal=metal+'.($result['debree']['att'][0]+$result['debree']['def'][0]).' , crystal=crystal+'.($result['debree']['att'][1]+$result['debree']['def'][1]).' WHERE `galaxy` = '. $FleetRow['fleet_end_galaxy'] .' AND `system` = '. $FleetRow['fleet_end_system'] .' AND `planet` = '. $FleetRow['fleet_end_planet'];
      doquery($sqlQuery,'galaxy');
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
      doquery("DELETE FROM {{table}} WHERE id={$FleetRow['fleet_group']}", 'aks');
    };

    foreach ($defenseFleets as $fleetID => $defender) {
      $fleetArray = '';
      $totalCount = 0;

      if ($fleetID == 0) {
        foreach ($defender['def'] as $element => $amount) {
          $fleetArray .= '`'.$resource[$element].'`='.$amount.', ';
        }
        $sqlQuery = 'UPDATE {{table}} SET '.$fleetArray.' metal=metal-'.$loot['looted']['metal'].', crystal=crystal-'.$loot['looted']['crystal'].', deuterium=deuterium-'.$loot['looted']['deuterium'].' WHERE id='.$TargetPlanet['id'];
        doquery($sqlQuery,'planets');
      } else {
        foreach ($defender['def'] as $element => $amount) {
          if ($amount)
            $fleetArray .= $element.','.$amount.';';
          $totalCount += $amount;
        }
        if ($totalCount <= 0) {
          doquery ('DELETE FROM {{table}} WHERE `fleet_id`='.$fleetID,'fleets');
        } else {
          $sqlQuery = 'UPDATE {{table}} SET fleet_array="'.$fleetArray.'", fleet_amount='.$totalCount.', fleet_mess=1 WHERE fleet_id='.$fleetID;
          doquery($sqlQuery,'fleets');
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
          if       ($result['won'] == 1) {
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

  } elseif ($FleetRow['fleet_end_time'] <= $time_now) {
    RestoreFleetToPlanet($FleetRow);
    doquery ('DELETE FROM {{table}} WHERE `fleet_id`='.$FleetRow['fleet_id'],'fleets');
  }
}
// MadnessRed 2008
?>
