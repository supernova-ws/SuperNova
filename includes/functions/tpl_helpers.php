<?php

// Compare function to sort fleet in time order
function tpl_assign_fleet_compare($a, $b) {
  if($a['fleet']['OV_THIS_PLANET'] == $b['fleet']['OV_THIS_PLANET']) {
    if($a['fleet']['OV_LEFT'] == $b['fleet']['OV_LEFT']) {
      return 0;
    }

    return ($a['fleet']['OV_LEFT'] < $b['fleet']['OV_LEFT']) ? -1 : 1;
  } else {
    return $a['fleet']['OV_THIS_PLANET'] ? -1 : 1;
  }
}

function tpl_assign_fleet(&$template, $fleets, $js_name = 'fleets') {
  if(!$fleets) {
    return;
  }

  usort($fleets, 'tpl_assign_fleet_compare');

  foreach($fleets as $fleet_data) {
    $template->assign_block_vars($js_name, $fleet_data['fleet']);

    if($fleet_data['ships']) {
      foreach($fleet_data['ships'] as $ship_data) {
        $template->assign_block_vars("{$js_name}.ships", $ship_data);
      }
    }
  }
}

// function that parses internal fleet representation (as array(id => count))
function tpl_parse_fleet_sn($fleet, $fleet_id) {
  global $lang, $user;

  $user_data = &$user;

  $return['fleet'] = array(
    'ID' => $fleet_id,

    'METAL'     => $fleet[RES_METAL],
    'CRYSTAL'   => $fleet[RES_CRYSTAL],
    'DEUTERIUM' => $fleet[RES_DEUTERIUM],
  );

  foreach($fleet as $ship_id => $ship_amount) {
    if(in_array($ship_id, sn_get_groups('fleet'))) {
      $single_ship_data = get_ship_data($ship_id, $user_data);
      $return['ships'][$ship_id] = array(
        'ID'          => $ship_id,
        'NAME'        => $lang['tech'][$ship_id],
        'AMOUNT'      => $ship_amount,
        'CONSUMPTION' => $single_ship_data['consumption'],
        'SPEED'       => $single_ship_data['speed'],
        'CAPACITY'    => $single_ship_data['capacity'],
      );
    }
  }

  return $return;
}

function tpl_parse_fleet_db($fleet_row, $index, $user_data = false) { return sn_function_call('tpl_parse_fleet_db', array($fleet_row, $index, $user_data, &$result)); }

function sn_tpl_parse_fleet_db($fleet_row, $index, $user_data = false, &$result) {
  global $lang, $user;

  if(!$user_data) {
    $user_data = $user;
  }

  if($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_mission'] == MT_AKS) {
    $aks = doquery("SELECT * FROM {{aks}} WHERE id={$fleet_row['fleet_group']} LIMIT 1;", true);
  }

  $spy_level = $user['id'] == $fleet_row['fleet_owner'] ? 100 : GetSpyLevel($user);

  $result['fleet'] = isset($result['fleet']) ? $result['fleet'] : array();

  $result['fleet'] = array(
    'NUMBER' => $index,

    'ID'           => $fleet_row['fleet_id'],
    'OWNER'        => $fleet_row['fleet_owner'],
    'TARGET_OWNER' => $fleet_row['fleet_target_owner'],

    'MESSAGE'      => $fleet_row['fleet_mess'],
    'MISSION'      => $fleet_row['fleet_mission'],
    'MISSION_NAME' => $lang['type_mission'][$fleet_row['fleet_mission']],
    'ACS'          => $aks['name'],
    'AMOUNT'       => $spy_level >= 4 ? (pretty_number($fleet_row['fleet_amount']) . ($fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'] ? '+' : '')) : '?',

    'METAL'     => $spy_level >= 8 ? $fleet_row['fleet_resource_metal'] : 0,
    'CRYSTAL'   => $spy_level >= 8 ? $fleet_row['fleet_resource_crystal'] : 0,
    'DEUTERIUM' => $spy_level >= 8 ? $fleet_row['fleet_resource_deuterium'] : 0,

    'START_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$fleet_row['fleet_start_type']],
    'START_COORDS'       => "[{$fleet_row['fleet_start_galaxy']}:{$fleet_row['fleet_start_system']}:{$fleet_row['fleet_start_planet']}]",
    'START_TIME_TEXT'    => date(FMT_DATE_TIME, $fleet_row['fleet_end_time'] + SN_CLIENT_TIME_DIFF),
    'START_LEFT'         => floor($fleet_row['fleet_end_time'] + 1 - SN_TIME_NOW),
    'START_URL'          => uni_render_coordinates_href($fleet_row, 'fleet_start_', 3),
    'START_NAME'         => $fleet_row['fleet_start_name'],

    'END_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$fleet_row['fleet_end_type']],
    'END_COORDS'       => "[{$fleet_row['fleet_end_galaxy']}:{$fleet_row['fleet_end_system']}:{$fleet_row['fleet_end_planet']}]",
    'END_TIME_TEXT'    => date(FMT_DATE_TIME, $fleet_row['fleet_start_time'] + SN_CLIENT_TIME_DIFF),
    'END_LEFT'         => floor($fleet_row['fleet_start_time'] + 1 - SN_TIME_NOW),
    'END_URL'          => uni_render_coordinates_href($fleet_row, 'fleet_end_', 3),
    'END_NAME'         => $fleet_row['fleet_end_name'],

    'STAY_TIME' => date(FMT_DATE_TIME, $fleet_row['fleet_end_stay'] + SN_CLIENT_TIME_DIFF),
    'STAY_LEFT' => floor($fleet_row['fleet_end_stay'] + 1 - SN_TIME_NOW),

    'OV_LABEL'        => $fleet_row['ov_label'],
    'EVENT_TIME_TEXT' => date(FMT_DATE_TIME, $fleet_row['event_time'] + SN_CLIENT_TIME_DIFF),
    'OV_LEFT'         => floor($fleet_row['event_time'] + 1 - SN_TIME_NOW),
    'OV_THIS_PLANET'  => $fleet_row['ov_this_planet'],
  );

  $ship_list_fully_parsed = Fleet::proxy_string_to_array($fleet_row);

  $ship_id = 0;
  if($spy_level >= 6) {
    foreach($ship_list_fully_parsed as $ship_sn_id => $ship_amount) {
      if($spy_level >= 10) {
        $single_ship_data = get_ship_data($ship_sn_id, $user_data);
        $result['ships'][$ship_sn_id] = array(
          'ID'          => $ship_sn_id,
          'NAME'        => $lang['tech'][$ship_sn_id],
          'AMOUNT'      => $ship_amount,
          'AMOUNT_TEXT' => pretty_number($ship_amount),
          'CONSUMPTION' => $single_ship_data['consumption'],
          'SPEED'       => $single_ship_data['speed'],
          'CAPACITY'    => $single_ship_data['capacity'],
        );
      } else {
        $result['ships'][$ship_sn_id] = array(
          'ID'               => $ship_id++,
          'NAME'             => $lang['tech'][UNIT_SHIPS],
          'AMOUNT'           => $ship_amount,
          'AMOUNT_TEXT'      => pretty_number($ship_amount),
          'CONSUMPTION'      => 0,
          'CONSUMPTION_TEXT' => '0',
          'SPEED'            => 0,
          'CAPACITY'         => 0,
        );
      }
    }
  }

  return $result;
}

function tpl_parse_planet_que($que, $planet, $que_id) {
  $hangar_que = array();
  $que_hangar = $que['ques'][$que_id][$planet['id_owner']][$planet['id']];
  if(!empty($que_hangar)) {
    foreach($que_hangar as $que_item) {
      $hangar_que['que'][] = array('id' => $que_item['que_unit_id'], 'count' => $que_item['que_unit_amount']);
      $hangar_que[$que_item['que_unit_id']] += $que_item['que_unit_amount'];
    }
  }

  return $hangar_que;
}

function tpl_parse_planet($planet) {
  global $lang;

  $fleet_list = flt_get_fleets_to_planet($planet);

  $que = que_get($planet['id_owner'], $planet['id'], false);

  $structure_que = tpl_parse_planet_que($que, $planet, QUE_STRUCTURES); // TODO Заменить на que_tpl_parse_element($que_element);
  $structure_que_first = is_array($structure_que['que']) ? reset($structure_que['que']) : array();
  $hangar_que = tpl_parse_planet_que($que, $planet, SUBQUE_FLEET); // TODO Заменить на que_tpl_parse_element($que_element);
  $hangar_que_first = is_array($hangar_que['que']) ? reset($hangar_que['que']) : array();
  $defense_que = tpl_parse_planet_que($que, $planet, SUBQUE_DEFENSE); // TODO Заменить на que_tpl_parse_element($que_element);
  $defense_que_first = is_array($defense_que['que']) ? reset($defense_que['que']) : array();

  $result = array(
    'ID'    => $planet['id'],
    'NAME'  => $planet['name'],
    'IMAGE' => $planet['image'],

    'GALAXY'      => $planet['galaxy'],
    'SYSTEM'      => $planet['system'],
    'PLANET'      => $planet['planet'],
    'TYPE'        => $planet['planet_type'],
    'COORDINATES' => uni_render_coordinates($planet),

    'METAL_PERCENT'     => $planet['metal_mine_porcent'] * 10,
    'CRYSTAL_PERCENT'   => $planet['crystal_mine_porcent'] * 10,
    'DEUTERIUM_PERCENT' => $planet['deuterium_sintetizer_porcent'] * 10,

    'STRUCTURE' => isset($structure_que_first['id']) ? $lang['tech'][$structure_que_first['id']] : '',

    'HANGAR'     => isset($hangar_que_first['id']) ? $lang['tech'][$hangar_que_first['id']] : '',
    'hangar_que' => $hangar_que,

    'DEFENSE'     => isset($defense_que_first['id']) ? $lang['tech'][$defense_que_first['id']] : '',
    'defense_que' => $defense_que,

    'FIELDS_CUR' => $planet['field_current'],
    'FIELDS_MAX' => eco_planet_fields_max($planet),
    'FILL'       => min(100, floor($planet['field_current'] / eco_planet_fields_max($planet) * 100)),

    'FLEET_OWN'     => $fleet_list['own']['count'],
    'FLEET_ENEMY'   => $fleet_list['enemy']['count'],
    'FLEET_NEUTRAL' => $fleet_list['neutral']['count'],

    'fleet_list' => $fleet_list,

    'PLANET_GOVERNOR_ID'        => $planet['PLANET_GOVERNOR_ID'],
    'PLANET_GOVERNOR_NAME'      => $lang['tech'][$planet['PLANET_GOVERNOR_ID']],
    'PLANET_GOVERNOR_LEVEL'     => $planet['PLANET_GOVERNOR_LEVEL'],
    'PLANET_GOVERNOR_LEVEL_MAX' => get_unit_param($planet['PLANET_GOVERNOR_ID'], P_MAX_STACK),
  );

  if(!empty($que['ques'][QUE_STRUCTURES][$planet['id_owner']][$planet['id']])) {
    $result['building_que'] = array();
    $building_que = &$que['ques'][QUE_STRUCTURES][$planet['id_owner']][$planet['id']];
    foreach($building_que as $que_element) {
      $result['building_que'][] = que_tpl_parse_element($que_element);
    }
  }

  return $result;
}

function flt_get_fleets_to_planet($planet, $fleet_db_list = 0) {
  if(!($planet && $planet['id']) && !$fleet_db_list) {
    return $planet;
  }

  global $user;

  if($fleet_db_list === 0) {
    $fleet_db_list = fleet_and_missiles_list_by_coordinates($planet);
  }

  foreach($fleet_db_list as $fleet_row) {
    if($fleet_row['fleet_owner'] == $user['id']) {
      if($fleet_row['fleet_mission'] == MT_MISSILE) {
        continue;
      }
      $fleet_ownage = 'own';
    } else {
      switch($fleet_row['fleet_mission']) {
        case MT_ATTACK:
        case MT_AKS:
        case MT_DESTROY:
        case MT_MISSILE:
          $fleet_ownage = 'enemy';
        break;

        default:
          $fleet_ownage = 'neutral';
        break;

      }
    }

    $fleet_list[$fleet_ownage]['fleets'][$fleet_row['fleet_id']] = $fleet_row;

    if($fleet_row['fleet_mess'] == 1 || ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_mission'] == MT_RELOCATE) || ($fleet_row['fleet_target_owner'] != $user['id'])) {
      $fleet_sn = Fleet::proxy_string_to_array($fleet_row);
      foreach($fleet_sn as $ship_id => $ship_amount) {
        if(in_array($ship_id, sn_get_groups('fleet'))) {
          $fleet_list[$fleet_ownage]['total'][$ship_id] += $ship_amount;
        }
      }
    }

    $fleet_list[$fleet_ownage]['count']++;
    $fleet_list[$fleet_ownage]['amount'] += $fleet_row['fleet_amount'];
    $fleet_list[$fleet_ownage]['total'][RES_METAL] += $fleet_row['fleet_resource_metal'];
    $fleet_list[$fleet_ownage]['total'][RES_CRYSTAL] += $fleet_row['fleet_resource_crystal'];
    $fleet_list[$fleet_ownage]['total'][RES_DEUTERIUM] += $fleet_row['fleet_resource_deuterium'];
  }

  return $fleet_list;
}

function tpl_set_resource_info(&$template, $planetrow, $fleets_to_planet = array(), $round = 0) {
  $template->assign_vars(array(
    'RESOURCE_ROUNDING' => $round,

    'ENERGY_BALANCE' => pretty_number($planetrow['energy_max'] - $planetrow['energy_used'], true, true),
    'ENERGY_MAX'     => pretty_number($planetrow['energy_max'], true, -$planetrow['energy_used']),
    'ENERGY_FILL'    => round(($planetrow["energy_used"] / ($planetrow["energy_max"] + 1)) * 100, 0),

    'PLANET_METAL'            => round($planetrow["metal"], $round),
    'PLANET_METAL_TEXT'       => pretty_number($planetrow["metal"], $round, $planetrow["metal_max"]),
    'PLANET_METAL_MAX'        => round($planetrow["metal_max"], $round),
    'PLANET_METAL_MAX_TEXT'   => pretty_number($planetrow["metal_max"], $round, -$planetrow["metal"]),
    'PLANET_METAL_FILL'       => round(($planetrow["metal"] / ($planetrow["metal_max"] + 1)) * 100, 0),
    'PLANET_METAL_PERHOUR'    => round($planetrow["metal_perhour"], 5),
    'PLANET_METAL_FLEET_TEXT' => pretty_number($fleets_to_planet[$planetrow['id']]['fleet']['METAL'], $round, true),

    'PLANET_CRYSTAL'            => round($planetrow["crystal"], $round),
    'PLANET_CRYSTAL_TEXT'       => pretty_number($planetrow["crystal"], $round, $planetrow["crystal_max"]),
    'PLANET_CRYSTAL_MAX'        => round($planetrow["crystal_max"], $round),
    'PLANET_CRYSTAL_MAX_TEXT'   => pretty_number($planetrow["crystal_max"], $round, -$planetrow["crystal"]),
    'PLANET_CRYSTAL_FILL'       => round(($planetrow["crystal"] / ($planetrow["crystal_max"] + 1)) * 100, 0),
    'PLANET_CRYSTAL_PERHOUR'    => round($planetrow["crystal_perhour"], 5),
    'PLANET_CRYSTAL_FLEET_TEXT' => pretty_number($fleets_to_planet[$planetrow['id']]['fleet']['CRYSTAL'], $round, true),

    'PLANET_DEUTERIUM'            => round($planetrow["deuterium"], $round),
    'PLANET_DEUTERIUM_TEXT'       => pretty_number($planetrow["deuterium"], $round, $planetrow["deuterium_max"]),
    'PLANET_DEUTERIUM_MAX'        => round($planetrow["deuterium_max"], $round),
    'PLANET_DEUTERIUM_MAX_TEXT'   => pretty_number($planetrow["deuterium_max"], $round, -$planetrow["deuterium"]),
    'PLANET_DEUTERIUM_FILL'       => round(($planetrow["deuterium"] / ($planetrow["deuterium_max"] + 1)) * 100, 0),
    'PLANET_DEUTERIUM_PERHOUR'    => round($planetrow["deuterium_perhour"], 5),
    'PLANET_DEUTERIUM_FLEET_TEXT' => pretty_number($fleets_to_planet[$planetrow['id']]['fleet']['DEUTERIUM'], $round, true),
  ));
}
