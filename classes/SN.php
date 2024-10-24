<?php
/** @noinspection PhpDeprecationInspection */
/** @noinspection SqlResolve */

/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

use DBAL\db_mysql;
use Player\userOptions;
use Common\Vector;
use Core\GlobalContainer;
use Unit\DBStaticUnit;

/**
 * Class SN
 *
 * Singleton
 */
class SN {
  const DB_TRANSACTION_SHOULD_NOT_BE = null;
  const DB_TRANSACTION_SHOULD_BE = true;
  const DB_TRANSACTION_WHATEVER = false;
  /**
   * Flag that something was rendered
   *
   * @var bool
   */
  public static $gSomethingWasRendered = false;

  /**
   * @var GlobalContainer $gc
   */
  public static $gc;

  /**
   * Основная БД для доступа к данным
   *
   * @var db_mysql $db
   */
  public static $db;
  public static $db_name = '';

  /**
   * Настройки из файла конфигурации
   *
   * @var string
   */
  public static $cache_prefix = 'sn_';
  public static $sn_secret_word = '';

  /**
   * Конфигурация игры
   *
   * @var classConfig $config
   */
  public static $config;


  /**
   * Кэш игры
   *
   * @var classCache $cache
   */
  public static $cache;

  /**
   * @var classLocale $lang
   */
  public static $lang;


  /**
   * @var core_auth $auth
   */
  public static $auth = null;


  public static $db_in_transaction = false;
  public static $transaction_id = 0;
  public static $transactionDepth = 0;

  public static $user = array();
  /**
   * @var userOptions
   */
  public static $user_options;

  /**
   * @var debug $debug
   */
  public static $debug = null;


  public static $options = array();

  /**
   * Is header already rendered?
   *
   * @var bool $headerRendered
   */
  public static $headerRendered = false;

  /** @var bool $sys_user_logged_in Is user logged in? TODO - move to user-related */
  public static $sys_user_logged_in = false;

  /*
  TODO Кэш:
  1. Всегда дешевле использовать процессор, чем локальную память
  2. Всегда дешевле использовать локальную память, чем общую память всех процессов
  3. Всегда дешевле использовать общую память всех процессов, чем обращаться к БД

  Кэш - многоуровневый: локальная память-общая память-БД
  БД может быть сверх-кэширующей - см. HyperNova. Это реализуется на уровне СН-драйвера БД
  Предусмотреть вариант, когда уровни кэширования совпадают, например когда нет xCache и используется общая память
  */
  //public static $cache; // Кэширующий объект - либо встроенная память, либо кэш в памяти с блокировками - находится внутри $db!!!!
  //public static $db; // Объект-БД - либо кэширующий объект с блокировками, либо БД

  // protected static $info = array(); // Кэш информации - инфо о юнитах, инфо о группах итд

  // TODO Автоматически заполнять эту таблицу. В случае кэша в памяти - делать show table при обращении к таблице
  public static $location_info = array(
    LOC_USER => array(
      P_TABLE_NAME => 'users',
      P_ID         => 'id',
      P_OWNER_INFO => array(),
    ),

    LOC_PLANET => array(
      P_TABLE_NAME => 'planets',
      P_ID         => 'id',
      P_OWNER_INFO => array(
        LOC_USER => array(
          P_LOCATION    => LOC_USER,
          P_OWNER_FIELD => 'id_owner',
        ),
      ),
    ),

    LOC_UNIT => array(
      P_TABLE_NAME => 'unit',
      P_ID         => 'unit_id',
      P_OWNER_INFO => array(
        LOC_USER => array(
          P_LOCATION    => LOC_USER,
          P_OWNER_FIELD => 'unit_player_id',
        ),
      ),
    ),

    LOC_QUE => array(
      P_TABLE_NAME => 'que',
      P_ID         => 'que_id',
      P_OWNER_INFO => array(
        array(
          P_LOCATION    => LOC_USER,
          P_OWNER_FIELD => 'que_player_id',
        ),

        array(
          P_LOCATION    => LOC_PLANET,
          P_OWNER_FIELD => 'que_planet_id_origin',
        ),

        array(
          P_LOCATION    => LOC_PLANET,
          P_OWNER_FIELD => 'que_planet_id',
        ),
      ),
    ),

    LOC_FLEET => array(
      P_TABLE_NAME => 'fleets',
      P_ID         => 'fleet_id',
      P_OWNER_INFO => array(
        array(
          P_LOCATION    => LOC_USER,
          P_OWNER_FIELD => 'fleet_owner',
        ),

        array(
          P_LOCATION    => LOC_USER,
          P_OWNER_FIELD => 'fleet_target_owner',
        ),

        array(
          P_LOCATION    => LOC_PLANET,
          P_OWNER_FIELD => 'fleet_start_planet_id',
        ),

        array(
          P_LOCATION    => LOC_PLANET,
          P_OWNER_FIELD => 'fleet_end_planet_id',
        ),
      ),
    ),
  );

  /**
   * @var callable[] $afterInit
   */
  public static $afterInit = [];

  public function __construct() {

  }


  public static function log_file($message, $spaces = 0) {
    if (self::$debug) {
      self::$debug->log_file($message, $spaces);
    }
  }





  // TODO Вынести в отдельный объект

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
    if ($status && !static::$db_in_transaction) {
      $error_msg = 'No transaction started for current operation';
    } elseif ($status === null && static::$db_in_transaction) {
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

    return static::$db_in_transaction;
  }

  public static function db_transaction_start($level = '') {
    static::db_transaction_check(SN::DB_TRANSACTION_SHOULD_NOT_BE);

    static::$gc->db->transactionStart($level);

    static::$transaction_id++;

    if (static::$config->db_manual_lock_enabled) {
      static::$config->db_loadItem('var_db_manually_locked');
      static::$config->db_saveItem('var_db_manually_locked', SN_TIME_SQL);
    }

    static::$db_in_transaction = true;
    self::$transactionDepth++;
    DBStaticUnit::cache_clear();

    return static::$transaction_id;
  }

  public static function db_transaction_commit() {
    static::db_transaction_check(SN::DB_TRANSACTION_SHOULD_BE);

    DBStaticUnit::cache_clear();
    SN::$gc->db->transactionCommit();

    static::$db_in_transaction = false;
    self::$transactionDepth--;

    return static::$transaction_id++;
  }

  public static function db_transaction_rollback() {
    // static::db_transaction_check(true); // TODO - вообще-то тут тоже надо проверять есть ли транзакция
    DBStaticUnit::cache_clear();

    SN::$gc->db->transactionRollback();

    static::$db_in_transaction = false;
    self::$transactionDepth--;

    return static::$transaction_id;
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
      self::$db->doquery(
        "SELECT 1 FROM {{{$table_name}}}" . ($condition ? ' WHERE ' . $condition : '')
      );
    }
  }

  /**
   * @param      $query
   * @param bool $fetch
   * @param bool $skip_lock
   *
   * @return array|bool|mysqli_result|null
   * @deprecated
   */
  public static function db_query_select($query, $fetch = false, $skip_lock = false) {
    $select = strpos(strtoupper($query), 'SELECT') !== false;

    $query .= $select && $fetch ? ' LIMIT 1' : '';
    $query .= $select && !$skip_lock && static::db_transaction_check(SN::DB_TRANSACTION_WHATEVER) ? ' FOR UPDATE' : '';

    return self::$db->doquery($query, $fetch);
  }

  /**
   * @param $query
   *
   * @return array|bool|mysqli_result|null
   * @deprecated
   */
  public static function db_query_update($query) {
    return self::$db->doquery($query, false);
  }

  /**
   * @param $query
   *
   * @return array|bool|mysqli_result|null
   * @deprecated
   */
  public static function db_query_delete($query) {
    return self::$db->doquery($query, false);
  }

  /**
   * @param $query
   *
   * @return array|bool|mysqli_result|null
   * @deprecated
   */
  public static function db_query_insert($query) {
    return self::$db->doquery($query, false);
  }

  /**
   * Возвращает информацию о записи по её ID
   *
   * @param int       $location_type
   * @param int|array $record_id_unsafe
   *    <p>int - ID записи</p>
   *    <p>array - запись пользователя с установленным полем P_ID</p>
   *
   * @return array|false
   *    <p>false - Нет записи с указанным ID</p>
   *    <p>array - запись</p>
   */
  public static function db_get_record_by_id($location_type, $record_id_unsafe) {
    $id_field       = static::$location_info[$location_type][P_ID];
    $record_id_safe = idval(is_array($record_id_unsafe) && isset($record_id_unsafe[$id_field]) ? $record_id_unsafe[$id_field] : $record_id_unsafe);

    return static::db_get_record_list($location_type, "`{$id_field}` = {$record_id_safe}", true);
  }

  public static function db_get_record_list($location_type, $filter = '', $fetch = false, $no_return = false) {
    $location_info = &static::$location_info[$location_type];
    $id_field      = $location_info[P_ID];
    $tableName     = $location_info[P_TABLE_NAME];

    $result = false;

    $query = static::db_query_select(
      "SELECT * FROM {{{$tableName}}}" . (($filter = trim($filter)) ? " WHERE {$filter}" : '')
    );
    while ($row = db_fetch($query)) {
      $result[$row[$id_field]] = $row;
      if ($fetch) {
        break;
      }
    }

    if ($no_return) {
      return true;
    } else {
      return $fetch ? (is_array($result) ? reset($result) : false) : $result;
    }
  }

  public static function db_upd_record_by_id($location_type, $record_id, $set) {
    if (!($record_id = idval($record_id)) || !($set = trim($set))) {
      return false;
    }

    $location_info = &static::$location_info[$location_type];
    $id_field      = $location_info[P_ID];
    $table_name    = $location_info[P_TABLE_NAME];
    if ($result = static::db_query_update("UPDATE {{{$table_name}}} SET {$set} WHERE `{$id_field}` = {$record_id}")) // TODO Как-то вернуть может быть LIMIT 1 ?
    {
      if (static::$db->db_affected_rows()) {
        // Обновляем данные только если ряд был затронут
        DBStaticUnit::cache_clear();
      }
    }

    return $result;
  }

  public static function db_upd_record_list($location_type, $condition, $set) {
    if (!($set = trim($set))) {
      return false;
    }

    $condition  = trim($condition);
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];

    if ($result = static::db_query_update("UPDATE {{{$table_name}}} SET " . $set . ($condition ? ' WHERE ' . $condition : ''))) {

      if (static::$db->db_affected_rows()) { // Обновляем данные только если ряд был затронут
        // Поскольку нам неизвестно, что и как обновилось - сбрасываем кэш этого типа полностью
        DBStaticUnit::cache_clear();
      }
    }

    return $result;
  }

  public static function db_ins_record($location_type, $set) {
    $set        = trim($set);
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    if ($result = static::db_query_insert("INSERT INTO `{{{$table_name}}}` SET {$set}")) {
      if (static::$db->db_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        $record_id = SN::$db->db_insert_id();
        // Вытаскиваем запись целиком, потому что в $set могли быть "данные по умолчанию"
        $result = static::db_get_record_by_id($location_type, $record_id);
        // Очищаем второстепенные кэши - потому что вставленная запись могла повлиять на результаты запросов или локация или еще чего
        DBStaticUnit::cache_clear();
      }
    }

    return $result;
  }

  public static function db_del_record_by_id($location_type, $safe_record_id) {
    if (!($safe_record_id = idval($safe_record_id))) {
      return false;
    }

    $location_info = &static::$location_info[$location_type];
    $id_field      = $location_info[P_ID];
    $table_name    = $location_info[P_TABLE_NAME];
    if ($result = static::db_query_delete("DELETE FROM `{{{$table_name}}}` WHERE `{$id_field}` = {$safe_record_id}")) {
      if (static::$db->db_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        DBStaticUnit::cache_clear();
      }
    }

    return $result;
  }

  public static function db_del_record_list($location_type, $condition) {
    if (!($condition = trim($condition))) {
      return false;
    }

    $table_name = static::$location_info[$location_type][P_TABLE_NAME];

    if ($result = static::db_query_delete("DELETE FROM `{{{$table_name}}}` WHERE {$condition}")) {
      if (static::$db->db_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        // Обнуление кэша, потому что непонятно, что поменялось
        DBStaticUnit::cache_clear();
      }
    }

    return $result;
  }


  /*
   * С $for_update === true эта функция должна вызываться только из транзакции! Все соответствующие записи в users и planets должны быть уже блокированы!
   *
   * $que_type
   *   !$que_type - все очереди
   *   QUE_XXX - конкретная очередь по планете
   * $user_id - ID пользователя
   * $planet_id
   *   $que_type == QUE_RESEARCH - игнорируется
   *   null - обработка очередей планет не производится
   *   false/0 - обрабатываются очереди всех планет по $user_id
   *   (integer) - обрабатываются локальные очереди для планеты. Нужно, например, в обработчике флотов
   *   иначе - $que_type для указанной планеты
   * $for_update - true == нужно блокировать записи
   *
   * TODO Работа при !$user_id
   * TODO Переформатировать вывод данных, что бы можно было возвращать данные по всем планетам и юзерам в одном запросе: добавить под-массивы 'que', 'planets', 'players'
   *
   */
  /** @noinspection PhpUnusedParameterInspection */
  public static function db_que_list_by_type_location($user_id, $planet_id = null, $que_type = false, $for_update = false) {
    if (!$user_id) {
      pdump(debug_backtrace());
      die('No user_id for que_get_que()');
    }

    $ques = array();

    $query = array();

    if ($user_id = idval($user_id)) {
      $query[] = "`que_player_id` = {$user_id}";
    }

    if ($que_type == QUE_RESEARCH || $planet_id === null) {
      $query[] = "`que_planet_id` IS NULL";
    } elseif ($planet_id) {
      $query[] = "(`que_planet_id` = {$planet_id}" . ($que_type ? '' : ' OR que_planet_id IS NULL') . ")";
    }
    if ($que_type) {
      $query[] = "`que_type` = {$que_type}";
    }

    $ques['items'] = static::db_get_record_list(LOC_QUE, implode(' AND ', $query));

    return que_recalculate($ques);
  }


  public static function loadFileSettings() {
    $dbsettings = array();

    require(SN_CONFIG_PATH);
    //self::$db_prefix = $dbsettings['prefix'];
    self::$cache_prefix = !empty($dbsettings['cache_prefix']) ? $dbsettings['cache_prefix'] : $dbsettings['prefix'];
    self::$db_name      = $dbsettings['name'];
    /** @noinspection SpellCheckingInspection */
    self::$sn_secret_word = $dbsettings['secretword'];

    self::services();

    unset($dbsettings);
  }

  public static function init_global_objects() {
    global $sn_cache, $config, $debug;

    $debug    = self::$debug = self::$gc->debug;
    self::$db = self::$gc->db;
    self::$db->sn_db_connect();

    self::$user_options = new userOptions(0);

    // Initializing global `cache` object
    $sn_cache = static::$cache = self::$gc->cache;
    $tables   = SN::$db->schema()->getSnTables();
    empty($tables) && die('DB error - cannot find any table. Halting...');

    // Initializing global `config` object
    $config = static::$config = self::$gc->config;

    // Initializing statics
    Vector::_staticInit(static::$config);

    // After init callbacks
    foreach (static::$afterInit as $callback) {
      if (is_callable($callback)) {
        $callback();
      }
    }
  }

  /**
   * @param int    $newStatus
   * @param string $newMessage
   *
   * @return int
   * @noinspection PhpUnusedParameterInspection
   */
  public static function gameDisable($newStatus = GAME_DISABLE_REASON, $newMessage = '') {
    /** @noinspection PhpCastIsUnnecessaryInspection */
    $old_server_status = intval(self::$config->pass()->game_disable);

    self::$config->pass()->game_disable = $newStatus;

    return $old_server_status;
  }

//  public static function gameEnable() {
//    self::$config->pass()->game_disable = GAME_DISABLE_NONE;
//  }

  /**
   * Is game disabled?
   *
   * @return bool
   */
  public static function gameIsDisabled() {
    return self::$config->pass()->game_disable != GAME_DISABLE_NONE;
  }


  /**
   * @return GlobalContainer
   */
  public static function services() {
    if (empty(self::$gc)) {
      self::$gc = new GlobalContainer(array(
        'cachePrefix' => self::$cache_prefix,
      ));
    }

    return self::$gc;
  }

}
