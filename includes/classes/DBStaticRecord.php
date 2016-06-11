<?php

/**
 * Class DBStaticRecord
 */
class DBStaticRecord {

  public static $_table = '_table';
  public static $_idField = 'id';

  /**
   * @var db_mysql $dbStatic
   */
  protected static $dbStatic;

  /**
   * DBStaticRecord constructor.
   *
   * @param db_mysql|null $db
   */
  public static function _init($db = null) {
    static::$dbStatic = (!empty($db) && $db instanceof db_mysql) || !class_exists('classSupernova', false) ? $db : classSupernova::$db;
  }

  /**
   * @return DbQueryConstructor
   */
  public static function buildSelect() {
    return
      DbQueryConstructor::build(self::$dbStatic)
        ->getParamsFromStaticClass(get_called_class())
        ->select();
  }

  /**
   * @return DbQueryConstructor
   */
  public static function buildSelectCountId() {
    return
      static::buildSelect()
        ->fieldCount(static::$_idField);
  }

  /**
   * @return DbQueryConstructor
   */
  public static function buildSelectLock() {
    return
      static::buildSelect()
        ->field(1)
        ->setForUpdate();
  }

  /**
   * @param array       $where
   * @param mixed|array $fieldList
   *     Field list can be scalar - it would be converted to array and used as field name
   * @param bool        $for_update
   *
   * @return array
   */
  protected static function getRecord($where = array(), $fieldList = '*', $for_update = false) {
    $stmt =
      static::buildSelect()
        ->fields($fieldList)
        ->where($where)
        ->setFetchOne();


    $result = static::$dbStatic->selectRow($stmt);

    return $result;
  }

  /**
   * Get maximum ID from table
   *
   * @return int
   */
  public static function getMaxId() {
    $maxId = static::getRecord(array(), DbSqlLiteral::build()->max(static::$_idField, 'maxId'));

    return !empty($maxId['maxId']) ? $maxId['maxId'] : 0;
  }

  /**
   * @param int|string  $recordId
   * @param mixed|array $fieldList
   * @param bool        $forUpdate
   *
   * @return array|null
   */
  public static function getRecordById($recordId, $fieldList = '*', $forUpdate = false) {
    return static::getRecord(array(static::$_idField . '=' . $recordId), $fieldList, $forUpdate);
  }

  /**
   * @param array $idList
   *
   * @return DbResultIterator
   */
  public static function queryExistsIdInList($idList) {
    if (!empty($idList) && is_array($idList)) {
      $query = static::selectIterator(
        static::buildSelect()
          ->fields(static::$_idField)
          ->where(array("`" . static::$_idField . "` IN (" . implode(',', $idList) . ")"))
      );
    } else {
      $query = new DbEmptyIterator();
    }

    return $query;
  }


  /**
   * @param string $idList
   *
   * @return string
   */
  public static function filterIdListStringRepack($idList) {
    // TODO - remove HelperArray caller
    $idList = HelperArray::stringToArrayFilterEmpty($idList);

    $result = array();
    if (!empty($idList)) {
      foreach(static::queryExistsIdInList($idList) as $row) {
        $result[] = $row[static::$_idField];
      }
    }

    // TODO - remove implode
    return implode(',', $result);
  }

  /**
   *
   */
  public static function lockAllRecords() {
    static::selectIterator(static::buildSelectLock());
  }

  /**
   * Executes prepared statement and returns Iterator
   *
   * @param string|DbQueryConstructor $sql - SQL string or SqlQuery
   *
   * @return DbResultIterator
   */
  protected static function selectIterator($sql) {
    $result = static::$dbStatic->selectIterator($sql, false);
    if(!($result instanceof DbResultIterator)) {
      $result = new DbEmptyIterator();
    }
    return $result;
//    return static::$dbStatic->select(DbSqlPrepare::build($sqlQuery, $values));
  }

}

DBStaticRecord::_init();
