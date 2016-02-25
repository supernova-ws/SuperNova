<?php

/**
 * Class UBEFleetUnit
 */
class UBEFleetUnit {
  public $unit_id = 0;

  public $count = 0;

  public $attack = 0;
  public $shield = 0;
  public $armor = 0;

  public $type = 0;

  public $defence_restored = 0;
  public $units_lost = 0;

  public $capacity = 0;
  public $price = array();
  public $amplify = array();

  /**
   * @param $bonus_list
   */
  public function fill_unit_info($bonus_list) {
    global $ube_convert_to_techs;

    $unit_info = get_unit_param($this->unit_id);
    // Заполняем информацию о кораблях в информации флота
    $this->attack = floor($unit_info[$ube_convert_to_techs[UBE_ATTACK]] * (1 + $bonus_list[UBE_ATTACK]));
    $this->shield = floor($unit_info[$ube_convert_to_techs[UBE_SHIELD]] * (1 + $bonus_list[UBE_SHIELD]));
    $this->armor = floor($unit_info[$ube_convert_to_techs[UBE_ARMOR]] * (1 + $bonus_list[UBE_ARMOR]));

    $this->amplify = $unit_info[P_AMPLIFY];

    $this->capacity = $unit_info[P_CAPACITY];
    $this->type = $unit_info[P_UNIT_TYPE];

    $this->price[RES_METAL] = $unit_info[P_COST][RES_METAL];
    $this->price[RES_CRYSTAL] = $unit_info[P_COST][RES_CRYSTAL];
    $this->price[RES_DEUTERIUM] = $unit_info[P_COST][RES_DEUTERIUM];
    $this->price[RES_DARK_MATTER] = $unit_info[P_COST][RES_DARK_MATTER];
  }

}
