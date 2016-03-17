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

  public function dbRowParse($db_row) {
    parent::dbRowParse($db_row);
    $this->unitList->loadByLocation($this);
  }

}
