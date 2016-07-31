<?php

class DBRowPublish extends DBRow {
  public function dbGetLockById($dbId) {
    // TODO: Implement dbGetLockById() method.
  }

  public function isEmpty() {
    // TODO: Implement isEmpty() method.
  }

}

/**
 * Class DBRowTest
 *
 * @coversDefaultClass DBRow
 */
class DBRowTest extends PHPUnit_Framework_TestCase {
  /**
   * @var DBRowPublish $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();
    $this->object = new DBRowPublish(null);
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::getDb
   * @covers ::setDb
   */
  public function testSetDb() {
    // Default values check
    $this->assertEquals(null, DBRowPublish::getDb());

    // Empty value check
    DBRowPublish::setDb('');
    $this->assertEquals(null, DBRowPublish::getDb());

    // Non-empty non-object value check
    DBRowPublish::setDb('qwe');
    $this->assertEquals(null, DBRowPublish::getDb());

    // Non-empty non-db_mysql value check
    DBRowPublish::setDb(new stdClass());
    $this->assertEquals(null, DBRowPublish::getDb());

    // db_mysql value check. Also check that getter return value - not 'null' every time
    $db = new db_mysql(new \Common\GlobalContainer());
    DBRowPublish::setDb($db);
    $this->assertEquals($db, DBRowPublish::getDb());

    // Null again
    DBRowPublish::setDb(null);
    $this->assertEquals(null, DBRowPublish::getDb());
  }

  /**
   * @covers ::getTable
   * @covers ::setTable
   */
  public function testSetTable() {
    $this->assertEquals('', DBRowPublish::getTable());

    DBRowPublish::setTable('table');
    $this->assertEquals('table', DBRowPublish::getTable());
  }

  /**
   * @covers ::getIdFieldName
   * @covers ::setIdFieldName
   */
  public function testSetIdFieldName() {
    $this->assertEquals('id', DBRowPublish::getIdFieldName());

    DBRowPublish::setIdFieldName('id2');
    $this->assertEquals('id2', DBRowPublish::getIdFieldName());
  }

  /**
   * @covers ::__construct
   * @covers ::getDb
   * covers ::build
   */
  public function test__construct() {
    $db = new db_mysql(new \Common\GlobalContainer());
    $test = new DBRowPublish($db);

    $this->assertEquals('DBRowPublish', get_class($test));
    $this->assertEquals($db, $test->getDb());

    unset($test);
  }

}
