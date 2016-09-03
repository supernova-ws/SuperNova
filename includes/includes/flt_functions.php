<?php

use DBStatic\DBStaticFleetACS;
use DBStatic\DBStaticFleetBashing;
use DBStatic\DBStaticPlanet;
use DBStatic\DBStaticUnit;
use DBStatic\DBStaticUser;
use Vector\Vector;

function flt_fleet_speed($user, $fleet) {
  if(!is_array($fleet)) {
    $fleet = array($fleet => 1);
  }

  $speeds = array();
  if(!empty($fleet)) {
    foreach($fleet as $ship_id => $amount) {
      if($amount && in_array($ship_id, classSupernova::$gc->groupFleetAndMissiles)) {
        $single_ship_data = get_ship_data($ship_id, $user);
        $speeds[] = $single_ship_data['speed'];
      }
    }
  }

  return empty($speeds) ? 0 : min($speeds);
}

function flt_travel_distance($from, $to) {
  return Vector::distanceBetweenCoordinates($from, $to);
}

/**
 * @param     $user_row
 * @param     $from
 * @param     $to
 * @param     $fleet_array
 * @param int $speed_percent
 *
 * @return array
 */
function flt_travel_data($user_row, $from, $to, $fleet_array, $speed_percent = 10) {
  $distance = flt_travel_distance($from, $to);

  $consumption = 0;
  $capacity = 0;
  $duration = 0;

  $game_fleet_speed = flt_server_flight_speed_multiplier();
  $fleet_speed = flt_fleet_speed($user_row, $fleet_array);
  if(!empty($fleet_array) && $fleet_speed && $game_fleet_speed) {
    $speed_percent = $speed_percent ? max(min($speed_percent, 10), 1) : 10;
    $real_speed = $speed_percent * sqrt($fleet_speed);

    $duration = max(1, round((35000 / $speed_percent * sqrt($distance * 10 / $fleet_speed) + 10) / $game_fleet_speed));

    foreach($fleet_array as $ship_id => $ship_count) {
      if(!$ship_id || !$ship_count) {
        continue;
      }

      $single_ship_data = get_ship_data($ship_id, $user_row);
      $single_ship_data['speed'] = $single_ship_data['speed'] < 1 ? 1 : $single_ship_data['speed'];

      $consumption += $single_ship_data['consumption'] * $ship_count * pow($real_speed / sqrt($single_ship_data['speed']) / 10 + 1, 2);
      $capacity += $single_ship_data['capacity'] * $ship_count;
    }

    $consumption = round($distance * $consumption / 35000) + 1;
  }

  return array(
    'fleet_speed'            => $fleet_speed,
    'distance'               => $distance,
    'duration'               => $duration,
    'consumption'            => $consumption,
    'capacity'               => $capacity,
    'hold'                   => $capacity - $consumption,
    'transport_effectivness' => $consumption ? $capacity / $consumption : 0,
  );
}

function flt_bashing_check($user, $enemy, $planet_dst, $mission, $flight_duration, $fleet_group = 0) {
  $config_bashing_attacks = classSupernova::$config->fleet_bashing_attacks;
  $config_bashing_interval = classSupernova::$config->fleet_bashing_interval;
  if(!$config_bashing_attacks) {
    // Bashing allowed - protection disabled
    return FLIGHT_ALLOWED;
  }

  $bashing_result = FLIGHT_MISSION_ATTACK_BASHING;
  if($user['ally_id'] && $enemy['ally_id']) {
    $relations = ali_relations($user['ally_id'], $enemy['ally_id']);
    if(!empty($relations)) {
      $relations = $relations[$enemy['ally_id']];
      switch($relations['alliance_diplomacy_relation']) {
        case ALLY_DIPLOMACY_WAR:
          if(SN_TIME_NOW - $relations['alliance_diplomacy_time'] <= classSupernova::$config->fleet_bashing_war_delay) {
            $bashing_result = FLIGHT_MISSION_ATTACK_BASHING_WAR_DELAY;
          } else {
            return FLIGHT_ALLOWED;
          }
        break;
        // Here goes other relations

        /*
                default:
                  return FLIGHT_ALLOWED;
                break;
        */
      }
    }
  }

  $time_limit = SN_TIME_NOW + $flight_duration - classSupernova::$config->fleet_bashing_scope;
  $bashing_list = array(SN_TIME_NOW);

  // Retrieving flying fleets
  $objFleetsBashing = FleetList::dbGetFleetListBashing($user['id'], $planet_dst);
  foreach($objFleetsBashing->_container as $fleetBashing) {
    // Checking for ACS - each ACS count only once
    if($fleetBashing->group_id) {
      $bashing_list["{$user['id']}_{$fleetBashing->group_id}"] = $fleetBashing->time_arrive_to_target;
    } else {
      $bashing_list[] = $fleetBashing->time_arrive_to_target;
    }
  }

  // Check for joining to ACS - if there are already fleets in ACS no checks should be done
  if($mission == MT_ACS && $bashing_list["{$user['id']}_{$fleet_group}"]) {
    return FLIGHT_ALLOWED;
  }

  $query = DBStaticFleetBashing::db_bashing_list_get($user, $planet_dst, $time_limit);
  while($bashing_row = db_fetch($query)) {
    $bashing_list[] = $bashing_row['bashing_time'];
  }

  sort($bashing_list);

  $last_attack = 0;
  $wave = 0;
  $attack = 1;
  foreach($bashing_list as &$bash_time) {
    $attack++;
    if($bash_time - $last_attack > $config_bashing_interval || $attack > $config_bashing_attacks) {
      $attack = 1;
      $wave++;
    }

    $last_attack = $bash_time;
  }

  return ($wave > classSupernova::$config->fleet_bashing_waves ? $bashing_result : FLIGHT_ALLOWED);
}

function flt_can_attack($planet_src, $planet_dst, $fleet = array(), $mission, $options = false) { return sn_function_call(__FUNCTION__, array($planet_src, $planet_dst, $fleet, $mission, $options, &$result)); }

function sn_flt_can_attack($planet_src, $planet_dst, $fleet = array(), $mission, $options = false, &$result) {
  //TODO: try..catch
  global $user;

  if($user['vacation']) {
    return $result = FLIGHT_PLAYER_VACATION_OWN;
  }

  if(empty($fleet) || !is_array($fleet)) {
    return $result = FLIGHT_SHIPS_NO_SHIPS;
  }

  $sn_groups_mission = sn_get_groups('missions');
  if(!isset($sn_groups_mission[$mission])) {
    return $result = FLIGHT_MISSION_UNKNOWN;
  }
  $sn_data_mission = $sn_groups_mission[$mission];

//TODO: Проверка на отстуствие ресурсов в нетранспортных миссиях (Транспорт, Передислокация, Колонизация)

  //TODO: Проверка на наличие ресурсов при Транспорте
  // TODO: Проверка на отрицательные ресурсы при транспорте
  // TODO: Проверка на перегрузку при транспорте

  // TODO: В ракетных миссиях могут лететь только ракеты
  // TODO: В неракетных миссиях ракеты должны отсутствовать
  $ships = 0;
  $recyclers = 0;
  $spies = 0;
  $resources = 0;
  $ship_ids = classSupernova::$gc->groupFleet;
  $resource_ids = sn_get_groups('resources_loot');
  foreach($fleet as $ship_id => $ship_count) {
    $is_ship = in_array($ship_id, $ship_ids);
    $is_resource = in_array($ship_id, $resource_ids);
    if(!$is_ship && !$is_resource) {
      // TODO Спецобработчик для Капитана и модулей
//      return FLIGHT_SHIPS_UNIT_WRONG;
    }

    if($ship_count < 0) {
      return $result = $is_ship ? FLIGHT_SHIPS_NEGATIVE : FLIGHT_RESOURCES_NEGATIVE;
    }

    if($ship_count > mrc_get_level($user, $planet_src, $ship_id)) {
      // TODO FLIGHT_MISSION_MISSILE_NO_MISSILES
      return $result = $is_ship ? FLIGHT_SHIPS_NOT_ENOUGH_OR_RESOURCES : FLIGHT_RESOURCES_NOT_ENOUGH;
    }

    if($is_ship) {
      $single_ship_data = get_ship_data($ship_id, $user);
      if($single_ship_data[P_SPEED] <= 0) {
        return $result = FLIGHT_SHIPS_UNMOVABLE;
      }
      $ships += $ship_count;
      $recyclers += in_array($ship_id, classSupernova::$gc->groupRecyclers) ? $ship_count : 0;
      $spies += $ship_id == SHIP_SPY ? $ship_count : 0;
    } elseif($is_resource) {
      $resources += $ship_count;
    }
  }
  /*
    if($ships <= 0)
    {
      return FLIGHT_SHIPS_NO_SHIPS;
    }
  */

  if(isset($options['resources']) && $options['resources'] > 0 && !(isset($sn_data_mission['transport']) && $sn_data_mission['transport'])) {
    return $result = FLIGHT_RESOURCES_FORBIDDEN;
  }

  /*
    elseif($mission == MT_TRANSPORT)
    {
      return FLIGHT_RESOURCES_EMPTY;
    }
  */

  $speed = $options['fleet_speed_percent'];
  if($speed && ($speed != intval($speed) || $speed < 1 || $speed > 10)) {
    return $result = FLIGHT_FLEET_SPEED_WRONG;
  }

  $travel_data = flt_travel_data($user, $planet_src, $planet_dst, $fleet, $options['fleet_speed_percent']);


  if(mrc_get_level($user, $planet_src, RES_DEUTERIUM) < $fleet[RES_DEUTERIUM] + $travel_data['consumption']) {
    return $result = FLIGHT_RESOURCES_FUEL_NOT_ENOUGH;
  }

  if($travel_data['consumption'] > $travel_data['capacity']) {
    return $result = FLIGHT_FLEET_TOO_FAR;
  }

  if($travel_data['hold'] < $resources) {
    return $result = FLIGHT_FLEET_OVERLOAD;
  }

  $fleet_start_time = SN_TIME_NOW + $travel_data['duration'];

  $fleet_group = $options['fleet_group'];
  if($fleet_group) {
    if($mission != MT_ACS) {
      return $result = FLIGHT_MISSION_IMPOSSIBLE;
    };

    $acs = DBStaticFleetACS::db_acs_get_by_group_id($fleet_group);
    if(!$acs['id']) {
      return $result = FLIGHT_MISSION_ACS_NOT_EXISTS;
    }

    if($planet_dst['galaxy'] != $acs['galaxy'] || $planet_dst['system'] != $acs['system'] || $planet_dst['planet'] != $acs['planet'] || $planet_dst['planet_type'] != $acs['planet_type']) {
      return $result = FLIGHT_MISSION_ACS_WRONG_TARGET;
    }

    if($fleet_start_time > $acs['ankunft']) {
      return $result = FLIGHT_MISSION_ACS_TOO_LATE;
    }
  }

  $flying_fleets = $options['flying_fleets'];
  if(!$flying_fleets) {
    $flying_fleets = FleetList::fleet_count_flying($user['id']);
  }
  if(GetMaxFleets($user) <= $flying_fleets && $mission != MT_MISSILE) {
    return $result = FLIGHT_FLEET_NO_SLOTS;
  }

  // В одиночку шпионские зонды могут летать только в миссии Шпионаж, Передислокация и Транспорт
  if($ships && $spies && $spies == $ships && !($mission == MT_SPY || $mission == MT_RELOCATE || $mission == MT_TRANSPORT)) {
    return $result = FLIGHT_SHIPS_NOT_ONLY_SPIES;
  }

  // Checking for no planet
  if(!$planet_dst['id_owner']) {
    if($mission == MT_COLONIZE && !$fleet[SHIP_COLONIZER]) {
      return $result = FLIGHT_SHIPS_NO_COLONIZER;
    }

    if($mission == MT_EXPLORE || $mission == MT_COLONIZE) {
      return $result = FLIGHT_ALLOWED;
    }

    return $result = FLIGHT_VECTOR_NO_TARGET;
  }

  if($mission == MT_RECYCLE) {
    if($planet_dst['debris_metal'] + $planet_dst['debris_crystal'] <= 0) {
      return $result = FLIGHT_MISSION_RECYCLE_NO_DEBRIS;
    }
    if($recyclers <= 0) {
      return $result = FLIGHT_SHIPS_NO_RECYCLERS;
    }

    return $result = FLIGHT_ALLOWED;
  }

  // Got planet. Checking if it is ours
  if($planet_dst['id_owner'] == $user['id']) {
    if($mission == MT_TRANSPORT || $mission == MT_RELOCATE) {
      return $result = FLIGHT_ALLOWED;
    }

    return $planet_src['id'] == $planet_dst['id'] ? FLIGHT_VECTOR_SAME_SOURCE : FLIGHT_PLAYER_OWN;
  }

  // No, planet not ours. Cutting mission that can't be send to not-ours planet
  if($mission == MT_RELOCATE || $mission == MT_COLONIZE || $mission == MT_EXPLORE) {
    return $result = FLIGHT_MISSION_IMPOSSIBLE;
  }

  $enemy = DBStaticUser::db_user_by_id($planet_dst['id_owner']);
  // We cannot attack or send resource to users in VACATION mode
  if($enemy['vacation'] && $mission != MT_RECYCLE) {
    return $result = FLIGHT_PLAYER_VACATION;
  }

  // Multi IP protection
  // TODO: Here we need a procedure to check proxies
  if(sys_is_multiaccount($user, $enemy)) {
    return $result = FLIGHT_PLAYER_SAME_IP;
  }

  $user_points = $user['total_points'];
  $enemy_points = $enemy['total_points'];

  // Is it transport? If yes - checking for buffing to prevent mega-alliance destroyer
  if($mission == MT_TRANSPORT) {
    if($user_points >= $enemy_points || classSupernova::$config->allow_buffing) {
      return $result = FLIGHT_ALLOWED;
    } else {
      return $result = FLIGHT_PLAYER_BUFFING;
    }
  }

  // Only aggresive missions passed to this point. HOLD counts as passive but aggresive

  // Is it admin with planet protection?
  if($planet_dst['id_level'] > $user['authlevel']) {
    return $result = FLIGHT_PLAYER_ADMIN;
  }

  // Okay. Now skipping protection checks for inactive longer then 1 week
  if(!$enemy['onlinetime'] || $enemy['onlinetime'] >= (SN_TIME_NOW - 60 * 60 * 24 * 7)) {
    if(
      ($enemy_points <= classSupernova::$config->game_noob_points && $user_points > classSupernova::$config->game_noob_points)
      ||
      (classSupernova::$config->game_noob_factor && $user_points > $enemy_points * classSupernova::$config->game_noob_factor)
    ) {
      if($mission != MT_HOLD) {
        return $result = FLIGHT_PLAYER_NOOB;
      }
      if($mission == MT_HOLD && !($user['ally_id'] && $user['ally_id'] == $enemy['ally_id'] && classSupernova::$config->ally_help_weak)) {
        return $result = FLIGHT_PLAYER_NOOB;
      }
    }
  }

  // Is it HOLD mission? If yes - there should be ally deposit
  if($mission == MT_HOLD) {
    if(mrc_get_level($user, $planet_dst, STRUC_ALLY_DEPOSIT)) {
      return $result = FLIGHT_ALLOWED;
    }

    return $result = FLIGHT_MISSION_HOLD_NO_ALLY_DEPOSIT;
  }

  if($mission == MT_SPY) {
    return $result = $spies >= 1 ? FLIGHT_ALLOWED : FLIGHT_MISSION_SPY_NO_SPIES;
  }

  // Is it MISSILE mission?
  if($mission == MT_MISSILE) {
    $sn_data_mip = get_unit_param(UNIT_DEF_MISSILE_INTERPLANET);
    if(mrc_get_level($user, $planet_src, STRUC_SILO) < $sn_data_mip[P_REQUIRE][STRUC_SILO]) {
      return $result = FLIGHT_MISSION_MISSILE_NO_SILO;
    }

    if(!$fleet[UNIT_DEF_MISSILE_INTERPLANET]) {
      return $result = FLIGHT_MISSION_MISSILE_NO_MISSILES;
    }

    $distance = abs($planet_dst['system'] - $planet_src['system']);
    $mip_range = flt_get_missile_range($user);
    if($distance > $mip_range || $planet_dst['galaxy'] != $planet_src['galaxy']) {
      return $result = FLIGHT_MISSION_MISSILE_TOO_FAR;
    }

    if(isset($options['target_structure']) && $options['target_structure'] && !in_array($options['target_structure'], sn_get_groups('defense_active'))) {
      return $result = FLIGHT_MISSION_MISSILE_WRONG_STRUCTURE;
    }
  }

  if($mission == MT_DESTROY && $planet_dst['planet_type'] != PT_MOON) {
    return $result = FLIGHT_MISSION_IMPOSSIBLE;
  }

  if($mission == MT_ATTACK || $mission == MT_ACS || $mission == MT_DESTROY) {
    return $result = flt_bashing_check($user, $enemy, $planet_dst, $mission, $travel_data['duration'], $fleet_group);
  }

  return $result = FLIGHT_ALLOWED;
}

/*
$user - actual user record
$from - actual planet record
$to - actual planet record
$fleet - array of records $unit_id -> $amount
$mission - fleet mission
*/

function flt_t_send_fleet($user, &$from, $to, $fleet_REAL_array, $mission, $options = array()) {
//ini_set('error_reporting', E_ALL);

  $internal_transaction = !sn_db_transaction_check(false) ? sn_db_transaction_start() : false;
//pdump($internal_transaction);

  // TODO Потенциальный дедлок - если успела залочится запись пользователя - хозяина планеты
  $user = DBStaticUser::db_user_by_id($user['id'], true);
  $from = sys_o_get_updated($user, $from['id'], SN_TIME_NOW);
  $from = $from['planet'];

  $can_attack = flt_can_attack($from, $to, $fleet_REAL_array, $mission, $options);
  if($can_attack != FLIGHT_ALLOWED) {
    $internal_transaction ? sn_db_transaction_rollback() : false;

    return $can_attack;
  }

  $to['id_owner'] = idval($to['id_owner']);

  $travel_data = flt_travel_data($user, $from, $to, $fleet_REAL_array, $options['fleet_speed_percent']);

  $time_on_mission = 0;
  if($mission == MT_EXPLORE || $mission == MT_HOLD) {
    // TODO - include some checks about maximum and minumum stay_duration
    $time_on_mission = $options['stay_time'] * 3600;
  }

  $options['fleet_group'] = !empty($options['fleet_group']) ? idval($options['fleet_group']) : 0;
  $objFleet = new Fleet();
  $objFleet->set_times($travel_data['duration'], $time_on_mission);
  // TODO ???????? Прямое обращение к unitList????????????
  // - Диферсифицировать по типу? Если массив - значит unitsSetFromArray?
  // - Иначе - экспешн?
  $objFleet->unitsSetFromArray($fleet_REAL_array);
  $objFleet->mission_type = $mission;
  $objFleet->set_start_planet($from);
  $objFleet->set_end_planet($to);
  $objFleet->playerOwnerId = $user['id'];
  $objFleet->group_id = $options['fleet_group'];
  $objFleet->dbInsert();

  $sn_group_fleet = classSupernova::$gc->groupFleet;
  $sn_group_resources_loot = sn_get_groups('resources_loot');
  $planetRowFieldChanges = array();
  foreach($fleet_REAL_array as $unit_id => $amount) {
    if(!$amount || !$unit_id) {
      continue;
    }

    if(in_array($unit_id, $sn_group_fleet)) {
      DBStaticUnit::dbUpdateOrInsertUnit($unit_id, -$amount, $user, $from['id']);
    } elseif(in_array($unit_id, $sn_group_resources_loot)) {
      $planetRowFieldChanges[$unit_id] -= $amount;
    }
  }

  $planetRowFieldChanges[RES_DEUTERIUM] -= $travel_data['consumption'];
  DBStaticPlanet::db_planet_update_resources($planetRowFieldChanges, $from['id']);


  // $internal_transaction = false;sn_db_transaction_rollback(); // TODO - REMOVE !!!!!!!!!!!!!!!!!!

  $internal_transaction ? sn_db_transaction_commit() : false;
  $from = DBStaticPlanet::db_planet_by_id($from['id']);

  return FLIGHT_ALLOWED;
//ini_set('error_reporting', E_ALL ^ E_NOTICE);
}

function flt_calculate_ship_to_transport_sort($a, $b) {
  return $a['transport_effectivness'] == $b['transport_effectivness'] ? 0 : ($a['transport_effectivness'] > $b['transport_effectivness'] ? -1 : 1);
}

// flt_calculate_ship_to_transport - calculates how many ships need to transport pointed amount of resources
// $ship_list - list of available ships
// $resource_amount - how much amount of resources need to be transported
// $from - transport from
// $to - transport to
function flt_calculate_fleet_to_transport($ship_list, $resource_amount, $from, $to) {
  global $user;

  $ship_data = array();
  $fleet_array = array();
  foreach($ship_list as $transport_id => $cork) {
    $ship_data[$transport_id] = flt_travel_data($user, $from, $to, array($transport_id => 1), 10);
  }
  uasort($ship_data, 'flt_calculate_ship_to_transport_sort');

  $fleet_capacity = 0;
  $fuel_total = $fuel_left = mrc_get_level($user, $from, RES_DEUTERIUM);
  foreach($ship_data as $transport_id => &$ship_info) {
    $ship_loaded = min($ship_list[$transport_id], ceil($resource_amount / $ship_info['hold']), floor($fuel_left / $ship_info['consumption']));
    if($ship_loaded) {
      $fleet_array[$transport_id] = $ship_loaded;
      $resource_amount -= min($resource_amount, $ship_info['hold'] * $ship_loaded);
      $fuel_left -= $ship_info['consumption'] * $ship_loaded;

      $fleet_capacity += $ship_info['capacity'] * $ship_loaded;
    }
  }

  return array('fleet' => $fleet_array, 'ship_data' => $ship_data, 'capacity' => $fleet_capacity, 'consumption' => $fuel_total - $fuel_left);
}
