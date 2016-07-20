<?php

/**
 * Class SnCache
 *
 * Permanent cache for classSupernova
 */
class SnCache {
  /**
   * Кэш данных - юзера, планеты, юниты, очередь, альянсы итд
   *
   * @var array $data
   */
  protected static $data = array();

  // Массив $locator - хранит отношения между записями для быстрого доступа по тип_записи:тип_локации:ид_локации:внутренний_ид_записи=>информация
  // Для LOC_UNIT внутренний ИД - это SNID, а информация - это ссылка на запись `unit`
  // Для LOC_QUE внутренний ИД - это тип очереди, а информация - массив ссылок на `que`
  public static $locator = array(); // Кэширует соответствия между расположением объектов - в частности юнитов и очередей

  /**
   * Repacking data for $location_type
   *
   * @param int $location_type
   * @param int $record_id
   */
  public static function cache_repack($location_type, $record_id = 0) {
    // Если есть $user_id - проверяем, а надо ли перепаковывать?
    if ($record_id && isset(static::$data[$location_type][$record_id]) && static::$data[$location_type][$record_id] !== null) {
      return;
    }

    HelperArray::array_repack(static::$data[$location_type]);
    HelperArray::array_repack(static::$locator[$location_type], 3); // TODO У каждого типа локации - своя глубина!!!! Но можно и глубже ???
    HelperArray::array_repack(classSupernova::$queries[$location_type], 1);
  }

  public static function cache_clear($location_type, $hard = true) {
    if ($hard && !empty(static::$data[$location_type])) {
      // Здесь нельзя делать unset - надо записывать NULL, что бы это отразилось на зависимых записях
      // TODO - replace with setNull
      array_walk(static::$data[$location_type], function (&$item) { $item = null; });
    }
    static::$locator[$location_type] = array();
    classSupernova::$queries[$location_type] = array();
    static::cache_repack($location_type); // Перепаковываем внутренние структуры, если нужно
  }

  public static function setNull(&$item) {
    $item = null;
  }

  public static function cache_clear_all($hard = true) {
    if ($hard) {
      static::$data = array();
      SnCache::cache_lock_unset_all();
    }
    static::$locator = array();
    classSupernova::$queries = array();
  }

  public static function cache_isset($location_type, $record_id) {
    return isset(static::$data[$location_type][$record_id]) && static::$data[$location_type][$record_id] !== null;
  }

  // TODO - UNUSED ????????????
  public static function cache_get($location_type, $record_id) {
    return isset(static::$data[$location_type][$record_id]) ? static::$data[$location_type][$record_id] : null;
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
  public static function cache_set($location_type, $record, $force_overwrite = false, $skip_lock = false) {
    // нет идентификатора - выход
    if (!($record_id = $record[classSupernova::$location_info[$location_type][P_ID]])) {
      return;
    }

    $in_transaction = classSupernova::db_transaction_check(false);
    if (
      $force_overwrite
      ||
      // Не заменяются заблокированные записи во время транзакции
      ($in_transaction && !SnCache::cache_lock_get($location_type, $record_id))
      ||
      !static::cache_isset($location_type, $record_id)
    ) {
      static::$data[$location_type][$record_id] = $record;
      if ($in_transaction && !$skip_lock) {
        SnCache::cache_lock_set($location_type, $record_id);
      }
    }
  }

  public static function cache_unset($cache_id, $safe_record_id) {
    // $record_id должен быть проверен заранее !
    if (isset(static::$data[$cache_id][$safe_record_id]) && static::$data[$cache_id][$safe_record_id] !== null) {
      // Выставляем запись в null
      static::$data[$cache_id][$safe_record_id] = null;
      // Очищаем кэш мягко - что бы удалить очистить связанные данные - кэш локаций и кэш запоросов и всё, что потребуется впредь
      static::cache_clear($cache_id, false);
    }
  }

  public static function cache_lock_get($location_type, $record_id) {
    return isset(classSupernova::$locks[$location_type][$record_id]);
  }

  public static function cache_lock_set($location_type, $record_id) {
    return classSupernova::$locks[$location_type][$record_id] = true; // Не всегда - от результата
  }

  // TODO - UNUSED ????????????
  public static function cache_lock_unset($location_type, $record_id) {
    if (isset(classSupernova::$locks[$location_type][$record_id])) {
      unset(classSupernova::$locks[$location_type][$record_id]);
    }

    return true; // Не всегда - от результата
  }

  public static function cache_lock_unset_all() {
    // Когда будем работать с xcache - это понадобиться, что бы снимать в xcache блокировки с записей
    // Пройти по массиву - снять блокировки для кэшера в памяти
    classSupernova::$locks = array();

    return true; // Не всегда - от результата
  }

  public static function getData($locationType = LOC_NONE) {
    return $locationType == LOC_NONE ? static::$data : static::$data[$locationType];
  }

  public static function cacheUnsetElement($locationType, $recordId) {
    static::$data[$locationType][$recordId] = null;
  }

  public static function isArrayLocation($locationType) {
    return is_array(static::$data[$locationType]);
  }

  /**
   * Return reference to record in $data by locationType and recordId
   *
   * @param int $locationType
   * @param int $recordId
   *
   * @return &mixed
   */
  public static function &getDataRefByLocationAndId($locationType, $recordId) {
    return static::$data[$locationType][$recordId];
  }

}
