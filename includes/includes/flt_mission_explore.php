<?php

use Fleet\DbFleetStatic;

/**
 * MissionCaseExpedition.php
 *
 * version 2.0 returns results for new fleet handler
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_explore_outcome_lost_fleet(&$result) {
  $fleet = &$result['$fleet'];
  $fleet_lost = &$result['$fleet_lost'];

  $fleet_left = 1 - mt_rand(1, 3) * mt_rand(200000, 300000) / 1000000;
  $fleet_lost = array();
  foreach($fleet as $unit_id => &$unit_amount) {
    $ships_left = floor($unit_amount * $fleet_left);
    $fleet_lost[$unit_id] = $unit_amount - $ships_left;
    $unit_amount = $ships_left;
    if(!$unit_amount) {
      unset($fleet[$unit_id]);
    }
  }
}

function flt_mission_explore_outcome_lost_fleet_all(&$result) {
  $result['$fleet_lost'] = $result['$fleet'];
  $result['$fleet'] = array();
//  $fleet_lost = $fleet;
//  $fleet = array();
}

function flt_mission_explore_addon(&$result){return sn_function_call('flt_mission_explore_addon', array(&$result));}
function sn_flt_mission_explore_addon(&$result) {
  return $result;
}


function flt_mission_explore(&$mission_data) {
  if(!isset($mission_data['fleet_event']) || $mission_data['fleet_event'] != EVENT_FLT_ACOMPLISH) {
    return CACHE_NONE;
  }

  global $lang, $config;

  static $ship_data, $rates;

  $result = array(
    '$mission_data' => $mission_data,
    '$outcome_list' => array(),
    '$mission_outcome' => FLT_EXPEDITION_OUTCOME_NONE,
    '$outcome_value' => 0,
    '$outcome_percent' => 0,
    '$outcome_mission_sub' => -1,

    '$fleet' => array(),
    '$fleet_lost' => array(),
//    '$fleet_left' => 0,
    '$found_dark_matter' => 0,
    '$fleet_metal_points' => 0,
  );
  $fleet = &$result['$fleet'];
//  $fleet_left = &$result['$fleet_left'];
  $fleet_lost = &$result['$fleet_lost'];
  $outcome_mission_sub = &$result['$outcome_mission_sub'];
  $outcome_percent = &$result['$outcome_percent'];
  $found_dark_matter = &$result['$found_dark_matter'];
  $mission_outcome = &$result['$mission_outcome'];
  $outcome_value = &$result['$outcome_value'];
  $outcome_list = &$result['$outcome_list'];
  $fleet_metal_points = &$result['$fleet_metal_points'];

  if(!$ship_data) {
    foreach(sn_get_groups('fleet') as $unit_id) {
      $unit_info = get_unit_param($unit_id);
      if($unit_info[P_UNIT_TYPE] != UNIT_SHIPS || !isset($unit_info['engine'][0]['speed']) || !$unit_info['engine'][0]['speed']) {
        continue;
      }
      $ship_data[$unit_id][P_COST_METAL] = get_unit_cost_in($unit_info[P_COST]);
    }
    $rates = SN::$gc->economicHelper->getResourcesExchange();
  }

  $fleet_row = $mission_data['fleet'];
  $fleet = sys_unit_str2arr($fleet_row['fleet_array']);
  $fleet_capacity = 0;
  $fleet_metal_points = 0;
  foreach($fleet as $ship_id => $ship_amount) {
    $unit_info = get_unit_param($ship_id);
    $fleet_capacity += $ship_amount * $unit_info[P_CAPACITY];
    $fleet_metal_points += $ship_amount * $ship_data[$ship_id][P_COST_METAL];
  }
  $fleet_capacity = max(0, $fleet_capacity - $fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium']);

  $flt_stay_hours = ($fleet_row['fleet_end_stay'] - $fleet_row['fleet_start_time']) / 3600 * ($config->game_speed_expedition ? $config->game_speed_expedition : 1);

  $outcome_list = sn_get_groups(GROUP_MISSION_EXPLORE_OUTCOMES);
  $outcome_list[FLT_EXPEDITION_OUTCOME_NONE]['chance'] = ceil(200 / pow($flt_stay_hours, 1/1.7));

  $chance_max = 0;
  foreach($outcome_list as $key => &$value) {
    if(!$value['chance']) {
      unset($outcome_list[$key]);
      continue;
    }
    $value['value'] = $chance_max = $value['chance'] + $chance_max;
  }
  $outcome_value = mt_rand(0, $chance_max);
// $outcome_value = 409;
  $outcome_description = &$outcome_list[$mission_outcome = FLT_EXPEDITION_OUTCOME_NONE];
  foreach($outcome_list as $key => &$value) {
    if(!$value['chance']) {
      continue;
    }
    $mission_outcome = $key;
    $outcome_description = $value;
    if($outcome_value <= $outcome_description['value']) {
      break;
    }
  }

  // Вычисляем вероятность выпадения данного числа в общем пуле
  $msg_sender = $lang['flt_mission_expedition']['msg_sender'];
  $msg_title = $lang['flt_mission_expedition']['msg_title'];

  $outcome_percent = ($outcome_description['value'] - $outcome_value) / $outcome_description['chance'];

  $msg_text = '';
  $msg_text_addon = '';
  $found_dark_matter = 0;
//  $outcome_mission_sub = -1;

  switch($mission_outcome) {
//  switch(FLT_EXPEDITION_OUTCOME_LOST_FLEET) { // TODO DEBUG!
    case FLT_EXPEDITION_OUTCOME_LOST_FLEET:
      flt_mission_explore_outcome_lost_fleet($result);
//      // $fleet_left = 1 - mt_rand(1, 3) * 0.25;// * 0.25;
//      $fleet_left = 1 - mt_rand(1, 3) * mt_rand(200000, 300000) / 1000000;
//      $fleet_lost = array();
//      foreach($fleet as $unit_id => &$unit_amount) {
//        $ships_left = floor($unit_amount * $fleet_left);
//        $fleet_lost[$unit_id] = $unit_amount - $ships_left;
//        $unit_amount = $ships_left;
//        if(!$unit_amount) {
//          unset($fleet[$unit_id]);
//        }
//      }
    break;

    case FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL:
      flt_mission_explore_outcome_lost_fleet_all($result);
//      $fleet_lost = $fleet;
//      $fleet = array();
    break;

    case FLT_EXPEDITION_OUTCOME_FOUND_FLEET:
      $outcome_mission_sub = $outcome_percent >= 0.99 ? 0 : ($outcome_percent >= 0.90 ? 1 : 2);
      $outcome_percent = $outcome_description['percent'][$outcome_mission_sub];
      // Рассчитываем эквивалент найденного флота в метале
      // $found_in_metal = min($outcome_percent * $fleet_metal_points, $config->resource_multiplier * 10000000); // game_speed
      $found_in_metal = min($outcome_percent * $fleet_metal_points, game_resource_multiplier(true) * 10000000); // game_speed
      //  13 243 754 000 g x1
      //  60 762 247 000 a x10
      // 308 389 499 488 000 b x500

      // Рассчитываем стоимость самого дорого корабля в металле
      $max_metal_cost = 0;
      foreach($fleet as $ship_id => $ship_amount) {
        $max_metal_cost = max($max_metal_cost, $ship_data[$ship_id]['metal_cost']);
      }

      // Ограничиваем корабли только теми, чья стоимость в металле меньше или равно стоимости самого дорогого корабля
      $can_be_found = array();
      foreach($ship_data as $ship_id => $ship_info) {
        if(
          $ship_info['metal_cost'] < $max_metal_cost
          // and not race ship
          && empty(get_unit_param($ship_id, 'player_race'))
          // and not event-related ship
          && empty(get_unit_param($ship_id, REQUIRE_HIGHSPOT))
        ) {
          $can_be_found[$ship_id] = $ship_info['metal_cost'];
        }
      }
      // Убираем колонизаторы и шпионов - миллиарды шпионов и колонизаторов нам не нужны
      unset($can_be_found[SHIP_COLONIZER]);
      unset($can_be_found[SHIP_SPY]);

      $fleet_found = array();
      while(count($can_be_found) && $found_in_metal >= max($can_be_found)) {
        $found_index = mt_rand(1, count($can_be_found)) - 1;
        $found_ship = array_slice($can_be_found, $found_index, 1, true);
        $found_ship_cost = reset($found_ship);
        $found_ship_id = key($found_ship);

        if($found_ship_cost > $found_in_metal) {
          unset($can_be_found[$found_ship_id]);
        } else {
          $found_ship_count = mt_rand(1, floor($found_in_metal / $found_ship_cost));
          $fleet_found[$found_ship_id] += $found_ship_count;
          $found_in_metal -= $found_ship_count * $found_ship_cost;
        }
      }

      if(empty($fleet_found)) {
        $msg_text_addon = $lang['flt_mission_expedition']['outcomes'][$mission_outcome]['no_result'];
      } else {
        foreach($fleet_found as $unit_id => $unit_amount) {
          $fleet[$unit_id] += $unit_amount;
        }
      }
    break;

    case FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES:
      $outcome_mission_sub = $outcome_percent >= 0.99 ? 0 : ($outcome_percent >= 0.90 ? 1 : 2);
      $outcome_percent = $outcome_description['percent'][$outcome_mission_sub];
      // Рассчитываем количество найденных ресурсов
      $found_in_metal = ceil(min($outcome_percent * $fleet_metal_points, game_resource_multiplier(true) * 10000000, $fleet_capacity) * mt_rand(950000, 1050000) / 1000000); // game_speed

      $resources_found[RES_METAL] = floor(mt_rand(300000, 700000) / 1000000 * $found_in_metal);
      $found_in_metal -= $resources_found[RES_METAL];
      $found_in_metal = floor($found_in_metal * $rates[RES_METAL] / $rates[RES_CRYSTAL]);

      $resources_found[RES_CRYSTAL] = floor(mt_rand(500000, 1000000) / 1000000 * $found_in_metal);
      $found_in_metal -= $resources_found[RES_CRYSTAL];
      $found_in_metal = floor($found_in_metal * $rates[RES_CRYSTAL] / $rates[RES_DEUTERIUM]);

      $resources_found[RES_DEUTERIUM] = $found_in_metal;

      $fleet_row['fleet_resource_metal'] += $resources_found[RES_METAL];
      $fleet_row['fleet_resource_crystal'] += $resources_found[RES_CRYSTAL];
      $fleet_row['fleet_resource_deuterium'] += $resources_found[RES_DEUTERIUM];

      if(array_sum($resources_found) == 0) {
        $msg_text_addon = $lang['flt_mission_expedition']['outcomes'][$mission_outcome]['no_result'];
      }
    break;

    case FLT_EXPEDITION_OUTCOME_FOUND_DM:
      $outcome_mission_sub = $outcome_percent >= 0.99 ? 0 : ($outcome_percent >= 0.90 ? 1 : 2);
      $outcome_percent = $outcome_description['percent'][$outcome_mission_sub];
      // Рассчитываем количество найденной ТМ
      $found_dark_matter = floor(min($outcome_percent * $fleet_metal_points / $rates[RES_DARK_MATTER], 10000) * mt_rand(750000, 1000000) / 1000000);

      if(!$found_dark_matter) {
        $msg_text_addon = $lang['flt_mission_expedition']['outcomes'][$mission_outcome]['no_result'];
      }
    break;

    case FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT:
    break;

    default:
    break;
  }

  flt_mission_explore_addon($result);

  if($found_dark_matter) {
    rpg_points_change($fleet_row['fleet_owner'], RPG_EXPEDITION, $found_dark_matter, 'Expedition Bonus');
    $msg_text_addon = sprintf($lang['flt_mission_expedition']['found_dark_matter'], $found_dark_matter);
  }

  if(!empty($fleet_lost)) {
    $msg_text_addon = $lang['flt_mission_expedition']['lost_fleet'];
    foreach($fleet_lost as $ship_id => $ship_amount) {
      $msg_text_addon .= $lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
    }
  }

  $fleet_row['fleet_amount'] = array_sum($fleet);
  if(!empty($fleet) && $fleet_row['fleet_amount']) {
    if(!empty($fleet_found)) {
      $msg_text_addon = $lang['flt_mission_expedition']['found_fleet'];
      foreach($fleet_found as $ship_id => $ship_amount) {
        $msg_text_addon .= $lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
      }
    }

    $query_delta = array();
    if(!empty($resources_found) && array_sum($resources_found) > 0) {
      $msg_text_addon = $lang['flt_mission_expedition']['found_resources'];
      foreach($resources_found as $ship_id => $ship_amount) {
        $msg_text_addon .= $lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
      }

//      $query_data[] = "`fleet_resource_metal` = `fleet_resource_metal` + {$resources_found[RES_METAL]}";
//      $query_data[] = "`fleet_resource_crystal` = `fleet_resource_crystal` + {$resources_found[RES_CRYSTAL]}";
//      $query_data[] = "`fleet_resource_deuterium` = `fleet_resource_deuterium` + {$resources_found[RES_DEUTERIUM]}";
      $query_delta['fleet_resource_metal'] = $resources_found[RES_METAL];
      $query_delta['fleet_resource_crystal'] = $resources_found[RES_CRYSTAL];
      $query_delta['fleet_resource_deuterium'] = $resources_found[RES_DEUTERIUM];
    }

    $query_data = array();
    if(!empty($fleet_lost) || !empty($fleet_found)) {
      $fleet_row['fleet_array'] = sys_unit_arr2str($fleet);

//      $query_data[] = "`fleet_amount` = {$fleet_row['fleet_amount']}";
//      $query_data[] = "`fleet_array` = '{$fleet_row['fleet_array']}'";
      $query_data['fleet_amount'] = $fleet_row['fleet_amount'];
      $query_data['fleet_array'] = $fleet_row['fleet_array'];
    }

//    $query_data[] = '`fleet_mess` = 1';
    $query_data['fleet_mess'] = 1;

//    $query_data = "UPDATE {{fleets}} SET " . implode(',', $query_data);
//    $query_data .=  " WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1";
//    doquery($query_data);
//    db_fleet_update_set_safe_string($fleet_row['fleet_id'], implode(',', $query_data));
    DbFleetStatic::fleet_update_set($fleet_row['fleet_id'], $query_data, $query_delta);
  } else {
    // Удалить флот
//    $query_data = "DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1";
//    doquery($query_data);
    DbFleetStatic::db_fleet_delete($fleet_row['fleet_id']);
  }
//  $query_data .=  " WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1";
//  doquery($query_data);

  db_user_set_by_id($fleet_row['fleet_owner'], "`player_rpg_explore_xp` = `player_rpg_explore_xp` + 1");

  if(!$msg_text) {
    $messages = &$lang['flt_mission_expedition']['outcomes'][$mission_outcome]['messages'];
    if($outcome_mission_sub >= 0 && is_array($messages)) {
      $messages = &$messages[$outcome_mission_sub];
    }

    $msg_text = is_string($messages) ? $messages :
      (is_array($messages) ? $messages[mt_rand(0, count($messages) - 1)] : '');
  }
  $msg_text = sprintf($msg_text, $fleet_row['fleet_id'], uni_render_coordinates($fleet_row, 'fleet_end_')) .
    ($msg_text_addon ? "\r\n" . $msg_text_addon: '');

  msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $msg_text);

  return CACHE_FLEET | CACHE_USER_SRC;
}
