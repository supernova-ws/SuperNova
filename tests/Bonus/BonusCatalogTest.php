<?php
/**
 * Created by Gorlum 03.12.2017 14:37
 */

namespace Bonus;

use SN;
use Core\GlobalContainer;
use \classConfig;

/**
 * Class BonusCatalogTest
 * @package Bonus
 * @coversDefaultClass \Bonus\BonusCatalog
 */
class BonusCatalogTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var BonusCatalog $object
   */
  protected $object;

  /**
   * @var GlobalContainer $gc
   */
  protected $gc;


  protected function setUp() {
    parent::setUp();

    $this->gc = $this->createMock(GlobalContainer::class);

    $this->object = new BonusCatalog($this->gc);
  }

  /**
   * @covers ::__construct
   * @covers ::loadDefaults
   */
  public function test__construct() {
    $this->assertAttributeEquals($this->gc, 'gc', $this->object);
  }

  /**
   * @covers ::registerBonus
   * @covers ::getBonusListAtom
   */
  public function testGetBonusDescriptions() {
    $this->assertNull($this->object->getBonusListAtom(UNIT_TEST_ID_STRING_1));
    $this->object->registerBonus(UNIT_TEST_ID_STRING_1, UNIT_TEST_ID_STRING_1);
    $this->object->registerBonus(UNIT_TEST_ID_STRING_1, UNIT_TEST_ID_STRING_2);
    $this->assertCount(2, $this->object->getBonusListAtom(UNIT_TEST_ID_STRING_1));
  }

  protected function tearDown() {
    parent::tearDown();

    unset($this->object);
  }

}
