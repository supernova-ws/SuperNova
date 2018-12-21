<?php
/**
 * Created by Gorlum 12.07.2017 12:28
 */

namespace DBAL\Tests;

use Core\GlobalContainer;
use DBAL\db_mysql;
use DBAL\ActiveRecordAbstract;
use DBAL\Tests\Fixtures\ActiveAbstractObjectDump;
use DBAL\Tests\Fixtures\RecordActiveAbstractObject;

/**
 * Class AccessLoggedTest
 * @coversDefaultClass \DBAL\ActiveRecordAbstract
 * @package classes
 */
class ActiveRecordAbstractTest extends \PHPUnit_Framework_TestCase {

  /**
   * Checking values setting
   *
   * @covers ::db
   * @covers ::setDb
   * @covers ::tableName
   * @covers ::calcTableName
   * @covers ::dbPrepareQuery
   */
  public function testDbData() {
    $this->assertEquals(\SN::services()->db, RecordActiveAbstractObject::db());

    $db = new db_mysql(new GlobalContainer());
    RecordActiveAbstractObject::setDb($db);
    $this->assertAttributeEquals($db, 'db', 'DBAL\Tests\Fixtures\RecordActiveAbstractObject');
    $this->assertEquals($db, RecordActiveAbstractObject::db());

    $this->assertEquals('active_abstract_object', RecordActiveAbstractObject::tableName());
    $this->assertEquals('active_abstract_object_dump', ActiveAbstractObjectDump::tableName());

    $dbq = invokeMethod(RecordActiveAbstractObject::class, 'dbPrepareQuery');
    $this->assertEquals("DBAL\\DbQuery", get_class($dbq));
    $this->assertAttributeEquals('active_abstract_object', 'table', $dbq);
  }

  /**
   * @covers ::haveTranslationToProperty
   */
  public function testHaveTranslationToProperty() {
    // Direct access field have NO translation
    $this->assertFalse(invokeMethod(RecordActiveAbstractObject::class, 'haveTranslationToProperty', ['varchar']));
    // Translated field DO HAVE translation
    $this->assertTrue(invokeMethod(RecordActiveAbstractObject::class, 'haveTranslationToProperty', ['timestamp_current']));
    // Property name have NO translation
    $this->assertFalse(invokeMethod(RecordActiveAbstractObject::class, 'haveTranslationToProperty', ['timestampCurrent']));
    // Not exists field have NO translation
    $this->assertFalse(invokeMethod(RecordActiveAbstractObject::class, 'haveTranslationToProperty', ['notAField']));
  }

  /**
   * @covers ::haveField
   */
  public function testHaveField() {
    // Checking for fields and properties
    $this->assertTrue(invokeMethod(RecordActiveAbstractObject::class, 'haveField', ['varchar']));
    $this->assertTrue(invokeMethod(RecordActiveAbstractObject::class, 'haveField', ['timestamp_current']));
    $this->assertFalse(invokeMethod(RecordActiveAbstractObject::class, 'haveField', ['timestampCurrent']));
    $this->assertFalse(invokeMethod(RecordActiveAbstractObject::class, 'haveField', ['notAField']));
  }

  /**
   * @covers ::haveProperty
   */
  public function testHaveProperty() {
    $this->assertTrue(invokeMethod(RecordActiveAbstractObject::class, 'haveProperty', ['varchar']));
    $this->assertTrue(invokeMethod(RecordActiveAbstractObject::class, 'haveProperty', ['timestampCurrent']));
    $this->assertFalse(invokeMethod(RecordActiveAbstractObject::class, 'haveProperty', ['timestamp_current']));
    $this->assertFalse(invokeMethod(RecordActiveAbstractObject::class, 'haveProperty', ['notAField']));
  }


  public function dataGetPropertyName() {
    return
      [
        ['varchar', 'varchar'],
        ['timestamp_current', 'timestampCurrent'],
        ['timestampCurrent', ''],
        ['notAField', ''],
      ];
  }

  /**
   * @covers ::getPropertyName
   * @dataProvider dataGetPropertyName
   */
  public function testGetPropertyName($param, $expected) {
    $this->assertEquals($expected, invokeMethod(RecordActiveAbstractObject::class, 'getPropertyName', [$param]));
  }


  /**
   * @return array
   */
  public function dataGetFieldName() {
    return
      [
        ['varchar', 'varchar'],
        ['timestampCurrent', 'timestamp_current'],
        ['timestamp_current', ''],
        ['notAField', ''],
      ];
  }

  /**
   * @covers ::getFieldName
   * @dataProvider dataGetFieldName
   */
  public function testGetFieldName($param, $expected) {
    $this->assertEquals($expected, invokeMethod(RecordActiveAbstractObject::class, 'getFieldName', [$param]));
  }

  /**
   * @covers ::translateNames
   */
  public function testTranslation() {
    // Fields to Properties
    $this->assertEquals(
      [
        'timestampCurrent' => TEST_VALUE_SQL_DATE,
        'varchar'          => 'varvalue',
        // TODO - add incorrect field
      ],
      invokeMethod(RecordActiveAbstractObject::class, 'translateNames', [
        [
          'timestamp_current' => TEST_VALUE_SQL_DATE,
          'varchar'           => 'varvalue',
          'notAField'         => 'test',
        ],
        ActiveRecordAbstract::FIELDS_TO_PROPERTIES
      ]));

    // Properties to Fields
    $this->assertEquals(
      [
        'timestamp_current' => TEST_VALUE_SQL_DATE,
        'varchar'           => 'varvalue',
      ],
      invokeMethod(RecordActiveAbstractObject::class, 'translateNames', [
        [
          'timestampCurrent' => TEST_VALUE_SQL_DATE,
          'varchar'          => 'varvalue',
          'notAProperty'     => 'test',
        ],
        ActiveRecordAbstract::PROPERTIES_TO_FIELDS
      ]));
  }

  /**
   * @covers ::__construct
   * @covers ::accept
   */
  public function testConstructor() {
    $object = new RecordActiveAbstractObject();
//    $this->assertAttributeEquals(\SN::$gc, 'services', $object);
    $this->assertAttributeEquals(true, '_isNew', $object);
//    $this->assertEquals(0, $object->id);
//
//    $object->accept();
//    $this->assertAttributeEquals(true, '_isNew', $object);
//    $this->assertEquals(0, $object->id);
//
//    $object->id = 5;
//    $object->accept();
//    $this->assertAttributeEquals(false, '_isNew', $object);
//    $this->assertEquals(5, $object->id);

  }

  /**
   * @covers ::defaultValues
   * @covers ::fromProperties
   * @covers ::buildEvenEmpty
   * @covers ::build
   */
  public function testBuild() {
    // Testing empty buildEvenEmpty()
    $this->assertEquals(
      RecordActiveAbstractObject::class,
      get_class(invokeMethod(RecordActiveAbstractObject::class, 'buildEvenEmpty', [[]]))
    );

    // Testing empty build
    $this->assertFalse(RecordActiveAbstractObject::build([]));

    // Testing default values and overriding CURRENT_TIMESTAMP
    $object = RecordActiveAbstractObject::build([
      'timestampCurrent' => TEST_VALUE_SQL_DATE,
      'varchar'          => 'varvalue',
    ]);
    $this->assertEquals(TEST_VALUE_SQL_DATE, $object->timestampCurrent);
    $this->assertEquals('varvalue', $object->varchar);
    $this->assertNull($object->null);
    $this->assertNull($object->notAField);
    $this->assertEquals(
      [
        'timestampCurrent' => TEST_VALUE_SQL_DATE,
        'varchar'          => "varvalue",
        'null'             => null,
      ],
      $object->asArray()
    );


    // Testing CURRENT_TIMESTAMP and setting values to NULL-defaulted values
    $object = RecordActiveAbstractObject::build([
      'null' => 'nullvalue',
    ]);

    $this->assertEquals(date(FMT_DATE_TIME_SQL, SN_TIME_NOW), $object->timestampCurrent);
    $this->assertEquals('', $object->varchar);
    $this->assertEquals('nullvalue', $object->null);
  }

  /**
   * @covers ::fromFields
   */
  public function testFromFields() {
    $object = new RecordActiveAbstractObject();
    invokeMethod($object, 'fromFields', [[
      'timestamp_current' => TEST_VALUE_SQL_DATE,
      'notAField'         => 'test',
    ]]);

    $this->assertEquals(TEST_VALUE_SQL_DATE, $object->timestampCurrent);
    $this->assertEquals('', $object->varchar);
    $this->assertNull($object->notAField);
  }


  /**
   * @covers ::getDefault
   */
  public function testGetDefault() {
    $object = RecordActiveAbstractObject::buildEvenEmpty([]);

    $this->assertNull(invokeMethod($object, 'getDefault', ['timestamp_current']));
    $this->assertNull(invokeMethod($object, 'getDefault', ['NonExistingProperty']));
  }

  /**
   * @covers ::shieldName
   * @covers ::__set
   * expectedExceptionMessageRegExp /{{{ Свойство \s+ не существует в ActiveRecord \s+ }}}/
   * expectedExceptionMessage {{{ Свойство \s+ не существует в ActiveRecord \s+ }}}
   */
  public function test__set() {
    $object = RecordActiveAbstractObject::buildEvenEmpty([]);
    $this->assertEquals(SN_TIME_SQL, $object->timestampCurrent);
    $object->timestampCurrent = TEST_VALUE_SQL_DATE;
    $this->assertEquals(TEST_VALUE_SQL_DATE, $object->timestampCurrent);
    $this->expectExceptionMessage('{{{ Свойство \'q\' не существует в ActiveRecord \'' . RecordActiveAbstractObject::class . '\' }}}');
    $object->q = 5;
  }

  /**
   * @covers ::shieldName
   * @covers ::getDefault
   * @covers ::__get
   */
  public function test__get() {
    // Testing directly set values
    $object = RecordActiveAbstractObject::build([
      'timestampCurrent' => TEST_VALUE_SQL_DATE,
      'varchar'          => 'varvalue',
      'null'             => 'qwe',
    ]);
    $this->assertEquals(TEST_VALUE_SQL_DATE, $object->__get('timestampCurrent'));
    $this->assertEquals(TEST_VALUE_SQL_DATE, $object->timestampCurrent);

    // Checking false-positive for field translated to property
    $this->assertNull($object->__get('timestamp_current'));
    $this->assertNull($object->timestamp_current);

    $this->assertEquals('varvalue', $object->varchar);
    $this->assertEquals('qwe', $object->null);
    $this->assertNull($object->notAProperty);

    // Testing CURRENT_TIMESTAMP and setting values to NULL-defaulted values
    $object = RecordActiveAbstractObject::buildEvenEmpty([]);

    $this->assertEquals(date(FMT_DATE_TIME_SQL, SN_TIME_NOW), $object->timestampCurrent);
    $this->assertEquals('', $object->varchar);
    $this->assertNull($object->null);
  }

  /**
   * @covers ::fromRecordList
   */
  public function testFromRecordList() {
    $this->assertEquals([], invokeMethod(RecordActiveAbstractObject::class, 'fromRecordList', ['']));
    $this->assertEquals([], invokeMethod(RecordActiveAbstractObject::class, 'fromRecordList', [[]]));

    $testable = invokeMethod(RecordActiveAbstractObject::class, 'fromRecordList', [[
      ['null' => null,],
      [],
      ['timestamp_current' => TEST_VALUE_SQL_DATE, 'null' => 'test',],
    ]]);
    $this->assertCount(2, $testable);
    $this->assertEquals(RecordActiveAbstractObject::class, get_class($testable[0]));
    // Checking that indexes are maintained
    $this->assertEquals(RecordActiveAbstractObject::class, get_class($testable[2]));
    $this->assertEquals(TEST_VALUE_SQL_DATE, $testable[2]->timestampCurrent);
    $this->assertEquals('test', $testable[2]->null);

    $testable = invokeMethod(RecordActiveAbstractObject::class, 'fromRecordList', [
      [
        ['null' => null,],
        [],
        ['timestampCurrent' => TEST_VALUE_SQL_DATE, 'null' => 'test',],
      ],
      RecordActiveAbstractObject::PROPERTIES_TO_FIELDS
    ]);
    $this->assertCount(2, $testable);
    $this->assertEquals(RecordActiveAbstractObject::class, get_class($testable[0]));
    $this->assertEquals(RecordActiveAbstractObject::class, get_class($testable[2]));
    $this->assertEquals(TEST_VALUE_SQL_DATE, $testable[2]->timestampCurrent);
    $this->assertEquals('test', $testable[2]->null);
  }

}
