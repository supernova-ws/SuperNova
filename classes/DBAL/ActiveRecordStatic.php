<?php
/**
 * Created by Gorlum 12.06.2017 14:41
 */

namespace DBAL;

use Common\AccessLogged;
use Common\GlobalContainer;


/**
 * Class ActiveRecordStatic
 *
 * @property int|string $id
 *
 * @package DBAL
 */
abstract class ActiveRecordStatic extends AccessLogged {
  const IGNORE_PREFIX = 'Table';

  protected static $_primaryIndexField = 'id';
  protected static $_tableName = '';

  // AR's service fields
  protected $_isNew = true;

  /**
   * Get table name
   *
   * @return string
   */
  public static function tableName() {
    if (empty(static::$_tableName)) {
      static::fillTableName();
    }

    return static::$_tableName;
  }

  /**
   * Get used DB
   *
   * @return \db_mysql
   */
  public static function db() {
    return \classSupernova::$db;
  }

  /**
   * Finds records by params in DB
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]. Pass [] to find all records in DB
   *
   * @return bool|\mysqli_result
   */
  public static function find($where) {
    if (!is_array($where)) {
      $where = [static::$_primaryIndexField => $where];
    }

    $dbq = static::prepareDbQuery();
    if (!empty($where)) {
      $dbq->setWhereArray($where);
    }

    return $dbq->doSelect();
  }

  /**
   * Gets first record by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return string[] - [$field_name => $field_value]
   */
  public static function findOne($where) {
    $result = empty($dbq = static::find($where)) ? [] : static::db()->db_fetch($dbq);

    return empty($result) ? [] : $result;
  }

  /**
   * Gets all records by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return string[][] - [(int) => [$field_name => $field_value]]
   */
  public static function findAll($where) {
    return empty($dbq = static::find($where)) ? [] : $dbq->fetch_all(MYSQL_ASSOC);
  }

  /**
   * Gets all records by $where - array indexes is a record IDs
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return string[][] - [$record_db_id => [$field_name => $field_value]]
   */
  public static function findAllIndexed($where) {
    $result = [];
    if (!empty($dbq = static::find($where))) {
      while ($row = static::db()->db_fetch($dbq)) {
        $result[$row[static::$_primaryIndexField]] = $row;
      }
    }

    return $result;
  }

  /**
   * Normalize array
   *
   * Basically - uppercase all field names to make it use in PTL
   * Can be override by descendants to make more convinient, clear and robust indexes
   *
   * @param array $array
   *
   * @return array
   */
  public static function ptlArray(array $array) {
    $result = [];
    foreach ($array as $key => $value) {
      $result[strtoupper($key)] = $value;
    }

    return $result;
  }

  /**
   * ActiveRecordStatic constructor.
   *
   * @param GlobalContainer|null $services
   */
  public function __construct(GlobalContainer $services = null) {
    parent::__construct($services);
  }

  public function save() {
//    $dbq = static::prepareDbQuery()
//      ->setValues()
//      ->doInsert();
  }

  /**
   * @param array $array
   *
   * @return array|bool|\mysqli_result|null
   */
  public static function updateFromArray(array $array) {
    return
      static::prepareDbQuery()
        ->setValues($array)
        ->setWhereArray([static::$_primaryIndexField => $array[static::$_primaryIndexField]])
        ->doUpdate();
  }

  /**
   * Fills table name based on class if it is empty
   *
   * Namespaces does not count - only class name taken into account
   * Class name converted from CamelCase to underscore_name
   * Prefix "Table" is ignored - can be override
   *
   * Examples:
   * Class \Namespace\ClassName will map to table `class_name`
   * Class \NameSpace\TableLongName will map to table `long_name`
   *
   */
  protected static function fillTableName() {
    $temp = explode('\\', get_called_class());
    $className = end($temp);
    if (strpos($className, static::IGNORE_PREFIX) === 0) {
      $className = substr($className, strlen(static::IGNORE_PREFIX));
    }
    static::$_tableName = \HelperString::camelToUnderscore($className);
  }

  /**
   * @return DbQuery
   */
  protected static function prepareDbQuery() {
    return DbQuery::build(static::db())->setTable(static::tableName());
  }

  protected function fillFields($array) {
    foreach ($array as $name => $value) {
      if ($name == static::$_primaryIndexField) {
        $name = 'id';
      }
      $record->__set($name, $value);
    }
  }

  public function getValuesArray() {
    return $this->values;
  }


  /**
   * @param $array
   *
   * @return static|bool
   */
  public static function fromArray($array) {
    if (!is_array($array) || empty($array)) {
      return false;
    }

    $record = new static();
    $record->fillFields($array);

    return $record;
  }

  /**
   * Gets first ActiveRecord by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return static|bool
   */
  public static function findOneObject($where) {
    $record = static::fromArray(static::findOne($where));
    if (!empty($record)) {
      $record->_isNew = false;
    }

    return $record;
  }

  /**
   * @param array $records
   *
   * @return array|static[]
   */
  public static function fromArrayList($records) {
    if (is_array($records) && !empty($records)) {
      foreach ($records as &$record) {
        $record = static::fromArray($record);
        $record->_isNew = false;
      }
    } else {
      $records = [];
    }

    return $records;
  }

  /**
   * Gets all ActiveRecords by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return array|static[] - [(int) => static]
   */
  public static function findAllObjects($where) {
    return static::fromArrayList(static::findAll($where));
  }

  /**
   * Gets all ActiveRecords by $where - array indexed by record IDs
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return array|static[] - [$record_db_id => static]
   */
  public static function findAllIndexedObjects($where) {
    return static::fromArrayList(static::findAllIndexed($where));
  }

}
