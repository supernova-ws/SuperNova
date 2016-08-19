<?php

/**
 * Created by Gorlum 12.08.2016 15:24
 */
use Entity\EntityContainer;

/**
 * Class EntityContainerTest
 * @coversDefaultClass Entity\EntityContainer
 */

class EntityContainerTest extends PHPUnit_Framework_TestCase {

  /**
   * @var EntityContainer $object
   */
  protected $object;

  /**
   * @var \Common\GlobalContainer
   */
  protected $gc;

  public function setUp() {
    parent::setUp();
//    $this->gc = new \Common\GlobalContainer();
//    $this->object = new Entity\EntityContainer($this->gc);
  }

  public function tearDown() {
    unset($this->object);
    unset($this->gc);
    parent::tearDown();
  }

  /**
   * covers ::setProperties
   */
  public function testSetProperties() {
//    $this->object->setProperties(array('p1' => array()));
//    $this->assertAttributeEquals(array('p1' => array()), 'properties', $this->object);
  }

  /**
   * covers ::clearProperties
   * @covers ::__unset
   */
  public function testClearProperties() {
//    $this->assertAttributeEquals(array(), 'values', $this->object);
//
//    $this->object->setProperties(array('p1' => array(), 'p3' => array()));
//    $this->object->assignAccessor('p3', P_CONTAINER_SET, function(Common\ContainerAccessors $that, $value) {$that->setDirect('p4', $value);});
//    $this->object->assignAccessor('p3', P_CONTAINER_UNSET, function(Common\ContainerAccessors $that) {unset($that->p4);});
//
//    $this->object->p1 = 'v1';
//    $this->object->p2 = 'v2';
//    $this->object->p3 = 'v4';
//    // Internal consistency test
//    $this->assertAttributeEquals(array('p1' => 'v1', 'p2' => 'v2', 'p4' => 'v4'), 'values', $this->object);
//
//    $this->object->clearProperties();
//    // Internal consistency test
//    $this->assertAttributeEquals(array('p2' => 'v2'), 'values', $this->object);
  }

}
