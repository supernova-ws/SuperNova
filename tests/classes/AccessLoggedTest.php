<?php
/**
 * Created by Gorlum 25.06.2017 13:07
 */

namespace classes;

use Common\AccessLogged;

/**
 * Class AccessLoggedTest
 * @coversDefaultClass \Common\AccessLogged
 * @package classes
 */
class AccessLoggedTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var AccessLogged $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();

    $this->object = new AccessLogged();
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  public function test__construct() {
    // Checking initial state
    $this->assertTrue($this->object->isEmpty());
    $this->assertFalse(isset($this->object->test));
    $this->assertNull($this->object->test);
    $this->assertAttributeEquals([], 'values', $this->object);
    $this->assertAttributeEquals([], '_startValues', $this->object);
    $this->assertAttributeEquals([], '_changes', $this->object);
    $this->assertAttributeEquals([], '_deltas', $this->object);
  }

  /**
   * Checking values setting
   *
   * @covers ::__set
   * @covers ::valueSet
   * @covers ::blockChange
   * @covers ::getChanges
   */
  public function testSet() {
    // Checking base __set()
    $this->object->test = 5;
    $this->assertFalse($this->object->isEmpty());
    $this->assertTrue(isset($this->object->test));
    $this->assertTrue($this->object->__isset('test'));
    $this->assertEquals(5, $this->object->test);
    $this->assertAttributeEquals(['test' => 5], 'values', $this->object);
    $this->assertAttributeEquals(['test' => 5], '_startValues', $this->object);
    $this->assertAttributeEquals([], '_changes', $this->object);
    $this->assertAttributeEquals([], '_deltas', $this->object);

    // Checking first value change
    $this->object->test = 6;
    $this->assertEquals(6, $this->object->test);
    $this->assertAttributeEquals(['test' => 6], 'values', $this->object);
    $this->assertAttributeEquals(['test' => 5], '_startValues', $this->object);
    $this->assertAttributeEquals(['test' => 6], '_changes', $this->object);
    $this->assertAttributeEquals([], '_deltas', $this->object);

    // Checking second value change
    $this->object->test = 7;
    $this->assertEquals(7, $this->object->test);
    $this->assertAttributeEquals(['test' => 7], 'values', $this->object);
    $this->assertAttributeEquals(['test' => 5], '_startValues', $this->object);
    $this->assertAttributeEquals(['test' => 7], '_changes', $this->object);
    $this->assertAttributeEquals([], '_deltas', $this->object);

    // Checking changes extraction
    $this->assertAttributeEquals($this->object->getChanges(), '_changes', $this->object);

    // Checking change block after increment
    $this->expectExceptionMessage('Common\AccessLogged::test2 already INCREMENTED/DECREMENTED - can not CHANGE');
    $this->object->inc()->test2 = 7;
    $this->object->test2 = 10;
  }

  /**
   * Checking values changing
   *
   * @covers ::inc
   * @covers ::dec
   * @covers ::__set
   * @covers ::valueSet
   * @covers ::valueDelta
   * @covers ::getDeltas
   * @covers ::blockDelta
   * @covers ::clear
   * @covers ::accept
   * @covers ::reject
   * @covers ::isChanged
   */
  public function testDelta() {
    // Checking base inc()
    $this->assertFalse($this->object->isChanged());
    $this->object->inc()->fromZero = 5;
    $this->assertTrue($this->object->isChanged());
    $this->assertFalse($this->object->isEmpty());
    $this->assertTrue(isset($this->object->fromZero));
    $this->assertTrue($this->object->__isset('fromZero'));
    $this->assertEquals(5, $this->object->fromZero);
    $this->assertAttributeEquals(['fromZero' => 5], 'values', $this->object);
    $this->assertAttributeEquals(['fromZero' => 0], '_startValues', $this->object);
    $this->assertAttributeEquals(['fromZero' => 5], '_deltas', $this->object);
    $this->assertAttributeEquals([], '_changes', $this->object);

    // Checking base dec()
    $this->object->dec()->fromZeroDec = 5;
    $this->assertFalse($this->object->isEmpty());
    $this->assertTrue(isset($this->object->fromZeroDec));
    $this->assertTrue($this->object->__isset('fromZeroDec'));
    $this->assertEquals(-5, $this->object->fromZeroDec);
    $this->assertAttributeEquals(['fromZero' => 5, 'fromZeroDec' => -5], 'values', $this->object);
    $this->assertAttributeEquals(['fromZero' => 0, 'fromZeroDec' => 0], '_startValues', $this->object);
    $this->assertAttributeEquals(['fromZero' => 5, 'fromZeroDec' => -5], '_deltas', $this->object);
    $this->assertAttributeEquals([], '_changes', $this->object);

    // Checking deltas extraction
    $this->assertAttributeEquals($this->object->getDeltas(), '_deltas', $this->object);

    // Checking accept()
    $this->object->changed = 7;
    $this->object->changed = 8;
    $this->assertAttributeEquals(['changed' => 8], '_changes', $this->object);
    $this->object->accept();
    $this->assertAttributeEquals(['fromZero' => 5, 'fromZeroDec' => -5, 'changed' => 8], 'values', $this->object);
    $this->assertAttributeEquals(['fromZero' => 5, 'fromZeroDec' => -5, 'changed' => 8], '_startValues', $this->object);
    $this->assertAttributeEquals([], '_deltas', $this->object);
    $this->assertAttributeEquals([], '_changes', $this->object);

    // Checking clear()
    $this->object->changed = 9;
    $this->object->inc()->fromZero = 5;
    $this->object->dec()->fromZeroDec = 5;
    $this->assertAttributeEquals(['fromZero' => 10, 'fromZeroDec' => -10, 'changed' => 9], 'values', $this->object);
    $this->assertAttributeEquals(['fromZero' => 5, 'fromZeroDec' => -5, 'changed' => 8], '_startValues', $this->object);
    $this->assertAttributeEquals(['fromZero' => 5, 'fromZeroDec' => -5], '_deltas', $this->object);
    $this->assertAttributeEquals(['changed' => 9], '_changes', $this->object);
    $this->object->clear();

    $this->assertFalse($this->object->isChanged());
    $this->object->changed = 3;
    // TODO - Это тоже должно работать!
//    $this->assertTrue($this->object->isChanged());
    $this->assertFalse($this->object->isChanged());
    $this->object->changed = 5;
    $this->assertTrue($this->object->isChanged());
    $this->object->clear();

    $this->test__construct();

    $this->object->changed = 3;
    $this->object->inc()->integer = 4;
    $this->assertTrue($this->object->isChanged());
    $this->assertAttributeEquals(['changed' => 3, 'integer' => 0], '_startValues', $this->object);
    $this->assertAttributeEquals(['integer' => 4], '_deltas', $this->object);
    $this->object->accept();
    $this->assertAttributeEquals(['changed' => 3, 'integer' => 4], '_startValues', $this->object);
    $this->object->changed = 7;
    $this->object->inc()->integer = 2;
    $this->assertEquals(7, $this->object->changed);
    $this->assertEquals(6, $this->object->integer);
    $this->object->reject();
    $this->assertEquals(3, $this->object->changed);
    $this->assertEquals(4, $this->object->integer);
    $this->object->clear();

    // Checking delta block after direct change
    $this->expectExceptionMessage('Common\AccessLogged::changed already changed - can not use DELTA');
    // First change is legit - filling startValue
    $this->object->changed = 7;
    // Second change makes value unchangeable by delta
    $this->object->changed = 8;
    // Raising exception
    $this->object->inc()->changed = 10;
  }

}
