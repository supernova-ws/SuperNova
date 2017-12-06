<?php
/**
 * Created by Gorlum 04.12.2017 0:40
 */

namespace Bonus;

use Fixtures\UnitInfo;

/**
 * Class BonusAtomPercentTest
 * @package Bonus
 * @coversDefaultClass \Bonus\BonusAtomPercent
 */
class BonusAtomPercentTest extends BonusAtomAbilityTest {

  /**
   * @var string $objectClass
   */
  protected $objectClass = BonusAtomPercent::class;

  /**
   * @var BonusAtomPercent $object
   */
  protected $object;

  protected function setUp() {
    parent::setUp();

    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus(BONUS_PERCENT, 25)->install();
  }

  public function dataAdjustValue() {
    return [
      [BonusAtom::RETURN_ALWAYS, 0, 0, 0, 0, 25],
      [BonusAtom::RETURN_ALWAYS, 0, 2.5, 0, 0, 25],
      [BonusAtom::RETURN_ALWAYS, 7, 0, 0, 7, 25],
      [BonusAtom::RETURN_ALWAYS, 6, 2.5, 3.75, 9.75, 25], // 25%(6) = 1.5; 1.5 * 2.5 = 3.75

      [BonusAtom::RETURN_ALWAYS, 6, 2.5, 15, 21, 100], // 100%(6) = 6; 6 * 2.5 = 15
      [BonusAtom::RETURN_ALWAYS, 6, 2.5, 0, 6, 0], // 0%(6) = 0; 0 * 2.5 = 0
      [BonusAtom::RETURN_ALWAYS, 6, 2.5, -3.75, 2.25, -25], // -25%(6) = -15; -1.5 * 2.5 = -3.75

      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 0, 0, 0, 0, 25],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 0, 2.5, 0, 0, 25],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 7, 0, 0, 7, 25],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 6, 2.5, 3.75, 9.75, 25],
    ];
  }

  /**
   * @covers ::adjustValue
   * @covers ::calcAdjustment
   * @dataProvider dataAdjustValue
   */
  public function testAdjustValue($isZeroReturned, $baseValue, $bonusAmount, $expectedResult, $expectedValue, $percent = 0) {
    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus(BONUS_PERCENT, $percent)->install();

    // Recalculating object because of new UnitInfo
    $this->object = new $this->objectClass(UNIT_TEST_ID_STRING_1, BonusAtom::RETURN_IF_BASE_NOT_ZERO);

    parent::testAdjustValue($isZeroReturned, $baseValue, $bonusAmount, $expectedResult, $expectedValue);
  }

}
