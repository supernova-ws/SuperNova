<?php
/**
 * Created by Gorlum 10.08.2016 18:05
 */

/**
 * Created by Gorlum 10.08.2016 12:40
 */
interface IEntityContainer {
  /**
   * @return \db_mysql
   */
  public function getDbStatic();

  /**
   * Gets entity's table name
   *
   * @return string
   */
  public function getTableName();

  public function setTableName($value);

  /**
   * Gets entity's DB ID field name (which is unique within entity set)
   *
   * @return string
   */
  public function getIdFieldName();

  public function setIdField($value);


  /**
   * BuddyContainer constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc);

  /**
   * Import DB row state into object properties
   *
   * @param array $row
   */
  public function importRow($row);

  /**
   * Exports object properties to DB row state with ID
   *
   * @return array
   */
  public function exportRowWithoutId();

  /**
   * Exports object properties to DB row state WITHOUT ID
   *
   * Useful for INSERT operations
   *
   * @return array
   */
  public function exportRowWithId();

  /**
   * Trying to load object info by buddy ID - if it is supplied
   *
   * @return bool
   */
  public function loadTry();

  public function isEmpty();

  public function isNew();
}