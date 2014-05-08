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

  // Кэш индексов - ключ MD5-строка от суммы ключевых строк через | - менять | на что-то другое перед поиском и назад - после поиска
  // Так же в индексах могут быть двойные вхождения - например, названия планет да и вообще
  // Придумать спецсимвол для NULL
  // protected static $indexes = array();


  // protected static $info = array(); // Кэш информации - инфо о юнитах, инфо о группах итд

  // Перепаковывает массив на заданную глубину, убирая поля с null
  public static function array_repack(&$array, &$lock_table, $level = 0)
  {
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

  public static function cache_clear($cache_id = LOC_USER, $hard = true)
  {
    if($hard)
    {
      static::$data[$cache_id] = array();
      static::$locks[$cache_id] = array();
    }
    static::$queries[$cache_id] = array();
  }

  public static function cache_repack($cache_id = LOC_USER, $record_id = 0)
  {
    // Если есть $user_id - проверяем, а надо ли перепаковывать?
    if($record_id && isset(static::$data[$cache_id][$record_id]) && static::$data[$cache_id][$record_id] !== null) return;

    static::array_repack(static::$data[$cache_id], static::$locks[$cache_id]);
    static::array_repack(static::$queries[$cache_id], $cork = null, 1);
    // TODO - а вот тут непонятно. Надо ли вообще это перепаковывать и будет ли польза?
    // На самом деле если запись заблокирована - то она заблокирована. Другой записи с таким же ИД быть не может, а вот инфа может заново поменятся и сразу будет блокированной
    // Впрочем, тут есть сторонние эффекты
    // static::array_repack(static::$locks[LOC_USER]);
  }

  public static function cache_unset($cache_id = LOC_USER, $record_id = 0)
  {
    if(!$record_id)
    {
      static::cache_clear($cache_id, true);
    }
    elseif($record_id && isset(static::$data[$cache_id][$record_id]) && static::$data[$cache_id][$record_id] !== null)
    {
      static::$data[$cache_id][$record_id] = null;
      // static::db_get_user_by_id($user_id);
      static::cache_repack($cache_id, $record_id); // Перепаковываем внутренние структуры, если нужно
    }
  }

  // Кэшируем запись в соответствующий кэш
  // По умолчанию - не перезаписываем существующие значения, если не стоит $force_overwrite
  public static function cache_set($cache_id = LOC_USER, $record_id, $record, $force_overwrite = false)
  {
    if(!$record_id || (isset(static::$data[$cache_id][$record['id']]) && static::$data[$cache_id][$record['id']] && !$force_overwrite)) return;

    static::$data[$cache_id][$record_id] = $record;
    if(static::db_transaction_check(false))
    {
      static::$locks[$cache_id][static::$data[$cache_id][$record_id]] = true;
    }
  }

  // Пока не нужна
  public static function cache_get($cache_id = LOC_USER, $record_id)
  {
  }



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













  public static function db_query($query, $fetch = false)
  {
    $select = strpos(strtoupper($query), 'SELECT') !== false;

    $query .= $select && $fetch ? ' LIMIT 1' : '';
    $query .= $select && static::db_transaction_check(false) ? ' FOR UPDATE' : '';
    return doquery($query, $fetch);
  }


  /**
   * Возвращает информацию о пользователе по его ID
   *
   * @param int|array $user_id
   *    <p>int - ID пользователя</p>
   *    <p>array - запись пользователя с установленным полем ['id']</p>
   * @param bool $for_update @deprecated
   * @param string $fields @deprecated список полей или '*'/'' для всех полей
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
    // TODO $fields
    $fields = $fields ? $fields : '*';
    $user_id = intval(is_array($user_id) && isset($user_id['id']) ? $user_id['id'] : $user_id);
    if(!$user_id)
    {
      return false;
    }

    if(!isset(static::$data[LOC_USER][$user_id]) || static::$data[LOC_USER][$user_id] === null)
    {
      $user = static::db_query("SELECT * FROM {{users}} WHERE `id` = {$user_id}", true);
      static::cache_set(LOC_USER, $user_id, $user);
    }

    $user = &static::$data[LOC_USER][$user_id];

    return
      (is_array($user) && ($player === null || (!($user_as_ally = $user['user_as_ally']) && $player === true) || ($user_as_ally && $player === false)))
        ? static::$data[LOC_USER][$user_id]
        : false;
  }

  /*
  public static function db_get_player_by_id($user_id, $for_update = false, $fields = '*')
  {
    $user = static::db_user_get_by_id($user_id);

    return is_array($user) && !$user['user_as_ally'] ? $user : false;
  }
  */

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
      if(is_array($user_data) && isset($user_data['email']))
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



  // TODO При запросах списков имеет смысл спрашивать ID пользователей, а затем выбирать каждую запись отдельно - что бы работало кэширование. Впрочем, потестить
  public static function db_get_user_list($user_filter = '', $for_update = false, $fields = '*')
  {
    $user_filter = trim($user_filter);

    $query_cache = &static::$queries[LOC_USER][$user_filter];
    if(!isset($query_cache))
    {
      $query_cache = array();
      $query = static::db_query(
        'SELECT * FROM {{users}}' .
        ($user_filter ? " WHERE {$user_filter}" : '')
      );
      while($user = mysql_fetch_assoc($query))
      {
        static::cache_set(LOC_USER, $user['id'], $user); // В кэш-юзер так же заполнять индексы
        $query_cache[$user['id']] = &static::$data[LOC_USER][$user['id']];
      }
    }

    return $query_cache;
    // return static::$queries[LOC_USER][$user_filter];
  }

  // При апдейте единичном кэш запросов в принципе сбрасывать не надо - потому что если на эту запись есть ссылки, то её проапдейтит при изменении $data
  // TODO - $set давать в массиве
  // TODO Проверять по $set изменения в индексах и запросах
  public static function db_set_user_by_id($user_id, $set)
  {
    if(!(($user_id = intval($user_id)) && ($set = trim($set)))) return false;

    if($result = static::db_query("UPDATE {{users}} SET {$set} WHERE `id` = {$user_id}")) // TODO Как-то вернуть может быть LIMIT 1 ?
    {
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        static::$data[LOC_USER][$user_id] = null;
        static::db_get_user_by_id($user_id);
        // TODO Сейчас сбрасывать запросы из-за того, что $set может серъезно поменять результат кэшированного запроса
        static::cache_clear(LOC_USER, false); // Мягкий сброс - только $queries
        // TODO А вот индексы, возможно, прийдется обновить - если поменялись ключевые поля, по которым строились индексы. Но это - попозже
      }
    }

    return $result;
  }
  public static function db_upd_user_list($condition, $set)
  {
    if(!($set = trim($set))) return false;
    $condition = trim($condition);

    if($result = static::db_query('UPDATE {{users}} SET ' . $set . ($condition ? ' WHERE ' . $condition : '')))
    {
      // TODO Сейчас сбрасывать всю инфу о юзерах потому, что $set может серъезно поменять результат кэшированного запроса
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        // TODO Здесь жесткий сброс пока
        static::cache_clear(LOC_USER, true);
      }
    }

    return $result;
  }

  public static function db_ins_user($set)
  {
    if(!($set = trim($set))) return false;

    if($result = static::db_query("INSERT INTO `{{users}}` SET {$set}"))
    {
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        $user_id = mysql_insert_id();
        static::db_get_user_by_id($user_id);
        // TODO Сейчас сбрасывать запросы из-за того, что $set может серъезно поменять результат кэшированного запроса
        static::cache_clear(LOC_USER, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }

  public static function db_del_user_by_id($user_id)
  {
    if(!($user_id = intval($user_id))) return false;

    if($result = static::db_query("DELETE FROM `{{users}}` WHERE `id` = {$user_id}"))
    {
      if(mysql_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        static::cache_unset(LOC_USER, $user_id);
        // static::$data[LOC_USER][$user_id] = null;
        // static::cache_repack(LOC_USER, $user_id); // Перепаковываем внутренние структуры, если нужно
      }
    }

    return $result;
  }

















  // que_process не всегда должна работать в режиме прямой работы с БД !! Она может работать и в режиме эмуляции











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





}
