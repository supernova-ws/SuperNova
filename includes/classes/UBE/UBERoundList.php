<?php

/**
 * Class UBERoundList
 *
 * @method UBERound offsetGet($offset)
 * @property UBERound[] $_container
 */
class UBERoundList extends ArrayAccessV2 {

  /**
   * @return UBERound
   */
  public function get_last_element() {
    return end($this->_container);
  }


  // REPORT RENDER *****************************************************************************************************
  // Генерируем отчет по флотам
  /**
   * @param     $template_result
   * @param UBE $ube
   */
  // OK3
  public function report_render_rounds(&$template_result, UBE $ube) {
    $round_count = $this->count();
    for($round = 1; $round <= $round_count - 1; $round++) {
      $template_result['.']['round'][] = array(
        'NUMBER' => $round,
        '.'      => array(
          'fleet' => $this[$round]->report_render_round($ube, $this[$round - 1]), // OK3
        ),
      );
    }
  }

  /**
   * Сохраняем информацию о юнитах в раундах
   *
   * @param $ube_report_id
   * @param $sql_perform_ube_report_unit
   */
  // OK3
  public function sql_generate_unit_array($ube_report_id, &$sql_perform_ube_report_unit) {
    $unit_sort_order = 1;
    foreach($this->_container as $round_number => $UBERound) {
      $UBERound->fleet_combat_data->sql_generate_unit_array($ube_report_id, $sql_perform_ube_report_unit, $unit_sort_order, $round_number);
    }
  }

  public function db_load_round_list_from_report_row($report_row, UBE $ube) {
    $query = doquery("SELECT * FROM {{ube_report_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_unit_id`");
    while($report_unit_row = db_fetch($query)) {
      $round_number = $report_unit_row['ube_report_unit_round'];
      if(!isset($this[$round_number])) {
        $this[$round_number] = new UBERound();
        $this[$round_number]->init_round_from_report_unit_row($report_unit_row);
      }
      // TODO - обработка ошибок
      $player_side = $ube->players[$report_unit_row['ube_report_unit_player_id']]->player_side();

      $this[$round_number]->fleet_combat_data->load_fleet_from_report_unit_row($report_unit_row, $player_side);
    }
  }

}
