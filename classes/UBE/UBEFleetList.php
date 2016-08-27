<?php

/**
 * Class UBEFleetList
 *
 * @method ube_load_from_players(UBEPlayerList $players) - load player-specific info into each fleet in fleet list
 * @see UBEFleet::ube_load_from_players
 *
 * @method UBEFleet offsetGet($offset)
 * @property UBEFleet[] $_container
 *
 */
class UBEFleetList extends FleetList {

//  /**
//   * Method list that should support applying to container content
//   *
//   * @var string[]
//   */
//  protected static $_call = array('ube_load_from_players');

  /**
   * @var UBEASA[]
   */
  protected $ube_total = array();

  /**
   * Какие стороны присутствуют. ТОЛЬКО ДЛЯ ИСПОЛЬЗОВАНИЯ в next_round_fleet_array()!!!!
   *
   * @var array
   */
  protected $ube_side_present_at_round_start = array();

  /**
   * UBEFleetList constructor.
   *
   */
  public function __construct() {
    parent::__construct();

    $this->ube_total = array(
      UBE_PLAYER_IS_ATTACKER => new UBEASA(),
      UBE_PLAYER_IS_DEFENDER => new UBEASA(),
    );

    $this->ube_side_present_at_round_start = array();
  }

  /**
   * @return UBEFleet
   *
   */
  public function _createElement() {
    return new UBEFleet();
  }

  /**
   * @param Fleet $objFleet
   *
   */
  public function ube_insert_from_Fleet(Fleet $objFleet) {
    $this[$objFleet->dbId] = new UBEFleet();
    $this[$objFleet->dbId]->read_from_fleet_object($objFleet);

    // Вызов основной функции!!!
    ube_attack_prepare_fleet_from_object($this[$objFleet->dbId]); // Used by UNIT_CAPTAIN
  }

  public function ube_insert_from_planet_row(array &$planet_row, UBEPlayer $player, Bonus $planet_bonus) {
    $this[0] = new UBEFleet();
    $this[0]->read_from_planet_row($planet_row, $player);
    $this[0]->fleet_bonus->mergeBonus($planet_bonus);
  }


  /**
   * @param $report_row
   */
  public function ube_db_load_fleets_outcome($report_row) {
    $query = classSupernova::$db->doSelect("SELECT * FROM {{ube_report_outcome_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($row = db_fetch($query)) {
      $fleet_id = $row['ube_report_outcome_fleet_fleet_id'];
      $this[$fleet_id]->load_outcome_from_report_row($row);
    }

    $query = classSupernova::$db->doSelect("SELECT * FROM {{ube_report_outcome_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_outcome_unit_sort_order`");
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
  public function ube_report_render_fleets_outcome(UBE $ube) {
    $result = array(
      UBE_PLAYER_IS_ATTACKER => array(),
      UBE_PLAYER_IS_DEFENDER => array(),
    );

    foreach($this->_container as $fleet_id => $UBEFleet) {
      $result[$UBEFleet->is_attacker][] = array(
        'ID'          => $fleet_id,
        'NAME'        => $ube->players[$UBEFleet->owner_id]->name,
        'IS_ATTACKER' => $UBEFleet->is_attacker == UBE_PLAYER_IS_ATTACKER,
        '.'           => array(
          'param' => $UBEFleet->report_render_outcome_side_fleet(),
        ),
      );
    }

    return array_merge($result[UBE_PLAYER_IS_ATTACKER], $result[UBE_PLAYER_IS_DEFENDER]);
  }

  /**
   * @param bool      $is_simulator
   * @param UBEDebris $debris
   * @param array     $resource_exchange_rates
   *
   */
  public function ube_analyze_fleets($is_simulator, UBEDebris $debris, array $resource_exchange_rates) {
    // Генерируем результат боя
    foreach($this->_container as $fleet_id => $UBEFleet) {
      // Инициализируем массив результатов для флота
//      $this->init_fleet_outcome_and_link_to_side($UBEFleet);

      foreach($UBEFleet->unit_list->_container as $UBEUnit) {
        $UBEUnit->ube_analyze_unit($is_simulator);
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
  public function ube_get_groups() {
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
  public function ube_get_capacity_attackers() {
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
  public function ube_db_load_from_report_row(array $report_row, UBE $ube) {
    $query = classSupernova::$db->doSelect("SELECT * FROM {{ube_report_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($fleet_row = db_fetch($query)) {
      $objFleet = new UBEFleet();
      $objFleet->load_from_report($fleet_row, $ube);
      $this[$objFleet->db_id] = $objFleet;
    }

  }

  /**
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function ube_prepare_for_next_round($is_simulator) {
    foreach($this->_container as $fleet_id => $UBEFleet) {
      $UBEFleet->prepare_for_next_round($is_simulator);
    }

    // Суммируем данные по атакующим и защитникам
    $this->ube_total[UBE_PLAYER_IS_ATTACKER]->_reset();
    $this->ube_total[UBE_PLAYER_IS_DEFENDER]->_reset();
    foreach($this->_container as $fleet_id => $UBEFleet) {
      $this->ube_total[$UBEFleet->is_attacker]->add_unit_stats_array($UBEFleet->total_stats);
    }
//pvar_dump($this->ube_total);

    // Высчитываем долю атаки, приходящейся на юнит равную отношению брони юнита к общей броне - крупные цели атакуют чаще
    foreach($this->_container as $fleet_id => $UBEFleet) {
      $UBEFleet->calculate_unit_partial_data($this->ube_total[$UBEFleet->is_attacker]);
    }
  }

  /**
   * Рассчитывает результат столкновения флотов ака раунд
   *
   * @param UBE $ube
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function ube_calculate_attack_results(UBE $ube) {
    // Каждый флот атакует все
    foreach($this->_container as $attack_fleet_data) {
      defined('DEBUG_UBE') ? print("Fleet {$attack_fleet_data->db_id} attacks<br /><div style='margin-left: 30px;'>") : false;

      $attack_fleet_data->attack_fleets($this, $ube->is_simulator);

      defined('DEBUG_UBE') ? print('</div>') : false;
    }
  }

  /**
   *
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function ube_actualize_sides() {
    foreach($this->_container as $UBEFleet) {
      if($UBEFleet->get_unit_count() > 0) {
        $this->ube_side_present_at_round_start[$UBEFleet->is_attacker] = 1;
      }
    }
  }

  /**
   * @return int
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function ube_calculate_attack_reapers() {
    $reapers = 0;
    foreach($this->_container as $fleet_id => $UBERoundFleetCombat) {
      if($UBERoundFleetCombat->is_attacker == UBE_PLAYER_IS_ATTACKER) {
        $reapers += $UBERoundFleetCombat->unit_list->unitCountReapers();
      }
    }

    return $reapers;
  }

  public function ube_get_sides_count() {
    return count($this->ube_side_present_at_round_start);
  }

  public function ubeAnalyzeFleetOutcome($round) {
    $this->ube_actualize_sides();

    $result = UBE_COMBAT_RESULT_DRAW;
    // Проверяем результат боя
    if($this->ube_get_sides_count() == 0 || $round >= 10) {
      // Если кого-то не осталось или не осталось обоих - заканчиваем цикл
      $result = UBE_COMBAT_RESULT_DRAW_END;
    } elseif($this->ube_get_sides_count() == 1) {
      // Если осталась одна сторона - она и выиграла
      $result = isset($this->ube_side_present_at_round_start[UBE_PLAYER_IS_ATTACKER]) ? UBE_COMBAT_RESULT_WIN : UBE_COMBAT_RESULT_LOSS;
    }

    return $result;
  }

  /**
   * Add attacking fleets
   *
   * @param Fleet         $objFleet
   * @param UBEPlayerList $players
   *
   */
  public function ubeInitGetAttackers(Fleet $objFleet, UBEPlayerList $players) {
    if($objFleet->group_id) {
      $fleets_added = $this->dbLoadWhere("`fleet_group` = {$objFleet->group_id}", DB_SELECT_FOR_UPDATE);
    } else {
      $this->ube_insert_from_Fleet($objFleet);
      $fleet_db_id = $objFleet->dbId;
      $fleets_added = array($fleet_db_id => $fleet_db_id);
    }

    $players->ubeLoadPlayersAndSetSideFromFleetIdList($fleets_added, $this, UBE_PLAYER_IS_ATTACKER);
  }

  /**
   * Add fleets on hold on planet orbit
   *
   * @param Fleet $objFleet
   *
   */
  public function ubeInitGetFleetsOnHold(Fleet $objFleet, UBEPlayerList $players) {
    $fleets_added = $this->dbLoadWhere(
      "`fleet_end_galaxy` = {$objFleet->fleet_end_galaxy}
        AND `fleet_end_system` = {$objFleet->fleet_end_system}
        AND `fleet_end_planet` = {$objFleet->fleet_end_planet}
        AND `fleet_end_type` = {$objFleet->fleet_end_type}
        AND `fleet_start_time` <= {$objFleet->time_arrive_to_target}
        AND `fleet_end_stay` >= {$objFleet->time_arrive_to_target}
        AND `fleet_mess` = 0"
      ,DB_SELECT_FOR_UPDATE
    );
    $players->ubeLoadPlayersAndSetSideFromFleetIdList($fleets_added, $this, UBE_PLAYER_IS_DEFENDER);
  }

}
