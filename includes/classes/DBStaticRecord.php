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
  public static function buildDBQ() {
    return DbQueryConstructor::build(static::$dbStatic, get_called_class());
  }

  /**
   * Get maximum ID from table
   *
   * @return int
   */
  public static function getMaxId() {
    $stmt = static::buildDBQ()->fieldMax(static::$_idField);

    return idval(static::$dbStatic->doStmtSelectValue($stmt));
  }

  /**
   * @param int|string  $recordId
   * @param mixed|array $fieldList
   * @param bool   $forUpdate
   *
   * @return array
   */
  // TODO - protected. Code doesn't know about fields
  public static function getRecordById($recordId, $fieldList = '*', $forUpdate = false) {
    $stmt =
      static::buildDBQ()
        ->fields($fieldList)
        ->where(static::$_idField . '=' . $recordId);

    if($forUpdate) {
      $stmt->setForUpdate();
    }

    $result = static::$dbStatic->doStmtSelectRow($stmt);

    return $result;
  }

  /**
   * @param array $idList
   *
   * @return DbResultIterator
   */
  public static function queryExistsIdInList($idList) {
    if (!empty($idList) && is_array($idList)) {
      $query = static::$dbStatic->doStmtSelectIterator(
        static::buildDBQ()
          ->fields(static::$_idField)
          ->where(array("`" . static::$_idField . "` IN (" . implode(',', $idList) . ")"))
      );
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
    static::$dbStatic->doStmtLockAll(static::buildDBQ());
  }

}

DBStaticRecord::_init();
