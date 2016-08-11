<?php

use \Common\GlobalContainer;

/**
 * Class EntityContainer
 *
 * Support export/import accessors
 *
 * Importer is a callable like
 *    function ($that, &$row[, $propertyName[, $fieldName]]) {}
 *
 * Exporter is a callable like
 *    function ($that, &$row[, $propertyName[, $fieldName]]) {}
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
//  protected $properties = array();


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

  /**
   * @param array  $row
   * @param string $processor
   */
  protected function processRow(&$row, $processor) {
    foreach ($this->properties as $propertyName => $propertyData) {
      $fieldName = !empty($propertyData[P_DB_FIELD]) ? $propertyData[P_DB_FIELD] : '';
      if (
        !empty($this->accessors[$propertyName][$processor])
        &&
        is_callable($this->accessors[$propertyName][$processor])
      ) {
        call_user_func_array($this->accessors[$propertyName][$processor], array($this, &$row, $propertyName, $fieldName));
      } elseif ($fieldName) {
        if($processor == P_CONTAINER_IMPORT) {
          $this->$propertyName = $row[$fieldName];
        } else {
          $row[$fieldName] = $this->$propertyName;
        }
      }
      // Otherwise it's internal field - filled and used internally
    }

  }

  public function importRow($row) {
    $this->clearProperties();

    if (empty($row)) {
      return true;
    }

    $this->processRow($row, P_CONTAINER_IMPORT);

    return true;
  }

  /**
   * @return array
   */
  public function exportRow() {
    $row = array();
    $this->processRow($row, P_CONTAINER_EXPORT);

    return $row;
  }

  /**
   * @return array
   */
  public function exportRowNoId() {
    $row = $this->exportRow();

    unset($row[$this->getIdFieldName()]);

    return $row;
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

  public function insert() {
    static::$rowOperator->insert($this);
  }

  public function delete() {
    static::$rowOperator->deleteById($this);
  }

}
