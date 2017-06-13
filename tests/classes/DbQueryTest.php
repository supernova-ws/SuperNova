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
   * @covers ::makeAdjustString
   */
  public function testMakeAdjustString() {
    $this->assertEquals("'test'", $result = invokeMethod($this->object, 'makeAdjustString', array('test', 1)));
    $this->assertEquals("`f1` = `f1` + ('v1')", $result = invokeMethod($this->object, 'makeAdjustString', array('v1', 'f1')));
  }

  /**
   * @covers ::makeFieldEqualValue
   */
  public function testMakeFieldEqualValue() {
    $this->assertEquals("'test2'", $result = invokeMethod($this->object, 'makeFieldEqualValue', array('test2', 1)));
    $this->assertEquals("`f2` = 'v2'", $result = invokeMethod($this->object, 'makeFieldEqualValue', array('v2', 'f2')));
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
   * @covers ::makeValueSafe
   * @dataProvider dataCastAsDbValue
   */
  public function testCastAsDbValue($expected, $type, $value, $originalType) {
    if ($originalType != TYPE_NULL && $originalType != TYPE_EMPTY) {
      $this->assertInternalType($originalType, $value);
    }
    $this->assertEquals($expected, invokeMethod($this->object, 'makeValueSafe', array($value)));
    $this->assertInternalType($type, invokeMethod($this->object, 'makeValueSafe', array($value)));
  }

  /**
   * @covers ::setTable
   * @covers ::setOneRow
   * @covers ::setValues
   * @covers ::setValuesDanger
   * @covers ::setAdjust
   * @covers ::setAdjustDanger
   * @covers ::setFields
   * @covers ::setWhereArray
   * @covers ::setWhereArrayDanger
   */
  public function testSetters() {
    $result = $this->object
      ->setTable('table')
      ->setOneRow(DbQuery::DB_RECORD_ONE)
      ->setValues(array('f1' => 'v1'))
      ->setValuesDanger(array('f2 <= v1'))
      ->setAdjust(array('f7' => '7'))
      ->setAdjustDanger(array('f3' => '3'))
      ->setFields(array('f4' => 'v4'))
      ->setWhereArray(array('f5' => '5'))
      ->setWhereArrayDanger(array('f6' => '6'));

    // Fluid interface is working
    $this->assertEquals($this->object, $result);

    $this->assertAttributeEquals('table', 'table', $this->object);
    $this->assertAttributeEquals(DbQuery::DB_RECORD_ONE, 'isOneRow', $this->object);
    $this->assertAttributeEquals(array('f1' => 'v1'), 'values', $this->object);
    $this->assertAttributeEquals(array('f2 <= v1'), 'valuesDanger', $this->object);
    $this->assertAttributeEquals(array('f7' => '7'), 'adjust', $this->object);
    $this->assertAttributeEquals(array('f3' => '3'), 'adjustDanger', $this->object);
    $this->assertAttributeEquals(array('f4' => 'v4'), 'fields', $this->object);
    $this->assertAttributeEquals(array('f5' => '5'), 'where', $this->object);
    $this->assertAttributeEquals(array('f6' => '6'), 'whereDanger', $this->object);
  }

  public function dataBuildCommand() {
    return array(
      array('', ''),
      array('SELECT', DbQuery::SELECT),
      array('INSERT INTO `{{theTable}}`', DbQuery::INSERT),
      array('INSERT IGNORE INTO `{{theTable}}`', DbQuery::INSERT_IGNORE),
      array('REPLACE INTO `{{theTable}}`', DbQuery::REPLACE),
      array('UPDATE `{{theTable}}`', DbQuery::UPDATE),
      array('DELETE FROM `{{theTable}}`', DbQuery::DELETE),
    );
  }

  /**
   * @covers ::buildCommand
   * @covers ::__toString
   * @dataProvider dataBuildCommand
   */
  public function testBuildCommand($expected, $command) {
    $this->object->setTable('theTable');
//    $property = getPrivateProperty($this->object, 'command');
//    $property->setValue($this->object, $command);
    invokeMethod($this->object, 'buildCommand', array($command));
    $this->assertEquals($expected, $this->object->__toString());
  }


  /**
   * @covers ::buildSetFields
   */
  public function testBuildSetFields() {
    $this->object
      ->setValues(array('f1' => 's1', 'f2' => 1))
      ->setValuesDanger(array('f3 = 5, f4 = "str4"  '))
      ->setAdjust(array('f5' => 1, 'f6' => 'str6', 0 => 'quotedString'))
      ->setAdjustDanger(array('f7 = f7 + 2  '));

    invokeMethod($this->object, 'buildSetFields', array());
    $this->assertEquals(" SET f3 = 5, f4 = \"str4\"  ,`f1` = 's1',`f2` = 1,f7 = f7 + 2  ,`f5` = `f5` + (1),`f6` = `f6` + ('str6'),'quotedString'", $this->object->__toString());
  }


  /**
   * @covers ::buildFieldNames
   */
  public function testBuildFieldNames() {
    $this->object->setFields(array('v1', 'v2'));
    invokeMethod($this->object, 'buildFieldNames', array());
    $this->assertEquals('`v1`,`v2`', $this->object->__toString());
  }

  /**
   * @covers ::buildValuesVector
   */
  public function testBuildValuesVector() {
    $this->object->setValues(array(
      array('f1' => 'string', 'f2' => 1, 'f3' => null),
      array('f1' => 'v1', 'f2' => 2, 'f3' => null),
    ));
    $this->object->setValuesDanger('(DANGER!)');
    invokeMethod($this->object, 'buildValuesVector', array());
    $this->assertEquals("(DANGER!),('string',1,NULL),('v1',2,NULL)", $this->object->__toString());
  }


  /**
   * @covers ::buildWhere
   */
  public function testBuildWhere() {
    invokeMethod($this->object, 'buildWhere', array());
    $this->assertEquals('', $this->object->__toString());

    $this->object
      ->setWhereArray(array('f1' => 's1', 0 => 'quotedString'))
      ->setWhereArrayDanger(array('f2' => 's2'));
    invokeMethod($this->object, 'buildWhere', array());
    $this->assertEquals(" WHERE s2 AND `f1` = 's1' AND 'quotedString'", $this->object->__toString());
  }


  /**
   * @covers ::buildLimit
   */
  public function testBuildLimit() {
    $this->object->setOneRow(DbQuery::DB_RECORD_ONE);
    invokeMethod($this->object, 'buildLimit', array());
    $this->assertEquals(" LIMIT 1", $this->object->__toString());
  }


  /**
   * @covers ::delete
   */
  public function testDelete() {
    $this->object
      ->setTable('aT')
      ->setWhereArray(array('f1' => 's1', 0 => 'ANDinQuote'))
      ->setWhereArrayDanger(array('f2' => 'f3 = 1'))
      ->setOneRow(DbQuery::DB_RECORD_ONE);
    $this->assertEquals("DELETE FROM `{{aT}}` WHERE f3 = 1 AND `f1` = 's1' AND 'ANDinQuote' LIMIT 1", $this->object->delete());
  }


  /**
   * @covers ::update
   */
  public function testUpdate() {
    $this->object
      ->setTable('aT')
      ->setValues(array('f1' => 's1'))
      ->setValuesDanger(array(' f3 = 5 '))
      ->setAdjust(array('f5' => 1, 'f6' => -1))
      ->setAdjustDanger(array(' f7 = f7 + 2 '))
      ->setWhereArray(array('f1' => 's1'))
      ->setWhereArrayDanger(array('f2' => 'f3 = 1'))
      ->setOneRow(DbQuery::DB_RECORD_ONE);
    $this->assertEquals(
      "UPDATE `{{aT}}`" .
      " SET  f3 = 5 ,`f1` = 's1'," .
      " f7 = f7 + 2 ,`f5` = `f5` + (1),`f6` = `f6` + (-1)" .
      " WHERE f3 = 1 AND `f1` = 's1' LIMIT 1",
      $this->object->update()
    );
  }



  /**
   * Insert SET test
   *
   * @covers ::insert
   * @covers ::setInsertCommand
   */
  public function testInsertSet() {
    $this->object
      ->setTable('aT')
      ->setValues(array('f1' => 's1'))
      ->setValuesDanger(array(' f3 = 5 '))
      ->setAdjust(array('f5' => 1, 'f6' => -1))
      ->setAdjustDanger(array(' f7 = f7 + 2 '));
    $this->assertEquals(
      "INSERT INTO `{{aT}}`" .
      " SET  f3 = 5 ,`f1` = 's1'," .
      " f7 = f7 + 2 ,`f5` = `f5` + (1),`f6` = `f6` + (-1)",
      invokeMethod($this->object, 'insert', array(DbQuery::DB_INSERT_PLAIN))
    );

    $this->object->setTable('aT2')->setAdjust(array('f25' => 21, 'f26' => -21));
    $this->assertEquals(
      "INSERT IGNORE INTO `{{aT2}}`" .
      " SET  f3 = 5 ,`f1` = 's1'," .
      " f7 = f7 + 2 ,`f5` = `f5` + (1),`f6` = `f6` + (-1)," .
      "`f25` = `f25` + (21),`f26` = `f26` + (-21)",
      invokeMethod($this->object, 'insert', array(DbQuery::DB_INSERT_IGNORE))
    );

    $this->object->setTable('aT2')->setAdjust(array('f36' => -31));
    $this->assertEquals(
      "REPLACE INTO `{{aT2}}`" .
      " SET  f3 = 5 ,`f1` = 's1'," .
      " f7 = f7 + 2 ,`f5` = `f5` + (1),`f6` = `f6` + (-1)," .
      "`f25` = `f25` + (21),`f26` = `f26` + (-21),`f36` = `f36` + (-31)",
      invokeMethod($this->object, 'insert', array(DbQuery::DB_INSERT_REPLACE))
    );
  }


  /**
   * Insert BATCH test
   *
   * @covers ::insert
   * @covers ::setInsertCommand
   */
  public function testInsertBatch() {
    $this->object
      ->setTable('aT')
      ->setFields(array('f1', 'f2'))
      ->setValues(array(array('v1', 1)))
    ;
    $this->assertEquals(
      "INSERT INTO `{{aT}}` (`f1`,`f2`) VALUES " .
      "('v1',1)"
      ,
      invokeMethod($this->object, 'insert', array(DbQuery::DB_INSERT_PLAIN))
    );

    $this->object
      ->setTable('aT2')
      ->setValues(array(array('v2', 2)))
    ;
    $this->assertEquals(
      "INSERT IGNORE INTO `{{aT2}}` (`f1`,`f2`) VALUES " .
      "('v1',1),('v2',2)"
      ,
      invokeMethod($this->object, 'insert', array(DbQuery::DB_INSERT_IGNORE))
    );

    $this->object
      ->setTable('aT2')
      ->setValues(array(array('v3', 3),array('v4', 4)))
      ->setValuesDanger(array('this IS danger!'))
    ;
    $this->assertEquals(
      "REPLACE INTO `{{aT2}}` (`f1`,`f2`) VALUES " .
      "this IS danger!,('v1',1),('v2',2),('v3',3),('v4',4)"
      ,
      invokeMethod($this->object, 'insert', array(DbQuery::DB_INSERT_REPLACE))
    );
  }

  /**
   * @covers ::select
   */
  public function testSelect() {
    $this->object
      ->setTable('aT')
      // ->setFields(array('f1', 'f2')) // TODO - unused
      ->setWhereArray(array('f1' => 's1'))
      ->setWhereArrayDanger(array('f2' => '(f3 = 1 OR f3 = 2)'))
      ->setOneRow(DbQuery::DB_RECORD_ONE)
    ;
    $this->assertEquals(
      "SELECT * FROM `{{aT}}` WHERE (f3 = 1 OR f3 = 2) AND `f1` = 's1' LIMIT 1",
      $this->object->select()
    );
  }

}
