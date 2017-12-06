<?php
/**
 * Created by Gorlum 04.12.2017 0:08
 */

namespace Bonus;

use Fixtures\UnitInfo;

/**
 * Class BonusAtomMultiplyTest
 * @package Bonus
 * @coversDefaultClass \Bonus\BonusAtomMultiply
 */
class BonusAtomMultiplyTest extends BonusAtomAbilityTest {

  /**
   * @var string $objectClass
   */
  protected $objectClass = BonusAtomMultiply::class;

  /**
   * @var BonusAtomMultiply $object
   */
  protected $object;

  protected function setUp() {
    parent::setUp();

    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus(BONUS_MULTIPLY, TEST_VALUE_INT_7)->install();
  }

  public function dataAdjustValue() {
    return [
      [BonusAtom::RETURN_ALWAYS, 0, 0, 0, 0],
      [BonusAtom::RETURN_ALWAYS, 0, 2.5, 0, 0],
      [BonusAtom::RETURN_ALWAYS, 7, 0, -7, 0], // Multiply is 0 so final result is 0 too. Also we should substract 7 to make base value equal 0
      [BonusAtom::RETURN_ALWAYS, 7, 2.5, 115.5, 122.5], // Unit power is 7, so 7 * 2.5 = 17.5 - multiplier. 17.5 * 7 = 122.5. (final)122.5 - (current)7 = 115.5 (returned)


      [BonusAtom::RETURN_ALWAYS, 2, 0.2, 0.8, 2.8], // 7 * 0.2 = 1.4;  1.4 * 2 = 2.8 (final); 2.8 - 2 = 0.8 (returned)
      [BonusAtom::RETURN_ALWAYS, 2, 0.1, -0.6, 1.4], // 7 * 0.1 = 0.7;  0.7 * 2 = 1.4 (final); 1.4 - 2 = -0.6 (returned)
      [BonusAtom::RETURN_ALWAYS, 5, 1, 30, 35], // Amount = 1

      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 0, 0, 0, 0],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 0, 2.5, 0, 0],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 7, 0, -7, 0],
      [BonusAtom::RETURN_IF_BASE_NOT_ZERO, 7, 2.5, 115.5, 122.5],
    ];
  }

}
