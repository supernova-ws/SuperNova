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
//      [BonusCatalog::VALUE_ANY, 7, 2.5, 115.5, 122.5, 25], // Unit power is 3, so 7 * 2.5 = 17.5 - multiplier. 17.5 * 7 = 122.5. (final)122.5 - (current)7 = 115.5 (returned)
//
//
//      [BonusCatalog::VALUE_ANY, 2, 0.2, 0.8, 2.8, 25], // 7 * 0.2 = 1.4;  1.4 * 2 = 2.8 (final); 2.8 - 2 = 0.8 (returned)
//      [BonusCatalog::VALUE_ANY, 2, 0.1, -0.6, 1.4, 25], // 7 * 0.1 = 0.7;  0.7 * 2 = 1.4 (final); 1.4 - 2 = -0.6 (returned)
//      [BonusCatalog::VALUE_ANY, 5, 1, 30, 35, 25], // Amount = 1
//
      [BonusCatalog::VALUE_NON_ZERO, 0, 0, 0, 0, 25],
      [BonusCatalog::VALUE_NON_ZERO, 0, 2.5, 0, 0, 25],
      [BonusCatalog::VALUE_NON_ZERO, 7, 0, 0, 7, 25],
//      [BonusCatalog::VALUE_NON_ZERO, 7, 2.5, 115.5, 122.5, 25],
    ];
  }

  /**
   * @covers ::adjustValue
   * @dataProvider dataAdjustValue
   */
  public function testAdjustValue($isZeroReturned, $baseValue, $bonusAmount, $expectedResult, $expectedValue, $percent = 0) {
    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus(BONUS_PERCENT, $percent)->install();

    parent::testAdjustValue($isZeroReturned, $baseValue, $bonusAmount, $expectedResult, $expectedValue);
  }

}
