<?php

/**
 * @version 2015-04-11 11:47:49 39b14.2
 * @copyright 2008-2015 Gorlum for Project "SuperNova.WS"
 */

if(!defined('INSIDE')) {
  die();
}

require_once('db/db_queries.php');

function db_change_units_perform($query, $tablename, $object_id) {
  $query = implode(',', $query);
  if($query && $object_id) {
    return SN::db_upd_record_by_id($tablename == 'users' ? LOC_USER : LOC_PLANET, $object_id, $query);
  }
}

// TODO: THIS FUNCTION IS OBSOLETE AND SHOULD BE REPLACED!
// TODO - ТОЛЬКО ДЛЯ РЕСУРСОВ
// $unit_list should have unique entrances! Recompress non-uniq entrances before pass param!
function db_change_units(&$user, &$planet, $unit_list = array(), $query = null) {
  $query = is_array($query) ? $query : array(
    LOC_USER => array(),
    LOC_PLANET => array(),
  );

  $group = sn_get_groups('resources_loot');

  foreach($unit_list as $unit_id => $unit_amount) {
    if(!in_array($unit_id, $group)) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: db_change_units() вызван для не-ресурсов!</h1>');
      pdump(debug_backtrace());
      die('db_change_units() вызван для не-ресурсов!');
    }

    if(!$unit_amount) {
      continue;
    }

    $unit_db_name = pname_resource_name($unit_id);

    $unit_location = sys_get_unit_location($user, $planet, $unit_id);

    // Changing value in object
    switch($unit_location) {
      case LOC_USER:
        $user[$unit_db_name] += $unit_amount;
        break;
      case LOC_PLANET:
        $planet[$unit_db_name] += $unit_amount;
        break;
    }

    $unit_amount = $unit_amount < 0 ? $unit_amount : "+{$unit_amount}"; // Converting positive unit_amount to string '+unit_amount'
    $query[$unit_location][$unit_id] = "`{$unit_db_name}`=`{$unit_db_name}`{$unit_amount}";
  }

  db_change_units_perform($query[LOC_USER], 'users', $user['id']);
  db_change_units_perform($query[LOC_PLANET], 'planets', $planet['id']);
}


/**
 * Функция проверяет статус транзакции
 *
 * @param null|true|false $status Должна ли быть запущена транзакция в момент проверки
 *   <p>null - транзакция НЕ должна быть запущена</p>
 *   <p>true - транзакция должна быть запущена - для совместимости с $for_update</p>
 *   <p>false - всё равно - для совместимости с $for_update</p>
 * @return bool Текущий статус транзакции
 */
function sn_db_transaction_check($transaction_should_be_started = null) {
  return SN::db_transaction_check($transaction_should_be_started);
}
function sn_db_transaction_start($level = '') {
  return SN::db_transaction_start($level);
}
function sn_db_transaction_commit() {
  return SN::db_transaction_commit();
}
function sn_db_transaction_rollback() {
  return SN::db_transaction_rollback();
}




function db_error() {
  return SN::$db->db_error();
}
function sn_db_connect() {
  return SN::$db->sn_db_connect();
}
function sn_db_disconnect() {
  return SN::$db->db_disconnect();
}

/**
 * @param        $query
 * @param string $table
 * @param bool   $fetch
 * @param bool   $skip_query_check
 *
 * @return array|bool|mysqli_result|null
 * @deprecated
 */
function doquery($query, $table = '', $fetch = false, $skip_query_check = false) {
  if(is_bool($table) || !is_string($table)) {
    $fetch = $table;
  }
  return SN::$db->doquery($query, $fetch, $skip_query_check);
}
function db_fetch(&$query) {
  return SN::$db->db_fetch($query);
}
function db_fetch_row(&$query) {
  return SN::$db->db_fetch_row($query);
}
function db_escape($unescaped_string) {
  return SN::$db->db_escape($unescaped_string);
}
function db_insert_id() {
  return SN::$db->db_insert_id();
}
function db_num_rows(&$result) {
  return SN::$db->db_num_rows($result);
}
function db_affected_rows() {
  return SN::$db->db_affected_rows();
}
// Информационные функции
function db_get_client_info() {
  return SN::$db->db_get_client_info();
}
function db_get_server_info() {
  return SN::$db->db_get_server_info();
}
function db_get_host_info() {
  return SN::$db->db_get_host_info();
}
function db_server_stat() {
  return SN::$db->db_get_server_stat();
}
