<?php

/**
 * Class EntityModel
 *
 * This class have only one instance - i.e. is a service
 * Describes persistent entity - which can be loaded from/stored to storage
 *
 *
 * Introduces linked models and export/import operations
 *
 * Importer is a callable like
 *    function ($that, &$row[, $propertyName[, $fieldName]]) {}
 *
 * Exporter is a callable like
 *    function ($that, &$row[, $propertyName[, $fieldName]]) {}
 *
 *
 * @property int|float|string $dbId EntityModel unique ID for entire entities' set
 */
class EntityModel {
  /**
   * Service to work with rows
   *
   * ALL DB ACCESS SHOULD BE DONE VIA ROW OPERATOR! NO DIRECT ACCESS TO DB IS ALLOWED!
   *
   * @var \DbRowDirectOperator $rowOperator
   */
  protected $rowOperator;
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
   * Name of exception class that would be thrown
   *
   * Uses for calling when you don't know which exact exception should be called
   * On EntityModel's children should be used exception class name
   *
   * @var string $exceptionClass
   */
  protected $exceptionClass = 'EntityException';
  protected $entityContainerClass = '\EntityContainer';

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
   * Array of accessors - getters/setters/etc
   *
   * @var callable[][]
   */
  protected $accessors = array();

  public function setAccessor($varName, $processor, $callable) {
    if (empty($callable)) {
      return;
    }

    if (is_callable($callable)) {
      $this->accessors[$varName][$processor] = $callable;
    } else {
      throw new \Exception('Error assigning callable in ' . get_called_class() . '! Callable typed [' . $processor . '] is not a callable or not accessible in the scope');
    }
  }

  /**
   * @param $varName
   * @param $processor
   *
   * @return callable|null
   */
  public function getAccessor($varName, $processor) {
    return isset($this->accessors[$varName][$processor]) ? $this->accessors[$varName][$processor] : null;
  }

  /**
   * EntityModel constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
    // Here own rowOperator can be made - if needed to operate other, non-global, DB
    $this->rowOperator = $gc->dbGlobalRowOperator;
  }

  /**
   * @return \DbRowDirectOperator
   */
  public function getRowOperator() {
    return $this->rowOperator;
  }

  /**
   * @param string $value
   */
  public function setTableName($value) {
    $this->tableName = $value;
  }

  /**
   * Gets entity's table name
   *
   * @return string
   */
  public function getTableName() {
    return $this->tableName;
  }

  /**
   * @param string $value
   */
  public function setIdFieldName($value) {
    $this->idField = $value;
  }

  /**
   * Gets entity's DB ID field name (which is unique within entity set)
   *
   * @return string
   */
  public function getIdFieldName() {
    return $this->idField;
  }


  /**
   * @param \EntityContainer $that
   * @param string           $processor
   */
  protected function processRow($that, $processor) {
    foreach ($this->properties as $propertyName => $propertyData) {
      $fieldName = !empty($propertyData[P_DB_FIELD]) ? $propertyData[P_DB_FIELD] : '';
      if (isset($this->accessors[$propertyName][$processor])) {
        call_user_func_array($this->accessors[$propertyName][$processor], array($that, $propertyName, $fieldName));
      } elseif ($fieldName) {
        if ($processor == P_CONTAINER_IMPORT) {
          $that->$propertyName = isset($that->row[$fieldName]) ? $that->row[$fieldName] : null;
        } else {
          $that->row += array($fieldName => $that->$propertyName);
        }
      }
      // Otherwise it's internal field - filled and used internally
    }
  }

  /**
   * Import DB row state into object properties
   *
   * @param EntityContainer $cEntity
   * @param array           $row
   */
  protected function importRow($cEntity, $row) {
//    $this->clearProperties($cEntity);
    $cEntity->clear();
    $cEntity->row = $row;

    if (is_array($row) && !empty($row)) {
      $this->processRow($cEntity, P_CONTAINER_IMPORT);
    }
  }

  /**
   * @param array $array
   *
   * @return \EntityContainer
   */
  public function fromArray($array) {
    /**
     * @var EntityContainer $cEntity
     */
    $cEntity = $this->getContainer();
    $this->importRow($cEntity, $array);

    return $cEntity;
  }


  /**
   * Exports object properties to DB row state with ID
   *
   * @param \EntityContainer $cEntity
   */
  public function exportRow($cEntity) {
    $cEntity->row = array();
    $this->processRow($cEntity, P_CONTAINER_EXPORT);
  }

  /**
   * Exports object properties to DB row state WITHOUT ID
   *
   * Useful for INSERT operations
   *
   * @param \EntityContainer $cEntity
   */
  protected function exportRowNoId($cEntity) {
    $this->exportRow($cEntity);

    if ($this->getIdFieldName() != '') {
      unset($cEntity->row[$this->getIdFieldName()]);
    }
  }

  /**
   * @return \EntityContainer
   */
  public function getContainer() {
    /**
     * @var \EntityContainer $container
     */
    $container = new $this->entityContainerClass($this);
//    $container->setProperties($this->properties);
//    $container->setAccessors($this->accessors);

    return $container;
  }


  /**
   * @param int|string $dbId
   *
   * @return \EntityContainer|false
   */
  public function loadById($dbId) {
    $row = $this->rowOperator->getById($this, $dbId);
    if (empty($row)) {
      return false;
    } else {
      $cEntity = $this->fromArray($row);
    }

    return $cEntity;
  }

  /**
   * @param EntityContainer $cEntity
   *
   * @return bool
   */
  public function isEmpty($cEntity) {
    return $cEntity->isEmpty();
  }

  /**
   * @param EntityContainer $cEntity
   *
   * @return bool
   */
  public function isNew($cEntity) {
    return $cEntity->isEmpty();
  }

//  /**
//   * Clears only properties which declared in $properties array
//   *
//   * @param EntityContainer $cEntity
//   */
//  public function clearProperties($cEntity) {
//    foreach ($this->properties as $propertyName => $propertyData) {
//      unset($cEntity->$propertyName);
//    }
//  }

}
