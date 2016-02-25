<?php

/**
 * Class UBEUnit
 */
class UBEUnit {
  public $unit_id = 0;

  public $count = 0;
  public $units_lost = 0;
  public $units_restored = 0;

  public $attack = 0;
  public $shield = 0;
  public $armor = 0;

  public $type = 0;


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


  /**
   * @param UBEFleetCombat $UBERoundFleetCombat
   * @param bool           $is_simulator
   */
  public function ube_analyze_unit(UBEFleetCombat $UBERoundFleetCombat, $is_simulator) {
    // Вычисляем сколько юнитов осталось и сколько потеряно
    $this->units_lost = $this->count - $UBERoundFleetCombat->unit_list[$this->unit_id]->count;

    // Восстановление обороны - 75% от уничтоженной
    $this->restore_unit($is_simulator);

    // Приводим количество юнитов к текущему состоянию
    $this->count -= $this->units_lost;

  }

  /**
   * @param bool $is_simulator
   */
  public function restore_unit($is_simulator) {
    if($this->type != UNIT_DEFENCE || $this->units_lost <= 0) {
      return;
    }

    if($is_simulator) {
      $units_giveback = round($this->units_lost * UBE_DEFENCE_RESTORATION_CHANCE_AVG / 100); // for simulation just return 75% of loss
    } else {
      // Checking - should we trigger mass-restore
      if($this->units_lost >= UBE_DEFENCE_RESTORATION_MASS_COUNT) {
        // For large amount - mass-restoring defence
        $units_giveback = round($this->units_lost * mt_rand(UBE_DEFENCE_RESTORATION_CHANCE_MIN, UBE_DEFENCE_RESTORATION_CHANCE_MAX) / 100);
      } else {
        // For small amount - restoring defence per single unit
        $units_giveback = 0;
        for($i = 1; $i <= $this->units_lost; $i++) {
          if(mt_rand(1, 100) <= UBE_DEFENCE_RESTORATION_CHANCE_AVG) {
            $units_giveback++;
          }
        }
      }
    }

    $this->units_restored = $units_giveback;
    $this->units_lost -= $units_giveback;
  }

}
