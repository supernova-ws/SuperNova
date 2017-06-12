<?php

use Vector\Vector;
use Common\GlobalContainer;

/**
 * Class classSupernova
 *
 * Singleton
 */
class classSupernova {
  /**
   * @var classSupernova $_sn
   */
  protected static $_sn;

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
  public static $cache_prefix = '';
  // public static $db_prefix = '';
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


  /*
  // protected static $user = null;
  // protected static $planet = null;
  // protected static $ally = null;
  */

  public static $data = array(); // Кэш данных - юзера, планеты, юниты, очередь, альянсы итд
  public static $locks = array(); // Информация о блокировках
  public static $queries = array(); // Кэш запросов

  // Массив $locator - хранит отношения между записями для быстрого доступа по тип_записи:тип_локации:ид_локации:внутренний_ид_записи=>информация
  // Для LOC_UNIT внутренний ИД - это SNID, а информация - это ссылка на запись `unit`
  // Для LOC_QUE внутренний ИД - это тип очереди, а информация - массив ссылок на `que`
  public static $locator = array(); // Кэширует соответствия между расположением объектов - в частности юнитов и очередей

  /**
   * Is header already rendered?
   *
   * @var bool $headerRendered
   */
  public static $headerRendered = false;

  // Кэш индексов - ключ MD5-строка от суммы ключевых строк через | - менять | на что-то другое перед поиском и назад - после поиска
  // Так же в индексах могут быть двойные вхождения - например, названия планет да и вообще
  // Придумать спецсимвол для NULL
  // protected static $indexes = array();

  /*
  TODO Кэш:
  1. Всегда дешевле использовать процессор, чем локальную память
  2. Всегда дешевле использовать локальную память, чем общую память всех процессов
  3. Всегда дешевле использовать общую память всех процессов, чем обращаться к БД

  Кэш - многоуровневый: локальная память-общая память-БД
  БД может быть сверхкэширующей - см. HyperNova. Это реализуется на уровне СН-драйвера БД
  Предусмотреть вариант, когда уровни кэширования совпадают, например когда нет xcache и используется общая память
  */
  //public static $cache; // Объект-кэшер - либо встроенная память, либо мемкэш с блокировками - находится внутри $db!!!!
  //public static $db; // Объект-БД - либок кэшер с блокировками, либо БД

  // protected static $info = array(); // Кэш информации - инфо о юнитах, инфо о группах итд

  // TODO Автоматически заполнять эту таблицу. В случае кэша в памяти - делать show table при обращении к таблице
  public static $location_info = array(
    LOC_USER => array(
      P_TABLE_NAME => 'users',
      P_ID => 'id',
      P_OWNER_INFO => array(),
    ),

    LOC_PLANET => array(
      P_TABLE_NAME => 'planets',
      P_ID => 'id',
      P_OWNER_INFO => array(
        LOC_USER => array(
          P_LOCATION => LOC_USER,
          P_OWNER_FIELD => 'id_owner',
        ),
      ),
    ),

    LOC_UNIT => array(
      P_TABLE_NAME => 'unit',
      P_ID => 'unit_id',
      P_OWNER_INFO => array(
        LOC_USER => array(
          P_LOCATION => LOC_USER,
          P_OWNER_FIELD => 'unit_player_id',
        ),
      ),
    ),

    LOC_QUE => array(
      P_TABLE_NAME => 'que',
      P_ID => 'que_id',
      P_OWNER_INFO => array(
        array(
          P_LOCATION => LOC_USER,
          P_OWNER_FIELD => 'que_player_id',
        ),

        array(
          P_LOCATION => LOC_PLANET,
          P_OWNER_FIELD => 'que_planet_id_origin',
        ),

        array(
          P_LOCATION => LOC_PLANET,
          P_OWNER_FIELD => 'que_planet_id',
        ),
      ),
    ),

    LOC_FLEET => array(
      P_TABLE_NAME => 'fleets',
      P_ID => 'fleet_id',
      P_OWNER_INFO => array(
        array(
          P_LOCATION => LOC_USER,
          P_OWNER_FIELD => 'fleet_owner',
        ),

        array(
          P_LOCATION => LOC_USER,
          P_OWNER_FIELD => 'fleet_target_owner',
        ),

        array(
          P_LOCATION => LOC_PLANET,
          P_OWNER_FIELD => 'fleet_start_planet_id',
        ),

        array(
          P_LOCATION => LOC_PLANET,
          P_OWNER_FIELD => 'fleet_end_planet_id',
        ),
      ),
    ),
  );

  /**
   * @return classSupernova
   */
  public static function sn() {
    if(!isset(self::$_sn)) {
      self::$_sn = new self();
    }

    return self::$_sn;
  }

  public function __construct() {

  }


  public static function log_file($message, $spaces = 0) {
    if(self::$debug) {
      self::$debug->log_file($message, $spaces);
    }
  }

  // Перепаковывает массив на заданную глубину, убирая поля с null
  public static function array_repack(&$array, $level = 0) {
    // TODO $lock_table не нужна тут
    if(!is_array($array)) return;

    foreach($array as $key => &$value) {
      if($value === null) {
        unset($array[$key]);
      } elseif($level > 0 && is_array($value)) {
        static::array_repack($value, $level - 1);
        if(empty($value)) unset($array[$key]);
      }
    }
  }


  // TODO Вынести в отдельный объект
  public static function cache_repack($location_type, $record_id = 0) {
    // Если есть $user_id - проверяем, а надо ли перепаковывать?
    if($record_id && isset(static::$data[$location_type][$record_id]) && static::$data[$location_type][$record_id] !== null) return;

    static::array_repack(static::$data[$location_type]);
    static::array_repack(static::$locator[$location_type], 3); // TODO У каждого типа локации - своя глубина!!!! Но можно и глубже ???
    static::array_repack(static::$queries[$location_type], 1);
  }
  public static function cache_clear($location_type, $hard = true) {
    //print("<br />CACHE CLEAR {$cache_id} " . ($hard ? 'HARD' : 'SOFT') . "<br />");
    if($hard && !empty(static::$data[$location_type])) {
      // Здесь нельзя делать unset - надо записывать NULL, что бы это отразилось на зависимых записях
      array_walk(static::$data[$location_type], function(&$item){$item = null;});
    }
    static::$locator[$location_type] = array();
    static::$queries[$location_type] = array();
    static::cache_repack($location_type); // Перепаковываем внутренние структуры, если нужно
  }
  public static function cache_clear_all($hard = true) {
    //print('<br />CACHE CLEAR ALL<br />');
    if($hard) {
      static::$data = array();
      static::cache_lock_unset_all();
    }
    static::$locator = array();
    static::$queries = array();
  }

  public static function cache_get($location_type, $record_id) {
    return isset(static::$data[$location_type][$record_id]) ? static::$data[$location_type][$record_id] : null;
  }
  public static function cache_isset($location_type, $record_id) {
    return isset(static::$data[$location_type][$record_id]) && static::$data[$location_type][$record_id] !== null;
  }
  /* Кэшируем запись в соответствующий кэш

  Писать в кэш:
  1. Если записи не существует в кэше
  2. Если стоит $force_overwrite
  3. Если во время транзакции существующая запись не заблокирована

  Блокировать запись:
  1. Если идет транзакция и запись не заблокирована
  2. Если не стоит скип-лок
  */
  public static function cache_set($location_type, $record_id, $record, $force_overwrite = false, $skip_lock = false) {
    // нет идентификатора - выход
    if(!($record_id = $record[static::$location_info[$location_type][P_ID]])) return;

    $in_transaction = static::db_transaction_check(false);
    if(
      $force_overwrite
      ||
      // Не заменяются заблокированные записи во время транзакции
      ($in_transaction && !static::cache_lock_get($location_type, $record_id))
      ||
      // !isset(static::$data[$location_type][$record_id]) || static::$data[$location_type][$record_id] === null
      !static::cache_isset($location_type, $record_id)
    ) {
      static::$data[$location_type][$record_id] = $record;
      if($in_transaction && !$skip_lock) {
        static::cache_lock_set($location_type, $record_id);
      }
    }
  }
  public static function cache_unset($cache_id, $safe_record_id) {
    // $record_id должен быть проверен заранее !
    if(isset(static::$data[$cache_id][$safe_record_id]) && static::$data[$cache_id][$safe_record_id] !== null) {
      // Выставляем запись в null
      static::$data[$cache_id][$safe_record_id] = null;
      // Очищаем кэш мягко - что бы удалить очистить связанные данные - кэш локаций и кэш запоросов и всё, что потребуется впредь
      static::cache_clear($cache_id, false);
    }
  }
  public static function cache_lock_get($location_type, $record_id) {
    return isset(static::$locks[$location_type][$record_id]);
  }
  public static function cache_lock_set($location_type, $record_id) {
    return static::$locks[$location_type][$record_id] = true; // Не всегда - от результата
  }
  public static function cache_lock_unset($location_type, $record_id) {
    if(isset(static::$locks[$location_type][$record_id]))
      unset(static::$locks[$location_type][$record_id]);
    return true; // Не всегда - от результата
  }
  public static function cache_lock_unset_all() {
    // Когда будем работать с xcache - это понадобиться, что бы снимать в xcache блокировки с записей
    // Пройти по массиву - снять блокировки для кэшера в памяти
    static::$locks = array();

    return true; // Не всегда - от результата
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
   * @return bool Текущий статус транзакции
   */
  public static function db_transaction_check($status = null) {
    $error_msg = false;
    if($status && !static::$db_in_transaction) {
      $error_msg = 'No transaction started for current operation';
    } elseif($status === null && static::$db_in_transaction) {
      $error_msg = 'Transaction is already started';
    }

    if($error_msg) {
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
    global $config;

    static::db_transaction_check(null);

    $level ? doquery('SET TRANSACTION ISOLATION LEVEL ' . $level) : false;

    static::$transaction_id++;
    doquery('START TRANSACTION');

    if($config->db_manual_lock_enabled) {
      $config->db_loadItem('var_db_manually_locked');
      $config->db_saveItem('var_db_manually_locked', SN_TIME_SQL);
    }

    static::$db_in_transaction = true;
    static::$locator = array();
    static::$queries = array();
    //print('<hr/>TRANSACTION START id' . static::$transaction_id . '<br />');

    return static::$transaction_id;
  }
  public static function db_transaction_commit() {
    static::db_transaction_check(true);

    static::cache_lock_unset_all();
    doquery('COMMIT');

    //print('<br/>TRANSACTION COMMIT id' . static::$transaction_id . '<hr />');
    static::$db_in_transaction = false;

    return static::$transaction_id++;
  }
  public static function db_transaction_rollback() {
    // static::db_transaction_check(true); // TODO - вообще-то тут тоже надо проверять есть ли транзакция
    static::cache_lock_unset_all();
    doquery('ROLLBACK');

    //print('<br/>TRANSACTION ROLLBACK id' . static::$transaction_id . '<hr />');
    static::$db_in_transaction = false;
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
    foreach($tables as $table_name => $condition) {
      self::$db->doquery("SELECT 1 FROM {{{$table_name}}}" . ($condition ? ' WHERE ' . $condition : ''));
    }
  }
  public static function db_query_select($query, $fetch = false, $skip_lock = false) {
    $select = strpos(strtoupper($query), 'SELECT') !== false;

    $query .= $select && $fetch ? ' LIMIT 1' : '';
    $query .= $select && !$skip_lock && static::db_transaction_check(false) ? ' FOR UPDATE' : '';

    $result = self::$db->doquery($query, $fetch);

    return $result;
  }
  public static function db_query_update($query) {
    return self::$db->doquery($query, false);
  }
  public static function db_query_delete($query) {
    return self::$db->doquery($query, false);
  }
  public static function db_query_insert($query) {
    return self::$db->doquery($query, false);
  }

  /**
   * Возвращает информацию о записи по её ID
   *
   * @param int $location_type
   * @param int|array $record_id_unsafe
   *    <p>int - ID записи</p>
   *    <p>array - запись пользователя с установленным полем P_ID</p>
   * @param bool $for_update @deprecated
   * @param string $fields @deprecated список полей или '*'/'' для всех полей
   * @param bool $skip_lock Указывает на то, что не нужно блокировать запись //TODO и не нужно сохранять в кэше
   * @return array|false
   *    <p>false - Нет записи с указанным ID</p>
   *    <p>array - запись</p>
   */
  public static function db_get_record_by_id($location_type, $record_id_unsafe, $for_update = false, $fields = '*', $skip_lock = false) {
    $id_field = static::$location_info[$location_type][P_ID];
    // $record_id = intval(is_array($record_id) && isset($record_id[$id_field]) ? $record_id[$id_field] : $record_id);
    $record_id_safe = idval(is_array($record_id_unsafe) && isset($record_id_unsafe[$id_field]) ? $record_id_unsafe[$id_field] : $record_id_unsafe);

    return static::db_get_record_list($location_type, "`{$id_field}` = {$record_id_safe}", true, false);
  }

  public static function db_get_record_list($location_type, $filter = '', $fetch = false, $no_return = false) {
    $query_cache = &static::$queries[$location_type][$filter];

    if(!isset($query_cache) || $query_cache === null) {
      $location_info = &static::$location_info[$location_type];
      $id_field = $location_info[P_ID];
      $query_cache = array();

      if(static::db_transaction_check(false)) {
        // Проходим по всем родителям данной записи
        foreach($location_info[P_OWNER_INFO] as $owner_data) {
          $owner_location_type = $owner_data[P_LOCATION];
          $parent_id_list = array();
          // Выбираем родителей данного типа и соответствующие ИД текущего типа
          $query = static::db_query_select(
            "SELECT
              distinct({{{$location_info[P_TABLE_NAME]}}}.{$owner_data[P_OWNER_FIELD]}) AS parent_id
            FROM {{{$location_info[P_TABLE_NAME]}}}" .
            ($filter ? ' WHERE ' . $filter : '') .
            ($fetch ? ' LIMIT 1' : ''),
            false,
            true
          );

          while($row = db_fetch($query)) {
            // Исключаем из списка родительских ИД уже заблокированные записи
            if(!static::cache_lock_get($owner_location_type, $row['parent_id']))
              $parent_id_list[$row['parent_id']] = $row['parent_id'];
          }

          // Если все-таки какие-то записи еще не заблокированы - вынимаем текущие версии из базы
          if($indexes_str = implode(',', $parent_id_list)) {
            $parent_id_field = static::$location_info[$owner_location_type][P_ID];
            static::db_get_record_list($owner_location_type,
              $parent_id_field . (count($parent_id_list) > 1 ? " IN ({$indexes_str})" : " = {$indexes_str}"), $fetch, true);
          }
        }
      }

      $query = static::db_query_select(
        "SELECT * FROM {{{$location_info[P_TABLE_NAME]}}}" . (($filter = trim($filter)) ? " WHERE {$filter}" : '')
      );
      while($row = db_fetch($query)) {
        static::cache_set($location_type, $row[$id_field], $row);
        $query_cache[$row[$id_field]] = &static::$data[$location_type][$row[$id_field]];
      }
    }

    if($no_return) {
      return true;
    } else {
      $result = false;
      if(is_array($query_cache)) {
        foreach($query_cache as $key => $value) {
          $result[$key] = $value;
          if($fetch) break;
        }
      }
      return $fetch ? (is_array($result) ? reset($result) : false) : $result;
    }
  }

  public static function db_upd_record_by_id($location_type, $record_id, $set) {
    if(!($record_id = idval($record_id)) || !($set = trim($set))) return false;

    $location_info = &static::$location_info[$location_type];
    $id_field = $location_info[P_ID];
    $table_name = $location_info[P_TABLE_NAME];
    if($result = static::db_query_update("UPDATE {{{$table_name}}} SET {$set} WHERE `{$id_field}` = {$record_id}")) // TODO Как-то вернуть может быть LIMIT 1 ?
    {
      if(static::$db->db_affected_rows()) {
        // Обновляем данные только если ряд был затронут
        // TODO - переделать под работу со структурированными $set

        // Тут именно так, а не cache_unset - что бы в кэшах автоматически обновилась запись. Будет нужно на будущее
        static::$data[$location_type][$record_id] = null;
        // Вытаскиваем обновленную запись
        static::db_get_record_by_id($location_type, $record_id);
        static::cache_clear($location_type, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }
  public static function db_upd_record_list($location_type, $condition, $set) {
    if(!($set = trim($set))) return false;

    $condition = trim($condition);
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];

    if($result = static::db_query_update("UPDATE {{{$table_name}}} SET " . $set . ($condition ? ' WHERE ' . $condition : ''))) {

      if(static::$db->db_affected_rows()) { // Обновляем данные только если ряд был затронут
        // Поскольку нам неизвестно, что и как обновилось - сбрасываем кэш этого типа полностью
        // TODO - когда будет структурированный $condition и $set - перепаковывать данные
        static::cache_clear($location_type, true);
      }
    }

    return $result;
  }

  public static function db_ins_record($location_type, $set) {
    $set = trim($set);
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    if($result = static::db_query_insert("INSERT INTO `{{{$table_name}}}` SET {$set}")) {
      if(static::$db->db_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        $record_id = db_insert_id();
        // Вытаскиваем запись целиком, потому что в $set могли быть "данные по умолчанию"
        $result = static::db_get_record_by_id($location_type, $record_id);
        // Очищаем второстепенные кэши - потому что вставленная запись могла повлиять на результаты запросов или локация или еще чего
        // TODO - когда будет поддержка изменения индексов и локаций - можно будет вызывать её
        static::cache_clear($location_type, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }

  public static function db_del_record_by_id($location_type, $safe_record_id)
  {
    if(!($safe_record_id = idval($safe_record_id))) return false;

    $location_info = &static::$location_info[$location_type];
    $id_field = $location_info[P_ID];
    $table_name = $location_info[P_TABLE_NAME];
    if($result = static::db_query_delete("DELETE FROM `{{{$table_name}}}` WHERE `{$id_field}` = {$safe_record_id}"))
    {
      if(static::$db->db_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        static::cache_unset($location_type, $safe_record_id);
      }
    }

    return $result;
  }
  public static function db_del_record_list($location_type, $condition)
  {
    if(!($condition = trim($condition))) return false;

    $location_info = &static::$location_info[$location_type];
    $table_name = $location_info[P_TABLE_NAME];

    if($result = static::db_query_delete("DELETE FROM `{{{$table_name}}}` WHERE {$condition}"))
    {
      if(static::$db->db_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        // Обнуление кэша, потому что непонятно, что поменялось
        static::cache_clear($location_type);
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
   * @param bool $for_update @deprecated
   * @param string $fields @deprecated список полей или '*'/'' для всех полей
   * @param null $player
   * @param bool|null $player Признак выбора записи пользователь типа "игрок"
   *    <p>null - Можно выбрать запись любого типа</p>
   *    <p>true - Выбирается только запись типа "игрок"</p>
   *    <p>false - Выбирается только запись типа "альянс"</p>
   * @return array|false
   *    <p>false - Нет записи с указанным ID и $player</p>
   *    <p>array - запись типа $user</p>
   */
  public static function db_get_user_by_id($user_id_unsafe, $for_update = false, $fields = '*', $player = null)
  {
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
  public static function db_get_user_by_username($username_unsafe, $for_update = false, $fields = '*', $player = null, $like = false)
  {
    // TODO Проверить, кстати - а везде ли нужно выбирать юзеров или где-то все-таки ищутся Альянсы ?
    if(!($username_unsafe = trim($username_unsafe))) return false;

    $user = null;
    if(is_array(static::$data[LOC_USER]))
    foreach(static::$data[LOC_USER] as $user_id => $user_data)
    {
      if(is_array($user_data) && isset($user_data['username']))
      {
        // проверяем поле
        // TODO Возможно есть смысл всегда искать по strtolower - но может игрок захочет переименоваться с другим регистром? Проверить!
        if((!$like && $user_data['username'] == $username_unsafe) || ($like && strtolower($user_data['username']) == strtolower($username_unsafe)))
        {
          // $user_as_ally = intval($user_data['user_as_ally']);
          $user_as_ally = idval($user_data['user_as_ally']);
          if($player === null || ($player === true && !$user_as_ally) || ($player === false && $user_as_ally))
          {
            $user = $user_data;
            break;
          }
        }
      }
    }

    if($user === null)
    {
      // Вытаскиваем запись
      $username_safe = db_escape($like ? strtolower($username_unsafe) : $username_unsafe); // тут на самом деле strtolower() лишняя, но пусть будет

      $user = static::db_query_select(
        "SELECT * FROM {{users}} WHERE `username` " . ($like ? 'LIKE' : '='). " '{$username_safe}'",
        true
      );
      static::cache_set(LOC_USER, $user['id'], $user); // В кэш-юзер так же заполнять индексы
    }

    return $user;
  }
  // UNUSED
  public static function db_get_user_by_email($email_unsafe, $use_both = false, $for_update = false, $fields = '*') {
    if(!($email_unsafe = strtolower(trim($email_unsafe)))) return false;

    $user = null;
    if(is_array(static::$data[LOC_USER])) {
      foreach(static::$data[LOC_USER] as $user_id => $user_data) {
        if(is_array($user_data) && isset($user_data['email_2'])) {
          // проверяем поле
          if(strtolower($user_data['email_2']) == $email_unsafe || ($use_both && strtolower($user_data['email']) == $email_unsafe)) {
            $user = $user_data;
            break;
          }
        }
      }
    }

    if($user === null) {
      // Вытаскиваем запись
      $email_safe = db_escape($email_unsafe);
      $user = static::db_query_select(
        "SELECT * FROM {{users}} WHERE LOWER(`email_2`) = '{$email_safe}'" . ($use_both ? " OR LOWER(`email`) = '{$email_safe}'" : ''),
        true
      );

      static::cache_set(LOC_USER, $user['id'], $user); // В кэш-юзер так же заполнять индексы
    }

    return $user;
  }
  public static function db_get_user_by_where($where_safe, $for_update = false, $fields = '*') {
    $user = null;
    // TODO переделать на индексы

    if($user === null && !empty($where_safe)) {
      // Вытаскиваем запись
      $user = static::db_query_select(
        "SELECT * FROM {{users}} WHERE {$where_safe}",
        true
      );

      static::cache_set(LOC_USER, $user['id'], $user); // В кэш-юзер так же заполнять индексы
    }

    return $user;
  }















  public static function db_unit_time_restrictions($date = SN_TIME_NOW)
  {
    $date = is_numeric($date) ? "FROM_UNIXTIME({$date})" : "'{$date}'";
    return
      "(unit_time_start IS NULL OR unit_time_start <= {$date}) AND
    (unit_time_finish IS NULL OR unit_time_finish = '1970-01-01 03:00:00' OR unit_time_finish >= {$date})";
  }
  public static function db_get_unit_by_id($unit_id, $for_update = false, $fields = '*')
  {
    // TODO запихивать в $data[LOC_LOCATION][$location_type][$location_id]
    $unit = static::db_get_record_by_id(LOC_UNIT, $unit_id, $for_update, $fields);
    if(is_array($unit))
    {
      static::$locator[LOC_UNIT][$unit['unit_location_type']][$unit['unit_location_id']][$unit['unit_snid']] = &static::$data[LOC_UNIT][$unit_id];
    }

    return $unit;
  }

  public static function db_get_unit_list_by_location($user_id = 0, $location_type, $location_id)
  {
    if(!($location_type = idval($location_type)) || !($location_id = idval($location_id))) return false;

    $query_cache = &static::$locator[LOC_UNIT][$location_type][$location_id];
    if(!isset($query_cache))
    {
      $got_data = static::db_get_record_list(LOC_UNIT, "unit_location_type = {$location_type} AND unit_location_id = {$location_id} AND " . static::db_unit_time_restrictions());
      if(is_array($got_data))
      {
        foreach($got_data as $unit_id => $unit_data)
        {
          // static::$data[LOC_LOCATION][$location_type][$location_id][$unit_data['unit_snid']] = &static::$data[LOC_UNIT][$unit_id];
          $query_cache[$unit_data['unit_snid']] = &static::$data[LOC_UNIT][$unit_id];
        }
      }
    }

    $result = false;
    if(is_array($query_cache)) {
      foreach($query_cache as $key => $value) {
        $result[$key] = $value;
      }
    }

    return $result;
  }
  public static function db_get_unit_by_location($user_id = 0, $location_type, $location_id, $unit_snid = 0, $for_update = false, $fields = '*')
  {
    static::db_get_unit_list_by_location($user_id, $location_type, $location_id);

    return $unit_snid ? static::$locator[LOC_UNIT][$location_type][$location_id][$unit_snid] : static::$locator[LOC_UNIT][$location_type][$location_id];
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
  public static function db_que_list_by_type_location($user_id, $planet_id = null, $que_type = false, $for_update = false)
  {
    if(!$user_id)
    {
      pdump(debug_backtrace());
      die('No user_id for que_get_que()');
    }

    $ques = array();

    $query = array();

    if($user_id = idval($user_id))
      $query[] = "`que_player_id` = {$user_id}";

    if($que_type == QUE_RESEARCH || $planet_id === null)
      $query[] = "`que_planet_id` IS NULL";
    elseif($planet_id)
      $query[] = "(`que_planet_id` = {$planet_id}" . ($que_type ? '' : ' OR que_planet_id IS NULL') . ")";
    if($que_type)
      $query[] = "`que_type` = {$que_type}";

    $ques['items'] = static::db_get_record_list(LOC_QUE, implode(' AND ', $query));

    return que_recalculate($ques);
  }










































  public static function db_changeset_revert()
  {
    // TODO Для этапа 1 - достаточно чистить только те таблицы, что были затронуты
    // Для этапа 2 - чистить только записи
    // Для этапа 3 - возвращать всё
    static::cache_clear_all(true);
  }



































  // que_process не всегда должна работать в режиме прямой работы с БД !! Она может работать и в режиме эмуляции
  // !!!!!!!! После que_get брать не [0] элемент, а first() - тогда можно в индекс элемента засовывать que_id из таблицы











  // TODO - это вообще-то надо хранить в конфигурации
  public static function db_get_user_player_username_last_registered() {
    $user = static::db_query_select(
      'SELECT * FROM `{{users}}` WHERE `user_as_ally` IS NULL ORDER BY `id` DESC',
      true
    );
    static::cache_set(LOC_USER, $user['id'], $user);
    return isset($user['username']) ? $user['username'] : '';
  }

  // Это для поиска по кэшу
  protected static function db_get_record_by_field($location_type) {
  }

  // Для модулей - регистрация юнитов
  public static function unit_register() {

  }


  public static function loadFileSettings () {
    $dbsettings = array();

    require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);
    //self::$db_prefix = $dbsettings['prefix'];
    self::$cache_prefix = !empty($dbsettings['cache_prefix']) ? $dbsettings['cache_prefix'] : $dbsettings['prefix'];
    self::$db_name = $dbsettings['name'];
    self::$sn_secret_word = $dbsettings['secretword'];

    self::$gc = new GlobalContainer(array(
      'cachePrefix' => self::$cache_prefix,
    ));

    unset($dbsettings);
  }

  public static function init_global_objects() {
    global $sn_cache, $config, $debug;

    $debug = self::$debug = self::$gc->debug;
    self::$db = self::$gc->db;
    self::$db->sn_db_connect();

    self::$user_options = new userOptions(0);

    // Initializing global 'cache' object
    $sn_cache = static::$cache = self::$gc->cache;
    empty(classSupernova::$db->schema()->getSnTables()) && die('DB error - cannot find any table. Halting...');

    // Initializing global "config" object
    $config = static::$config = self::$gc->config;

    // Initializing statics
    Vector::_staticInit(static::$config);
  }

  /**
   * @param int    $newStatus
   * @param string $newMessage
   *
   * @return int
   */
  public static function gameDisable($newStatus = GAME_DISABLE_REASON, $newMessage = '') {
    $old_server_status = intval(self::$config->db_loadItem('game_disable'));
    self::$config->db_saveItem('game_disable', $newStatus);

    return $old_server_status;
  }

  public static function gameEnable() {
    self::$config->db_saveItem('game_disable', GAME_DISABLE_NONE);
  }

}
