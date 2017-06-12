<?php
/**
 * Created by Gorlum 12.06.2017 14:41
 */

namespace DBAL;


/**
 * Class ActiveRecord
 * @package DBAL
 */

abstract class ActiveRecord {
  const TABLE = 'table_';
  const TABLE_LENGTH = 6;

  protected static $_primaryIndexField = '';

  /**
   * @return string
   */
  public static function tableName() {
    $tableName = \HelperString::camelToUnderscore(basename(get_called_class()));
    if(strpos($tableName, self::TABLE) === 0) {
      $tableName = substr($tableName, self::TABLE_LENGTH);
    }
    return $tableName;
  }

  /**
   * @return \db_mysql
   */
  public static function db() {
    return \classSupernova::$db;
  }



}
