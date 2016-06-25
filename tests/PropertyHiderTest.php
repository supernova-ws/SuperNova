<?php

/**
 * Class PropertyHiderTested
 *
 * @property int    test
 * @property int    testGetSet
 * @property array  testWithDiff
 *
 * @property int    testInteger
 * @property float  testFloat
 * @property string testString
 * @property array  testArray
 *
 * @property null   testNull
 */
class PropertyHiderTested extends PropertyHider {
  protected $_test = -2;
  protected $_testGetSet = -1;
  protected $_testWithDiff = array();

  protected $_testInteger = 0;
  protected $_testFloat = 0.0;
  protected $_testString = '';
  protected $_testArray = array();

  protected $_testNull = null;

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
  protected function gettestWithDiff() {
    return $this->_testWithDiff;
  }

  /**
   * @param array $value
   */
  protected function settestWithDiff($value) {
    $this->_testWithDiff = $value;
  }

  /**
   * @param array $value
   *
   * @return array
   */
  protected function adjtestWithDiff($diff) {
//    if(!isset($this->propertiesAdjusted['testWithDiff']) || !is_array($this->propertiesAdjusted['testWithDiff'])) {
//      $this->propertiesAdjusted['testWithDiff'] = array();
//    }

    HelperArray::merge($this->_testWithDiff, $diff, HelperArray::MERGE_PHP);

    return $this->_testWithDiff;
  }

  /**
   * @param array $value
   */
  protected function adjtestWithDiffDiff($diff) {
    if (!is_array($this->propertiesAdjusted['testWithDiff'])) {
      $this->propertiesAdjusted['testWithDiff'] = array();
    }

    HelperArray::merge($this->propertiesAdjusted['testWithDiff'], $diff, HelperArray::MERGE_PHP);

    return $this->propertiesAdjusted['testWithDiff'];
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
    'testWithDiff'            => array(),
    'noClassPropertyOrMethod' => array(),
    'testInteger'             => array(),
    'testFloat'               => array(),
    'testString'              => array(),
    'testArray'               => array(),
    'testNull'                => array(),
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
   * @covers ::_setUnsafe
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
   * @covers ::_setUnsafe
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
   * Testing adjusters
   *
   * @covers ::__set
   * @covers ::_setUnsafe
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
   * @covers ::_setUnsafe
   * @covers ::__get
   * @covers ::__adjust
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjustWithDiff() {
    // Test with calling getters/setters and DIFF adjuster on array
    $this->assertEquals(array(), $this->object->testWithDiff);
    // Testing setter
    $this->object->__set('testWithDiff', array('one' => 'ten'));
    $this->assertEquals(array('one' => 'ten'), $this->object->testWithDiff);
    $this->assertEquals(array('testWithDiff' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    // Testing adjuster
    $this->assertEquals(array('one' => 'ten', 'two' => 'eleven'), $this->object->__adjust('testWithDiff', array('two' => 'eleven')));
    $this->assertEquals(array('one' => 'ten', 'two' => 'eleven'), $this->object->testWithDiff);
    $this->assertEquals(array('testWithDiff' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('testWithDiff' => array('two' => 'eleven')), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }

  /**
   * Testing built-in type adjusters
   *
   * @covers ::__set
   * @covers ::_setUnsafe
   * @covers ::__get
   * @covers ::__adjust
   * @covers ::_adjustValue
   * @covers ::_adjustValueDiff
   * @covers ::_adjustValueInteger
   * @covers ::_adjustValueIntegerDiff
   * @covers ::propertyMethodResult
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjustValueInteger() {
    // Test with calling getters/setters and DIFF adjuster on array
    $this->assertEquals(0, $this->object->testInteger);
    $this->assertEquals(2, $this->object->__adjust('testInteger', 2));
    $this->assertEquals(array('testInteger' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('testInteger' => 2), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }

  /**
   * Testing built-in type adjusters
   *
   * @covers ::__set
   * @covers ::_setUnsafe
   * @covers ::__get
   * @covers ::__adjust
   * @covers ::_adjustValue
   * @covers ::_adjustValueDiff
   * @covers ::_adjustValueDouble
   * @covers ::_adjustValueDoubleDiff
   * @covers ::propertyMethodResult
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjustValueFloat() {
    // Test with calling getters/setters and DIFF adjuster on array
    $this->assertEquals(0.0, $this->object->testFloat);
    $this->assertEquals(3.1, $this->object->__adjust('testFloat', 3.1));
    $this->assertEquals(array('testFloat' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('testFloat' => 3.1), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }

  /**
   * Testing built-in type adjusters
   *
   * @covers ::__set
   * @covers ::_setUnsafe
   * @covers ::__get
   * @covers ::__adjust
   * @covers ::_adjustValue
   * @covers ::_adjustValueDiff
   * @covers ::_adjustValueString
   * @covers ::_adjustValueStringDiff
   * @covers ::propertyMethodResult
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjustValueString() {
    // Test with calling getters/setters and DIFF adjuster on array
    $this->assertEquals('', $this->object->testString);
    $this->assertEquals('foo', $this->object->__adjust('testString', 'foo'));
    $this->assertEquals(array('testString' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('testString' => 'foo'), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));

    $this->assertEquals('foobar', $this->object->__adjust('testString', 'bar'));
    $this->assertEquals(array('testString' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('testString' => 'foobar'), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }

  /**
   * Testing built-in type adjusters
   *
   * @covers ::__set
   * @covers ::_setUnsafe
   * @covers ::__get
   * @covers ::__adjust
   * @covers ::_adjustValue
   * @covers ::_adjustValueDiff
   * @covers ::_adjustValueArray
   * @covers ::_adjustValueArrayDiff
   * @covers ::propertyMethodResult
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjustValueArray() {
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

//  /**
//   * Testing built-in type adjusters
//   *
//   * @covers ::__set
//   * @covers ::__get
//   * @covers ::__adjust
//   * @covers ::_adjustValue
//   * @covers ::_adjustValueInteger
//   * @covers ::_adjustValueDouble
//   * @covers ::_adjustValueString
//   * @covers ::checkOverwriteAdjusted
//   */
//  public function test__adjustType() {
//    // Test with calling getters/setters and DIFF adjuster on array
////    $this->assertEquals(0, $this->object->testInteger);
////    $this->assertEquals(2, $this->object->__adjust('testInteger', 2));
////    $this->assertEquals(array('testInteger' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
////    $this->assertEquals(array('testInteger' => 2), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
//
////    $this->assertEquals(array(), $this->object->testWithDiff);
//    // Testing setter
////    $this->object->__set('testWithDiff', array('one' => 'ten'));
////    $this->assertEquals(array('one' => 'ten'), $this->object->testWithDiff);
////    $this->assertEquals(array('testWithDiff' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
////    // Testing adjuster
////    $this->assertEquals(array('one' => 'ten', 'two' => 'eleven'), $this->object->__adjust('testWithDiff', array('two' => 'eleven')));
////    $this->assertEquals(array('one' => 'ten', 'two' => 'eleven'), $this->object->testWithDiff);
////    $this->assertEquals(array('testWithDiff' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
////    $this->assertEquals(array('testWithDiff' => array('two' => 'eleven')), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
//  }


  /**
   * Test exception when trying to adjust unsupported property type
   *
   * @covers ::propertyMethodResult
   * @expectedException ExceptionTypeUnsupported
   */
  public function testExceptionTypeUnsupported() {
    $this->object->__adjust('testNull', 10);
//    $this->assertEquals(-2, $this->object->test);
//    $this->assertEquals(8, $this->object->__adjust('test', 10));
//    $this->object->test = 20;
  }


  /**
   * Test exception when trying to set in already adjusted property
   *
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
