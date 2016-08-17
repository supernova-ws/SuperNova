<?php

namespace V2Unit;
use DBStatic\DBStaticUnit;

/**
 * Class V2UnitList
 *
 * @method V2UnitContainer current()
 *
 * @package V2Unit
 */
class V2UnitList extends \SplObjectStorage {

  /**
   * @var array
   */
  protected $unitBySnId;

  /**
   */
  public function load($locationType, $locationId) {

    if(!($unitRows = DBStaticUnit::db_get_unit_list_by_location(0, $locationType, $locationId))) {
      return null;
    }

    $model = \classSupernova::$gc->unitModel;
    foreach($unitRows as $dbId => $unitRow) {
      $unit = $model->fromArray($unitRow);
      $this->attach($unit);
      if($unit->snId) {
        $this->unitBySnId[$unit->snId] = $unit;
      }
    }

    /**
     *
     *


     foreach(unitsInLocation($location) as $unit_row) {
       $unit_obj = new V2Unit()->load($unit_row);
        $this->attach($unit_obj, $unit_obj->dbId);
     }




     *
     */
  }

}
