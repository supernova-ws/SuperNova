<?php

/**
 * Object that have attached units via locationType/dbId
 */
abstract class UnitContainer {
  /**
   * Type of this location
   *
   * @var int $locationType
   */
  public static $locationType = LOC_NONE;
  /**
   * `fleet_id`
   *
   * @var int
   */
  protected $db_id = 0;
  /**
   * @var UnitList $unitList
   */
  public $unitList = null;

  public function __construct() {
    $this->unitList = new UnitList();
  }

  /**
   * @return int
   */
  public function getDbId() {
    return $this->db_id;
  }

}
