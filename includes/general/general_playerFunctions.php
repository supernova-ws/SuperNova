<?php

use Planet\DBStaticPlanet;

/**
 * Created by Gorlum 04.12.2017 4:34
 */

// ----------------------------------------------------------------------------------------------------------------
function sys_user_options_pack(&$user) {
  global $user_option_list;

  $options = '';
  $option_list = array();
  foreach ($user_option_list as $option_group_id => $option_group) {
    $option_list[$option_group_id] = array();
    foreach ($option_group as $option_name => $option_value) {
      if (!isset($user[$option_name])) {
        $user[$option_name] = $option_value;
      } elseif ($user[$option_name] == '') {
        $user[$option_name] = 0;
      }
      $options .= "{$option_name}^{$user[$option_name]}|";
      $option_list[$option_group_id][$option_name] = $user[$option_name];
    }
  }

  $user['options'] = $options;
  $user['option_list'] = $option_list;

  return $options;
}

function sys_user_options_unpack(&$user) {
  global $user_option_list;

  $option_list = array();
  $option_string_list = explode('|', $user['options']);

  foreach ($option_string_list as $option_string) {
    list($option_name, $option_value) = explode('^', $option_string);
    $option_list[$option_name] = $option_value;
  }

  $final_list = array();
  foreach ($user_option_list as $option_group_id => $option_group) {
    $final_list[$option_group_id] = array();
    foreach ($option_group as $option_name => $option_value) {
      if (!isset($option_list[$option_name])) {
        $option_list[$option_name] = $option_value;
      }
      $user[$option_name] = $final_list[$option_group_id][$option_name] = $option_list[$option_name];
    }
  }

  $user['option_list'] = $final_list;

  return $final_list;
}


// ----------------------------------------------------------------------------------------------------------------
function get_player_max_expeditons(&$user, $astrotech = -1) { $result = null; return sn_function_call('get_player_max_expeditons', array(&$user, $astrotech, &$result)); }

function sn_get_player_max_expeditons(&$user, $astrotech = -1, &$result = 0) {
  if ($astrotech == -1) {
    if (!isset($user[UNIT_PLAYER_EXPEDITIONS_MAX])) {
      $astrotech = mrc_get_level($user, false, TECH_ASTROTECH);
      $user[UNIT_PLAYER_EXPEDITIONS_MAX] = $astrotech >= 1 ? floor(sqrt($astrotech - 1)) : 0;
    }

    return $result += $user[UNIT_PLAYER_EXPEDITIONS_MAX];
  } else {
    return $result += $astrotech >= 1 ? floor(sqrt($astrotech - 1)) : 0;
  }
}

function get_player_max_expedition_duration(&$user, $astrotech = -1) {
  return $astrotech == -1 ? mrc_get_level($user, false, TECH_ASTROTECH) : $astrotech;
}

function get_player_max_colonies(&$user, $astrotech = -1) {
  if ($astrotech == -1) {
    if (!isset($user[UNIT_PLAYER_COLONIES_MAX])) {

      $expeditions = get_player_max_expeditons($user);
      $astrotech = mrc_get_level($user, false, TECH_ASTROTECH);
      $colonies = $astrotech - $expeditions;

      $user[UNIT_PLAYER_COLONIES_MAX] = SN::$config->player_max_colonies < 0 ? $colonies : min(SN::$config->player_max_colonies, $colonies);
    }

    return $user[UNIT_PLAYER_COLONIES_MAX];
  } else {
    $expeditions = get_player_max_expeditons($user, $astrotech);
    $colonies = $astrotech - $expeditions;

    return SN::$config->player_max_colonies < 0 ? $colonies : min(SN::$config->player_max_colonies, $colonies);
  }
}

function get_player_current_colonies(&$user) {
  return $user[UNIT_PLAYER_COLONIES_CURRENT] = isset($user[UNIT_PLAYER_COLONIES_CURRENT]) ? $user[UNIT_PLAYER_COLONIES_CURRENT] : max(0, DBStaticPlanet::db_planet_count_by_type($user['id']) - 1);
}

function GetSpyLevel(&$user) {
  return mrc_modify_value($user, false, array(MRC_SPY, TECH_SPY), 0);
}

function GetMaxFleets(&$user) {
  return mrc_modify_value($user, false, array(MRC_COORDINATOR, TECH_COMPUTER), 1);
}


// ----------------------------------------------------------------------------------------------------------------
/**
 * @param int|string $user_id
 * @param int|string $capitalPlanetId
 *
 * @return mixed
 */
function sys_player_new_adjust($user_id, $capitalPlanetId) { $result = null; return sn_function_call('sys_player_new_adjust', array($user_id, $capitalPlanetId, &$result)); }

function sn_sys_player_new_adjust($user_id, $planet_id, &$result) {
  return $result;
}


// ----------------------------------------------------------------------------------------------------------------
function flt_get_missile_range($user) {
  return max(0, mrc_get_level($user, false, TECH_ENGINE_ION) * 5 - 1);
}

