<?php

/**
 * Created by Gorlum 11.08.2016 11:13
 */
use Common\ContainerAccessors;

/**
 * Class ContainerAccessorsTest
 * @coversDefaultClass Common\ContainerAccessors
 */
class ContainerAccessorsTest extends PHPUnit_Framework_TestCase {

  /**
   * @var ContainerAccessors $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();
    $this->object = new ContainerAccessors();
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::setDirect
   * @covers ::getDirect
   * @covers ::unsetDirect
   * @covers ::setAccessors
   */
  public function testSetDirect() {
    // Setting really returned field
    $this->object->setDirect('p1', 'v1');
    $this->assertEquals('v1', $this->object->p1);
    $this->assertEquals('v1', $this->object->getDirect('p1'));
    $this->object->unsetDirect('p1');
    $this->assertEquals(null, $this->object->p1);
  }

//  /**
//   * @covers ::setAccessors
//   */
//  public function testSetAccessors() {
//    $this->object->setAccessors(array('test'));
//
//    $this->assertAttributeEquals(array('test'), 'accessors', $this->object);
//  }

//  /**
//   * @covers ::assignAccessor
//   */
//  public function testAssignAccessor() {
//    $this->object->setAccessor('test', P_CONTAINER_GET, null);
//    $this->assertAttributeEquals(array(), 'accessors', $this->object);
//
//    $lambda = function(){};
//    $this->object->setAccessor('test', P_CONTAINER_GET, $lambda);
//    $this->assertAttributeEquals(array('test' => array(P_CONTAINER_GET => $lambda)), 'accessors', $this->object);
//
//    $this->setExpectedException('Exception', 'Error assigning callable in Common\ContainerAccessors! Callable typed [' . P_CONTAINER_GET . '] is not a callable or not accessible in the scope');
//    $this->object->setAccessor('test', P_CONTAINER_GET, 1);
//  }

  /**
   * @covers ::__set
   * @covers ::__get
   * @covers ::__unset
   * @covers ::performMagic
   */
  public function test__set() {
    $accessors = new \Common\Accessors();
    $this->object->setAccessors($accessors);

    // Basic setter/getter
//    $this->object->setProperties(array('p1' => array()));
    // Setter test
    $this->object->p1 = 'v1';
    // Getter test
    $this->assertEquals('v1', $this->object->p1);
    // Internal consistency test
    $this->assertAttributeEquals(array('p1' => 'v1'), 'values', $this->object);

    // Setting getter. It will return value of p3 when accessing p1
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
    // Installing setter
    $accessors->setAccessor('p1', P_CONTAINER_SET, function(ContainerAccessors $that, $value) {$that->setDirect('p2', $value . '3');});
//    $this->object->setAccessor('p1', P_CONTAINER_SET, function(Common\ContainerAccessors $that, $value) {$that->setDirect('p2', $value . '3');});
    $this->object->p1 = 'v2';
    // Setting value
    $this->assertEquals('v23', $this->object->p2);
    // Internal consistency test
    $this->assertAttributeEquals(array('p1' => 'v1', 'p3' => 'v3', 'p2' => 'v23'), 'values', $this->object);
    // Installing getter for p2. It will return modified value of p3
    $accessors->setAccessor('p2', P_CONTAINER_GET, function($c) {return $c->p3 . '4';});
//    $this->object->setAccessor('p2', P_CONTAINER_GET, function($c) {return $c->p3 . '4';});
    $this->assertEquals('v34', $this->object->p2);

    // Testing trivial unsetter
    unset($this->object->p3);
    $this->assertEquals(null, $this->object->p3);
    // p3 should be unset
    $this->assertAttributeEquals(array('p1' => 'v1', 'p2' => 'v23'), 'values', $this->object);

    // Testing lambda unsetter
//    $this->object->setAccessor('p1', P_CONTAINER_UNSET, function(Common\ContainerAccessors $that) {$that->unsetDirect('p2');});
    $accessors->setAccessor('p1', P_CONTAINER_UNSET, function(ContainerAccessors $that) {$that->unsetDirect('p2');});
    unset($this->object->p1);
    $this->assertEquals(null, $this->object->p1);
    // p2 should be unset via unsetter
    $this->assertAttributeEquals(array('p1' => 'v1',), 'values', $this->object);
  }


  /**
   * @covers ::__isset
   */
  public function test__isset() {
    $this->assertAttributeEquals(array(), 'values', $this->object);

    $accessors = new \Common\Accessors();
    $this->object->setAccessors($accessors);

    $accessors->setAccessor('p2', P_CONTAINER_GET, function($c) {return $c->p3;});
//    $this->object->setAccessor('p2', P_CONTAINER_GET, function($c) {return $c->p3;});
    $this->object->p4 = function ($c) {return $c->p5;};
//    $this->object->setAccessor('p6', P_CONTAINER_GET, function($c) {return $c->p7;});
    $accessors->setAccessor('p6', P_CONTAINER_GET, function($c) {return $c->p7;});
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
