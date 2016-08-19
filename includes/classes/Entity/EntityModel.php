<?php
namespace Entity;

use Entity\EntityContainer;

/**
 * Class Entity\EntityModel
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
  // TODO - remove
  protected $idField = 'id';

  /**
   * Name of exception class that would be thrown
   *
   * Uses for calling when you don't know which exact exception should be called
   * On Entity\EntityModel's children should be used exception class name
   *
   * @var string $exceptionClass
   */
  protected $exceptionClass = 'Entity\EntityException';
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
   * @var \Common\Accessors $accessors
   */
  protected $accessors;


  /**
   * Entity\EntityModel constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
    // Here own rowOperator can be made - if needed to operate other, non-global, DB
    $this->rowOperator = $gc->dbGlobalRowOperator;
    $this->accessors = new \Common\Accessors();
  }

  /**
   * @param \Entity\EntityContainer $that
   * @param string                  $processor
   */
  protected function processRow($that, $processor) {
    foreach ($this->properties as $propertyName => $propertyData) {
      $fieldName = !empty($propertyData[P_DB_FIELD]) ? $propertyData[P_DB_FIELD] : '';
      if ($this->accessors->haveAccessor($propertyName, $processor)) {
        $this->accessors->invokeProcessor($propertyName, $processor, array($that, $propertyName, $fieldName));
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
  public function importRow($cEntity, $row) {
    $cEntity->clear(); // ????????????????????? clearProperties($cEntity)
    $cEntity->row = $row;

    if (is_array($row) && !empty($row)) {
      $this->processRow($cEntity, P_CONTAINER_IMPORT);
    }
  }

  /**
   * @param array $array
   *
   * @return \Entity\EntityContainer
   */
  public function fromArray($array) {
    /**
     * @var EntityContainer $cEntity
     */
    $cEntity = $this->buildContainer();
    $this->importRow($cEntity, $array);

    return $cEntity;
  }

  /**
   * Exports object properties to DB row state with ID
   *
   * @param \Entity\EntityContainer $cEntity
   */
  public function exportRow($cEntity) {
    $cEntity->row = array();
    $this->processRow($cEntity, P_CONTAINER_EXPORT);
  }

  /**
   * @return \Entity\EntityContainer
   */
  public function buildContainer() {
    /**
     * @var \Entity\EntityContainer $container
     */
    $container = new $this->entityContainerClass($this);
//    $container->setProperties($this->properties);
//    $container->setAccessors($this->accessors);

    return $container;
  }


  /**
   * @param int|string $dbId
   *
   * @return \Entity\EntityContainer|false
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
  // TODO - Loaded flag ?????
  public function isNew($cEntity) {
    return $cEntity->isEmpty();
  }


//  /**
//   * Clears only properties which declared in $properties array
//   *
//   * @param Entity\EntityContainer $cEntity
//   */
//  public function clearProperties($cEntity) {
//    foreach ($this->properties as $propertyName => $propertyData) {
//      unset($cEntity->$propertyName);
//    }
//  }


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

//  /**
//   * @param string $value
//   */
//  public function setIdFieldName($value) {
//    $this->idField = $value;
//  }
//  /**
//   * Gets entity's DB ID field name (which is unique within entity set)
//   *
//   * @return string
//   */
//  public function getIdFieldName() {
//    return $this->idField;
//  }

  /**
   * @return array[]
   */
  public function getProperties() {
    return $this->properties;
  }

  /**
   * @param $array
   */
  public function extendProperties($array) {
    $this->properties += $array;
  }

  /**
   * @return \Common\Accessors
   */
  public function getAccessors() {
    return $this->accessors;
  }

}
