<?php

namespace V2Unit;

use Common\IndexedObjectStorage;
use Common\V2Location;
use DBStatic\DBStaticUnit;

/**
 * Class V2UnitList
 *
 * @method V2UnitContainer current()
 *
 * @package V2Unit
 */
class V2UnitList extends IndexedObjectStorage {

  public function load(V2Location $location) {
    if (!($unitRows = DBStaticUnit::getUnitListByV2Location($location))) {
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

  public function unitAdd($snId, $level) {
    if($this->indexIsSet($snId)) {
      $this->indexGetObject($snId)->$level += $level;
    } else {
      $unit = \classSupernova::$gc->unitModel->buildContainer();
      $unit->snId = $snId;
      $unit->level = $level;
      $this->attach($unit, $snId);
    }
  }

}
