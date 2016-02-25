<?php

class UBERound {

  public $round_number = 0;

  /**
   * [$fleet_id][$unit_id]
   *
   * @var UBERoundFleetCombatList
   */
  public $fleet_combat_data = null; // UBERoundFleetCombatList

  public $round_outcome = UBE_COMBAT_RESULT_DRAW;

  public function __construct() {
    $this->fleet_combat_data = new UBERoundFleetCombatList();
  }

  public function __clone() {
    $this->fleet_combat_data = clone $this->fleet_combat_data;
  }

  /**
   * @param UBEFleetList $fleets
   * @param bool         $is_simulator
   */
  // OK3
  public function prepare_zero_round(UBEFleetList $fleets, $is_simulator) {
    $this->fleet_combat_data->init_from_UBEFleetList($fleets);
    $this->fleet_combat_data->sn_ube_combat_round_prepare($fleets, $is_simulator);
  }

  // ------------------------------------------------------------------------------------------------
  // Анализирует результаты раунда и генерирует данные для следующего раунда
  // OK3
  function sn_ube_combat_round_analyze($round) {
    $this->round_outcome = UBE_COMBAT_RESULT_DRAW;

    $nextRound = new UBERound();
    $nextRound->init_from_previous_round($this);

    $outcome = $nextRound->fleet_combat_data->side_present_at_round_start;

    // Проверяем результат боя
    if(count($outcome) == 0 || $round >= 10) {
      // Если кого-то не осталось или не осталось обоих - заканчиваем цикл
      $this->round_outcome = UBE_COMBAT_RESULT_DRAW_END;
    } elseif(count($outcome) == 1) {
      // Если осталась одна сторона - она и выиграла
      $this->round_outcome = isset($outcome[UBE_PLAYER_IS_ATTACKER]) ? UBE_COMBAT_RESULT_WIN : UBE_COMBAT_RESULT_LOSS;
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
