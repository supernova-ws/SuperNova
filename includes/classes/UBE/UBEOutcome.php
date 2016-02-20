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
   * @var array [$resource_id] => (int)$resource_amount
   */
  protected $debris = array();

  /**
   * Флаг РМФ
   *
   * @var int
   */
  public $is_small_fleet_recce = 0;

  public function __construct() {
    $this->debris_reset();
  }

  public function load_from_report_row($report_row) {
    $this->combat_result = $report_row['ube_report_combat_result'];

    $this->debris_reset();
    $this->debris_add_resource(RES_METAL, $report_row['ube_report_debris_metal']);
    $this->debris_add_resource(RES_CRYSTAL, $report_row['ube_report_debris_crystal']);

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


  public function debris_reset() {
    $this->debris = array(
      RES_METAL   => 0,
      RES_CRYSTAL => 0,
    );
  }

  /**
   * @return array
   */
  public function debris_get() {
    return !empty($this->debris) ? $this->debris : array();
  }

  /**
   * @param int $resource_id
   *
   * @return float
   */
  public function debris_get_resource($resource_id) {
    return !empty($this->debris[$resource_id]) ? floor($this->debris[$resource_id]) : 0.0;
  }

  /**
   * @return float
   */
  public function debris_total() {
    return empty($this->debris) || !is_array($this->debris) ? 0.0 : floor(array_sum($this->debris));
  }

  /**
   * @param int   $resource_id
   * @param float $resource_amount
   */
  public function debris_add_resource($resource_id, $resource_amount) {
    // В обломках может быть только металл или кристалл
    if($resource_id != RES_METAL && $resource_id != RES_CRYSTAL) {
      return;
    }
    $this->debris[$resource_id] += $resource_amount;
  }

  public function debris_adjust_proportional($moon_debris_left_percent) {
    foreach($this->debris as $resource_id => &$resource_amount) {
      $resource_amount = floor($resource_amount * $moon_debris_left_percent);
    }
  }

}
