<?php

/**
 * User: Gorlum
 * Date: 31.01.2016
 * Time: 2:05
 */
class UnitBasic {
  /**
   * @var bool
   */
  protected static $_is_static_init = false;
  /**
   * @var string
   */
  protected static $_sn_group_name = '';
  /**
   * @var array
   */
  protected static $_group_unit_id_list = array();
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
    if(static::$_is_static_init) {
      return;
    }

    static::$_sn_group_name = $group_name;

    if(static::$_sn_group_name) {
      static::$_group_unit_id_list = sn_get_groups(static::$_sn_group_name);
      static::$_group_unit_id_list === null ? static::$_group_unit_id_list = array() : false;

      foreach(static::$_group_unit_id_list as $resource_id) {
        static::$_group_pnames[$resource_id] = pname_resource_name($resource_id);
      }
    }

  }

  /**
   * Проверяет - принадлежит ли указанный ID юнита данной группе
   *
   * @param int $unit_id
   *
   * @return bool
   */
  public static function is_in_group($unit_id) {
    return isset(static::$_group_unit_id_list[$unit_id]);
  }

  /**
   * Конвертирует массив ресурсов в формате <SN_ID> => <AMOUNT> в формат $prefix<ИМЯ_ПОЛЯ> => <AMOUNT>
   *
   * @param array  $resource_array
   * @param string $prefix
   *
   * @return array
   */
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
