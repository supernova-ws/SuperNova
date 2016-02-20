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
  public $debris = array();

  /**
   * Флаг РМФ
   *
   * @var int
   */
  public $is_small_fleet_recce = 0;

  public function __construct() {
    $this->debris_reset();
  }

  public function moon_create_try($is_simulator) {
    $this->outcome[UBE_MOON] = UBE_MOON_NONE;

    $debris_for_moon = $this->debris_total();

    if(!$debris_for_moon) {
      return;
    }

    // TODO uni_calculate_moon_chance
    $moon_chance = min($debris_for_moon / UBE_MOON_DEBRIS_PER_PERCENT, UBE_MOON_PERCENT_MAX); // TODO Configure
    $moon_chance = $moon_chance >= UBE_MOON_PERCENT_MIN ? $moon_chance : 0;
    $this->outcome[UBE_MOON_CHANCE] = $moon_chance;
    if($moon_chance) {
      if($is_simulator || mt_rand(1, 100) <= $moon_chance) {
        $this->outcome[UBE_MOON] = UBE_MOON_CREATE_SUCCESS;
        $this->outcome[UBE_MOON_SIZE] = round($is_simulator ? $moon_chance * 150 + 1999 : mt_rand($moon_chance * 100 + 1000, $moon_chance * 200 + 2999));

        if($debris_for_moon <= UBE_MOON_DEBRIS_MAX_SPENT) {
          $this->debris_reset();
        } else {
          $moon_debris_left_percent = ($debris_for_moon - UBE_MOON_DEBRIS_MAX_SPENT) / $debris_for_moon;
          $this->debris_adjust_proportional($moon_debris_left_percent);
        }
      } else {
        $this->outcome[UBE_MOON] = UBE_MOON_CREATE_FAILED;
      }
    }
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

      UBE_MOON        => $report_row['ube_report_moon'],
      UBE_MOON_CHANCE => $report_row['ube_report_moon_chance'],
      UBE_MOON_SIZE   => $report_row['ube_report_moon_size'],

      UBE_MOON_REAPERS            => $report_row['ube_report_moon_reapers'],
      UBE_MOON_DESTROY_CHANCE     => $report_row['ube_report_moon_destroy_chance'],
      UBE_MOON_REAPERS_DIE_CHANCE => $report_row['ube_report_moon_reapers_die_chance'],

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

  protected function debris_adjust_proportional($moon_debris_left_percent) {
    foreach($this->debris as $resource_id => &$resource_amount) {
      $resource_amount = floor($resource_amount * $moon_debris_left_percent);
    }
  }

}
