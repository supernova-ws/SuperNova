<?php

use DBAL\OldDbChangeSet;
use Fleet\DbFleetStatic;
use Fleet\Fleet;
use Planet\DBStaticPlanet;

function flt_fleet_speed($user, $fleet, $shipData = []) {
  if (!is_array($fleet)) {
    $fleet = array($fleet => 1);
  }

  $speeds = array();
  if (!empty($fleet)) {
    foreach ($fleet as $ship_id => $amount) {
      if ($amount && in_array($ship_id, sn_get_groups(array('fleet', 'missile')))) {
        $single_ship_data = !empty($shipData[$ship_id]) ? $shipData[$ship_id] : get_ship_data($ship_id, $user);
        $speeds[] = $single_ship_data['speed'];
      }
    }
  }

  return empty($speeds) ? 0 : min($speeds);
}

function flt_get_galaxy_distance() {
  return SN::$config->uni_galaxy_distance ? SN::$config->uni_galaxy_distance : UNIVERSE_GALAXY_DISTANCE;
}

function flt_travel_distance($from, $to) {
  if ($from['galaxy'] != $to['galaxy']) {
    $distance = abs($from['galaxy'] - $to['galaxy']) * flt_get_galaxy_distance();
  } elseif ($from['system'] != $to['system']) {
    $distance = abs($from['system'] - $to['system']) * 5 * 19 + 2700;
  } elseif ($from['planet'] != $to['planet']) {
    $distance = abs($from['planet'] - $to['planet']) * 5 + 1000;
  } else {
    $distance = 5;
  }

  return $distance;
}


function fltDistanceAsGalaxy($distance) {
  return $distance ? $distance / flt_get_galaxy_distance() : 0;
}

function fltDistanceAsSystem($distance) {
  return $distance ? ($distance - 2700) / 5 / 19 : 0;
}

/**
 * @param int   $ship_id
 * @param int   $speed_percent
 * @param array $shipsData
 *
 * @return float|int
 */
function flt_get_max_distance($ship_id, $speed_percent = 100, $shipsData = []) {
  $single_ship_data = $shipsData[$ship_id];

  if (!$single_ship_data['capacity'] || !$single_ship_data['consumption']) {
    return 0;
  }

  return $distance = calcDistance($speed_percent, $single_ship_data);
}

/**
 * @param $speed_percent
 * @param $single_ship_data
 *
 * @return float
 */
function calcDistance($speed_percent, $single_ship_data) {
  return floor(($single_ship_data['capacity'] - 1) / $single_ship_data['consumption'] / pow($speed_percent / 100 + 1, 2) * 35000);
}


/**
 * @param         $user_row
 * @param         $from
 * @param         $to
 * @param         $fleet_array
 * @param int     $speed_percent
 * @param array[] $shipsData - prepared ships data to use in calculations
 *
 * @return array
 */
function flt_travel_data($user_row, $from, $to, $fleet_array, $speed_percent = 10, $shipsData = [], $distance = null) {
  $distance = $distance === null ? flt_travel_distance($from, $to) : $distance;

  $consumption = 0;
  $capacity = 0;
  $duration = 0;

  $game_fleet_speed = flt_server_flight_speed_multiplier();
  $fleet_speed = flt_fleet_speed($user_row, $fleet_array, $shipsData);
  if (!empty($fleet_array) && $fleet_speed && $game_fleet_speed) {
    $speed_percent = $speed_percent ? max(min($speed_percent, 10), 1) : 10;
    $real_speed = $speed_percent * sqrt($fleet_speed);

    $duration = max(1, round((35000 / $speed_percent * sqrt($distance * 10 / $fleet_speed) + 10) / $game_fleet_speed));

    foreach ($fleet_array as $ship_id => $ship_count) {
      if (!$ship_id || !$ship_count) {
        continue;
      }

      $single_ship_data = !empty($shipsData[$ship_id]) ? $shipsData[$ship_id] : get_ship_data($ship_id, $user_row);
      $single_ship_data['speed'] = $single_ship_data['speed'] < 1 ? 1 : $single_ship_data['speed'];

      $consumption += $single_ship_data['consumption'] * $ship_count * pow($real_speed / sqrt($single_ship_data['speed']) / 10 + 1, 2);
      $capacity += $single_ship_data['capacity'] * $ship_count;
    }

    $consumption = ceil($distance * $consumption / 35000) + 1;
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
  global $config;

  $config_bashing_attacks = $config->fleet_bashing_attacks;
  $config_bashing_interval = $config->fleet_bashing_interval;
  if (!$config_bashing_attacks) {
    // Bashing allowed - protection disabled
    return ATTACK_ALLOWED;
  }

  $bashing_result = ATTACK_BASHING;
  if ($user['ally_id'] && $enemy['ally_id']) {
    $relations = ali_relations($user['ally_id'], $enemy['ally_id']);
    if (!empty($relations)) {
      $relations = $relations[$enemy['ally_id']];
      switch ($relations['alliance_diplomacy_relation']) {
        case ALLY_DIPLOMACY_WAR:
          if (SN_TIME_NOW - $relations['alliance_diplomacy_time'] <= $config->fleet_bashing_war_delay) {
            $bashing_result = ATTACK_BASHING_WAR_DELAY;
          } else {
            return ATTACK_ALLOWED;
          }
        break;
        // Here goes other relations

        /*
                default:
                  return ATTACK_ALLOWED;
                break;
        */
      }
    }
  }

  $time_limit = SN_TIME_NOW + $flight_duration - $config->fleet_bashing_scope;
  $bashing_list = array(SN_TIME_NOW);

  // Retrieving flying fleets
  $bashing_fleet_list = DbFleetStatic::fleet_list_bashing($user['id'], $planet_dst);
  foreach ($bashing_fleet_list as $fleet_row) {
    // Checking for ACS - each ACS count only once
    if ($fleet_row['fleet_group']) {
      $bashing_list["{$user['id']}_{$fleet_row['fleet_group']}"] = $fleet_row['fleet_start_time'];
    } else {
      $bashing_list[] = $fleet_row['fleet_start_time'];
    }
  }

  // Check for joining to ACS - if there are already fleets in ACS no checks should be done
  if ($mission == MT_AKS && $bashing_list["{$user['id']}_{$fleet_group}"]) {
    return ATTACK_ALLOWED;
  }

  $query = doquery("SELECT bashing_time FROM `{{bashing}}` WHERE bashing_user_id = {$user['id']} AND bashing_planet_id = {$planet_dst['id']} AND bashing_time >= {$time_limit};");
  while ($bashing_row = db_fetch($query)) {
    $bashing_list[] = $bashing_row['bashing_time'];
  }

  sort($bashing_list);

  $last_attack = 0;
  $wave = 0;
  $attack = 1;
  foreach ($bashing_list as &$bash_time) {
    $attack++;
    if ($bash_time - $last_attack > $config_bashing_interval || $attack > $config_bashing_attacks) {
      $attack = 1;
      $wave++;
    }

    $last_attack = $bash_time;
  }

  return ($wave > $config->fleet_bashing_waves ? $bashing_result : ATTACK_ALLOWED);
}

/**
 * @param array $planet_src - source planet record/vector
 * @param array $planet_dst - destination planet record/vector
 * @param array $fleet      - array of ship amount [(int)shipId => (float)shipAmount]
 * @param int   $mission    - Mission ID
 * @param array $options    - [
 *                          P_FLEET_ATTACK_RESOURCES_SUM       => (float),
 *                          P_FLEET_ATTACK_RES_LIST            => [(int)resId => (float)amount]
 *                          P_FLEET_ATTACK_SPEED_PERCENT_TENTH => (int)1..10
 *                          P_FLEET_ATTACK_FLEET_GROUP         => (int|string)
 *                          P_FLEET_ATTACK_FLYING_COUNT        => (int)
 *                          P_FLEET_ATTACK_TARGET_STRUCTURE    => (int) - targeted defense structure snID for MISSILE missions
 *                          P_FLEET_ATTACK_STAY_TIME           => (int) - stay HOURS
 *                          ]
 *
 * @return int
 */
function flt_can_attack($planet_src, $planet_dst, $fleet = [], $mission, $options = []) {
  $result = null;

  return sn_function_call('flt_can_attack', [$planet_src, $planet_dst, $fleet, $mission, $options, &$result]);
}

/**
 * @param array $planet_src
 * @param array $planet_dst
 * @param array $fleet
 * @param int   $mission
 * @param array $options
 * @param int   $result
 *
 * @return int
 * @see flt_can_attack()
 */
function sn_flt_can_attack($planet_src, $planet_dst, $fleet = [], $mission, $options = [], &$result) {
  //TODO: try..catch
  global $config, $user;

  !is_array($options) ? $options = [] : false;

  if ($user['vacation']) {
    return $result = ATTACK_OWN_VACATION;
  }

  $sn_groups_mission = sn_get_groups('missions');
  if (!isset($sn_groups_mission[$mission])) {
    return $result = ATTACK_MISSION_ABSENT;
  }
  $sn_data_mission = $sn_groups_mission[$mission];

//TODO: Проверка на отстуствие ресурсов в нетранспортных миссиях (Транспорт, Передислокация, Колонизация)

  //TODO: Проверка на наличие ресурсов при Транспорте
  // TODO: Проверка на отрицательные ресурсы при транспорте
  // TODO: Проверка на перегрузку при транспорте

  // TODO: В ракетных миссиях могут лететь только ракеты
  // TODO: В неракетных миссиях ракеты должны отсутствовать

  if (empty($fleet) || !is_array($fleet)) {
    return $result = ATTACK_NO_FLEET;
  }

  $ships = 0;
  $recyclers = 0;
  $spies = 0;
  $resources = 0;
  $ship_ids = sn_get_groups('fleet');
  $resource_ids = sn_get_groups('resources_loot');
  foreach ($fleet as $ship_id => $ship_count) {
    $is_ship = in_array($ship_id, $ship_ids);
    $is_resource = in_array($ship_id, $resource_ids);
//    if (!$is_ship && !$is_resource) {
//      // TODO Спецобработчик для Капитана и модулей
//      return ATTACK_WRONG_UNIT;
//    }

    if ($ship_count < 0) {
      return $result = $is_ship ? ATTACK_SHIP_COUNT_WRONG : ATTACK_RESOURCE_COUNT_WRONG;
    }

    if ($ship_count > mrc_get_level($user, $planet_src, $ship_id)) {
      // TODO ATTACK_NO_MISSILE
      return $result = $is_ship ? ATTACK_NO_SHIPS : ATTACK_NO_RESOURCES;
    }

    if ($is_ship) {
      $single_ship_data = get_ship_data($ship_id, $user);
      if ($single_ship_data[P_SPEED] <= 0) {
        return $result = ATTACK_ZERO_SPEED;
      }
      $ships += $ship_count;
      $recyclers += in_array($ship_id, sn_get_groups('flt_recyclers')) ? $ship_count : 0;
      $spies += $ship_id == SHIP_SPY ? $ship_count : 0;
    } elseif ($is_resource) {
      $resources += $ship_count;
    }
  }

  if (empty($resources) && !empty($options[P_FLEET_ATTACK_RES_LIST]) && is_array($options[P_FLEET_ATTACK_RES_LIST])) {
    $resources = array_sum($options[P_FLEET_ATTACK_RES_LIST]);
  }

  if (
    isset($options[P_FLEET_ATTACK_RESOURCES_SUM])
    && $options[P_FLEET_ATTACK_RESOURCES_SUM] > 0
    && empty($sn_data_mission['transport'])
  ) {
    return $result = ATTACK_RESOURCE_FORBIDDEN;
  }

  /*
    elseif($mission == MT_TRANSPORT)
    {
      return ATTACK_TRANSPORT_EMPTY;
    }
  */

  $speed = $options[P_FLEET_ATTACK_SPEED_PERCENT_TENTH];
  if ($speed && ($speed != intval($speed) || $speed < 1 || $speed > 10)) {
    return $result = ATTACK_WRONG_SPEED;
  }

  $travel_data = flt_travel_data($user, $planet_src, $planet_dst, $fleet, $options[P_FLEET_ATTACK_SPEED_PERCENT_TENTH]);


  if (mrc_get_level($user, $planet_src, RES_DEUTERIUM) < $fleet[RES_DEUTERIUM] + $travel_data['consumption']) {
    return $result = ATTACK_NO_FUEL;
  }

  if ($travel_data['consumption'] > $travel_data['capacity']) {
    return $result = ATTACK_TOO_FAR;
  }

  if ($travel_data['hold'] < $resources) {
    return $result = ATTACK_OVERLOADED;
  }

  $fleet_start_time = SN_TIME_NOW + $travel_data['duration'];

  $fleet_group = $options[P_FLEET_ATTACK_FLEET_GROUP];
  if ($fleet_group) {
    if ($mission != MT_AKS) {
      return $result = ATTACK_WRONG_MISSION;
    };

    $acs = DbFleetStatic::dbAcsGetById($fleet_group);
    if (!$acs['id']) {
      return $result = ATTACK_NO_ACS;
    }

    if ($planet_dst['galaxy'] != $acs['galaxy'] || $planet_dst['system'] != $acs['system'] || $planet_dst['planet'] != $acs['planet'] || $planet_dst['planet_type'] != $acs['planet_type']) {
      return $result = ATTACK_ACS_WRONG_TARGET;
    }

    if ($fleet_start_time > $acs['ankunft']) {
      return $result = ATTACK_ACS_TOO_LATE;
    }

    if(DbFleetStatic::acsIsAcsFull($acs['id'])) {
      return $result = ATTACK_ACS_MAX_FLEETS;
    }
  }

  $flying_fleets = $options[P_FLEET_ATTACK_FLYING_COUNT];
  if (!$flying_fleets) {
    $flying_fleets = DbFleetStatic::fleet_count_flying($user['id']);
  }
  if (GetMaxFleets($user) <= $flying_fleets && $mission != MT_MISSILE) {
    return $result = ATTACK_NO_SLOTS;
  }

  // В одиночку шпионские зонды могут летать только в миссии Шпионаж, Передислокация и Транспорт
  if ($ships && $spies && $spies == $ships && !($mission == MT_SPY || $mission == MT_RELOCATE || $mission == MT_TRANSPORT)) {
    return $result = ATTACK_SPIES_LONLY;
  }

  // Checking for no planet
  if (!$planet_dst['id_owner']) {
    if ($mission == MT_COLONIZE && !$fleet[SHIP_COLONIZER]) {
      return $result = ATTACK_NO_COLONIZER;
    }

    if ($mission == MT_EXPLORE || $mission == MT_COLONIZE) {
      return $result = ATTACK_ALLOWED;
    }

    return $result = ATTACK_NO_TARGET;
  }

  if ($mission == MT_RECYCLE) {
    if ($planet_dst['debris_metal'] + $planet_dst['debris_crystal'] <= 0) {
      return $result = ATTACK_NO_DEBRIS;
    }
    if ($recyclers <= 0) {
      return $result = ATTACK_NO_RECYCLERS;
    }

    return $result = ATTACK_ALLOWED;
  }

  // Got planet. Checking if it is ours
  if ($planet_dst['id_owner'] == $user['id']) {
    if ($mission == MT_TRANSPORT || $mission == MT_RELOCATE) {
      return $result = ATTACK_ALLOWED;
    }

    return $planet_src['id'] == $planet_dst['id'] ? ATTACK_SAME : ATTACK_OWN;
  }

  // No, planet not ours. Cutting mission that can't be send to not-ours planet
  if ($mission == MT_RELOCATE || $mission == MT_COLONIZE || $mission == MT_EXPLORE) {
    return $result = ATTACK_WRONG_MISSION;
  }

  $enemy = db_user_by_id($planet_dst['id_owner']);
  // We cannot attack or send resource to users in VACATION mode
  if ($enemy['vacation'] && $mission != MT_RECYCLE) {
    return $result = ATTACK_VACATION;
  }

  // Multi IP protection
  // TODO: Here we need a procedure to check proxies
  if (sys_is_multiaccount($user, $enemy)) {
    return $result = ATTACK_SAME_IP;
  }

  $user_points = $user['total_points'];
  $enemy_points = $enemy['total_points'];

  // Is it transport? If yes - checking for buffing to prevent mega-alliance destroyer
  if ($mission == MT_TRANSPORT) {
    if ($user_points >= $enemy_points || $config->allow_buffing) {
      return $result = ATTACK_ALLOWED;
    } else {
      return $result = ATTACK_BUFFING;
    }
  }

  // Only aggresive missions passed to this point. HOLD counts as passive but aggresive

  // Is it admin with planet protection?
  if ($planet_dst['id_level'] > $user['authlevel']) {
    return $result = ATTACK_ADMIN;
  }

  // Okay. Now skipping protection checks for inactive longer then 1 week
  if (!$enemy['onlinetime'] || $enemy['onlinetime'] >= (SN_TIME_NOW - 60 * 60 * 24 * 7)) {
    if (
      (SN::$gc->general->playerIsNoobByPoints($enemy_points) && !SN::$gc->general->playerIsNoobByPoints($user_points))
      ||
      (SN::$gc->general->playerIs1stStrongerThen2nd($user_points, $enemy_points))
    ) {
      if ($mission != MT_HOLD) {
        return $result = ATTACK_NOOB;
      }
      if ($mission == MT_HOLD && !($user['ally_id'] && $user['ally_id'] == $enemy['ally_id'] && $config->ally_help_weak)) {
        return $result = ATTACK_NOOB;
      }
    }
  }

  // Is it HOLD mission? If yes - there should be ally deposit
  if ($mission == MT_HOLD) {
    if (mrc_get_level($user, $planet_dst, STRUC_ALLY_DEPOSIT)) {
      return $result = ATTACK_ALLOWED;
    }

    return $result = ATTACK_NO_ALLY_DEPOSIT;
  }

  if ($mission == MT_SPY) {
    return $result = $spies >= 1 ? ATTACK_ALLOWED : ATTACK_NO_SPIES;
  }

  // Is it MISSILE mission?
  if ($mission == MT_MISSILE) {
    $sn_data_mip = get_unit_param(UNIT_DEF_MISSILE_INTERPLANET);
    if (mrc_get_level($user, $planet_src, STRUC_SILO) < $sn_data_mip[P_REQUIRE][STRUC_SILO]) {
      return $result = ATTACK_NO_SILO;
    }

    if (!$fleet[UNIT_DEF_MISSILE_INTERPLANET]) {
      return $result = ATTACK_NO_MISSILE;
    }

    $distance = abs($planet_dst['system'] - $planet_src['system']);
    $mip_range = flt_get_missile_range($user);
    if ($distance > $mip_range || $planet_dst['galaxy'] != $planet_src['galaxy']) {
      return $result = ATTACK_MISSILE_TOO_FAR;
    }

    if (!empty($options[P_FLEET_ATTACK_TARGET_STRUCTURE]) && !in_array($options[P_FLEET_ATTACK_TARGET_STRUCTURE], sn_get_groups('defense_active'))) {
      return $result = ATTACK_WRONG_STRUCTURE;
    }
  }

  if ($mission == MT_DESTROY && $planet_dst['planet_type'] != PT_MOON) {
    return $result = ATTACK_WRONG_MISSION;
  }

  if ($mission == MT_ATTACK || $mission == MT_AKS || $mission == MT_DESTROY) {
    return $result = flt_bashing_check($user, $enemy, $planet_dst, $mission, $travel_data['duration'], $fleet_group);
  }

  return $result = ATTACK_ALLOWED;
}

/**
 * @param array $user    - actual user record
 * @param array $from    - actual planet record
 * @param array $to      - actual planet record
 * @param array $fleet   - array of records $unit_id -> $amount
 * @param int   $mission - fleet mission
 * @param array $options
 *
 * @return int
 * @throws Exception
 * @see flt_can_attack()
 */
function flt_t_send_fleet($user, &$from, $to, $fleet, $resources, $mission, $options = array()) {
  $internal_transaction = !sn_db_transaction_check(false) ? sn_db_transaction_start() : false;

  // TODO Потенциальный дедлок - если успела залочится запись пользователя - хозяина планеты
  $user = db_user_by_id($user['id'], true);
  $from = sys_o_get_updated($user, $from['id'], SN_TIME_NOW);
  $from = $from['planet'];

//  $fleet = [
//    202 => 1,
//  ];
//  $resources = [
//    901 => 1,
//  ];
//  var_dump($fleet);
//  var_dump($resources);
//  var_dump(mrc_get_level($user, $from, 202));
//  var_dump($from['metal']);
//  var_dump($from['deuterium']);
//  die();


  !is_array($resources) ? $resources = [] : false;
  if(empty($options[P_FLEET_ATTACK_RES_LIST])) {
    $options[P_FLEET_ATTACK_RES_LIST] = $resources;
  }
  $can_attack = flt_can_attack($from, $to, $fleet, $mission, $options);
  if ($can_attack != ATTACK_ALLOWED) {
    $internal_transaction ? sn_db_transaction_rollback() : false;

    return $can_attack;
  }

  empty($options[P_FLEET_ATTACK_SPEED_PERCENT_TENTH]) ? $options[P_FLEET_ATTACK_SPEED_PERCENT_TENTH] = 10 : false;
  $options[P_FLEET_ATTACK_STAY_TIME] = !empty($options[P_FLEET_ATTACK_STAY_TIME]) ? $options[P_FLEET_ATTACK_STAY_TIME] * PERIOD_HOUR : 0;

  $fleetObj = new Fleet();
  $travel_data = $fleetObj
    ->setMission($mission)
    ->setSourceFromPlanetRecord($from)
    ->setDestinationFromPlanetRecord($to)
    ->setUnits($fleet)
    ->setUnits($resources)
    ->setSpeedPercentInTenth($options[P_FLEET_ATTACK_SPEED_PERCENT_TENTH])
    ->calcTravelTimes(SN_TIME_NOW, $options[P_FLEET_ATTACK_STAY_TIME]);
  $fleetObj->save();

  $result = fltSendFleetAdjustPlanetResources($from['id'], $resources, $travel_data['consumption']);

  $result = fltSendFleetAdjustPlanetUnits($user, $from['id'], $fleet);

  $internal_transaction ? sn_db_transaction_commit() : false;

  $from = DBStaticPlanet::db_planet_by_id($from['id']);

//  var_dump(mrc_get_level($user, $from, 202));
//  var_dump($from['metal']);
//  var_dump($from['deuterium']);
//  die();

  return ATTACK_ALLOWED;
}

/**
 * @param array      $user
 * @param int|string $fromId
 * @param float[]    $fleet - [(int)shipId => (float)count]
 *
 * @return bool
 */
function fltSendFleetAdjustPlanetUnits($user, $fromId, $fleet) {
  $result = [];

  foreach ($fleet as $unit_id => $amount) {
    if (floatval($amount) >= 1 && intval($unit_id) && in_array($unit_id, sn_get_groups('fleet'))) {
      $result[] = OldDbChangeSet::db_changeset_prepare_unit($unit_id, -$amount, $user, $fromId);
    }
  }

  return OldDbChangeSet::db_changeset_apply(['unit' => $result]);
}

/**
 * @param int|string $fromId      - Source planet ID
 * @param array      $resources   - Array of resources to transfer [(int)resourceId => (float)amount]
 * @param int|float  $consumption - Fleet consumption
 *
 * @return bool
 *
 * @throws Exception
 */
function fltSendFleetAdjustPlanetResources($fromId, $resources, $consumption) {
  $planetObj = SN::$gc->repoV2->getPlanet($fromId);

  $planetObj->changeResource(RES_DEUTERIUM, -$consumption);

  foreach ($resources as $resource_id => $amount) {
    if (floatval($amount) >= 1 && intval($resource_id) && in_array($resource_id, sn_get_groups('resources_loot'))) {
      $planetObj->changeResource($resource_id, -$amount);
    }
  }

  return $planetObj->save();
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
  foreach ($ship_list as $transport_id => $cork) {
    $ship_data[$transport_id] = flt_travel_data($user, $from, $to, array($transport_id => 1), 10);
  }
  uasort($ship_data, 'flt_calculate_ship_to_transport_sort');

  $fleet_hold = 0;
  $fleet_capacity = 0;
  $fuel_total = $fuel_left = mrc_get_level($user, $from, RES_DEUTERIUM);
  foreach ($ship_data as $transport_id => &$ship_info) {
    $ship_loaded = min($ship_list[$transport_id], ceil($resource_amount / $ship_info['hold']), floor($fuel_left / $ship_info['consumption']));
    if ($ship_loaded) {
      $fleet_array[$transport_id] = $ship_loaded;
      $resource_amount -= min($resource_amount, $ship_info['hold'] * $ship_loaded);
      $fuel_left -= $ship_info['consumption'] * $ship_loaded;

      $fleet_capacity += $ship_info['capacity'] * $ship_loaded;
    }
  }

  return array('fleet' => $fleet_array, 'ship_data' => $ship_data, 'capacity' => $fleet_capacity, 'consumption' => $fuel_total - $fuel_left);
}
