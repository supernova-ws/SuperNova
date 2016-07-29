<?php

namespace Common;

use db_mysql;

/**
 * Class Entity
 *
 * Describes persistent entity - which can be loaded from/stored to storage
 *
 * @property int|float|string $dbId Entity unique ID for entire entities' set
 */
interface IEntity {

  /**
   * Returns link to DB used by entity
   *
   * DB can be differ for different entity. For ex. - UNIT Entity will use standard DB while AUTH entity would prefer dbAuth
   *
   * @return db_mysql
   */
  public function getDbStatic();

  /**
   * Gets entity's table name
   *
   * @return string
   */
  public function getTableName();

  /**
   * Gets entity's DB ID field name (which is unique within entity set)
   *
   * @return string
   */
  public function getIdFieldName();

  /**
   * Return state of container
   *
   * Used to determine operation on save() - delete() on empty Entity or insert()/update() on non-empty Entity
   *
   * @return bool
   */
  public function isContainerEmpty();

  /**
   * Is it a new entity?
   *
   * Used to distinguish which operation should be used for save() - insert() or update()
   *
   * @return bool
   */
  public function isNew();

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

}
