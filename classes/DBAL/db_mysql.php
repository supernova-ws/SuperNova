<?php

/** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpMissingParamTypeInspection */

/** @noinspection PhpRedundantOptionalArgumentInspection */
/** @noinspection PhpDeprecationInspection */
/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

namespace DBAL;

use classConfig;
use Core\GlobalContainer;
use debug;
use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use PDO;
use PDOStatement;
use SN;

/**
 * User: Gorlum
 * Date: 01.09.2015
 * Time: 15:58
 */
class db_mysql {

  const TRANSACTION_LEVEL_SERIALIZABLE = 'SERIALIZABLE';
  const TRANSACTION_LEVEL_REPEATABLE_READ = 'REPEATABLE READ';
  const TRANSACTION_LEVEL_READ_COMMITTED = 'READ COMMITTED';
  const TRANSACTION_LEVEL_READ_UNCOMMITTED = 'READ UNCOMMITTED';

  const TRANSACTION_LEVELS_ALLOWED = [
    self::TRANSACTION_LEVEL_SERIALIZABLE,
    self::TRANSACTION_LEVEL_REPEATABLE_READ,
    self::TRANSACTION_LEVEL_READ_COMMITTED,
    self::TRANSACTION_LEVEL_READ_UNCOMMITTED,
  ];
  const DB_TRANSACTION_WHATEVER = false;
  const DB_TRANSACTION_SHOULD_BE = true;
  const DB_TRANSACTION_SHOULD_NOT_BE = null;
  public static $transaction_id = 0;
  public static $db_in_transaction = false;
  public static $transactionDepth = 0;

  const TABLE_ID_FIELD = [
    'fleets' => 'fleet_id',
  ];

  // Order in which tables should be locked
  const TABLE_LOCK_ORDER = [
    'users',
    'planets',
    'que',
    'unit',
    'fleets',
  ];

  /**
   * DB schemes
   *
   * @var Schema|null $schema
   */
  protected $schema = null;

  /**
   * Статус соединения с MySQL
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
//  /**
//   * Драйвер для прямого обращения к MySQL
//   *
//   * @var db_mysql_v5 $driver
//   */
//  public $driver = null;

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

  private ?PDO $pdo;
  /** @noinspection PhpPropertyOnlyWrittenInspection */
  private Capsule $capsule;


  /**
   * DBAL\db_mysql constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct($gc = null) {
//    $this->transaction = new \DBAL\DbTransaction($gc, $this);
//    $this->snCache = new $gc->snCacheClass($gc, $this);
//    $this->operator = new DbRowDirectOperator($this);
  }

  /**
   * @return Schema
   */
  public function schema() {
    if (!isset($this->schema)) {
      $this->schema = new Schema($this);
    }

    return $this->schema;
  }

  public function load_db_settings() {
    $dbsettings = array();

    require(SN_CONFIG_PATH);

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

    // TODO - фатальные (?) ошибки на каждом шагу
    try {
      if (!empty($this->dbsettings)) {
        // $driver_name = 'DBAL\\' . (empty($this->dbsettings['sn_driver']) ? 'db_mysql_v5' : $this->dbsettings['sn_driver']);
        // $this->driver = new $driver_name();
        $this->db_prefix = $this->dbsettings['prefix'];

        $this->connected = $this->connected || $this->mysql_connect_driver($this->dbsettings);

        if ($this->connected && empty($this->schema()->getSnTables())) {
          die('DB error - cannot find any table. Halting...');
        }

        $this->doQueryFast('SET SESSION TRANSACTION ISOLATION LEVEL ' . self::TRANSACTION_LEVEL_SERIALIZABLE);
      } else {
        $this->connected = false;
      }
    } catch (Exception $e) {
      $this->connected = false;

      if ($e->getCode() == 1044) {
        die('Access denied for specified DB user to specified database. Check DB settings in game and user privileges on DB server');
      } else {
        die('Error happens while connecting to DB. Check DB settings in game and DB accessibility');
      }
    }

    return $this->connected;
  }

  public function mysql_connect_driver($settings) {
    global $debug;
    /** @var ?debug $debug */

    static $need_keys = array('server', 'user', 'pass', 'name', 'prefix');

    if ($this->connected) {
      return true;
    }

    if (empty($settings) || !is_array($settings) || array_intersect($need_keys, array_keys($settings)) != $need_keys) {
      $debug->error_fatal('There is miss-configuration in your config.php. Check it again');
    }

    try {
//      $this->pdo = new PDO(
//        "mysql:host={$settings['server']};dbname={$settings['name']};charset=utf8mb4",
//        $settings['user'],
//        $settings['pass'],
//        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
//      );

      $capsule = new Capsule;

      // Подключение к базе
      $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => $settings['server'],
        'database'  => $settings['name'],
        'username'  => $settings['user'],
        'password'  => $settings['pass'],
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => $settings['prefix'],
      ]);

      // Делаем глобально доступным
      $capsule->setAsGlobal();
      $capsule->bootEloquent();

      $this->capsule = $capsule;

      $this->pdo = Capsule::connection()->getPdo();

      $this->db_sql_query("/*!40101 SET NAMES 'utf8' */");
      $this->db_sql_query("SET NAMES 'utf8';");
      $this->db_sql_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . self::TRANSACTION_LEVEL_SERIALIZABLE . ';');
    } catch (Exception $e) {
      $debug->error_fatal('DB Error - cannot connect to server error #' . $e->getCode(), $e->getMessage());
    }

    $this->connected = true;

    return true;
  }

  public function db_disconnect() {
    if ($this->connected) {
      /** @noinspection PhpFieldImmediatelyRewrittenInspection */
      $this->connected = !$this->driver_disconnect();
      $this->connected = false;
    }

    return !$this->connected;
  }

  /**
   * @param int    $errno
   * @param string $errStr
   * @param string $errFile
   * @param int    $errLine
   * @param array  $errContext
   *
   * @noinspection PhpUnusedParameterInspection
   */
  public function handlerQueryWarning($errno, $errStr, $errFile, $errLine, $errContext) {
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

  /**
   * @param             $query
   * @param bool        $skip_query_check
   *
   * @return bool|PDOStatement
   */
  public function doOnlyQuery($query, $skip_query_check = false) {
    /**
     * @var debug       $debug
     * @var classConfig $config
     */
    global $numQueries, $debug, $config;

    static $exclude_functions;
    if(empty($exclude_functions)) {
      $exclude_functions = $debug->getExcludedFunctionNames();
    }

    if (!$this->connected) {
      $this->sn_db_connect();
    }

    $query = trim($query);
    $this->security_watch_user_queries($query);
    $skip_query_check or $this->security_query_check_bad_words($query);

    $sql = $this->prefixReplace($query);

    if ($config->debug ?? null) {
      $numQueries++;
      $arr   = debug_backtrace();
      while(
        !empty($arr)
        && (in_array(basename($arr[0]['file']), ['db.php', 'db_mysql.php', 'db_queries.php', 'DbQuery.php'])
//        || in_array(basename($arr[0]['function']), $exclude_functions)
        )
      ) {
        array_shift($arr);
      }

      $array = explode('/', $arr[0]['file']);
      $file  = end($array);
      $line  = $arr[0]['line'];
      $debug->add($numQueries, $query, $file, $line);
//      $debug->add($numQueries, str_replace("\n", '<br>', $debug->comment_query(debug_backtrace(), $sql)), $file, $line);
    }

    if (defined('DEBUG_SQL_COMMENT')) {
      $sql = $debug->comment_query(debug_backtrace(), $sql);
    }

    set_error_handler([$this, 'handlerQueryWarning']);
    $sqlquery = $this->db_sql_query($sql);
    if (!$sqlquery) {
      $debug->error(SN::$db->db_error() . "\n$sql\n", 'SQL Error');
    }
    restore_error_handler();

    return $sqlquery;
  }

  public function doQueryAndFetch($sql) {
    $stmt = $this->doOnlyQuery($sql, false);

    return $this->db_fetch($stmt);
  }

  /**
   * Get all records for a query as array of arrays or objects
   *
   * @param string $query          SQL query to execute
   * @param bool   $skipQueryCheck Should query skip security check
   * @param bool   $asArray        Should records be returned as array? If false - records would be returned as StdObject
   *
   * @return array
   */
  public function dbGetAll($query, $skipQueryCheck = false, $asArray = true) {
    $queryResult = $this->doOnlyQuery($query, $skipQueryCheck);

    $result = [];
    while ($row = $this->db_fetch($queryResult)) {
      $result[] = $asArray ? $row : (object)$row;
    }

    return $result;
  }

  public function doQueryFast($query) {
    $sql = $this->prefixReplace($query);

    set_error_handler([$this, 'handlerQueryWarning']);
    $sqlQuery = $this->db_sql_query($sql) or SN::$debug->error(SN::$db->db_error() . "<br />$sql<br />", 'SQL Error');
    restore_error_handler();

    return $sqlQuery;
  }

  /**
   * @param string $query
   *
   * @return int|null
   */
  public function selectValue($query) {
    $row = $this->doQueryAndFetch($query);

    return !empty($row) ? intval(reset($row)) : null;
  }

  /**
   * @param DbQuery $dbQuery
   *
   * @return array|null
   */
  public function dbqSelectAndFetch(DbQuery $dbQuery) {
    return $this->doQueryAndFetch($dbQuery->select());
  }


  public function security_watch_user_queries($query) {
    // TODO Заменить это на новый логгер
    global $config, $is_watching, $user, $debug;

    if (
      !$is_watching
      && !empty($config)
      && $config->game_watchlist_array
      && in_array($user['id'], $config->game_watchlist_array)
    ) {
      if (!preg_match('/^(select|commit|rollback|start transaction)/i', $query)) {
        $is_watching = true;
        $msg         = "\$query = \"{$query}\"\n\r";
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


  /** @noinspection SpellCheckingInspection */
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
        /** @noinspection PhpUnreachableStatementInspection */
      break;
    }
  }

  public function mysql_get_table_list() {
    return $this->pdoSelectAssoc('SHOW TABLES;');
  }

  public function mysql_get_innodb_status() {
    return $this->pdoSelectAssoc('SHOW ENGINE INNODB STATUS;');
  }

  /**
   * @param string $tableName_unsafe
   *
   * @return array[]
   */
  public function mysql_get_fields($tableName_unsafe) {
    $result = [];

    $prefixedTableName_safe = $this->db_escape($this->db_prefix . $tableName_unsafe);
    $q1                     = $this->pdoSelectAssoc("SHOW FULL COLUMNS FROM `{$prefixedTableName_safe}`;");
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
    $q1                     = $this->pdoSelectAssoc("SHOW INDEX FROM {$prefixedTableName_safe};");
    while ($r1 = db_fetch($q1)) {
      $indexName = $r1['Key_name'];
      if (empty($result[$indexName])) {
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

    $dbNameSafe = SN::$db->db_escape(SN::$db_name);

    $q1 = $this->pdoSelectAssoc(
      "SELECT * 
        FROM `information_schema`.`KEY_COLUMN_USAGE` 
        WHERE `TABLE_SCHEMA` = '{$dbNameSafe}' 
          AND `TABLE_NAME` = '{$prefixedTableName_safe}' 
          AND `REFERENCED_TABLE_NAME` IS NOT NULL;"
    );
    while ($r1 = db_fetch($q1)) {
      $indexName = $r1['CONSTRAINT_NAME'];

      $table_referenced = str_replace($this->db_prefix, '', $r1['REFERENCED_TABLE_NAME']);

      $result[$indexName]['name']                       = $indexName;
      $result[$indexName]['signature'][]                = "{$r1['COLUMN_NAME']}=>{$table_referenced}.{$r1['REFERENCED_COLUMN_NAME']}";
      $r1['REFERENCED_TABLE_NAME']                      = $table_referenced;
      $r1['TABLE_NAME']                                 = $tableName_unsafe;
      $result[$indexName]['fields'][$r1['COLUMN_NAME']] = $r1;
    }

    foreach ($result as &$constraint) {
      $constraint['signature'] = implode(',', $constraint['signature']);
    }

    return $result;
  }

  /**
   * Executes direct SQL query
   *
   * @param string $query_string
   *
   * @return false|PDOStatement
   */
  public function db_sql_query($query_string) {
    $mt = microtime(true);

//    // SELECT / SHOW / DESCRIBE / EXPLAIN
//    if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)\b/i', $sql)) {
//      $result = $this->dbLaravel->select($sql, $bindings);
//      // stdClass[] → array[]
//      $result = array_map(fn($row) => (array) $row, $result);
//    } else {
//      // INSERT / UPDATE / DELETE / DDL
//      $result = $this->dbLaravel->statement($sql, $bindings);
//    }

    $result = $this->pdo->query($query_string, PDO::FETCH_ASSOC);

    $this->time_mysql_total += microtime(true) - $mt;

    return $result;
//    return $this->driver->mysql_query($query_string);
  }

  /**
   * @param PDOStatement $stmt
   *
   * @return array|null
   */
  public function db_fetch($stmt) {
    $mt = microtime(true);

    // $result = mysqli_fetch_assoc($query_result);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $this->time_mysql_total += microtime(true) - $mt;

    return empty($result) ? null : $result;
  }

  /**
   * @param PDOStatement $stmt
   *
   * @return array|null
   */
  public function dbFetchClass($stmt, $className = \stdClass::class, $params = [], $mode = PDO::FETCH_CLASS) {
    $mt = microtime(true);

    // $result = mysqli_fetch_assoc($query_result);
    $result = $stmt->fetch($mode, $className);

    $this->time_mysql_total += microtime(true) - $mt;

    return empty($result) ? null : $result;
  }

//  public function db_fetch_row(&$query) {
//    return mysqli_fetch_row($query);
//  }

  public function db_escape($unescaped_string) {
    return substr($this->pdo->quote($unescaped_string), 1, -1);
  }

  public function driver_disconnect() {
    if (is_object($this->pdo)) {
      $this->pdo       = null;
      $this->connected = false;
      unset($this->pdo);
    }

    return true;
  }

  public function db_error() {
//    return mysqli_error($this->link);
    return $this->pdo->errorInfo()[2] ?? null;
  }

  /**
   * @return false|string
   */
  public function db_insert_id() {
    return $this->pdo->lastInsertId();
  }

  /**
   * @param PDOStatement $result
   *
   * @return int
   */
  public function db_num_rows($result) {
    return $result->rowCount();
  }

  /**
   * @param PDOStatement $result
   */
  public function db_affected_rows($result = null) {
    return $result ? $result->rowCount() : null;
  }

  public function getClientInfo() {
    return (string)$this->pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
//    return mysqli_get_client_info($this->link);
  }

  public function getServerInfo() {
//    return mysqli_get_server_info($this->link);
    return (string)$this->pdo->getAttribute(PDO::ATTR_SERVER_INFO);
  }

  public function getHostInfo() {
//    return mysqli_get_host_info($this->link);
    return (string)$this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
  }

  public function getServerStat() {
//    return mysqli_stat($this->link);
    $stmt = $this->pdo->query("SHOW STATUS");
    $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    return sprintf(
      "Uptime: %s  Threads: %s  Questions: %s  Slow queries: %s  Opens: %s  Flush tables: %s  Open tables: %s  Queries per second avg: %.3f",
      $rows['Uptime'] ?? 0,
      $rows['Threads_connected'] ?? 0,
      $rows['Questions'] ?? 0,
      $rows['Slow_queries'] ?? 0,
      $rows['Opened_tables'] ?? 0,
      $rows['Flush_commands'] ?? 0,
      $rows['Open_tables'] ?? 0,
      isset($rows['Uptime'], $rows['Questions']) && $rows['Uptime'] > 0
        ? $rows['Questions'] / $rows['Uptime']
        : 0
    );
  }

  /**
   * @param array $dbSettings
   */
  public function setDbSettings($dbSettings) {
    $this->dbsettings = $dbSettings;
    $this->dbName     = $this->dbsettings['name'];

    return $this;
  }

  /**
   * @param $level
   *
   * @return void
   */
  public function transactionStart($level = '') {
    $this->inTransaction = true;

    if ($level && in_array($level, self::TRANSACTION_LEVELS_ALLOWED)) {
      $this->doOnlyQuery("SET TRANSACTION ISOLATION LEVEL {$level};");
    }

    $this->doOnlyQuery('START TRANSACTION; /* transaction start */', false);
  }

  /**
   * @return void
   */
  public function transactionCommit() {
    $this->doOnlyQuery('COMMIT; /* transaction commit */');
    $this->inTransaction = false;
  }

  /**
   * @return void
   */
  public function transactionRollback() {
    $this->doOnlyQuery('ROLLBACK; /* transaction rollback */');
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

  public static function db_transaction_start($level = '') {
    self::db_transaction_check(db_mysql::DB_TRANSACTION_SHOULD_NOT_BE);

    SN::$gc->db->transactionStart($level);

    self::$transaction_id++;

    if (SN::$config->db_manual_lock_enabled) {
      SN::$config->db_loadItem('var_db_manually_locked');
      SN::$config->db_saveItem('var_db_manually_locked', SN_TIME_SQL);
    }

    self::$db_in_transaction = true;
    self::$transactionDepth++;

    return self::$transaction_id;
  }

  public static function db_transaction_rollback() {
    // static::db_transaction_check(true); // TODO - вообще-то тут тоже надо проверять есть ли транзакция

    SN::$gc->db->transactionRollback();

    self::$db_in_transaction = false;
    self::$transactionDepth--;

    return self::$transaction_id;
  }

  /**
   * Блокирует указанные таблицу/список таблиц
   *
   * @param string|array $tables Таблица/список таблиц для блокировки. Названия таблиц - без префиксов
   *                             <p>string - название таблицы для блокировки</p>
   *                             <p>array - массив, где ключ - имя таблицы, а значение - условия блокировки элементов</p>
   */
  public static function db_lock_tables($tables) {
    $tables = is_array($tables) ? $tables : array($tables => '');
    foreach ($tables as $table_name => $condition) {
      SN::$db->doDbSelect(
        "SELECT 1 FROM {{{$table_name}}}" . ($condition ? ' WHERE ' . $condition : '')
      );
    }
  }

  public static function db_transaction_commit() {
    self::db_transaction_check(db_mysql::DB_TRANSACTION_SHOULD_BE);

    SN::$gc->db->transactionCommit();

    self::$db_in_transaction = false;
    self::$transactionDepth--;

    return self::$transaction_id++;
  }

  /**
   * Эта функция проверяет статус транзакции
   *
   * Это - низкоуровневая функция. В нормальном состоянии движка её сообщения никогда не будут видны
   *
   * @param null|true|false $status Должна ли быть запущена транзакция в момент проверки
   *                                <p>null - транзакция НЕ должна быть запущена</p>
   *                                <p>true - транзакция должна быть запущена - для совместимости с $for_update</p>
   *                                <p>false - всё равно - для совместимости с $for_update</p>
   *
   * @return bool Текущий статус транзакции
   */
  public static function db_transaction_check($status = self::DB_TRANSACTION_WHATEVER) {
    $error_msg = false;
    if ($status && !self::$db_in_transaction) {
      $error_msg = 'No transaction started for current operation';
    } elseif ($status === null && self::$db_in_transaction) {
      $error_msg = 'Transaction is already started';
    }

    if ($error_msg) {
      // TODO - Убрать позже
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sn_db_transaction_check() - ' . $error_msg . '</h1>');
      $backtrace = debug_backtrace();
      array_shift($backtrace);
      pdump($backtrace);
      die($error_msg);
    }

    return self::$db_in_transaction;
  }

  /**
   * Lock specified records in specified tables
   *
   * @param array[] $locks ['$tableName' => [$idToLock, ...],],
   *
   * @return bool|null
   */
  public function lockRecords($locks) {
    // Reordering lock order for known tables
    $newLocks = [];
    foreach (self::TABLE_LOCK_ORDER as $tableName) {
      if (array_key_exists($tableName, $locks)) {
        $newLocks[$tableName] = $locks[$tableName];
        unset($locks[$tableName]);
      }
    }
    // Adding tables left - their lock order is not important
    $newLocks += $locks;

    // Compiling SQL queries to lock records
    $queries = [];
    foreach ($newLocks as $tableName => $lockedIds) {
      if (!empty($lockedIds)) {
        // Detecting primary key name
        $idFieldName = !empty(static::TABLE_ID_FIELD[$tableName]) ? static::TABLE_ID_FIELD[$tableName] : 'id';
        // Making ID mysql-safe
        array_walk($lockedIds, function (&$id) { $id = idval($id); });
        /** @noinspection SqlResolve */
        $queries[] = "(SELECT 1 FROM `{{{$tableName}}}` WHERE `{$idFieldName}` IN (" . implode(',', $lockedIds) . ") FOR UPDATE)";
      }
    }

//    return !empty($query) ? doquery(implode(' UNION ', $query)) : false;
    foreach ($queries as $query) {
      doSelect($query);
    }

    return true;
  }

  // TODO
  public function doDbSelect(string $string, $skip_query_check = false) {
    return $this->doOnlyQuery($string, $skip_query_check);
  }

  // TODO
  /**
   * @param string $string
   * @param        $skip_query_check
   *
   * @return array
   */
  public function doDbSelectAndFetchAll(string $string, $skip_query_check = false) {
    $stmt = $this->doOnlyQuery($string, $skip_query_check);

    return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  /**
   * doDbDelete - not to miss with doDelete() on replaces
   *
   * @param string $string
   *
   * @return bool|PDOStatement
   */
  public function doDbDelete(string $string) {
    return $this->doOnlyQuery($string);
  }

  public function doDbUpdate(string $string) {
    return $this->doOnlyQuery($string);
  }

  public function doDbReplace(string $string) {
    return $this->doOnlyQuery($string);
  }

  public function doDbInsert(string $string) {
    return $this->doOnlyQuery($string);
  }

  /**
   * Executes direct SQL query that returns result like SELECT/SHOW/DESCRIBE/EXPLAIN
   *
   * @param string $sql
   *
   * @return false|PDOStatement
   */
  public function pdoSelectAssoc($sql) {
    $mt = microtime(true);

//    // SELECT / SHOW / DESCRIBE / EXPLAIN
//    if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)\b/i', $sql)) {
//      $result = $this->dbLaravel->select($sql, $bindings);
//      // stdClass[] → array[]
//      $result = array_map(fn($row) => (array) $row, $result);
//    } else {
//      // INSERT / UPDATE / DELETE / DDL
//      $result = $this->dbLaravel->statement($sql, $bindings);
//    }

    $result = $this->pdo->query($sql, PDO::FETCH_ASSOC);

    $this->time_mysql_total += microtime(true) - $mt;

    return $result;
  }

  public function getCapsule(): Capsule {
    return $this->capsule;
  }

  public function countQueryRecords(string $sql): int {
    return $this->getCapsule()::selectOne($this->prefixReplace(
      "SELECT COUNT(*) AS cnt FROM (
    {$sql}
  ) t"))->cnt;
  }

}
