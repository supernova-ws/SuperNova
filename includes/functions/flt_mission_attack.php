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
  global $lang, $sn_data, $time_now;

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

  $fortifier_bonus = 1 - ($destination_planet['PLANET_GOVERNOR_ID'] == MRC_FORTIFIER ? $destination_planet['PLANET_GOVERNOR_LEVEL'] : 0) * $sn_data[MRC_FORTIFIER]['bonus'] / 100;
  $defenseFleets = array(
    0 => array(
      'def' => array(),
      'user' => array(
        'id'             => $destination_user['id'],
        'username'       => $destination_user['username'],
        $db_admiral_name => $destination_user[$db_admiral_name],
        'defence_tech'   => floor($destination_user['defence_tech'] * $fortifier_bonus),
        'shield_tech'    => floor($destination_user['shield_tech'] * $fortifier_bonus),
        'military_tech'  => floor($destination_user['military_tech'] * $fortifier_bonus),
      ),
    )
  );

  foreach($sn_data['groups']['combat'] as $combatUnitID)
  {
    if ($destination_planet[$sn_data[$combatUnitID]['name']] > 0)
    {
      $defenseFleets[0]['def'][$combatUnitID] = $destination_planet[$sn_data[$combatUnitID]['name']];
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
  if ($destination_user['authlevel'] == 0)
  {
    doquery('UPDATE {{planets}} SET `debris_metal` = `debris_metal` + '.($result['debree']['att'][0]+$result['debree']['def'][0]).' , debris_crystal = debris_crystal+'.($result['debree']['att'][1]+$result['debree']['def'][1]).' WHERE `galaxy` = '. $fleet_row['fleet_end_galaxy'] .' AND `system` = '. $fleet_row['fleet_end_system'] .' AND `planet` = '. $fleet_row['fleet_end_planet'] . ' AND `planet_type` = 1 LIMIT 1;');
  };

  // !G+ post-calculation for Attackers: fleet left and possible loot
  $loot = BE_calculatePostAttacker($destination_planet, $attackFleets, $result, false);

  if($result['won'] == 2 && count($result['rw']) == 2)
  {
    $one_round_loss = true;
  }
  else
  {
    $one_round_loss = false;
  }

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
        $fleetArray .= "`{$sn_data[$element]['name']}` = '{$amount}', ";
      }
      doquery('UPDATE {{planets}} SET '.$fleetArray.' metal = metal - '.$loot['looted']['metal'].', crystal = crystal - '.$loot['looted']['crystal'].', deuterium=deuterium-'.$loot['looted']['deuterium'].' WHERE id='.$destination_planet['id']);
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
        doquery("UPDATE {{fleets}} SET fleet_array = '{$fleetArray}', fleet_amount = {$totalCount}" . ($one_round_loss ? '' : ', fleet_mess = 1') . " WHERE fleet_id = {$fleetID} LIMIT 1;");
      }
    }
  }
  // TvdW (c) 2008

  $planet_coordinates = uni_render_coordinates($fleet_row, 'fleet_end_');

  // FROM HERE THE SCRIPT WAS IMPORTED (not TvdW code anymore)
  $MoonChance = BE_calculateMoonChance($result['debree']['att'][0] + $result['debree']['def'][0] + $result['debree']['att'][1] + $result['debree']['def'][1]);
  if((mt_rand(1, 100) <= $MoonChance) && ($TargetPlanetName = uni_create_moon($fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'], $TargetUserID, $MoonChance)))
  {
    $GottenMoon = sprintf($lang['sys_moonbuilt'], $TargetPlanetName, $planet_coordinates);
  }

  // Adjust number of raids made/win/loose and xpraid
  $str_loose_or_win = $result['won'] == 1 ? 'raidswin' : 'raidsloose';
  doquery("UPDATE {{users}} SET `xpraid` = `xpraid` + 1, `raids` = `raids` + 1, `{$str_loose_or_win}` = `{$str_loose_or_win}` + 1 WHERE id = '{$fleet_row['fleet_owner']}' LIMIT 1;");

  $bashing_list = array();
  foreach ($defenseFleets as $fleetID => $defender)
  {
    $users2[$defender['user']['id']] = $users_defender[$defender['user']['id']] = $defender['user']['id'];
  }

  foreach ($attackFleets as $fleetID => $attacker)
  {
    $users2[$attacker['user']['id']] = $users_attacker[$attacker['user']['id']] = $attacker['user']['id'];
    // Generating attackers list for bashing table
    $bashing_list[$attacker['user']['id']] = "({$attacker['user']['id']}, {$destination_planet['id']}, {$fleet_row['fleet_end_time']})";
  }
  $bashing_list = implode(',', $bashing_list);
  doquery("INSERT INTO {{bashing}} (bashing_user_id, bashing_planet_id, bashing_time) VALUES {$bashing_list};");

  //MadnessRed CR Creation.
  $raport = formatCR($result, $loot['looted'], $MoonChance, $GottenMoon, $totaltime);
  $raport = $raport['html'];
  $rid = md5($raport);
  $QryInsertRapport  = 'INSERT INTO `{{rw}}` SET ';
  $QryInsertRapport .= '`time` = UNIX_TIMESTAMP(), ';
  $QryInsertRapport .= '`owners` = "'.implode(',', $users2).'", ';
  $QryInsertRapport .= '`id_owner1` = "'.$attacker['user']['id'].'", ';
  $QryInsertRapport .= '`id_owner2` = "'.$defender['user']['id'].'", ';
  $QryInsertRapport .= '`rid` = "'. $rid .'", ';
  $QryInsertRapport .= '`raport` = "'. mysql_real_escape_string( $raport ) .'"';
  doquery($QryInsertRapport) or die("Error inserting CR to database".mysql_error()."<br /><br />Trying to execute:".mysql_query());

  switch($result['won'])
  {
    case 0:
      $color_attackers = $color_defenders = 'orange';
    break;

    case 1:
      $color_attackers = 'green';
      $color_defenders = 'red';
    break;

    case 2:
      $color_attackers = 'red';
      $color_defenders = 'green';
    break;
  }

  $raport_part1 = '<span OnClick=\'f("rw.php?raport='. $rid .'", "");\' ><center><font color=';
  $raport_part2 = ">{$lang['sys_mess_attack_report']} {$planet_coordinates}</font></span><br /><br />" . 
    "<font color=\"red\">{$lang['sys_perte_attaquant']}: " . pretty_number($result['lost']['att'], true) . "</font>&nbsp;<font color=\"green\">{$lang['sys_perte_defenseur']}: " . pretty_number($result['lost']['def'], true) . "</font><br />" .
    "{$lang['sys_debris']} {$lang['Metal']} <font color=\"#adaead\">" . pretty_number($result['debree']['att'][0] + $result['debree']['def'][0], true) . "</font> {$lang['Crystal']} <font color=\"#ef51ef\">" . pretty_number($result['debree']['att'][1] + $result['debree']['def'][1], true) .'</font><br />' .
    ($result['won'] == 1 ? ("{$lang['sys_gain']} " .
      "{$lang['Metal']} <font color=\"#adaead\">". pretty_number($loot['looted']['metal'], true) .'</font> ' .
      "{$lang['Crystal']} <font color=\"#ef51ef\">" . pretty_number($loot['looted']['crystal'], true) . '</font> ' .
      "{$lang['Deuterium']} <font color=\"#f77542\">" . pretty_number($loot['looted']['deuterium'], true) .'</font><br />') : '') . 
    "{$st_1}{$st_2}</center>";

  $raport_acs = $one_round_loss 
    ? "<center><span class=\"negative\">{$lang['sys_mess_attack_report']} {$planet_coordinates}\r\n{$lang['sys_coe_lost_contact']}</span></center>" 
    : "{$raport_part1}{$color_attackers}{$raport_part2}";
  foreach ($users_attacker as $id)
  {
//    if ($id != $fleet_row['fleet_owner'] && $id != 0)
    if($id)
    {
      msg_send_simple_message ( $id, '', $fleet_row['fleet_start_time'], MSG_TYPE_COMBAT, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $raport_acs);
    }
  }

  $raport_hold = "{$raport_part1}{$color_defenders}{$raport_part2}";
  foreach ($users_defender as $id)
  {
    if ($id && $id != $fleet_row['fleet_owner'])
    {
      msg_send_simple_message($id, '', $fleet_row['fleet_start_time'], MSG_TYPE_COMBAT, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $raport_hold);
    }
  }

  return $result;
}

// MadnessRed 2008
?>
