<?php

class DBStaticRecord {

  public static $_table = '_table';
  public static $_idField = 'id';

  /**
   * Converts fields array to string
   * Scalar values or empty arrays would be converted to wildcard '*"
   * null array members would be converted to field NULL
   * All other values would be enquoted by `
   *
   * @param array $fieldList
   *
   * @return string
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
            $result[] = '`' . (string)$fieldName . '`';
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
      $user_record = doquery(
        (
          "SELECT {$fieldList}" .
          " FROM {{" . self::$_table . "}}" .
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
   * @param string $where
   * @param mixed  $fieldList
   *     Field list can be scalar - it would be converted to array and used as field name
   * @param bool   $for_update
   *
   * @return array|null
   *
   * @see static::getRecordList
   */
  protected static function getRecord($where = '', $fieldList = array(), $for_update = false) {
    return static::getRecordList($where, $fieldList, $for_update, true);
  }

  /**
   * Get maximum ID from table
   *
   * @return int
   */
  public static function getMaxId() {
    $maxId = classSupernova::$db->doquery("SELECT MAX(`" . static::$_idField . "`) AS `maxId` FROM `{{" . static::$_table . "}}`", true);

    return !empty($maxId['maxId']) ? $maxId['maxId'] : 0;
  }

  /**
   * @param int|string $user_id
   * @param array      $fieldList
   * @param bool       $for_update
   *
   * @return array|null
   */
  public static function getRecordById($user_id, $fieldList = array(), $for_update = false) {
    return static::getRecord(static::$_idField . ' = ' . $user_id, $fieldList, $for_update);
  }


  /**
   * @param array $idList
   *
   * @return mysqli_result|null
   */
  public static function queryExistsIdInList($idList) {
    $query = null;
    if (!empty($idList)) {
      $query = doquery("SELECT `" . static::$_idField . "` FROM `{{" . static::$_table . "}}` WHERE `" . static::$_idField . "` IN (" . implode(',', $idList) . ")");
    }

    return !empty($query) ? $query : null;
  }


  /**
   * @param string $idList
   *
   * @return string
   */
  public static function filterIdListStringRepack($idList) {
    $idList = HelperArray::stringToArrayFilterEmpty($idList);

    $result = array();
    if (!empty($idList)) {
      $query = static::queryExistsIdInList($idList);
      while ($row = db_fetch($query)) {
        $result[] = $row[static::$_idField];
      }
    }

    return implode(',', $result);
  }

}
