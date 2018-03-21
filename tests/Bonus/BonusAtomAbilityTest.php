<?php
/**
 * Created by Gorlum 03.12.2017 22:55
 */

namespace Bonus;

use Core\GlobalContainer;
use Fixtures\UnitInfo;

/**
 * Class BonusAtomAbilityTest
 * @package Bonus
 * @coversDefaultClass \Bonus\BonusAtomAbility
 */
class BonusAtomAbilityTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var string $objectClass
   */
  protected $objectClass = BonusAtomAbility::class;

  /**
   * @var BonusAtomAbility $object
   */
  protected $object;

  protected function setUp() {
    parent::setUp();

    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus(BONUS_ABILITY, TEST_VALUE_INT_7)->install();
    $this->object = new $this->objectClass(UNIT_TEST_ID_STRING_1, BonusAtom::RETURN_IF_BASE_NOT_ZERO);
  }

  public function dataAdjustValue() {
    return [
      [BonusAtom::RETURN_ALWAYS, 0, 0, 0, 0],
      [BonusAtom::RETURN_ALWAYS, 0, 2.5, 1, 1],
      [BonusAtom::RETURN_ALWAYS, 7, 0, 0, 1],
      [BonusAtom::RETURN_ALWAYS, 7, 2.5, 1, 1],

      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 0, 0, 0, 0],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 0, 2.5, 0, 0],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 7, 0, 0, 1],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 7, 2.5, 1, 1],
    ];
  }

  /**
   * @covers ::adjustValue
   * @covers ::calcAdjustment
   * @dataProvider dataAdjustValue
   */
  public function testAdjustValue($isZeroReturned, $baseValue, $bonusAmount, $expectedResult, $expectedValue) {
    /**
     * @var ValueBonused $valueBonused
     */
    \SN::$gc = new GlobalContainer();
    $valueBonused = $this->getMockBuilder(ValueBonused::class)
      ->setConstructorArgs([UNIT_TEST_ID_STRING_0, $baseValue])
      ->getMock();

    $this->object->ifBaseNonZero = $isZeroReturned;

    $this->assertEquals($expectedResult, $this->object->adjustValue($valueBonused->value, $bonusAmount, $valueBonused->base));
    $this->assertEquals($expectedValue, $valueBonused->value);
  }

}
