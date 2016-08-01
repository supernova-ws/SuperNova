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

  /**
   * Кэширует соответствия между расположением объектов - в частности юнитов и очередей
   *
   * Массив $locator - хранит отношения между записями для быстрого доступа по тип_записи:тип_локации:ид_локации:внутренний_ид_записи=>информация
   * Для LOC_UNIT внутренний ИД - это SNID, а информация - это ссылка на запись `unit`
   * Для LOC_QUE внутренний ИД - это тип очереди, а информация - массив ссылок на `que`
   *
   * @var array $locator
   */
  protected static $locator = array();

  /**
   * Кэш запросов
   *
   * @var array $queries
   */
  protected static $queries = array();

  /**
   * Информация о блокировках
   *
   * @var array $locks
   */
  protected static $locks = array();

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
    HelperArray::array_repack(static::$queries[$location_type], 1);
  }

  public static function cache_clear($location_type, $hard = true) {
    if ($hard && !empty(static::$data[$location_type])) {
      // Здесь нельзя делать unset - надо записывать NULL, что бы это отразилось на зависимых записях
      // TODO - replace with setNull
      array_walk(static::$data[$location_type], function (&$item) { $item = null; });
    }
    static::$locator[$location_type] = array();
    static::$queries[$location_type] = array();
    static::cache_repack($location_type); // Перепаковываем внутренние структуры, если нужно
  }

  public static function setNull(&$item) {
    $item = null;
  }

  public static function cache_clear_all($hard = true) {
    if ($hard) {
      static::$data = array();
      static::cache_lock_unset_all();
    }
    static::$locator = array();
    static::$queries = array();
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
    if (!($record_id = $record[SnDbCachedOperator::$location_info[$location_type][P_ID]])) {
      return;
    }

    $in_transaction = classSupernova::$db->getTransaction()->check(false);
    if (
      $force_overwrite
      ||
      // Не заменяются заблокированные записи во время транзакции
      ($in_transaction && !static::cache_lock_get($location_type, $record_id))
      ||
      !static::cache_isset($location_type, $record_id)
    ) {
      static::$data[$location_type][$record_id] = $record;
      if ($in_transaction && !$skip_lock) {
        static::cache_lock_set($location_type, $record_id);
      }
    }
  }

  public static function queryCacheSetByFilter($location_type, $filter, $record_id) {
    SnCache::$queries[$location_type][$filter][$record_id] = &SnCache::getDataRefByLocationAndId($location_type, $record_id);
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
    return isset(static::$locks[$location_type][$record_id]);
  }

  public static function cache_lock_set($location_type, $record_id) {
    return static::$locks[$location_type][$record_id] = true; // Не всегда - от результата
  }

  // TODO - UNUSED ????????????
  public static function cache_lock_unset($location_type, $record_id) {
    if (isset(static::$locks[$location_type][$record_id])) {
      unset(static::$locks[$location_type][$record_id]);
    }

    return true; // Не всегда - от результата
  }

  public static function cache_lock_unset_all() {
    // Когда будем работать с xcache - это понадобиться, что бы снимать в xcache блокировки с записей
    // Пройти по массиву - снять блокировки для кэшера в памяти
    static::$locks = array();

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

  public static function setUnitLocator($unit, $unit_id) {
    if (is_array($unit)) {
      static::$locator[LOC_UNIT][$unit['unit_location_type']][$unit['unit_location_id']][$unit['unit_snid']] = &static::getDataRefByLocationAndId(LOC_UNIT, $unit_id);
    }
  }

  public static function getUnitLocator($location_type, $location_id, $unit_snid) {
    return $unit_snid ? static::$locator[LOC_UNIT][$location_type][$location_id][$unit_snid] : static::$locator[LOC_UNIT][$location_type][$location_id];
  }

  public static function getUnitLocatorByFullLocation($location_type, $location_id) {
    return is_array(static::$locator[LOC_UNIT][$location_type][$location_id]) ? static::$locator[LOC_UNIT][$location_type][$location_id] : array();
  }

  public static function setUnitLocatorByLocationAndIDs($location_type, $location_id, $unit_data) {
    SnCache::$locator[LOC_UNIT][$location_type][$location_id][$unit_data['unit_snid']] = &static::$data[LOC_UNIT][$unit_data['unit_id']];
  }

  public static function isUnitLocatorNotSet($location_type, $location_id) {
    return !isset(static::$locator[LOC_UNIT][$location_type][$location_id]);
  }

  public static function locatorReset() {
    static::$locator = array();
  }

  public static function queriesReset() {
    static::$queries = array();
  }

  public static function getQueries() {
    return static::$queries;
  }

  public static function getLocks() {
    return static::$locks;
  }

  public static function getQueriesByLocationAndFilter($locationType, $filter) {
    return !empty(static::$queries[$locationType][$filter]) && is_array(static::$queries[$locationType][$filter]) ? static::$queries[$locationType][$filter] : array();
  }

  public static function isQueryCacheByLocationAndFilterEmpty($locationType, $filter) {
    return !isset(SnCache::$queries[$locationType][$filter]) || SnCache::$queries[$locationType][$filter] === null;
  }

  public static function queryCacheResetByLocationAndFilter($locationType, $filter) {
    SnCache::$queries[$locationType][$filter] = array();
  }

}
