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
   * Добавляет юнит к списку бонусов
   *
   * @param array $bonus_array
   */
  public function addUnitArray(array $bonus_array) {
    $this->grants = array_replace_recursive($this->grants, $bonus_array);
  }

//  /**
//   * Применяет бонус к характеристике
//   *
//   * @param object $object - Объект, к которому применяется бонус
//   * @param int    $param - Характеристика (????)
//   */
//  // Не нужно??????
//  public function apply_bonus($object, $param = 0) {
//
//  }

  /**
   * Вычисляет бонус сам по себе или бонусное значение от базового
   *
   * Выдает финальный бонус в виде множителя
   *
   * @param string|int $param - ИД бонуса
   *
   * @return float
   */
  public function calcBonus($param, $base_value = null) {
    /**
     * Сортируем бонусы
     *    - первый - базовый с индексом 0
     *    - затем - мультипликаторы по базе
     *    - затем - мультипликаторы кумулятивные
     *    - куда-то вставить аддитивные бонусы - +левел от према, например
     */
//    $this->grants[$param_name][$unit_id] = $unit_level * get_unit_param($unit_id, P_BONUS_VALUE) / 100;;
    $value_add = $base_value;
    $cumulative = 1.0; // Для случая BONUS_PERCENT
    if(!empty($this->grants[$param]) && is_array($this->grants[$param])) {
      foreach($this->grants[$param] as $unit_id => $unit_level) {
        $unit_bonus = 0;
        if($unit_id < 0) {
          // Meta-unit - leave as is
        } else {
          // TODO - Подумать, что будет при смешивании разных бонусов и как этого избежать
          $bonus_value = get_unit_param($unit_id, P_BONUS_VALUE);
          $bonus_type = get_unit_param($unit_id, P_BONUS_TYPE);
          switch($bonus_type) {
            case BONUS_PERCENT:
              $unit_bonus = $unit_level * $bonus_value / 100;
              $cumulative += $unit_bonus;
            break;

            case BONUS_ADD:
              $value_add += $unit_level * $bonus_value;
            break;

            case BONUS_ABILITY:
            break;

            // UNUSED    define('BONUS_MULTIPLY',            4);  // Multiply by value
            // UNUSED    define('BONUS_PERCENT_CUMULATIVE' , 5);  // Cumulative percent on base value
            // UNUSED    define('BONUS_PERCENT_DEGRADED' ,   6);  // Bonus amount degraded with increase as pow(bonus, level) (?)
            // UNUSED    define('BONUS_SPEED',               7);  // Speed bonus
          }
        }
      }
    }

    if($base_value === null) {
      $result = $cumulative;
    } else {
      $result = $base_value * $cumulative;
    }

    return $result;
  }

  /**
   * Мерджит данные о бонусах к текущему объекту
   *
   * @param Bonus $bonus
   */
  public function mergeBonus(Bonus $bonus) {
    $this->grants = array_replace_recursive($this->grants, $bonus->grants);
  }

  /**
   * Выставляет бонусы пакетом. Например, при загрузке бонусов из отчёта
   *
   * @param array $bonus_list
   */
  public function setBonusList(array $bonus_list) {
    $this->grants = $bonus_list;
  }

}

Bonus::_init();
