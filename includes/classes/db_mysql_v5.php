<?php

/**
 * Created by Gorlum 02.09.2015 0:41
 */
class db_mysql_v5 {
  const DB_MYSQL_TRANSACTION_SERIALIZABLE = 'SERIALIZABLE';
  const DB_MYSQL_TRANSACTION_REPEATABLE_READ = 'REPEATABLE READ';
  const DB_MYSQL_TRANSACTION_READ_COMMITTED = 'READ COMMITTED';
  const DB_MYSQL_TRANSACTION_READ_UNCOMMITTED = 'READ UNCOMMITTED';

  /**
   * Соединение с MySQL
   *
   * @var mysqli $link
   */
  public $link;
  /**
   * Статус соеднения с MySQL
   *
   * @var bool
   */
  public $connected = false;

  // public $dbsettings = array();

  function mysql_connect($settings) {
    static $need_keys = array('server', 'user', 'pass', 'name', 'prefix');

    if ($this->connected) {
      return true;
    }

    if (empty($settings) || !is_array($settings) || array_intersect($need_keys, array_keys($settings)) != $need_keys) {
      classSupernova::$debug->error_fatal('There is missconfiguration in your config.php. Check it again');
    }

    @$this->link = mysqli_connect($settings['server'], $settings['user'], $settings['pass'], $settings['name']);
    if (!is_object($this->link) || $this->link->connect_error) {
      classSupernova::$debug->error_fatal('DB Error - cannot connect to server error #' . $this->link->connect_errno, $this->link->connect_error);
    }


    !$this->mysql_query("/*!40101 SET NAMES 'utf8' */")
      ? classSupernova::$debug->error_fatal('DB error - cannot set names 1 error #' . $this->link->errno, $this->link->error)
      : false;
    !$this->mysql_query("SET NAMES 'utf8';")
      ? classSupernova::$debug->error_fatal('DB error - cannot set names 2 error #' . $this->link->errno, $this->link->error)
      : false;

    !$this->mysql_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . self::DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';')
      ? classSupernova::$debug->error_fatal('DB error - cannot set desired isolation level error #' . $this->link->errno, $this->link->error)
      : false;

    $this->connected = true;

    return true;
  }

  /**
   * L0 perform the query
   *
   * @param $query_string
   *
   * @return bool|mysqli_result
   */
  function mysql_query($query_string) {
    return $this->link->query($query_string);
  }

  /**
   * L0 fetch assoc array
   *
   * @param mysqli_result $query
   *
   * @return array|null
   */
  function mysql_fetch_assoc(&$query) {
    return mysqli_fetch_assoc($query);
  }

  function mysql_fetch_row(&$query) {
    return mysqli_fetch_row($query);
  }

  function mysql_real_escape_string($unescaped_string) {
    return mysqli_real_escape_string($this->link, $unescaped_string);
  }

  function mysql_close_link() {
    if (is_object($this->link)) {
      $this->link->close();
      $this->connected = false;
      unset($this->link);
    }

    return true;
  }

  function mysql_error() {
    return mysqli_error($this->link);
  }

  function mysql_insert_id() {
    return mysqli_insert_id($this->link);
  }

  function mysql_num_rows(&$result) {
    return mysqli_num_rows($result);
  }

  function mysql_affected_rows() {
    return mysqli_affected_rows($this->link);
  }

  function mysql_get_client_info() {
    return mysqli_get_client_info();
  }

  function mysql_get_server_info() {
    return mysqli_get_server_info($this->link);
  }

  function mysql_get_host_info() {
    return mysqli_get_host_info($this->link);
  }

  function mysql_stat() {
    return mysqli_stat($this->link);
  }

  /**
   * @param string $statement
   *
   * @return bool|mysqli_stmt
   */
  function mysql_prepare($statement) {
    return mysqli_prepare($this->link, $statement);
  }

}
