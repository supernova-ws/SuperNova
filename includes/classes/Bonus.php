<?php

/**
 * Class Bonus
 */
class Bonus {

  protected static $_bonus_group = array();

  protected $grants = array();
  protected $recieves = array();

  public static function _init() {
    static::$_bonus_group = sn_get_groups(P_BONUS_VALUE);
    empty(static::$_bonus_group) ? static::$_bonus_group = array() : false;
  }

  public function __construct() {
    foreach(static::$_bonus_group as $param_name => $unit_list) {
      $this->grants[$param_name][0] = 1; // Базовое значение
    }
  }

  /**
   * Добавляет юнит к списку бонусов
   *
   * @param $unit_id
   * @param $unit_level
   */
  public function add_unit($unit_id, $unit_level) {
    if(!$unit_level) {
      return;
    }

    foreach(static::$_bonus_group as $param_name => $unit_list) {
      if(!empty($unit_list[$unit_id])) {
        // Простейший вариант - мультипликатор по базе
        // Общий мультипликатор добавляется в конец
        $this->grants[$param_name][$unit_id] = $unit_level;
      }
    }

  }

  /**
   * Применяет бонус к характеристике
   *
   * @param object $object - Объект, к которому применяется бонус
   * @param int    $param - Характеристика (????)
   */
  // Не нужно??????
  public function apply_bonus($object, $param = 0) {

  }

  /**
   * Выдает финальный бонус в виде множителя
   *
   * @param string|int $param - ИД бонуса
   *
   * @return float
   */
  public function getBonus($param) {
    /**
     * Сортируем бонусы
     *    - первый - базовый с индексом 0
     *    - затем - мультипликаторы по базе
     *    - затем - мультипликаторы кумулятивные
     *    - куда-то вставить аддитивные бонусы
     */
//    $this->grants[$param_name][$unit_id] = $unit_level * get_unit_param($unit_id, P_BONUS_VALUE) / 100;;
    return 1.0;
  }

  /**
   * Мерджит данные о бонусах к текущему объекту
   *
   * @param Bonus $bonus
   */
  public function mergeBonus(Bonus $bonus) {

  }

}

Bonus::_init();
