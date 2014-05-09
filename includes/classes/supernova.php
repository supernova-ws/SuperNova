<?php

class classSupernova
{
  public static $db_in_transaction = false;
  public static $transaction_id = 0;

  public $options = array();

  // protected static $user = null;
  // protected static $planet = null;
  // protected static $ally = null;

  /*
  protected static $users = array();
  protected static $planets = array();
  protected static $units = array();
  protected static $ques = array();
  protected static $allies = array();
  */

  public static $data = array(); // Кэш данных - юзера, планеты, юниты, очередь, альянсы итд
  public static $locks = array(); // Информация о блокировках
  public static $queries = array(); // Кэш запросов

  public static $db_changeset = array(); // Накопительный массив изменений
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
  public static $cache; // Объект-кэшер - либо встроенная память, либо мемкэш с блокировками - находится внутри $db!!!!
  public static $db; // Объект-БД - либок кэшер с блокировками, либо БД

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
        array(
          P_LOCATION => LOC_USER,
          P_OWNER_FIELD => 'id_owner',
        )
      ),
    ),
    LOC_UNIT => array(
      P_TABLE_NAME => 'unit',
      P_ID => 'unit_id',
      P_OWNER_INFO => array(
        array(
          P_LOCATION => LOC_USER,
          P_OWNER_FIELD => 'unit_player_id',
        )
      ),
    ),
    LOC_QUE => array(
      P_TABLE_NAME => 'que',
      P_ID => 'que_id',
      P_OWNER_INFO => array(
        array(
          P_LOCATION => LOC_USER,
          P_OWNER_FIELD => 'que_player_id',
        )
      ),
    ),
  );

  // Перепаковывает массив на заданную глубину, убирая поля с null
  public static function array_repack(&$array, &$lock_table, $level = 0)
  {
    if(!is_array($array)) return;

    foreach($array as $key => &$value)
    {
      if($value === null)
      {
        unset($array[$key]);
        // Если мы на самом нижнем уровне - снимаем блокировку с записи с соответствующим ID
        if($level == 0 && is_array($lock_table))
        {
          unset($lock_table[$key]);
        }
      }
      elseif($level > 0 && is_array($value))
      {
        static::array_repack($value, $lock_table, $level - 1);
      }
    }
  }

  // TODO Вынести в отдельный объект
  public static function cache_clear_all($hard = true)
  {
    if($hard)
    {
      static::$data = array();
      static::$locks = array();
    }
    static::$queries = array();
    // static::$indexes = array();
  }
  // TODO Через вызов функции посмотреть, где она используется и как её можно оптимизировать
  public static function cache_clear($cache_id, $hard = true)
  {
    if($hard)
    {
      static::$data[$cache_id] = array();
      static::$data[LOC_LOCATION][$cache_id] = array();
      static::$locks[$cache_id] = array();
    }
    static::$queries[$cache_id] = array();
  }
  public static function cache_repack($cache_id, $record_id = 0)
  {
    // Если есть $user_id - проверяем, а надо ли перепаковывать?
    if($record_id && isset(static::$data[$cache_id][$record_id]) && static::$data[$cache_id][$record_id] !== null) return;

    static::array_repack(static::$data[$cache_id], static::$locks[$cache_id]);
    static::array_repack(static::$data[LOC_LOCATION][$cache_id], $cork = null, 2); // TODO - проверить перепаковку LOC_LOCATION
    static::array_repack(static::$queries[$cache_id], $cork = null, 1);
    // TODO - а вот тут непонятно. Надо ли вообще это перепаковывать и будет ли польза?
    // На самом деле если запись заблокирована - то она заблокирована. Другой записи с таким же ИД быть не может, а вот инфа может заново поменятся и сразу будет блокированной
    // Впрочем, тут есть сторонние эффекты
    // static::array_repack(static::$locks[LOC_USER]);
  }
  public static function cache_unset($cache_id, $record_id)
  {
    if(!$record_id)
    {
      static::cache_clear($cache_id, true);
    }
    elseif($record_id && isset(static::$data[$cache_id][$record_id]) && static::$data[$cache_id][$record_id] !== null)
    {
      unset(static::$data[$cache_id][$record_id]);
      // static::db_get_user_by_id($user_id);
      static::cache_repack($cache_id, $record_id); // Перепаковываем внутренние структуры, если нужно
    }
  }
  public static function cache_get($cache_id, $record_id)
  {
    // Пока не нужна
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
  public static function cache_set($location_type, $record_id, $record, $force_overwrite = false, $skip_lock = false)
  {
    // нет идентификатора - выход
    if(!($record_id = $record[static::$location_info[$location_type][P_ID]])) return;

    $in_transaction = static::db_transaction_check(false);
    if(
      $force_overwrite
      ||
      // Не заменяются заблокированные записи во время транзакции
      ($in_transaction && !static::cache_lock_get($location_type, $record_id))
      ||
      !isset(static::$data[$location_type][$record_id])
      ||
      static::$data[$location_type][$record_id] === null
    )
    {
      static::$data[$location_type][$record_id] = $record;
      if($in_transaction && !$skip_lock)
      {
        static::cache_lock_set($location_type, $record_id);
      }
    }
    /*

    // находимся в транзакции и надо пропустить блокировку - выход, $force_overwrite игнорируется
    $in_transaction = static::db_transaction_check(false);
    if(!$force_overwrite && $in_transaction && $skip_lock) return $record;
    // Дальше $skip_lock не используется

    // Кэш обязательно нужно обновить, если запись не залочена и происходит транзакция
    $force_overwrite = $force_overwrite || ($in_transaction && !static::cache_lock_get($location_type, $record_id));
    // Дальше $force_overwrite указывает на безусловное обновление кэша

    // Если запись существует и её не нужно обновлять - вернуть её значение
    if(!$force_overwrite && ($current_record = isset(static::$data[$location_type][$record_id]) ? static::$data[$location_type][$record_id] : null) !== null) return $current_record;

    static::$data[$location_type][$record_id] = $record;
    if(!$skip_lock && $in_transaction)
    {
      static::cache_lock_set($location_type, $record_id);
    }

    // TODO Перестраивать индексы - может пакетом ?

    return $record;
    */
  }
  public static function cache_lock_get($location_type, $record_id)
  {
    return isset(static::$locks[$location_type][$record_id]);
  }
  public static function cache_lock_set($location_type, $record_id)
  {
    return static::$locks[$location_type][$record_id] = true;
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
  public static function db_transaction_check($status = null)
  {
    $error_msg = false;
    if($status && !static::$db_in_transaction)
    {
      $error_msg = 'No transaction started for current operation';
    }
    elseif($status === null && static::$db_in_transaction)
    {
      $error_msg = 'Transaction is already started';
    }

    if($error_msg)
    {
      // TODO - Убрать позже
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sn_db_transaction_check() - ' . $error_msg . '</h1>');
      $backtrace = debug_backtrace();
      array_shift($backtrace);
      pdump($backtrace);
      die($error_msg);
    }

    return static::$db_in_transaction;
  }
  public static function db_transaction_start($level = '')
  {
    static::db_transaction_check();

    if($level)
    {
      doquery('SET TRANSACTION ISOLATION LEVEL ' . $level);
    }
    doquery('START TRANSACTION');

    static::$db_in_transaction = true;
    static::$transaction_id++;

    static::cache_clear_all();

    return static::$transaction_id;
  }
  public static function db_transaction_commit()
  {
    static::db_transaction_check(true);
    doquery('COMMIT');
    static::$db_in_transaction = false;
    static::$transaction_id++;
    static::$locks = array();

    return static::$transaction_id;
  }
  public static function db_transaction_rollback()
  {
    // static::db_transaction_check(true); // TODO - вообще-то тут тоже надо проверять есть ли транзакция
    doquery('ROLLBACK');
    static::$db_in_transaction = false;
    static::$transaction_id++;
    static::$locks = array();

    return static::$transaction_id;
  }
  /**
   * Блокирует указанные таблицу/список таблиц
   *
   * @param string|array $tables Таблица/список таблиц для блокировки. Названия таблиц - без префиксов
   * <p>string - название таблицы для блокировки</p>
   * <p>array - массив, где ключ - имя таблицы, а значение - условия блокировки элементов</p>
   */
  public static function db_lock_tables($tables)
  {
    $tables = is_array($tables) ? $tables : array($tables => '');
    foreach($tables as $table_name => $condition)
    {
      doquery("SELECT 1 FROM {{{$table_name}}}" . ($condition ? ' WHERE ' . $condition : ''));
    }
  }
  public static function db_query($query, $fetch = false, $skip_lock = false)
  {
    $select = strpos(strtoupper($query), 'SELECT') !== false;

    $query .= $select && $fetch ? ' LIMIT 1' : '';
    $query .= !$skip_lock && $select && static::db_transaction_check(false) ? ' FOR UPDATE' : '';
    return doquery($query, $fetch);
  }

  /**
   * Возвращает информацию о пользователе по его ID
   *
   * @param int $location_type
   * @param int|array $record_id
   *    <p>int - ID пользователя</p>
   *    <p>array - запись пользователя с установленным полем ['id']</p>
   * @param bool $for_update @deprecated
   * @param string $fields @deprecated список полей или '*'/'' для всех полей
   * @param bool $skip_lock Указывает на то, что не нужно блокировать запись //TODO и не нужно сохранять в кэше
   * @internal param int $user_owner_id ID юзера-владельца записи для блокировки
   *    <p>0 - Узнать самостоятельно</p>
   *    <p>int - ID юзера-владельца</p>
   * @return array|false
   * <p>false - Нет записи с указанным ID</p>
   * <p>array - запись</p>
   * @todo $fields
   */
  public static function db_get_record_by_id($location_type, $record_id, $for_update = false, $fields = '*', $skip_lock = false)
  { // $fields = $fields ? $fields : '*'; // TODO $fields
    $location_info = &static::$location_info[$location_type];
    $id_field = $location_info[P_ID];

    $record_id = intval(is_array($record_id) && isset($record_id[$id_field]) ? $record_id[$id_field] : $record_id);
    if(!$record_id) return false;

    $in_transaction = static::db_transaction_check(false);

    // Если не пропускаем блокировку - вытаскиваем все родительские записи
    // Потому что если $skip_lock - это, скорее всего, рекурсивный вызов
    if(!$skip_lock && !empty($location_info[P_OWNER_INFO]))
    {
      // TODO && !empty($location_info[P_OWNER_INFO])
      $record = static::db_get_record_by_id($location_type, $record_id, false, 'id', true);
      if(!$record) return $record;

      foreach($location_info[P_OWNER_INFO] as $owner_data)
      {
        static::db_get_record_by_id($owner_data[P_LOCATION], $record[$owner_data[P_OWNER_FIELD]], $for_update, '*');
      }
    }

    if(
      !isset(static::$data[$location_type][$record_id]) || static::$data[$location_type][$record_id] === null
      // Если запись уже есть - то перечитываем её только в транзакции если нет блокировки
      // Хотя если у нас идет $skip_lock и запись уже есть в кэше - зачем нам её перечитывать ?!
      || ($in_transaction && !$skip_lock && !static::cache_lock_get($location_type, $record_id))
    )
    {
      $table_name = static::$location_info[$location_type][P_TABLE_NAME];
      $record = static::db_query("SELECT * FROM {{{$table_name}}} WHERE `{$id_field}` = {$record_id}", true, $skip_lock);
      static::cache_set($location_type, $record_id, $record, $skip_lock);
    }

    return static::$data[$location_type][$record_id];
  }









  public static function db_get_record_list($location_type, $filter = '', $fetch = false)
  {
    $query_cache = &static::$queries[$location_type][$filter];
    if(!isset($query_cache))
    {
      $location_info = &static::$location_info[$location_type];
      $id_field = $location_info[P_ID];
      $query_cache = array();

      if(static::db_transaction_check(false))
      {
        foreach($location_info[P_OWNER_INFO] as $location_data)
        {
          $parent_location = &static::$location_info[$location_info[P_OWNER_INFO][P_LOCATION]];
          /*
          // Блокируем таблицу - родителя
          // TODO - переделать позже
          // На самом деле это не совсем правильно - если у таблицы-родителя будут другие родители, то они не заблокируются
          // Но пока везде указаны юзеры - это нормально и быстро
          static::db_query(
            "SELECT 1 FROM
              {{{$location_info[P_TABLE_NAME]}}} AS t1
              JOIN {{{$parent_location[P_TABLE_NAME]}}} AS t2
                ON t2.{$parent_location[P_TABLE_NAME][P_ID]} = t1.{$location_info[P_ID]}");
          */
          static::db_query(
            "SELECT 1 FROM
              {{{$location_info[P_TABLE_NAME]}}} AS t1
              JOIN {{{$parent_location[P_TABLE_NAME]}}} AS t2
            WHERE t2.{$parent_location[P_TABLE_NAME][P_ID]} = t1.{$location_info[P_ID]}", $fetch);
        }
      }

      $query = static::db_query(
        "SELECT {$id_field} FROM {{{$location_info[P_TABLE_NAME]}}}" .
        (($filter = trim($filter)) ? " WHERE {$filter}" : ''), $fetch
      );
      while($row = mysql_fetch_assoc($query))
      {
        static::db_get_record_by_id($location_type, $row[$id_field]);
        $query_cache[$row[$id_field]] = &static::$data[$location_type][$row[$id_field]];
      }
      /*
      $query = static::db_query(
        "SELECT * FROM {{{$location_info[P_TABLE_NAME]}}}" .
        (($filter = trim($filter)) ? " WHERE {$filter}" : '')
      );
      while($row = mysql_fetch_assoc($query))
      {
        static::cache_set($location_type, $row[$id_field], $row); // В кэш-юзер так же заполнять индексы
        $query_cache[$row[$id_field]] = &static::$data[$location_type][$row[$id_field]];
      }
      */
    }

    return $fetch ? (is_array($query_cache) ? reset($query_cache) : false) : $query_cache;
  }






























  public static function db_upd_record_by_id($location_type, $record_id, $set)
  {
    // При апдейте единичном кэш запросов в принципе сбрасывать не надо - потому что если на эту запись есть ссылки, то её проапдейтит при изменении $data
    // TODO - $set давать в массиве
    // TODO Проверять по $set изменения в индексах и запросах
    if(!($record_id = intval($record_id) || !($set = trim($set)))) return false;

    $id_field = static::$location_info[$location_type][P_ID];
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    if($result = static::db_query("UPDATE {{{$table_name}}} SET {$set} WHERE `{$id_field}` = {$record_id}")) // TODO Как-то вернуть может быть LIMIT 1 ?
    {
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        static::$data[$location_type][$record_id] = null; // Тут именно так, а не cache_unset - что бы в кэшах автоматически обновилась запись
        static::db_get_record_by_id($location_type, $record_id);
        // TODO Сейчас сбрасывать запросы из-за того, что $set может серъезно поменять результат кэшированного запроса
        static::cache_clear($location_type, false); // Мягкий сброс - только $queries
        // TODO А вот индексы, возможно, прийдется обновить - если поменялись ключевые поля, по которым строились индексы. Но это - попозже
      }
    }

    return $result;
  }
  public static function db_upd_record_list($location_type, $condition, $set)
  {
    if(!($set = trim($set))) return false;

    $condition = trim($condition);
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    if($result = static::db_query("UPDATE {{{$table_name}}} SET " . $set . ($condition ? ' WHERE ' . $condition : '')))
    {
      // TODO Сейчас сбрасывать всю инфу о юзерах потому, что $set может серъезно поменять результат кэшированного запроса
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        // TODO Сейчас сбрасывать запросы из-за того, что $set может серъезно поменять результат кэшированного запроса
        static::cache_clear($location_type, true);
      }
    }

    return $result;
  }
  public static function db_ins_record($location_type, $set)
  {
    $set = trim($set);
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    if($result = static::db_query("INSERT INTO `{{{$table_name}}}` SET {$set}"))
    {
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        // TODO Сейчас сбрасывать запросы из-за того, что $set может серъезно поменять результат кэшированного запроса
        static::cache_clear($location_type, false); // Мягкий сброс - только $queries

        $record_id = mysql_insert_id();
        $record = static::db_get_record_by_id($location_type, $record_id);
        static::cache_set($location_type, $record_id, $record, true);
      }
    }

    return $result;
  }
  public static function db_del_record_by_id($location_type, $record_id)
  {
    if(!($record_id = intval($record_id))) return false;

    $location_info = &static::$location_info[$location_type];
    $id_field = $location_info[P_ID];
    $table_name = $location_info[P_TABLE_NAME];
    if($result = static::db_query("DELETE FROM `{{{$table_name}}}` WHERE `{$id_field}` = {$record_id}"))
    {
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        static::cache_unset($location_type, $record_id);
        static::cache_clear($location_type, false);
      }
    }

    return $result;
  }
  public static function db_del_record_list($location_type, $condition)
  {
    if(!($condition = trim($condition))) return false;

    $location_info = &static::$location_info[$location_type];
    $table_name = $location_info[P_TABLE_NAME];
    if($result = static::db_query("DELETE FROM `{{{$table_name}}}` WHERE {$condition}"))
    {
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        // TODO Сейчас сбрасывать запросы из-за того, что $set может серъезно поменять результат кэшированного запроса
        static::cache_clear($location_type);
      }
    }

    return $result;
  }



  // Работа с пользователями
  /**
   * Возвращает информацию о пользователе по его ID
   *
   * @param int|array $user_id
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
   * <p>false - Нет записи с указанным ID и $player</p>
   * <p>array - запись типа $user</p>
   * @todo $fields
   */
  public static function db_get_user_by_id($user_id, $for_update = false, $fields = '*', $player = null)
  {
    $user = static::db_get_record_by_id(LOC_USER, $user_id, $for_update, $fields);
    return (is_array($user) &&
    (
      $player === null
      ||
      ($player === true && !$user['user_as_ally'])
      ||
      ($player === false && $user['user_as_ally'])
    )) ? $user : false;
  }
  // $player
  //   true - только игроки
  //   false - только Альянсы
  //   null - всё равно
  // TODO Проверить, кстати - а везде ли нужно выбирать юзеров или где-то все-таки ищутся Альянсы ?
  // TODO Индекс по username сюда
  public static function db_get_user_by_username($username, $for_update = false, $fields = '*', $player = null, $like = false)
  {
    if(!($username = trim($username))) return false;

    $user = null;
    // TODO переделать на индексы
    foreach(static::$data[LOC_USER] as $user_id => $user_data)
    {
      if(is_array($user_data) && isset($user_data['username']))
      {
        // проверяем поле
        // TODO Возможно есть смысл всегда искать по strtolower - но может игрок захочет переименоваться с другим регистром? Проверить!
        if((!$like && $user_data['username'] == $username) || ($like && strtolower($user_data['username']) == strtolower($username)))
        {
          $user_as_ally = intval($user_data['user_as_ally']);
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
      $username_safe = mysql_real_escape_string($like ? strtolower($username) : $username); // тут на самом деле strtolower() лишняя, но пусть будет
      $user = static::db_query(
        "SELECT * FROM {{users}} WHERE `username` " . ($like ? 'LIKE' : '='). " '{$username_safe}'"
      , true);
      static::cache_set(LOC_USER, $user['id'], $user); // В кэш-юзер так же заполнять индексы
    }

    return $user;
  }
  public static function db_get_user_by_email($email, $use_both = false, $for_update = false, $fields = '*')
  {
    if(!($email = strtolower(trim($email)))) return false;

    $user = null;
    // TODO переделать на индексы
    foreach(static::$data[LOC_USER] as $user_id => $user_data)
    {
      if(is_array($user_data) && isset($user_data['email_2']))
      {
        // проверяем поле
        if(strtolower($user_data['email_2']) == $email || ($use_both && strtolower($user_data['email_2']) == $email))
        {
          $user = $user_data;
          break;
        }
      }
    }

    if($user === null)
    {
      // Вытаскиваем запись
      $email_safe = mysql_real_escape_string($email);
      $user = static::db_query(
        "SELECT * FROM {{users}} WHERE LOWER(`email_2`) = '{$email_safe}'" .
        ($use_both ? " OR LOWER(`email`) = '{$email_safe}'" : '')
      , true);

      static::cache_set(LOC_USER, $user['id'], $user); // В кэш-юзер так же заполнять индексы
    }

    return $user;
  }
  public static function db_set_user_by_id($user_id, $set)
  {
    return static::db_upd_record_by_id(LOC_USER, $user_id, $set);
  }
  public static function db_upd_user_list($condition, $set)
  {
    return static::db_upd_record_list(LOC_USER, $condition, $set);
  }
  public static function db_ins_user($set)
  {
    return static::db_ins_record(LOC_USER, $set);
  }
  public static function db_del_user_by_id($user_id)
  {
    return static::db_del_record_by_id(LOC_USER, $user_id);
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
    return static::db_get_record_by_id(LOC_UNIT, $unit_id, $for_update, $fields);
  }
  public static function db_get_unit_list($filter = '')
  {
    // TODO запихивать в $data[LOC_LOCATION][$location_type][$location_id]
    return static::db_get_record_list(LOC_UNIT, $filter);
  }



















  public static function db_get_unit_list_by_location($user_id = 0, $location_type, $location_id)
  {
    $query_cache = &static::$data[LOC_LOCATION][$location_type][$location_id];
    if(!isset($query_cache))
    {
      $query_cache = static::db_get_record_list(LOC_UNIT, "unit_location_type = {$location_type} AND unit_location_id = {$location_id}");
      $query_cache = is_array($query_cache) ? $query_cache : array();
      foreach($query_cache as $unit_id => $unit_data)
      {
        static::$data[LOC_LOCATION][$location_type][$location_id][$unit_data['unit_snid']] = &static::$data[$location_type][$unit_id];
      }
    }
    // todo Надо ли
    $query_cache = &static::$data[LOC_LOCATION][$location_type][$location_id];

    return $query_cache;
  }
  public static function db_get_unit_by_location($user_id = 0, $location_type, $location_id, $unit_snid = 0, $for_update = false, $fields = '*')
  {
    static::db_get_unit_list_by_location($user_id, $location_type, $location_id);

    return static::$data[LOC_LOCATION][$location_type][$location_id][$unit_snid];
  }





















  public static function db_unit_list_in_fleet_by_user($user_id, $location_id, $for_update)
  {
    return doquery(
      "SELECT *
      FROM {{fleets}} AS f
        JOIN {{unit}} AS u ON u.`unit_location_id` = f.fleet_id
      WHERE
        f.fleet_owner = {$user_id} AND
        (f.fleet_start_planet_id = {$location_id} OR f.fleet_end_planet_id = {$location_id})
        AND u.`unit_location_type` = " . LOC_FLEET .
      " AND " . static::db_unit_time_restrictions() .
      ($for_update ? ' FOR UPDATE' : '')
      , true);
  }







  public static function db_changeset_prepare_unit($unit_id, $unit_value, $user, $planet_id = null)
  {
    if(!is_array($user))
    {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: sn_db_unit_changeset_prepare() - USER is not ARRAY</h1>');
      pdump(debug_backtrace());
      die('USER is not ARRAY');
    }
    if(!isset($user['id']) || !$user['id'])
    {
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

    $db_changeset = array();
    $temp = db_unit_by_location($user['id'], $unit_location, $location_id, $unit_id, true, 'unit_id');
    if($temp['unit_id'])
    {
      $db_changeset = array(
        'action' => SQL_OP_UPDATE,
        'where' => array(
          "`unit_id` = {$temp['unit_id']}",
        ),
        'fields' => array(
          'unit_level' => array(
            'delta' => $unit_value
          ),
        ),
      );
    }
    else
    {
      $db_changeset = array(
        'action' => SQL_OP_INSERT,
        'fields' => array(
          'unit_player_id' => array(
            'set' => $user['id'],
          ),
          'unit_location_type' => array(
            'set' => $unit_location,
          ),
          'unit_location_id' => array(
            'set' => $unit_location == LOC_USER ? $user['id'] : $planet_id,
          ),
          'unit_type' => array(
            'set' => get_unit_param($unit_id, P_UNIT_TYPE),
          ),
          'unit_snid' => array(
            'set' => $unit_id,
          ),
          'unit_level' => array(
            'set' => $unit_value,
          ),
        ),
      );
    }

    return $db_changeset;
  }


  public static function db_changeset_apply($db_changeset)
  {
    $result = false;
    if(!is_array($db_changeset) || empty($db_changeset)) return $result;

    foreach($db_changeset as $table_name => $table_data)
    {
      foreach($table_data as $record_id => $conditions)
      {
        $where = '';
        if(!empty($conditions['where']))
        {
          $where = implode(' AND ', $conditions['where']);
        }

        $fields = array();
        if($conditions['fields'])
        {
          foreach($conditions['fields'] as $field_name => $field_data)
          {
            $condition = "`{$field_name}` = ";
            $value = '';
            if($field_data['delta'])
            {
              $value = "`{$field_name}`" . ($field_data['delta'] >= 0 ? '+' : '') . $field_data['delta'];
            }
            elseif($field_data['set'])
            {
              $value = (is_string($field_data['set']) ? "'{$field_data['set']}'": $field_data['set']);
            }
            if($value)
            {
              $fields[] = $condition . $value;
            }
          }
        }
        $fields = implode(',', $fields);
        if($conditions['action'] != SQL_OP_DELETE && !$fields) continue;
        if($conditions['action'] == SQL_OP_DELETE && !$where) continue; // Защита от случайного удаления всех данных в таблице

        if($table_name == 'unit' && false)
        {
          $location_type = LOC_UNIT;
          // die('spec ops');
          switch($conditions['action'])
          {
            case SQL_OP_DELETE: $result = $result && classSupernova::db_del_record_list($location_type, $where); break;
            case SQL_OP_UPDATE: $result = $result && classSupernova::db_upd_record_list($location_type, $where, $fields); break;
            case SQL_OP_INSERT: $result = $result && classSupernova::db_ins_record($location_type, $fields); break;
            // case SQL_OP_REPLACE: $result = $result && doquery("REPLACE INTO {{{$table_name}}} SET {$fields}"); break;
          }
        }
        else
        {
          $where = $where ? 'WHERE ' . $where : '';
          switch($conditions['action'])
          {
            case SQL_OP_DELETE: $result = $result && doquery("DELETE FROM {{{$table_name}}} {$where}"); break;
            case SQL_OP_UPDATE: $result = $result && doquery("UPDATE {{{$table_name}}} SET {$fields} {$where}"); break;
            case SQL_OP_INSERT: $result = $result && doquery("INSERT INTO {{{$table_name}}} SET {$fields}"); break;
            case SQL_OP_REPLACE: $result = $result && doquery("REPLACE INTO {{{$table_name}}} SET {$fields}"); break;
          }
        }
      }
    }

    return $result;
  }



































  // que_process не всегда должна работать в режиме прямой работы с БД !! Она может работать и в режиме эмуляции
  // !!!!!!!! После que_get брать не [0] элемент, а first() - тогда можно в индекс элемента засовывать que_id из таблицы











  // TODO - это вообще-то надо хранить в конфигурации
  public static function db_get_user_player_username_last_registered()
  {
    $user = static::db_query('SELECT * FROM {{users}} WHERE `user_as_ally` IS NULL ORDER BY `id` DESC', true);
    static::cache_set(LOC_USER, $user['id'], $user);
    return isset($user['username']) ? $user['username'] : '';
  }

  // Это для поиска по кэшу
  protected static function db_get_record_by_field($location_type)
  {
  }

  // Для модулей - регистрация юнитов
  public static function unit_register()
  {

  }

}
