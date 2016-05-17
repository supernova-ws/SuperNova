<?php

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
   * Converts fields array to string
   * Scalar values or empty arrays would be converted to wildcard '*'
   * null array members would be converted to field NULL
   * All other values would be enquoted by `
   *
   * @param array $fieldList
   *
   * @return string
   *
   * @throws ExceptionDBFieldEmpty
   */
  protected static function fieldsToString($fieldList) {
    $fieldList = HelperArray::makeArray($fieldList);

    $result = array();
    if (!empty($fieldList)) {
      foreach ($fieldList as $fieldName) {
        switch (true) {
          case is_int($fieldName):
            $result[] = $fieldName;
          break;
          case is_null($fieldName):
            $result[] = 'NULL';
          break;
          default:
            $string = (string)$fieldName;
            if ($string == '') {
              throw new ExceptionDBFieldEmpty();
            }
            $result[] = '`' . $string . '`';
        }
      }
    } else {
      $result = array('*');
    }

    $result = implode(',', $result);

    return $result;
  }

  /**
   * @param string $where
   * @param mixed  $fieldList
   *     Field list is scalar it would be converted to array and used as field name
   * @param bool   $for_update
   * @param bool   $returnFirst
   *
   * @return array|null
   */
  protected static function getRecordList($where = '', $fieldList = array(), $for_update = false, $returnFirst = false) {
    $fieldList = static::fieldsToString($fieldList);

    $user_record = null;
    if (!empty($fieldList)) {
      $user_record = static::$dbStatic->doquery(
        (
          "SELECT {$fieldList}" .
          " FROM {{" . static::$_table . "}}" .
          (!empty($where) ? " WHERE {$where}" : '') .
          (!empty($for_update) ? " FOR UPDATE" : '') .
          ($returnFirst ? ' LIMIT 1' : '')
        ),
        $returnFirst
      );
    }

    return !empty($user_record) ? $user_record : null;
  }

  /**
   * @param array       $where
   * @param mixed|array $fieldList
   *     Field list can be scalar - it would be converted to array and used as field name
   * @param bool        $for_update
   *
   * @return array|null
   *
   * @see static::getRecordList
   */
  protected static function getRecord($where = array(), $fieldList = '*', $for_update = false) {
    $maxId = static::$dbStatic->fetchOne(
      DbSqlStatement::build(null, get_called_class())
        ->select()
        ->fields($fieldList)
        ->where($where)
        ->fetchOne()
    );

    return $maxId;
  }

  /**
   * Get maximum ID from table
   *
   * @return int
   */
  public static function getMaxId() {
    $maxId = static::$dbStatic->fetchOne(
      DbSqlStatement::build(null, get_called_class())
        ->select()
        ->fields(new DbSqlLiteral('MAX(id) AS maxId'))
        ->fetchOne()
    );

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
//    return static::getRecord(array(static::$_idField => $recordId), $fieldList, $forUpdate);
    return static::getRecord(array(static::$_idField . '=' . $recordId), $fieldList, $forUpdate);
  }


  /**
   * @param array $idList
   *
   * @return mysqli_result|null
   */
  /**
   * @param array $idList
   *
   * @return mysqli_result|null
   */
  public static function queryExistsIdInList($idList) {
    $query = null;
    if (!empty($idList) && is_array($idList)) {
      $query = static::$dbStatic->execute(
        DbSqlStatement::build(null, get_called_class())
          ->select()
          ->fields(static::$_idField)
          ->where(array("`" . static::$_idField . "` IN (" . implode(',', $idList) . ")"))
      );
    }

    return !empty($query) ? $query : null;
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
      $query = static::queryExistsIdInList($idList);
      while ($row = db_fetch($query)) {
        $result[] = $row[static::$_idField];
      }
    }

    // TODO - remove implode
    return implode(',', $result);
  }

}

DBStaticRecord::_init();
