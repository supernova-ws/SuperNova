<?php

class UBEOutcome {

  /**
   * [UBE_xxx]
   *
   * @var array
   */
  public $outcome = array();

  public $combat_result = 0;

  /**
   * Флаг РМФ
   *
   * @var int
   */
  public $is_small_fleet_recce = 0;

  public function __construct() {
  }

  public function load_from_report_row($report_row) {
    $this->combat_result = $report_row['ube_report_combat_result'];

    $this->is_small_fleet_recce = intval($report_row['ube_report_combat_sfr']);

    $this->outcome = array(
      UBE_CAPTURE_RESULT => $report_row['ube_report_capture_result'],

      UBE_ATTACKERS => array(),
      UBE_DEFENDERS => array(),
    );
  }

}
