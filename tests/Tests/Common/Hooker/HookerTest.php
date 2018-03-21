<?php
/**
 * Created by Gorlum 18.03.2018 16:57
 */

namespace Tests\Common\Hooker;

use Core\GlobalContainer;
use Common\Hooker\Pimp;
use Common\Hooker\Hooker;

/**
 * Class HookerTest
 * @package Tests
 * @coversDefaultClass \Common\Hooker\Hooker
 */
class HookerTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * @var Pimp $pimp
   */
  protected $pimp;

  /**
   * @var Hooker $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();

    $this->gc = new GlobalContainer();
    $this->pimp = new Pimp($this->gc);

    $this->object = new Hooker($this->pimp);
  }

  public function tearDown() {
    unset($this->object);
    unset($this->pimp);
    parent::tearDown();
  }


  /**
   * @covers ::__construct
   */
  public function test__construct() {
    $this->assertEquals($this->pimp, getPrivatePropertyValue($this->object, 'pimp'));
  }

  /**
   * @covers ::addClient
   */
  public function testAddClient() {
    $func1 = function ($prevResult, $arg) {return $arg + 1;};
    $func2 = function ($prevResult, $arg) {return $prevResult + 2;};
    $func3 = function ($prevResult, $arg) {return $prevResult + 3;};

    $this->object->addClient('functionNotExists');
    $this->object->addClient($func1);
    $this->object->addClient($func2);
    $this->object->addClient($func3, -1);

    $hookerClients = getPrivatePropertyValue($this->object, 'clients');
    $this->assertEquals([0 => ['functionNotExists', $func1, $func2], -1 => [$func3]], $hookerClients);

    // Checking that there was sorting applied
    reset($hookerClients);
    $this->assertEquals(-1, key($hookerClients));
  }

  /**
   * @covers ::serve
   * @covers ::__invoke
   */
  public function testServe() {
    $this->assertNull($this->object->serve(5));
    $this->assertNull($this->object->__invoke(5));

    $func1 = function ($prevResult, $arg) {return $arg + 1;};
    $func2 = function ($prevResult, $arg) {return $prevResult + 2;};

    $this->object->addClient($func1);
    $this->object->addClient($func2);

    $this->assertEquals(8, $this->object->serve([5]));
    $this->assertEquals(8, $this->object->__invoke([5]));

    // Testing not an array as argument
    $this->assertEquals(8, $this->object->serve(5));
    $this->assertEquals(8, $this->object->__invoke(5));
  }

}
