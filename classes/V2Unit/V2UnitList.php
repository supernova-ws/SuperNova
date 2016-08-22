<?php

namespace V2Unit;

use Common\GlobalContainer;
use Common\ObjectCollection;
use Common\V2Location;
use DBStatic\DBStaticUnit;

/**
 * Class V2UnitList
 *
 * @method V2UnitContainer current()
 *
 * @package V2Unit
 */
class V2UnitList extends ObjectCollection {
  /**
   * @var V2UnitModel $unitModel;
   */
  protected $unitModel;

  public function __construct(GlobalContainer $gc) {
    $this->unitModel = $gc->unitModel;
  }

  public function load(V2Location $location) {
    if (!($unitRows = DBStaticUnit::getUnitListByV2Location($location))) {
      return;
    }

    foreach ($unitRows as $dbId => $unitRow) {
      $unit = $this->unitModel->fromArray($unitRow);
      $this[$unit->snId] = $unit;
    }
  }

  public function unitAdd($snId, $level) {
    $unit = $this->unitModel->buildContainer();
    $unit->snId = $snId;
    $unit->level = $level;
    $this[$snId] = $unit;
  }

  /**
   * Function called when index already exists
   *
   * Can be used by child object
   *
   * @param V2UnitContainer $newUnit
   * @param int             $snId
   *
   * @throws \Exception
   */
  protected function indexDuplicated($newUnit, $snId) {
    // TODO - error if not stackable
    $this[$snId]->level += $newUnit->level;

    return false;
  }

  public function isEmpty() {
    // TODO - sum of unit count

    return $this->count() > 0;
  }

}
