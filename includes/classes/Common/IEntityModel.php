<?php

namespace Common;

use db_mysql;

/**
 * Class EntityModel
 *
 * Describes persistent entity - which can be loaded from/stored to storage
 *
 * @property int|float|string $dbId EntityModel unique ID for entire entities' set
 */
interface IEntityModel {

  /**
   * Returns link to DB used by entity
   *
   * DB can be differ for different entity. For ex. - UNIT EntityModel will use standard DB while AUTH entity would prefer dbAuth
   *
   * @return db_mysql
   */
  public function getDbStatic();

  /**
   * @return \DbRowDirectOperator
   */
  public function getRowOperator();

  /**
   * @param string $value
   */
  public function setTableName($value);

  /**
   * Gets entity's table name
   *
   * @return string
   */
  public function getTableName();

  /**
   * @param string $value
   */
  public function setIdFieldName($value);

  /**
   * Gets entity's DB ID field name (which is unique within entity set)
   *
   * @return string
   */
  public function getIdFieldName();

  /**
   * @param array $array
   *
   * @return \IEntityContainer
   */
  public function fromArray($array);

  /**
   * Exports object properties to DB row state WITHOUT ID
   *
   * Useful for INSERT operations
   *
   * @param \IEntityContainer $cEntity
   * @return array
   */
  public function exportRow($cEntity);

  /**
   * Exports object properties to DB row state with ID
   *
   * @param \IEntityContainer $cEntity
   * @return array
   */
  public function exportRowNoId($cEntity);

  /**
   * @param mixed $dbId
   *
   * @return \EntityContainer|false
   */
  public function loadTry($dbId);

}
