<?php

namespace DBAL;
/**
 * User: Gorlum
 * Date: 02.09.2015
 * Time: 0:41
 */
class db_mysql_v4 {
  const DB_MYSQL_TRANSACTION_SERIALIZABLE = 'SERIALIZABLE';
  const DB_MYSQL_TRANSACTION_REPEATABLE_READ = 'REPEATABLE READ';
  const DB_MYSQL_TRANSACTION_READ_COMMITTED = 'READ COMMITTED';
  const DB_MYSQL_TRANSACTION_READ_UNCOMMITTED = 'READ UNCOMMITTED';

  /**
   * Соединение с MySQL
   *
   * @var resource $link
   */
  public $link;
  /**
   * Статус соеднения с MySQL
   *
   * @var bool
   */
  public $connected = false;

  // public $dbsettings = array();

  public function mysql_connect($settings) {
    global $debug;

    static $need_keys = array('server', 'user', 'pass', 'name', 'prefix');

    if ($this->connected) {
      return true;
    }

    if (empty($settings) || !is_array($settings) || array_intersect($need_keys, array_keys($settings)) != $need_keys) {
      $debug->error_fatal('There is missconfiguration in your config.php. Check it again', $this->mysql_error());
    }

    // TODO !!!!!! DEBUG -> error!!!!
    @$this->link = mysql_connect($settings['server'], $settings['user'], $settings['pass']);
    if (!is_resource($this->link)) {
      $debug->error_fatal('DB Error - cannot connect to server', $this->mysql_error());
    }

    $this->mysql_query("/*!40101 SET NAMES 'utf8' */") or $debug->error_fatal('DB error - cannot set names', $this->mysql_error());
    $this->mysql_query("SET NAMES 'utf8';") or $debug->error_fatal('DB error - cannot set names', $this->mysql_error());

    mysql_select_db($settings['name']) or $debug->error_fatal('DB error - cannot find DB on server', $this->mysql_error());
    $this->mysql_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . self::DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';') or $debug->error_fatal('DB error - cannot set desired isolation level', $this->mysql_error());

    $this->connected = true;

    return true;
  }

  public function mysql_query($query_string) {
    return mysql_query($query_string, $this->link);
  }

  public function mysql_fetch_assoc(&$query) {
    return mysql_fetch_assoc($query);
  }

  public function mysql_fetch_row(&$query) {
    return mysql_fetch_row($query);
  }

  public function mysql_real_escape_string($unescaped_string) {
    return mysql_real_escape_string($unescaped_string, $this->link);
  }

  public function mysql_close_link() {
    if ($this->connected) {
      $this->connected = false;
      mysql_close($this->link);
      unset($this->link);
    }

    return true;
    // return mysql_close($this->link);
  }

  public function mysql_error() {
    return mysql_error($this->link);
  }

  public function mysql_insert_id() {
    return mysql_insert_id($this->link);
  }

  public function mysql_num_rows(&$result) {
    return mysql_num_rows($result);
  }

  public function mysql_affected_rows() {
    return mysql_affected_rows($this->link);
  }

  public function mysql_get_client_info() {
    return mysql_get_client_info();
  }

  public function mysql_get_server_info() {
    return mysql_get_server_info($this->link);
  }

  public function mysql_get_host_info() {
    return mysql_get_host_info($this->link);
  }

  public function mysql_stat() {
    return mysql_stat($this->link);
  }
}
