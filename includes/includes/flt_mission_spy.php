<?php

require_once('includes/includes/coe_simulator_helpers.php');

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

function flt_spy_scan($target, $group_name, $section_title)
{
  global $lang, $sn_data, $time_now;

  $result = "<tr><td class=\"c\" colspan=\"4\">{$section_title}</td></tr>";
  foreach($sn_data['groups'][$group_name] as $unit_id)
  {
    if($target[$sn_data[$unit_id]['name']] > 0)
    {
      $result  .= "<tr><td align=left colspan = 3>{$lang['tech'][$unit_id]}</td><td align=right>{$target[$sn_data[$unit_id]['name']]}</td></tr>";
    }
  }

  return $result;
}

function flt_mission_spy($mission_data)
{
  global $time_now, $lang, $sn_data;

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
    $TargetSpyLvl      = GetSpyLevel($target_user_row);
    $CurrentSpyLvl     = GetSpyLevel($spying_user_row);

    $spy_probes = $fleet_array[SHIP_SPY];
    $spy_diff   = $CurrentSpyLvl + sqrt($spy_probes) - 1 - $TargetSpyLvl;

    $combat_pack[0] = array(
      RES_METAL => $target_planet_row['metal'],
      RES_CRYSTAL => $target_planet_row['crystal'],
      RES_DEUTERIUM => $target_planet_row['deuterium']
    );

    $spy_message  = "<table width=\"440\" cellspacing = \"1\"><tr><td class=\"c\" colspan=\"4\">{$lang['sys_spy_maretials']} {$target_planet_row['name']} ";
    $spy_message .= uni_render_coordinates_href($target_planet_row, '', 3);
    $spy_message .= " ({$lang['Player_']} '{$target_user_row['username']}') {$lang['On_']} ";
    $spy_message .= date(FMT_DATE_TIME, $fleet_row['fleet_end_time']);
    $spy_message .= "</td></tr><tr>";
    $spy_message .= "<td width=220>{$lang['sys_metal']}</td><td width=220 align=right>" . pretty_number($target_planet_row['metal']) . "</td>";
    $spy_message .= "<td width=220>{$lang['sys_crystal']}</td></td><td width=220 align=right>" . pretty_number($target_planet_row['crystal']) . "</td>";
    $spy_message .= "</tr><tr>";
    $spy_message .= "<td width=220>{$lang['sys_deuterium']}</td><td width=220 align=right>" . pretty_number($target_planet_row['deuterium'])  . "</td>";
    $spy_message .= "<td width=220>{$lang['sys_energy']}</td><td width=220 align=right>" . pretty_number($target_planet_row['energy_max']) . "</td>";
    $spy_message .= "</tr>";
    if ($spy_diff >= 2)
    {
      $spy_message .= "<div class='spy_medium'>" . flt_spy_scan($target_planet_row, 'fleet', $lang['tech'][UNIT_SHIPS]) . "</div>";
      coe_compress_add_units($sn_data['groups']['fleet'], $target_planet_row, $combat_pack[0]);
    }
    if ($spy_diff >= 3) {
      $spy_message .= "<div class='spy_medium'>" . flt_spy_scan($target_planet_row, 'defense', $lang['tech'][UNIT_DEFENCE]) . "</div>";
      coe_compress_add_units($sn_data['groups']['defense_active'], $target_planet_row, $combat_pack[0]);
    }
    if ($spy_diff >= 5)
    {
      $spy_message .= "<div class='spy_long'>" . flt_spy_scan($target_planet_row, 'structures', $lang['tech'][UNIT_STRUCTURES]) . "</div>";
    }
    if ($spy_diff >= 7)
    {
      $spy_message .= "<div class='spy_long'>" . flt_spy_scan($target_user_row, 'tech', $lang['tech'][UNIT_TECHNOLOGIES]) . "</div>";
      coe_compress_add_units(array(TECH_WEAPON, TECH_SHIELD, TECH_ARMOR), $target_user_row, $combat_pack[0]);
    }

    $simulator_link = coe_sym_encode_replay($combat_pack, 'D');

    $target_unit_list = 0;
    foreach($sn_data['groups']['fleet'] as $unit_id)
    {
      $target_unit_list += max(0, $target_planet_row[$sn_data[$unit_id]['name']]);
    }

    $spy_detected = $spy_probes * $target_unit_list / 4 * pow(2, $TargetSpyLvl - $CurrentSpyLvl);

    if(mt_rand(0, 99) > $spy_detected)
    {
      $spy_outcome_str = sprintf($lang['sys_mess_spy_detect_chance'], $spy_detected);
      $spy_detected = false;
    }
    else
    {
      $spy_outcome_str = $lang['sys_mess_spy_destroyed'];
      $spy_detected = true;
    }

    $spy_message .= "<tr><th class=\"c_c\" colspan=4>";
    $spy_message .= "{$spy_outcome_str}<br />";
    $spy_message .= "<a href=\"fleet.php?target_mission=1&planet_type={$fleet_row['fleet_end_type']}&galaxy={$fleet_row['fleet_end_galaxy']}&system={$fleet_row['fleet_end_system']}&planet={$fleet_row['fleet_end_planet']} \">{$lang['type_mission'][1]}</a><br />";
    $spy_message .= "<a href=\"simulator.php?replay={$simulator_link}\">{$lang['COE_combatSimulator']}</a><br />";
    $spy_message .= "</th></tr></table>";
    // End of link generation

    msg_send_simple_message($spying_user_row['id'], '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_mess_qg'], $lang['sys_mess_spy_report'], $spy_message);

    $target_message  = "{$lang['sys_mess_spy_ennemyfleet']} {$spying_planet_row['name']} " . uni_render_coordinates_href($spying_planet_row, '', 3);
    $target_message .= " {$lang['sys_mess_spy_seen_at']} {$target_planet_row['name']} " . uni_render_coordinates($target_planet_row);

    $target_user_id = $fleet_row['fleet_target_owner'];

    if($spy_detected)
    {
      doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");

      $debris_planet_id = $target_planet_row['planet_type'] == PT_PLANET ? $target_planet_row['id'] : $target_planet_row['parent_planet'];

      $QryUpdateGalaxy  = "UPDATE {{planets}} SET ";
      $QryUpdateGalaxy .= "`debris_metal` = `debris_metal` + '". floor($spy_probes * $sn_data[SHIP_SPY]['metal'] * 0.3) ."', ";
      $QryUpdateGalaxy .= "`debris_crystal` = `debris_crystal` + '". floor($spy_probes * $sn_data[SHIP_SPY]['crystal'] * 0.3) ."' ";
      $QryUpdateGalaxy .= "WHERE `id` = '{$debris_planet_id}' LIMIT 1;";
      doquery($QryUpdateGalaxy);

      $target_message .= "<br />{$lang['sys_mess_spy_destroyed_enemy']}";

      $result = CACHE_FLEET | CACHE_PLANET_DST;
    }
    else
    {
      $result = CACHE_FLEET;
    }
    msg_send_simple_message($target_user_id, '', $fleet_row['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_mess_spy_control'], $lang['sys_mess_spy_activity'], $target_message);
  }

  if(!$spy_detected)
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
  }

  return $result;
}

?>
