<?php
namespace Entity;

use \Common\Accessors;

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
   * Name of exception class that would be thrown
   *
   * Uses for calling when you don't know which exact exception should be called
   * On Entity\EntityModel's children should be used exception class name
   *
   * @var string $exceptionClass
   */
  protected $exceptionClass = 'Entity\EntityException';
  protected $entityContainerClass = '\Entity\EntityContainer';

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
   * @var Accessors $accessors
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
    $this->accessors = new Accessors();
  }

  /**
   * @param EntityContainer $that
   * @param string          $accessor
   */
  protected function processRow($that, $accessor) {
    foreach ($this->properties as $propertyName => $propertyData) {
      $fieldName = !empty($propertyData[P_DB_FIELD]) ? $propertyData[P_DB_FIELD] : '';
      if ($this->accessors->haveAccessor($propertyName, $accessor)) {
        $this->accessors->invokeAccessor($propertyName, $accessor, array($that, $propertyName, $fieldName));
      } elseif ($fieldName) {
        if ($accessor == P_CONTAINER_IMPORT) {
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
    $cEntity->clear();
    if (is_array($row) && !empty($row)) {
      $cEntity->row = $row;
      $this->processRow($cEntity, P_CONTAINER_IMPORT);
    }
  }

  /**
   * @param array $array
   *
   * @return EntityContainer
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
   * @param EntityContainer $cEntity
   */
  public function exportRow($cEntity) {
    $cEntity->row = array();
    $this->processRow($cEntity, P_CONTAINER_EXPORT);
  }

  /**
   * @return EntityContainer
   */
  public function buildContainer() {
    /**
     * @var EntityContainer $container
     */
    $container = new $this->entityContainerClass($this);

    return $container;
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
   * @return Accessors
   */
  public function getAccessors() {
    return $this->accessors;
  }

}
