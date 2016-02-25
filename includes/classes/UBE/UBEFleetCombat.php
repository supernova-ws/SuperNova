<?php

/**
 * Class UBEFleetCombat
 */
class UBEFleetCombat {
  public $fleet_id = 0;
  public $owner_id = 0;
  public $is_attacker = UBE_PLAYER_IS_ATTACKER;

  /**
   * @var UBEUnitCombatList
   */
  public $unit_list = null;

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
    $this->unit_list = new UBEUnitCombatList();
  }

  public function __clone() {
    $this->unit_list = clone $this->unit_list;
  }

  /**
   * @param UBEFleet $UBEFleet
   */
  // OK3
  public function init_from_UBEFleet(UBEFleet $UBEFleet) {
    $this->fleet_id = $UBEFleet->fleet_id;
    $this->is_attacker = $UBEFleet->is_attacker;
    $this->owner_id = $UBEFleet->owner_id;

    $this->unit_list->init_from_UBEFleet($UBEFleet);
  }

  /**
   * @param UBEFleetCombat $source
   */
  // OK3
  public function init_from_UBERoundFleetCombat(UBEFleetCombat $source) {
    $this->fleet_id = $source->fleet_id;
    $this->is_attacker = $source->is_attacker;
    $this->owner_id = $source->owner_id;

    $this->unit_list->init_from_UBERoundFleetCombat($source);
  }


  public function get_unit_count() {
    $result = 0;
    foreach($this->unit_list->_container as $unit_id => $UBERoundCombatUnit) {
      $result += $UBERoundCombatUnit->count;
    }

    return $result;
  }


  /**
   * @param UBEFleet $fleet_info
   * @param bool     $is_simulator
   */
  // OK3
  public function load_unit_info_from_UBEFleet(UBEFleet $fleet_info, $is_simulator) {
    $this->unit_list->load_unit_info_from_UBEFleet($fleet_info, $is_simulator);

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

    foreach($this->unit_list->_container as $UBERoundCombatUnit) {
      $UBERoundCombatUnit->share_of_side_armor = $UBERoundCombatUnit->pool_armor / $side_ASA->armor;
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

    foreach($this->unit_list->_container as $unit_id => $UBERoundCombatUnit) {
      switch($stat_name) {
        case UBE_ATTACK:
          $result += $UBERoundCombatUnit->pool_attack;
        break;

        case UBE_SHIELD:
          $result += $UBERoundCombatUnit->pool_shield;
        break;

        case UBE_ARMOR:
          $result += $UBERoundCombatUnit->pool_armor;
        break;
      }
    }

    return $result;
  }







  // REPORT ************************************************************************************************************
  //    REPORT SAVE ====================================================================================================
  /**
   * @param array $sql_perform_ube_report_unit
   * @param int   $ube_report_id
   * @param int   $round_number
   * @param int   $unit_sort_order
   */
  // OK6
  public function sql_generate_unit_array(array &$sql_perform_ube_report_unit, $ube_report_id, $round_number, &$unit_sort_order) {
    $prefix = array(
      $ube_report_id,
      // $ube->rounds[$round]->fleet_info[$fleet_id]->UBE_OWNER
      $this->owner_id,
      $this->fleet_id,
      $round_number,
    );

    $this->unit_list->sql_generate_unit_array($sql_perform_ube_report_unit, $unit_sort_order, $prefix);
  }



  //    REPORT RENDER ==================================================================================================
  /**
   * @param array[] $previous_unit_list
   *
   * @return array
   */
  // OK6
  public function report_render_ship_list(UBEUnitCombatList $prev_unit_combat) {
    $template_ships = array();

    foreach($this->unit_list->_container as $unit_id => $UBERoundCombatUnit) {
      $template_ships[] = $UBERoundCombatUnit->report_render_unit($prev_unit_combat[$unit_id]);
    }

    return $template_ships;
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
   * @param array $report_unit_row
   */
  // OK3
  public function load_fleet_from_report_unit_row(array $report_unit_row) {
    $unit_id = $report_unit_row['ube_report_unit_unit_id'];
    $this->unit_list[$unit_id] = new UBEUnitCombat();
    $this->unit_list[$unit_id]->load_unit_from_report_unit_row($report_unit_row);
  }

  /**
   * @param UBEFleetCombatList $fleet_list
   * @param UBE                $ube
   */
  // OK3
  public function attack_fleets(UBEFleetCombatList $fleet_list, UBE $ube) {
    foreach($fleet_list->_container as &$defending_fleet) {
      // Не атакуются флоты на своей стороне
      if($this->is_attacker == $defending_fleet->is_attacker) {
        continue;
      }
      $this->attack_fleet($defending_fleet, $ube);
    }
  }

  /**
   * @param UBEFleetCombat $defend_fleet_data
   * @param UBE            $ube
   */
  // OK6
  public function attack_fleet(UBEFleetCombat $defend_fleet_data, UBE $ube) {
    foreach($this->unit_list->_container as $attack_unit_id => $attacking_unit_pool) {
      $attacker_amplify_array = &$ube->fleet_list[$this->fleet_id]->unit_list[$attack_unit_id]->amplify;
      // if($attack_unit_count <= 0) continue; // TODO: Это пока нельзя включать - вот если будут "боевые порядки юнитов..."
      foreach($defend_fleet_data->unit_list->_container as $defend_unit_id => $defending_unit_pool) {
        // Вычисляем прямой дамадж от атакующего юнита с учетом размера атакуемого
        // TODO - это можно высчитывать и в начале раунда!
        $direct_attack = $attacking_unit_pool->pool_attack * $defending_unit_pool->share_of_side_armor;
        // TODO - ...и это
        $attacker_amplify = !empty($attacker_amplify_array[$defend_unit_id])
          ? $attacker_amplify_array[$defend_unit_id]
          : 1;
        // TODO - ...и это тоже
        // Применяем амплифай, если есть
        $amplified_attack = floor($direct_attack * $attacker_amplify);

        $defending_unit_pool->attack_income = $amplified_attack;

        $defending_unit_pool->receive_damage($ube->is_simulator);
      }
    }
  }

}
