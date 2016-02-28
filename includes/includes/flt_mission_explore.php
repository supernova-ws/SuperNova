<?php

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
}

// Used by Festival
function mission_expedition_result_adjust(&$result) { return sn_function_call(__FUNCTION__, array(&$result)); }
function sn_mission_expedition_result_adjust(&$result) {
  return $result;
}


/**
 * Fleet mission "Relocate"
 *
 * @param $mission_data Mission
 *
 * @return int
 *
 * @copyright 2008 by Gorlum for Project "SuperNova.WS"
 */
function flt_mission_explore(&$mission_data) {
  global $lang, $config;
  static $ship_data, $rates;

  if(empty($mission_data->fleet_event) || $mission_data->fleet_event != EVENT_FLT_ACOMPLISH) {
    return CACHE_NONE;
  }

  $objFleet = $mission_data->fleet;

  $result = array(
    '$mission_data'        => $mission_data,
    '$outcome_list'        => array(),
    '$mission_outcome'     => FLT_EXPEDITION_OUTCOME_NONE,
    '$outcome_value'       => 0,
    '$outcome_percent'     => 0,
    '$outcome_mission_sub' => -1,

    '$fleet'              => array(),
    '$fleet_lost'         => array(),
    '$found_dark_matter'  => 0,
    '$fleet_metal_points' => 0,
  );
  $fleet_real_array = &$result['$fleet'];
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
    $rates = get_resource_exchange();
  }


  $fleet_capacity = 0;
  $fleet_metal_points = 0;

  $fleet_real_array = $objFleet->get_unit_list();
  foreach($fleet_real_array as $ship_id => $ship_amount) {
    $unit_info = get_unit_param($ship_id);
    $fleet_capacity += $ship_amount * $unit_info[P_CAPACITY];
    $fleet_metal_points += $ship_amount * $ship_data[$ship_id][P_COST_METAL];
  }
  $fleet_capacity = max(0, $fleet_capacity - $objFleet->get_resources_amount());

  $flt_stay_hours = ($objFleet->time_mission_job_complete - $objFleet->time_arrive_to_target) / 3600 * ($config->game_speed_expedition ? $config->game_speed_expedition : 1);

  $outcome_list = sn_get_groups('mission_explore_outcome_list');
  $outcome_list[FLT_EXPEDITION_OUTCOME_NONE]['chance'] = ceil(200 / pow($flt_stay_hours, 1 / 1.7));

  $chance_max = 0;
  foreach($outcome_list as $key => &$value) {
    if(!$value['chance']) {
      unset($outcome_list[$key]);
      continue;
    }
    $value['value'] = $chance_max = $value['chance'] + $chance_max;
  }
  $outcome_value = mt_rand(0, $chance_max);
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

  $fleet_found = array();
  switch($mission_outcome) {
    case FLT_EXPEDITION_OUTCOME_LOST_FLEET:
      flt_mission_explore_outcome_lost_fleet($result);
    break;

    case FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL:
      flt_mission_explore_outcome_lost_fleet_all($result);
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
      foreach($fleet_real_array as $ship_id => $ship_amount) {
        $max_metal_cost = max($max_metal_cost, $ship_data[$ship_id]['metal_cost']);
      }

      // Ограничиваем корабли только теми, чья стоимость в металле меньше или равно стоимости самого дорогого корабля
      $can_be_found = array();
      foreach($ship_data as $ship_id => $ship_info) {
        if($ship_info['metal_cost'] < $max_metal_cost) {
          $can_be_found[$ship_id] = $ship_info['metal_cost'];
        }
      }
      // Убираем колонизаторы и шпионов - миллиарды шпионов и колонизаторов нам не нужны
      unset($can_be_found[SHIP_COLONIZER]);
      unset($can_be_found[SHIP_SPY]);

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
          $fleet_real_array[$unit_id] += $unit_amount;
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

      $objFleet->update_resources($resources_found);

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

  mission_expedition_result_adjust($result);

  if($found_dark_matter) {
    rpg_points_change($objFleet->owner_id, RPG_EXPEDITION, $found_dark_matter, 'Expedition Bonus');
    $msg_text_addon = sprintf($lang['flt_mission_expedition']['found_dark_matter'], $found_dark_matter);
  }

  if(!empty($fleet_lost)) {
    $msg_text_addon = $lang['flt_mission_expedition']['lost_fleet'];
    foreach($fleet_lost as $ship_id => $ship_amount) {
      $msg_text_addon .= $lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
    }
  }

  if(!empty($fleet_found)) {
    $msg_text_addon = $lang['flt_mission_expedition']['found_fleet'];
    foreach($fleet_found as $ship_id => $ship_amount) {
      $msg_text_addon .= $lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
    }
  }

  if(!empty($resources_found) && array_sum($resources_found) > 0) {
    $msg_text_addon = $lang['flt_mission_expedition']['found_resources'];
    foreach($resources_found as $resource_id => $resource_amount) {
      $msg_text_addon .= $lang['tech'][$resource_id] . ' - ' . $resource_amount . "\r\n";
    }
  }

  if(!$msg_text) {
    $messages = &$lang['flt_mission_expedition']['outcomes'][$mission_outcome]['messages'];
    if($outcome_mission_sub >= 0 && is_array($messages)) {
      $messages = &$messages[$outcome_mission_sub];
    }

    $msg_text = is_string($messages) ? $messages :
      (is_array($messages) ? $messages[mt_rand(0, count($messages) - 1)] : '');
  }

  $fleet_row_end_coordinates_without_type = $objFleet->target_coordinates_without_type();

  $msg_text = sprintf($msg_text, $objFleet->db_id, uni_render_coordinates($fleet_row_end_coordinates_without_type)) .
    ($msg_text_addon ? "\r\n" . $msg_text_addon : '');

  msg_send_simple_message($objFleet->owner_id, '', $objFleet->time_mission_job_complete, MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $msg_text);

  db_user_set_by_id($objFleet->owner_id, "`player_rpg_explore_xp` = `player_rpg_explore_xp` + 1");

  if(!empty($fleet_real_array) && $objFleet->get_ship_count() >= 1) {
    // ПОКА НЕ НУЖНО - мы уже выше посчитали суммарные ресурсы (те, что были до отправку в экспу плюс найденное) и обновили $fleet_row
    // НО МОЖЕТ ПРИГОДИТЬСЯ, когда будем работать напрямую с $objFleet
//    if(!empty($resources_found) && array_sum($resources_found) > 0) {
//      $objFleet->update_resources($resources_found); // TODO - проверить, что бы не терялись ресурсы в трюме
//    }

    if(!empty($fleet_lost) || !empty($fleet_found)) {
      $objFleet->replace_ships($fleet_real_array);
    }
    $objFleet->mark_fleet_as_returned();
    $objFleet->flush_changes_to_db();
  } else {
    // Удалить флот
    $objFleet->method_db_delete_this_fleet();
    // From this point $fleet_row is useless - all data are put in local variables
  }

  return CACHE_FLEET | CACHE_USER_SRC;
}
