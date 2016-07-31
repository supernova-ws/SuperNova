<?php

/**
 * Class ValidatorsTest
 *
 * @coversDefaultClass Validators
 */
class ValidatorsTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  /**
   * @covers ::isNotEmpty
   * @covers ::isNotEmptyByRef
   */
  public function testIsNotEmpty() {
    $tested = new Validators();

    // Not empty
    $this->assertTrue(invokeMethod($tested, 'isNotEmpty', array('1')));

    // Empty
    $this->assertFalse(invokeMethod($tested, 'isNotEmpty', array('0')));
  }

}
