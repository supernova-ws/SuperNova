<?php

use Vector\Vector;

class classSupernova {
  /**
   * ex $sn_mvc
   *
   * @var array
   */
  public static $sn_mvc = array();

  /**
   * ex $functions
   *
   * @var array
   */
  public static $functions = array();

  /**
   * @var array[] $design
   */
  public static $design = array(
    'bbcodes' => array(),
    'smiles'  => array(),
  );

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
  public static $cache_prefix = '';

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
   * @var core_auth $auth
   */
  public static $auth = null;


  public static $db_in_transaction = false;
  public static $db_records_locked = false;
  public static $transaction_id = 0;
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

  public static $locks = array(); // Информация о блокировках

  public static $delayed_changset = array(); // Накопительный массив изменений

  // Кэш индексов - ключ MD5-строка от суммы ключевых строк через | - менять | на что-то другое перед поиском и назад - после поиска
  // Так же в индексах могут быть двойные вхождения - например, названия планет да и вообще
  // Придумать спецсимвол для NULL

  /*
  TODO Кэш:
  1. Всегда дешевле использовать процессор, чем локальную память
  2. Всегда дешевле использовать локальную память, чем общую память всех процессов
  3. Всегда дешевле использовать общую память всех процессов, чем обращаться к БД

  Кэш - многоуровневый: локальная память-общая память-БД
  БД может быть сверхкэширующей - см. HyperNova. Это реализуется на уровне СН-драйвера БД
  Предусмотреть вариант, когда уровни кэширования совпадают, например когда нет xcache и используется общая память
  */

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
   * @param $db db_mysql
   */
  public static function init_main_db($db) {
    self::$db = $db;
    self::$db->sn_db_connect();
  }


  public static function log_file($message, $spaces = 0) {
    if (self::$debug) {
      self::$debug->log_file($message, $spaces);
    }
  }

  public static function debug_set_handler($debug) {
    self::$debug = $debug;
  }


  // TODO Вынести в отдельный объект
  /**
   * Эта функция проверяет статус транзакции
   *
   * Это - низкоуровневая функция. В нормальном состоянии движка её сообщения никогда не будут видны
   *
   * @param null|true|false $status Должна ли быть запущена транзакция в момент проверки
   *   <p>null - транзакция НЕ должна быть запущена</p>
   *   <p>true - транзакция должна быть запущена - для совместимости с $for_update</p>
   *   <p>false - всё равно - для совместимости с $for_update</p>
   *
   * @return bool Текущий статус транзакции
   */
  public static function db_transaction_check($status = null) {
    $error_msg = false;
    if ($status && !static::$db_in_transaction) {
      $error_msg = 'No transaction started for current operation';
    } elseif ($status === null && static::$db_in_transaction) {
      $error_msg = 'Transaction is already started';
    }

    if (!empty($error_msg)) {
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
    static::db_transaction_check(null);

    $level ? doquery('SET TRANSACTION ISOLATION LEVEL ' . $level) : false;

    static::$transaction_id++;
    doquery('START TRANSACTION');

    if (classSupernova::$config->db_manual_lock_enabled) {
      classSupernova::$config->db_loadItem('var_db_manually_locked');
      classSupernova::$config->db_saveItem('var_db_manually_locked', SN_TIME_SQL);
    }

    static::$db_in_transaction = true;
    SnCache::locatorReset();
    SnCache::queriesReset();

    return static::$transaction_id;
  }

  public static function db_transaction_commit() {
    static::db_transaction_check(true);

    if (!empty(static::$delayed_changset)) {
      static::db_changeset_apply(static::$delayed_changset, true);
    }
    doquery('COMMIT');

    return static::db_transaction_clear();
  }

  public static function db_transaction_rollback() {
    // static::db_transaction_check(true); // TODO - вообще-то тут тоже надо проверять есть ли транзакция

    if (!empty(static::$delayed_changset)) {
//      static::db_changeset_revert();
      // TODO Для этапа 1 - достаточно чистить только те таблицы, что были затронуты
      // Для этапа 2 - чистить только записи
      // Для этапа 3 - возвращать всё
      SnCache::cache_clear_all(true);
    }
    doquery('ROLLBACK');

    return static::db_transaction_clear();
  }

  protected static function db_transaction_clear() {
    static::$delayed_changset = array();
    SnCache::cache_lock_unset_all();

    static::$db_in_transaction = false;
    static::$db_records_locked = false;
    static::$transaction_id++;

    return static::$transaction_id;
  }

  /**
   * Блокирует указанные таблицу/список таблиц
   *
   * @param string|array $tables Таблица/список таблиц для блокировки. Названия таблиц - без префиксов
   * <p>string - название таблицы для блокировки</p>
   * <p>array - массив, где ключ - имя таблицы, а значение - условия блокировки элементов</p>
   */
  public static function db_lock_tables($tables) {
    $tables = is_array($tables) ? $tables : array($tables => '');
    foreach ($tables as $table_name => $condition) {
      self::$db->doquery("SELECT 1 FROM {{{$table_name}}}" . ($condition ? ' WHERE ' . $condition : ''));
    }
  }

  /**
   * @param      $query
   * @param bool $fetch
   * @param bool $skip_lock
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_query($query, $fetch = false, $skip_lock = false) {
    $select = strpos(strtoupper($query), 'SELECT') !== false;

    $query .= $select && $fetch ? ' LIMIT 1' : '';
    $query .= $select && !$skip_lock && static::db_transaction_check(false) ? ' FOR UPDATE' : '';

    $result = self::$db->doquery($query, $fetch);

    return $result;
  }

  /**
   * Возвращает информацию о записи по её ID
   *
   * @param int       $location_type
   * @param int|array $record_id_unsafe
   *    <p>int - ID записи</p>
   *    <p>array - запись пользователя с установленным полем P_ID</p>
   * @param bool      $for_update @deprecated
   * @param string    $fields @deprecated список полей или '*'/'' для всех полей
   * @param bool      $skip_lock Указывает на то, что не нужно блокировать запись //TODO и не нужно сохранять в кэше
   *
   * @return array|false
   *    <p>false - Нет записи с указанным ID</p>
   *    <p>array - запись</p>
   */
  public static function db_get_record_by_id($location_type, $record_id_unsafe, $for_update = false, $fields = '*', $skip_lock = false) {
    $id_field = static::$location_info[$location_type][P_ID];
    $record_id_safe = idval(is_array($record_id_unsafe) && isset($record_id_unsafe[$id_field]) ? $record_id_unsafe[$id_field] : $record_id_unsafe);

    return static::db_get_record_list($location_type, "`{$id_field}` = {$record_id_safe}", true, false);
  }

  public static function db_get_record_list($location_type, $filter = '', $fetch = false, $no_return = false) {
//    $query_cache = &SnCache::$queries[$location_type][$filter];
    $query_cache = &SnCache::getQueriesByLocationAndFilter($location_type, $filter);

    if (!isset($query_cache) || $query_cache === null) {
      $location_info = &static::$location_info[$location_type];
      $id_field = $location_info[P_ID];
      $query_cache = array();

      if (static::db_transaction_check(false)) {
        // Проходим по всем родителям данной записи
        foreach ($location_info[P_OWNER_INFO] as $owner_data) {
          $owner_location_type = $owner_data[P_LOCATION];
          $parent_id_list = array();
          // Выбираем родителей данного типа и соответствующие ИД текущего типа
          $query = static::db_query(
            "SELECT
              distinct({{{$location_info[P_TABLE_NAME]}}}.{$owner_data[P_OWNER_FIELD]}) AS parent_id
            FROM {{{$location_info[P_TABLE_NAME]}}}" .
            ($filter ? ' WHERE ' . $filter : '') .
            ($fetch ? ' LIMIT 1' : ''), false, true);

          while ($row = db_fetch($query)) {
            // Исключаем из списка родительских ИД уже заблокированные записи
            if (!SnCache::cache_lock_get($owner_location_type, $row['parent_id'])) {
              $parent_id_list[$row['parent_id']] = $row['parent_id'];
            }
          }
          // Если все-таки какие-то записи еще не заблокированы - вынимаем текущие версии из базы
          if ($indexes_str = implode(',', $parent_id_list)) {
            $parent_id_field = static::$location_info[$owner_location_type][P_ID];
            static::db_get_record_list($owner_location_type,
              $parent_id_field . (count($parent_id_list) > 1 ? " IN ({$indexes_str})" : " = {$indexes_str}"), $fetch, true);
          }
        }
      }

      $query = static::db_query(
        "SELECT * FROM {{{$location_info[P_TABLE_NAME]}}}" .
        (($filter = trim($filter)) ? " WHERE {$filter}" : '')
      );
      while ($row = db_fetch($query)) {
        SnCache::cache_set($location_type, $row);
//        $query_cache[$row[$id_field]] = &SnCache::$data[$location_type][$row[$id_field]];
        $query_cache[$row[$id_field]] = &SnCache::getDataRefByLocationAndId($location_type, $row[$id_field]);
//        static::checkReturnRef($query_cache[$row[$id_field]], SnCache::$data[$location_type][$row[$id_field]]);
      }
    }

    if ($no_return) {
      return true;
    } else {
      $result = false;
      if (is_array($query_cache)) {
        foreach ($query_cache as $key => $value) {
          $result[$key] = $value;
          if ($fetch) {
            break;
          }
        }
      }

      return $fetch ? (is_array($result) ? reset($result) : false) : $result;
    }
  }

  /**
   * @param int    $location_type
   * @param int    $record_id
   * @param string $set - SQL SET structure
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_upd_record_by_id($location_type, $record_id, $set) {
    if (!($record_id = idval($record_id)) || !($set = trim($set))) {
      return false;
    }

    $location_info = &static::$location_info[$location_type];
    $id_field = $location_info[P_ID];
    $table_name = $location_info[P_TABLE_NAME];
    if ($result = static::db_query($q = "UPDATE {{{$table_name}}} SET {$set} WHERE `{$id_field}` = {$record_id}")) // TODO Как-то вернуть может быть LIMIT 1 ?
    {
      if (static::$db->db_affected_rows()) {
        // Обновляем данные только если ряд был затронут
        // TODO - переделать под работу со структурированными $set

        // Тут именно так, а не cache_unset - что бы в кэшах автоматически обновилась запись. Будет нужно на будущее
        //static::$data[$location_type][$record_id] = null;
        SnCache::cacheUnsetElement($location_type, $record_id);
        // Вытаскиваем обновленную запись
        static::db_get_record_by_id($location_type, $record_id);
        SnCache::cache_clear($location_type, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }

  public static function db_upd_record_list($location_type, $condition, $set) {
    if (!($set = trim($set))) {
      return false;
    }

    $condition = trim($condition);
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];

    if ($result = static::db_query("UPDATE {{{$table_name}}} SET " . $set . ($condition ? ' WHERE ' . $condition : ''))) {

      if (static::$db->db_affected_rows()) { // Обновляем данные только если ряд был затронут
        // Поскольку нам неизвестно, что и как обновилось - сбрасываем кэш этого типа полностью
        // TODO - когда будет структурированный $condition и $set - перепаковывать данные
        SnCache::cache_clear($location_type, true);
      }
    }

    return $result;
  }

  /**
   * @param int    $location_type
   * @param string $set
   *
   * @return array|bool|false|mysqli_result|null
   */
  public static function db_ins_record($location_type, $set) {
    $set = trim($set);
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    if ($result = static::db_query("INSERT INTO `{{{$table_name}}}` SET {$set}")) {
      if (static::$db->db_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        $record_id = db_insert_id();
        // Вытаскиваем запись целиком, потому что в $set могли быть "данные по умолчанию"
        $result = static::db_get_record_by_id($location_type, $record_id);
        // Очищаем второстепенные кэши - потому что вставленная запись могла повлиять на результаты запросов или локация или еще чего
        // TODO - когда будет поддержка изменения индексов и локаций - можно будет вызывать её
        SnCache::cache_clear($location_type, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }

  public static function db_ins_field_set($location_type, $field_set, $serialize = false) {
    // TODO multiinsert
    !sn_db_field_set_is_safe($field_set) ? $field_set = sn_db_field_set_make_safe($field_set, $serialize) : false;
    sn_db_field_set_safe_flag_clear($field_set);
    $values = implode(',', $field_set);
    $fields = implode(',', array_keys($field_set));

    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    if ($result = static::db_query("INSERT INTO `{{{$table_name}}}` ($fields) VALUES ($values);")) {
      if (static::$db->db_affected_rows()) {
        // Обновляем данные только если ряд был затронут
        $record_id = db_insert_id();
        // Вытаскиваем запись целиком, потому что в $set могли быть "данные по умолчанию"
        $result = static::db_get_record_by_id($location_type, $record_id);
        // Очищаем второстепенные кэши - потому что вставленная запись могла повлиять на результаты запросов или локация или еще чего
        // TODO - когда будет поддержка изменения индексов и локаций - можно будет вызывать её
        SnCache::cache_clear($location_type, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }

  public static function db_del_record_by_id($location_type, $safe_record_id) {
    if (!($safe_record_id = idval($safe_record_id))) {
      return false;
    }

    $location_info = &static::$location_info[$location_type];
    $id_field = $location_info[P_ID];
    $table_name = $location_info[P_TABLE_NAME];
    if ($result = static::db_query("DELETE FROM `{{{$table_name}}}` WHERE `{$id_field}` = {$safe_record_id}")) {
      // Обновляем данные только если ряд был затронут
      if (static::$db->db_affected_rows()) {
        SnCache::cache_unset($location_type, $safe_record_id);
      }
    }

    return $result;
  }

  public static function db_del_record_list($location_type, $condition) {
    if (!($condition = trim($condition))) {
      return false;
    }

    $location_info = &static::$location_info[$location_type];
    $table_name = $location_info[P_TABLE_NAME];

    if ($result = static::db_query("DELETE FROM `{{{$table_name}}}` WHERE {$condition}")) {
      // Обновляем данные только если ряд был затронут
      if (static::$db->db_affected_rows()) {
        // Обнуление кэша, потому что непонятно, что поменялось
        // TODO - когда будет структурированный $condition можно будет делать только cache_unset по нужным записям
        SnCache::cache_clear($location_type);
      }
    }

    return $result;
  }



  // Работа с пользователями
  /**
   * Возвращает информацию о пользователе по его ID
   *
   * @param int|array $user_id_unsafe
   *    <p>int - ID пользователя</p>
   *    <p>array - запись пользователя с установленным полем ['id']</p>
   * @param bool      $for_update @deprecated
   * @param string    $fields @deprecated список полей или '*'/'' для всех полей
   * @param null      $player
   * @param bool|null $player Признак выбора записи пользователь типа "игрок"
   *    <p>null - Можно выбрать запись любого типа</p>
   *    <p>true - Выбирается только запись типа "игрок"</p>
   *    <p>false - Выбирается только запись типа "альянс"</p>
   *
   * @return array|false
   *    <p>false - Нет записи с указанным ID и $player</p>
   *    <p>array - запись типа $user</p>
   */
  public static function db_get_user_by_id($user_id_unsafe, $for_update = false, $fields = '*', $player = null) {
    $user = static::db_get_record_by_id(LOC_USER, $user_id_unsafe, $for_update, $fields);

    return (is_array($user) &&
      (
        $player === null
        ||
        ($player === true && !$user['user_as_ally'])
        ||
        ($player === false && $user['user_as_ally'])
      )) ? $user : false;
  }

  public static function db_get_user_by_username($username_unsafe, $for_update = false, $fields = '*', $player = null, $like = false) {
    // TODO Проверить, кстати - а везде ли нужно выбирать юзеров или где-то все-таки ищутся Альянсы ?
    if (!($username_unsafe = trim($username_unsafe))) {
      return false;
    }

    $user = null;
    if (SnCache::isArrayLocation(LOC_USER)) {
      foreach (SnCache::getData(LOC_USER) as $user_id => $user_data) {
        if (is_array($user_data) && isset($user_data['username'])) {
          // проверяем поле
          // TODO Возможно есть смысл всегда искать по strtolower - но может игрок захочет переименоваться с другим регистром? Проверить!
          if ((!$like && $user_data['username'] == $username_unsafe) || ($like && strtolower($user_data['username']) == strtolower($username_unsafe))) {
            // $user_as_ally = intval($user_data['user_as_ally']);
            $user_as_ally = idval($user_data['user_as_ally']);
            if ($player === null || ($player === true && !$user_as_ally) || ($player === false && $user_as_ally)) {
              $user = $user_data;
              break;
            }
          }
        }
      }
    }

    if ($user === null) {
      // Вытаскиваем запись
      $username_safe = db_escape($like ? strtolower($username_unsafe) : $username_unsafe); // тут на самом деле strtolower() лишняя, но пусть будет

      // TODO переписать
      $user = self::$db->selectRow(
        DBStaticUser::buildSelect()
          ->field('*')
          ->where(array("`username` " . ($like ? 'LIKE' : '=') . " '{$username_safe}'"))
          ->setFetchOne()
      );

      if (empty($user)) {
        $user = null;
      }

//      $user = static::db_query(
//        "SELECT * FROM {{users}} WHERE `username` " . ($like ? 'LIKE' : '=') . " '{$username_safe}'"
//        , true);
      SnCache::cache_set(LOC_USER, $user); // В кэш-юзер так же заполнять индексы
    }

    return $user;
  }

  // UNUSED
//  public static function db_get_user_by_email($email_unsafe, $use_both = false, $for_update = false, $fields = '*') {
//    if (!($email_unsafe = strtolower(trim($email_unsafe)))) {
//      return false;
//    }
//
//    $user = null;
//    // TODO переделать на индексы
//    if (is_array(static::$data[LOC_USER])) {
//      foreach (static::$data[LOC_USER] as $user_id => $user_data) {
//        if (is_array($user_data) && isset($user_data['email_2'])) {
//          // проверяем поле
//          if (strtolower($user_data['email_2']) == $email_unsafe || ($use_both && strtolower($user_data['email']) == $email_unsafe)) {
//            $user = $user_data;
//            break;
//          }
//        }
//      }
//    }
//
//    if ($user === null) {
//      // Вытаскиваем запись
//      $email_safe = db_escape($email_unsafe);
//      $user = static::db_query(
//        "SELECT * FROM {{users}} WHERE LOWER(`email_2`) = '{$email_safe}'" .
//        ($use_both ? " OR LOWER(`email`) = '{$email_safe}'" : '')
//        , true);
//
//      static::cache_set(LOC_USER, $user); // В кэш-юзер так же заполнять индексы
//    }
//
//    return $user;
//  }

  public static function db_get_user_by_where($where_safe, $for_update = false, $fields = '*') {
    $user = null;
    // TODO переделать на индексы

    if ($user === null && !empty($where_safe)) {
      // Вытаскиваем запись
      $user = static::db_query("SELECT * FROM {{users}} WHERE {$where_safe}", true);

      SnCache::cache_set(LOC_USER, $user); // В кэш-юзер так же заполнять индексы
    }

    return $user;
  }


  public static function db_unit_time_restrictions($date = SN_TIME_NOW) {
    $date = is_numeric($date) ? "FROM_UNIXTIME({$date})" : "'{$date}'";

    return
      "(unit_time_start IS NULL OR unit_time_start <= {$date}) AND
    (unit_time_finish IS NULL OR unit_time_finish = '1970-01-01 03:00:00' OR unit_time_finish >= {$date})";
  }

  public static function db_get_unit_by_id($unit_id, $for_update = false, $fields = '*') {
    // TODO запихивать в $data[LOC_LOCATION][$location_type][$location_id]
    $unit = static::db_get_record_by_id(LOC_UNIT, $unit_id, $for_update, $fields);
//    if (is_array($unit)) {
//      // static::$locator[LOC_UNIT][$unit['unit_location_type']][$unit['unit_location_id']][$unit['unit_snid']] = &SnCache::$data[LOC_UNIT][$unit_id];
//      SnCache::$locator[LOC_UNIT][$unit['unit_location_type']][$unit['unit_location_id']][$unit['unit_snid']] = &SnCache::getDataRefByLocationAndId(LOC_UNIT, $unit_id);
//    }
    SnCache::setUnitLocator($unit, $unit_id);

    return $unit;
  }

  /**
   * @param int $user_id
   * @param int $location_type
   * @param int $location_id
   *
   * @return array|bool
   */
  public static function db_get_unit_list_by_location($user_id = 0, $location_type, $location_id) {
    if (!($location_type = idval($location_type)) || !($location_id = idval($location_id))) {
      return false;
    }

//    $query_cache = &SnCache::$locator[LOC_UNIT][$location_type][$location_id];
    $query_cache = &SnCache::getUnitLocatorByFullLocation($location_type, $location_id);
    if (!isset($query_cache)) {
      $got_data = static::db_get_record_list(LOC_UNIT, "unit_location_type = {$location_type} AND unit_location_id = {$location_id} AND " . static::db_unit_time_restrictions());
      if (is_array($got_data)) {
        foreach ($got_data as $unit_id => $unit_data) {
//          $query_cache[$unit_data['unit_snid']] = &SnCache::$data[LOC_UNIT][$unit_id];
          $query_cache[$unit_data['unit_snid']] = &SnCache::getDataRefByLocationAndId(LOC_UNIT, $unit_id);
        }
      }
    }

    $result = false;
    if (is_array($query_cache)) {
      foreach ($query_cache as $key => $value) {
        $result[$key] = $value;
      }
    }

    return $result;
  }

  public static function db_get_unit_by_location($user_id = 0, $location_type, $location_id, $unit_snid = 0, $for_update = false, $fields = '*') {
    static::db_get_unit_list_by_location($user_id, $location_type, $location_id);

    return SnCache::getUnitLocator($location_type, $location_id, $unit_snid);
  }


  /*
   * С $for_update === true эта функция должна вызываться только из транзакции! Все соответствующие записи в users и planets должны быть уже блокированы!
   *
   * $que_type
   *   !$que_type - все очереди
   *   QUE_XXXXXX - конкретная очередь по планете
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
   * TODO Переформатировать вывод данных, что бы можно было возвращать данные по всем планетам и юзерам в одном запросе: добавить подмассивы 'que', 'planets', 'players'
   *
   */
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


  public static function db_changeset_prepare_unit($unit_id, $unit_value, $user, $planet_id = null) {
    if (!is_array($user)) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sn_db_unit_changeset_prepare() - USER is not ARRAY</h1>');
      pdump(debug_backtrace());
      die('USER is not ARRAY');
    }
    if (!isset($user['id']) || !$user['id']) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sn_db_unit_changeset_prepare() - USER[id] пустой</h1>');
      pdump($user);
      pdump(debug_backtrace());
      die('USER[id] пустой');
    }
    $planet_id = is_array($planet_id) && isset($planet_id['id']) ? $planet_id['id'] : $planet_id;

    $unit_location = sys_get_unit_location($user, array(), $unit_id);
    $location_id = $unit_location == LOC_USER ? $user['id'] : $planet_id;
    $location_id = $location_id ? $location_id : 'NULL';

    $temp = DBStaticUnit::db_unit_by_location($user['id'], $unit_location, $location_id, $unit_id, true, 'unit_id');
    if ($temp['unit_id']) {
      $db_changeset = array(
        'action'  => SQL_OP_UPDATE,
        P_VERSION => 1,
        'where'   => array(
          "unit_id" => $temp['unit_id'],
        ),
        'fields'  => array(
          'unit_level' => array(
            'delta' => $unit_value
          ),
        ),
      );
    } else {
      $db_changeset = array(
        'action' => SQL_OP_INSERT,
        'fields' => array(
          'unit_player_id'     => array(
            'set' => $user['id'],
          ),
          'unit_location_type' => array(
            'set' => $unit_location,
          ),
          'unit_location_id'   => array(
            'set' => $unit_location == LOC_USER ? $user['id'] : $planet_id,
          ),
          'unit_type'          => array(
            'set' => get_unit_param($unit_id, P_UNIT_TYPE),
          ),
          'unit_snid'          => array(
            'set' => $unit_id,
          ),
          'unit_level'         => array(
            'set' => $unit_value,
          ),
        ),
      );
    }

    return $db_changeset;
  }


  public function db_changeset_delay($table_name, $table_data) {
    // TODO Применять ченджсет к записям
    static::$delayed_changset[$table_name] = is_array(static::$delayed_changset[$table_name]) ? static::$delayed_changset[$table_name] : array();
    // TODO - На самом деле дурацкая оптимизация, если честно - может быть идентичные записи с идентичными дельтами - и привет. Но не должны, конечно
    static::$delayed_changset[$table_name] = array_merge(static::$delayed_changset[$table_name], $table_data);
  }

  public function db_changeset_condition_compile(&$conditions, &$table_name = '') {
    if (!$conditions[P_LOCATION] || $conditions[P_LOCATION] == LOC_NONE) {
      $conditions[P_LOCATION] = LOC_NONE;
      switch ($table_name) {
        case 'users':
        case LOC_USER:
          $conditions[P_TABLE_NAME] = $table_name = 'users';
          $conditions[P_LOCATION] = LOC_USER;
        break;

        case 'planets':
        case LOC_PLANET:
          $conditions[P_TABLE_NAME] = $table_name = 'planets';
          $conditions[P_LOCATION] = LOC_PLANET;
        break;

        case 'unit':
        case LOC_UNIT:
          $conditions[P_TABLE_NAME] = $table_name = 'unit';
          $conditions[P_LOCATION] = LOC_UNIT;
        break;
      }
    }

    $conditions[P_FIELDS_STR] = '';
    if ($conditions['fields']) {
      $fields = array();
      foreach ($conditions['fields'] as $field_name => $field_data) {
        $condition = "`{$field_name}` = ";
        $value = '';
        if ($field_data['delta']) {
          $value = "`{$field_name}`" . ($field_data['delta'] >= 0 ? '+' : '') . $field_data['delta'];
        } elseif ($field_data['set']) {
          $value = (is_string($field_data['set']) ? "'{$field_data['set']}'" : $field_data['set']);
        }

        if ($value) {
          $fields[] = $condition . $value;
        }
      }
      $conditions[P_FIELDS_STR] = implode(',', $fields);
    }

    $conditions[P_WHERE_STR] = '';
    if (!empty($conditions['where'])) {
      if ($conditions[P_VERSION] == 1) {
        $the_conditions = array();
        foreach ($conditions['where'] as $field_id => $field_value) {
          // Простое условие - $field_id = $field_value
          if (is_string($field_id)) {
            $field_value =
              $field_value === null ? 'NULL' :
                (is_string($field_value) ? "'" . db_escape($field_value) . "'" :
                  (is_bool($field_value) ? intval($field_value) : $field_value));
            $the_conditions[] = "`{$field_id}` = {$field_value}";
          } else {
            die('Неподдерживаемый тип условия');
          }
        }
      } else {
        $the_conditions = &$conditions['where'];
      }
      $conditions[P_WHERE_STR] = implode(' AND ', $the_conditions);
    }

    switch ($conditions['action']) {
      case SQL_OP_DELETE:
        $conditions[P_ACTION_STR] = ("DELETE FROM {{{$table_name}}}");
      break;
      case SQL_OP_UPDATE:
        $conditions[P_ACTION_STR] = ("UPDATE {{{$table_name}}} SET");
      break;
      case SQL_OP_INSERT:
        $conditions[P_ACTION_STR] = ("INSERT INTO {{{$table_name}}} SET");
      break;
      // case SQL_OP_REPLACE: $result = doquery("REPLACE INTO {{{$table_name}}} SET {$fields}") && $result; break;
      default:
        die('Неподдерживаемая операция в classSupernova::db_changeset_condition_compile');
    }

    $conditions[P_QUERY_STR] = $conditions[P_ACTION_STR] . ' ' . $conditions[P_FIELDS_STR] . (' WHERE ' . $conditions[P_WHERE_STR]);
  }

  public static function db_changeset_apply($db_changeset, $flush_delayed = false) {
    $result = true;
    if (!is_array($db_changeset) || empty($db_changeset)) {
      return $result;
    }

    foreach ($db_changeset as $table_name => &$table_data) {
      // TODO - delayed changeset
      foreach ($table_data as $record_id => &$conditions) {
        static::db_changeset_condition_compile($conditions, $table_name);

        if ($conditions['action'] != SQL_OP_DELETE && !$conditions[P_FIELDS_STR]) {
          continue;
        }
        if ($conditions['action'] == SQL_OP_DELETE && !$conditions[P_WHERE_STR]) {
          continue;
        } // Защита от случайного удаления всех данных в таблице

        if ($conditions[P_LOCATION] != LOC_NONE) {
          switch ($conditions['action']) {
            case SQL_OP_DELETE:
              $result = self::db_del_record_list($conditions[P_LOCATION], $conditions[P_WHERE_STR]) && $result;
            break;
            case SQL_OP_UPDATE:
              $result = self::db_upd_record_list($conditions[P_LOCATION], $conditions[P_WHERE_STR], $conditions[P_FIELDS_STR]) && $result;
            break;
            case SQL_OP_INSERT:
              $result = self::db_ins_record($conditions[P_LOCATION], $conditions[P_FIELDS_STR]) && $result;
            break;
            default:
              die('Неподдерживаемая операция в classSupernova::db_changeset_apply');
            // case SQL_OP_REPLACE: $result = $result && doquery("REPLACE INTO {{{$table_name}}} SET {$fields}"); break;
          }
        } else {
          $result = doquery($conditions[P_QUERY_STR]) && $result;
        }
      }
    }

    return $result;
  }



































  // que_process не всегда должна работать в режиме прямой работы с БД !! Она может работать и в режиме эмуляции
  // !!!!!!!! После que_get брать не [0] элемент, а first() - тогда можно в индекс элемента засовывать que_id из таблицы


  // Это для поиска по кэшу
  protected static function db_get_record_by_field($location_type) {
  }

  // Для модулей - регистрация юнитов
  public static function unit_register() {

  }


  public static function init_0_prepare() {
    // Отключаем magic_quotes
    ini_get('magic_quotes_sybase') ? die('SN is incompatible with \'magic_quotes_sybase\' turned on. Disable it in php.ini or .htaccess...') : false;
    if (@get_magic_quotes_gpc()) {
      $gpcr = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
      array_walk_recursive($gpcr, function (&$value, $key) {
        $value = stripslashes($value);
      });
    }
    if (function_exists('set_magic_quotes_runtime')) {
      @set_magic_quotes_runtime(0);
      @ini_set('magic_quotes_runtime', 0);
      @ini_set('magic_quotes_sybase', 0);
    }
  }

  public static function init_3_load_config_file() {
    $dbsettings = array();

    require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);
    self::$cache_prefix = !empty($dbsettings['cache_prefix']) ? $dbsettings['cache_prefix'] : $dbsettings['prefix'];
    self::$db_name = $dbsettings['name'];
    self::$sn_secret_word = $dbsettings['secretword'];
    unset($dbsettings);
  }

  public static function init_global_objects() {
    self::$user_options = new userOptions(0);

    // Initializing global 'cacher' object
    static::$cache = new classCache(classSupernova::$cache_prefix);
    $sn_cache = static::$cache;
    empty($sn_cache->tables) ? sys_refresh_tablelist() : false;
    empty($sn_cache->tables) ? die('DB error - cannot find any table. Halting...') : false;

    // Initializing global "config" object
    static::$config = new classConfig(classSupernova::$cache_prefix);

    // Initializing statics
    Vector::_staticInit(static::$config);
  }

  public static function init_debug_state() {
    if ($_SERVER['SERVER_NAME'] == 'localhost' && !defined('BE_DEBUG')) {
      define('BE_DEBUG', true);
    }
    // define('DEBUG_SQL_ONLINE', true); // Полный дамп запросов в рил-тайме. Подойдет любое значение
    define('DEBUG_SQL_ERROR', true); // Выводить в сообщении об ошибке так же полный дамп запросов за сессию. Подойдет любое значение
    define('DEBUG_SQL_COMMENT_LONG', true); // Добавлять SQL запрос длинные комментарии. Не зависим от всех остальных параметров. Подойдет любое значение
    define('DEBUG_SQL_COMMENT', true); // Добавлять комментарии прямо в SQL запрос. Подойдет любое значение
    // Включаем нужные настройки
    defined('DEBUG_SQL_ONLINE') && !defined('DEBUG_SQL_ERROR') ? define('DEBUG_SQL_ERROR', true) : false;
    defined('DEBUG_SQL_ERROR') && !defined('DEBUG_SQL_COMMENT') ? define('DEBUG_SQL_COMMENT', true) : false;
    defined('DEBUG_SQL_COMMENT_LONG') && !defined('DEBUG_SQL_COMMENT') ? define('DEBUG_SQL_COMMENT', true) : false;

    if (defined('BE_DEBUG') || static::$config->debug) {
      @define('BE_DEBUG', true);
      @ini_set('display_errors', 1);
      @error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
    } else {
      @define('BE_DEBUG', false);
      @ini_set('display_errors', 0);
    }

  }

  public static function checkReturnRef(&$ref1, &$ref2) {
    if (isset($ref1['id'])) {
      $ref1['id']++;
      pdump($ref1['id']);
      pdump($ref2['id']);
      if ($ref2['id'] == $ref1['id']) {
        pdump('ok');
      } else {
        pdie('failed');
      }
      $ref2['id']--;
      if ($ref2['id'] == $ref1['id']) {
        pdump('ok');
      } else {
        pdie('failed');
      }
    }

  }

}
