<?php
/**
 * Created by Gorlum 12.06.2017 14:41
 */

namespace DBAL;


/**
 * Class ActiveRecordStatic
 *
 * @package DBAL
 */

abstract class ActiveRecordStatic {
  const IGNORE_PREFIX = 'table_';

  protected static $_primaryIndexField = 'id';
  protected static $_tableName = '';

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

  public function __construct(\db_mysql $db = null) {

  }

  public function save() {
//    $dbq = static::prepareDbQuery()
//      ->setValues()
//      ->doInsert();
  }

  /**
   * @param $array
   *
   * @return array|bool|\mysqli_result|null
   */
  public static function updateFromArray($array) {
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
    static::$_tableName = \HelperString::camelToUnderscore(basename(get_called_class()));
    if (strpos(static::$_tableName, static::IGNORE_PREFIX) === 0) {
      static::$_tableName = substr(static::$_tableName, strlen(static::IGNORE_PREFIX));
    }
  }

  /**
   * @return DbQuery
   */
  protected static function prepareDbQuery() {
    return DbQuery::build(static::db())->setTable(static::tableName());
  }

}
