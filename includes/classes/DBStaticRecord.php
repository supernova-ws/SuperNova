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

//  /**
//   * DBStaticRecord constructor.
//   *
//   * @param db_mysql|null $db
//   */
//  public static function _init($db = null) {
//    static::$dbStatic = (!empty($db) && $db instanceof db_mysql) || !class_exists('classSupernova', false) ? $db : classSupernova::$db;
//  }

  /**
   * @param db_mysql $db
   *
   */
  public static function setDb($db) {
    static::$dbStatic = $db;
  }

  /**
   * @return db_mysql
   *
   */
  public static function getDb() {
    return (!empty(static::$dbStatic) && static::$dbStatic instanceof db_mysql) || !class_exists('classSupernova', false) ? static::$dbStatic : classSupernova::$db;
  }

  /**
   * @param string|DBStaticRecord $DBStaticRecord
   *
   * @return DbQueryConstructor
   */
  public static function buildDBQ($DBStaticRecord = '') {
    $dbClassName = empty($DBStaticRecord) ? get_called_class() : $DBStaticRecord;
    $dbClassName = is_string($dbClassName) ? $dbClassName : get_class($dbClassName);

    return DbQueryConstructor::build($dbClassName::getDb(), $dbClassName);
  }

  /**
   * Get maximum ID from table
   *
   * @return int
   */
  public static function getMaxId() {
    $stmt = static::buildDBQ()->fieldMax(static::$_idField)->selectValue();

    return idval($stmt);
  }

//  /**
//   * @param string|DBStaticRecord $DBStaticRecord
//   */
//  // TODO - IoC test
//  public static function getMax($DBStaticRecord) {
//    $result = static::buildDBQ($DBStaticRecord)->fieldMax($DBStaticRecord::$_idField)->selectValue();
//
//    return idval($result);
//  }

  /**
   * @param string|DBStaticRecord $DBStaticRecord
   * @param int|string            $recordId
   * @param mixed|array           $fieldList
   * @param bool                  $forUpdate
   *
   * @return array
   */
  // TODO - protected. Code doesn't know about fields
  public static function getRecordById($recordId, $fieldList = '*', $forUpdate = false) {
    $stmt =
      static::buildDBQ()
        ->fields($fieldList)
        ->where(static::$_idField . '=' . $recordId);

    if ($forUpdate) {
      $stmt->setForUpdate();
    }

    $result = $stmt->selectRow();

    return $result;
  }

  /**
   * @param array $idList
   *
   * @return DbResultIterator
   */
  public static function queryExistsIdInList($idList) {
    if (!empty($idList) && is_array($idList)) {
      $query =
        static::buildDBQ()
          ->fields(static::$_idField)
          ->where(array("`" . static::$_idField . "` IN (" . implode(',', $idList) . ")"))
          ->selectIterator();
    } else {
      $query = new DbEmptyIterator();
    }

    return $query;
  }


  /**
   * Filter list of ID by only existed IDs in table
   *
   * @param string $idList
   *
   * @return string
   */
  public static function filterIdListStringRepack($idList) {
    // TODO - remove HelperArray caller
    $idList = HelperArray::stringToArrayFilterEmpty($idList);

    $result = array();
    if (!empty($idList)) {
      foreach (static::queryExistsIdInList($idList) as $row) {
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
    static::getDb()->doStmtLockAll(static::buildDBQ());
  }

}

//DBStaticRecord::_init();
