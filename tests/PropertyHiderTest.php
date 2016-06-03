<?php

class PropertyHiderTested extends PropertyHider {
  protected $_test = -2;

  protected $_testGetSet = -1;

  protected function getTest2() {
    return $this->_testGetSet + 1;
  }

  protected function setTest2($value) {
    $this->_testGetSet = $value + 2;
  }

  /**
   * @param int $value
   *
   * @return int
   */
  protected function adjTest2($value) {
    return $this->_testGetSet + $value + 4;
  }
}

/**
 * Class PropertyHiderTest
 *
 * @coversDefaultClass PropertyHider
 */
class PropertyHiderTest extends PHPUnit_Framework_TestCase {
  /**
   * @var PropertyHiderTested $object
   */
  protected $object;
  protected $testProperties = array(
    'test'                    => array(),
    'test2'                   => array(),
    'noClassPropertyOrMethod' => array(),
  );

  public function setUp() {
    parent::setUp();
    $this->object = new PropertyHiderTested();
    PropertyHiderTested::setProperties($this->testProperties);
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::getProperties
   * @covers ::setProperties
   * @covers ::__construct
   */
  public function testSetGetProperties() {
    $this->assertEquals('PropertyHiderTested', get_class($test = new PropertyHiderTested()));

    $this->assertEquals(
      $this->testProperties,
      PropertyHiderTested::getProperties()
    );

    PropertyHiderTested::setProperties(array('asd' => 'qwe'));
    $this->assertEquals(array('asd' => 'qwe'), PropertyHiderTested::getProperties());
  }

  /**
   * @covers ::__get
   * @covers ::checkPropertyExists
   * @covers ::__set
   * @covers ::checkOverwriteAdjusted
   * @covers ::___set
   */
  public function testCheckPropertyExists() {
    $this->assertEquals(-2, $this->object->test);

    // Simple set
    $this->object->test = 5;
    $this->assertEquals(5, $this->object->test);
    $this->assertEquals(array('test' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));

    // Test with calling getters/setters
    // Getter = +1 to real value
    $this->assertEquals(0, $this->object->test2);
    // Setter = +2 to setting value
    $this->object->test2 = 0;
    // Getter + Setter = 0 + 1 + 2
    $this->assertEquals(3, $this->object->test2);

    $this->assertEquals(array('test' => true, 'test2' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
  }

  public function dataSetGetException() {
    return array(
      array('notInPropertyArray'),
      array('noClassPropertyOrMethod'),
    );
  }

  /**
   * @param mixed $badValue
   *
   * @dataProvider dataSetGetException
   *
   * @covers ::__set
   * @covers ::checkPropertyExists
   * @covers ::checkOverwriteAdjusted
   * @covers ::___set
   * @expectedException ExceptionPropertyNotExists
   */
  public function testSetException($badValue) {
    $this->object->$badValue = 0;
  }

  /**
   * @param mixed $badValue
   *
   * @dataProvider dataSetGetException
   *
   * @covers ::__get
   * @covers ::checkPropertyExists
   * @expectedException ExceptionPropertyNotExists
   */
  public function testGetException($badValue) {
    $test = $this->object->$badValue;
  }

  /**
   * @covers ::___set
   * @covers ::__set
   * @covers ::__get
   * @covers ::__adjust
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjust() {
    // Simple adjust
    $this->assertEquals(-2, $this->object->test);
    $this->assertEquals(8, $this->object->__adjust('test', 10));
    $this->assertEquals(array('test' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('test' => 10), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));

    // Test with calling getters/setters
    // Getter = +1 to real value
    $this->assertEquals(0, $this->object->test2);
    // $Diff (8) + Adjuster (+4) + Setter(+2) + Getter (+1) + real value (-1) = 14
    $this->assertEquals(14, $this->object->__adjust('test2', 8));
    $this->assertEquals(array('test' => true, 'test2' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('test' => 10, 'test2' => 8), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }

  /**
   * @covers ::___set
   * @covers ::__set
   * @covers ::checkOverwriteAdjusted
   * @expectedException PropertyAccessException
   */
  public function testPropertyAccessException() {
    $this->assertEquals(-2, $this->object->test);
    $this->assertEquals(8, $this->object->__adjust('test', 10));
    $this->object->test = 20;
  }

}
