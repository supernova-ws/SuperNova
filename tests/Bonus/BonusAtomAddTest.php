<?php
/**
 * Created by Gorlum 03.12.2017 23:54
 */

namespace Bonus;

use Fixtures\UnitInfo;

/**
 * Class BonusAtomAddTest
 * @package Bonus
 * @coversDefaultClass \Bonus\BonusAtomAdd
 */
class BonusAtomAddTest extends BonusAtomAbilityTest {

  /**
   * @var string $objectClass
   */
  protected $objectClass = BonusAtomAdd::class;

  /**
   * @var BonusAtomAdd $object
   */
  protected $object;

  protected function setUp() {
    parent::setUp();

    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus(BONUS_ADD, TEST_VALUE_INT_7)->install();
  }

  public function dataAdjustValue() {
    return [
      [BonusCatalog::VALUE_ANY, 0, 0, 0, 0],
      [BonusCatalog::VALUE_ANY, 0, 2.5, 17.5, 17.5], // Unit power is 7, so 7 * 2.5 = 17.5
      [BonusCatalog::VALUE_ANY, 7, 0, 0, 7],
      [BonusCatalog::VALUE_ANY, 7, 2.5, 17.5, 24.5],

      [BonusCatalog::VALUE_NON_ZERO, 0, 0, 0, 0],
      [BonusCatalog::VALUE_NON_ZERO, 0, 2.5, 0, 0],
      [BonusCatalog::VALUE_NON_ZERO, 7, 0, 0, 7],
      [BonusCatalog::VALUE_NON_ZERO, 7, 2.5, 17.5, 24.5],
    ];
  }

}
