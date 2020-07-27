<?php

use Meta\Economic\BuildDataStatic;
use Unit\DBStaticUnit;

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
    $query                       = DBStaticUnit::db_unit_list_laboratories($user['id']);
    while ($row = db_fetch($query)) {
      if (!eco_unit_busy($user, $row, UNIT_TECHNOLOGIES)) {
        $row                                     += array(
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
      $lab_level = doquery("SELECT ally_members AS effective_level FROM {{alliance}} WHERE id = {$user['user_as_ally']} LIMIT 1", true);
    } else {
      $tech_intergalactic           = mrc_get_level($user, false, TECH_RESEARCH) + 1;
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

/**
 * @param array      $user
 * @param array      $planet
 * @param int        $unit_id
 * @param int|string $unit_level
 * @param bool       $only_cost
 * @param array|null $info
 *
 * @return mixed
 */
function eco_get_build_data(&$user, $planet, $unit_id, $unit_level = 0, $only_cost = false, $info = null) {
  $unit_data = get_unit_param($unit_id);

  // Filling basic build data - unit cost
  $cost = BuildDataStatic::getBasicData($user, $planet, $unit_data, $unit_level);

  // Getting autoconvert unit amount
  $cost[BUILD_AUTOCONVERT] = !$cost[P_OPTIONS][P_ONLY_DARK_MATTER] ? BuildDataStatic::getAutoconvertCount($user, $planet, $cost) : 0;
  // Limiting autoconvert amount to unit max stack - if is set
  !empty($unit_data[P_MAX_STACK]) ? $cost[BUILD_AUTOCONVERT] = min($unit_data[P_MAX_STACK], $cost[BUILD_AUTOCONVERT]) : false;

  $cost[P_OPTIONS][P_TIME_RAW] = $cost[P_OPTIONS][P_TIME_RAW] * 60 * 60 / get_game_speed() / 2500;

  // TODO - Вынести в отдельную процедуру расчёт стоимости
  if ($only_cost) {
    return $cost;
  }

  // Check if unit can be built
  $cost['RESULT'][BUILD_CREATE] = BuildDataStatic::getBuildStatus($user, $planet, $unit_id, $cost['CAN'][BUILD_CREATE]);
  // Setting destroy status
  $cost['RESULT'][BUILD_DESTROY] = BuildDataStatic::getDestroyStatus($user, $planet, $unit_id, $cost);


  // Time calculations
  if (in_array($unit_id, sn_get_groups('governors')) || $cost[P_OPTIONS][P_ONLY_DARK_MATTER]) {
    // Zero build time for Governors and other units with DM in price
    $cost[RES_TIME][BUILD_CREATE] = $cost[RES_TIME][BUILD_DESTROY] = 0;
  } else {
    $cost[RES_TIME][BUILD_CREATE] = $cost[P_OPTIONS][P_TIME_RAW];

    // Applying time modifiers
    $cost[RES_TIME][BUILD_CREATE] /= BuildDataStatic::getCapitalTimeDivisor($user, $planet, $unit_id);
    $cost[RES_TIME][BUILD_CREATE] /= BuildDataStatic::getMercenaryTimeDivisor($user, $planet, $unit_id);
    $cost[RES_TIME][BUILD_CREATE] /= SN::$gc->pimp->getStructuresTimeDivisor($user, $planet, $unit_id, $unit_data);

    // Final calculations
    $cost[RES_TIME][BUILD_CREATE]  = $cost[RES_TIME][BUILD_CREATE] > 1 ? ceil($cost[RES_TIME][BUILD_CREATE]) : 1;
    $cost[RES_TIME][BUILD_DESTROY] = $cost[RES_TIME][BUILD_CREATE] / 2 > 1 ? ceil($cost[RES_TIME][BUILD_CREATE] / 2) : 1;
  }

  return $cost;
}


function eco_can_build_unit($user, $planet, $unit_id) {
  $result = null;

  return sn_function_call('eco_can_build_unit', [$user, $planet, $unit_id, &$result]);
}

function sn_eco_can_build_unit($user, $planet, $unit_id, &$result) {
  $result = isset($result) ? $result : BUILD_ALLOWED;
  $result = $result == BUILD_ALLOWED && eco_unit_busy($user, $planet, $unit_id) ? BUILD_UNIT_BUSY : $result;

  $unit_param = get_unit_param($unit_id);
  if ($unit_param[P_UNIT_TYPE] != UNIT_MERCENARIES || !SN::$config->empire_mercenary_temporary) {
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

  $unit_list  = is_array($unit_list) ? $unit_list : array($unit_list => $unit_list);
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

function eco_unit_busy(&$user, &$planet, $unit_id) {
  $result = null;

  return sn_function_call('eco_unit_busy', [&$user, &$planet, $unit_id, &$result]);
}

function sn_eco_unit_busy(&$user, &$planet, $unit_id, &$result) {
  global $config;

  $result = isset($result) ? $result : false;
  if (!$result) {
    if (($unit_id == STRUC_LABORATORY || $unit_id == STRUC_LABORATORY_NANO) && !$config->BuildLabWhileRun) {
      $global_que = que_get($user['id'], $planet['id'], QUE_RESEARCH, false);
      if (is_array($global_que['ques'][QUE_RESEARCH][$user['id']])) {
        $first_element = reset($global_que['ques'][QUE_RESEARCH][$user['id']]);
        if (is_array($first_element)) {
          $result = true;
        }
      }
    } elseif (($unit_id == UNIT_TECHNOLOGIES || in_array($unit_id, sn_get_groups('tech'))) && !$config->BuildLabWhileRun) {
      $userId        = floatval($user['id']);
      $isLabBuilding = doquery(
        "SELECT 1 FROM `{{que}}`
        WHERE
            `que_player_id` = {$userId}
            AND `que_unit_id` IN (" . STRUC_LABORATORY . ", " . STRUC_LABORATORY_NANO . ")", true);

      $result = !empty($isLabBuilding);
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
