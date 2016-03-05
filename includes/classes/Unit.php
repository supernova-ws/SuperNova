<?php

/**
 * Class Unit
 */
class Unit {
//  public $db_id = 0;
  public $unit_id = 0;
  public $count = 0;
  public $type = 0;


  /**
   * @var array
   */
  protected $bonus = array();


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
      empty(static::$_group_unit_id_list) ? static::$_group_unit_id_list = array() : false;
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

}
