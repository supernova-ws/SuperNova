<?php

/**
 * Class UBEFleetList
 *
 * @method UBEFleet offsetGet($offset)
 * @property UBEFleet[] $_container
 */
class UBEFleetList extends ArrayAccessV2 {

  public function load_from_players(UBEPlayerList $players) {
    foreach($this->_container as $fleet_id => $objFleet) {
      // TODO - эта последовательность должна быть при загрузке флота (?)

      $objFleet->copy_stats_from_player($players[$objFleet->owner_id]);

      // Вычисляем бонус игрока и добавляем его к бонусам флота
      $objFleet->bonuses_add_float($players[$objFleet->owner_id]->player_bonus_get_all());
      // TODO
//      $objFleet->add_planet_bonuses();
//      $objFleet->add_fleet_bonuses();
//      $objFleet->add_ship_bonuses();

      $objFleet->calculate_battle_stats();
    }
  }

  /**
   * @param $report_row
   */
  public function db_load_fleets_outcome($report_row) {
    $query = doquery("SELECT * FROM {{ube_report_outcome_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($row = db_fetch($query)) {
      $fleet_id = $row['ube_report_outcome_fleet_fleet_id'];
      $this[$fleet_id]->load_outcome_from_report_row($row);
    }

    $query = doquery("SELECT * FROM {{ube_report_outcome_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_outcome_unit_sort_order`");
    while($row = db_fetch($query)) {
      $fleet_id = $row['ube_report_outcome_unit_fleet_id'];
      $this[$fleet_id]->load_unit_outcome_from_row($row);
    }
  }

  // REPORT RENDER *****************************************************************************************************
  /**
   * Генерирует данные для отчета из разобранных данных боя
   *
   * @param UBE $ube
   *
   * @return array
   */
  public function report_render_fleets_outcome(UBE $ube) {
    $result = array(
      UBE_PLAYER_IS_ATTACKER => array(),
      UBE_PLAYER_IS_DEFENDER => array(),
    );

    foreach($this->_container as $fleet_id => $UBEFleet) {
      $result[$UBEFleet->is_attacker][] = array(
        'ID'          => $fleet_id,
        'NAME'        => $ube->players[$UBEFleet->owner_id]->player_name_get(),
        'IS_ATTACKER' => $UBEFleet->is_attacker == UBE_PLAYER_IS_ATTACKER,
        '.'           => array(
          'param' => $UBEFleet->report_render_outcome_side_fleet(),
        ),
      );
    }

    return array_merge($result[UBE_PLAYER_IS_ATTACKER], $result[UBE_PLAYER_IS_DEFENDER]);
  }


  /**
   * @param UBERound  $lastRound
   * @param           $is_simulator
   * @param UBEDebris $debris
   * @param array     $resource_exchange_rates
   */
  public function ube_analyze_fleets(UBERound $lastRound, $is_simulator, UBEDebris $debris, array $resource_exchange_rates) {
    // Генерируем результат боя
    foreach($this->_container as $fleet_id => $UBEFleet) {
      // Инициализируем массив результатов для флота
//      $this->init_fleet_outcome_and_link_to_side($UBEFleet);

      foreach($UBEFleet->unit_list->_container as $unit_id => $UBEFleetUnit) {
        $UBEFleetUnit->ube_analyze_unit($lastRound->fleet_combat_data[$fleet_id], $is_simulator);
      }

      // Вычисляем ёмкость трюмов оставшихся кораблей
      // Вычисляем потери в ресурсах
      $UBEFleet->calc_fleet_stats(); // calc fleet_capacity

      // TODO - вынести на уровень FleetList
      $debris->add_wrecks($UBEFleet->resources_lost_on_ships, $is_simulator);
      $debris->add_cargo_drop($UBEFleet->cargo_dropped, $is_simulator);

      // TODO - вынести подальше и сделать подсчёт сразу всех потерь
      // ...в металле
      $this->resources_lost_in_metal = array(
        RES_METAL => 0,
      );
      foreach($UBEFleet->resources_lost_on_units as $resource_id => $resource_amount) {
        $UBEFleet->resources_lost_in_metal[RES_METAL] += $resource_amount * $resource_exchange_rates[$resource_id];
      }
      foreach($UBEFleet->cargo_dropped as $resource_id => $resource_amount) {
        $UBEFleet->resources_lost_in_metal[RES_METAL] += $resource_amount * $resource_exchange_rates[$resource_id];
      }
    }
  }

  /**
   * @return array
   */
  public function get_groups() {
    $result = array();
    foreach($this->_container as $UBEFleet) {
      if($UBEFleet->group_id) {
        $result[$UBEFleet->group_id] = $UBEFleet->group_id;
      }
    }

    return $result;
  }

  /**
   * @return int
   */
  public function get_capacity_attackers() {
    $result = 0;
    foreach($this->_container as $UBEFleet) {
      if($UBEFleet->is_attacker) {
        $result += $UBEFleet->fleet_capacity;
      }
    }

    return $result;
  }

  /**
   * @param array $report_row
   * @param UBE   $ube
   */
  public function db_load_from_report_row(array $report_row, UBE $ube) {
    $query = doquery("SELECT * FROM {{ube_report_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($fleet_row = db_fetch($query)) {
      $objFleet = new UBEFleet();
      $objFleet->load_from_report($fleet_row, $ube);
      $this[$objFleet->fleet_id] = $objFleet;
    }

  }

}
