<?php
/**
 * Created by Gorlum 03.12.2017 14:49
 */

namespace Bonus;

use Fixtures\UnitInfo;

/**
 * Class BonusAtomTest
 * @package Bonus
 * @coversDefaultClass \Bonus\BonusAtom
 */
class BonusAtomTest extends BonusAtomAbilityTest {

  /**
   * @var string $objectClass
   */
  protected $objectClass = BonusAtom::class;

  /**
   * @var BonusAtom $object
   */
  protected $object;

//  protected function setUp() {
//    parent::setUp();
//
//    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus(BONUS_MULTIPLY, TEST_VALUE_INT_7)->install();
//    $this->object = new $this->objectClass(UNIT_TEST_ID_STRING_1, BonusCatalog::VALUE_NON_ZERO);
//  }

  /**
   * @covers ::calcBonusType
   */
  public function testCalcBonusType() {
    $this->assertEquals(BONUS_ADD, BonusAtom::calcBonusType(UnitInfo::build()->asArray()));
    $this->assertEquals(BONUS_MULTIPLY, BonusAtom::calcBonusType(UnitInfo::build()->bonus(BONUS_MULTIPLY)->asArray()));
  }

  /**
   * @covers ::calcBonusPower
   */
  public function testCalcBonusPower() {
    $this->assertEquals(TEST_VALUE_INT_7, BonusAtom::calcBonusPower(UnitInfo::build()->bonus(BONUS_MULTIPLY, TEST_VALUE_INT_7)->asArray()));

    $this->assertEquals(TEST_VALUE_INT_1, BonusAtom::calcBonusPower(UnitInfo::build()->bonus(BONUS_ABILITY)->asArray()));
    $this->assertEquals(TEST_VALUE_INT_0, BonusAtom::calcBonusPower(UnitInfo::build()->bonus(BONUS_MULTIPLY)->asArray()));
  }

  /**
   * @covers ::__construct
   * @backupGlobals enabled
   */
  public function test__construct() {

    $this->assertAttributeEquals(UNIT_TEST_ID_STRING_1, 'snId', $this->object);
    $this->assertAttributeEquals(true, 'ifBaseNonZero', $this->object);
    $this->assertAttributeEquals(TEST_VALUE_INT_7, 'power', $this->object);
  }

  public function dataIsReturnNothing() {
    return [
      [BonusAtom::RETURN_ALWAYS, 0, false],
      [BonusAtom::RETURN_ALWAYS, 1, false],

      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 0, true],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 1, false],
    ];
  }


  /**
   * @covers ::isReturnNothing
   * @dataProvider dataIsReturnNothing
   */
  public function testIsReturnNothing($isZeroReturned, $baseValue, $expected) {
    /**
     * @var ValueBonused $valueBonused
     */
    $valueBonused = $this->createMock(ValueBonused::class);
    $valueBonused->base = $baseValue;

    $this->object = new BonusAtom(UNIT_TEST_ID_STRING_1, $isZeroReturned);
    $this->assertEquals($expected, invokeMethod($this->object, 'isReturnNothing', [$valueBonused->base]));
  }

  public function dataAdjustValue() {
    return [
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 0, 0, 0, 0],

      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 2, 3, 0, 2],
    ];
  }

}
