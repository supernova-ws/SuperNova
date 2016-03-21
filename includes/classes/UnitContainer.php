<?php

/**
 * Object that have attached UnitList via locationType/dbId
 */
abstract class UnitContainer extends DBRowLocation {


  // UnitContainer implementation **************************************************************************************

  /**
   * @var UnitList $unitList
   */
  protected $unitList = null;

  public function __construct() {
    parent::__construct();
    $this->unitList = new UnitList();
    $this->unitList->setLocatedAt($this);
    $this->triggerDbOperationOn[] = $this->unitList;
  }

  public function dbRowParse($db_row) {
    parent::dbRowParse($db_row);
    $this->unitList->dbLoad($this->_dbId);
  }

}
