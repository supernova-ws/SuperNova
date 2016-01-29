<?php
/**
 * User: Gorlum
 * Date: 27.01.2016
 * Time: 21:21
 *
 * This file contains all operations with `fleet_array`
 *
 */

/**
 * Возвращает ёмкость переработчиков во флоте
 *
 * @param $fleet_row
 * @param $recycler_info
 *
 * @return int
 */
function fleet_recyclers_capacity($fleet_row, $recycler_info) {
  $recyclers_incoming_capacity = 0;
  $fleet_data = sys_unit_str2arr($fleet_row['fleet_array']);
  foreach($recycler_info as $recycler_id => $recycler_data) {
    $recyclers_incoming_capacity += $fleet_data[$recycler_id] * $recycler_data['capacity'];
  }

  return $recyclers_incoming_capacity;
}

/**
 * Парсит строку юнитов в array(ID => AMOUNT)
 *
 * @param array $fleet_row
 *
 * @return array
 */
function fleet_parse_fleet_row_string_to_real_array($fleet_row) {
  return sys_unit_str2arr($fleet_row['fleet_array']);
}

function fleet_row_set_array_string_from_real_array(&$fleet_row, $fleet_array) {
  $fleet_row['fleet_array'] = sys_unit_arr2str($fleet_array);
}

/**
 * Пре-инициализирует во $fleet_row поля fleet_array и fleet_amount по данным из массива $fleet_REAL_array
 *
 * @param array $fleet_REAL_array
 *
 * @return array
 */
function fleet_pre_set_from_array($fleet_REAL_array) {
  $fleet_pre_set = array();

  $fleet_pre_set['fleet_array'] = sys_unit_arr2str($fleet_REAL_array);
  $fleet_pre_set['fleet_amount'] = array_sum($fleet_REAL_array);

  return $fleet_pre_set;
}

/**
 * Поднимает флот с планеты: пре-инициализирует $fleet_row, готовит ченджсет для планеты
 *
 * @param $fleet_REAL_array
 * @param $planet_row_changed_fields
 * @param $db_changeset
 *
 * @return array
 */
// TODO - Разбить на две функции - собственно составление $fleet_pre_set и изменение планеты
function fleet_send_from_planet($fleet_REAL_array, &$planet_row_changed_fields, &$db_changeset) {
  $sn_group_fleet = sn_get_groups('fleet');
  $sn_group_resources_loot = sn_get_groups('resources_loot');

  $db_changeset = array();
  $planet_row_changed_fields = array();
  foreach($fleet_REAL_array as $unit_id => $amount) {
    if(!$amount || !$unit_id) {
      continue;
    }

    if(in_array($unit_id, $sn_group_fleet)) {
      $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, -$amount, $user, $from['id']);
    } elseif(in_array($unit_id, $sn_group_resources_loot)) {
      $planet_row_changed_fields[pname_resource_name($unit_id)]['delta'] -= $amount;
    }
  }
}


/**
 * Функция извлекает данные для fleet_update_set из $fleet_row и fleet_real_array
 *
 * @param $something_changed
 * @param $fleet_row
 * @param $fleet_real_array
 *
 * @return array
 */
function fleet_extract_update_data_from_row(&$fleet_row, $fleet_real_array) {
  $query_data = array();
  fleet_row_set_array_string_from_real_array($fleet_row, $fleet_real_array);

  $query_data['fleet_amount'] = $fleet_row['fleet_amount'];
  $query_data['fleet_array'] = $fleet_row['fleet_array'];

  return $query_data;
}
