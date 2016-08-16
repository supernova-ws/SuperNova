<?php

namespace V2Unit;

/**
 * Class V2UnitList
 *
 * @method V2UnitContainer current()
 *
 * @package V2Unit
 */
class V2UnitList extends \SplObjectStorage {

  /**
   * @param $location - Typed location with ID
   */
  public function load($location) {
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
