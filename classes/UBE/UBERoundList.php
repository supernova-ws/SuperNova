<?php

/**
 * Class UBERoundList
 *
 * @method UBERound offsetGet($offset)
 * @property UBERound[] $_container
 */
class UBERoundList extends ContainerArrayOfObject {

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
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function report_render_rounds(&$template_result, UBE $ube) {
    $round_count = $this->count();
    for($round = 1; $round <= $round_count - 1; $round++) {
      $template_result['.']['round'][] = array(
        'NUMBER' => $round,
        '.'      => array(
          'fleet' => $this[$round]->report_render_round_fleet_list($ube, $this[$round - 1]->snapshot),
        ),
      );
    }
  }

  // REPORT ************************************************************************************************************
  //    REPORT SAVE ====================================================================================================
  /**
   * Сохраняем информацию о юнитах в раундах
   *
   * @param array        $sql_perform_ube_report_unit
   * @param int          $ube_report_id
   * @param UBEFleetList $UBEFleetList
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function sql_generate_unit_array(array &$sql_perform_ube_report_unit, $ube_report_id, UBEFleetList $UBEFleetList) {
    $unit_sort_order = 1;
    foreach($this->_container as $round_number => $UBERound) {
      $outer_prefix = array(
        $ube_report_id,
        $round_number,
      );
      $UBERound->sql_generate_unit_array($sql_perform_ube_report_unit, $unit_sort_order, $UBEFleetList, $outer_prefix);
    }
  }

  //    REPORT LOAD ====================================================================================================
  /**
   * @param     $report_row
   * @param UBE $ube
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function db_load_round_list_from_report_row($report_row, UBE $ube) {
    $query = classSupernova::$db->doSelect("SELECT * FROM {{ube_report_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_unit_id`");
    while($report_unit_row = db_fetch($query)) {
      $round_number = $report_unit_row['ube_report_unit_round'];
      if(!isset($this[$round_number])) {
        $this[$round_number] = new UBERound();
        $this[$round_number]->init_round_from_report_unit_row($report_unit_row);
      }
      // TODO - обработка ошибок
//      $player_side = $ube->players[$report_unit_row['ube_report_unit_player_id']]->player_side();

//$this[$round_number]->fleet_combat_data->load_fleet_from_report_unit_row($report_unit_row, $player_side);
      $this[$round_number]->load_snapshot_unit_from_report_unit_row($report_unit_row);
    }
  }

}
