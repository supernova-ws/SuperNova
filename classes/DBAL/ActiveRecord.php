<?php
/**
 * Created by Gorlum 12.06.2017 14:41
 */

namespace DBAL;


/**
 * Class ActiveRecord
 *
 * @package DBAL
 */

abstract class ActiveRecord {
  const TABLE = 'table_';

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
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return bool|\mysqli_result
   */
  public static function find($where) {
    if (!is_array($where)) {
      $where = [static::$_primaryIndexField => $where];
    }

    if (!empty($where)) {
      $result = static::prepareDbQuery()
        ->setWhereArray($where)
        ->doSelect();
    } else {
      $result = false;
    }

    return $result;
  }

  /**
   * Gets first record by $where
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return array - [$field_name => $field_value]
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
   * @return array - [(int) => [$field_name => $field_value]]
   */
  public static function findAll($where) {
    return empty($dbq = static::find($where)) ? [] : $dbq->fetch_all(MYSQL_ASSOC);
  }

  /**
   * Gets all records by $where - array indexes is a record IDs
   *
   * @param array|mixed $where - ID of found record OR [$field_name => $field_value]
   *
   * @return array - [$record_db_id => [$field_name => $field_value]]
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

  public static function insert($values) {
    $dbq = static::prepareDbQuery()
      ->setValues()
      ->doInsert();
  }

  /**
   * Fills table name based on class if it is empty
   *
   * Namespaces does not count - only class name took into account
   * Class name converted from CamelCase to underscore_name
   * Prefix "Table" is ignored - can be override
   *
   * Examples:
   * \Namespace\ClassName will map to table `class_name`
   * \NameSpace\TableLongName will map to table `long_name`
   *
   */
  protected static function fillTableName() {
    static::$_tableName = \HelperString::camelToUnderscore(basename(get_called_class()));
    if (strpos(static::$_tableName, static::TABLE) === 0) {
      static::$_tableName = substr(static::$_tableName, strlen(static::TABLE));
    }
  }

  /**
   * @return DbQuery
   */
  protected static function prepareDbQuery() {
    return DbQuery::build(static::db())->setTable(static::tableName());
  }

}
