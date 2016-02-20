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
      UBE_PLANET => array(
        PLANET_ID     => $report_row['ube_report_planet_id'],
        PLANET_NAME   => $report_row['ube_report_planet_name'],
        PLANET_SIZE   => $report_row['ube_report_planet_size'],
        PLANET_GALAXY => $report_row['ube_report_planet_galaxy'],
        PLANET_SYSTEM => $report_row['ube_report_planet_system'],
        PLANET_PLANET => $report_row['ube_report_planet_planet'],
        PLANET_TYPE   => $report_row['ube_report_planet_planet_type'],
      ),

      UBE_CAPTURE_RESULT => $report_row['ube_report_capture_result'],

      UBE_ATTACKERS => array(),
      UBE_DEFENDERS => array(),
    );
  }

}
