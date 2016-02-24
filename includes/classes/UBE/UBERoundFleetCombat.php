<?php

/**
 * Class UBERoundFleetCombat
 */
class UBERoundFleetCombat {
  public $fleet_id = 0;
  public $is_attacker = UBE_PLAYER_IS_ATTACKER;
  public $owner_id = 0;

  /**
   * @var array[]
   */
  public $unit_list = array(); // TODO - переделать под коллекцию
  /*
   *
   * [UBE_COUNT]
   *
   * [UBE_ATTACK]
   * [UBE_SHIELD]
   * [UBE_ARMOR]
   *
   * [UBE_ARMOR_REST]
   * [UBE_SHIELD_REST]
   *
   * [UBE_UNITS_BOOM]
   *
   * [UBE_ATTACK_BASE]
   * [UBE_SHIELD_BASE]
   * [UBE_ARMOR_BASE]
   *
   * [UBE_SHARE_OF_SIDE_ARMOR]
   *
   */

  /**
   * [UBE_ATTACK/UBE_ARMOR/UBE_SHIELD]
   *
   * @var array[]
   */
  public $total_stats = array();

  /**
   * Доля флота в общем вкладе в броню стороны (Аттакера/Дефендера)
   *
   * @var float
   */
  public $fleet_share_of_side_armor = 0.0;

  public function __construct() {

  }

  public function __clone() {
    // TODO: Implement __clone() method.
  }

  /**
   * @param UBEFleet $UBEFleet
   */
  // OK3
  public function init_from_UBEFleet(UBEFleet $UBEFleet) {
    $this->fleet_id = $UBEFleet->fleet_id;
    $this->is_attacker = $UBEFleet->is_attacker;
    $this->owner_id = $UBEFleet->UBE_OWNER;

    $this->unit_list = array();
    foreach($UBEFleet->UBE_COUNT as $unit_id => $unit_count) {
      // Копируем информацию о кораблях в первый раунд
      $this->unit_list[$unit_id][UBE_UNIT_SNID] = $unit_id;
      $this->unit_list[$unit_id][UBE_COUNT] = $UBEFleet->UBE_COUNT [$unit_id]; // $first_round_data[$fleet_id][UBE_COUNT][$unit_id] = $unit_count;
      $this->unit_list[$unit_id][UBE_ARMOR] = $UBEFleet->UBE_ARMOR [$unit_id] * $UBEFleet->UBE_COUNT[$unit_id]; // $first_round_data[$fleet_id][UBE_ARMOR][$unit_id] = $fleet_info[UBE_ARMOR][$unit_id] * $unit_count;
      $this->unit_list[$unit_id][UBE_ARMOR_REST] = $UBEFleet->UBE_ARMOR [$unit_id]; // $first_round_data[$fleet_id][UBE_ARMOR_REST][$unit_id] = $fleet_info[UBE_ARMOR][$unit_id];
      $this->unit_list[$unit_id][UBE_SHIELD_REST] = $UBEFleet->UBE_SHIELD[$unit_id]; // $first_round_data[$fleet_id][UBE_SHIELD_REST][$unit_id] = $fleet_info[UBE_SHIELD][$unit_id];

    }
  }

  /**
   * @param UBERoundFleetCombat $source
   */
  // OK3
  public function init_from_UBERoundFleetCombat(UBERoundFleetCombat $source) {
    $this->fleet_id = $source->fleet_id;
    $this->is_attacker = $source->is_attacker;
    $this->owner_id = $source->owner_id;

    $this->unit_list = array();
    foreach($source->unit_list as $unit_id => $source_data_array) {
      if(empty($source_data_array[UBE_COUNT]) || $source_data_array[UBE_COUNT] <= 0) {
        continue;
      }
      $this->unit_list[$unit_id][UBE_UNIT_SNID] = $unit_id;
      $this->unit_list[$unit_id][UBE_COUNT] = $source_data_array[UBE_COUNT];
      $this->unit_list[$unit_id][UBE_ARMOR] = $source_data_array[UBE_ARMOR];
      $this->unit_list[$unit_id][UBE_ARMOR_REST] = $source_data_array[UBE_ARMOR_REST];
    }
  }


  public function get_unit_count() {
    $unit_count = 0;
    foreach($this->unit_list as $unit_id => $unit_array_data) {
      $unit_count += $unit_array_data[UBE_COUNT];
    }

    return $unit_count;
  }


  /**
   * @param UBEFleet $fleet_info
   * @param bool     $is_simulator
   */
  // OK3
  public function load_unit_info_from_UBEFleet(UBEFleet $fleet_info, $is_simulator) {
    foreach($this->unit_list as $unit_id => $unit_data_array) {
      if($unit_data_array[UBE_COUNT] <= 0) {
        continue;
      }

      // TODO:  Добавить процент регенерации щитов

      // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
      $unit_data_array[UBE_ATTACK_BASE] = floor($fleet_info->UBE_ATTACK[$unit_id] * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
      $unit_data_array[UBE_SHIELD_BASE] = floor($fleet_info->UBE_SHIELD[$unit_id] * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
      $unit_data_array[UBE_ARMOR_BASE] = floor($fleet_info->UBE_ARMOR[$unit_id]);// * ($is_simulator ? 1 : mt_rand(80, 120) / 100));

      $unit_data_array[UBE_ATTACK] = $unit_data_array[UBE_ATTACK_BASE] * $unit_data_array[UBE_COUNT];
      $unit_data_array[UBE_SHIELD] = $unit_data_array[UBE_SHIELD_BASE] * $unit_data_array[UBE_COUNT];
      $unit_data_array[UBE_SHIELD_REST] = $unit_data_array[UBE_SHIELD_BASE];

    }

    $this->total_stats[UBE_ATTACK] = $this->get_fleet_total_stat(UBE_ATTACK);
    $this->total_stats[UBE_SHIELD] = $this->get_fleet_total_stat(UBE_SHIELD);
    $this->total_stats[UBE_ARMOR] = $this->get_fleet_total_stat(UBE_ARMOR);
  }

  /**
   * @param UBEASA $side_ASA
   */
  // OK4
  public function calculate_unit_partial_data(UBEASA $side_ASA) {
    $this->fleet_share_of_side_armor = $this->total_stats[UBE_ARMOR] / $side_ASA->armor;

    foreach($this->unit_list as &$unit_data_array) {
      $unit_data_array[UBE_SHARE_OF_SIDE_ARMOR] = $unit_data_array[UBE_ARMOR] / $side_ASA->armor;
    }
  }


  /**
   * @param string $stat_name UBE_ATTACK/UBE_SHIELD/UBE_ARMOR...etc
   *
   * @return int
   */
  // OK3
  public function get_fleet_total_stat($stat_name) {
    $result = 0;
    foreach($this->unit_list as $unit_id => $unit_data_array) {
      $result += $unit_data_array[$stat_name];
    }

    return $result;
  }







  // REPORT ************************************************************************************************************
  //    REPORT SAVE ====================================================================================================
  /**
   * Сохраняем информацию о юнитах в раундах
   *
   * @param $ube_report_id
   * @param $sql_perform_ube_report_unit
   */
  // OK3
  public function sql_generate_unit_array($ube_report_id, &$sql_perform_ube_report_unit, &$unit_sort_order, $round_number) {
    foreach($this->unit_list as $unit_id => $unit_data_array) {
      // TODO - ВЫНЕСТИ В ОБЪЕКТ, КОГДА БУДЕТ UBE_UNIT_DATA
      $sql_perform_ube_report_unit[] = array(
        $ube_report_id,
        // $ube->rounds[$round]->fleet_info[$fleet_id]->UBE_OWNER
        $this->owner_id,
        $this->fleet_id,
        $round_number,

        $unit_data_array[$unit_id][UBE_UNIT_SNID],
        $unit_data_array[$unit_id][UBE_COUNT],
        (int)$unit_data_array[$unit_id][UBE_UNITS_BOOM],

        $unit_data_array[$unit_id][UBE_ATTACK],
        $unit_data_array[$unit_id][UBE_SHIELD],
        $unit_data_array[$unit_id][UBE_ARMOR],

        $unit_data_array[$unit_id][UBE_ATTACK_BASE],
        $unit_data_array[$unit_id][UBE_SHIELD_BASE],
        $unit_data_array[$unit_id][UBE_ARMOR_BASE],

        $unit_sort_order++,
      );
    }
  }



  //    REPORT RENDER ==================================================================================================
  /**
   * @param array[] $previous_unit_list
   *
   * @return array
   */
  // OK3
  public function report_render_ship_list($previous_unit_list) {
    $template_ships = array();
    foreach($this->unit_list as $unit_id => $unit_info_array) {
      $template_ships[] = $this->report_render_ship($unit_info_array, $previous_unit_list[$unit_id]);
    }

    return $template_ships;
  }

  /**
   * @param array $unit_info_array
   * @param array $previous_unit_info_array
   *
   * @return array
   */
  // WANNABE метод UBECombatUnitData
  // OK3
  protected function report_render_ship($unit_info_array, $previous_unit_info_array) {
    global $lang;

    $shields_original = $unit_info_array[UBE_SHIELD_BASE] * $previous_unit_info_array[UBE_COUNT];

    return array(
      'ID'          => $unit_info_array[UBE_UNIT_SNID],
      'NAME'        => $lang['tech'][$unit_info_array[UBE_UNIT_SNID]],
      'ATTACK'      => pretty_number($unit_info_array[UBE_ATTACK]),
      'SHIELD'      => pretty_number($shields_original),
      'SHIELD_LOST' => pretty_number($shields_original - $unit_info_array[UBE_SHIELD]),
      'ARMOR'       => pretty_number($previous_unit_info_array[UBE_ARMOR]),
      'ARMOR_LOST'  => pretty_number($previous_unit_info_array[UBE_ARMOR] - $unit_info_array[UBE_ARMOR]),
      'UNITS'       => pretty_number($previous_unit_info_array[UBE_COUNT]),
      'UNITS_LOST'  => pretty_number($previous_unit_info_array[UBE_COUNT] - $unit_info_array[UBE_COUNT]),
      'UNITS_BOOM'  => pretty_number($unit_info_array[UBE_UNITS_BOOM]),
    );
  }



  //    REPORT LOAD ====================================================================================================
  /**
   * @param $report_unit_row
   * @param $player_side
   */
  // OK3
  public function init_fleet_from_report_unit_row($report_unit_row, $player_side) {
    $this->fleet_id = $report_unit_row['ube_report_unit_fleet_id'];
    $this->owner_id = $report_unit_row['ube_report_unit_player_id'];
    $this->is_attacker = $player_side;
  }

  /**
   * @param $report_unit_row
   */
  // OK3
  public function load_fleet_from_report_unit_row($report_unit_row) {
    $unit_id = $report_unit_row['ube_report_unit_unit_id'];
    $this->load_unit_from_report_unit_row($report_unit_row, $this->unit_list[$unit_id]);
  }

  /**
   * @param $report_unit_row
   * @param $unit_data_array
   */
  // WANNABE метод UBECombatUnitData
  // OK3
  public function load_unit_from_report_unit_row($report_unit_row, &$unit_data_array) {
    $unit_data_array[UBE_UNIT_SNID] = $report_unit_row['ube_report_unit_unit_id'];
    $unit_data_array[UBE_COUNT] = $report_unit_row['ube_report_unit_count'];
    $unit_data_array[UBE_UNITS_BOOM] = $report_unit_row['ube_report_unit_boom'];

    $unit_data_array[UBE_ATTACK] = $report_unit_row['ube_report_unit_attack'];
    $unit_data_array[UBE_SHIELD] = $report_unit_row['ube_report_unit_shield'];
    $unit_data_array[UBE_ARMOR] = $report_unit_row['ube_report_unit_armor'];

    $unit_data_array[UBE_ATTACK_BASE] = $report_unit_row['ube_report_unit_attack_base'];
    $unit_data_array[UBE_SHIELD_BASE] = $report_unit_row['ube_report_unit_shield_base'];
    $unit_data_array[UBE_ARMOR_BASE] = $report_unit_row['ube_report_unit_armor_base'];
  }


  /**
   * @param UBERoundFleetCombatList $fleet_list
   * @param UBE                     $ube
   */
  // OK3
  public function attack_fleets(UBERoundFleetCombatList $fleet_list, UBE $ube) {
    foreach($fleet_list->_container as &$defending_fleet) {
      // Не атакуются флоты на своей стороне
      if($this->is_attacker == $defending_fleet->is_attacker) {
        continue;
      }
      $this->attack_fleet($defending_fleet, $ube);
    }
  }

  /**
   * @param UBERoundFleetCombat $defend_fleet_data
   * @param UBE                 $ube
   */
  // OK3
  public function attack_fleet(UBERoundFleetCombat &$defend_fleet_data, UBE $ube) {
    $attacker_amplify_array = &$ube->fleet_list[$this->fleet_id]->UBE_AMPLIFY;

    foreach($this->unit_list as $attack_unit_id => &$attacking_pool) {
      // if($attack_unit_count <= 0) continue; // TODO: Это пока нельзя включать - вот если будут "боевые порядки юнитов..."
      foreach($defend_fleet_data->unit_list as $defend_unit_id => &$defending_pool) {
        // Вычисляем прямой дамадж от атакующего юнита с учетом размера атакуемого
        // TODO - это можно высчитывать и в начале раунда!
        $direct_attack = $attacking_pool[UBE_ATTACK] * $defending_pool[UBE_SHARE_OF_SIDE_ARMOR];
        // TODO - ...и это
        $attacker_amplify = !empty($attacker_amplify_array[$attack_unit_id][$defend_unit_id])
          ? $attacker_amplify_array[$attack_unit_id][$defend_unit_id]
          : 1;
        // TODO - ...и это тоже
        // Применяем амплифай, если есть
        $amplified_attack = floor($direct_attack * $attacker_amplify);

        $this->attack_unit_pool($defending_pool, $amplified_attack, $ube->is_simulator);
      }
    }
  }

  /**
   * Рассчитывает результат столкновения двух юнитов ака ход
   *
   * @param array $defending_pool
   * @param int   $attack
   * @param bool  $is_simulator
   */
  // OK3
  function attack_unit_pool(&$defending_pool, $attack, $is_simulator) {
    if($defending_pool[UBE_COUNT] <= 0) {
      return;
    }

    // Проверяем - не взорвался ли текущий юнит
    $this->attack_damaged_unit($defending_pool, $attack, $is_simulator);

    $defend_unit_base_defence = $defending_pool[UBE_SHIELD_BASE] + $defending_pool[UBE_ARMOR_BASE];

    // todo Добавить взрывы от полуповрежденных юнитов - т.е. заранее вычислить из убитых юнитов еще количество убитых умножить на вероятность от структуры

    // Вычисляем, сколько юнитов взорвалось полностью
    $units_lost_full = floor($attack / $defend_unit_base_defence);
    // Уменьшаем дамадж на ту же сумму
    $attack -= $units_lost_full * $defend_unit_base_defence;
    // Вычисляем, сколько юнитов осталось
    $defending_pool[UBE_COUNT] = max(0, $defending_pool[UBE_COUNT] - $units_lost_full);
    // Уменьшаем броню подразделения на броню потерянных юнитов
    $defending_pool[UBE_ARMOR] -= $units_lost_full * $defending_pool[UBE_ARMOR_BASE];
    $defending_pool[UBE_SHIELD] -= $units_lost_full * $defending_pool[UBE_SHIELD_BASE];

    // Проверяем - не взорвался ли текущий юнит
    $this->attack_damaged_unit($defending_pool, $attack, $is_simulator);
  }

  /**
   * @param array $defending_pool
   * @param int   $attack
   * @param bool  $is_simulator
   *
   * @return bool
   */
  // OK3
  function attack_damaged_unit(&$defending_pool, &$attack, $is_simulator) {
    $unit_is_lost = false;

    $boom_limit = 75; // Взрываемся на 75% прочности
    if($defending_pool[UBE_COUNT] > 0 && $attack) {

      $damage_to_shield = min($attack, $defending_pool[UBE_SHIELD_REST]);
      $attack -= $damage_to_shield;
      $defending_pool[UBE_SHIELD_REST] -= $damage_to_shield;

      $damage_to_armor = min($attack, $defending_pool[UBE_ARMOR_REST]);
      $attack -= $damage_to_armor;
      $defending_pool[UBE_ARMOR_REST] -= $damage_to_armor;

      // Если брони не осталось - юнит потерян
      if($defending_pool[UBE_ARMOR_REST] <= 0) {
        $unit_is_lost = true;
      } elseif($defending_pool[UBE_SHIELD_REST] <= 0) {
        // Если броня осталось, но не осталось щитов - прошел дамадж по броне и надо проверить - не взорвался ли корабль
        $last_unit_hp = $defending_pool[UBE_ARMOR_REST];
        $last_unit_percent = $last_unit_hp / $defending_pool[UBE_ARMOR_BASE] * 100;

        $random = $is_simulator ? $boom_limit / 2 : mt_rand(0, 100);
        if($last_unit_percent <= $boom_limit && $last_unit_percent <= $random) {
          $unit_is_lost = true;
          $damage_to_armor += $defending_pool[UBE_ARMOR_REST];
          $defending_pool[UBE_UNITS_BOOM]++;
          $defending_pool[UBE_ARMOR_REST] = 0;
        }
      }

      $defending_pool[UBE_ARMOR] -= $damage_to_armor;
      $defending_pool[UBE_SHIELD] -= $damage_to_shield;

      if($unit_is_lost) {
        $defending_pool[UBE_COUNT]--;
        if($defending_pool[UBE_COUNT]) {
          $defending_pool[UBE_ARMOR_REST] = $defending_pool[UBE_ARMOR_BASE];
          $defending_pool[UBE_SHIELD_REST] = $defending_pool[UBE_SHIELD_BASE];
        }
      }
    }

    return $unit_is_lost;
  }

}
