<?php

namespace DBAL;

use mysqli_result;
use SN;

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
   * DB schemes
   *
   * @var Schema|null $schema
   */
  protected static $schema = null;

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
  public $dbName = '';
  /**
   * Настройки БД
   *
   * @var array
   */
  protected $dbsettings = [];
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
   * @var bool $inTransaction
   */
  protected $inTransaction = false;

  /**
   * DBAL\db_mysql constructor.
   *
   * @param \Core\GlobalContainer $gc
   */
  public function __construct($gc) {
//    $this->transaction = new \DBAL\DbTransaction($gc, $this);
//    $this->snCache = new $gc->snCacheClass($gc, $this);
//    $this->operator = new DbRowDirectOperator($this);
  }

  /**
   * @return Schema
   */
  public function schema() {
    if (!isset(self::$schema)) {
      self::$schema = new Schema($this);
    }

    return self::$schema;
  }

  public function load_db_settings() {
    $dbsettings = array();

    require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);

    $this->setDbSettings($dbsettings);
  }

  public function sn_db_connect($external_db_settings = null) {
    $this->db_disconnect();

    if (!empty($external_db_settings) && is_array($external_db_settings)) {
      $this->setDbSettings($external_db_settings);
    }

    if (empty($this->dbsettings)) {
      $this->load_db_settings();
    }

    // TODO - фатальные (?) ошибки на каждом шагу. Хотя - скорее Эксепшны
    if (!empty($this->dbsettings)) {
      $driver_name = 'DBAL\\' . (empty($this->dbsettings['sn_driver']) ? 'db_mysql_v5' : $this->dbsettings['sn_driver']);
      $this->driver = new $driver_name();
      $this->db_prefix = $this->dbsettings['prefix'];

      $this->connected = $this->connected || $this->driver_connect();

      if ($this->connected && empty($this->schema()->getSnTables())) {
        die('DB error - cannot find any table. Halting...');
      }

      $this->doQueryFast('SET SESSION TRANSACTION ISOLATION LEVEL ' . self::DB_MYSQL_TRANSACTION_SERIALIZABLE);
    } else {
      $this->connected = false;
    }

    return $this->connected;
  }

  public function driver_connect() {
    global $debug;

    if (!is_object($this->driver)) {
      $debug->error_fatal('DB Error - No driver for MySQL found!');
    }

    if (!method_exists($this->driver, 'mysql_connect')) {
      $debug->error_fatal('DB Error - WRONG MySQL driver!');
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
   * @param int    $errno
   * @param string $errstr
   * @param string $errfile
   * @param int    $errline
   * @param array  $errcontext
   */
  public function handlerQueryWarning($errno, $errstr, $errfile, $errline, $errcontext) {
    static $alreadyHandled;

    // Error was suppressed with the @-operator
    if (0 === error_reporting()) {
      return false;
    }

    if (!$alreadyHandled) {
      print(SN_TIME_SQL . '<br />Server is busy. Please try again in several minutes...<br />Сервер занят. Попробуйте снова через несколько минут...<br />Server zanyat. Poprobujte snova cherez neskolko minut...');
      $alreadyHandled = true;
    }

    return true;
  }

  public function prefixReplace($sql) {
    if (strpos($sql, '{{') !== false) {
      foreach ($this->schema()->getSnTables() as $tableName) {
        $sql = str_replace("{{{$tableName}}}", $this->db_prefix . $tableName, $sql);
      }
    }

    return $sql;
  }

  public function doquery($query, $fetch = false, $skip_query_check = false) {
    global $numqueries, $debug, $config;

    if (!$this->connected) {
      $this->sn_db_connect();
    }

    $query = trim($query);
    $this->security_watch_user_queries($query);
    $skip_query_check or $this->security_query_check_bad_words($query);

    $sql = $this->prefixReplace($query);
//    $sql = $query;
//    if (strpos($sql, '{{') !== false) {
//      foreach ($this->schema()->getSnTables() as $tableName) {
//        $sql = str_replace("{{{$tableName}}}", $this->db_prefix . $tableName, $sql);
//      }
//    }

    if ($config->debug) {
      $numqueries++;
      $arr = debug_backtrace();
      $file = end(explode('/', $arr[0]['file']));
      $line = $arr[0]['line'];
      $debug->add("<tr><th>Query $numqueries: </th><th>$query</th><th>$file($line)</th><th>&nbsp;</th><th>$fetch</th></tr>");
    }

    if (defined('DEBUG_SQL_COMMENT')) {
      $backtrace = debug_backtrace();
      $sql_comment = $debug->compact_backtrace($backtrace, defined('DEBUG_SQL_COMMENT_LONG'));

      $sql_commented = '/* ' . implode("<br />", $sql_comment) . '<br /> */ ' . preg_replace("/\s+/", ' ', $sql);
      if (defined('DEBUG_SQL_ONLINE')) {
        $debug->warning($sql_commented, 'SQL Debug', LOG_DEBUG_SQL);
      }

      if (defined('DEBUG_SQL_ERROR')) {
        array_unshift($sql_comment, preg_replace("/\s+/", ' ', $sql));
        $debug->add_to_array($sql_comment);
        // $debug->add_to_array($sql_comment . preg_replace("/\s+/", ' ', $sql));
      }
      $sql = $sql_commented;
    }

    set_error_handler([$this, 'handlerQueryWarning']);
    $sqlquery = $this->db_sql_query($sql);
    if(!$sqlquery) {
      $debug->error(db_error() . "<br />$sql<br />", 'SQL Error');
    }
    restore_error_handler();

    return $fetch ? $this->db_fetch($sqlquery) : $sqlquery;
  }

  public function doQueryAndFetch($query) {
    return $this->doquery($query, true);
  }

  public function doQueryFast($query, $fetch = false) {
    $sql = $this->prefixReplace($query);

    set_error_handler([$this, 'handlerQueryWarning']);
    $sqlquery = $this->db_sql_query($sql) or SN::$debug->error(db_error() . "<br />$sql<br />", 'SQL Error');
    restore_error_handler();

    return $fetch ? $this->db_fetch($sqlquery) : $sqlquery;
  }

  /**
   * @param string $query
   * @param bool   $skip_query_check
   *
   * @return DbMysqliResultIterator
   */
  public function selectIterator($query, $skip_query_check = false) {
    return new DbMysqliResultIterator($this->doquery($query, false, $skip_query_check));
  }

  /**
   * @param string $query
   * @param bool   $skip_query_check
   *
   * @return int|null
   */
  public function selectValue($query, $skip_query_check = false) {
    $row = $this->doquery($query, true, $skip_query_check);

    return !empty($row) ? intval(reset($row)) : null;
  }

  /**
   * @param \DBAL\DbQuery $dbQuery
   *
   * @return array|null
   */
  public function dbqSelectAndFetch(\DBAL\DbQuery $dbQuery) {
    return $this->doQueryAndFetch($dbQuery->select());
  }


  public function security_watch_user_queries($query) {
    // TODO Заменить это на новый логгер
    global $config, $is_watching, $user, $debug;

    if (!$is_watching && $config->game_watchlist_array && in_array($user['id'], $config->game_watchlist_array)) {
      if (!preg_match('/^(select|commit|rollback|start transaction)/i', $query)) {
        $is_watching = true;
        $msg = "\$query = \"{$query}\"\n\r";
        if (!empty($_POST)) {
          $msg .= "\n\r" . dump($_POST, '$_POST');
        }
        if (!empty($_GET)) {
          $msg .= "\n\r" . dump($_GET, '$_GET');
        }
        $debug->warning($msg, "Watching user {$user['id']}", 399, array('base_dump' => true));
        $is_watching = false;
      }
    }
  }


  public function security_query_check_bad_words($query) {
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

  public function mysql_get_table_list() {
    return $this->db_sql_query('SHOW TABLES;');
  }

  public function mysql_get_innodb_status() {
    return $this->db_sql_query('SHOW ENGINE INNODB STATUS;');
  }

  /**
   * @param string $tableName_unsafe
   *
   * @return array[]
   */
  public function mysql_get_fields($tableName_unsafe) {
    $result = [];

    $prefixedTableName_safe = $this->db_escape($this->db_prefix . $tableName_unsafe);
    $q1 = $this->db_sql_query("SHOW FULL COLUMNS FROM `{$prefixedTableName_safe}`;");
    while ($r1 = db_fetch($q1)) {
      $dbf = new DbFieldDescription();
      $dbf->fromMySqlDescription($r1);

      $result[$r1['Field']] = $dbf;
    }

    return $result;
  }

  /**
   * @param string $tableName_unsafe
   *
   * @return DbIndexDescription[]
   */
  public function mysql_get_indexes($tableName_unsafe) {
    /**
     * @var DbIndexDescription[] $result
     */
    $result = [];

    $prefixedTableName_safe = $this->db_escape($this->db_prefix . $tableName_unsafe);
    $q1 = $this->db_sql_query("SHOW INDEX FROM {$prefixedTableName_safe};");
    while ($r1 = db_fetch($q1)) {
      $indexName = $r1['Key_name'];
      if(empty($result[$indexName])) {
        $result[$indexName] = new DbIndexDescription();
      }
      $result[$indexName]->addField($r1);
    }

    return $result;
  }

  /**
   * @param string $tableName_unsafe
   *
   * @return array[]
   */
  public function mysql_get_constraints($tableName_unsafe) {
    $result = [];

    $prefixedTableName_safe = $this->db_escape($this->db_prefix . $tableName_unsafe);

    $q1 = $this->db_sql_query("SELECT * FROM `information_schema`.`KEY_COLUMN_USAGE` WHERE `TABLE_SCHEMA` = '" . db_escape(SN::$db_name) . "' AND `TABLE_NAME` = '{$prefixedTableName_safe}' AND `REFERENCED_TABLE_NAME` IS NOT NULL;");
    while ($r1 = db_fetch($q1)) {
      $indexName = $r1['CONSTRAINT_NAME'];

      $table_referenced = str_replace($this->db_prefix, '', $r1['REFERENCED_TABLE_NAME']);

      $result[$indexName]['name'] = $indexName;
      $result[$indexName]['signature'][] = "{$r1['COLUMN_NAME']}=>{$table_referenced}.{$r1['REFERENCED_COLUMN_NAME']}";
      $r1['REFERENCED_TABLE_NAME'] = $table_referenced;
      $r1['TABLE_NAME'] = $tableName_unsafe;
      $result[$indexName]['fields'][$r1['COLUMN_NAME']] = $r1;
    }

    foreach ($result as &$constraint) {
      $constraint['signature'] = implode(',', $constraint['signature']);
    }

    return $result;
  }


  public function db_sql_query($query_string) {
    $microtime = microtime(true);
    $result = $this->driver->mysql_query($query_string);
    $this->time_mysql_total += microtime(true) - $microtime;

    return $result;
//    return $this->driver->mysql_query($query_string);
  }

  /**
   * @param mysqli_result $query_result
   *
   * @return array|null
   */
  public function db_fetch(&$query_result) {
    $microtime = microtime(true);
    $result = $this->driver->mysql_fetch_assoc($query_result);
    $this->time_mysql_total += microtime(true) - $microtime;

    return $result;
//    return $this->driver->mysql_fetch_assoc($query);
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

  /**
   * @return int|string
   */
  public function db_insert_id() {
    return $this->driver->mysql_insert_id();
  }

  public function db_num_rows(&$result) {
    return $this->driver->mysql_num_rows($result);
  }

  public function db_affected_rows() {
    return $this->driver->mysql_affected_rows();
  }

  public function db_get_client_info() {
    return $this->driver->mysql_get_client_info();
  }

  public function db_get_server_info() {
    return $this->driver->mysql_get_server_info();
  }

  public function db_get_host_info() {
    return $this->driver->mysql_get_host_info();
  }

  public function db_get_server_stat() {
    return $this->driver->mysql_stat();
  }

  /**
   * @param array $dbSettings
   */
  public function setDbSettings($dbSettings) {
    $this->dbsettings = $dbSettings;
    $this->dbName = $this->dbsettings['name'];

    return $this;
  }

  public function getDbSettings() {
    return $this->dbsettings;
  }

  public function transactionStart($level = '') {
    $this->inTransaction = true;

    $level ? $this->db_sql_query("SET TRANSACTION ISOLATION LEVEL {$level};") : false;

    $this->db_sql_query('START TRANSACTION;');
  }

  public function transactionCommit() {
    $this->db_sql_query('COMMIT;');
    $this->inTransaction = false;
  }

  public function transactionRollback() {
    $this->db_sql_query('ROLLBACK;');
    $this->inTransaction = false;
  }

  /**
   * Check if transaction started
   *
   * @return bool
   */
  public function transactionCheck() {
    return $this->inTransaction;
  }

  /**
   * Wrap callback in transaction
   *
   * @param callable $callback
   * @param string   $level
   *
   * @return mixed
   */
  public function transactionWrap($callback, $level = '') {
    $this->transactionStart($level);
    $result = $callback();
    $this->transactionCommit();

    return $result;
  }
}
