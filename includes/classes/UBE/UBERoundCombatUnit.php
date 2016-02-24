<?php

/**
 * Class UBERoundCombatUnit
 */
class UBERoundCombatUnit {

  public $unit_id = 0;

  public $attack_base = 0;
  public $shield_base = 0;
  public $armor_base = 0;

  public $count = 0;

  public $attack = 0;
  public $shield = 0;
  public $armor = 0;

  public $armor_rest = 0;
  public $shield_rest = 0;

  public $unit_count_boom = 0;
  public $unit_destroyed = 0;

  public $share_of_side_armor = 0;

  public $attack_income = 0;

  /**
   * @param UBEFleetUnit $UBEFleetUnit
   */
  public function init_from_UBEFleetUnit(UBEFleetUnit $UBEFleetUnit) {
    $this->unit_id = $UBEFleetUnit->unit_id;
    $this->count = $UBEFleetUnit->count;
    $this->armor = $UBEFleetUnit->armor * $UBEFleetUnit->count;
    $this->armor_rest = $UBEFleetUnit->armor;
    $this->shield_rest = $UBEFleetUnit->shield;
  }

  /**
   * @param UBERoundCombatUnit $UBERoundCombatUnit
   */
  public function init_from_UBERoundCombatUnit(UBERoundCombatUnit $UBERoundCombatUnit) {
    $this->unit_id = $UBERoundCombatUnit->unit_id;
    $this->count = $UBERoundCombatUnit->count;
    $this->armor = $UBERoundCombatUnit->armor;
    $this->armor_rest = $UBERoundCombatUnit->armor_rest;
  }

  /**
   * @param UBEFleetUnit $UBEFleetUnit
   * @param              $is_simulator
   */
  public function load_unit_info_from_UBEFleet(UBEFleetUnit $UBEFleetUnit, $is_simulator) {
    // TODO:  Добавить процент регенерации щитов

    // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
    $this->attack_base = floor($UBEFleetUnit->attack * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
    $this->shield_base = floor($UBEFleetUnit->shield * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
    $this->armor_base = floor($UBEFleetUnit->armor);// * ($is_simulator ? 1 : mt_rand(80, 120) / 100));

    $this->attack = $this->attack_base * $this->count;
    $this->shield = $this->shield_base * $this->count;
    $this->shield_rest = $this->shield_base;
  }


  /**
   * @param array $report_unit_row
   */
  public function load_unit_from_report_unit_row(array &$report_unit_row) {
    $this->unit_id = $report_unit_row['ube_report_unit_unit_id'];
    $this->count = $report_unit_row['ube_report_unit_count'];
    $this->unit_count_boom = $report_unit_row['ube_report_unit_boom'];

    $this->attack = $report_unit_row['ube_report_unit_attack'];
    $this->shield = $report_unit_row['ube_report_unit_shield'];
    $this->armor = $report_unit_row['ube_report_unit_armor'];

    $this->attack_base = $report_unit_row['ube_report_unit_attack_base'];
    $this->shield_base = $report_unit_row['ube_report_unit_shield_base'];
    $this->armor_base = $report_unit_row['ube_report_unit_armor_base'];
  }

  /**
   * @param bool $is_simulator
   */
  // OK6
  public function receive_damage($is_simulator) {
    if($this->count <= 0) {
      return;
    }

    $start_count = $this->count;

    // Проверяем - не взорвался ли текущий юнит
    $this->attack_damaged_unit($is_simulator);

    $defend_unit_base_defence = $this->shield_base + $this->armor_base;

    // todo Добавить взрывы от полуповрежденных юнитов - т.е. заранее вычислить из убитых юнитов еще количество убитых умножить на вероятность от структуры

    // Вычисляем, сколько юнитов взорвалось полностью
    $units_lost_full = floor($this->attack_income / $defend_unit_base_defence);
    // Уменьшаем дамадж на ту же сумму
    $this->attack_income -= $units_lost_full * $defend_unit_base_defence;
    // Вычисляем, сколько юнитов осталось
    $this->count = max(0, $this->count - $units_lost_full);
    // Уменьшаем броню подразделения на броню потерянных юнитов
    $this->armor -= $units_lost_full * $this->armor_base;
    $this->shield -= $units_lost_full * $this->shield_base;

    // Проверяем - не взорвался ли текущий юнит
    $this->attack_damaged_unit($is_simulator);

    $this->unit_destroyed += $start_count - $this->count;
  }

  /**
   * @param bool $is_simulator
   *
   * @return bool
   */
  // OK6
  function attack_damaged_unit($is_simulator) {
    $unit_is_lost = false;

    $boom_limit = 75; // Взрываемся на 75% прочности
    if($this->count > 0 && $this->attack_income) {

      $damage_to_shield = min($this->attack_income, $this->shield_rest);
      $this->attack_income -= $damage_to_shield;
      $this->shield_rest -= $damage_to_shield;

      $damage_to_armor = min($this->attack_income, $this->armor_rest);
      $this->attack_income -= $damage_to_armor;
      $this->armor_rest -= $damage_to_armor;

      // Если брони не осталось - юнит потерян
      if($this->armor_rest <= 0) {
        $unit_is_lost = true;
      } elseif($this->shield_rest <= 0) {
        // Если броня осталось, но не осталось щитов - прошел дамадж по броне и надо проверить - не взорвался ли корабль
        $last_unit_hp = $this->armor_rest;
        $last_unit_percent = $last_unit_hp / $this->armor_base * 100;

        $random = $is_simulator ? $boom_limit / 2 : mt_rand(0, 100);
        if($last_unit_percent <= $boom_limit && $last_unit_percent <= $random) {
          $unit_is_lost = true;
          $damage_to_armor += $this->armor_rest;
          $this->unit_count_boom++;
          $this->armor_rest = 0;
        }
      }

      $this->armor -= $damage_to_armor;
      $this->shield -= $damage_to_shield;

      if($unit_is_lost) {
        $this->count--;
        if($this->count) {
          $this->armor_rest = $this->armor_base;
          $this->shield_rest = $this->shield_base;
        }
      }
    }

    return $unit_is_lost;
  }

  /**
   * @param UBERoundCombatUnit $prev_unit_state
   *
   * @return array
   */
  // OK6
  public function report_render_unit(UBERoundCombatUnit $prev_unit_state) {
    global $lang;

    $shields_original = $this->shield_base * $prev_unit_state->count;

    return array(
      'ID'          => $this->unit_id,
      'NAME'        => $lang['tech'][$this->unit_id],
      'ATTACK'      => pretty_number($this->attack),
      'SHIELD'      => pretty_number($shields_original),
      'SHIELD_LOST' => pretty_number($shields_original - $this->shield),
      'ARMOR'       => pretty_number($prev_unit_state->armor),
      'ARMOR_LOST'  => pretty_number($prev_unit_state->armor - $this->armor),
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

      $this->attack,
      $this->shield,
      $this->armor,

      $this->attack_base,
      $this->shield_base,
      $this->armor_base,
    );
  }

}
