<?php

function flt_fleet_speed($user, $fleet)
{
  if (!is_array($fleet))
  {
    $fleet = array($fleet => 1);
  }

  $speeds = array();
  if(!empty($fleet))
  {
    foreach ($fleet as $ship_id => $amount)
    {
      if($amount && in_array($ship_id, sn_get_groups(array('fleet', 'missile'))))
      {
        $single_ship_data = get_ship_data($ship_id, $user);
        $speeds[] = $single_ship_data['speed'];
      }
    }
  }

  return empty($speeds) ? 0 : min($speeds);
}

function flt_travel_distance($from, $to) {
  global $config;

  if($from['galaxy'] != $to['galaxy'])
  {
    $distance = abs($from['galaxy'] - $to['galaxy']) * $config->uni_galaxy_distance;
  }
  elseif($from['system'] != $to['system'])
  {
    $distance = abs($from['system'] - $to['system']) * 5 * 19 + 2700;
  }
  elseif($from['planet'] != $to['planet'])
  {
    $distance = abs($from['planet'] - $to['planet']) * 5 + 1000;
  }
  else
  {
    $distance = 5;
  }

  return $distance;
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
function flt_travel_data($user_row, $from, $to, $fleet_array, $speed_percent = 10)
{
  $distance = flt_travel_distance($from, $to);

  $consumption = 0;
  $capacity = 0;
  $duration = 0;

  $game_fleet_speed = flt_server_flight_speed_multiplier();
  $fleet_speed = flt_fleet_speed($user_row, $fleet_array);
  if(!empty($fleet_array) && $fleet_speed && $game_fleet_speed)
  {
    $speed_percent = $speed_percent ? max(min($speed_percent, 10), 1) : 10;
    $real_speed = $speed_percent * sqrt($fleet_speed);

    $duration = max(1, round((35000 / $speed_percent * sqrt($distance * 10 / $fleet_speed) + 10) / $game_fleet_speed));

    foreach($fleet_array as $ship_id => $ship_count)
    {
      if (!$ship_id || !$ship_count)
      {
        continue;
      }

      $single_ship_data = get_ship_data($ship_id, $user_row);
      $single_ship_data['speed'] = $single_ship_data['speed'] < 1 ? 1 : $single_ship_data['speed'];

      $consumption += $single_ship_data['consumption'] * $ship_count * pow($real_speed / sqrt($single_ship_data['speed']) / 10 + 1, 2);
      $capacity += $single_ship_data['capacity'] * $ship_count;
    }

    $consumption = round($distance * $consumption / 35000) + 1;
/*
    $speed_percent = $speed_percent ? max(min($speed_percent, 10), 1) : 10;

    $duration = (35000 / $speed_percent * sqrt($distance / $fleet_speed * 10 ) + 10) / $game_fleet_speed;
    $duration = max(1, round($duration));

    foreach($fleet_array as $ship_id => $ship_count)
    {
      if (!$ship_id || !$ship_count)
      {
        continue;
      }

      $single_ship_data = get_ship_data($ship_id, $user_row);
      $single_ship_data['speed'] = $single_ship_data['speed'] < 1 ? 1 : $single_ship_data['speed'];

      $consumption += $single_ship_data['consumption'] * $ship_count * pow($speed_percent / 10 * sqrt($fleet_speed / $single_ship_data['speed']) + 1, 2);
    }

    $consumption = round($distance * $consumption / 35000) + 1;
*/
  }
  return array(
    'fleet_speed' => $fleet_speed,
    'distance' => $distance,
    'duration' => $duration,
    'consumption' => $consumption,
    'capacity' => $capacity,
    'hold' => $capacity - $consumption,
    'transport_effectivness' => $consumption ? $capacity / $consumption : 0,
  );
}

function flt_bashing_check($user, $enemy, $planet_dst, $mission, $flight_duration, $fleet_group = 0)
{
  global $time_now, $config;

  $config_bashing_attacks = $config->fleet_bashing_attacks;
  $config_bashing_interval = $config->fleet_bashing_interval;
  if(!$config_bashing_attacks) {
    // Bashing allowed - protection disabled
    return ATTACK_ALLOWED;
  }

  $bashing_result = ATTACK_BASHING;
  if($user['ally_id'] && $enemy['ally_id']) {
    $relations = ali_relations($user['ally_id'], $enemy['ally_id']);
    if(!empty($relations)) {
      $relations = $relations[$enemy['ally_id']];
      switch($relations['alliance_diplomacy_relation']) {
        case ALLY_DIPLOMACY_WAR:
          if($time_now - $relations['alliance_diplomacy_time'] <= $config->fleet_bashing_war_delay) {
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

  $time_limit = $time_now + $flight_duration - $config->fleet_bashing_scope;
  $bashing_list = array($time_now);

  // Retrieving flying fleets
  $flying_fleets = array();
  $query = doquery("SELECT fleet_group, fleet_start_time FROM {{fleets}} WHERE
  fleet_end_galaxy = {$planet_dst['galaxy']} AND
  fleet_end_system = {$planet_dst['system']} AND
  fleet_end_planet = {$planet_dst['planet']} AND
  fleet_end_type   = {$planet_dst['planet_type']} AND
  fleet_owner = {$user['id']} AND fleet_mission IN (" . MT_ATTACK . "," . MT_AKS . "," . MT_DESTROY . ") AND fleet_mess = 0;");
  while($bashing_fleets = db_fetch($query)) {
    // Checking for ACS - each ACS count only once
    if($bashing_fleets['fleet_group']) {
      $bashing_list["{$user['id']}_{$bashing_fleets['fleet_group']}"] = $bashing_fleets['fleet_start_time'];
    } else {
      $bashing_list[] = $bashing_fleets['fleet_start_time'];
    }
  }

  // Check for joining to ACS - if there are already fleets in ACS no checks should be done
  if($mission == MT_AKS && $bashing_list["{$user['id']}_{$fleet_group}"]) {
    return ATTACK_ALLOWED;
  }

  $query = doquery("SELECT bashing_time FROM {{bashing}} WHERE bashing_user_id = {$user['id']} AND bashing_planet_id = {$planet_dst['id']} AND bashing_time >= {$time_limit};");
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

  return ($wave > $config->fleet_bashing_waves ? $bashing_result : ATTACK_ALLOWED);
}

function flt_can_attack($planet_src, $planet_dst, $fleet = array(), $mission, $options = false){return sn_function_call('flt_can_attack', array($planet_src, $planet_dst, $fleet, $mission, $options, &$result));}
function sn_flt_can_attack($planet_src, $planet_dst, $fleet = array(), $mission, $options = false, &$result) {
  //TODO: try..catch
  global $config, $user;

  if($user['vacation']) {
    return $result = ATTACK_OWN_VACATION;
  }

  if(empty($fleet) || !is_array($fleet)) {
    return $result = ATTACK_NO_FLEET;
  }

  $sn_groups_mission = sn_get_groups('missions');
  if(!isset($sn_groups_mission[$mission])) {
    return $result = ATTACK_MISSION_ABSENT;
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
  $ship_ids = sn_get_groups('fleet');
  $resource_ids = sn_get_groups('resources_loot');
  foreach($fleet as $ship_id => $ship_count) {
    $is_ship = in_array($ship_id, $ship_ids);
    $is_resource = in_array($ship_id, $resource_ids);
    if(!$is_ship && !$is_resource) {
      // TODO Спецобработчик для Капитана и модулей
//      return ATTACK_WRONG_UNIT;
    }

    if($ship_count < 0) {
      return $result = $is_ship ? ATTACK_SHIP_COUNT_WRONG : ATTACK_RESOURCE_COUNT_WRONG;
    }

    if($ship_count > mrc_get_level($user, $planet_src, $ship_id)) {
      // TODO ATTACK_NO_MISSILE
      return $result = $is_ship ? ATTACK_NO_SHIPS : ATTACK_NO_RESOURCES;
    }

    if($is_ship) {
      $single_ship_data = get_ship_data($ship_id, $user);
      if($single_ship_data[P_SPEED] <= 0) {
        return $result = ATTACK_ZERO_SPEED;
      }
      $ships += $ship_count;
      $recyclers += in_array($ship_id, sn_get_groups('flt_recyclers')) ? $ship_count : 0;
      $spies += $ship_id == SHIP_SPY ? $ship_count : 0;
    } elseif($is_resource) {
      $resources += $ship_count;
    }
  }
/*
  if($ships <= 0)
  {
    return ATTACK_NO_FLEET;
  }
*/

  if(isset($options['resources']) && $options['resources'] > 0 && !(isset($sn_data_mission['transport']) && $sn_data_mission['transport'])) {
    return $result = ATTACK_RESOURCE_FORBIDDEN;
  }

  /*
    elseif($mission == MT_TRANSPORT)
    {
      return ATTACK_TRANSPORT_EMPTY;
    }
  */

  $speed = $options['fleet_speed_percent'];
  if($speed && ($speed != intval($speed) || $speed < 1 || $speed > 10)) {
    return $result = ATTACK_WRONG_SPEED;
  }

  $travel_data = flt_travel_data($user, $planet_src, $planet_dst, $fleet, $options['fleet_speed_percent']);


  if(mrc_get_level($user, $planet_src, RES_DEUTERIUM) < $fleet[RES_DEUTERIUM] + $travel_data['consumption']) {
    return $result = ATTACK_NO_FUEL;
  }

  if($travel_data['consumption'] > $travel_data['capacity']) {
    return $result = ATTACK_TOO_FAR;
  }

  if($travel_data['hold'] < $resources) {
    return $result = ATTACK_OVERLOADED;
  }

  $fleet_start_time = SN_TIME_NOW + $travel_data['duration'];

  $fleet_group = $options['fleet_group'];
  if($fleet_group) {
    if($mission != MT_AKS) {
      return $result = ATTACK_WRONG_MISSION;
    };

    $acs = doquery("SELECT * FROM {{aks}} WHERE id = '{$fleet_group}' LIMIT 1;", '', true);
    if(!$acs['id']) {
      return $result = ATTACK_NO_ACS;
    }

    if ($planet_dst['galaxy'] != $acs['galaxy'] || $planet_dst['system'] != $acs['system'] || $planet_dst['planet'] != $acs['planet'] || $planet_dst['planet_type'] != $acs['planet_type']) {
      return $result = ATTACK_ACS_WRONG_TARGET;
    }

    if ($fleet_start_time>$acs['ankunft']) {
      return $result = ATTACK_ACS_TOO_LATE;
    }
  }

  $flying_fleets = $options['flying_fleets'];
  if(!$flying_fleets) {
    $flying_fleets = doquery("SELECT COUNT(fleet_id) AS `flying_fleets` FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", '', true);
    $flying_fleets = $flying_fleets['flying_fleets'];
  }
  if(GetMaxFleets($user) <= $flying_fleets && $mission != MT_MISSILE) {
    return $result = ATTACK_NO_SLOTS;
  }

  // В одиночку шпионские зонды могут летать только в миссии Шпионаж, Передислокация и Транспорт
  if($ships && $spies && $spies == $ships && !($mission == MT_SPY || $mission == MT_RELOCATE || $mission == MT_TRANSPORT)) {
    return $result = ATTACK_SPIES_LONLY;
  }

  // Checking for no planet
  if(!$planet_dst['id_owner']) {
    if($mission == MT_COLONIZE && !$fleet[SHIP_COLONIZER]) {
      return $result = ATTACK_NO_COLONIZER;
    }

    if($mission == MT_EXPLORE || $mission == MT_COLONIZE) {
      return $result = ATTACK_ALLOWED;
    }
    return $result = ATTACK_NO_TARGET;
  }

  if($mission == MT_RECYCLE) {
    if($planet_dst['debris_metal'] + $planet_dst['debris_crystal'] <= 0) {
      return $result = ATTACK_NO_DEBRIS;
    }
    if($recyclers <= 0) {
      return $result = ATTACK_NO_RECYCLERS;
    }
    return $result = ATTACK_ALLOWED;
  }

  // Got planet. Checking if it is ours
  if($planet_dst['id_owner'] == $user['id']) {
    if($mission == MT_TRANSPORT || $mission == MT_RELOCATE) {
      return $result = ATTACK_ALLOWED;
    }
    return $planet_src['id'] == $planet_dst['id'] ? ATTACK_SAME : ATTACK_OWN;
  }

  // No, planet not ours. Cutting mission that can't be send to not-ours planet
  if($mission == MT_RELOCATE || $mission == MT_COLONIZE || $mission == MT_EXPLORE) {
    return $result = ATTACK_WRONG_MISSION;
  }

  $enemy = db_user_by_id($planet_dst['id_owner']);
  // We cannot attack or send resource to users in VACATION mode
  if($enemy['vacation'] && $mission != MT_RECYCLE) {
    return $result = ATTACK_VACATION;
  }

  // Multi IP protection
  // TODO: Here we need a procedure to check proxies
  if(sys_is_multiaccount($user, $enemy)) {
    return $result = ATTACK_SAME_IP;
  }

  $user_points = $user['total_points'];
  $enemy_points = $enemy['total_points'];

  // Is it transport? If yes - checking for buffing to prevent mega-alliance destroyer
  if($mission == MT_TRANSPORT) {
    if($user_points >= $enemy_points || $config->allow_buffing) {
      return $result = ATTACK_ALLOWED;
    } else {
      return $result = ATTACK_BUFFING;
    }
  }

  // Only aggresive missions passed to this point. HOLD counts as passive but aggresive

  // Is it admin with planet protection?
  if($planet_dst['id_level'] > $user['authlevel']) {
    return $result = ATTACK_ADMIN;
  }

  // Okay. Now skipping protection checks for inactive longer then 1 week
  if(!$enemy['onlinetime'] || $enemy['onlinetime'] >= (SN_TIME_NOW - 60*60*24*7)) {
    if(
      ($enemy_points <= $config->game_noob_points && $user_points > $config->game_noob_points)
      ||
      ($config->game_noob_factor && $user_points > $enemy_points * $config->game_noob_factor)
    ) {
      if($mission != MT_HOLD) {
        return $result = ATTACK_NOOB;
      }
      if($mission == MT_HOLD && !($user['ally_id'] && $user['ally_id'] == $enemy['ally_id'] && $config->ally_help_weak)) {
        return $result = ATTACK_NOOB;
      }
    }
  }

  // Is it HOLD mission? If yes - there should be ally deposit
  if($mission == MT_HOLD) {
    if(mrc_get_level($user, $planet_dst, STRUC_ALLY_DEPOSIT)) {
      return $result = ATTACK_ALLOWED;
    }
    return $result = ATTACK_NO_ALLY_DEPOSIT;
  }

  if($mission == MT_SPY) {
    return $result = $spies >= 1 ? ATTACK_ALLOWED : ATTACK_NO_SPIES;
  }

  // Is it MISSILE mission?
  if($mission == MT_MISSILE) {
    $sn_data_mip = get_unit_param(UNIT_DEF_MISSILE_INTERPLANET);
    if(mrc_get_level($user, $planet_src, STRUC_SILO) < $sn_data_mip[P_REQUIRE][STRUC_SILO]) {
      return $result = ATTACK_NO_SILO;
    }

    if(!$fleet[UNIT_DEF_MISSILE_INTERPLANET]) {
      return $result = ATTACK_NO_MISSILE;
    }

    $distance = abs($planet_dst['system'] - $planet_src['system']);
    $mip_range = flt_get_missile_range($user);
    if($distance > $mip_range || $planet_dst['galaxy'] != $planet_src['galaxy']) {
      return $result = ATTACK_MISSILE_TOO_FAR;
    }

    if(isset($options['target_structure']) && $options['target_structure'] && !in_array($options['target_structure'], sn_get_groups('defense_active'))) {
      return $result = ATTACK_WRONG_STRUCTURE;
    }
  }

  if($mission == MT_DESTROY && $planet_dst['planet_type'] != PT_MOON) {
    return $result = ATTACK_WRONG_MISSION;
  }

  if($mission == MT_ATTACK || $mission == MT_AKS || $mission == MT_DESTROY) {
    return $result = flt_bashing_check($user, $enemy, $planet_dst, $mission, $travel_data['duration'], $fleet_group);
  }

  return $result = ATTACK_ALLOWED;
}

/*
$user - actual user record
$from - actual planet record
$to - actual planet record
$fleet - array of records $unit_id -> $amount
$mission - fleet mission
*/

function flt_t_send_fleet($user, &$from, $to, $fleet, $mission, $options = array())
{
//ini_set('error_reporting', E_ALL);

  $internal_transaction = !sn_db_transaction_check(false) ? sn_db_transaction_start() : false;
//pdump($internal_transaction);

  $user = db_user_by_id($user['id'], true);
  $from = sys_o_get_updated($user, $from['id'], SN_TIME_NOW);
  $from = $from['planet'];

  $can_attack = flt_can_attack($from, $to, $fleet, $mission, $options);
  if($can_attack != ATTACK_ALLOWED)
  {
    $internal_transaction ? sn_db_transaction_rollback() : false;
    return $can_attack;
  }

  $fleet_group = isset($options['fleet_group']) ? floatval($options['fleet_group']) : 0;

  $travel_data  = flt_travel_data($user, $from, $to, $fleet, $options['fleet_speed_percent']);

  $fleet_start_time = SN_TIME_NOW + $travel_data['duration'];

  if($mission == MT_EXPLORE || $mission == MT_HOLD)
  {
    $stay_duration = $options['stay_time'] * 3600;
    $stay_time     = $fleet_start_time + $stay_duration;
  }
  else
  {
    $stay_duration = 0;
    $stay_time     = 0;
  }
  $fleet_end_time = $fleet_start_time + $travel_data['duration'] + $stay_duration;

  $fleet_ship_count  = 0;
  $fleet_string      = '';
  $db_changeset = array();
  $planet_fields = array();
  foreach($fleet as $unit_id => $amount)
  {
    if(!$amount || !$unit_id)
    {
      continue;
    }

    if(in_array($unit_id, sn_get_groups('fleet')))
    {
      $fleet_ship_count += $amount;
      $fleet_string     .= "{$unit_id},{$amount};";
      $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, -$amount, $user, $from['id']);
    }
    elseif(in_array($unit_id, sn_get_groups('resources_loot')))
    {
      $planet_fields[pname_resource_name($unit_id)]['delta'] -= $amount;
    }
  }

  $to['id_owner'] = intval($to['id_owner']);

  $QryInsertFleet  = "INSERT INTO {{fleets}} SET ";
  $QryInsertFleet .= "`fleet_owner` = '{$user['id']}', ";
  $QryInsertFleet .= "`fleet_mission` = '{$mission}', ";
  $QryInsertFleet .= "`fleet_amount` = '{$fleet_ship_count}', ";
  $QryInsertFleet .= "`fleet_array` = '{$fleet_string}', ";
  $QryInsertFleet .= "`fleet_start_time` = '{$fleet_start_time}', ";
  if($from['id'])
  {
    $QryInsertFleet .= "`fleet_start_planet_id` = '{$from['id']}', ";
  }
  $QryInsertFleet .= "`fleet_start_galaxy` = '{$from['galaxy']}', ";
  $QryInsertFleet .= "`fleet_start_system` = '{$from['system']}', ";
  $QryInsertFleet .= "`fleet_start_planet` = '{$from['planet']}', ";
  $QryInsertFleet .= "`fleet_start_type` = '{$from['planet_type']}', ";
  $QryInsertFleet .= "`fleet_end_time` = '{$fleet_end_time}', ";
  $QryInsertFleet .= "`fleet_end_stay` = '{$stay_time}', ";
  if($to['id'])
  {
    $QryInsertFleet .= "`fleet_end_planet_id` = '{$to['id']}', ";
  }
  $QryInsertFleet .= "`fleet_end_galaxy` = '{$to['galaxy']}', ";
  $QryInsertFleet .= "`fleet_end_system` = '{$to['system']}', ";
  $QryInsertFleet .= "`fleet_end_planet` = '{$to['planet']}', ";
  $QryInsertFleet .= "`fleet_end_type` = '{$to['planet_type']}', ";
  $QryInsertFleet .= "`fleet_resource_metal` = " . floatval($fleet[RES_METAL]) . ", ";
  $QryInsertFleet .= "`fleet_resource_crystal` = " . floatval($fleet[RES_CRYSTAL]) . ", ";
  $QryInsertFleet .= "`fleet_resource_deuterium` = " . floatval($fleet[RES_DEUTERIUM]) . ", ";
  $QryInsertFleet .= "`fleet_target_owner` = '{$to['id_owner']}', ";
  $QryInsertFleet .= "`fleet_group` = '{$fleet_group}', ";
  $QryInsertFleet .= "`start_time` = '" . SN_TIME_NOW . "';";
  doquery( $QryInsertFleet);

  $planet_fields[pname_resource_name(RES_DEUTERIUM)]['delta'] -= $travel_data['consumption'];
  $db_changeset['planets'][] = array(
    'action' => SQL_OP_UPDATE,
    P_VERSION => 1,
    'where' => array(
      'id' => $from['id'],
    ),
    'fields' => $planet_fields,
  );

  db_changeset_apply($db_changeset);

  $internal_transaction ? sn_db_transaction_commit() : false;
  $from = db_planet_by_id($from['id']);

  return ATTACK_ALLOWED;
//ini_set('error_reporting', E_ALL ^ E_NOTICE);
}

function flt_calculate_ship_to_transport_sort($a, $b)
{
  return $a['transport_effectivness'] == $b['transport_effectivness'] ? 0 : ($a['transport_effectivness'] > $b['transport_effectivness'] ? -1 : 1);
}

// flt_calculate_ship_to_transport - calculates how many ships need to transport pointed amount of resources
// $ship_list - list of available ships
// $resource_amount - how much amount of resources need to be transported
// $from - transport from
// $to - transport to
function flt_calculate_fleet_to_transport($ship_list, $resource_amount, $from, $to)
{
  global $user;

  $ship_data = array();
  $fleet_array = array();
  foreach($ship_list as $transport_id => $cork)
  {
    $ship_data[$transport_id] = flt_travel_data($user, $from, $to, array($transport_id => 1), 10);
  }
  uasort($ship_data, flt_calculate_ship_to_transport_sort);

  $fleet_hold = 0;
  $fleet_capacity = 0;
  $fuel_total = $fuel_left = mrc_get_level($user, $from, RES_DEUTERIUM);
  foreach($ship_data as $transport_id => &$ship_info)
  {
    $ship_loaded = min($ship_list[$transport_id], ceil($resource_amount / $ship_info['hold']), floor($fuel_left / $ship_info['consumption']));
    if($ship_loaded)
    {
      $fleet_array[$transport_id] = $ship_loaded;
      $resource_amount -= min($resource_amount, $ship_info['hold'] * $ship_loaded);
      $fuel_left -= $ship_info['consumption'] * $ship_loaded;

      $fleet_capacity += $ship_info['capacity'] * $ship_loaded;
    }
  }

  return array('fleet' => $fleet_array, 'ship_data' => $ship_data, 'capacity' => $fleet_capacity, 'consumption' => $fuel_total - $fuel_left);
}
