<?php

/**
 * Class UnitResourceLoot
 */
class UnitResourceLoot extends Unit {

  /**
   * @var array
   */
  protected static $_group_pnames = array();

  /**
   * Статический иницилизатор. ДОЛЖЕН БЫТЬ ВЫЗВАН ПЕРЕД ИСПОЛЬЗВОАНИЕМ КЛАССА!
   *
   * @param string $group_name
   */
  public static function _init($group_name = '') {
    parent::_init($group_name);

    foreach(static::$_group_unit_id_list as $resource_id) {
      static::$_group_pnames[$resource_id] = pname_resource_name($resource_id);
    }

  }

  /**
   * Конвертирует массив ресурсов в формате <SN_ID> => <AMOUNT> в формат $prefix<ИМЯ_ПОЛЯ> => <AMOUNT>
   *
   * Может применятся как для списка ресурсов, так и для списка дельт
   *
   * @param array  $resource_array
   * @param string $prefix
   *
   * @return array
   */
  // Нужен только пока в БД не будут вынесены ресурсы флота, игрока, альянса, планеты в таблицу `unit`
  public static function convert_id_to_field_name($resource_array, $prefix = '') {
    $result = array();

    !is_array($resource_array) ? $resource_array = array() : false;

    foreach($resource_array as $resource_id => $resource_actual_delta) {
      if(!$resource_actual_delta) {
        // No delta - no changes
        continue;
      }

      $result[$prefix . static::$_group_pnames[$resource_id]] = $resource_actual_delta;
    }

    return $result;
  }


}

UnitResourceLoot::_init('resources_loot');
