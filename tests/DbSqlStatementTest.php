<?php

/**
 * Class DbSqlStatementTest
 *
 * @coversDefaultClass DbSqlStatement
 */
class DbSqlStatementTest extends PHPUnit_Framework_TestCase {

  /**
   * @var DbSqlStatement $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();
    $this->object = new DbSqlStatement(null);
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::select
   * @covers ::from
   * @covers ::setIdField
   * @covers ::field
   * covers ::fields
   * covers ::where
   * covers ::groupBy
   * covers ::orderBy
   * covers ::having
   * @covers ::__call
   * @covers ::limit
   * @covers ::offset
   * @covers ::fetchOne
   * @covers ::forUpdate
   * @covers ::skipLock
   * @covers ::_reset
   */
  public function test_reset() {
    // Setting some values
    $this->assertEquals(
      $this->object,
      $this->object
        ->select()
        ->from('aTable', 'theTable')
        ->setIdField('idField')
        ->fields('qwe')
        ->field('qer', 'qrt')
        ->field('qas', 'qzx')
        ->where('asd')
        ->groupBy('qaz')
        ->orderBy('zxc')
        ->having('wes')
        ->limit(2)
        ->offset(3)
        ->fetchOne()
        ->forUpdate()
        ->skipLock()
    );

    // Checking that all properties is set
    $this->assertEquals(DbSqlStatement::SELECT, $this->object->operation);
    $this->assertEquals('aTable', $this->object->table);
    $this->assertEquals('theTable', $this->object->alias);
    $this->assertEquals('idField', $this->object->idField);

    $this->assertEquals(array('qwe', 'qer', 'qrt', 'qas', 'qzx'), $this->object->fields);
    $this->assertEquals(array('asd'), $this->object->where);
    $this->assertEquals(array('qaz'), $this->object->groupBy);
    $this->assertEquals(array('zxc'), $this->object->orderBy);
    $this->assertEquals(array('wes'), $this->object->having);

    $this->assertEquals(2, $this->object->limit);
    $this->assertEquals(3, $this->object->offset);

    $this->assertTrue($this->object->fetchOne);
    $this->assertTrue($this->object->forUpdate);
    $this->assertTrue($this->object->skipLock);

    // Checking soft-reset
    $this->assertEquals($this->object, invokeMethod($this->object, '_reset', array(false)));

    // Checking that protected part is not affected
    $this->assertEquals(DbSqlStatement::SELECT, $this->object->operation);
    $this->assertEquals('aTable', $this->object->table);
    $this->assertEquals('theTable', $this->object->alias);
    $this->assertEquals('idField', $this->object->idField);

    // Checking that common part is cleared
    $this->assertEquals(array(), $this->object->fields);
    $this->assertEquals(array(), $this->object->where);
    $this->assertEquals(array(), $this->object->groupBy);
    $this->assertEquals(array(), $this->object->orderBy);
    $this->assertEquals(array(), $this->object->having);

    $this->assertEquals(0, $this->object->limit);
    $this->assertEquals(0, $this->object->offset);

    $this->assertFalse($this->object->fetchOne);
    $this->assertFalse($this->object->forUpdate);
    $this->assertFalse($this->object->skipLock);

    // Hard reset
    $this->assertEquals($this->object, invokeMethod(
      $this->object
        ->select()
        ->fields(array('qwe'))
        ->from('aTable', 'theTable')
        ->setIdField('idField'),
      '_reset',
      array(true)
    ));

    $this->assertEquals('', $this->object->operation);
    $this->assertEquals('', $this->object->table);
    $this->assertEquals('', $this->object->alias);
    $this->assertEquals('', $this->object->idField);
  }

  /**
   * @covers ::__construct
   * @covers ::build
   * @covers ::getParamsFromStaticClass
   */
  public function testBuild() {
    $this->assertEquals('DbSqlStatement', get_class($test = DbSqlStatement::build(null, 'DBStaticRecord')));

    $this->assertEquals('_table', $test->table);
    $this->assertEquals('id', $test->idField);

    unset($test);
  }

  /**
   * @covers ::select
   */
  public function testSelect() {
    $this->assertEquals($this->object, $this->object->select());

    // Testing behaviour by default
    $this->assertEquals(DbSqlStatement::SELECT, $this->object->operation);
    $this->assertEquals(array(), $this->object->fields);

    // Testing preset fields
    $this->assertEquals($this->object, $this->object->fields('test')->select());
    $this->assertEquals(array('test'), $this->object->fields);
  }

  /**
   * @covers ::fetchOne
   * @covers ::limit
   * @covers ::offset
   */
  public function testFetchOne() {
    $this->assertEquals($this->object, $this->object->limit(10)->offset(20)->fetchOne());

    // Testing behaviour by default
    $this->assertTrue($this->object->fetchOne);
    $this->assertEquals(10, $this->object->limit);
    $this->assertEquals(20, $this->object->offset);

    // Testing fetchOne reset
    $this->assertEquals($this->object, $this->object->limit(10)->offset(20)->fetchOne(false));
    $this->assertNotTrue($this->object->fetchOne);
  }

  /**
   * covers ::where
   * @covers ::__call
   */
  public function testWhere() {
    // Scalar value
    $this->assertEquals($this->object, $this->object->where('a = b'));
    $this->assertEquals(array('a = b'), $this->object->where);

    // Array - replace
    $this->assertEquals($this->object, $this->object->where(array('b = c')));
    $this->assertEquals(array('b = c'), $this->object->where);

    // Array - merge
    $this->assertEquals($this->object, $this->object->where(array('w'), HelperArray::ARRAY_MERGE));
    $this->assertEquals(array('b = c', 'w'), $this->object->where);
  }

  /**
   * @covers ::select
   * covers ::fields
   * @covers ::__call
   * @covers ::from
   * @covers ::fromAlias
   * @covers ::setIdField
   */
  public function testChain() {
    $this->assertEquals($this->object, $this->object->select()->fields(array('qwe'))->from('aTable', 'theTable')->setIdField('idField'));

    $this->assertEquals(DbSqlStatement::SELECT, $this->object->operation);
    $this->assertEquals('aTable', $this->object->table);
    $this->assertEquals('theTable', $this->object->alias);
    $this->assertEquals('idField', $this->object->idField);

    $this->assertEquals(array('qwe'), $this->object->fields);
  }

  /**
   * @covers ::getParamsFromStaticClass
   */
  public function testGetParamsFromStaticClass() {
    $this->assertEquals($this->object, $this->object->getParamsFromStaticClass('DBStaticRecord'));

    $this->assertEquals('_table', $this->object->table);
    $this->assertEquals('id', $this->object->idField);
  }


  // selectFieldsToString ----------------------------------------------------------------------------------------------
  public function dataSelectFieldsToString() {
    return array(
      // Testing single values

      // --- Every field
      array('*', '*'),

      // --- DbSqlLiteral
      array('MAX()', new DbSqlLiteral(null, 'MAX()')),

      // --- Booleans
      array('1', true),
      array('0', false),

      // --- Numeric
      array('2', 2),
      array('1.2', 1.2),
      array('1.5E+100', 1.5e100),
      array('1.5E-100', 1.5e-100),

      // --- Null
      array('NULL', null),

      // --- String
      array('`test`', 'test'),

      // --- Testing escaping with stringEscape function
      array('`t\\\'e\\"s\\\\t`', 't\'e"s\\t'),

      // Testing arrays
      array('`t\\\'e\\"s\\\\t`,1,NULL,2,*', array('t\'e"s\\t', true, null, 2, '*')),
      array('0', array('', '', false)),
    );
  }


  /**
   * @param string $expected
   * @param mixed $param
   *
   * @dataProvider dataSelectFieldsToString
   *
   * @covers ::selectFieldsToString
   * @covers ::processField
   * @covers ::makeFieldFromString
   */
  public function testSelectFieldsToString($expected, $param) {
    $this->assertEquals($expected, invokeMethod($this->object, 'selectFieldsToString', array($param)));
  }

  public function dataSelectFieldsToStringExceptionDataProvider() {
    return array(
      array(''),
      array(array()),
      array(array('', '')),
    );
  }

  /**
   * @param mixed $badValue
   *
   * @dataProvider dataSelectFieldsToStringExceptionDataProvider
   *
   * @covers ::selectFieldsToString
   * @expectedException ExceptionDBFieldEmpty
   */
  public function testSelectFieldsToStringException($badValue) {
    invokeMethod($this->object, 'selectFieldsToString', array($badValue));
  }



  // __toString --------------------------------------------------------------------------------------------------------
  /**
   * @covers ::__toString
   * @expectedException ExceptionDbOperationEmpty
   */
  public function test__toStringExceptionDbOperationEmpty() {
    $this->object->operation = '';
    $this->object->__toString();
  }

  /**
   * @covers ::__toString
   * @expectedException ExceptionDbOperationRestricted
   */
  public function test__toStringExceptionDbOperationRestricted() {
//    return doquery("SELECT u.*, COUNT(r.id) AS referral_count, SUM(r.dark_matter) AS referral_dm FROM {{users}} as u
//    LEFT JOIN {{referrals}} as r on r.id_partner = u.id
//    WHERE" .
//      ($online ? " `onlinetime` >= " . (SN_TIME_NOW - classSupernova::$config->game_users_online_timeout) : ' user_as_ally IS NULL') .
//      " GROUP BY u.id
//    ORDER BY user_as_ally, {$sort} ASC");

//    $test = DBStaticUser::buildSelectNoFields()
//      ->fromAlias('u')
//      ->field('u.*')
////      ->field(DbSqlLiteral::build()->count('r.id', 'referral_count'))
////      ->field(DbSqlLiteral::build()->sum('r.dark_matter', 'referral_dm'))
////      ->count('r.id', 'referral_count')
//      ->singleFunction('count','r.id', 'referral_count')
//      ->singleFunction('sum','r.dark_matter', 'referral_dm')
//      ->join('LEFT JOIN {{referrals}} as r on r.id_partner = u.id')
//      ->where(!empty($online) ? "`onlinetime` >= " . (SN_TIME_NOW - classSupernova::$config->game_users_online_timeout) : 'user_as_ally IS NULL')
//      ->groupBy('u.id')
//      ->orderBy('user_as_ally, {$sort} ASC');
//
//
//    @print($test);


    $this->object->operation = 'RESTRICTED';
    $this->object->__toString();
  }

}
