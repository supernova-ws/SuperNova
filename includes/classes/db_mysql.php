<?php

/**
 * User: Gorlum
 * Date: 01.09.2015
 * Time: 15:58
 */
class db_mysql {
  const TRANSACTION_SERIALIZABLE = 'SERIALIZABLE';
  const TRANSACTION_REPEATABLE_READ = 'REPEATABLE READ';
  const TRANSACTION_READ_COMMITTED = 'READ COMMITTED';
  const TRANSACTION_READ_UNCOMMITTED = 'READ UNCOMMITTED';

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
  public $queryCount = 0;

  public $isWatching = false;

  public function __construct() {
  }

  public function load_db_settings() {
    $dbsettings = array();

    require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);

    $this->dbsettings = $dbsettings;
  }

  public function sn_db_connect($external_db_settings = null) {
    $this->db_disconnect();

    if (!empty($external_db_settings) && is_array($external_db_settings)) {
      $this->dbsettings = $external_db_settings;
    }

    if (empty($this->dbsettings)) {
      $this->load_db_settings();
    }

    // TODO - фатальные (?) ошибки на каждом шагу. Хотя - скорее Эксепшны
    if (!empty($this->dbsettings)) {
      $driver_name = empty($this->dbsettings['sn_driver']) ? 'db_mysql_v5' : $this->dbsettings['sn_driver'];
      $this->driver = new $driver_name();
      $this->db_prefix = $this->dbsettings['prefix'];

      $this->connected = $this->connected || $this->driver_connect();

      if ($this->connected) {
        $this->table_list = $this->db_get_table_list();
        // TODO Проверка на пустоту
      }
    } else {
      $this->connected = false;
    }

    return $this->connected;
  }

  protected function driver_connect() {
    if (!is_object($this->driver)) {
      classSupernova::$debug->error_fatal('DB Error - No driver for MySQL found!');
    }

    if (!method_exists($this->driver, 'mysql_connect')) {
      classSupernova::$debug->error_fatal('DB Error - WRONG MySQL driver!');
    }

    return $this->driver->mysql_connect($this->dbsettings);
  }

  public function db_disconnect() {
    if ($this->connected) {
      $this->connected = !$this->driver_disconnect();
      $this->connected = false;
    }

    return !$this->connected;
  }

  /**
   * @param string $query
   *
   * @return mixed|string
   */
  public function replaceTablePlaceholders($query) {
    $sql = $query;
    if (strpos($sql, '{{') !== false) {
      foreach ($this->table_list as $tableName) {
        $sql = str_replace("{{{$tableName}}}", $this->db_prefix . $tableName, $sql);
      }
    }

    return $sql;
  }

  /**
   * @param       $query
   * @param       $fetch
   */
  protected function logQuery($query, $fetch) {
    if (!classSupernova::$config->debug) {
      return;
    }

    $this->queryCount++;
    $arr = debug_backtrace();
    $file = end(explode('/', $arr[0]['file']));
    $line = $arr[0]['line'];
    classSupernova::$debug->add("<tr><th>Query {$this->queryCount}: </th><th>$query</th><th>{$file} @ {$line}</th><th>&nbsp;</th><th> " . ($fetch ? '+' : '&nbsp;') . " </th></tr>");
  }


  /**
   * @return string
   */
  public function queryTrace() {
    if (!defined('DEBUG_SQL_COMMENT') || constant('DEBUG_SQL_ERROR') !== true) {
      return '';
    }

    $backtrace = debug_backtrace();
    $sql_comment = classSupernova::$debug->compact_backtrace($backtrace, defined('DEBUG_SQL_COMMENT_LONG'));

    if (defined('DEBUG_SQL_ERROR') && constant('DEBUG_SQL_ERROR') === true) {
      classSupernova::$debug->add_to_array($sql_comment);
    }

    $sql_commented = implode("\r\n", $sql_comment);
    if (defined('DEBUG_SQL_ONLINE') && constant('DEBUG_SQL_ONLINE') === true) {
      classSupernova::$debug->warning($sql_commented, 'SQL Debug', LOG_DEBUG_SQL);
    }

    return $sql_commented;
  }

  /**
   * @param string $query
   * @param string $table
   * @param bool   $fetch
   * @param bool   $skip_query_check
   *
   * @return array|bool|mysqli_result|null
   */
  public function doquery($query, $table = '', $fetch = false, $skip_query_check = false) {
    if (!is_string($table)) {
      $fetch = $table;
    }

    if (!$this->connected) {
      $this->sn_db_connect();
    }

//    $stringQuery = $query instanceof DbSqlPrepare ? $query->query : $query;
    $stringQuery = $query;
    $stringQuery = trim($stringQuery);
    // You can't do it - 'cause you can break commented statement with line-end comments
    // $stringQuery = preg_replace("/\s+/", ' ', $stringQuery);

    $this->security_watch_user_queries($stringQuery);
    $this->security_query_check_bad_words($stringQuery, $skip_query_check);
    $this->logQuery($stringQuery, $fetch);

    $stringQuery = $this->replaceTablePlaceholders($stringQuery);

    $queryTrace = $this->queryTrace();

    $queryResult = null;
    try {
      $queryResult = $this->db_sql_query($stringQuery . DbSqlHelper::quoteComment($queryTrace));
      if (!$queryResult) {
        throw new Exception();
      }
    } catch (Exception $e) {
      classSupernova::$debug->error($this->db_error() . "<br />{$query}<br />", 'SQL Error');
    }

    if ($fetch) {
      $queryResult = $this->db_fetch($queryResult);
      // DO NOT CLOSE STATEMENT HERE TO MAKE STATEMENT CACHING WORK!
    }

    return $queryResult;
  }

  /**
   * @param string $query
   * @param bool   $skip_query_check
   *
   * @return array|null
   */
  public function doQueryFetch($query, $skip_query_check = false) {
    $queryResult = $this->doquery($query, '', false, $skip_query_check);

    return $this->db_fetch($queryResult);
  }

  /**
   * @param string $query
   * @param bool   $skip_query_check
   *
   * @return mixed|null
   */
  public function doQueryFetchValue($query, $skip_query_check = false) {
    $row = $this->doQueryFetch($query, $skip_query_check);

    return is_array($row) ? reset($row) : null;
  }

  /**
   * Returns iterator to iterate through mysqli_result
   *
   * @param string $query
   * @param bool   $skip_query_check
   *
   * return DbResultIterator
   *
   * @return DbEmptyIterator|DbMysqliResultIterator
   */
  public function doQueryIterator($query, $skip_query_check = false) {
    $queryResult = $this->doquery($query, '', false, $skip_query_check);

    if ($queryResult instanceof mysqli_result) {
      $result = new DbMysqliResultIterator($queryResult);
    } else {
      $result = new DbEmptyIterator();
    }

    return $result;
  }

  /**
   * @param DbQueryConstructor $stmt
   * @param bool               $skip_query_check
   */
  public function doStmtLockAll($stmt, $skip_query_check = false) {
    $this->doquery(
      $stmt
        ->select()
        ->field(1)
        ->setForUpdate()
        ->__toString(),
      '',
      false,
      $skip_query_check
    );
  }

  // TODO Заменить это на новый логгер
  protected function security_watch_user_queries($query) {
    global $user;

    if (
      !$this->isWatching // Not already watching
      && !empty(classSupernova::$config->game_watchlist_array) // There is some players in watchlist
      && in_array($user['id'], classSupernova::$config->game_watchlist_array) // Current player is in watchlist
      && !preg_match('/^(select|commit|rollback|start transaction)/i', $query) // Current query should be watched
    ) {
      $this->isWatching = true;
      $msg = "\$query = \"{$query}\"\n\r";
      if (!empty($_POST)) {
        $msg .= "\n\r" . dump($_POST, '$_POST');
      }
      if (!empty($_GET)) {
        $msg .= "\n\r" . dump($_GET, '$_GET');
      }
      classSupernova::$debug->warning($msg, "Watching user {$user['id']}", 399, array('base_dump' => true));
      $this->isWatching = false;
    }
  }


  public function security_query_check_bad_words($query, $skip_query_check = false) {
    if ($skip_query_check) {
      return;
    }

    global $user, $dm_change_legit, $mm_change_legit;

    switch (true) {
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
  public function db_get_table_list($prefixed_only = true) {
    $query = $this->mysql_get_table_list();

    $prefix_length = strlen($this->db_prefix);

    $tl = array();
    while ($row = $this->db_fetch($query)) {
      foreach ($row as $table_name) {
        if (strpos($table_name, $this->db_prefix) === 0) {
          $table_name = substr($table_name, $prefix_length);
        } elseif ($prefixed_only) {
          continue;
        }
        // $table_name = str_replace($db_prefix, '', $table_name);
        $tl[$table_name] = $table_name;
      }
    }

    return $tl;
  }

  /**
   * @param string $statement
   *
   * @return bool|mysqli_stmt
   */
  public function db_prepare($statement) {
    $microtime = microtime(true);
    $result = $this->driver->mysql_prepare($statement);
    $this->time_mysql_total += microtime(true) - $microtime;

    return $result;
  }


  /**
   * L1 perform the query
   *
   * @param $query_string
   *
   * @return bool|mysqli_result
   */
  public function db_sql_query($query_string) {
    $microtime = microtime(true);
    $result = $this->driver->mysql_query($query_string);
    $this->time_mysql_total += microtime(true) - $microtime;

    return $result;
  }

  /**
   * L1 fetch assoc array
   *
   * @param mysqli_result $query
   *
   * @return array|null
   */
  public function db_fetch(&$query) {
    $microtime = microtime(true);
    $result = $this->driver->mysql_fetch_assoc($query);
    $this->time_mysql_total += microtime(true) - $microtime;

    return $result;
  }

  public function db_fetch_row(&$query) {
    return $this->driver->mysql_fetch_row($query);
  }

  public function db_escape($unescaped_string) {
    return $this->driver->mysql_real_escape_string($unescaped_string);
  }

  public function driver_disconnect() {
    return $this->driver->mysql_close_link();
  }

  public function db_error() {
    return $this->driver->mysql_error();
  }

  public function db_insert_id() {
    return $this->driver->mysql_insert_id();
  }

  public function db_num_rows(&$result) {
    return $this->driver->mysql_num_rows($result);
  }

  public function db_affected_rows() {
    return $this->driver->mysql_affected_rows();
  }

  /**
   * @return string
   */
  public function db_get_client_info() {
    return $this->driver->mysql_get_client_info();
  }

  /**
   * @return string
   */
  public function db_get_server_info() {
    return $this->driver->mysql_get_server_info();
  }

  /**
   * @return string
   */
  public function db_get_host_info() {
    return $this->driver->mysql_get_host_info();
  }

  public function db_get_server_stat() {
    $result = array();

    $status = explode('  ', $this->driver->mysql_stat());
    foreach ($status as $value) {
      $row = explode(': ', $value);
      $result[$row[0]] = $row[1];
    }

    return $result;
  }

  /**
   * @return array
   * @throws Exception
   */
  public function db_core_show_status() {
    $result = array();

    $query = $this->db_sql_query('SHOW STATUS;');
    if (is_bool($query)) {
      throw new Exception('Result of SHOW STATUS command is boolean - which should never happen. Connection to DB is lost?');
    }
    while ($row = db_fetch($query)) {
      $result[$row['Variable_name']] = $row['Value'];
    }

    return $result;
  }

  public function mysql_get_table_list() {
    return $this->db_sql_query('SHOW TABLES;');
  }

  public function mysql_get_innodb_status() {
    return $this->db_sql_query('SHOW ENGINE INNODB STATUS;');
  }

}
