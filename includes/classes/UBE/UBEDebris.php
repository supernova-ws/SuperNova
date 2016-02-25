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
    $this->_reset();
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
   * @param $moon_debris_left_part
   */
  public function debris_adjust_proportional($moon_debris_left_part) {
    foreach($this->debris as $resource_id => &$resource_amount) {
      $resource_amount = floor($resource_amount * $moon_debris_left_part);
    }
  }

  /**
   * @param classConfig $config
   *
   * @return int
   */
  public function debris_in_metal($config) {
    return floatval(
      ($this->debris_get_resource(RES_METAL) + $this->debris_get_resource(RES_CRYSTAL) * $config->rpg_exchange_crystal)
      /
      (floatval($config->rpg_exchange_metal) ? floatval($config->rpg_exchange_metal) : 1)
    );
  }

  /**
   * @param classConfig $config
   *
   * @return string
   */
  public function report_generate_sql(classConfig $config) {
    return "
      `ube_report_debris_metal` = " . (float)$this->debris_get_resource(RES_METAL) . ",
      `ube_report_debris_crystal` = " . (float)$this->debris_get_resource(RES_CRYSTAL) . ",
      `ube_report_debris_total_in_metal` = " . (float)$this->debris_in_metal($config) . ", ";
  }
}
