<?php

/**
 * Created by PhpStorm.
 * User: Gorlum
 * Date: 20.02.2016
 * Time: 18:04
 */
class UBEDebris {

  /**
   * @var array [$resource_id] => (int)$resource_amount
   */
  protected $debris = array();

  public function __construct() {
//    $this->_reset();
  }

  public function _reset() {
    $this->debris = array(
      RES_METAL   => 0,
      RES_CRYSTAL => 0,
    );
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

  /**
   * @param array $wreckage Список ресурсов, выпавших в обломки с кораблей
   * @param bool  $is_simulator
   */
  public function add_wrecks(array $wreckage, $is_simulator) {
    foreach($wreckage as $resource_id => $resource_amount) {
      $this->debris_add_resource($resource_id, floor($resource_amount *
        ($is_simulator
          ? UBE_SHIP_WRECKS_TO_DEBRIS_AVG
          : mt_rand(UBE_SHIP_WRECKS_TO_DEBRIS_MIN, UBE_SHIP_WRECKS_TO_DEBRIS_MAX)
        )
        / 100
      ));
    }
  }

  /**
   * @param array $dropped_resources Список ресурсов, выброшенных из трюма
   * @param bool  $is_simulator
   */
  public function add_cargo_drop(array $dropped_resources, $is_simulator) {
    foreach($dropped_resources as $resource_id => $resource_amount) {
      $this->debris_add_resource($resource_id, floor($resource_amount *
        ($is_simulator
          ? UBE_CARGO_DROPPED_TO_DEBRIS_AVG
          : mt_rand(UBE_CARGO_DROPPED_TO_DEBRIS_MIN, UBE_CARGO_DROPPED_TO_DEBRIS_MAX)
        )
        / 100
      ));
    }
  }


  /**
   * @return array
   */
  public function get_debris() {
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
   * @param $moon_debris_left_part
   */
  public function debris_adjust_proportional($moon_debris_left_part) {
    foreach($this->debris as $resource_id => &$resource_amount) {
      $resource_amount = floor($resource_amount * $moon_debris_left_part);
    }
  }

  /**
   * @return int
   */
  public function debris_in_metal() {
    return floatval(
      ($this->debris_get_resource(RES_METAL) + $this->debris_get_resource(RES_CRYSTAL) * classSupernova::$config->rpg_exchange_crystal)
      /
      (floatval(classSupernova::$config->rpg_exchange_metal) ? floatval(classSupernova::$config->rpg_exchange_metal) : 1)
    );
  }

  /**
   * @return string
   */
  public function report_generate_sql() {
    return "
      `ube_report_debris_metal` = " . (float)$this->debris_get_resource(RES_METAL) . ",
      `ube_report_debris_crystal` = " . (float)$this->debris_get_resource(RES_CRYSTAL) . ",
      `ube_report_debris_total_in_metal` = " . (float)$this->debris_in_metal() . ", ";
  }

  /**
   * @param array $report_row
   */
  public function load_from_report_row(array $report_row) {
//    $this->_reset();
    $this->debris_add_resource(RES_METAL, $report_row['ube_report_debris_metal']);
    $this->debris_add_resource(RES_CRYSTAL, $report_row['ube_report_debris_crystal']);
  }

}
