<?php

/**
 * Created by Gorlum 10.08.2016 21:29
 */

use Common\AccessMagic;

/**
 * Class AccessMagicTest
 * @coversDefaultClass \Common\AccessMagic
 */
class AccessMagicTest extends PHPUnit_Framework_TestCase {

  /**
   * @var AccessMagic $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();

    $this->object = new AccessMagic();
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::__get
   * @covers ::__isset
   * @covers ::__set
   * @covers ::__unset
   * @covers ::isEmpty
   * @covers ::asArray
   * @covers ::clear
   */
  public function test__get() {
    $this->assertTrue($this->object->isEmpty());
    $this->assertFalse(isset($this->object->test));
    $this->assertNull($this->object->test);

    $this->object->test = 'value';
    $this->assertFalse($this->object->isEmpty());
    $this->assertAttributeEquals(array('test' => 'value'), 'values', $this->object);
    $this->assertTrue(isset($this->object->test));
    $this->assertEquals('value', $this->object->test);

    $this->assertAttributeEquals($this->object->asArray(), 'values', $this->object);

    unset($this->object->test);
    $this->assertTrue($this->object->isEmpty());
    $this->assertFalse(isset($this->object->test));
    $this->assertNull($this->object->test);
    $this->assertAttributeEquals(array(), 'values', $this->object);

    $this->object->test = 'value';
    $this->object->clear();
    $this->assertTrue($this->object->isEmpty());
  }

}
