<?php

/**
 * Class Entity
 *
 * @property int|float $dbId Buddy record DB ID
 */
class Entity implements \Common\IMagicAccess, \Common\IEntity {
  const ENTITY_DB_ID_INCLUDE = true;
  const ENTITY_DB_ID_EXCLUDE = false;

  /**
   * Link to DB which used by this Entity
   *
   * @var db_mysql $dbStatic
   * deprecated - replace with container ID like 'db' or 'dbAuth'
   */
  protected static $dbStatic;
  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected static $tableName = '_table';
  /**
   * Name of key field field in this table
   *
   * @var string $idField
   */
  protected static $idField = 'id';

  /**
   * Name of exception class that would be thrown
   *
   * Uses for calling when you don't know which exact exception should be called
   * On Entity's children should be used exception class name
   *
   * @var string $exceptionClass
   */
  protected static $exceptionClass = 'EntityException';

  /**
   * Container for property values
   *
   * @var \Common\IPropertyContainer $_container
   */
  protected $_container;
  protected static $_containerName = 'V2PropertyContainer';

  /**
   * Property list and description
   *
   * propertyName => array(
   *    P_DB_FIELD => 'dbFieldName', - directly converts property to field and vice versa
   * )
   *
   * @var array[]
   */
  protected static $_properties = array();

  /**
   * Service to work with rows
   *
   * @var \DbRowDirectOperator $rowOperator
   */
  protected static $rowOperator;

  /**
   * Entity constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
    empty(static::$dbStatic) && !empty($gc->db) ? static::$dbStatic = $gc->db : false;
    static::$rowOperator = $gc->dbRowOperator;

    $this->_container = new static::$_containerName();
    $this->_container->setProperties(static::$_properties);
  }

  /**
   * @return array[]
   */
  public function getProperties() {
    return static::$_properties;
  }

  /**
   * Import DB row state into object properties
   *
   * @param array $row
   */
  public function importRow($row) {
    $this->_container->importRow($row);
  }

  /**
   * Export data from object properties into DB row for further use
   *
   * @param bool $withDbId - Should dbId too be returned. Useful for INSERT statements
   *
   * @return array
   */
  protected function exportDbRow($withDbId = self::ENTITY_DB_ID_INCLUDE) {
    $row = $this->_container->exportRow();

    if ($withDbId == self::ENTITY_DB_ID_EXCLUDE) {
      unset($row[$this->getIdFieldName()]);
    }

    return $row;
  }

  /**
   * @return array
   */
  public function exportRowWithoutId() {
    return $this->exportDbRow(self::ENTITY_DB_ID_EXCLUDE);
  }

  /**
   * @return array
   */
  public function exportRowWithId() {
    return $this->exportDbRow(self::ENTITY_DB_ID_INCLUDE);
  }

  /**
   * Trying to load object info by buddy ID - if it is supplied
   *
   * @param int|float|string $dbId
   *
   * @return bool
   */
  protected function loadTry($dbId) {
    $this->dbId = $dbId;
    $row = static::$rowOperator->getById($this);

    if (empty($row)) {
      $this->dbId = 0;

      return false;
    } else {
      $this->importRow($row);
    }

    return true;
  }

  /**
   * Gets entity's table name
   *
   * @return string
   */
  public function getTableName() {
    return static::$tableName;
  }

  /**
   * @return string
   */
  public function getIdFieldName() {
    return static::$idField;
  }

  /**
   * @return bool
   */
  public function isContainerEmpty() {
    return $this->_container->isEmpty();
  }

  /**
   * @return bool
   */
  public function isNew() {
    return empty($this->dbId);
  }

  /**
   * @return db_mysql
   */
  public function getDbStatic() {
    return static::$dbStatic;
  }

  public function __get($name) {
    return $this->_container->$name;
  }

  public function __set($name, $value) {
    $this->_container->$name = $value;
  }

  public function __isset($name) {
    return isset($this->_container->$name);
  }

  public function __unset($name) {
    unset($this->_container->$name);
  }

}
