<?php

/**
 * MissionCaseSpy.php
 *
 * @version 1
 * @copyright 2008
 */
// ----------------------------------------------------------------------------------------------------------------
// Mission Case 6: -> Espionner
//

/**
 * SpyTarget
 *
 * @version 1
 * @copyright 2008
 */

// ----------------------------------------------------------------------------------------------------------------
//
// SpyTarget
//
// $target_planet -> Enregistrement 'planet' de la base de donnees
// $Mode         -> Ce que l'on va notifier
//                  0 -> Ressources, 1 -> Flotte, 2 ->Defenses, 3 -> Batiments, 4 -> Technologies
// $TitleString  -> Chaine definissant le titre ou la parcelle de titre a afficher
function SpyTarget ( $target_planet, $Mode, $TitleString, $TargetUsername="" ) {
  global $lang, $resource;

  $LookAtLoop = true;
  if       ($Mode == 0) {
    $String  = "<table width=\"440\"><tr><td class=\"c\" colspan=\"5\">";
    $String .= $TitleString ." ". $target_planet['name'];
    $String .= " <a href=\"galaxy.php?mode=3&galaxy=". $target_planet["galaxy"] ."&system=". $target_planet["system"]. "\">";
    $String .= "[". $target_planet["galaxy"] .":". $target_planet["system"] .":". $target_planet["planet"] ."]</a>";
    $String .= " (".$lang['Player_']." '".$TargetUsername."') ".$lang['On_']." ". date(FMT_DATE_TIME, time() + 2 * 60 * 60) ."</td>";
    $String .= "</tr><tr>";
    $String .= "<td width=220>". $lang['Metal']     .":</td><td width=220 align=right>". pretty_number($target_planet['metal'])      ."</td><td>&nbsp;</td>";
    $String .= "<td width=220>". $lang['Crystal']   .":</td></td><td width=220 align=right>". pretty_number($target_planet['crystal'])    ."</td>";
    $String .= "</tr><tr>";
    $String .= "<td width=220>". $lang['Deuterium'] .":</td><td width=220 align=right>". pretty_number($target_planet['deuterium'])  ."</td><td>&nbsp;</td>";
    $String .= "<td width=220>". $lang['Energy']    .":</td><td width=220 align=right>". pretty_number($target_planet['energy_max']) ."</td>";
    $String .= "</tr>";
    $LookAtLoop = false;
  } elseif ($Mode == 1) {
    $ResFrom[0] = 200;
    $ResTo[0]   = 299;
    $Loops      = 1;
  } elseif ($Mode == 2) {
    $ResFrom[0] = 400;
    $ResTo[0]   = 499;
    $ResFrom[1] = 500;
    $ResTo[1]   = 599;
    $Loops      = 2;
  } elseif ($Mode == 3) {
    $ResFrom[0] = 1;
    $ResTo[0]   = 99;
    $Loops      = 1;
  } elseif ($Mode == 4) {
    $ResFrom[0] = 100;
    $ResTo[0]   = 199;
    $Loops      = 1;
  }

  if ($LookAtLoop == true) {
    $String  = "<table width=\"440\" cellspacing=\"1\"><tr><td class=\"c\" colspan=\"". ((2 * SPY_REPORT_ROW) + (SPY_REPORT_ROW - 1))."\">". $TitleString ."</td></tr>";
    $Count       = 0;
    $CurrentLook = 0;
    while ($CurrentLook < $Loops) {
      $row     = 0;
      for ($Item = $ResFrom[$CurrentLook]; $Item <= $ResTo[$CurrentLook]; $Item++) {
        if ( $target_planet[$resource[$Item]] > 0) {
          if ($row == 0) {
            $String  .= "<tr>";
          }
          $String  .= "<td align=left>".$lang['tech'][$Item]."</td><td align=right>".$target_planet[$resource[$Item]]."</td>";
          if ($row < SPY_REPORT_ROW - 1) {
            $String  .= "<td>&nbsp;</td>";
          }
          $Count   += $target_planet[$resource[$Item]];
          $row++;
          if ($row == SPY_REPORT_ROW) {
            $String  .= "</tr>";
            $row      = 0;
          }
        }
      }

      while ($row != 0) {
        $String  .= "<td>&nbsp;</td><td>&nbsp;</td>";
        $row++;
        if ($row == SPY_REPORT_ROW) {
          $String  .= "</tr>";
          $row      = 0;
        }
      }
      $CurrentLook++;
    } // while
  }
  $String .= "</table>";

  $return['String'] = $String;
  $return['Count']  = $Count;
  return $return;
}

function MissionCaseSpy ( $fleet_row ) {
  global $time_now;

  // --- This is universal part which should be moved to fleet manager
  // Checking if we should now to proceed this fleet - does it arrive? No - exiting.
  if ($fleet_row['fleet_start_time'] > $time_now) return false;

  // Checking fleet message: if not 0 then we already managed this fleet
  if($fleet_row['fleet_mess'] != 0) {
    // Checking fleet end_time: if less then time_now then restoring fleet to planet
    if($fleet_row['fleet_end_time'] <= $time_now) {
      RestoreFleetToPlanet($fleet_row);
    }
    return false;
  }

  // Using to get ownerID, lunaID from PLANETS table and list of resources
  $target_planet = doquery('SELECT * FROM {{planets}} WHERE ' .
         '`galaxy` = '. $fleet_row['fleet_end_galaxy'] .
    ' AND `system` = '. $fleet_row['fleet_end_system'] .
    ' AND `planet` = '. $fleet_row['fleet_end_planet'] .
    ' AND `planet_type` = '. $fleet_row['fleet_end_type'] .' LIMIT 1;',
  '', true);

  if (!$target_planet || !isset($target_planet['id'])) {
    if ($fleet_row['fleet_group'] > 0) {
      doquery("DELETE FROM {{aks}} WHERE `id` ='{$fleet_row['fleet_group']}';");
      doquery('UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_group` = ' . $fleet_row['fleet_group']);
    } else {
      doquery('UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` =' . $fleet_row['fleet_id']);
    }
    return false;
  }
  // --- End of Universal part

  $target_user_id      = $target_planet['id_owner'];

  $target_user_row     = doquery("SELECT * FROM {{users}} WHERE `id` = '{$target_user_id}' LIMIT 1;", '', true);
  if(!$target_user_row || !isset($target_user_row['id']))
  {
    return false;
  }

  $spying_user_row     = doquery("SELECT * FROM {{users}} WHERE `id` = '{$fleet_row['fleet_owner']}';", '', true);
  if(!$spying_user_row || !isset($spying_user_row['id']))
  {
    return false;
  }

  $TargetSpyLvl  = GetSpyLevel($target_user_row);
  $CurrentSpyLvl = GetSpyLevel($spying_user_row);

  $target_planet = sys_o_get_updated($target_user_row, $target_planet, $time_now, true);
  $target_planet = $target_planet['planet'];

  global $lang, $resource, $dbg_msg, $pricelist, $sn_data;

  doquery('START TRANSACTION;');
  $fleet_strings       = explode(';', $fleet_row['fleet_array']);
  foreach ($fleet_strings as $ship_string)
  {
    if (!$ship_string)
    {
      continue;
    }

    $ship_record = explode(",", $ship_string);
    if ($ship_record[0] == 210)
    {
      $MaterialsInfo    = SpyTarget ( $target_planet, 0, $lang['sys_spy_maretials'], $target_user_row['username'] );
      $Materials        = $MaterialsInfo['String'];

      $PlanetFleetInfo  = SpyTarget ( $target_planet, 1, $lang['sys_spy_fleet'] );
      $PlanetFleet      = $Materials;
      $PlanetFleet     .= $PlanetFleetInfo['String'];
      $PlanetFleet      = "<div class='spy_medium'>{$PlanetFleet}</div>";

      $PlanetDefenInfo  = SpyTarget ( $target_planet, 2, $lang['sys_spy_defenses'] );
      $PlanetDefense    = $PlanetFleet;
      $PlanetDefense   .= $PlanetDefenInfo['String'];
      $PlanetDefense    = "<div class='spy_medium'>{$PlanetDefense}</div>";

      $PlanetBuildInfo  = SpyTarget ( $target_planet, 3, $lang['tech'][0] );
      $PlanetBuildings  = $PlanetDefense;
      $PlanetBuildings .= $PlanetBuildInfo['String'];
      $PlanetBuildings  = "<div class='spy_long'>{$PlanetBuildings}</div>";

      $TargetTechnInfo  = SpyTarget ( $target_user_row, 4, $lang['tech'][100] );
      $TargetTechnos    = $PlanetBuildings;
      $TargetTechnos   .= $TargetTechnInfo['String'];
      $TargetTechnos    = "<div class='spy_long'>{$TargetTechnos}</div>";

      $spy_drones       = $ship_record[1];
      $SpyToolDebrisM   = $spy_drones * $pricelist[210]['metal'] * 0.3;
      $SpyToolDebrisC   = $spy_drones * $pricelist[210]['crystal'] * 0.3;

      if ($CurrentSpyLvl == $TargetSpyLvl) {
        $TargetForce = 0.25 * $spy_drones * $PlanetFleetInfo['Count'];
      } elseif ($TargetSpyLvl > $CurrentSpyLvl) {
        $TargetForce = ($TargetSpyLvl - $CurrentSpyLvl + 1) * (0.50 * $spy_drones * $PlanetFleetInfo['Count']);
      } else {
        $TargetForce = 0.125 * $spy_drones * $PlanetFleetInfo['Count'];
      }

      if ($spy_drones == 1) {
        $SpyChances = 0;
      } elseif ($spy_drones > 2) {
        $SpyChances = 30;
      } else {
        $SpyChances = 10;
      }

      if (($TargetForce > 0) AND ($TargetForce > 100) AND ($TargetForce < 200)) {
        $TargetForce_h = 100;
        $TargetForce_l = 0;
      } elseif (($TargetForce > 0) AND ($TargetForce > 200)) {
        $TargetForce_h = 100;
        $TargetForce_l = 20;
      } else {
        $TargetForce_h = 0;
        $TargetForce_l = 0;
      }

      $TargetChances = rand($TargetForce_l, $TargetForce_h);
      $SpyerChances = rand($SpyChances, 100);
      if ($TargetChances < $SpyerChances) {
        $DestProba = sprintf( $lang['sys_mess_spy_lostproba'], $TargetChances);
      } elseif ($TargetChances >= $SpyerChances) {
        $DestProba = "".$lang['sys_mess_spy_destroyed']."";
      }

      $AttackLink = "<center>";
      $AttackLink .= "<a href=\"fleet.php?galaxy=". $fleet_row['fleet_end_galaxy'] ."&system=". $fleet_row['fleet_end_system'] ."";
      $AttackLink .= "&planet=".$fleet_row['fleet_end_planet']."";
      $AttackLink .= "&target_mission=1";
      $AttackLink .= " \">". $lang['type_mission'][1] ."";
      $AttackLink .= "</a></center>";

      $MessageEnd = "<center>".$DestProba."</center>";

      if ($TargetSpyLvl > $CurrentSpyLvl) {
        $ST = ($spy_drones - pow(($TargetSpyLvl - $CurrentSpyLvl), 2));
      }
      if ($CurrentSpyLvl > $TargetSpyLvl) {
        $ST = ($spy_drones + pow(($CurrentSpyLvl - $TargetSpyLvl), 2));
      }
      if ($TargetSpyLvl == $CurrentSpyLvl) {
        $ST = $spy_drones;
      }

      // Generating link to simulator
      $combat_pack[0] = array(
        RES_METAL => $target_planet['metal'],
        RES_CRYSTAL => $target_planet['crystal'],
        RES_DEUTERIUM => $target_planet['deuterium']
      );
      if ($ST >= 2)
      {
        // add planet fleet
        coe_compress_add_units($sn_data['groups']['fleet'], $target_planet, $combat_pack[0]);
      }
      if ($ST >= 3)
      {
        // add planet defense
        coe_compress_add_units($sn_data['groups']['defense_active'], $target_planet, $combat_pack[0]);
      }
      if ($ST >= 7)
      {
        // add user technos
        coe_compress_add_units(array(109, 110, 111), $target_user_row, $combat_pack[0]);
      }
      $simulator_link = eco_sym_encode_replay($combat_pack, 'D');
      $AttackLink .= "<center><a href=\"simulator.php?replay={$simulator_link}\">{$lang['COE_combatSimulator']}</a></center><br />";
      // End of link generation

      if ($ST <= "1") {
        $SpyMessage = $Materials."<br />".$AttackLink.$MessageEnd;
      }
      if ($ST == "2") {
        $SpyMessage = $PlanetFleet."<br />".$AttackLink.$MessageEnd;
      }
      if ($ST == "4" or $ST == "3") {
        $SpyMessage = $PlanetDefense."<br />".$AttackLink.$MessageEnd;
      }
      if ($ST == "5" or $ST == "6") {
        $SpyMessage = $PlanetBuildings."<br />".$AttackLink.$MessageEnd;
      }
      if ($ST >= "7") {
        $SpyMessage = $TargetTechnos."<br />".$AttackLink.$MessageEnd;
      }

      SendSimpleMessage ( $spying_user_row['id'], '', $fleet_row['fleet_start_time'], 0, $lang['sys_mess_qg'], $lang['sys_mess_spy_report'], $SpyMessage);

      $spying_planet_row  = doquery("SELECT * FROM {{table}} WHERE `galaxy` = '".$fleet_row['fleet_start_galaxy']."' AND `system` = '".$fleet_row['fleet_start_system']."' AND `planet` = '".$fleet_row['fleet_start_planet']."' AND `planet_type` = '".$fleet_row['fleet_start_type']."';", 'planets', true);

      $TargetMessage  = $lang['sys_mess_spy_ennemyfleet'] ." ". $spying_planet_row['name'];
      $TargetMessage .= "<a href=\"galaxy.php?mode=3&galaxy=". $spying_planet_row["galaxy"] ."&system=". $spying_planet_row["system"] ."\">";
      $TargetMessage .= "[". $spying_planet_row["galaxy"] .":". $spying_planet_row["system"] .":". $spying_planet_row["planet"] ."]</a> ";
      $TargetMessage .= $lang['sys_mess_spy_seen_at'] ." ". $target_planet['name'];
      $TargetMessage .= " [". $target_planet["galaxy"] .":". $target_planet["system"] .":". $target_planet["planet"] ."].";

      SendSimpleMessage ( $target_user_id, '', $fleet_row['fleet_start_time'], 0, $lang['sys_mess_spy_control'], $lang['sys_mess_spy_activity'], $TargetMessage);

    }

    if ($TargetChances >= $SpyerChances) {
      $QryUpdateGalaxy  = "UPDATE {{planets}} SET ";
      $QryUpdateGalaxy .= "`debris_metal` = `debris_metal` + '". (0 + $SpyToolDebrisM) ."', ";
      $QryUpdateGalaxy .= "`debris_crystal` = `debris_crystal` + '". (0 + $SpyToolDebrisC) ."' ";
      $QryUpdateGalaxy .= "WHERE ";

      $QryUpdateGalaxy .= "`galaxy` = '". $fleet_row['fleet_end_galaxy'] ."' AND ";
      $QryUpdateGalaxy .= "`system` = '". $fleet_row['fleet_end_system'] ."' AND ";
      $QryUpdateGalaxy .= "`planet` = '". $fleet_row['fleet_end_planet'] ."' AND ";
      $QryUpdateGalaxy .= "`planet_type` = 1;";
      doquery( $QryUpdateGalaxy);

      SendSimpleMessage ( $target_user_id, '', $fleet_row['fleet_start_time'], 0, $lang['sys_mess_spy_control'], $lang['sys_mess_spy_activity'], 'Ваш шпионский флот уничтожен');

      doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
    }
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
  }
  doquery('COMMIT;');
}

?>
