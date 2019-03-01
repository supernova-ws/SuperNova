<?php /** @noinspection SqlResolve */

/**
 * Created by Gorlum 01.03.2019 6:03
 */

namespace Core;


use mysqli_result;
use SN;

class Updater {

  public function upd_log_message($message) {
    global $sys_log_disabled, $upd_log, $debug;

    if ($sys_log_disabled) {
//    print("{$message}<br />");
    } else {
      $upd_log .= "{$message}\r\n";
      $debug->warning($message, 'Database Update', 103);
    }
  }

  /**
   * @deprecated
   */
  public function upd_log_version_update() {
    global $new_version;

    $this->upd_do_query('START TRANSACTION;', true);
    $this->upd_add_more_time();

    $this->upd_log_message("Detected outdated version {$new_version}. Upgrading...");
  }

  public function upd_log_version_update2() {
    global $new_version;

    $this->upd_add_more_time();
    $this->upd_log_message("Detected outdated version {$new_version}. Upgrading...");
  }

  public function upd_unset_table_info($table_name) {
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

  public function upd_drop_table($table_name) {
    $config = SN::$config;

    SN::$db->db_sql_query("DROP TABLE IF EXISTS {$config->db_prefix}{$table_name};");

    $this->upd_unset_table_info($table_name);
  }


  public function upd_load_table_info($prefix_table_name, $prefixed = true) {
    global $config, $update_tables, $update_indexes, $update_indexes_full, $update_foreigns;

    $tableName = $prefixed ? str_replace($config->db_prefix, '', $prefix_table_name) : $prefix_table_name;
    $prefix_table_name = $prefixed ? $prefix_table_name : $config->db_prefix . $prefix_table_name;

    $this->upd_unset_table_info($tableName);

    $q1 = $this->upd_do_query("SHOW FULL COLUMNS FROM {$prefix_table_name};", true);
    while ($r1 = db_fetch($q1)) {
      $update_tables[$tableName][$r1['Field']] = $r1;
    }

    $q1 = $this->upd_do_query("SHOW INDEX FROM {$prefix_table_name};", true);
    while ($r1 = db_fetch($q1)) {
      $update_indexes[$tableName][$r1['Key_name']] .= "{$r1['Column_name']},";
      $update_indexes_full[$tableName][$r1['Key_name']][$r1['Column_name']] = $r1;
    }

    $q1 = $this->upd_do_query("SELECT * FROM `information_schema`.`KEY_COLUMN_USAGE` WHERE `TABLE_SCHEMA` = '" . db_escape(SN::$db_name) . "' AND TABLE_NAME = '{$prefix_table_name}' AND REFERENCED_TABLE_NAME is not null;", true);
    while ($r1 = db_fetch($q1)) {
      $table_referenced = str_replace($config->db_prefix, '', $r1['REFERENCED_TABLE_NAME']);

      $update_foreigns[$tableName][$r1['CONSTRAINT_NAME']] .= "{$r1['COLUMN_NAME']},{$table_referenced},{$r1['REFERENCED_COLUMN_NAME']};";
    }
  }

  public function upd_check_key($key, $default_value, $condition = false) {
    global $config, $sys_log_disabled;

    $config->db_loadItem($key);
    if ($condition || !isset($config->$key)) {
      $this->upd_add_more_time();
      if (!$sys_log_disabled) {
        $this->upd_log_message("Updating config key '{$key}' with value '{$default_value}'");
      }
      $config->db_saveItem($key, $default_value);
    } else {
      $config->db_saveItem($key);
    }
  }

  /**
   * @param string          $table
   * @param string|string[] $alters
   * @param bool            $condition
   *
   * @return bool|mysqli_result|null
   */
  public function upd_alter_table($table, $alters, $condition = true) {
    global $config;

    if (!$condition) {
      return null;
    }

    $this->upd_add_more_time();
    $alters_print = is_array($alters) ? dump($alters) : $alters;
    $this->upd_log_message("Altering table '{$table}' with alterations {$alters_print}");

    if (!is_array($alters)) {
      $alters = array($alters);
    }

    $alters = implode(',', $alters);
    // foreach($alters as $table_name => )
    $qry = "ALTER TABLE {$config->db_prefix}{$table} {$alters};";

    $result = $this->upd_do_query($qry);
    $error = db_error();
    if ($error) {
      die("Altering error for table `{$table}`: {$error}<br />{$alters_print}");
    }

    $this->upd_load_table_info($table, false);

    return $result;
  }

  /**
   * @param string       $table_name
   * @param string|array $declaration
   * @param string       $tableOptions
   *
   * @return bool|mysqli_result
   */
  public function upd_create_table($table_name, $declaration, $tableOptions = '') {
    global $config, $update_tables;

    $result = null;

    if (!$update_tables[$table_name]) {
      $this->upd_do_query('set foreign_key_checks = 0;', true);
      if (is_array($declaration)) {
        $declaration = implode(',', $declaration);
      }
      $declaration = trim($declaration);
      if (substr($declaration, 0, 1) != '(') {
        $declaration = "($declaration)";
      }
      $tableOptions = trim($tableOptions);
      if (!empty($tableOptions)) {
        $declaration .= $tableOptions;
      }
      $result = $this->upd_do_query("CREATE TABLE IF NOT EXISTS `{$config->db_prefix}{$table_name}` {$declaration}");
      $error = db_error();
      if ($error) {
        die("Creating error for table `{$table_name}`: {$error}<br />" . dump($declaration));
      }
      $this->upd_do_query('set foreign_key_checks = 1;', true);
      $this->upd_load_table_info($table_name, false);
      SN::$db->schema()->clear();
    }

    return $result;
  }

  public function upd_add_more_time($time = 0) {
    global $config, $sys_log_disabled;

    $time = $time ? $time : $config->upd_lock_time;
    !$sys_log_disabled ? $config->pass()->var_db_update_end = SN_TIME_NOW + $time : false;
    set_time_limit($time);
  }

  /**
   * @param int $id
   *
   * @return bool
   */
  public function updPatchExists($id) {
    $q = $this->upd_do_query("SELECT 1 FROM `{{server_patches}}` WHERE `id` = " . intval($id), true);

    return !empty(db_fetch($q));
  }

  /**
   * @param int $id
   */
  public function updPatchRegister($id) {
    $this->upd_do_query("INSERT INTO `{{server_patches}}` SET `id` = " . intval($id));
  }

  /**
   * @param int      $patchId
   * @param callable $callable
   * @param bool     $preCheck - [PATCH_REGISTER|PATCH_PRE_CHECK]
   */
  public function updPatchApply($patchId, $callable, $preCheck = PATCH_REGISTER) {
    if (!$this->updPatchExists($patchId)) {
      $callable();

      if ($preCheck == PATCH_REGISTER) {
        $this->updPatchRegister($patchId);
      }
    }
  }

  /**
   * @param string $query
   * @param bool   $no_log
   *
   * @return bool|mysqli_result
   */
  public function upd_do_query($query, $no_log = false) {
    global $update_tables;

    $this->upd_add_more_time();

    if (!$no_log) {
      $this->upd_log_message("Performing query '{$query}'");
    }

    if (strpos($query, '{{') !== false) {
      foreach ($update_tables as $tableName => $cork) {
        $query = str_replace("{{{$tableName}}}", SN::$db->db_prefix . $tableName, $query);
      }
    }
    $result = SN::$db->db_sql_query($query) or die('Query error for ' . $query . ': ' . db_error());

    return $result;
  }

}
