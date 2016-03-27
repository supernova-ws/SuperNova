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
    $this->unitList->dbLoad($this->_dbId, $this->lockSkip);

    // Высчитываем бонусы
    $this->player_bonus->add_unit_by_snid(MRC_ADMIRAL, mrc_get_level($this->db_row, null, MRC_ADMIRAL));
    $this->player_bonus->add_unit_by_snid(TECH_WEAPON, mrc_get_level($this->db_row, null, TECH_WEAPON));
    $this->player_bonus->add_unit_by_snid(TECH_SHIELD, mrc_get_level($this->db_row, null, TECH_SHIELD));
    $this->player_bonus->add_unit_by_snid(TECH_ARMOR, mrc_get_level($this->db_row, null, TECH_ARMOR));
  }

}
