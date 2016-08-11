<?php

/**
 * Created by Gorlum 11.08.2016 11:13
 */

/**
 * Class V2PropertyContainerTest
 * @coversDefaultClass V2PropertyContainer
 */
class V2PropertyContainerTest extends PHPUnit_Framework_TestCase {

  /**
   * @var V2PropertyContainer $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();
    $this->object = new V2PropertyContainer();
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::setDirect
   */
  public function testSetDirect() {
    // Setting really returned field
    $this->object->setDirect('p1', 'v1');
    $this->assertEquals('v1', $this->object->p1);
  }

  /**
   * @covers ::setProperties
   */
  public function testSetProperties() {
    $this->object->setProperties(array('p1' => array()));
    $this->assertAttributeEquals(array('p1' => array()), 'properties', $this->object);
  }

  /**
   * @covers ::assignAccessor
   */
  public function testAssignAccessor() {
    $this->object->assignAccessor('test', P_CONTAINER_GETTER, null);
    $this->assertAttributeEquals(array(), 'accessors', $this->object);

    $lambda = function(){};
    $this->object->assignAccessor('test', P_CONTAINER_GETTER, $lambda);
    $this->assertAttributeEquals(array('test' => array(P_CONTAINER_GETTER => $lambda)), 'accessors', $this->object);

    $this->setExpectedException('Exception', 'Error assigning callable in V2PropertyContainer! Callable typed [' . P_CONTAINER_GETTER . '] is not a callable or not accessible in the scope');
    $this->object->assignAccessor('test', P_CONTAINER_GETTER, 1);
  }

  /**
   * @covers ::__set
   * @covers ::__get
   */
  public function test__set() {
    // Basic setter/getter
    $this->object->setProperties(array('p1' => array()));
    // Setter test
    $this->object->p1 = 'v1';
    // Getter test
    $this->assertEquals('v1', $this->object->p1);
    // Internal consistency test
    $this->assertAttributeEquals(array('p1' => 'v1'), 'values', $this->object);

    // Setting pimple-like getter. It will return value of p3 when accessing p1
    $lambda = function ($c) {return $c->p3;};
    // Setter test to work with callable
    $this->object->p1 = $lambda;
    // Setting really returned field
    $this->object->p3 = 'v3';
    // Testing previously installed pimple-like getter
    $this->assertEquals('v3', $this->object->p1);
    // Internal consistency test
    $this->assertAttributeEquals(array('p1' => 'v1', 'p3' => 'v3'), 'values', $this->object);


    // Testing lambda setter/getter
    // Needs until PHP 5.4
    $that = $this->object;
    // Installing setter
    $this->object->assignAccessor('p1', P_CONTAINER_SETTER, function(V2PropertyContainer $that, $value) {$that->setDirect('p2', $value . '3');});
    $this->object->p1 = 'v2';
    // Setting value
    $this->assertEquals('v23', $this->object->p2);
    // Internal consistency test
    $this->assertAttributeEquals(array('p1' => 'v1', 'p3' => 'v3', 'p2' => 'v23'), 'values', $this->object);
    // Installing getter for p2. It will return modified value of p3
    $this->object->assignAccessor('p2', P_CONTAINER_GETTER, function($c) {return $c->p3 . '4';});
    $this->assertEquals('v34', $this->object->p2);
  }

  /**
   * @covers ::clearProperties
   * @covers ::__unset
   */
  public function testClearProperties() {
    $this->assertAttributeEquals(array(), 'values', $this->object);

    $this->object->setProperties(array('p1' => array(), 'p3' => array()));
    $this->object->assignAccessor('p3', P_CONTAINER_SETTER, function(V2PropertyContainer $that, $value) {$that->setDirect('p4', $value);});
    $this->object->assignAccessor('p3', P_CONTAINER_UNSETTER, function(V2PropertyContainer $that) {unset($that->p4);});

    $this->object->p1 = 'v1';
    $this->object->p2 = 'v2';
    $this->object->p3 = 'v4';
    // Internal consistency test
    $this->assertAttributeEquals(array('p1' => 'v1', 'p2' => 'v2', 'p4' => 'v4'), 'values', $this->object);

    $this->object->clearProperties();
    // Internal consistency test
    $this->assertAttributeEquals(array('p2' => 'v2'), 'values', $this->object);
  }

  /**
   * @covers ::__isset
   */
  public function test__isset() {
    $this->assertAttributeEquals(array(), 'values', $this->object);

    $this->object->setProperties(array('p1' => array(), 'p2' => array(), 'p4' => array()));
    $that = $this->object;
    $this->object->assignAccessor('p2', P_CONTAINER_GETTER, function($c) {return $c->p3;});
    $this->object->p4 = function ($c) {return $c->p5;};
    $this->object->assignAccessor('p6', P_CONTAINER_GETTER, function($c) {return $c->p7;});
    $this->object->p8 = function ($c) {return $c->p9;};

    // Setting really returned field
    $this->object->p1 = 'v1';
    $this->object->p3 = 'v23';
    $this->object->p5 = 'v45';
    $this->assertEquals('v1', $this->object->p1);
    $this->assertEquals('v23', $this->object->p2);
    $this->assertEquals('v45', $this->object->p4);

    $this->assertTrue(isset($this->object->p1));
    $this->assertTrue(isset($this->object->p2));
    $this->assertTrue(isset($this->object->p4));

    $this->assertFalse(isset($this->object->p6));
    $this->assertFalse(isset($this->object->p8));
  }


}
