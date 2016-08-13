<?php

if (!defined('IN_UPDATE')) {
  die('Trying to call update helpers externally!');
}

function upd_do_query($query, $no_log = false) {
  global $update_tables;

  upd_add_more_time();
  if (!$no_log) {
    upd_log_message("Performing query '{$query}'");
  }

  // classSupernova::$db->sn_db_connect();
  if (!(strpos($query, '{{') === false)) {
    foreach ($update_tables as $tableName => $cork) {
      $query = str_replace("{{{$tableName}}}", classSupernova::$db->db_prefix . $tableName, $query);
    }
  }
  !($result = classSupernova::$db->db_sql_query($query)) ? die('Query error for ' . $query . ': ' . classSupernova::$db->db_error()) : false;

  return $result;
}

function upd_check_key($key, $default_value, $condition = false) {
  global $sys_log_disabled;

  classSupernova::$config->db_loadItem($key);
  if ($condition || !isset(classSupernova::$config->$key)) {
    upd_add_more_time();
    if (!$sys_log_disabled) {
      upd_log_message("Updating config key '{$key}' with value '{$default_value}'");
    }
    classSupernova::$config->db_saveItem($key, $default_value);
  } else {
    classSupernova::$config->db_saveItem($key);
  }
}

function upd_log_version_update() {
  global $new_version;

  classSupernova::$db->doSql('START TRANSACTION;');
  upd_add_more_time();
  upd_log_message("Detected outdated version {$new_version}. Upgrading...");
}

function upd_add_more_time($time = 0) {
  global $sys_log_disabled;

  $time = $time ? $time : (classSupernova::$config->upd_lock_time ? classSupernova::$config->upd_lock_time : 30);
  !$sys_log_disabled ? classSupernova::$config->db_saveItem('var_db_update_end', SN_TIME_NOW + $time) : false;
  set_time_limit($time);
}

function upd_log_message($message) {
  global $sys_log_disabled, $upd_log;

  if ($sys_log_disabled) {
//    print("{$message}<br />");
  } else {
    $upd_log .= "{$message}\r\n";
    classSupernova::$debug->warning($message, 'Database Update', 103);
  }
}

function upd_unset_table_info($table_name) {
  global $update_tables, $update_indexes, $update_foreigns;

  if (isset($update_tables[$table_name])) {
    unset($update_tables[$table_name]);
  }

  if (isset($update_indexes[$table_name])) {
    unset($update_indexes[$table_name]);
  }

  if (isset($update_foreigns[$table_name])) {
    unset($update_foreigns[$table_name]);
  }
}

function upd_load_table_info($prefix_table_name, $prefixed = true) {
  global $update_tables, $update_indexes, $update_indexes_full, $update_foreigns;

  $tableName = $prefixed ? str_replace(classSupernova::$config->db_prefix, '', $prefix_table_name) : $prefix_table_name;
  $prefix_table_name = $prefixed ? $prefix_table_name : classSupernova::$config->db_prefix . $prefix_table_name;

  upd_unset_table_info($tableName);

  $q1 = upd_do_query("SHOW FULL COLUMNS FROM {$prefix_table_name};", true);
  while ($r1 = db_fetch($q1)) {
    $update_tables[$tableName][$r1['Field']] = $r1;
  }

  $q1 = upd_do_query("SHOW INDEX FROM {$prefix_table_name};", true);
  while ($r1 = db_fetch($q1)) {
    $update_indexes[$tableName][$r1['Key_name']] .= "{$r1['Column_name']},";
    $update_indexes_full[$tableName][$r1['Key_name']][$r1['Column_name']] = $r1;
  }

  $q1 = upd_do_query("SELECT * FROM `information_schema`.`KEY_COLUMN_USAGE` WHERE `TABLE_SCHEMA` = '" . db_escape(classSupernova::$db_name) . "' AND TABLE_NAME = '{$prefix_table_name}' AND REFERENCED_TABLE_NAME is not null;", true);
  while ($r1 = db_fetch($q1)) {
    $table_referenced = str_replace(classSupernova::$config->db_prefix, '', $r1['REFERENCED_TABLE_NAME']);

    $update_foreigns[$tableName][$r1['CONSTRAINT_NAME']] .= "{$r1['COLUMN_NAME']},{$table_referenced},{$r1['REFERENCED_COLUMN_NAME']};";
  }
}

/**
 * @param string       $table
 * @param string|array $alters
 * @param bool         $condition
 *
 * @return bool|mysqli_result|void
 */
function upd_alter_table($table, $alters, $condition = true) {
  if (!$condition) {
    return;
  }

  upd_add_more_time();
  $alters_print = is_array($alters) ? dump($alters) : $alters;
  upd_log_message("Altering table '{$table}' with alterations {$alters_print}");

  if (!is_array($alters)) {
    $alters = array($alters);
  }

  $alters = implode(',', $alters);
  $qry = "ALTER TABLE {{{$table}}} {$alters};";

  $result = upd_do_query($qry);
  $error = classSupernova::$db->db_error();
  if ($error) {
    die("Altering error for table `{$table}`: {$error}<br />{$alters_print}");
  }

  upd_load_table_info($table, false);

  return $result;
}

function upd_drop_table($table_name) {
  $db_prefix = classSupernova::$config->db_prefix;
  classSupernova::$db->db_sql_query("DROP TABLE IF EXISTS {$db_prefix}{$table_name};");

  upd_unset_table_info($table_name);
}

function upd_create_table($table_name, $declaration) {
  global $update_tables;

  $result = false;

  if (!$update_tables[$table_name]) {
    upd_do_query('set foreign_key_checks = 0;', true);
    $db_prefix = classSupernova::$config->db_prefix;
    $result = upd_do_query("CREATE TABLE IF NOT EXISTS `{$db_prefix}{$table_name}` {$declaration}");
    $error = classSupernova::$db->db_error();
    if ($error) {
      die("Creating error for table `{$table_name}`: {$error}<br />" . dump($declaration));
    }
    upd_do_query('set foreign_key_checks = 1;', true);
    upd_load_table_info($table_name, false);
    sys_refresh_tablelist();
  }

  return $result;
}
