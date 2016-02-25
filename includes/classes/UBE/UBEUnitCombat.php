<?php

/**
 * Class UBEUnitCombat
 */
class UBEUnitCombat {
  public $unit_id = 0;

  public $count = 0;

  public $attack_base = 0;
  public $shield_base = 0;
  public $armor_base = 0;

  public $pool_attack = 0;
  public $pool_shield = 0;
  public $pool_armor = 0;

  public $unit_count_boom = 0;
  public $unit_destroyed = 0;

  public $share_of_side_armor = 0;

  public $attack_income = 0;
  public $unit_destroyed_by_income = 0;

  /**
   * @param UBEUnit $UBEFleetUnit
   */
  public function init_from_UBEFleetUnit(UBEUnit $UBEFleetUnit) {
    $this->unit_id = $UBEFleetUnit->unit_id;
    $this->count = $UBEFleetUnit->count;
    $this->pool_armor = $UBEFleetUnit->armor * $UBEFleetUnit->count;
  }

  /**
   * @param UBEUnitCombat $UBERoundCombatUnit
   */
  public function init_from_UBERoundCombatUnit(UBEUnitCombat $UBERoundCombatUnit) {
    $this->unit_id = $UBERoundCombatUnit->unit_id;
    $this->count = $UBERoundCombatUnit->count;
    $this->pool_armor = $UBERoundCombatUnit->pool_armor;
  }

  /**
   * @param UBEUnit      $UBEFleetUnit
   * @param              $is_simulator
   */
  public function load_unit_info_from_UBEFleet(UBEUnit $UBEFleetUnit, $is_simulator) {
    // TODO:  Добавить процент регенерации щитов

    // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
    $this->attack_base = floor($UBEFleetUnit->attack * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));
    $this->shield_base = floor($UBEFleetUnit->shield * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));
    $this->armor_base = floor($UBEFleetUnit->armor);// * ($is_simulator ? 1 : mt_rand(80, 120) / 100));

    $this->pool_attack = $this->attack_base * $this->count;
    $this->pool_shield = $this->shield_base * $this->count;
  }


  /**
   * @param array $report_unit_row
   */
  public function load_unit_from_report_unit_row(array &$report_unit_row) {
    $this->unit_id = $report_unit_row['ube_report_unit_unit_id'];
    $this->count = $report_unit_row['ube_report_unit_count'];
    $this->unit_count_boom = $report_unit_row['ube_report_unit_boom'];

    $this->pool_attack = $report_unit_row['ube_report_unit_attack'];
    $this->pool_shield = $report_unit_row['ube_report_unit_shield'];
    $this->pool_armor = $report_unit_row['ube_report_unit_armor'];

    $this->attack_base = $report_unit_row['ube_report_unit_attack_base'];
    $this->shield_base = $report_unit_row['ube_report_unit_shield_base'];
    $this->armor_base = $report_unit_row['ube_report_unit_armor_base'];
  }

  /**
   * @param bool $is_simulator
   */
  // OK6
  public function receive_damage($is_simulator) {
    // TODO - Добавить взрывы от полуповрежденных юнитов - т.е. заранее вычислить из убитых юнитов еще количество убитых умножить на вероятность от структуры
    if($this->count <= 0) {
      return;
    }

    $start_count = $this->count;

//    // Проверяем - не взорвался ли текущий юнит
//    $this->attack_damaged_unit($is_simulator);

    // Общая защита одного юнита
    $pool_base_defence = $this->shield_base + $this->armor_base;

    // Вычисляем, сколько юнитов взорвалось полностью, но не больше, чем их осталось во флоте
    $units_lost = min(floor($this->attack_income / $pool_base_defence), $this->count); // $units_lost_full всегда не больше $this->count

    // Уменьшаем дамадж на ту же сумму
    $this->attack_income -= $units_lost * $pool_base_defence;

    // Уменьшаем общие щиты на щиты уничтоженных юнитов, но не больше, чем есть
    $this->pool_shield -= min($units_lost * $this->shield_base, $this->pool_shield);
    // Уменьшаем общую броню на броню уничтоженных юнитов, но не больше, чем есть
    $this->pool_armor -= min($units_lost * $this->armor_base, $this->pool_armor);
    // Вычитаем уничтоженные юниты из общего количества юнитов
    $this->count -= $units_lost;

    // Проверяем - не взорвался ли текущий юнит
    while($this->count > 0 && $this->attack_income > 0) {
      $this->attack_damaged_unit($is_simulator);
    }

    $this->unit_destroyed += $start_count - $this->count;
  }

  /**
   * @param bool $is_simulator
   */
  // OK6
  function attack_damaged_unit($is_simulator) {
//    // Нет юнитов или не осталось атак - ничего не делаем
//    // Не нужно???????
//    if($this->count <= 0 || $this->attack_income <= 0) {
//      return;
//    }

    // Вычисляем остаток щитов на текущем корабле
    $shield_left = $this->pool_shield % $this->shield_base;
    // Вычисляем остаток брони
    $armor_left = $this->pool_armor % $this->armor_base;
    // Проверка - не атакуем ли мы целый корабль
    // Такое может быть, если на прошлой итерации поврежденный корабль был взорван и еще осталась входящяя атака
    if($shield_left == 0 && $armor_left == 0) {
      $shield_left = $this->shield_base;
      $armor_left = $this->armor_base;
    }

    // Сколько прошло дамаджа по щитам
    $damage_to_shield = min($shield_left, $this->attack_income);

    // Уменьшаем атаку на дамадж, поглощенный щитами
    $this->attack_income -= $damage_to_shield;

    // Вычитаем этот дамадж из щитов пула
    $this->pool_shield -= $damage_to_shield;
    // Если весь дамадж был поглощён щитами - выходим
    if($this->attack_income <= 0) {
      return;
    }


    // Сколько прошло дамаджа по броне
    $damage_to_armor = min($armor_left, $this->attack_income);

    // Уменьшаем атаку на дамадж, поглощенный бронёй
    $this->attack_income -= $damage_to_armor;

    // Вычитаем этот дамадж из брони пула
    $this->pool_armor -= $damage_to_armor;
    // Вычитаем дамадж из брони текущего корабля
    $armor_left -= $damage_to_armor;

    // Проверяем - осталась ли броня на текущем корабле и вааще
    if($this->pool_armor <= 0 || $armor_left <= 0) {
      // Не осталось - корабль уничтожен
      $this->count--;
      return;
    }

    // Броня осталась. Проверяем - не взорвался ли корабль
    $armor_left_percent = $armor_left / $this->armor_base * 100;
    // Проверяем % здоровья
    // TODO - сделать динамический процент для каждого вида юнитов
    if($armor_left_percent <= UBE_CRITICAL_DAMAGE_THRESHOLD) {
      // Дамадж пошёл по структуре. Чем более поврежден юнит - тем больше шансов у него взорваться
      $random = $is_simulator ? UBE_CRITICAL_DAMAGE_THRESHOLD / 2 : mt_rand(0, 100);
      if(mt_rand(0, 100) >= $armor_left_percent) {
        $this->count--;
        $this->unit_count_boom++;
        return;
      }
    }
  }


  /**
   * @param UBEUnitCombat $prev_unit_state
   *
   * @return array
   */
  // OK6
  public function report_render_unit(UBEUnitCombat $prev_unit_state) {
    global $lang;

    $shields_original = $this->shield_base * $prev_unit_state->count;

    return array(
      'ID'          => $this->unit_id,
      'NAME'        => $lang['tech'][$this->unit_id],
      'ATTACK'      => pretty_number($this->pool_attack),
      'SHIELD'      => pretty_number($shields_original),
      'SHIELD_LOST' => pretty_number($shields_original - $this->pool_shield),
      'ARMOR'       => pretty_number($prev_unit_state->pool_armor),
      'ARMOR_LOST'  => pretty_number($prev_unit_state->pool_armor - $this->pool_armor),
      'UNITS'       => pretty_number($prev_unit_state->count),
      'UNITS_LOST'  => pretty_number($prev_unit_state->count - $this->count),
      'UNITS_BOOM'  => pretty_number($this->unit_count_boom),
    );
  }

  /**
   * @return array
   */
  public function sql_generate_unit_array() {
    return array(
      $this->unit_id,
      $this->count,
      (int)$this->unit_count_boom,

      $this->pool_attack,
      $this->pool_shield,
      $this->pool_armor,

      $this->attack_base,
      $this->shield_base,
      $this->armor_base,
    );
  }






//  /**
//   * @param bool $is_simulator
//   *
//   * @return bool
//   */
//  // OK6
//  function attack_damaged_unit_old($is_simulator) {
//    $unit_is_lost = false;
//
//    $boom_limit = UBE_CRITICAL_DAMAGE_TRESHOLD; // Взрываемся на 75% прочности
//    if($this->count > 0 && $this->attack_income) {
//
//      $damage_to_shield = min($this->attack_income, $this->shield_rest);
//      $this->attack_income -= $damage_to_shield;
//      $this->shield_rest -= $damage_to_shield;
//
//      $damage_to_armor = min($this->attack_income, $this->armor_rest);
//      $this->attack_income -= $damage_to_armor;
//      $this->armor_rest -= $damage_to_armor;
//
//      // Если брони не осталось - юнит потерян
//      if($this->armor_rest <= 0) {
//        $unit_is_lost = true;
//      } elseif($this->shield_rest <= 0) {
//        // Если броня осталось, но не осталось щитов - прошел дамадж по броне и надо проверить - не взорвался ли корабль
//        $last_unit_hp = $this->armor_rest;
//        $last_unit_percent = $last_unit_hp / $this->armor_base * 100;
//
//        $random = $is_simulator ? $boom_limit / 2 : mt_rand(0, 100);
//        if($last_unit_percent <= $boom_limit && $last_unit_percent <= $random) {
//          $unit_is_lost = true;
//          $damage_to_armor += $this->armor_rest;
//          $this->unit_count_boom++;
//          $this->armor_rest = 0;
//        }
//      }
//
//      $this->armor -= $damage_to_armor;
//      $this->shield -= $damage_to_shield;
//
//      if($unit_is_lost) {
//        $this->count--;
//        if($this->count) {
//          $this->armor_rest = $this->armor_base;
//          $this->shield_rest = $this->shield_base;
//        }
//      }
//    }
//
//    return $unit_is_lost;
//  }

}
