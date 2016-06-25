<?php

/**
 * Class PropertyHiderTested
 *
 * @property int test
 * @property int testGetSet
 * @property array testArray
 */
class PropertyHiderTested extends PropertyHider {
  protected $_test = -2;
  protected $_testGetSet = -1;
  protected $_testArray = array();

  protected function getTestGetSet() {
    return $this->_testGetSet + 1;
  }

  protected function setTestGetSet($value) {
    $this->_testGetSet = $value + 2;
  }

  /**
   * @param int $value
   *
   * @return int
   */
  protected function adjTestGetSet($value) {
    return $this->_testGetSet + $value + 4;
  }


  /**
   * @return array
   */
  protected function getTestArray() {
    return $this->_testArray;
  }

  /**
   * @param array $value
   */
  protected function setTestArray($value) {
    $this->_testArray = $value;
  }

  /**
   * @param array $value
   *
   * @return array
   */
  protected function adjTestArray($diff) {
//    if(!isset($this->propertiesAdjusted['testArray']) || !is_array($this->propertiesAdjusted['testArray'])) {
//      $this->propertiesAdjusted['testArray'] = array();
//    }

    HelperArray::merge($this->_testArray, $diff, HelperArray::MERGE_PHP);
    return $this->_testArray;
  }

  /**
   * @param array $value
   */
  protected function adjTestArrayDiff($diff) {
    if(!is_array($this->propertiesAdjusted['testArray'])) {
      $this->propertiesAdjusted['testArray'] = array();
    }

    HelperArray::merge($this->propertiesAdjusted['testArray'], $diff, HelperArray::MERGE_PHP);
    return $this->propertiesAdjusted['testArray'];
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
    'testGetSet'              => array(),
    'testArray'               => array(),
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
   */
  public function testCheckPropertyExists() {
    $this->assertEquals(-2, $this->object->test);

    // Simple set
    $this->object->test = 5;
    $this->assertEquals(5, $this->object->test);
    $this->assertEquals(array('test' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));

    // Test with calling getters/setters
    // Getter = +1 to real value
    $this->assertEquals(0, $this->object->testGetSet);
    // Setter = +2 to setting value
    $this->object->testGetSet = 0;
    // Getter + Setter = 0 + 1 + 2
    $this->assertEquals(3, $this->object->testGetSet);

    $this->assertEquals(array('test' => true, 'testGetSet' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
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
    $this->assertEquals(0, $this->object->testGetSet);
    // $Diff (8) + Adjuster (+4) + Setter(+2) + Getter (+1) + real value (-1) = 14
    $this->assertEquals(14, $this->object->__adjust('testGetSet', 8));
    $this->assertEquals(array('test' => true, 'testGetSet' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('test' => 10, 'testGetSet' => 8), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }

  /**
   * @covers ::__set
   * @covers ::__get
   * @covers ::__adjust
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjustArray() {
    // Test with calling getters/setters and DIFF adjuster on array
    $this->assertEquals(array(), $this->object->testArray);
    // Testing setter
    $this->object->__set('testArray', array('one' => 'ten'));
    $this->assertEquals(array('one' => 'ten'), $this->object->testArray);
    $this->assertEquals(array('testArray' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    // Testing adjuster
    $this->assertEquals(array('one' => 'ten', 'two' => 'eleven'), $this->object->__adjust('testArray', array('two' => 'eleven')));
    $this->assertEquals(array('one' => 'ten', 'two' => 'eleven'), $this->object->testArray);
    $this->assertEquals(array('testArray' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('testArray' => array('two' => 'eleven')), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }

  /**
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
