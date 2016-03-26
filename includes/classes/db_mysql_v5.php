<?php

/**
 * User: Gorlum
 * Date: 02.09.2015
 * Time: 0:41
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
    global $debug;

    static $need_keys = array('server', 'user', 'pass', 'name', 'prefix');

    if($this->connected) {
      return true;
    }

    if(empty($settings) || !is_array($settings) || array_intersect($need_keys, array_keys($settings)) != $need_keys) {
      $debug->error_fatal('There is missconfiguration in your config.php. Check it again');
    }

//    @$this->link = mysql_connect($settings['server'], $settings['user'], $settings['pass']);
//    if(!is_resource($this->link)) {
//      $debug->error_fatal('DB Error - cannot connect to server', $this->mysql_error());
//    }
    @$this->link = mysqli_connect($settings['server'], $settings['user'], $settings['pass'], $settings['name']);
    if (!is_object($this->link) || $this->link->connect_error) {
      $debug->error_fatal('DB Error - cannot connect to server error #' . $this->link->connect_errno, $this->link->connect_error);
    }


    $this->mysql_query("/*!40101 SET NAMES 'utf8' */")
      or $debug->error_fatal('DB error - cannot set names 1 error #' . $this->link->errno, $this->link->error);
    $this->mysql_query("SET NAMES 'utf8';")
      or $debug->error_fatal('DB error - cannot set names 2 error #' . $this->link->errno, $this->link->error);

    //mysql_select_db($settings['name']) or $debug->error_fatal('DB error - cannot find DB on server', $this->mysql_error());
    $this->mysql_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . self::DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';')
      or $debug->error_fatal('DB error - cannot set desired isolation level error #' . $this->link->errno, $this->link->error);

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
   * @param $query
   *
   * @return array|null
   */
  function mysql_fetch_assoc(&$query) {
    return mysqli_fetch_assoc($query);
  }
  function mysql_fetch_row(&$query) {
if(is_bool($query)) {
  pdump(debug_backtrace());
  pdie();
}
    return mysqli_fetch_row($query);
  }
  function mysql_real_escape_string($unescaped_string) {
    return mysqli_real_escape_string($this->link, $unescaped_string);
  }
  function mysql_close_link() {
    if(is_object($this->link)) {
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
}