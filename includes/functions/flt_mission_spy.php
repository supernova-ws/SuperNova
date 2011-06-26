<?php

/**
 * MissionCaseSpy.php
 *
 * V2 optimizations; correctly works mission
 * @version 1
 * @copyright 2008
 */
// ----------------------------------------------------------------------------------------------------------------
// Mission Case 6: -> Espionner
//

function coe_compress_add_units($unit_group, $target, &$compress_data)
{
  global $sn_data;

  foreach($unit_group as $unit_id)
  {
    $unit_count = $target[$sn_data[$unit_id]['name']];
    if($unit_count > 0)
    {
      $compress_data[$unit_id] = $unit_count;
    }
  }
}

/**
 * flt_spy_scan
 *
 * @version 1
 * @copyright 2008
*/

// ----------------------------------------------------------------------------------------------------------------
//
// flt_spy_scan
//
// $target_planet -> Enregistrement 'planet' de la base de donnees
// $Mode         -> Ce que l'on va notifier
//                  0 -> Ressources, 1 -> Flotte, 2 ->Defenses, 3 -> Batiments, 4 -> Technologies
// $TitleString  -> Chaine definissant le titre ou la parcelle de titre a afficher
function flt_spy_scan ( $target_planet, $Mode, $TitleString, $TargetUsername="" ) {
  global $lang, $resource, $time_now;

  $LookAtLoop = true;
  if       ($Mode == 0) {
    $String  = "<table width=\"440\"><tr><td class=\"c\" colspan=\"5\">";
    $String .= $TitleString ." ". $target_planet['name'];
    $String .= " <a href=\"galaxy.php?mode=3&galaxy=". $target_planet["galaxy"] ."&system=". $target_planet["system"]. "\">";
    $String .= "[". $target_planet["galaxy"] .":". $target_planet["system"] .":". $target_planet["planet"] ."]</a>";
    $String .= " (".$lang['Player_']." '".$TargetUsername."') ".$lang['On_']." ". date(FMT_DATE_TIME, $time_now + 2 * 60 * 60) ."</td>";
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
    $ResFrom[0] = TECH_TECHNOLOGY;
    $ResTo[0]   = TECH_COLONIZATION;
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

//  $return['String'] = $String;
//  $return['Count']  = $Count;
  return $String;
}

function flt_mission_spy($mission_data)
{
  global $time_now;

  $fleet_row         = $mission_data['fleet'];
  $target_user_row   = $mission_data['dst_user'];
  $target_planet_row = $mission_data['dst_planet'];
  $spying_user_row   = $mission_data['src_user'];
  $spying_planet_row = $mission_data['src_planet'];

  if(!$target_user_row || !$target_planet_row || !is_array($target_user_row) || !is_array($target_planet_row))
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1;");
    return;
  }

  $fleet_array = sys_unit_str2arr($fleet_row['fleet_array']);
  if($fleet_array[SHIP_SPY] > 0)
  {
    $TargetSpyLvl      = GetSpyLevel($target_user_row); //mrc_modify_value($target_user_row, $target_planet_row, MRC_SPY, GetSpyLevel($target_user_row));
    $CurrentSpyLvl     = GetSpyLevel($spying_user_row); //mrc_modify_value($spying_user_row, $spying_planet_row, MRC_SPY, GetSpyLevel($spying_user_row));

    $spy_probes = $fleet_array[SHIP_SPY];
    $spy_diff   = $CurrentSpyLvl + sqrt($spy_probes) - 1 - $TargetSpyLvl;
/*
    pdump($spy_probes, '$spy_probes');
    pdump($CurrentSpyLvl, '$CurrentSpyLvl');
    pdump($TargetSpyLvl, '$TargetSpyLvl');
    pdump(sqrt($spy_probes), 'sqrt($spy_probes)');
    pdump($spy_diff, '$spy_diff');
*/
    global $lang, $sn_data;

    $spy_resources = flt_spy_scan ( $target_planet_row, 0, $lang['sys_spy_maretials'], $target_user_row['username'] );

    $spy_info      = flt_spy_scan ( $target_planet_row, 1, $lang['sys_spy_fleet'] );
    $spy_fleet     = "<div class='spy_medium'>{$spy_info}</div>";

    $spy_info      = flt_spy_scan ( $target_planet_row, 2, $lang['sys_spy_defenses'] );
    $spy_defence   = "<div class='spy_medium'>{$spy_info}</div>";

    $spy_info      = flt_spy_scan ( $target_planet_row, 3, $lang['tech'][0] );
    $spy_buildings = "<div class='spy_long'>{$spy_info}</div>";


    $combat_pack[0] = array(
      RES_METAL => $target_planet_row['metal'],
      RES_CRYSTAL => $target_planet_row['crystal'],
      RES_DEUTERIUM => $target_planet_row['deuterium']
    );

    $spy_message = $spy_resources;
    if ($spy_diff >= 2) {
      $spy_message .= $spy_fleet;
      coe_compress_add_units($sn_data['groups']['fleet'], $target_planet_row, $combat_pack[0]);
    }
    if ($spy_diff >= 3) {
      $spy_message .= $spy_defence;
      coe_compress_add_units($sn_data['groups']['defense_active'], $target_planet_row, $combat_pack[0]);
    }
    if ($spy_diff >= 5)
    {
      $spy_message .= $spy_buildings;
    }
    if ($spy_diff >= 7)
    {
      $spy_info      = flt_spy_scan ( $target_user_row, 4, $lang['tech'][TECH_TECHNOLOGY] );
      $spy_tech      = "<div class='spy_long'>{$spy_info}</div>";

      $spy_message .= $spy_tech;
      coe_compress_add_units(array(TECH_WEAPON, TECH_SHIELD, TECH_ARMOR), $target_user_row, $combat_pack[0]);
    }
    $simulator_link = eco_sym_encode_replay($combat_pack, 'D');

    $target_unit_list = 0;
    foreach($sn_data['groups']['fleet'] as $unit_id)
    {
      $target_unit_list += max(0, $target_planet_row[$sn_data[$unit_id]['name']]);
    }

    $spy_detected = $spy_probes * $target_unit_list / 4 * pow(2, $TargetSpyLvl - $CurrentSpyLvl);

    if (mt_rand(0, 99) > $spy_detected)
    {
      $DestProba = sprintf($lang['sys_mess_spy_detect_chance'], $spy_detected);
      $spy_detected = false;
    }
    else
    {
      $DestProba = $lang['sys_mess_spy_destroyed'];
      $spy_detected = true;
    }

    $spy_message .= "<br /><center><a href=\"fleet.php?target_mission=1&planet_type={$fleet_row['fleet_end_type']}&galaxy={$fleet_row['fleet_end_galaxy']}";
    $spy_message .= "&system={$fleet_row['fleet_end_system']}&planet={$fleet_row['fleet_end_planet']} \">{$lang['type_mission'][1]}</a></center>";
    $spy_message .= "<center><a href=\"simulator.php?replay={$simulator_link}\">{$lang['COE_combatSimulator']}</a></center><br />";
    $spy_message .= "<center>".$DestProba."</center>";
    // End of link generation

    msg_send_simple_message($spying_user_row['id'], '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_mess_qg'], $lang['sys_mess_spy_report'], $spy_message);

    $TargetMessage  = $lang['sys_mess_spy_ennemyfleet'] ." ". $spying_planet_row['name'];
    $TargetMessage .= "<a href=\"galaxy.php?mode=3&galaxy=". $spying_planet_row["galaxy"] ."&system=". $spying_planet_row["system"] ."\">";
    $TargetMessage .= "[". $spying_planet_row["galaxy"] .":". $spying_planet_row["system"] .":". $spying_planet_row["planet"] ."]</a> ";
    $TargetMessage .= $lang['sys_mess_spy_seen_at'] ." ". $target_planet_row['name'];
    $TargetMessage .= " [". $target_planet_row["galaxy"] .":". $target_planet_row["system"] .":". $target_planet_row["planet"] ."].";

    $target_user_id = $fleet_row['fleet_target_owner'];

    if ($spy_detected)
    {
      doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");

      if ($target_planet_row['planet_type'] == PT_PLANET)
      {
        $debris_planet_id = $target_planet_row['id'];
      }
      else
      {
        $debris_planet_id = $target_planet_row['parent_planet'];
      }

      $QryUpdateGalaxy  = "UPDATE {{planets}} SET ";
      $QryUpdateGalaxy .= "`debris_metal` = `debris_metal` + '". floor($spy_probes * $sn_data[SHIP_SPY]['metal'] * 0.3) ."', ";
      $QryUpdateGalaxy .= "`debris_crystal` = `debris_crystal` + '". floor($spy_probes * $sn_data[SHIP_SPY]['crystal'] * 0.3) ."' ";
      $QryUpdateGalaxy .= "WHERE `id` = '{$debris_planet_id}' LIMIT 1;";
      doquery($QryUpdateGalaxy);

      $TargetMessage .= "<br />{$lang['sys_mess_spy_destroyed_enemy']}";

//      msg_send_simple_message ( $target_user_id, '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_mess_spy_control'], $lang['sys_mess_spy_activity'], $TargetMessage . );

      $result = CACHE_FLEET | CACHE_PLANET_DST;
    }
    else
    {
      $result = CACHE_FLEET;
    }
    msg_send_simple_message ( $target_user_id, '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_mess_spy_control'], $lang['sys_mess_spy_activity'], $TargetMessage);
  }

  if(!$spy_detected)
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
  }

  return $result;
}

?>
