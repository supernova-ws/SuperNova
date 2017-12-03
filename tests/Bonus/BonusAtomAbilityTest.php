<?php
/**
 * Created by Gorlum 03.12.2017 22:55
 */

namespace Bonus;

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
    $this->object = new $this->objectClass(UNIT_TEST_ID_STRING_1, BonusCatalog::VALUE_NON_ZERO);
  }

  public function dataAdjustValue() {
    return [
      [BonusCatalog::VALUE_ANY, 0, 0, 0, 0],
      [BonusCatalog::VALUE_ANY, 0, 2.5, 1, 1],
      [BonusCatalog::VALUE_ANY, 7, 0, 0, 1],
      [BonusCatalog::VALUE_ANY, 7, 2.5, 1, 1],

      [BonusCatalog::VALUE_NON_ZERO, 0, 0, 0, 0],
      [BonusCatalog::VALUE_NON_ZERO, 0, 2.5, 0, 0],
      [BonusCatalog::VALUE_NON_ZERO, 7, 0, 0, 1],
      [BonusCatalog::VALUE_NON_ZERO, 7, 2.5, 1, 1],
    ];
  }

  /**
   * @covers ::adjustValue
   * @dataProvider dataAdjustValue
   */
  public function testAdjustValue($isZeroReturned, $baseValue, $bonusAmount, $expectedResult, $expectedValue) {
    /**
     * @var ValueBonused $valueBonused
     */
    $valueBonused = $this->createMock(ValueBonused::class);
    $valueBonused->base = $baseValue;
    $valueBonused->value = $baseValue;

    $this->object->ifBaseNonZero = $isZeroReturned;
    $this->assertEquals($expectedResult, $this->object->adjustValue($bonusAmount, $valueBonused));
    $this->assertEquals($expectedValue, $valueBonused->value);
  }

}
