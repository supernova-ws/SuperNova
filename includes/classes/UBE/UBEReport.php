<?php

class UBEReport {

  // ------------------------------------------------------------------------------------------------
  // Записывает боевой отчет в БД
  /**
   * @param UBE $ube
   *
   * @return bool|string
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  function sn_ube_report_save($ube) {
    global $config;

    // Если уже есть ИД репорта - значит репорт был взят из таблицы. С таким мы не работаем
    if($ube->get_cypher()) {
      return false;
    }

    // Генерируем уникальный секретный ключ и проверяем наличие в базе
    do {
      $ube->report_cypher = sys_random_string(32);
    } while(doquery("SELECT ube_report_cypher FROM {{ube_report}} WHERE ube_report_cypher = '{$ube->report_cypher}' LIMIT 1 FOR UPDATE", true));

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
          '`ube_report_unit_round`',
          '`ube_report_unit_player_id`',
          '`ube_report_unit_fleet_id`',
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
    $sql_str = "INSERT INTO `{{ube_report}}`
    SET
      `ube_report_cypher` = '{$ube->report_cypher}',
      `ube_report_time_combat` = '" . date(FMT_DATE_TIME_SQL, $ube->combat_timestamp) . "',
      `ube_report_time_spent` = {$ube->time_spent},

      `ube_report_combat_admin` = " . (int)$ube->is_admin_in_combat . ",
      `ube_report_mission_type` = {$ube->mission_type_id},

      `ube_report_combat_result` = {$ube->combat_result},
      `ube_report_combat_sfr` = " . (int)$ube->is_small_fleet_recce . ",

      `ube_report_planet_id`          = " . (int)$ube->ube_planet_info[PLANET_ID] . ",
      `ube_report_planet_name`        = '" . db_escape($ube->ube_planet_info[PLANET_NAME]) . "',
      `ube_report_planet_size`        = " . (int)$ube->ube_planet_info[PLANET_SIZE] . ",
      `ube_report_planet_galaxy`      = " . (int)$ube->ube_planet_info[PLANET_GALAXY] . ",
      `ube_report_planet_system`      = " . (int)$ube->ube_planet_info[PLANET_SYSTEM] . ",
      `ube_report_planet_planet`      = " . (int)$ube->ube_planet_info[PLANET_PLANET] . ",
      `ube_report_planet_planet_type` = " . (int)$ube->ube_planet_info[PLANET_TYPE] . ",

      `ube_report_capture_result` = " . (int)$ube->capture_result . ", "
      . $ube->debris->report_generate_sql($config)
      . $ube->moon_calculator->report_generate_sql();

    doquery($sql_str);
    $ube_report_id = db_insert_id();

    // Сохраняем общую информацию по игрокам
    $player_sides = $ube->players->get_player_sides();
    foreach($player_sides as $player_id => $player_side) {
      $sql_perform['ube_report_player'][] = array(
        $ube_report_id,
        $player_id,

        "'" . db_escape($ube->players[$player_id]->name) . "'",
        $ube->players[$player_id]->getSide() == UBE_PLAYER_IS_ATTACKER ? 1 : 0,

        (float)$ube->players[$player_id]->player_bonus->calcBonus(P_ATTACK),
        (float)$ube->players[$player_id]->player_bonus->calcBonus(P_SHIELD),
        (float)$ube->players[$player_id]->player_bonus->calcBonus(P_ARMOR),
      );
    }

    // Всякая информация по флотам
    foreach($ube->fleet_list->_container as $fleet_id => $UBEFleet) {
      // Сохраняем общую информацию по флотам
      $sql_perform['ube_report_fleet'][] = $UBEFleet->sql_generate_array($ube_report_id);

      // Сохраняем итоговую информацию по ресурсам флота - потеряно, выброшено, увезено
//      $sql_perform['ube_report_outcome_fleet'][] = $ube->outcome->sql_generate_fleet_array($ube_report_id, $UBEFleet);
      $sql_perform['ube_report_outcome_fleet'][] = $UBEFleet->sql_generate_outcome_fleet_array($ube_report_id);

      // Сохраняем результаты по юнитам - потеряно и восстановлено
      $UBEFleet->sql_generate_outcome_unit_array($sql_perform['ube_report_outcome_unit'], $ube_report_id);
    }

    // Сохраняем информацию о раундах
    $ube->rounds->sql_generate_unit_array($sql_perform['ube_report_unit'], $ube_report_id, $ube->fleet_list);

    // Пакетная вставка данных
    foreach($sql_perform as $table_name => $table_data) {
      if(count($table_data) < 2) {
        continue;
      }
      foreach($table_data as &$record_data) {
        $record_data = '(' . implode(',', $record_data) . ')';
      }
      $fields = $table_data[0];
      unset($table_data[0]);
      doquery("INSERT INTO {{{$table_name}}} {$fields} VALUES " . implode(',', $table_data));
    }

    return $ube->report_cypher;
  }


  // ------------------------------------------------------------------------------------------------
  // Читает боевой отчет из БД
  /**
   * @param $report_cypher
   *
   * @return string|UBE
   */
  function sn_ube_report_load($report_cypher) {
    $report_cypher = db_escape($report_cypher);

    $report_row = doquery("SELECT * FROM {{ube_report}} WHERE ube_report_cypher = '{$report_cypher}' LIMIT 1", true);
    if(!$report_row) {
      return UBE_REPORT_NOT_FOUND;
    }

    $ube = new UBE();
    $ube->load_from_report_row($report_row, $report_cypher);

    return $ube;
  }


  /**
   * @param UBE $ube
   * @param     $template_result
   */
  function sn_ube_report_generate(UBE $ube, &$template_result) {
    if(!is_object($ube)) {
      return;
    }

    // Обсчитываем результаты боя из начальных данных
    // Генерируем отчет по флотам
    $ube->rounds->report_render_rounds($template_result, $ube); // OK3

    // Боевые потери флотов
    $template_result['.']['loss'] = $ube->fleet_list->ube_report_render_fleets_outcome($ube);

// TODO: $combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] - если админский бой не генерировать осколки и не делать луну. Сделать серверную опцию

    // Координаты, тип и название планеты - если есть
//R  $planet_owner_id = $combat_data[UBE_FLEETS][0][UBE_OWNER];
    if(isset($ube->ube_planet_info)) {
      $template_result += $ube->ube_planet_info;
      $template_result[PLANET_NAME] = str_replace(' ', '&nbsp;', htmlentities($template_result[PLANET_NAME], ENT_COMPAT, 'UTF-8'));
    }

    // Обломки
    $debris = array();
    foreach(array(RES_METAL, RES_CRYSTAL) as $resource_id) {
      if($resource_amount = $ube->debris->debris_get_resource($resource_id)) {
        $debris[] = array(
          'NAME'   => classLocale::$lang['tech'][$resource_id],
          'AMOUNT' => pretty_number($resource_amount),
        );
      }
    }
    $template_result['.']['debris'] = $debris;

    $template_result += $ube->moon_calculator->report_render_moon();

    $template_result += array(
      'MICROTIME'         => $ube->get_time_spent(),
      'COMBAT_TIME'       => $ube->combat_timestamp ? $ube->combat_timestamp + SN_CLIENT_TIME_DIFF : 0,
      'COMBAT_TIME_TEXT'  => date(FMT_DATE_TIME, $ube->combat_timestamp + SN_CLIENT_TIME_DIFF),
      'COMBAT_ROUNDS'     => $ube->rounds->count() - 1,
      'UBE_MISSION_TYPE'  => $ube->mission_type_id,
      'UBE_REPORT_CYPHER' => $ube->get_cypher(),

      'PLANET_TYPE_TEXT' => classLocale::$lang['sys_planet_type_sh'][$template_result['PLANET_TYPE']],

      'UBE_CAPTURE_RESULT'      => $ube->capture_result,
      'UBE_CAPTURE_RESULT_TEXT' => classLocale::$lang['ube_report_capture_result'][$ube->capture_result],

      'UBE_SFR'           => $ube->is_small_fleet_recce,
      'UBE_COMBAT_RESULT' => $ube->combat_result,

      'MT_DESTROY'             => MT_DESTROY,
      'UBE_COMBAT_RESULT_WIN'  => UBE_COMBAT_RESULT_WIN,
      'UBE_COMBAT_RESULT_LOSS' => UBE_COMBAT_RESULT_LOSS,
    );

  }

}
