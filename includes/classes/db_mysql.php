<?php

/**
 * User: Gorlum
 * Date: 01.09.2015
 * Time: 15:58
 */
class db_mysql {
  const DB_MYSQL_TRANSACTION_SERIALIZABLE = 'SERIALIZABLE';
  const DB_MYSQL_TRANSACTION_REPEATABLE_READ = 'REPEATABLE READ';
  const DB_MYSQL_TRANSACTION_READ_COMMITTED = 'READ COMMITTED';
  const DB_MYSQL_TRANSACTION_READ_UNCOMMITTED = 'READ UNCOMMITTED';

  /**
   * Статус соеднения с MySQL
   *
   * @var bool
   */
  public $connected = false;
  /**
   * Префикс названий таблиц в БД
   *
   * @var string
   */
  public $db_prefix = '';
  /**
   * Список таблиц в БД
   *
   * @var array
   */
  public $table_list = array();

  /**
   * Настройки БД
   *
   * @var array
   */
  protected $dbsettings = array();
  /**
   * Драйвер для прямого обращения к MySQL
   *
   * @var db_mysql_v5 $driver
   */
  public $driver = null;

  /**
   * Общее время запросов
   *
   * @var float $time_mysql_total
   */
  public $time_mysql_total = 0.0;

  /**
   * Amount of queries on this DB
   *
   * @var int
   */
  public $numqueries = 0;

  protected $isWatching = false;

  public function __construct() {
  }

  function load_db_settings() {
    $dbsettings = array();

    require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);

    $this->dbsettings = $dbsettings;
  }

  function sn_db_connect($external_db_settings = null) {
    $this->db_disconnect();

    if(!empty($external_db_settings) && is_array($external_db_settings)) {
      $this->dbsettings = $external_db_settings;
    }

    if(empty($this->dbsettings)) {
      $this->load_db_settings();
    }

    // TODO - фатальные (?) ошибки на каждом шагу. Хотя - скорее Эксепшны
    if(!empty($this->dbsettings)) {
      $driver_name = empty($this->dbsettings['sn_driver']) ? 'db_mysql_v5' : $this->dbsettings['sn_driver'];
      $this->driver = new $driver_name();
      $this->db_prefix = $this->dbsettings['prefix'];

      $this->connected = $this->connected || $this->driver_connect();

      if($this->connected) {
        $this->table_list = $this->db_get_table_list();
        // TODO Проверка на пустоту
      }
    } else {
      $this->connected = false;
    }

    return $this->connected;
  }

  function driver_connect() {
    global $debug;

    if(!is_object($this->driver)) {
      $debug->error_fatal('DB Error - No driver for MySQL found!');
    }

    if(!method_exists($this->driver, 'mysql_connect')) {
      $debug->error_fatal('DB Error - WRONG MySQL driver!');
    }

    return $this->driver->mysql_connect($this->dbsettings);
  }

  function db_disconnect() {
    if($this->connected) {
      $this->connected = !$this->driver_disconnect();
      $this->connected = false;
    }

    return !$this->connected;
  }

  /**
   * @param $query
   *
   * @return mixed
   */
  public function replaceTablePlaceholders($query) {
    $sql = $query;
    if(strpos($sql, '{{') !== false) {
      foreach($this->table_list as $tableName) {
        $sql = str_replace("{{{$tableName}}}", $this->db_prefix . $tableName, $sql);
      }
    }

    return $sql;
  }

  /**
   * @param       $query
   * @param       $fetch
   */
  public function logQuery($query, $fetch) {
    if(!classSupernova::$config->debug) {
      return;
    }

    $this->numqueries++;
    $arr = debug_backtrace();
    $file = end(explode('/', $arr[0]['file']));
    $line = $arr[0]['line'];
    classSupernova::$debug->add("<tr><th>Query {$this->numqueries}: </th><th>$query</th><th>{$file} @ {$line}</th><th>&nbsp;</th><th> " . ($fetch ? '+' : '&nbsp;') . " </th></tr>");
  }


  /**
   * @param $sql
   *
   * @return void
   */
  public function commentQuery(&$sql) {
    if(!defined('DEBUG_SQL_COMMENT')) {
      return;
    }
    $backtrace = debug_backtrace();
    $sql_comment = classSupernova::$debug->compact_backtrace($backtrace, defined('DEBUG_SQL_COMMENT_LONG'));

    $sql_commented = '/* ' . implode("<br />", $sql_comment) . '<br /> */ ' . preg_replace("/\s+/", ' ', $sql);
    if(defined('DEBUG_SQL_ONLINE')) {
      classSupernova::$debug->warning($sql_commented, 'SQL Debug', LOG_DEBUG_SQL);
    }

    if(defined('DEBUG_SQL_ERROR')) {
      array_unshift($sql_comment, preg_replace("/\s+/", ' ', $sql));
      classSupernova::$debug->add_to_array($sql_comment);
    }

    $sql = $sql_commented;
  }

  public function doquery($query, $table = '', $fetch = false, $skip_query_check = false) {
    if(!is_string($table)) {
      $fetch = $table;
    }

    if(!$this->connected) {
      $this->sn_db_connect();
    }

    $query = trim($query);
    $this->security_watch_user_queries($query);
    !$skip_query_check ? $this->security_query_check_bad_words($query) : false;
    $this->logQuery($query, $fetch);

    $sql = $this->replaceTablePlaceholders($query);
    $this->commentQuery($sql);
    !($sqlquery = $this->db_sql_query($sql)) ? classSupernova::$debug->error(db_error() . "<br />$sql<br />", 'SQL Error') : false;

    return $fetch ? $this->db_fetch($sqlquery) : $sqlquery;
  }


  // TODO Заменить это на новый логгер
  function security_watch_user_queries($query) {
    global $user;

    if(
      !$this->isWatching // Not already watching
      && !empty(classSupernova::$config->game_watchlist_array) // There is some players in watchlist
      && in_array($user['id'], classSupernova::$config->game_watchlist_array) // Current player is in watchlist
      && !preg_match('/^(select|commit|rollback|start transaction)/i', $query) // Current query should be watched
    ) {
      $this->isWatching = true;
      $msg = "\$query = \"{$query}\"\n\r";
      if(!empty($_POST)) {
        $msg .= "\n\r" . dump($_POST, '$_POST');
      }
      if(!empty($_GET)) {
        $msg .= "\n\r" . dump($_GET, '$_GET');
      }
      classSupernova::$debug->warning($msg, "Watching user {$user['id']}", 399, array('base_dump' => true));
      $this->isWatching = false;
    }
  }


  function security_query_check_bad_words($query) {
    global $user, $dm_change_legit, $mm_change_legit;

    switch(true) {
      case stripos($query, 'RUNCATE TABL') != false:
      case stripos($query, 'ROP TABL') != false:
      case stripos($query, 'ENAME TABL') != false:
      case stripos($query, 'REATE DATABAS') != false:
      case stripos($query, 'REATE TABL') != false:
      case stripos($query, 'ET PASSWOR') != false:
      case stripos($query, 'EOAD DAT') != false:
      case stripos($query, 'RPG_POINTS') != false && stripos(trim($query), 'UPDATE ') === 0 && !$dm_change_legit:
      case stripos($query, 'METAMATTER') != false && stripos(trim($query), 'UPDATE ') === 0 && !$mm_change_legit:
      case stripos($query, 'AUTHLEVEL') != false && $user['authlevel'] < 3 && stripos($query, 'SELECT') !== 0:
        $report = "Hacking attempt (" . date("d.m.Y H:i:s") . " - [" . time() . "]):\n";
        $report .= ">Database Inforamation\n";
        $report .= "\tID - " . $user['id'] . "\n";
        $report .= "\tUser - " . $user['username'] . "\n";
        $report .= "\tAuth level - " . $user['authlevel'] . "\n";
        $report .= "\tAdmin Notes - " . $user['adminNotes'] . "\n";
        $report .= "\tCurrent Planet - " . $user['current_planet'] . "\n";
        $report .= "\tUser IP - " . $user['user_lastip'] . "\n";
        $report .= "\tUser IP at Reg - " . $user['ip_at_reg'] . "\n";
        $report .= "\tUser Agent- " . $_SERVER['HTTP_USER_AGENT'] . "\n";
        $report .= "\tCurrent Page - " . $user['current_page'] . "\n";
        $report .= "\tRegister Time - " . $user['register_time'] . "\n";
        $report .= "\n";

        $report .= ">Query Information\n";
        $report .= "\tQuery - " . $query . "\n";
        $report .= "\n";

        $report .= ">\$_SERVER Information\n";
        $report .= "\tIP - " . $_SERVER['REMOTE_ADDR'] . "\n";
        $report .= "\tHost Name - " . $_SERVER['HTTP_HOST'] . "\n";
        $report .= "\tUser Agent - " . $_SERVER['HTTP_USER_AGENT'] . "\n";
        $report .= "\tRequest Method - " . $_SERVER['REQUEST_METHOD'] . "\n";
        $report .= "\tCame From - " . $_SERVER['HTTP_REFERER'] . "\n";
        $report .= "\tPage is - " . $_SERVER['SCRIPT_NAME'] . "\n";
        $report .= "\tUses Port - " . $_SERVER['REMOTE_PORT'] . "\n";
        $report .= "\tServer Protocol - " . $_SERVER['SERVER_PROTOCOL'] . "\n";

        $report .= "\n--------------------------------------------------------------------------------------------------\n";

        $fp = fopen(SN_ROOT_PHYSICAL . 'badqrys.txt', 'a');
        fwrite($fp, $report);
        fclose($fp);

        $message = 'Привет, я не знаю то, что Вы пробовали сделать, но команда, которую Вы только послали базе данных, не выглядела очень дружественной и она была заблокированна.<br /><br />Ваш IP, и другие данные переданны администрации сервера. Удачи!.';
        die($message);
      break;
    }
  }

  /**
   * @param bool $prefixed_only
   *
   * @return array
   */
  function db_get_table_list($prefixed_only = true) {
    $query = $this->mysql_get_table_list();

    $prefix_length = strlen($this->db_prefix);

    $tl = array();
    while($row = $this->db_fetch($query)) {
      foreach($row as $table_name) {
        if(strpos($table_name, $this->db_prefix) === 0) {
          $table_name = substr($table_name, $prefix_length);
        } elseif($prefixed_only) {
          continue;
        }
        // $table_name = str_replace($db_prefix, '', $table_name);
        $tl[$table_name] = $table_name;
      }
    }

    return $tl;
  }

  function mysql_get_table_list() {
    return $this->db_sql_query('SHOW TABLES;');
  }

  function mysql_get_innodb_status() {
    return $this->db_sql_query('SHOW ENGINE INNODB STATUS;');
  }


  /**
   * L1 perform the query
   *
   * @param $query_string
   *
   * @return bool|mysqli_result
   */
  function db_sql_query($query_string) {
    $microtime = microtime(true);
    $result = $this->driver->mysql_query($query_string);
    $this->time_mysql_total += microtime(true) - $microtime;

    return $result;
  }

  /**
   * L1 fetch assoc array
   *
   * @param $query
   *
   * @return array|null
   */
  function db_fetch(&$query) {
    $microtime = microtime(true);
    $result = $this->driver->mysql_fetch_assoc($query);
    $this->time_mysql_total += microtime(true) - $microtime;

    return $result;
  }

  function db_fetch_row(&$query) {
    return $this->driver->mysql_fetch_row($query);
  }

  function db_escape($unescaped_string) {
    return $this->driver->mysql_real_escape_string($unescaped_string);
  }

  function driver_disconnect() {
    return $this->driver->mysql_close_link();
  }

  function db_error() {
    return $this->driver->mysql_error();
  }

  function db_insert_id() {
    return $this->driver->mysql_insert_id();
  }

  function db_num_rows(&$result) {
    return $this->driver->mysql_num_rows($result);
  }

  function db_affected_rows() {
    return $this->driver->mysql_affected_rows();
  }

  /**
   * @return string
   */
  function db_get_client_info() {
    return $this->driver->mysql_get_client_info();
  }

  /**
   * @return string
   */
  function db_get_server_info() {
    return $this->driver->mysql_get_server_info();
  }

  /**
   * @return string
   */
  function db_get_host_info() {
    return $this->driver->mysql_get_host_info();
  }

  function db_get_server_stat() {
    $result = array();

    $status = explode('  ', $this->driver->mysql_stat());
    foreach($status as $value) {
      $row = explode(': ', $value);
      $result[$row[0]] = $row[1];
    }

    return $result;
  }

  function db_core_show_status() {
    $result = array();

    $query = doquery('SHOW STATUS;');
    while($row = db_fetch($query)) {
      $result[$row['Variable_name']] = $row['Value'];
    }

    return $result;
  }

}
