<?php /** @noinspection SqlResolve */

/**
 * Created by Gorlum 01.03.2019 6:03
 */

namespace Core;


use classConfig;
use mysqli_result;
use SN;

class Updater {

  public $update_tables = [];
  public $update_indexes = [];
  public $update_foreigns = [];
  public $update_indexes_full = [];

  /**
   * @var classConfig $config
   */
  protected $config;
  protected $db;

  protected $upd_log = '';

  public $new_version = 0;
  protected $old_server_status;

  public function __construct() {
    $this->config = SN::$gc->config;
    $this->db     = SN::$gc->db;

    $this->new_version = floatval($this->config->db_version);

    // Closing any transaction that can be opened to this moment
    $this->upd_do_query('ROLLBACK;', true);

    $this->config->reset();
    $this->config->db_loadAll();

    $this->config->db_prefix    = $this->db->db_prefix; // Оставить пока для совместимости
    $this->config->cache_prefix = SN::$cache_prefix;

    $this->config->debug = 0;

    $this->checkVersionSupport();

    ini_set('memory_limit', '1G');
    set_time_limit($this->config->upd_lock_time + 10);
    $this->upd_do_query('SET FOREIGN_KEY_CHECKS=0;', true);

    $this->upd_log_message('Update started. Disabling server');
    $this->old_server_status = $this->config->pass()->game_disable;
    $this->config->db_saveItem('game_disable', GAME_DISABLE_UPDATE);

    $this->upd_log_message('Server disabled. Loading table info...');
    $this->update_tables  = [];
    $this->update_indexes = [];

    $query = $this->upd_do_query('SHOW TABLES;', true);
    while ($row = db_fetch_row($query)) {
      $this->upd_load_table_info($row[0]);
    }
    $this->upd_log_message('Table info loaded. Now looking DB for upgrades...');
  }

  public function __destruct() {
    $this->config->db_loadAll();
    /*
    if($user['authlevel'] >= 3) {
      print(str_replace("\r\n", '<br>', $upd_log));
    }
    */
    $this->db->schema()->clear();

    $this->upd_log_message('Restoring server status');
    $this->config->pass()->game_disable = $this->old_server_status;
  }

  public function upd_log_message($message) {
    global $sys_log_disabled, $debug;

    if ($sys_log_disabled) {
//    print("{$message}<br />");
    } else {
      $this->upd_log .= "{$message}\r\n";
      $debug->warning($message, 'Database Update', 103);
    }
  }

  /**
   * @deprecated
   */
  public function upd_log_version_update() {
    $this->upd_do_query('START TRANSACTION;', true);
    $this->upd_add_more_time();

    $this->upd_log_message("Detected outdated version {$this->new_version}. Upgrading...");
  }

  public function upd_log_version_update2() {
    $this->upd_add_more_time();
    $this->upd_log_message("Detected outdated version {$this->new_version}. Upgrading...");
  }

  public function upd_unset_table_info($table_name) {
    if (isset($this->update_tables[$table_name])) {
      unset($this->update_tables[$table_name]);
    }

    if (isset($this->update_indexes[$table_name])) {
      unset($this->update_indexes[$table_name]);
    }

    if (isset($this->update_foreigns[$table_name])) {
      unset($this->update_foreigns[$table_name]);
    }
  }

  public function upd_drop_table($table_name) {
    $this->db->db_sql_query("DROP TABLE IF EXISTS {$this->config->db_prefix}{$table_name};");

    $this->upd_unset_table_info($table_name);
  }


  public function upd_load_table_info($prefix_table_name, $prefixed = true) {
    $tableName = $prefixed ? str_replace($this->config->db_prefix, '', $prefix_table_name) : $prefix_table_name;

    $prefix_table_name = $prefixed ? $prefix_table_name : $this->config->db_prefix . $prefix_table_name;

    $this->upd_unset_table_info($tableName);

    $q1 = $this->upd_do_query("SHOW FULL COLUMNS FROM {$prefix_table_name};", true);
    while ($r1 = db_fetch($q1)) {
      $this->update_tables[$tableName][$r1['Field']] = $r1;
    }

    $q1 = $this->upd_do_query("SHOW INDEX FROM {$prefix_table_name};", true);
    while ($r1 = db_fetch($q1)) {
      $this->update_indexes[$tableName][$r1['Key_name']] .= "{$r1['Column_name']},";

      $this->update_indexes_full[$tableName][$r1['Key_name']][$r1['Column_name']] = $r1;
    }

    $q1 = $this->upd_do_query(
      "SELECT * 
      FROM `information_schema`.`KEY_COLUMN_USAGE` 
      WHERE 
        `TABLE_SCHEMA` = '" . db_escape(SN::$db_name) . "' 
        AND TABLE_NAME = '{$prefix_table_name}' 
        AND `REFERENCED_TABLE_NAME` is not null;",
      true);
    while ($r1 = db_fetch($q1)) {
      $table_referenced = str_replace($this->config->db_prefix, '', $r1['REFERENCED_TABLE_NAME']);

      $this->update_foreigns[$tableName][$r1['CONSTRAINT_NAME']] .= "{$r1['COLUMN_NAME']},{$table_referenced},{$r1['REFERENCED_COLUMN_NAME']};";
    }
  }

  public function upd_check_key($key, $default_value, $condition = false) {
    global $sys_log_disabled;

    $this->config->pass()->$key;
    if ($condition || !isset($this->config->$key)) {
      $this->upd_add_more_time();
      if (!$sys_log_disabled) {
        $this->upd_log_message("Updating config key '{$key}' with value '{$default_value}'");
      }
      $this->config->pass()->$key = $default_value;
    } else {
      $this->config->pass()->$key = null;
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
    $qry = "ALTER TABLE {$this->config->db_prefix}{$table} {$alters};";

    $result = $this->upd_do_query($qry);
    $error  = db_error();
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
    $result = null;

    if (!$this->update_tables[$table_name]) {
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
      $result = $this->upd_do_query("CREATE TABLE IF NOT EXISTS `{$this->config->db_prefix}{$table_name}` {$declaration}");
      $error  = db_error();
      if ($error) {
        die("Creating error for table `{$table_name}`: {$error}<br />" . dump($declaration));
      }
      $this->upd_do_query('set foreign_key_checks = 1;', true);
      $this->upd_load_table_info($table_name, false);
      $this->db->schema()->clear();
    }

    return $result;
  }

  public function upd_add_more_time($time = 0) {
    global $sys_log_disabled;

    $time = $time ? $time : $this->config->upd_lock_time;
    !$sys_log_disabled ? $this->config->pass()->var_db_update_end = SN_TIME_NOW + $time : false;
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
    $this->upd_add_more_time();

    if (!$no_log) {
      $this->upd_log_message("Performing query '{$query}'");
    }

    if (strpos($query, '{{') !== false) {
      foreach ($this->update_tables as $tableName => $cork) {
        $query = str_replace("{{{$tableName}}}", $this->db->db_prefix . $tableName, $query);
      }
    }
    $result = $this->db->db_sql_query($query) or die('Query error for ' . $query . ': ' . db_error());

    return $result;
  }

  protected function checkVersionSupport() {
    if ($this->config->db_version < DB_VERSION_MIN) {
//  print("This version does not supports upgrades from SN below v{$minVersion}. Please, use SN v42 to upgrade old database.<br />
//Эта версия игры не поддерживает обновление движка версий ниже v{$minVersion}. Пожалуйста, используйте SN v42 для апгрейда со старых версий игры.<br />");
      die(
        'Internal error! Updater detects DB version LESSER then can be handled!<br />
    Possible you have VERY out-of-date SuperNova version<br />
    Use first SuperNova version not greater then  ' . DB_VERSION_MIN . ' to make preliminary upgrade and then use newest version again<br />
    List of available releases <a href="https://github.com/supernova-ws/SuperNova/releases">GIT repository</a>'
      );
    } elseif ($this->config->db_version > DB_VERSION) {
      $this->config->pass()->var_db_update_end = SN_TIME_NOW;
      die(
      'Internal error! Updater detects DB version greater then can be handled!<br />
    Possible you have out-of-date SuperNova version<br />
    Please upgrade your server from <a href="http://github.com/supernova-ws/SuperNova">GIT repository</a>'
      );
    }
  }

  public function isTableExists($table) {
    return ! empty($this->update_tables[$table]);
  }

  public function isFieldExists($table, $field) {

  }

}
