<?php

/**
 * flotenajax.php
 *
 * Fleet manager on Ajax (to work in Galaxy view)
 *
 * @version 2.0 Security checks by Gorlum for http://supernova.ws
 *  [!] Full rewrite
 *  [+] Added missile attack launch sequience
 *  [-] Moved almost all check code to flt_can_attack
 * @version 1.1 Security checks by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 **/

include('common.' . substr(strrchr(__FILE__, '.'), 1));

define('IN_AJAX', true);

require_once('includes/includes/flt_functions.php');

/**
 * @throws Exception
 */
function fleet_ajax() {
  global $lang, $user;

  $lang->lng_include('universe');
  $lang->lng_include('fleet');

  $travel_data = array();

  // TODO - change to JSON. Message can be sent in JSON-encoded field
  header("Content-type: text/html; charset=utf-8");

  $target_mission = sys_get_param_int('mission');
  $sn_group_missions = sn_get_groups('missions');
  if(empty($sn_group_missions[$target_mission]['AJAX'])) {
    die($lang['gs_c00']);
  }

  // Checking target coordinates validity
  $target_coord = array(
    'id'          => null,
    'id_owner'    => 0,
    'galaxy'      => sys_get_param_int('galaxy'),
    'system'      => sys_get_param_int('system'),
    'planet'      => sys_get_param_int('planet'),
    'planet_type' => sys_get_param_int('planet_type'),
  );
  // fleet_ajax now can send fleets only to existing planets/moons
  // TODO - sending colonization and expeditions in 1 click
  if(!uni_coordinates_valid($target_coord)) {
    die($lang['gs_c02']);
  }

  sn_db_transaction_start();

  $user = db_user_by_id($user['id'], true);
  $planetrow = db_planet_by_id($user['current_planet'], true);

  // TODO - DEADLOCK CAN BE HERE!!!! We should lock SOURCE and TARGET owners in one query
  $target_row = db_planet_by_vector($target_coord);
  if(empty($target_row)) {
    $target_row = $target_coord;
    $target_row['id_owner'] = 0;
    // If fleet destination is PT_DEBRIS - then it's actually destination is PT_PLANET // TODO - REMOVE! Debris should be valid DESTINATION planet_type!
    $target_row['planet_type'] = $target_row['planet_type'] == PT_DEBRIS ? PT_PLANET : $target_row['planet_type'];
  } else {
    $target_coord['id'] = $target_row['id'];
    $target_coord['id_owner'] = $target_row['id_owner'];
  }

  $fleet_array = array();
  switch($target_mission) {
    case MT_SPY:
      $fleet_array[SHIP_SPY] = min(mrc_get_level($user, $planetrow, SHIP_SPY), abs(classSupernova::$user_options[PLAYER_OPTION_FLEET_SPY_DEFAULT]));
      $unit_group = 'flt_spies';
    break;

    case MT_RECYCLE:
      foreach(sn_get_groups('flt_recyclers') as $unit_id) {
        if($unit_count = mrc_get_level($user, $planetrow, $unit_id)) {
          $fleet_array[$unit_id] = $unit_count;
        }
      }
      $transport_data = flt_calculate_fleet_to_transport($fleet_array, $target_row['debris_metal'] + $target_row['debris_crystal'], $planetrow, $target_row);
      $fleet_array = $transport_data['fleet'];
      $unit_group = 'flt_recyclers';
    break;

    case MT_MISSILE:
      $fleet_array[UNIT_DEF_MISSILE_INTERPLANET] = min(mrc_get_level($user, $planetrow, UNIT_DEF_MISSILE_INTERPLANET), abs(sys_get_param_float('missiles')));
      $unit_group = 'missile';
    break;

  }

  $isAttackAllowed = flt_can_attack(
    $planetrow,
    $target_row,
    $fleet_array,
    $target_mission,
    array(
      'target_structure' => $target_structure = sys_get_param_int('structures'),
    )
  );
  if($isAttackAllowed != ATTACK_ALLOWED) {
    die($lang['fl_attack_error'][$isAttackAllowed]);
  }

  $db_changeset = array();
  foreach($fleet_array as $unit_id => $unit_count) {
    $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, -$unit_count, $user, $planetrow);
  }

  if($target_mission == MT_MISSILE) {
    $distance = abs($target_coord['system'] - $planetrow['system']);
    $duration = round((30 + (60 * $distance)) / flt_server_flight_speed_multiplier());
    $arrival = SN_TIME_NOW + $duration;
    $travel_data['consumption'] = 0;

    db_missile_insert($target_coord, $user, $planetrow, $arrival, array_sum($fleet_array), $target_structure);
  } else {
    $travel_data = flt_travel_data($user, $planetrow, $target_coord, $fleet_array, 10);

    if($planetrow['deuterium'] < $travel_data['consumption']) {
      die($lang['gs_c13']);
    }

    $objFleet = new Fleet();
    $objFleet->set_times($travel_data['duration']);
    $objFleet->unitsSetFromArray($fleet_array);
    $objFleet->mission_type = $target_mission;
    $objFleet->set_start_planet($planetrow);
    $objFleet->set_end_planet($target_coord);
    $objFleet->playerOwnerId = $user['id'];
    $objFleet->group_id = 0;
    $objFleet->create_and_send();
  }

  db_planet_set_by_id($planetrow['id'], "`deuterium` = `deuterium` - {$travel_data['consumption']}");
  db_changeset_apply($db_changeset);
  sn_db_transaction_commit();

  $ships_sent = array();
  $ships_sent_js = 0;
  foreach($fleet_array as $unit_id => $unit_count) {
    $ships_sent[] = "{$unit_count} {$lang['tech'][$unit_id]}";
    $ships_sent_js += mrc_get_level($user, $planetrow, $unit_id, false, true);
  }
  $ships_sent = implode(', ', $ships_sent);
  $ships_sent_js = "{$unit_group}={$ships_sent_js}";

  $ResultMessage = "{$lang['gs_sending']} {$ships_sent} {$lang['gs_to']} {$target_coord['galaxy']}:{$target_coord['system']}:{$target_coord['planet']}|{$ships_sent_js}";

  die($ResultMessage);
}

fleet_ajax();
