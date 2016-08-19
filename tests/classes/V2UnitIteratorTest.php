<?php

use V2Unit\V2UnitIterator;

/**
 * Class V2UnitIteratorTest
 * @coversDefaultClass \V2Unit\V2UnitIterator
 */
class V2UnitIteratorTest extends PHPUnit_Framework_TestCase {

  /**
   * @var V2UnitIterator $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();

  }

  public function tearDown() {
    unset($this->object);
  }

  public function testATest() {
//    $unitList = new \V2Unit\V2UnitList();
//    $model = new \V2Unit\V2UnitModel(new \Common\GlobalContainer());
//
//    $unit = $model->getContainer();
//    $unit->importRow(array('unit_snid' => RES_METAL));
//    $unitList->attach($unit);
//    unset($unit);
//
//    $unit = $model->getContainer();
//    $unit->importRow(array('unit_snid' => STRUC_ALLY_DEPOSIT));
//    $unitList->attach($unit);
//    unset($unit);
//
//    $unit = $model->getContainer();
//    $unit->importRow(array('unit_snid' => RES_CRYSTAL));
//    $unitList->attach($unit);
//    unset($unit);
//
//    $iterator = new \V2Unit\V2UnitIterator($unitList);
//    $iterator->setFilterType(UNIT_STRUCTURES);
//    $iterator->setFilterType(UNIT_RESOURCES);
//
//    foreach($iterator as $key => $value) {
//      pdump($value->snId, '$value->snId');
//    }


  }

}
