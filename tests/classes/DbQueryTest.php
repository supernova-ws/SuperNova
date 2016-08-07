<?php
/**
 * Created by Gorlum 07.08.2016 18:45
 */

use \DBAL\DbQuery;

class DbEscape {
  public function db_escape($value) {
    // Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.
    return str_replace(
      array("\\", "\0", "\n", "\r", "'", "\"", "\z",),
      array('\\\\', '\0', '\n', '\r', '\\\'', '\"', '\z',),
      $value
    );
  }
}

/**
 * Class DbQueryTest
 * @package classes
 * @coversDefaultClass \DBAL\DbQuery
 */
class DbQueryTest extends PHPUnit_Framework_TestCase {

  /**
   * @var DbQuery $object
   */
  protected $object;

  protected $db;

  public function setUp() {
    parent::setUp();

    $this->db = new DbEscape();
    $this->object = new DbQuery($this->db);
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::__construct
   */
  public function test__construct() {
    $this->assertAttributeEquals($this->db, 'db', $this->object);
  }

  /**
   * @covers ::build
   */
  public function testBuild() {
    $this->assertEquals('DBAL\DbQuery', get_class($query = DbQuery::build($this->db)));
    $this->assertAttributeEquals($this->db, 'db', $query);
  }

  /**
   * @covers ::escape
   */
  public function testEscape() {
    // TODO - create real DB connection here
    $this->assertEquals('\0\n\r \\\\z \\\\ \\\' \"', $result = invokeMethod($this->object, 'escape', array("\0\n\r \z \\ ' \"")));
    $this->assertEquals(19, strlen($result));
  }

  /**
   * @covers ::escapeEmulator
   */
  public function testEscapeEmulator() {
    $this->assertEquals('\0\n\r \\\\z \\\\ \\\' \"', $result = invokeMethod($this->object, 'escapeEmulator', array("\0\n\r \z \\ ' \"")));
    $this->assertEquals(19, strlen($result));
  }

  /**
   * @covers ::stringValue
   */
  public function testStringValue() {
    $this->assertEquals('\'The\"test\\\\of\\\'escape\'', $result = invokeMethod($this->object, 'stringValue', array('The"test\of\'escape')));
  }

  /**
   * @covers ::quote
   */
  public function testQuote() {
    $this->assertEquals('`The\"test\\\\of\\\'escape`', $result = invokeMethod($this->object, 'quote', array('The"test\of\'escape')));
  }

  /**
   * @covers ::quoteTable
   */
  public function testQuoteTable() {
    $this->assertEquals('`{{The\"test\\\\of\\\'escape}}`', $result = invokeMethod($this->object, 'quoteTable', array('The"test\of\'escape')));
  }

  public function dataCastAsDbValue() {
    return array(
      array(10, TYPE_INTEGER, 10, TYPE_INTEGER),
      array(PHP_INT_MAX, TYPE_INTEGER, PHP_INT_MAX, TYPE_INTEGER),

      array(11.0, TYPE_DOUBLE, 11.0, TYPE_DOUBLE),
      array(PHP_INT_MAX + 1, TYPE_DOUBLE, PHP_INT_MAX + 1, TYPE_DOUBLE),

      array(0, TYPE_INTEGER, false, TYPE_BOOLEAN),
      array(1, TYPE_INTEGER, true, TYPE_BOOLEAN),

      array('NULL', TYPE_STRING, null, TYPE_NULL),

      array('\'\'', TYPE_STRING, '', TYPE_EMPTY),

      array('\'\'', TYPE_STRING, '', TYPE_STRING),
      array('\'\0\n\r \\\\z \\\\ \\\' \"\'', TYPE_STRING, "\0\n\r \z \\ ' \"", TYPE_STRING),
      array('\'The\"test\\\\of\\\'escape\'', TYPE_STRING, 'The"test\of\'escape', TYPE_STRING),

      array('\'a:0:{}\'', TYPE_STRING, array(), TYPE_ARRAY),
      array('\'a:1:{s:4:\"k\\\'ey\";s:9:\"st\\\'ri\"n\\\\g\";}\'', TYPE_STRING, array('k\'ey' => 'st\'ri"n\\g'), TYPE_ARRAY),
    );
  }

  /**
   * @covers ::castAsDbValue
   * @dataProvider dataCastAsDbValue
   */
  public function testCastAsDbValue($expected, $type, $value, $originalType) {
//    $this->assertEquals('`{{The\"test\\\\of\\\'escape}}`', $result = invokeMethod($this->object, 'castAsDbValue', array('The"test\of\'escape')));
    if ($originalType != TYPE_NULL && $originalType != TYPE_EMPTY) {
      $this->assertInternalType($originalType, $value);
    }
    $this->assertEquals($expected, invokeMethod($this->object, 'castAsDbValue', array($value)));
    $this->assertInternalType($type, invokeMethod($this->object, 'castAsDbValue', array($value)));
  }

  /**
   * @covers ::setTable
   * @covers ::setOneRow
   * @covers ::setValues
   * @covers ::setValuesDanger
   * @covers ::setAdjustDanger
   * @covers ::setFields
   * @covers ::setWhereArray
   * @covers ::setWhereArrayDanger
   */
  public function testSetters() {
    $result = $this->object
      ->setTable('table')
      ->setOneRow(DB_RECORD_ONE)
      ->setValues(array('f1' => 'v1'))
      ->setValuesDanger(array('f2 <= v1'))
      ->setAdjustDanger(array('f3' => '3'))
      ->setFields(array('f4' => 'v4'))
      ->setWhereArray(array('f5' => '5'))
      ->setWhereArrayDanger(array('f6' => '6'));

    // Fluid interface is working
    $this->assertEquals($this->object, $result);

    $this->assertAttributeEquals('table', 'table', $this->object);
    $this->assertAttributeEquals(DB_RECORD_ONE, 'isOneRow', $this->object);
    $this->assertAttributeEquals(array('f1' => 'v1'), 'values', $this->object);
    $this->assertAttributeEquals(array('f2 <= v1'), 'valuesDanger', $this->object);
    $this->assertAttributeEquals(array('f3' => '3'), 'adjustDanger', $this->object);
    $this->assertAttributeEquals(array('f4' => 'v4'), 'fields', $this->object);
    $this->assertAttributeEquals(array('f5' => '5'), 'where', $this->object);
    $this->assertAttributeEquals(array('f6' => '6'), 'whereDanger', $this->object);
  }



  public function dataOnlyDanger() {
    return array(
      array(array(), 1),
      array(array(), array()),

      array(array('used', 54 => 'used54'), array('used', 'q' => 'unused', 54 => 'used54')),
    );
  }

  /**
   * @covers ::onlyDanger
   * @dataProvider dataOnlyDanger
   */
  public function testOnlyDanger($expected, $value) {
    $this->assertEquals($expected, invokeMethod($this->object, 'onlyDanger', array($value)));
  }


  public function dataFieldEqValue() {
    return array(
      array(array(), 1),
      array(array(), array()),

      array(array('q' => "`q` = 'unused'", 'int' => "`int` = 52"), array('used', 'q' => 'unused', 54 => 'used54', 'int' => 52)),
    );
  }

  /**
   * @covers ::fieldEqValue
   * @dataProvider dataFieldEqValue
   */
  public function testFieldEqValue($expected, $value) {
    $this->assertEquals($expected, invokeMethod($this->object, 'fieldEqValue', array($value)));
  }



  public function dataSafeFields() {
    return array(
      array(array(), 1),
      array(array(), array()),

      array(array('q' => "`unused`", 'int' => "`52`", 0 => '`used`', 54 => '`used54`'), array('used', 'q' => 'unused', 54 => 'used54', 'int' => 52)),
    );
  }

  /**
   * @covers ::safeFields
   * @dataProvider dataSafeFields
   */
  public function testSafeFields($expected, $value) {
    $this->assertEquals($expected, invokeMethod($this->object, 'safeFields', array($value)));
  }



  public function dataSafeFieldsAdjust() {
    return array(
      array(array(), 1),
      array(array(), array()),

      array(
//        array('q' => "`q` = `q` + ('unused')", 'int' => "`int` = `int` + (52)", 0 => 'used', 54 => 'used54', 'w' => '`w` = `w` + (-56.1)',),
        array('q' => "`q` = `q` + ('unused')", 'int' => "`int` = `int` + (52)", 'w' => '`w` = `w` + (-56.1)',),
        array('used', 'q' => 'unused', 54 => 'used54', 'int' => 52, 'w' => -56.1),
      ),
    );
  }

  /**
   * @covers ::safeFieldsAdjust
   * @dataProvider dataSafeFieldsAdjust
   */
  public function testSafeFieldsAdjust($expected, $value) {
    $this->assertEquals($expected, invokeMethod($this->object, 'safeFieldsAdjust', array($value)));
  }

// TODO - will be used for full build test
//  public function dataSafeFieldsAdjustDanger() {
//    return array(
//      array(array(), 1),
//      array(array(), array()),
//
//      array(
//        array('q' => "`q` = `q` + ('unused')", 'int' => "`int` = `int` + (52)", 0 => 'used', 54 => 'used54', 'w' => '`w` = `w` + (-56.1)',),
//        array('used', 'q' => 'unused', 54 => 'used54', 'int' => 52, 'w' => -56.1),
//      ),
//    );
//  }




//  public function dataDelete() {
//    return array(
//      array('', $where, $whereDanger, $isOneRecord),
//    );
//  }
//
//  /**
//   * @param $expected
//   * @param $where
//   * @param $whereDanger
//   * @param $isOneRecord
//   *
//   * @covers ::quoteTable
//   * @covers ::buildCommand
//   * @covers ::dangerWhere
//   * @covers ::escape
//   * @covers ::quote
//   * @covers ::stringValue
//   * @covers ::castAsDbValue
//   * @covers ::fieldEqValue
//   * @covers ::buildWhere
//   * @covers ::buildLimit
//   * @covers ::delete
//   */
//  public function testDelete($expected, $where, $whereDanger, $isOneRecord) {
//    $this->assertEquals(
//      $expected,
//      $this->object
//        ->setTable('aTable')
//        ->setWhereArray($where)
//        ->setWhereArrayDanger($whereDanger)
//        ->setOneRow($isOneRecord)
//        ->delete()
//    );
//  }

}
