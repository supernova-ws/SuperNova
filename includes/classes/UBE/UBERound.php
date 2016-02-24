<?php

class UBERound {

  public $round_number = 0;

  /**
   * [$fleet_id][$unit_id]
   *
   * @var UBERoundFleetCombatList
   */
  public $fleet_combat_data = null; // UBERoundFleetCombatList

  /**
   * @var UBEFleet[]
   */
  // TODO - переместить внутрь UBEFleetCombat!!!!!
  public $fleet_info = array(); // [UBE_FLEET_INFO] // TODO - UBEFleetList

  public $UBE_OUTCOME = UBE_COMBAT_RESULT_DRAW;

  public function __construct() {
    $this->fleet_combat_data = new UBERoundFleetCombatList();
  }

  public function __clone() {
// TODO !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// $fleet_info - клонировать? Инициализировать?
    $this->fleet_combat_data = clone $this->fleet_combat_data;
  }

  /**
   * @param UBEFleetList  $fleets
   * @param UBEPlayerList $players
   */
  // OK3
  public function init_zero_round(UBEFleetList $fleets, UBEPlayerList $players) {
    foreach($fleets->_container as $fleet_id => $objFleet) {
      // TODO - эта последовательность должна быть при загрузке флота (?)
//      $objFleet->UBE_COUNT = is_array($objFleet->UBE_COUNT) ? $objFleet->UBE_COUNT : array();
      $objFleet->copy_stats_from_player($players[$objFleet->UBE_OWNER]);
      // Вычисляем бонус игрока и добавляем его к бонусам флота
      $objFleet->add_player_bonuses($players[$objFleet->UBE_OWNER]);
//      $objFleet->add_planet_bonuses(); // TODO
      $objFleet->calculate_battle_stats();

      $this->fleet_combat_data->insert_from_UBEFleet($objFleet); // $first_round->round_fleets[$fleet_id][UBE_COUNT] = array();
    }
  }

  /**
   * @param UBEFleetList $fleets
   * @param bool         $is_simulator
   */
  // OK3
  public function sn_ube_combat_round_prepare(UBEFleetList $fleets, $is_simulator) {
    foreach($fleets as $fleet_id => $temp) {
      // Кэшируем переменные для легкого доступа к подмассивам
      $this->fleet_info[$fleet_id] = $fleets[$fleet_id];
    }

    // Суммируем данные по атакующим и защитникам
    // Высчитываем долю атаки, приходящейся на юнит равную отношению брони юнита к общей броне - крупные цели атакуют чаще
    $this->fleet_combat_data->sn_ube_combat_round_prepare($this->fleet_info, $is_simulator);
  }

  // ------------------------------------------------------------------------------------------------
  // Анализирует результаты раунда и генерирует данные для следующего раунда
  // OK3
  function sn_ube_combat_round_analyze($round) {
    $this->UBE_OUTCOME = UBE_COMBAT_RESULT_DRAW;

    $nextRound = new UBERound();
    $nextRound->init_from_previous_round($this);

    $outcome = $nextRound->fleet_combat_data->side_present_at_round_start;

    // Проверяем результат боя
    if(count($outcome) == 0 || $round >= 10) {
      // Если кого-то не осталось или не осталось обоих - заканчиваем цикл
      $round_data[UBE_OUTCOME] = UBE_COMBAT_RESULT_DRAW_END;
    } elseif(count($outcome) == 1) {
      // Если осталась одна сторона - она и выиграла
      $round_data[UBE_OUTCOME] = isset($outcome[UBE_PLAYER_IS_ATTACKER]) ? UBE_COMBAT_RESULT_WIN : UBE_COMBAT_RESULT_LOSS;
    }

    return $nextRound;
  }

  /**
   * @param UBERound $prevRound
   */
  // OK3
  function init_from_previous_round(UBERound $prevRound) {
    $this->round_number = $prevRound->round_number + 1;
    $prevRound->fleet_combat_data->copy_active_data_to_other($this->fleet_combat_data);
  }








  // REPORT ************************************************************************************************************
  //    REPORT LOAD ====================================================================================================
  /**
   * @param $report_unit_row
   */
  // OK3
  public function init_round_from_report_unit_row($report_unit_row) {
    $this->round_number = $report_unit_row['ube_report_unit_round'];
  }


  //    REPORT RENDER ==================================================================================================
  /**
   * @param UBE      $ube
   * @param UBERound $previousRound
   *
   * @return array
   */
  // Оставляем, потому что правильно концептуально - вызываем рендер раунда, а тот уже проводит рендеринг своих элементов
  // OK3
  public function report_render_round(UBE $ube, UBERound $previousRound) {
    return $this->fleet_combat_data->report_render_round_fleet_list($ube, $previousRound);
  }

}
