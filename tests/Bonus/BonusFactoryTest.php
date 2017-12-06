<?php
/**
 * Created by Gorlum 04.12.2017 3:18
 */

namespace Bonus;

use Fixtures\UnitInfo;

/**
 * Class BonusFactoryTest
 * @package Bonus
 * @coversDefaultClass \Bonus\BonusFactory
 */
class BonusFactoryTest extends \PHPUnit_Framework_TestCase {

  public function dataBuild() {
    return [
      [BONUS_NONE, 2, BonusAtom::class],
      [BONUS_PERCENT, 2, BonusAtomPercent::class],
      [BONUS_ADD, 2, BonusAtomAdd::class],
      [BONUS_ABILITY, 2, BonusAtomAbility::class],
      [BONUS_MULTIPLY, 2, BonusAtomMultiply::class],
    ];
  }

  /**
   * @covers ::build
   * @dataProvider dataBuild
   */
  public function testBuild($type, $power, $className) {
    UnitInfo::build(UNIT_TEST_ID_STRING_1)->bonus($type, $power)->install();

    $bonus = BonusFactory::build(UNIT_TEST_ID_STRING_1, BonusAtom::RETURN_ALWAYS);

    // Checking that appropriate type returned
    $this->assertTrue($bonus instanceof $className);
    $this->assertEquals($power, $bonus->power);
  }

}
