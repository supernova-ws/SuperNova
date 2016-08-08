<?php

/**
 * Created by Gorlum 01.08.2016 21:40
 */
class SnDbCachedOperator {
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
   * @var db_mysql $db
   */
  protected $db;

  /**
   * @var \SnCache $snCache
   */
  protected $snCache;

  /**
   * SnDbCachedOperator constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
    $this->db = $gc->db;
    $this->snCache = $gc->snCache;
  }

  public function db_del_record_by_id($location_type, $safe_record_id) {
    if (!($safe_record_id = idval($safe_record_id))) {
      return false;
    }

    $id_field = static::$location_info[$location_type][P_ID];
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    // TODO - lock value in cache
    if ($result = $this->db->doDeleteRow($table_name, array($id_field => $safe_record_id,))
    ) {
      // Обновляем данные только если ряд был затронут
      if ($this->db->db_affected_rows()) {
        $this->snCache->cache_unset($location_type, $safe_record_id);
      }
    }

    return $result;
  }

  /**
   * @param int   $location_type
   * @param array $condition
   *
   * @return array|bool|mysqli_result|null
   */
  public function db_del_record_list($location_type, $condition) {
    if (!is_array($condition) || empty($condition)) {
      return false;
    }

    $table_name = static::$location_info[$location_type][P_TABLE_NAME];

    if ($result = $this->db->doDeleteWhere($table_name, $condition)) {
      // Обновляем данные только если ряд был затронут
      if ($this->db->db_affected_rows()) {
        // Обнуление кэша, потому что непонятно, что поменялось
        // TODO - когда будет структурированный $condition можно будет делать только cache_unset по нужным записям
        $this->snCache->cache_clear($location_type);
      }
    }

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
  public function db_get_record_by_id($location_type, $record_id_unsafe, $for_update = false, $fields = '*', $skip_lock = false) {
    $id_field = static::$location_info[$location_type][P_ID];
    $record_id_safe = idval(is_array($record_id_unsafe) && isset($record_id_unsafe[$id_field]) ? $record_id_unsafe[$id_field] : $record_id_unsafe);

    return classSupernova::$gc->cacheOperator->db_get_record_list($location_type, "`{$id_field}` = {$record_id_safe}", true, false);
  }

  public function db_get_record_list($location_type, $filter = '', $fetch = false, $no_return = false) {
    if ($this->snCache->isQueryCacheByLocationAndFilterEmpty($location_type, $filter)) {
      $this->snCache->queryCacheResetByLocationAndFilter($location_type, $filter);

      $location_info = &static::$location_info[$location_type];
      $id_field = $location_info[P_ID];

      if ($this->db->getTransaction()->check(false)) {
        // Проходим по всем родителям данной записи
        foreach ($location_info[P_OWNER_INFO] as $owner_data) {
          $owner_location_type = $owner_data[P_LOCATION];
          $parent_id_list = array();
          // Выбираем родителей данного типа и соответствующие ИД текущего типа
          $query = $this->db->doSelect(
            "SELECT
              distinct({{{$location_info[P_TABLE_NAME]}}}.{$owner_data[P_OWNER_FIELD]}) AS parent_id
            FROM {{{$location_info[P_TABLE_NAME]}}}" .
            ($filter ? ' WHERE ' . $filter : '') .
            ($fetch ? ' LIMIT 1' : ''));
          while ($row = db_fetch($query)) {
            // Исключаем из списка родительских ИД уже заблокированные записи
            if (!$this->snCache->cache_lock_get($owner_location_type, $row['parent_id'])) {
              $parent_id_list[$row['parent_id']] = $row['parent_id'];
            }
          }

          // Если все-таки какие-то записи еще не заблокированы - вынимаем текущие версии из базы
          if ($indexes_str = implode(',', $parent_id_list)) {
            $parent_id_field = static::$location_info[$owner_location_type][P_ID];
            classSupernova::$gc->cacheOperator->db_get_record_list($owner_location_type,
              $parent_id_field . (count($parent_id_list) > 1 ? " IN ({$indexes_str})" : " = {$indexes_str}"), $fetch, true);
          }
        }
      }

      $query = $this->db->doSelect(
        "SELECT * FROM {{{$location_info[P_TABLE_NAME]}}}" .
        (($filter = trim($filter)) ? " WHERE {$filter}" : '')
        . " FOR UPDATE"
      );
      while ($row = db_fetch($query)) {
        // Caching record in row cache
        $this->snCache->cache_set($location_type, $row);
        // Making ref to cached record in query cache
        $this->snCache->queryCacheSetByFilter($location_type, $filter, $row[$id_field]);
      }
    }

    if ($no_return) {
      return true;
    } else {
      $result = false;
      foreach ($this->snCache->getQueriesByLocationAndFilter($location_type, $filter) as $key => $value) {
        $result[$key] = $value;
        if ($fetch) {
          break;
        }
      }

      return $fetch ? (is_array($result) ? reset($result) : false) : $result;
    }
  }

  /**
   * @param int   $location_type
   * @param int   $record_id
   * @param array $set - SQL SET structure
   * @param array $adjust - SQL ADJUST structure
   *
   * @return array|bool|mysqli_result|null
   */
  public function db_upd_record_by_id($location_type, $record_id, $set, $adjust) {
    if (!($record_id = idval($record_id)) || (empty($set) && empty($adjust))) {
      return false;
    }

    $id_field = static::$location_info[$location_type][P_ID];
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    // TODO Как-то вернуть может быть LIMIT 1 ?
    $result = $this->db->doUpdateRowAdjust(
      $table_name,
      $set,
      $adjust,
      array(
        $id_field => $record_id
      )
    );

    if ($result) {
      if ($this->db->db_affected_rows()) {
        // Обновляем данные только если ряд был затронут
        // TODO - переделать под работу со структурированными $set

        // Тут именно так, а не cache_unset - что бы в кэшах автоматически обновилась запись. Будет нужно на будущее
        //static::$data[$location_type][$record_id] = null;
        $this->snCache->cacheUnsetElement($location_type, $record_id);
        // Вытаскиваем обновленную запись
        $this->db_get_record_by_id($location_type, $record_id);
        $this->snCache->cache_clear($location_type, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }



  /**
   * @param int   $location_type
   * @param array $set
   * @param array $adjust
   *
   * @param array $where
   *
   * @return array|bool|mysqli_result|null
   */
  public function db_upd_record_list($location_type, $set, $adjust, $where, $whereDanger = array()) {
    if (empty($set) && empty($adjust)) {
      return false;
    }

    $result = $this->db->doUpdateTableAdjust(
      static::$location_info[$location_type][P_TABLE_NAME],
      $set,
      $adjust,
      $where,
      $whereDanger
    );

    if ($result) {
      // Обновляем данные только если ряд был затронут
      if ($this->db->db_affected_rows()) {
        // Поскольку нам неизвестно, что и как обновилось - сбрасываем кэш этого типа полностью
        // TODO - когда будет структурированный $condition и $set - перепаковывать данные
        $this->snCache->cache_clear($location_type, true);
      }
    }

    return $result;
  }

  /**
   * This calls is DANGER 'cause $condition contains direct variable injections and should be rewrote
   *
   * This call just a proxy to easier to locate code for rewrite
   *
   * @param int   $location_type
   * @param array $set
   * @param array $adjust
   * @param array $where
   * @param array $whereDanger
   *
   * @return array|bool|mysqli_result|null
   * @deprecated
   */
  public function db_upd_record_list_DANGER($location_type, $set, $adjust, $where, $whereDanger) {
    return $this->db_upd_record_list($location_type, $set, $adjust, $where, $whereDanger);
  }






  /**
   * @param int   $location_type
   * @param array $set
   *
   * @return array|bool|false|mysqli_result|null
   */
  public function db_ins_record($location_type, $set) {
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    $result = $this->db->doInsertSet($table_name, $set);
    if ($result) {
      if ($this->db->db_affected_rows()) // Обновляем данные только если ряд был затронут
      {
        $record_id = $this->db->db_insert_id();
        // Вытаскиваем запись целиком, потому что в $set могли быть "данные по умолчанию"
        $result = $this->db_get_record_by_id($location_type, $record_id);
        // Очищаем второстепенные кэши - потому что вставленная запись могла повлиять на результаты запросов или локация или еще чего
        // TODO - когда будет поддержка изменения индексов и локаций - можно будет вызывать её
        $this->snCache->cache_clear($location_type, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }


  public function db_ins_field_set($location_type, $field_set) {
    $table_name = static::$location_info[$location_type][P_TABLE_NAME];
    $result = $this->db->doInsertSet($table_name, $field_set);
    if ($result) {
      if ($this->db->db_affected_rows()) {
        // Обновляем данные только если ряд был затронут
        $record_id = $this->db->db_insert_id();
        // Вытаскиваем запись целиком, потому что в $set могли быть "данные по умолчанию"
        $result = $this->db_get_record_by_id($location_type, $record_id);
        // Очищаем второстепенные кэши - потому что вставленная запись могла повлиять на результаты запросов или локация или еще чего
        // TODO - когда будет поддержка изменения индексов и локаций - можно будет вызывать её
        $this->snCache->cache_clear($location_type, false); // Мягкий сброс - только $queries
      }
    }

    return $result;
  }


  /**
   * Блокирует указанные таблицу/список таблиц
   *
   * @param string|array $tables Таблица/список таблиц для блокировки. Названия таблиц - без префиксов
   * <p>string - название таблицы для блокировки</p>
   * <p>array - массив, где ключ - имя таблицы, а значение - условия блокировки элементов</p>
   */
  public function db_lock_tables($tables) {
    $tables = is_array($tables) ? $tables : array($tables => '');
    foreach ($tables as $table_name => $condition) {
      $this->db->doSelect("SELECT 1 FROM {{{$table_name}}}" . ($condition ? ' WHERE ' . $condition : ''));
    }
  }
}
