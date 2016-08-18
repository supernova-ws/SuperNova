<?php

namespace V2Unit;

use Common\IndexedObjectStorage;
use Common\IUnitLocationV2;
use DBStatic\DBStaticUnit;

/**
 * Class V2UnitList
 *
 * @method V2UnitContainer current()
 *
 * @package V2Unit
 */
class V2UnitList extends IndexedObjectStorage {
  /**
   *
   */
  public function load($locationType, $locationId) {

    if (!($unitRows = DBStaticUnit::db_get_unit_list_by_location(0, $locationType, $locationId))) {
      return;
    }

    $model = \classSupernova::$gc->unitModel;
    foreach ($unitRows as $dbId => $unitRow) {
      $unit = $model->fromArray($unitRow);
      $this->attach($unit, intval($unit->snId));
    }
  }

  public function loadFromContainer() {

  }

}
