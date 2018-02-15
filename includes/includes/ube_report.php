<?php

// ------------------------------------------------------------------------------------------------
// Записывает боевой отчет в БД
function sn_ube_report_save(&$combat_data) {
  global $config;

  // Если уже есть ИД репорта - значит репорт был взят из таблицы. С таким мы не работаем
  if ($combat_data[UBE_REPORT_CYPHER]) {
    return false;
  }

  // Генерируем уникальный секретный ключ и проверяем наличие в базе
  do {
    $combat_data[UBE_REPORT_CYPHER] = sys_random_string(32);
  } while (doquery("SELECT ube_report_cypher FROM {{ube_report}} WHERE ube_report_cypher = '{$combat_data[UBE_REPORT_CYPHER]}' LIMIT 1 FOR UPDATE", true));

  // Инициализация таблицы для пакетной вставки информации
  $sql_perform = array(
    'ube_report_player' => array(
      array(
        '`ube_report_id`',
        '`ube_report_player_player_id`',
        '`ube_report_player_name`',
        '`ube_report_player_attacker`',
        '`ube_report_player_bonus_attack`',
        '`ube_report_player_bonus_shield`',
        '`ube_report_player_bonus_armor`',
      ),
    ),

    'ube_report_fleet' => array(
      array(
        '`ube_report_id`',
        '`ube_report_fleet_player_id`',
        '`ube_report_fleet_fleet_id`',
        '`ube_report_fleet_planet_id`',
        '`ube_report_fleet_planet_name`',
        '`ube_report_fleet_planet_galaxy`',
        '`ube_report_fleet_planet_system`',
        '`ube_report_fleet_planet_planet`',
        '`ube_report_fleet_planet_planet_type`',
        '`ube_report_fleet_resource_metal`',
        '`ube_report_fleet_resource_crystal`',
        '`ube_report_fleet_resource_deuterium`',
        '`ube_report_fleet_bonus_attack`',
        '`ube_report_fleet_bonus_shield`',
        '`ube_report_fleet_bonus_armor`',
      ),
    ),

    'ube_report_outcome_fleet' => array(
      array(
        '`ube_report_id`',
        '`ube_report_outcome_fleet_fleet_id`',
        '`ube_report_outcome_fleet_resource_lost_metal`',
        '`ube_report_outcome_fleet_resource_lost_crystal`',
        '`ube_report_outcome_fleet_resource_lost_deuterium`',
        '`ube_report_outcome_fleet_resource_dropped_metal`',
        '`ube_report_outcome_fleet_resource_dropped_crystal`',
        '`ube_report_outcome_fleet_resource_dropped_deuterium`',
        '`ube_report_outcome_fleet_resource_loot_metal`',
        '`ube_report_outcome_fleet_resource_loot_crystal`',
        '`ube_report_outcome_fleet_resource_loot_deuterium`',
        '`ube_report_outcome_fleet_resource_lost_in_metal`',
      ),
    ),

    'ube_report_outcome_unit' => array(
      array(
        '`ube_report_id`',
        '`ube_report_outcome_unit_fleet_id`',
        '`ube_report_outcome_unit_unit_id`',
        '`ube_report_outcome_unit_restored`',
        '`ube_report_outcome_unit_lost`',
        '`ube_report_outcome_unit_sort_order`',
      ),
    ),

    'ube_report_unit' => array(
      array(
        '`ube_report_id`',
        '`ube_report_unit_player_id`',
        '`ube_report_unit_fleet_id`',
        '`ube_report_unit_round`',
        '`ube_report_unit_unit_id`',
        '`ube_report_unit_count`',
        '`ube_report_unit_boom`',
        '`ube_report_unit_attack`',
        '`ube_report_unit_shield`',
        '`ube_report_unit_armor`',
        '`ube_report_unit_attack_base`',
        '`ube_report_unit_shield_base`',
        '`ube_report_unit_armor_base`',
        '`ube_report_unit_sort_order`',
      ),
    ),
  );

  // Сохраняем общую информацию о бое
  $outcome = &$combat_data[UBE_OUTCOME];
  $ube_report_debris_total_in_metal = (
      floatval($outcome[UBE_DEBRIS][RES_METAL])
      + floatval($outcome[UBE_DEBRIS][RES_CRYSTAL]) * floatval($config->rpg_exchange_crystal)
    ) / (floatval($config->rpg_exchange_metal) ? floatval($config->rpg_exchange_metal) : 1);
  doquery("INSERT INTO `{{ube_report}}`
    SET
      `ube_report_cypher` = '{$combat_data[UBE_REPORT_CYPHER]}',
      `ube_report_time_combat` = '" . date(FMT_DATE_TIME_SQL, $combat_data[UBE_TIME]) . "',
      `ube_report_time_spent` = {$combat_data[UBE_TIME_SPENT]},

      `ube_report_combat_admin` = " . (int)$combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] . ",
      `ube_report_mission_type` = {$combat_data[UBE_OPTIONS][UBE_MISSION_TYPE]},

      `ube_report_combat_result` = {$outcome[UBE_COMBAT_RESULT]},
      `ube_report_combat_sfr` = " . (int)$outcome[UBE_SFR] . ",

      `ube_report_debris_metal` = " . (float)$outcome[UBE_DEBRIS][RES_METAL] . ",
      `ube_report_debris_crystal` = " . (float)$outcome[UBE_DEBRIS][RES_CRYSTAL] . ",
      `ube_report_debris_total_in_metal` = " . $ube_report_debris_total_in_metal . ",

      `ube_report_planet_id`          = " . (int)$outcome[UBE_PLANET][PLANET_ID] . ",
      `ube_report_planet_name`        = '" . db_escape($outcome[UBE_PLANET][PLANET_NAME]) . "',
      `ube_report_planet_size`        = " . (int)$outcome[UBE_PLANET][PLANET_SIZE] . ",
      `ube_report_planet_galaxy`      = " . (int)$outcome[UBE_PLANET][PLANET_GALAXY] . ",
      `ube_report_planet_system`      = " . (int)$outcome[UBE_PLANET][PLANET_SYSTEM] . ",
      `ube_report_planet_planet`      = " . (int)$outcome[UBE_PLANET][PLANET_PLANET] . ",
      `ube_report_planet_planet_type` = " . (int)$outcome[UBE_PLANET][PLANET_TYPE] . ",

      `ube_report_moon` = " . (int)$outcome[UBE_MOON] . ",
      `ube_report_moon_chance` = " . (int)$outcome[UBE_MOON_CHANCE] . ",
      `ube_report_moon_size` = " . (float)$outcome[UBE_MOON_SIZE] . ",

      `ube_report_moon_reapers` = " . (int)$outcome[UBE_MOON_REAPERS] . ",
      `ube_report_moon_destroy_chance` = " . (int)$outcome[UBE_MOON_DESTROY_CHANCE] . ",
      `ube_report_moon_reapers_die_chance` = " . (int)$outcome[UBE_MOON_REAPERS_DIE_CHANCE] . ",

      `ube_report_capture_result` = " . (int)$outcome[UBE_CAPTURE_RESULT] . "
  ");
  $ube_report_id = $combat_data[UBE_REPORT_ID] = db_insert_id();

  // Сохраняем общую информацию по игрокам
  foreach ($combat_data[UBE_PLAYERS] as $player_id => &$player_info) {
    $sql_perform['ube_report_player'][] = array(
      $ube_report_id,
      $player_id,

      "'" . db_escape($player_info[UBE_NAME]) . "'",
      (int)$player_info[UBE_ATTACKER],

      (float)$player_info[UBE_BONUSES][UBE_ATTACK],
      (float)$player_info[UBE_BONUSES][UBE_SHIELD],
      (float)$player_info[UBE_BONUSES][UBE_ARMOR],
    );
  }

  // Всякая информация по флотам
  $unit_sort_order = 0;
  foreach ($combat_data[UBE_FLEETS] as $fleet_id => &$fleet_info) {
    // Сохраняем общую информацию по флотам
    $sql_perform['ube_report_fleet'][] = array(
      $ube_report_id,
      $fleet_info[UBE_OWNER],
      $fleet_id,

      (float)$fleet_info[UBE_PLANET][PLANET_ID],
      "'" . db_escape($fleet_info[UBE_PLANET][PLANET_NAME]) . "'",
      (int)$fleet_info[UBE_PLANET][PLANET_GALAXY],
      (int)$fleet_info[UBE_PLANET][PLANET_SYSTEM],
      (int)$fleet_info[UBE_PLANET][PLANET_PLANET],
      (int)$fleet_info[UBE_PLANET][PLANET_TYPE],

      (float)$fleet_info[UBE_RESOURCES][RES_METAL],
      (float)$fleet_info[UBE_RESOURCES][RES_CRYSTAL],
      (float)$fleet_info[UBE_RESOURCES][RES_DEUTERIUM],

      (float)$fleet_info[UBE_BONUSES][UBE_ATTACK],
      (float)$fleet_info[UBE_BONUSES][UBE_SHIELD],
      (float)$fleet_info[UBE_BONUSES][UBE_ARMOR],
    );

    // Сохраняем итоговую информацию по ресурсам флота - потеряно, выброшено, увезено
    $fleet_outcome_data = &$outcome[UBE_FLEETS][$fleet_id];
    $sql_perform['ube_report_outcome_fleet'][] = array(
      $ube_report_id,
      $fleet_id,

      (float)$fleet_outcome_data[UBE_RESOURCES_LOST][RES_METAL],
      (float)$fleet_outcome_data[UBE_RESOURCES_LOST][RES_CRYSTAL],
      (float)$fleet_outcome_data[UBE_RESOURCES_LOST][RES_DEUTERIUM],

      (float)$fleet_outcome_data[UBE_CARGO_DROPPED][RES_METAL],
      (float)$fleet_outcome_data[UBE_CARGO_DROPPED][RES_CRYSTAL],
      (float)$fleet_outcome_data[UBE_CARGO_DROPPED][RES_DEUTERIUM],

      (float)$fleet_outcome_data[UBE_RESOURCES_LOOTED][RES_METAL],
      (float)$fleet_outcome_data[UBE_RESOURCES_LOOTED][RES_CRYSTAL],
      (float)$fleet_outcome_data[UBE_RESOURCES_LOOTED][RES_DEUTERIUM],

      (float)$fleet_outcome_data[UBE_RESOURCES_LOST_IN_METAL][RES_METAL],
    );

    // Сохраняем результаты по юнитам - потеряно и восстановлено
    foreach ($fleet_info[UBE_COUNT] as $unit_id => $unit_count) {
      if ($fleet_outcome_data[UBE_UNITS_LOST][$unit_id] || $fleet_outcome_data[UBE_DEFENCE_RESTORE][$unit_id]) {
        $unit_sort_order++;
        $sql_perform['ube_report_outcome_unit'][] = array(
          $ube_report_id,
          $fleet_id,

          $unit_id,
          (float)$fleet_outcome_data[UBE_DEFENCE_RESTORE][$unit_id],
          (float)$fleet_outcome_data[UBE_UNITS_LOST][$unit_id],

          $unit_sort_order,
        );
      }
    }
  }

  // Сохраняем информацию о раундах
  $unit_sort_order = 0;
  foreach ($combat_data[UBE_ROUNDS] as $round => &$round_data) {
    foreach ($round_data[UBE_FLEETS] as $fleet_id => &$fleet_data) {
      foreach ($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
        $unit_sort_order++;

        $sql_perform['ube_report_unit'][] = array(
          $ube_report_id,
          $fleet_data[UBE_FLEET_INFO][UBE_OWNER],
          $fleet_id,
          $round,

          $unit_id,
          $unit_count,
          (int)$fleet_data[UBE_UNITS_BOOM][$unit_id],

          $fleet_data[UBE_ATTACK][$unit_id],
          $fleet_data[UBE_SHIELD][$unit_id],
          $fleet_data[UBE_ARMOR][$unit_id],

          $fleet_data[UBE_ATTACK_BASE][$unit_id],
          $fleet_data[UBE_SHIELD_BASE][$unit_id],
          $fleet_data[UBE_ARMOR_BASE][$unit_id],

          $unit_sort_order,
        );
      }
    }
  }

  // Пакетная вставка данных
  foreach ($sql_perform as $table_name => $table_data) {
    if (count($table_data) < 2) {
      continue;
    }
    foreach ($table_data as &$record_data) {
      $record_data = '(' . implode(',', $record_data) . ')';
    }
    $fields = $table_data[0];
    unset($table_data[0]);
    doquery("INSERT INTO {{{$table_name}}} {$fields} VALUES " . implode(',', $table_data));
  }

  return $combat_data[UBE_REPORT_CYPHER];
}

// ------------------------------------------------------------------------------------------------
// Читает боевой отчет из БД
function sn_ube_report_load($report_cypher) {
  $report_cypher = db_escape($report_cypher);

  $report_row = doquery("SELECT * FROM {{ube_report}} WHERE ube_report_cypher = '{$report_cypher}' LIMIT 1", true);
  if (!$report_row) {
    return UBE_REPORT_NOT_FOUND;
  }

  $combat_data = array(
    UBE_OPTIONS => array(
      UBE_LOADED       => true,
      UBE_COMBAT_ADMIN => $report_row['ube_report_combat_admin'],
      UBE_MISSION_TYPE => $report_row['ube_report_mission_type'],
    ),

    UBE_TIME          => strtotime($report_row['ube_report_time_combat']),
    UBE_TIME_SPENT    => $report_row['ube_report_time_spent'],
    UBE_REPORT_CYPHER => $report_cypher,
    UBE_REPORT_ID     => $report_row['ube_report_id'],

    UBE_OUTCOME => array(
      UBE_COMBAT_RESULT => $report_row['ube_report_combat_result'],
      UBE_SFR           => $report_row['ube_report_combat_sfr'],

      UBE_PLANET => array(
        PLANET_ID     => $report_row['ube_report_planet_id'],
        PLANET_NAME   => $report_row['ube_report_planet_name'],
        PLANET_SIZE   => $report_row['ube_report_planet_size'],
        PLANET_GALAXY => $report_row['ube_report_planet_galaxy'],
        PLANET_SYSTEM => $report_row['ube_report_planet_system'],
        PLANET_PLANET => $report_row['ube_report_planet_planet'],
        PLANET_TYPE   => $report_row['ube_report_planet_planet_type'],
      ),

      UBE_DEBRIS => array(
        RES_METAL   => $report_row['ube_report_debris_metal'],
        RES_CRYSTAL => $report_row['ube_report_debris_crystal'],
      ),

      UBE_MOON        => $report_row['ube_report_moon'],
      UBE_MOON_CHANCE => $report_row['ube_report_moon_chance'],
      UBE_MOON_SIZE   => $report_row['ube_report_moon_size'],

      UBE_MOON_REAPERS            => $report_row['ube_report_moon_reapers'],
      UBE_MOON_DESTROY_CHANCE     => $report_row['ube_report_moon_destroy_chance'],
      UBE_MOON_REAPERS_DIE_CHANCE => $report_row['ube_report_moon_reapers_die_chance'],

      UBE_CAPTURE_RESULT => $report_row['ube_report_capture_result'],

      UBE_ATTACKERS => array(),
      UBE_DEFENDERS => array(),
    ),
  );

  $outcome = &$combat_data[UBE_OUTCOME];

  $query = doquery("SELECT * FROM {{ube_report_player}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
  while ($player_row = db_fetch($query)) {
    $combat_data[UBE_PLAYERS][$player_row['ube_report_player_player_id']] = array(
      UBE_NAME     => $player_row['ube_report_player_name'],
      UBE_ATTACKER => $player_row['ube_report_player_attacker'],

      UBE_BONUSES => array(
        UBE_ATTACK => $player_row['ube_report_player_bonus_attack'],
        UBE_SHIELD => $player_row['ube_report_player_bonus_shield'],
        UBE_ARMOR  => $player_row['ube_report_player_bonus_armor'],
      ),
    );
  }

  $query = doquery("SELECT * FROM {{ube_report_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
  while ($fleet_row = db_fetch($query)) {
    $combat_data[UBE_FLEETS][$fleet_row['ube_report_fleet_fleet_id']] = array(
      UBE_OWNER => $fleet_row['ube_report_fleet_player_id'],

      UBE_FLEET_TYPE => $combat_data[UBE_PLAYERS][$fleet_row['ube_report_fleet_player_id']][UBE_ATTACKER] ? UBE_ATTACKERS : UBE_DEFENDERS,

      UBE_PLANET => array(
        PLANET_ID     => $fleet_row['ube_report_fleet_planet_id'],
        PLANET_NAME   => $fleet_row['ube_report_fleet_planet_name'],
        PLANET_GALAXY => $fleet_row['ube_report_fleet_planet_galaxy'],
        PLANET_SYSTEM => $fleet_row['ube_report_fleet_planet_system'],
        PLANET_PLANET => $fleet_row['ube_report_fleet_planet_planet'],
        PLANET_TYPE   => $fleet_row['ube_report_fleet_planet_planet_type'],
      ),

      UBE_BONUSES => array(
        UBE_ATTACK => $player_row['ube_report_fleet_bonus_attack'],
        UBE_SHIELD => $player_row['ube_report_fleet_bonus_shield'],
        UBE_ARMOR  => $player_row['ube_report_fleet_bonus_armor'],
      ),

      UBE_RESOURCES => array(
        RES_METAL     => $player_row['ube_report_fleet_resource_metal'],
        RES_CRYSTAL   => $player_row['ube_report_fleet_resource_crystal'],
        RES_DEUTERIUM => $player_row['ube_report_fleet_resource_deuterium'],
      ),
    );
  }

  $combat_data[UBE_ROUNDS] = array();
  $rounds_data = &$combat_data[UBE_ROUNDS];

  $query = doquery("SELECT * FROM {{ube_report_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_unit_sort_order`");
  while ($round_row = db_fetch($query)) {
    $round = $round_row['ube_report_unit_round'];
    $fleet_id = $round_row['ube_report_unit_fleet_id'];

    $side = $combat_data[UBE_FLEETS][$fleet_id][UBE_FLEET_TYPE];
    $rounds_data[$round][$side][UBE_ATTACK][$fleet_id] = 0;

    if (!isset($rounds_data[$round][UBE_FLEETS][$fleet_id])) {
      $rounds_data[$round][UBE_FLEETS][$fleet_id] = array();
    }

    $round_fleet_data = &$rounds_data[$round][UBE_FLEETS][$fleet_id];
    $unit_id = $round_row['ube_report_unit_unit_id'];
    $round_fleet_data[UBE_COUNT][$unit_id] = $round_row['ube_report_unit_count'];
    $round_fleet_data[UBE_UNITS_BOOM][$unit_id] = $round_row['ube_report_unit_boom'];

    $round_fleet_data[UBE_ATTACK][$unit_id] = $round_row['ube_report_unit_attack'];
    $round_fleet_data[UBE_SHIELD][$unit_id] = $round_row['ube_report_unit_shield'];
    $round_fleet_data[UBE_ARMOR][$unit_id] = $round_row['ube_report_unit_armor'];

    $round_fleet_data[UBE_ATTACK_BASE][$unit_id] = $round_row['ube_report_unit_attack_base'];
    $round_fleet_data[UBE_SHIELD_BASE][$unit_id] = $round_row['ube_report_unit_shield_base'];
    $round_fleet_data[UBE_ARMOR_BASE][$unit_id] = $round_row['ube_report_unit_armor_base'];
  }


  $query = doquery("SELECT * FROM {{ube_report_outcome_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
  while ($row = db_fetch($query)) {
    $fleet_id = $row['ube_report_outcome_fleet_fleet_id'];

    $outcome[UBE_FLEETS][$fleet_id] = array(
      UBE_RESOURCES_LOST => array(
        RES_METAL     => $row['ube_report_outcome_fleet_resource_lost_metal'],
        RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_lost_crystal'],
        RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_lost_deuterium'],
      ),

      UBE_CARGO_DROPPED => array(
        RES_METAL     => $row['ube_report_outcome_fleet_resource_dropped_metal'],
        RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_dropped_crystal'],
        RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_dropped_deuterium'],
      ),

      UBE_RESOURCES_LOOTED => array(
        RES_METAL     => $row['ube_report_outcome_fleet_resource_loot_metal'],
        RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_loot_crystal'],
        RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_loot_deuterium'],
      ),

      UBE_RESOURCES_LOST_IN_METAL => array(
        RES_METAL => $row['ube_report_outcome_fleet_resource_lost_in_metal'],
      ),
    );

    $side = $combat_data[UBE_FLEETS][$fleet_id][UBE_FLEET_TYPE];

    $outcome[$side][UBE_FLEETS][$fleet_id] = &$outcome[UBE_FLEETS][$fleet_id];
  }

  $query = doquery("SELECT * FROM {{ube_report_outcome_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_outcome_unit_sort_order`");
  while ($row = db_fetch($query)) {
    $fleet_id = $row['ube_report_outcome_unit_fleet_id'];
    $side = $combat_data[UBE_FLEETS][$fleet_id][UBE_FLEET_TYPE];
    $outcome[$side][UBE_FLEETS][$fleet_id][UBE_UNITS_LOST][$row['ube_report_outcome_unit_unit_id']] = $row['ube_report_outcome_unit_lost'];
    $outcome[$side][UBE_FLEETS][$fleet_id][UBE_DEFENCE_RESTORE][$row['ube_report_outcome_unit_unit_id']] = $row['ube_report_outcome_unit_restored'];
  }

  return $combat_data;
}


// ------------------------------------------------------------------------------------------------
// Парсит инфу о раунде для темплейта
function sn_ube_report_round_fleet(&$combat_data, $round) {
  global $lang;

  $fleets_info = &$combat_data[UBE_FLEETS];
  $round_template = array();
  $round_data = &$combat_data[UBE_ROUNDS][$round];
  foreach (array(UBE_ATTACKERS, UBE_DEFENDERS) as $side) {
    $round_data[$side][UBE_ATTACK] = $round_data[$side][UBE_ATTACK] ? $round_data[$side][UBE_ATTACK] : array();
    foreach ($round_data[$side][UBE_ATTACK] as $fleet_id => $temp) {
      $fleet_data = &$round_data[UBE_FLEETS][$fleet_id];
      $fleet_data_prev = &$combat_data[UBE_ROUNDS][$round - 1][UBE_FLEETS][$fleet_id];
      $fleet_template = array(
        'ID'          => $fleet_id,
        'IS_ATTACKER' => $side == UBE_ATTACKERS,
        'PLAYER_NAME' => htmlentities($combat_data[UBE_PLAYERS][$fleets_info[$fleet_id][UBE_OWNER]][UBE_NAME], ENT_COMPAT, 'UTF-8'),
      );

      if (is_array($combat_data[UBE_FLEETS][$fleet_id][UBE_PLANET])) {
        $fleet_template += $combat_data[UBE_FLEETS][$fleet_id][UBE_PLANET];
        $fleet_template[PLANET_NAME] = $fleet_template[PLANET_NAME] ? htmlentities($fleet_template[PLANET_NAME], ENT_COMPAT, 'UTF-8') : '';
        $fleet_template['PLANET_TYPE_TEXT'] = $lang['sys_planet_type_sh'][$fleet_template['PLANET_TYPE']];
      }

      foreach ($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
        $shields_original = $fleet_data[UBE_SHIELD_BASE][$unit_id] * $fleet_data_prev[UBE_COUNT][$unit_id];
        $ship_template = array(
          'ID'          => $unit_id,
          'NAME'        => $lang['tech'][$unit_id],
          'ATTACK'      => HelperString::numberFloorAndFormat($fleet_data[UBE_ATTACK][$unit_id]),
          'SHIELD'      => HelperString::numberFloorAndFormat($shields_original),
          'SHIELD_LOST' => HelperString::numberFloorAndFormat($shields_original - $fleet_data[UBE_SHIELD][$unit_id]),
          'ARMOR'       => HelperString::numberFloorAndFormat($fleet_data_prev[UBE_ARMOR][$unit_id]),
          'ARMOR_LOST'  => HelperString::numberFloorAndFormat($fleet_data_prev[UBE_ARMOR][$unit_id] - $fleet_data[UBE_ARMOR][$unit_id]),
          'UNITS'       => HelperString::numberFloorAndFormat($fleet_data_prev[UBE_COUNT][$unit_id]),
          'UNITS_LOST'  => HelperString::numberFloorAndFormat($fleet_data_prev[UBE_COUNT][$unit_id] - $fleet_data[UBE_COUNT][$unit_id]),
          'UNITS_BOOM'  => HelperString::numberFloorAndFormat($fleet_data[UBE_UNITS_BOOM][$unit_id]),
        );

        $fleet_template['.']['ship'][] = $ship_template;
      }

      $round_template[] = $fleet_template;
    }
  }

  return $round_template;
}

// ------------------------------------------------------------------------------------------------
// Рендерит таблицу общего результата боя
function sn_ube_report_table_render(&$array, $header) {
  global $lang;

  $result = array();
  if (!empty($array)) {
    foreach ($array as $unit_id => $unit_count) {
      if ($unit_count) {
        $result[] = array(
          'NAME' => $lang['tech'][$unit_id],
          'LOSS' => HelperString::numberFloorAndFormat($unit_count),
        );
      }
    }
    if ($header && count($result)) {
      array_unshift($result, array('NAME' => $header));
    }
  }

  return $result;
}

// ------------------------------------------------------------------------------------------------
// Генерирует данные для отчета из разобранных данных боя
function sn_ube_report_generate(&$combat_data, &$template_result) {
  if (!is_array($combat_data)) {
    return;
  }

  global $lang;

  // Обсчитываем результаты боя из начальных данных
  $players_info = &$combat_data[UBE_PLAYERS];
  $fleets_info = &$combat_data[UBE_FLEETS];
  $outcome = &$combat_data[UBE_OUTCOME];
  // Генерируем отчет по флотам
  for ($round = 1; $round <= count($combat_data[UBE_ROUNDS]) - 1; $round++) {
    $round_template = array(
      'NUMBER' => $round,
      '.'      => array(
        'fleet' => sn_ube_report_round_fleet($combat_data, $round),
      ),
    );
    $template_result['.']['round'][] = $round_template;
  }

  // Боевые потери флотов
  foreach (array(UBE_ATTACKERS, UBE_DEFENDERS) as $side) {
    if (!is_array($outcome[$side][UBE_FLEETS])) {
      continue;
    }
    foreach ($outcome[$side][UBE_FLEETS] as $fleet_id => $temp) {
      $player_info = &$players_info[$fleets_info[$fleet_id][UBE_OWNER]];
      $fleet_outcome = &$outcome[UBE_FLEETS][$fleet_id];

      $template_result['.']['loss'][] = array(
        'ID'          => $fleet_id,
        'NAME'        => $player_info[UBE_NAME],
        'IS_ATTACKER' => $player_info[UBE_ATTACKER],
        '.'           => array(
          'param' => array_merge(
            sn_ube_report_table_render($fleet_outcome[UBE_DEFENCE_RESTORE], $lang['ube_report_info_restored']),
            sn_ube_report_table_render($fleet_outcome[UBE_UNITS_LOST], $lang['ube_report_info_loss_final']),
            sn_ube_report_table_render($fleet_outcome[UBE_RESOURCES_LOST], $lang['ube_report_info_loss_resources']),
            sn_ube_report_table_render($fleet_outcome[UBE_CARGO_DROPPED], $lang['ube_report_info_loss_dropped']),
            sn_ube_report_table_render($fleet_outcome[UBE_RESOURCES_LOOTED], $lang[$player_info[UBE_ATTACKER] ? 'ube_report_info_loot_lost' : 'ube_report_info_loss_gained']),
            sn_ube_report_table_render($fleet_outcome[UBE_RESOURCES_LOST_IN_METAL], $lang['ube_report_info_loss_in_metal'])
          ),
        ),
      );
    }
  }

  // Обломки
  $debrisOutcome = $combat_data[UBE_OPTIONS][UBE_SIMULATOR] ? $outcome[UBE_DEBRIS_ORIGINAL] : $outcome[UBE_DEBRIS];
  $debris = [];
  foreach ([RES_METAL, RES_CRYSTAL] as $resource_id) {
    if ($resource_amount = $debrisOutcome[$resource_id]) {
      $debris[] = array(
        'NAME'   => $lang['tech'][$resource_id],
        'AMOUNT' => HelperString::numberFloorAndFormat($resource_amount),
      );
    }
  }

// TODO: $combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] - если админский бой не генерировать осколки и не делать луну. Сделать серверную опцию

  // Координаты, тип и название планеты - если есть
//R  $planet_owner_id = $combat_data[UBE_FLEETS][0][UBE_OWNER];
  if (isset($combat_data[UBE_OUTCOME][UBE_PLANET])) {
    $template_result += $combat_data[UBE_OUTCOME][UBE_PLANET];
    $template_result[PLANET_NAME] = str_replace(' ', '&nbsp;', htmlentities($template_result[PLANET_NAME], ENT_COMPAT, 'UTF-8'));
  }

  /** @noinspection SpellCheckingInspection */
  $template_result += array(
    'MICROTIME'         => $combat_data[UBE_TIME_SPENT],
    'COMBAT_TIME'       => $combat_data[UBE_TIME] ? $combat_data[UBE_TIME] + SN_CLIENT_TIME_DIFF : 0,
    'COMBAT_TIME_TEXT'  => date(FMT_DATE_TIME, $combat_data[UBE_TIME] + SN_CLIENT_TIME_DIFF),
    'COMBAT_ROUNDS'     => count($combat_data[UBE_ROUNDS]) - 1,
    'UBE_MISSION_TYPE'  => $combat_data[UBE_OPTIONS][UBE_MISSION_TYPE],
    'MT_DESTROY'        => MT_DESTROY,
    'UBE_REPORT_CYPHER' => $combat_data[UBE_REPORT_CYPHER],
    'UBE_IS_SIMULATOR'  => $combat_data[UBE_OPTIONS][UBE_SIMULATOR],

    'PLANET_TYPE_TEXT' => $lang['sys_planet_type_sh'][$template_result['PLANET_TYPE']],

    'UBE_MOON'                    => $outcome[UBE_MOON],
    'UBE_MOON_CHANCE'             => round($outcome[UBE_MOON_CHANCE], 2),
    'UBE_MOON_SIZE'               => $outcome[UBE_MOON_SIZE],
    'UBE_MOON_REAPERS'            => $outcome[UBE_MOON_REAPERS],
    'UBE_MOON_DESTROY_CHANCE'     => $outcome[UBE_MOON_DESTROY_CHANCE],
    'UBE_MOON_REAPERS_DIE_CHANCE' => $outcome[UBE_MOON_REAPERS_DIE_CHANCE],

    'UBE_MOON_WAS'              => UBE_MOON_WAS,
    'UBE_MOON_NONE'             => UBE_MOON_NONE,
    'UBE_MOON_CREATE_SUCCESS'   => UBE_MOON_CREATE_SUCCESS,
    'UBE_MOON_CREATE_FAILED'    => UBE_MOON_CREATE_FAILED,
    'UBE_MOON_REAPERS_NONE'     => UBE_MOON_REAPERS_NONE,
    'UBE_MOON_DESTROY_SUCCESS'  => UBE_MOON_DESTROY_SUCCESS,
    'UBE_MOON_REAPERS_RETURNED' => UBE_MOON_REAPERS_RETURNED,

    'UBE_CAPTURE_RESULT'      => $combat_data[UBE_OUTCOME][UBE_CAPTURE_RESULT],
    'UBE_CAPTURE_RESULT_TEXT' => $lang['ube_report_capture_result'][$combat_data[UBE_OUTCOME][UBE_CAPTURE_RESULT]],

    'UBE_SFR'                => $outcome[UBE_SFR],
    'UBE_COMBAT_RESULT'      => $outcome[UBE_COMBAT_RESULT],
    'UBE_COMBAT_RESULT_WIN'  => UBE_COMBAT_RESULT_WIN,
    'UBE_COMBAT_RESULT_LOSS' => UBE_COMBAT_RESULT_LOSS,
  );
  $template_result['.']['debris'] = $debris;
}
