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
      [BonusCatalog::VALUE_ANY, 0, 0, 0, 0, 25],
      [BonusCatalog::VALUE_ANY, 0, 2.5, 0, 0, 25],
      [BonusCatalog::VALUE_ANY, 7, 0, 0, 7, 25],
      [BonusCatalog::VALUE_ANY, 6, 2.5, 3.75, 9.75, 25], // 25%(6) = 1.5; 1.5 * 2.5 = 3.75

      [BonusCatalog::VALUE_ANY, 6, 2.5, 15, 21, 100], // 100%(6) = 6; 6 * 2.5 = 15
      [BonusCatalog::VALUE_ANY, 6, 2.5, 0, 6, 0], // 0%(6) = 0; 0 * 2.5 = 0
      [BonusCatalog::VALUE_ANY, 6, 2.5, -3.75, 2.25, -25], // -25%(6) = -15; -1.5 * 2.5 = -3.75

      [BonusCatalog::VALUE_NON_ZERO, 0, 0, 0, 0, 25],
      [BonusCatalog::VALUE_NON_ZERO, 0, 2.5, 0, 0, 25],
      [BonusCatalog::VALUE_NON_ZERO, 7, 0, 0, 7, 25],
      [BonusCatalog::VALUE_NON_ZERO, 6, 2.5, 3.75, 9.75, 25],
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
    $this->object = new $this->objectClass(UNIT_TEST_ID_STRING_1, BonusCatalog::VALUE_NON_ZERO);

    parent::testAdjustValue($isZeroReturned, $baseValue, $bonusAmount, $expectedResult, $expectedValue);
  }

}
