<?php

use Unit\DBStaticUnit;

/**
 * Created by Gorlum 04.12.2017 4:32
 */

function eco_get_total_cost($unit_id, $unit_level) {
  static $rate, $sn_group_resources_all, $sn_group_resources_loot;
  if (!$rate) {
    $sn_group_resources_all = sn_get_groups('resources_all');
    $sn_group_resources_loot = sn_get_groups('resources_loot');

    $rate[RES_METAL] = SN::$config->rpg_exchange_metal;
    $rate[RES_CRYSTAL] = SN::$config->rpg_exchange_crystal / SN::$config->rpg_exchange_metal;
    $rate[RES_DEUTERIUM] = SN::$config->rpg_exchange_deuterium / SN::$config->rpg_exchange_metal;
  }

  $unit_cost_data = get_unit_param($unit_id, 'cost');
  if (!is_array($unit_cost_data)) {
    return array('total' => 0);
  }
  $factor = isset($unit_cost_data['factor']) ? $unit_cost_data['factor'] : 1;
  $cost_array = array(BUILD_CREATE => array(), 'total' => 0);
  $unit_level = $unit_level > 0 ? $unit_level : 0;
  foreach ($unit_cost_data as $resource_id => $resource_amount) {
    if (!in_array($resource_id, $sn_group_resources_all)) {
      continue;
    }
    $cost_array[BUILD_CREATE][$resource_id] = round($resource_amount * ($factor == 1 ? $unit_level : ((1 - pow($factor, $unit_level)) / (1 - $factor))));
    if (in_array($resource_id, $sn_group_resources_loot)) {
      $cost_array['total'] += $cost_array[BUILD_CREATE][$resource_id] * $rate[$resource_id];
    }
  }

  return $cost_array;
}

function sn_unit_purchase($unit_id) { }

function sn_unit_relocate($unit_id, $from, $to) { }

/**
 * @param array $user
 * @param array $planet     ([])
 * @param int   $unit_id
 * @param bool  $for_update (false)
 * @param bool  $plain      (false)
 *
 * @return int|float|bool
 */
function mrc_get_level(&$user, $planet = [], $unit_id, $for_update = false, $plain = false) {
  $result = null;

  return sn_function_call(__FUNCTION__, [&$user, $planet, $unit_id, $for_update, $plain, &$result]);
}

function sn_mrc_get_level(&$user, $planet = [], $unit_id, $for_update = false, $plain = false, &$result) {
  $mercenary_level = 0;
  $unit_db_name = pname_resource_name($unit_id);

  if (in_array($unit_id, sn_get_groups(array('plans', 'mercenaries', 'tech', 'artifacts')))) {
    $unit = !empty($user['id']) ? DBStaticUnit::db_unit_by_location($user['id'], LOC_USER, $user['id'], $unit_id) : 0;
    $mercenary_level = !empty($unit['unit_level']) ? $unit['unit_level'] : 0;
  } elseif (in_array($unit_id, sn_get_groups(array('structures', 'fleet', 'defense')))) {
    $unit = DBStaticUnit::db_unit_by_location(is_array($user) ? $user['id'] : $planet['id_owner'], LOC_PLANET, $planet['id'], $unit_id);
    $mercenary_level = !empty($unit['unit_level']) ? $unit['unit_level'] : 0;
  } elseif (in_array($unit_id, sn_get_groups('governors'))) {
    $mercenary_level = $unit_id == $planet['PLANET_GOVERNOR_ID'] ? $planet['PLANET_GOVERNOR_LEVEL'] : 0;
  } elseif ($unit_id == RES_DARK_MATTER) {
    $mercenary_level = $user[$unit_db_name] + ($plain || $user['user_as_ally'] ? 0 : SN::$auth->account->account_metamatter);
  } elseif ($unit_id == RES_METAMATTER) {
    $mercenary_level = SN::$auth->account->account_metamatter; //$user[$unit_db_name];
  } elseif (in_array($unit_id, sn_get_groups(array('resources_loot'))) || $unit_id == UNIT_SECTOR) {
    $mercenary_level = !empty($planet[$unit_db_name]) ? $planet[$unit_db_name] : $user[$unit_db_name];
  }

  return $result = $mercenary_level;
}

function mrc_modify_value(&$user, $planet = array(), $mercenaries, $value) { return sn_function_call('mrc_modify_value', array(&$user, $planet, $mercenaries, $value)); }

function sn_mrc_modify_value(&$user, $planet = array(), $mercenaries, $value, $base_value = null) {
  if (!is_array($mercenaries)) {
    $mercenaries = array($mercenaries);
  }

  $base_value = isset($base_value) ? $base_value : $value;

  foreach ($mercenaries as $mercenary_id) {
    $mercenary_level = mrc_get_level($user, $planet, $mercenary_id);

    $mercenary = get_unit_param($mercenary_id);
    $mercenary_bonus = $mercenary[P_BONUS_VALUE];

    switch ($mercenary[P_BONUS_TYPE]) {
      case BONUS_PERCENT:
        $mercenary_level = $mercenary_bonus < 0 && $mercenary_level * $mercenary_bonus < -90 ? -90 / $mercenary_bonus : $mercenary_level;
        $value += $base_value * $mercenary_level * $mercenary_bonus / 100;
      break;

      case BONUS_ADD:
        $value += $mercenary_level * $mercenary_bonus;
      break;

      case BONUS_ABILITY:
        $value = $mercenary_level ? $mercenary_level : 0;
      break;

      default:
      break;
    }
  }

  return $value;
}

function sys_unit_str2arr($fleet_string) {
  $fleet_array = array();
  if (!empty($fleet_string)) {
    $arrTemp = explode(';', $fleet_string);
    foreach ($arrTemp as $temp) {
      if ($temp) {
        $temp = explode(',', $temp);
        if (!empty($temp[0]) && !empty($temp[1])) {
          $fleet_array[$temp[0]] += $temp[1];
        }
      }
    }
  }

  return $fleet_array;
}

function sys_unit_arr2str($unit_list) {
  $fleet_string = array();
  if (isset($unit_list)) {
    if (!is_array($unit_list)) {
      $unit_list = array($unit_list => 1);
    }

    foreach ($unit_list as $unit_id => $unit_count) {
      if ($unit_id && $unit_count) {
        $fleet_string[] = "{$unit_id},{$unit_count}";
      }
    }
  }

  return implode(';', $fleet_string);
}

// TODO Для полноценного функионирования апдейтера пакет функций, включая эту должен быть вынесен раньше - или грузить general.php до апдейтера
function sys_get_unit_location($user, $planet, $unit_id) { return sn_function_call('sys_get_unit_location', array($user, $planet, $unit_id)); }

function sn_sys_get_unit_location($user, $planet, $unit_id) {
  return get_unit_param($unit_id, 'location');
}


function get_engine_data($user, $engine_info, $user_tech_level = null) {
  $sn_data_tech_bonus = get_unit_param($engine_info['tech'], P_BONUS_VALUE);

  $user_tech_level = $user_tech_level === null ? intval(mrc_get_level($user, false, $engine_info['tech'])) : $user_tech_level;

  $engine_info['speed_base'] = $engine_info['speed'];
  $tech_bonus = ($user_tech_level - $engine_info['min_level']) * $sn_data_tech_bonus / 100;
  $tech_bonus = $tech_bonus < -0.9 ? -0.95 : $tech_bonus;
  $engine_info['speed'] = floor(mrc_modify_value($user, false, array(MRC_NAVIGATOR), $engine_info['speed']) * (1 + $tech_bonus));

  $engine_info['consumption_base'] = $engine_info['consumption'];
  $tech_bonus = ($user_tech_level - $engine_info['min_level']) * $sn_data_tech_bonus / 1000;
  $tech_bonus = $tech_bonus > 0.5 ? 0.5 : ($tech_bonus < 0 ? $tech_bonus * 2 : $tech_bonus);
  $engine_info['consumption'] = ceil($engine_info['consumption'] * (1 - $tech_bonus));

  return $engine_info;
}

function get_ship_data($ship_id, $user) {
  $ship_data = array();
  if (in_array($ship_id, sn_get_groups(array('fleet', 'missile')))) {
    foreach (get_unit_param($ship_id, 'engine') as $engine_info) {
      $tech_level = intval(mrc_get_level($user, false, $engine_info['tech']));
      if (empty($ship_data) || $tech_level >= $engine_info['min_level']) {
        $ship_data = $engine_info;
        $ship_data['tech_level'] = $tech_level;
      }
    }
    $ship_data = get_engine_data($user, $ship_data);
    $ship_data['capacity'] = get_unit_param($ship_id, 'capacity');
  }

  return $ship_data;
}

/**
 * Get unit info by unit's SN ID
 *
 * @param int $unitSnId
 *
 * @return mixed
 */
function getUnitInfo($unitSnId) {
  return get_unit_param($unitSnId);
}

function get_unit_param($unit_id, $param_name = null, $user = null, $planet = null) {
  $result = null;

  return sn_function_call('get_unit_param', array($unit_id, $param_name, $user, $planet, &$result));
}

function sn_get_unit_param($unit_id, $param_name = null, $user = null, $planet = null, &$result) {
  global $sn_data;

  $result = isset($sn_data[$unit_id])
    ? ($param_name === null
      ? $sn_data[$unit_id]
      : (isset($sn_data[$unit_id][$param_name]) ? $sn_data[$unit_id][$param_name] : $result)
    )
    : $result;

  return $result;
}

/**
 * @param string|string[] $groups
 *
 * @return array|array[]
 */
function sn_get_groups($groups) {
  $result = null;

  return sn_function_call('sn_get_groups', array($groups, &$result));
}

function sn_sn_get_groups($groups, &$result) {
  $result = is_array($result) ? $result : array();
  foreach ($groups = is_array($groups) ? $groups : array($groups) as $group_name) {
    $result += is_array($a_group = get_unit_param(UNIT_GROUP, $group_name)) ? $a_group : array();
  }

  return $result;
}


function unit_requirements_render($user, $planetrow, $unit_id, $field = P_REQUIRE) {
  $result = null;

  return sn_function_call('unit_requirements_render', array($user, $planetrow, $unit_id, $field, &$result));
}

function sn_unit_requirements_render($user, $planetrow, $unit_id, $field = P_REQUIRE, &$result) {
  global $lang, $config;

  $sn_data_unit = get_unit_param($unit_id);

  $result = is_array($result) ? $result : array();
  if ($sn_data_unit[$field] && !($sn_data_unit[P_UNIT_TYPE] == UNIT_MERCENARIES && SN::$config->empire_mercenary_temporary)) {
    foreach ($sn_data_unit[$field] as $require_id => $require_level) {
      $level_got = mrc_get_level($user, $planetrow, $require_id);
      $level_basic = mrc_get_level($user, $planetrow, $require_id, false, true);
      $result[] = array(
        'NAME'             => $lang['tech'][$require_id],
        //'CLASS' => $require_level > $level_got ? 'negative' : ($require_level == $level_got ? 'zero' : 'positive'),
        'REQUEREMENTS_MET' => intval($require_level <= $level_got ? REQUIRE_MET : REQUIRE_MET_NOT),
        'LEVEL_REQUIRE'    => $require_level,
        'LEVEL'            => $level_got,
        'LEVEL_BASIC'      => $level_basic,
        'LEVEL_BONUS'      => max(0, $level_got - $level_basic),
        'ID'               => $require_id,
      );
    }
  }

  return $result;
}

/**
 * @param array $cost        - [(int)resourceId => (float)unitAmount] => [RES_CRYSTAL => 100]
 * @param int   $in_resource - RES_METAL...
 *
 * @return float|int
 */
function get_unit_cost_in($cost, $in_resource = RES_METAL) {
  static $rates;

  if (!$rates) {
    $rates = SN::$gc->economicHelper->getResourcesExchange();
  }

  unset($cost[P_FACTOR]);

  $mainResourceExchange = !empty($rates[$in_resource]) ? $rates[$in_resource] : 1;
  $metal_cost = 0;
  foreach ($cost as $resource_id => $resource_value) {
    if (empty($rates[$resource_id])) {
      continue;
    }

    $metal_cost += $rates[$resource_id] / $mainResourceExchange * $resource_value;
  }

  return $metal_cost;
}

/**
 * Calculates cost of STACKABLE unit in specified resource
 *
 * @param int|float[] $units - unit ID or array [unitId => unitAmount]
 * @param int         $costResourceId
 *
 * @return float|int
 */
function getStackableUnitsCost($units, $costResourceId = RES_METAL) {
  static $costCache;

  $result = 0;

  if (!is_array($units)) {
    $units = [$units => 1];
  }

  foreach ($units as $unitId => $unitAmount) {
    if (!isset($costCache[$unitId][$costResourceId])) {
      $unitInfo = get_unit_param($unitId);

      $costCache[$unitId][$costResourceId] = !empty($unitInfo[P_COST]) ? get_unit_cost_in($unitInfo[P_COST], $costResourceId) : 0;
    }

    $result += $costCache[$unitId][$costResourceId] * $unitAmount;
  }

  return $result;
}
