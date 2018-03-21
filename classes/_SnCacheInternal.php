<?php
/**
 * Created by Gorlum 31.10.2017 2:04
 */

/**
 * Class _SnCacheInternal
 *
 * Внутренний кэшер отдельных записей из БД в память PHP
 *
 * @deprecated
 */
class _SnCacheInternal {
  public static $data = array(); // Кэш данных - юзера, планеты, юниты, очередь, альянсы итд
  public static $locks = array(); // Информация о блокировках
  public static $queries = array(); // Кэш запросов

  // Массив $locator - хранит отношения между записями для быстрого доступа по тип_записи:тип_локации:ид_локации:внутренний_ид_записи=>информация
  // Для LOC_UNIT внутренний ИД - это SNID, а информация - это ссылка на запись `unit`
  // Для LOC_QUE внутренний ИД - это тип очереди, а информация - массив ссылок на `que`
  protected static $locator = array(); // Кэширует соответствия между расположением объектов - в частности юнитов и очередей


  public static function array_repack(&$array, $level = 0) {
    // TODO $lock_table не нужна тут
    if (!is_array($array)) {
      return;
    }

    foreach ($array as $key => &$value) {
      if ($value === null) {
        unset($array[$key]);
      } elseif ($level > 0 && is_array($value)) {
        _SnCacheInternal::array_repack($value, $level - 1);
        if (empty($value)) {
          unset($array[$key]);
        }
      }
    }
  }

  public static function cache_repack($location_type, $record_id = 0) {
    // Если есть $user_id - проверяем, а надо ли перепаковывать?
    if ($record_id && isset(_SnCacheInternal::$data[$location_type][$record_id]) && _SnCacheInternal::$data[$location_type][$record_id] !== null) {
      return;
    }

    _SnCacheInternal::array_repack(_SnCacheInternal::$data[$location_type]);
    _SnCacheInternal::array_repack(_SnCacheInternal::$locator[$location_type], 3); // TODO У каждого типа локации - своя глубина!!!! Но можно и глубже ???
    _SnCacheInternal::array_repack(_SnCacheInternal::$queries[$location_type], 1);
  }

  public static function cache_clear($location_type, $hard = true) {
    //print("<br />CACHE CLEAR {$cache_id} " . ($hard ? 'HARD' : 'SOFT') . "<br />");
    if ($hard && !empty(_SnCacheInternal::$data[$location_type])) {
      // Здесь нельзя делать unset - надо записывать NULL, что бы это отразилось на зависимых записях
      array_walk(_SnCacheInternal::$data[$location_type], function (&$item) { $item = null; });
    }
    _SnCacheInternal::$locator[$location_type] = [];
    _SnCacheInternal::$queries[$location_type] = [];
    _SnCacheInternal::cache_repack($location_type); // Перепаковываем внутренние структуры, если нужно
  }

  public static function cache_lock_unset_all() {
    // Когда будем работать с xcache - это понадобиться, что бы снимать в xcache блокировки с записей
    // Пройти по массиву - снять блокировки для кэшера в памяти
    _SnCacheInternal::$locks = array();

    return true; // Не всегда - от результата
  }

  public static function cache_locator_unset_all() {
    _SnCacheInternal::$locator = [];
  }

  public static function cache_queries_unset_all() {
    _SnCacheInternal::$queries = [];
  }

//  public static function cache_clear_all($hard = true) {
//    //print('<br />CACHE CLEAR ALL<br />');
//    if($hard) {
//      _SnCacheInternal::$data = array();
//      _SnCacheInternal::cache_lock_unset_all();
//    }
//    _SnCacheInternal::$locator = array();
//    _SnCacheInternal::$queries = array();
//  }


  // TODO - this code is currently unused (!)
  public static function cache_get($location_type, $record_id) {
    return isset(_SnCacheInternal::$data[$location_type][$record_id]) ? _SnCacheInternal::$data[$location_type][$record_id] : null;
  }

  public static function cache_isset($location_type, $record_id) {
    return isset(_SnCacheInternal::$data[$location_type][$record_id]) && _SnCacheInternal::$data[$location_type][$record_id] !== null;
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
    if (!($record_id = $record[SN::$location_info[$location_type][P_ID]])) {
      return;
    }

    $in_transaction = SN::db_transaction_check(false);
    if (
      $force_overwrite
      ||
      // Не заменяются заблокированные записи во время транзакции
      ($in_transaction && !_SnCacheInternal::cache_lock_get($location_type, $record_id))
      ||
      !_SnCacheInternal::cache_isset($location_type, $record_id)
    ) {
      _SnCacheInternal::$data[$location_type][$record_id] = $record;
      if ($in_transaction && !$skip_lock) {
        _SnCacheInternal::cache_lock_set($location_type, $record_id);
      }
    }
  }

  // TODO - 1 вхождение
  public static function cache_unset($cache_id, $safe_record_id) {
    // $record_id должен быть проверен заранее !
    if (isset(_SnCacheInternal::$data[$cache_id][$safe_record_id]) && _SnCacheInternal::$data[$cache_id][$safe_record_id] !== null) {
      // Выставляем запись в null
      _SnCacheInternal::$data[$cache_id][$safe_record_id] = null;
      // Очищаем кэш мягко - что бы удалить очистить связанные данные - кэш локаций и кэш запоросов и всё, что потребуется впредь
      _SnCacheInternal::cache_clear($cache_id, false);
    }
  }

  public static function cache_lock_get($location_type, $record_id) {
    return isset(_SnCacheInternal::$locks[$location_type][$record_id]);
  }

  // TODO - 1 вхождение
  public static function cache_lock_set($location_type, $record_id) {
    return _SnCacheInternal::$locks[$location_type][$record_id] = true; // Не всегда - от результата
  }

  // TODO - unused
  public static function cache_lock_unset($location_type, $record_id) {
    if (isset(_SnCacheInternal::$locks[$location_type][$record_id])) {
      unset(_SnCacheInternal::$locks[$location_type][$record_id]);
    }

    return true; // Не всегда - от результата
  }


  /**
   * @param array      $unit
   * @param int|string $unit_db_id
   */
  public static function unit_linkLocatorToData($unit, $unit_db_id) {
    _SnCacheInternal::$locator[LOC_UNIT][$unit['unit_location_type']][$unit['unit_location_id']][$unit['unit_snid']] = &_SnCacheInternal::$data[LOC_UNIT][$unit_db_id];
  }


  /**
   * @param $location_type
   * @param $location_id
   *
   * @return bool
   */
  public static function unit_locatorIsSet($location_type, $location_id) {
    return isset(_SnCacheInternal::$locator[LOC_UNIT][$location_type][$location_id]);
  }

  /**
   * @param $location_type
   * @param $location_id
   *
   * @return bool
   */
  public static function unit_locatorIsArray($location_type, $location_id) {
    return is_array(_SnCacheInternal::$locator[LOC_UNIT][$location_type][$location_id]);
  }

  /**
   * @param $location_type
   * @param $location_id
   *
   * @return array|false
   */
  public static function unit_locatorGetAllFromLocation($location_type, $location_id) {
    $result = false;
    if (_SnCacheInternal::unit_locatorIsArray($location_type, $location_id)) {
      foreach (_SnCacheInternal::$locator[LOC_UNIT][$location_type][$location_id] as $key => $value) {
        $result[$key] = $value;
      }
    }

    return $result;
  }

  /**
   * @param $location_type
   * @param $location_id
   * @param $unit_snid
   *
   * @return array|null
   */
  public static function unit_locatorGetUnitFromLocation($location_type, $location_id, $unit_snid) {
    $allUnits = _SnCacheInternal::unit_locatorGetAllFromLocation($location_type, $location_id);

    return isset($allUnits[$unit_snid]) ? $allUnits[$unit_snid] : null;
  }
}
