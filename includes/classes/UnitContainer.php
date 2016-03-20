<?php

/**
 * Object that have attached UnitList via locationType/dbId
 */
abstract class UnitContainer extends DBRowLocatedAt {


  // ILocation implementation ******************************************************************************************

  /**
   * Type of this location - READ ONLY!
   *
   * @var int $locationType
   */
  public static $locationType = LOC_NONE; // READ ONLY!


  // UnitContainer implementation **************************************************************************************

  /**
   * @var UnitList $unitList
   */
  public $unitList = null;


  public function __construct() {
    $this->unitList = new UnitList();
    $this->unitList->setLocatedAt($this);
  }

  /**
   * Временная функция, устанавливающая DB_ID текущего флота
   *
   * @param $fleet_id
   */
  // TODO - НЕЛЬЗЯ ТАК ДЕЛАТЬ! ЛИБО ФЛОТ УЖЕ СУЩЕСТВУЕТ - И ЕСТЬ ИД ЗАПИСИ, ЛИБО ЕГО ЕЩЕ НЕТ - И ТОГДА ИД РАВНО НУЛЮ!
  public function setDbId($fleet_id) {
    $this->dbId = idval($fleet_id);
  }


  public function dbRowParse($db_row) {
    parent::dbRowParse($db_row);
    $this->unitList->dbLoad($this->dbId);
  }

}
