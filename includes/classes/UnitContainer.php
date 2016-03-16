<?php

/**
 * Object that have attached units via locationType/dbId
 */
abstract class UnitContainer extends DBRow {
  /**
   * Type of this location
   *
   * @var int $locationType
   */
  public static $locationType = LOC_NONE;
//  /**
//   * @var int
//   */
//  protected $db_id = 0;
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

  public function getLocationDbId() {
    return $this->getDbId();
  }

  abstract public function getPlayerOwnerId();

}
