<?php

function eco_lab_sort_effectivness($a, $b) {
  return $a['laboratory_effective_level'] > $b['laboratory_effective_level'] ? -1 : ($a['laboratory_effective_level'] < $b['laboratory_effective_level'] ? 1 : 0);
}

/**
 * eco_get_build_data.php
 *
 * 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 * @version 1.0
 */
function eco_get_lab_max_effective_level(&$user, $lab_require) {
  if (!$user['user_as_ally'] && !isset($user['laboratories_active'])) {
    $user['laboratories_active'] = array();
    $query = DBStaticUnit::db_unit_list_laboratories($user['id']);
    while ($row = db_fetch($query)) {
      if (!eco_unit_busy($user, $row, UNIT_TECHNOLOGIES)) {
        $row += array(
          STRUC_LABORATORY             => $level_lab = mrc_get_level($user, $row, STRUC_LABORATORY),
          STRUC_LABORATORY_NANO        => $level_lab_nano = mrc_get_level($user, $row, STRUC_LABORATORY_NANO),
          'laboratory_effective_level' => $level_lab * pow(2, $level_lab_nano),
        );
        $user['laboratories_active'][$row['id']] = $row;
      }
    }

    uasort($user['laboratories_active'], 'eco_lab_sort_effectivness');
  }

  if (!isset($user['research_effective_level'][$lab_require])) {
    if ($user['user_as_ally']) {
      $lab_level = db_ally_get_ally_count($user);
    } else {
      $tech_intergalactic = mrc_get_level($user, null, TECH_RESEARCH) + 1;
      $lab_level['effective_level'] = 0;

      foreach ($user['laboratories_active'] as $data) {
        if ($tech_intergalactic <= 0) {
          break;
        }
        if ($data[STRUC_LABORATORY] >= $lab_require) {
          $lab_level['effective_level'] += $data['laboratory_effective_level'];
          $tech_intergalactic--;
        }
      }
    }
    $user['research_effective_level'][$lab_require] = $lab_level['effective_level'] ? $lab_level['effective_level'] : 1;
  }

  return $user['research_effective_level'][$lab_require];
}

function eco_get_build_data(&$user, $planet, $unit_id, $unit_level = 0, $only_cost = false) {
  $rpg_exchange_deuterium = classSupernova::$config->rpg_exchange_deuterium;

  $unit_data = get_unit_param($unit_id);
  // $unit_db_name = &$unit_data[P_NAME];

  $unit_factor = $unit_data[P_COST][P_FACTOR] ? $unit_data[P_COST][P_FACTOR] : 1;
  $price_increase = pow($unit_factor, $unit_level);

  $can_build = isset($unit_data[P_MAX_STACK]) && $unit_data[P_MAX_STACK] ? $unit_data[P_MAX_STACK] : 1000000000000;
  $can_destroy = 1000000000000;
  $time = 0;
  $only_dark_matter = 0;
  $cost_in_metal = 0;
  $cost = array();
  foreach ($unit_data[P_COST] as $resource_id => $resource_amount) {
    if ($resource_id === P_FACTOR || !($resource_cost = $resource_amount * $price_increase)) {
      continue;
    }

    $cost[BUILD_CREATE][$resource_id] = round($resource_cost);
    $cost[BUILD_DESTROY][$resource_id] = round($resource_cost / 2);

    $resource_db_name = pname_resource_name($resource_id);
    $cost_in_metal += $cost[BUILD_CREATE][$resource_id] * classSupernova::$config->__get("rpg_exchange_{$resource_db_name}");
    if (in_array($resource_id, sn_get_groups('resources_loot'))) {
      $time += $resource_cost * classSupernova::$config->__get("rpg_exchange_{$resource_db_name}") / $rpg_exchange_deuterium;
      $resource_got = mrc_get_level($user, $planet, $resource_id);
    } elseif ($resource_id == RES_DARK_MATTER) {
      $resource_got = mrc_get_level($user, null, $resource_id);
    } elseif ($resource_id == RES_ENERGY) {
      $resource_got = max(0, $planet['energy_max'] - $planet['energy_used']);
    } else {
      $resource_got = 0;
    }
    $only_dark_matter = $only_dark_matter ? $only_dark_matter : $resource_id;

    $can_build = min($can_build, $resource_got / $cost[BUILD_CREATE][$resource_id]);
    $can_destroy = min($can_destroy, $resource_got / $cost[BUILD_DESTROY][$resource_id]);
  }

  $resources_normalized = 0;
  $resources_loot = sn_get_groups('resources_loot');
  foreach ($resources_loot as $resource_id) {
    $resource_db_name = pname_resource_name($resource_id);
    $resource_got = mrc_get_level($user, $planet, $resource_id);
    $resources_normalized += floor($resource_got) * classSupernova::$config->__get("rpg_exchange_{$resource_db_name}");
  }

  $cost[BUILD_AUTOCONVERT] = $only_dark_matter != RES_DARK_MATTER ? max(!empty($unit_data[P_MAX_STACK]) ? $unit_data[P_MAX_STACK] : 0, floor($resources_normalized / $cost_in_metal)) : 0;

  $can_build = $can_build > 0 ? floor($can_build) : 0;
  $cost['CAN'][BUILD_CREATE] = $can_build;

  $can_destroy = $can_destroy > 0 ? floor($can_destroy) : 0;
  $cost['CAN'][BUILD_DESTROY] = $can_destroy;

  $cost[P_OPTIONS][P_ONLY_DARK_MATTER] = $only_dark_matter = $only_dark_matter == RES_DARK_MATTER;
  $cost[P_OPTIONS][P_TIME_RAW] = $time = $time * 60 * 60 / get_game_speed() / 2500;

  // TODO - Вынести в отдельную процедуру расчёт стоимости
  if ($only_cost) {
    return $cost;
  }

  $cost['RESULT'][BUILD_CREATE] = eco_can_build_unit($user, $planet, $unit_id);
  $cost['RESULT'][BUILD_CREATE] = $cost['RESULT'][BUILD_CREATE] == BUILD_ALLOWED ? ($cost['CAN'][BUILD_CREATE] ? BUILD_ALLOWED : BUILD_NO_RESOURCES) : $cost['RESULT'][BUILD_CREATE];

  $mercenary = 0;
  $cost['RESULT'][BUILD_DESTROY] = BUILD_INDESTRUCTABLE;
  if (in_array($unit_id, sn_get_groups('structures'))) {
    $time = $time * pow(0.5, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) / (mrc_get_level($user, $planet, STRUC_FACTORY_ROBOT) + 1);
    $mercenary = MRC_ENGINEER;
    $cost['RESULT'][BUILD_DESTROY] =
      mrc_get_level($user, $planet, $unit_id, null, true)
        ? ($cost['CAN'][BUILD_DESTROY]
        ? ($cost['RESULT'][BUILD_CREATE] == BUILD_UNIT_BUSY ? BUILD_UNIT_BUSY : BUILD_ALLOWED)
        : BUILD_NO_RESOURCES
      )
        : BUILD_NO_UNITS;
  } elseif (in_array($unit_id, sn_get_groups('tech'))) {
    $lab_level = eco_get_lab_max_effective_level($user, intval($unit_data['require'][STRUC_LABORATORY]));
    $time = $time / $lab_level;
    $mercenary = MRC_ACADEMIC;
  } elseif (in_array($unit_id, sn_get_groups('defense'))) {
    $time = $time * pow(0.5, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) / (mrc_get_level($user, $planet, STRUC_FACTORY_HANGAR) + 1);
    $mercenary = MRC_FORTIFIER;
  } elseif (in_array($unit_id, sn_get_groups('fleet'))) {
    $time = $time * pow(0.5, mrc_get_level($user, $planet, STRUC_FACTORY_NANO)) / (mrc_get_level($user, $planet, STRUC_FACTORY_HANGAR) + 1);
    $mercenary = MRC_ENGINEER;
  }

  if ($mercenary) {
    $time = $time / mrc_modify_value($user, $planet, $mercenary, 1);
  }

  if (in_array($unit_id, sn_get_groups('governors')) || $only_dark_matter) {
    $cost[RES_TIME][BUILD_CREATE] = $cost[RES_TIME][BUILD_DESTROY] = 0;
  } else {
    $cost[RES_TIME][BUILD_CREATE] = round($time >= 1 ? $time : 1);
    $cost[RES_TIME][BUILD_DESTROY] = round($time / 2 <= 1 ? 1 : $time / 2);
  }

  return $cost;
}

function eco_can_build_unit($user, $planet, $unit_id) { return sn_function_call(__FUNCTION__, array($user, $planet, $unit_id, &$result)); }

function sn_eco_can_build_unit($user, $planet, $unit_id, &$result) {
  $result = isset($result) ? $result : BUILD_ALLOWED;
  $result = $result == BUILD_ALLOWED && eco_unit_busy($user, $planet, $unit_id) ? BUILD_UNIT_BUSY : $result;

  $unit_param = get_unit_param($unit_id);
  if ($unit_param[P_UNIT_TYPE] != UNIT_MERCENARIES || !classSupernova::$config->empire_mercenary_temporary) {
    $requirement = &$unit_param[P_REQUIRE];
    if ($result == BUILD_ALLOWED && $requirement) {
      foreach ($requirement as $require_id => $require_level) {
        if (mrc_get_level($user, $planet, $require_id) < $require_level) {
          $result = BUILD_REQUIRE_NOT_MEET;
          break;
        }
      }
    }
  }

  return $result;
}

function eco_is_builds_in_que($planet_que, $unit_list) {
  $eco_is_builds_in_que = false;

  $unit_list = is_array($unit_list) ? $unit_list : array($unit_list => $unit_list);
  $planet_que = explode(';', $planet_que);
  foreach ($planet_que as $planet_que_item) {
    if ($planet_que_item) {
      list($planet_que_item) = explode(',', $planet_que_item);
      if (in_array($planet_que_item, $unit_list)) {
        $eco_is_builds_in_que = true;
        break;
      }
    }
  }

  return $eco_is_builds_in_que;
}

function eco_unit_busy(&$user, &$planet, $unit_id) { return sn_function_call(__FUNCTION__, array(&$user, &$planet, $unit_id, &$result)); }

function sn_eco_unit_busy(&$user, &$planet, $unit_id, &$result) {
  $result = isset($result) ? $result : false;
  if (!$result) {
    if (($unit_id == STRUC_LABORATORY || $unit_id == STRUC_LABORATORY_NANO) && !classSupernova::$config->BuildLabWhileRun) {
      $global_que = que_get($user['id'], $planet['id'], QUE_RESEARCH, false);
      if (is_array($global_que['ques'][QUE_RESEARCH][$user['id']])) {
        $first_element = reset($global_que['ques'][QUE_RESEARCH][$user['id']]);
        if (is_array($first_element)) {
          $result = true;
        }
      }
      //if(!empty($global_que['ques'][QUE_RESEARCH][$user['id']][0]))
      //{
      //  $result = true;
      //}
    } elseif (($unit_id == UNIT_TECHNOLOGIES || in_array($unit_id, sn_get_groups('tech'))) && !classSupernova::$config->BuildLabWhileRun && $planet['que']) {
      $result = eco_is_builds_in_que($planet['que'], array(STRUC_LABORATORY, STRUC_LABORATORY_NANO));
    }
  }

//  switch($unit_id)
//  {
//    case STRUC_FACTORY_HANGAR:
//      $hangar_busy = $planet['b_hangar'] && $planet['b_hangar_id'];
//      $return = $hangar_busy;
//    break;
//  }

  return $result;
}
