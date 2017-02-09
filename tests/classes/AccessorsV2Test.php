<?php

/**
 * Created by Gorlum 10.08.2016 21:29
 */

use Common\AccessorsV2;

/**
 * Class AccessorsV2Test
 * @coversDefaultClass \Common\AccessorsV2
 */
class AccessorsV2Test extends PHPUnit_Framework_TestCase {

  /**
   * @var AccessorsV2 $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();

    $this->object = new AccessorsV2();
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
   * @covers ::__call
   * @covers ::isEmpty
   */
  public function test__get() {
    $this->assertTrue($this->object->isEmpty());
    $this->assertFalse(isset($this->object->test));
    $this->assertNull($this->object->test);

    $callable = function () {
      static $q = '';

      return 'value' . $q++;
    };
    $this->object->test = $callable;
    $this->assertFalse($this->object->isEmpty());
    $this->assertTrue(isset($this->object->test));
    $this->assertAttributeEquals(array('test' => $callable), 'accessors', $this->object);
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array(), 'executed', $this->object);

    $this->assertEquals('value', $this->object->test());
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array('test' => 'value'), 'executed', $this->object);

    // Checking that executed result is NOT shared - i.e. accessor function called every time
    $this->assertEquals('value1', $this->object->test());
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array('test' => 'value1'), 'executed', $this->object);

    unset($this->object->test);
    $this->assertTrue($this->object->isEmpty());
    $this->assertFalse(isset($this->object->test));
    $this->assertNull($this->object->test);
    $this->assertAttributeEquals(array(), 'accessors', $this->object);
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array(), 'executed', $this->object);

  }

  /**
   * Testing shared functions behavior and clear() function
   *
   * @covers ::__call
   * @covers ::share
   * @covers ::clear
   */
  public function testShare() {
    $callable = function () {
      static $q = '';

      return 'value' . $q++;
    };

    $this->object->share('test', $callable);
    $this->assertFalse($this->object->isEmpty());
    $this->assertTrue(isset($this->object->test));
    $this->assertAttributeEquals(array('test' => $callable), 'accessors', $this->object);
    $this->assertAttributeEquals(array('test' => true), 'shared', $this->object);
    $this->assertAttributeEquals(array(), 'executed', $this->object);

    $this->assertEquals('value', $this->object->test());
    $this->assertAttributeEquals(array('test' => true), 'shared', $this->object);
    $this->assertAttributeEquals(array('test' => 'value'), 'executed', $this->object);

    // Sequental call should NOT change stored value of shared function
    $this->assertEquals('value', $this->object->test());
    $this->assertAttributeEquals(array('test' => true), 'shared', $this->object);
    $this->assertAttributeEquals(array('test' => 'value'), 'executed', $this->object);

    $this->object->clear();
    $this->assertAttributeEquals(array(), 'accessors', $this->object);
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array(), 'executed', $this->object);
  }

  // TODO - method test
  // TODO - Invoker test

  /**
   * @covers ::__set
   */
  public function testUseFunctionName() {
    $this->object->test = 'mt_rand';
    $this->assertFalse($this->object->isEmpty());
    $this->assertTrue($this->object->test instanceof \Common\Invoker);
    $this->assertAttributeNotEmpty('accessors', $this->object);
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array(), 'executed', $this->object);

    $this->assertTrue(is_integer($stored = $this->object->test()));
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array('test' => $stored), 'executed', $this->object);
  }


  public function helperMethod() {
    return 'value';
  }

  /**
   * How setting hook on class method performs
   *
   * @covers ::__set
   */
  public function testUseClass() {
    $this->object->test = array($this, 'helperMethod');
    $this->assertFalse($this->object->isEmpty());
    $this->assertTrue($this->object->test instanceof \Common\Invoker);
    $this->assertAttributeNotEmpty('accessors', $this->object);
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array(), 'executed', $this->object);

    $this->assertEquals('value', $this->object->test());
    $this->assertAttributeEquals(array(), 'shared', $this->object);
    $this->assertAttributeEquals(array('test' => 'value'), 'executed', $this->object);
  }

  /**
   * @covers ::__set
   */
  public function testExceptionSettingNotExistingCallable() {
    $this->setExpectedException(
      '\Exception',
      'Error assigning callable in Common\AccessorsV2::set()! Callable labeled [test] is not a callable or not accessible in this scope'
    );
    $this->object->test = 12345;
  }

  protected function hiddenMethod() {
    return 'protected';
  }

  /**
   * @covers ::__set
   */
  public function testExceptionSettingInaccessibleMethod() {
    $this->setExpectedException(
      '\Exception',
      'Error assigning callable in Common\AccessorsV2::set()! Callable labeled [test] is not a callable or not accessible in this scope'
    );
    $this->object->test = array($this, 'hiddenMethod');
  }

  /**
   * @covers ::__call
   */
  public function testExceptionCallNotExistingFunction() {
    $this->setExpectedException('\Exception', 'No [test] accessor found on Common\AccessorsV2::Common\AccessorsV2::__call');
    $this->object->test();
  }

}
