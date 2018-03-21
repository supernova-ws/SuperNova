<?php
/**
 * Created by Gorlum 18.03.2018 17:41
 */

namespace Tests\Common\Hooker;

use Core\GlobalContainer;
use Common\Hooker\Hooker;
use Common\Hooker\Pimp;

/**
 * Class HookerTest
 * @package Tests
 * @coversDefaultClass \Common\Hooker\Pimp
 */
class PimpTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var Pimp $pimp
   */
  protected $object;

  public function setUp() {
    parent::setUp();

    $this->gc = new GlobalContainer();
    $this->object = new Pimp($this->gc);
  }

  public function tearDown() {
    unset($this->object);
    unset($this->gc);

    parent::tearDown();
  }

  /**
   * @covers ::__construct
   */
  public function test__construct() {
    $this->assertEquals($this->gc, getPrivatePropertyValue($this->object, 'gc'));
  }

  /**
   * @covers ::register
   */
  public function testRegister() {
    $this->object->register(UNIT_TEST_STRING, function () {});

    $hookers = getPrivatePropertyValue($this->object, 'hookers');

    $this->assertTrue(is_array($hookers));
    $this->assertTrue($hookers[UNIT_TEST_STRING] instanceof Hooker);
  }

  /**
   * @covers ::__call
   */
  public function test__call() {
    $this->object->register(UNIT_TEST_STRING, function ($prevResult, $arg) {return $arg + 2;});
    $this->object->register(UNIT_TEST_STRING, function ($prevResult, $arg) {return $prevResult + 4;});

    $this->assertEquals(7, $this->object->test(1));

    $this->assertNull($this->object->testNotExists(1));
  }

}
