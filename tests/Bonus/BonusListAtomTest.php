<?php
/**
 * Created by Gorlum 06.12.2017 16:27
 */

namespace Bonus;

use Fixtures\UnitInfo;

/**
 * Class BonusListAtomTest
 * @package Bonus
 * @coversDefaultClass \Bonus\BonusListAtom
 */
class BonusListAtomTest extends \PHPUnit_Framework_TestCase {

  protected $object;

  /**
   * @covers ::__construct
   * @covers ::addUnit
   * @covers ::bonusSort
   * @covers ::count
   * @covers ::getBonusAtoms
   * @backupGlobals enable
   */
  public function test__construct() {
    // Main bonus
    UnitInfo::build(UNIT_TEST_ID_STRING_0)->bonus(BONUS_MULTIPLY, TEST_VALUE_INT_7)->install();
    // Installing bonuses
    UnitInfo::build(UNIT_TEST_ID_STRING_5)->bonus(BONUS_MULTIPLY, TEST_VALUE_INT_7)->install();
    UnitInfo::build(UNIT_TEST_ID_STRING_4)->bonus(BONUS_PERCENT, TEST_VALUE_INT_7)->install();
    UnitInfo::build(UNIT_TEST_ID_STRING_3)->bonus(BONUS_ADD, TEST_VALUE_INT_7)->install();
    UnitInfo::build(UNIT_TEST_ID_STRING_2)->bonus(BONUS_ABILITY, TEST_VALUE_INT_7)->install();
    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus(BONUS_NONE, TEST_VALUE_INT_7)->install();

    $this->object = new BonusListAtom(UNIT_TEST_ID_STRING_0);
    $this->assertAttributeEquals(UNIT_TEST_ID_STRING_0, 'bonusId', $this->object);

    $this->object->addUnit(UNIT_TEST_ID_STRING_1);
    $this->object->addUnit(UNIT_TEST_ID_STRING_5);
    $this->assertEquals(2, $this->object->count());

    $this->object->addUnit(UNIT_TEST_ID_STRING_2);
    $this->object->addUnit(UNIT_TEST_ID_STRING_3);
    $this->object->addUnit(UNIT_TEST_ID_STRING_4);

    $this->assertEquals(5, $this->object->count());

    $atomList = $this->object->getBonusAtoms();
    $i = 0;
    $checkOrder = [
      UNIT_TEST_ID_STRING_1,
      UNIT_TEST_ID_STRING_2,
      UNIT_TEST_ID_STRING_3,
      UNIT_TEST_ID_STRING_4,
      UNIT_TEST_ID_STRING_5,
    ];
    foreach ($atomList as $bonusUnitId => $bonusAtom) {
      $this->assertEquals($checkOrder[$i++], $bonusAtom->snId);
    }
  }

}
