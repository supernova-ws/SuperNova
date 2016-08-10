<?php

use \Common\GlobalContainer;

/**
 * Class EntityContainer
 *
 * @property int|float $dbId Entity DB ID
 */
class EntityContainer extends V2PropertyContainer implements IEntityContainer {
  const ENTITY_DB_ID_INCLUDE = true;
  const ENTITY_DB_ID_EXCLUDE = false;

  /**
   * @var EntityModel $model
   */
  protected $model;
  protected static $exceptionClass = 'EntityException';
  protected static $modelClass = 'EntityModel';

  /**
   * @var  \Common\GlobalContainer $gc
   */
  protected $gc;
  /**
   * Link to DB which used by this EntityModel
   *
   * @var \db_mysql $dbStatic
   * deprecated - replace with container ID like 'db' or 'dbAuth'
   */
  protected static $dbStatic;
  /**
   * Service to work with rows
   *
   * @var \DbRowDirectOperator $rowOperator
   */
  protected static $rowOperator;
  /**
   * Name of table for this entity
   *
   * @var string $tableName
   */
  protected $tableName = '_table';
  /**
   * Name of key field field in this table
   *
   * @var string $idField
   */
  protected $idField = 'id';
  /**
   * Property list and description
   *
   * propertyName => array(
   *    P_DB_FIELD => 'dbFieldName', - directly converts property to field and vice versa
   * )
   *
   * @var array[] $properties
   */
  protected $properties = array();


  /**
   * BuddyContainer constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct($gc) {
    // TODO - remove. No dependenceon container - we should extract all needed info here
    $this->gc = $gc;
    $this->model = new static::$modelClass($gc);
    static::$dbStatic = $gc->db;
    static::$rowOperator = $gc->dbRowOperator;
  }

  /**
   * @return EntityModel
   */
  public function getModel() {
    return $this->model;
  }

  /**
   * @return \db_mysql
   */
  public function getDbStatic() {
    return static::$dbStatic;
  }

  public function setTableName($value) {
    $this->tableName = $value;
  }

  public function getTableName() {
    return $this->tableName;
  }

  public function setIdField($value) {
    $this->idField = $value;
  }

  public function getIdFieldName() {
    return $this->idField;
  }

  public function importRow($row) {
    $this->clear();

    if (empty($row)) {
      return;
    }

    foreach ($this->properties as $propertyName => $propertyData) {
      if (is_callable($this->accessors[P_CONTAINER_IMPORTER][$propertyName])) {
        call_user_func_array($this->accessors[P_CONTAINER_IMPORTER][$propertyName], array(&$row));
      } elseif (!empty($propertyData[P_DB_FIELD])) {
        $this->$propertyName = $row[$propertyData[P_DB_FIELD]];
      }
      // Otherwise it's internal field - filled and used internally
    }
  }

  protected function exportRow($withDbId = self::ENTITY_DB_ID_INCLUDE) {
    $row = array();
    foreach ($this->properties as $propertyName => $propertyData) {
      if (is_callable($this->accessors[P_CONTAINER_EXPORTER][$propertyName])) {
        call_user_func_array($this->accessors[P_CONTAINER_EXPORTER][$propertyName], array(&$row));
      } elseif (!empty($propertyData[P_DB_FIELD])) {
        $row[$propertyData[P_DB_FIELD]] = $this->$propertyName;
      }
      // Otherwise it's internal field - filled and used internally
    }

    if ($withDbId == self::ENTITY_DB_ID_EXCLUDE) {
      unset($row[$this->getIdFieldName()]);
    }

    return $row;
  }

  /**
   * @return array
   */
  public function exportRowWithoutId() {
    return $this->exportRow(self::ENTITY_DB_ID_EXCLUDE);
  }

  /**
   * @return array
   */
  public function exportRowWithId() {
    return $this->exportRow(self::ENTITY_DB_ID_INCLUDE);
  }

  // TODO - load from self DB
  public function loadTry() {
    $row = static::$rowOperator->getById($this);

    if (empty($row)) {
      $this->dbId = 0;

      return false;
    } else {
      $this->importRow($row);
    }

    return true;
  }

  public function isEmpty() {
    // TODO - empty container - only properties
    return empty($this->dbId);
  }

  public function isNew() {
    return empty($this->dbId);
  }

  public function insert(){
    static::$rowOperator->insert($this);
  }

  public function delete(){
    static::$rowOperator->deleteById($this);
  }

}
