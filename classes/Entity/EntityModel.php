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

  protected $newProperties = array();

  /**
   * Entity\EntityModel constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {
    // Here own rowOperator can be made - if needed to operate other, non-global, DB
    $this->rowOperator = $gc->dbGlobalRowOperator;
    $this->accessors = new Accessors();

    if (property_exists($this, 'newProperties') && !empty($this->newProperties)) {
      $this->extendProperties($this->newProperties);
    }
  }

  /**
   * @param EntityContainer $that
   * @param string          $accessor
   */
  protected function processRow($that, $accessor) {
    foreach ($this->properties as $propertyName => $propertyData) {
      $fieldName = !empty($propertyData[P_DB_FIELD]) ? $propertyData[P_DB_FIELD] : '';
      if ($this->accessors->exists($accessor, $propertyName)) {
        $this->accessors->execute($accessor, $propertyName, array($that, $propertyName, $fieldName));
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
   * Exports object properties to DB row state with ID
   *
   * @param EntityContainer $cEntity
   */
  public function exportRow($cEntity) {
    $cEntity->row = array();
    $this->processRow($cEntity, P_CONTAINER_EXPORT);
  }

  /**
   * @param array $array
   *
   * @return EntityContainer
   */
  public function fromArray($array) {
    $cEntity = $this->buildContainer();
    $this->importRow($cEntity, $array);

    return $cEntity;
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

  //
  // Save/load methods =================================================================================================

  /**
   * @param array $filter
   *
   * @return EntityContainer|false
   */
  public function load($filter) {
    $cEntity = false;
    $cEntity = $this->buildContainer();

    return $cEntity;
  }

  /**
   * @param EntityContainer $cEntity
   */
  protected function insert($cEntity) {
    $this->rowOperator->insert($this, $this->exportRow($cEntity));
    // TODO - re-read record
  }

  /**
   * @param EntityContainer $cEntity
   *
   * @throws \Exception
   */
  protected function update($cEntity) {
    // TODO - separate real changes from internal ones
    // Generate changeset row
    // Foreach all rows. If there is change and no delta - then put delta. Otherwise put change
    // If row not empty - update
    throw new \Exception(__CLASS__ . '::update() in ' . get_called_class() . 'is not yet implemented');
  }

  /**
   * @param EntityContainer $cEntity
   *
   * @throws \Exception
   */
  protected function delete($cEntity) {
    throw new \Exception(__CLASS__ . '::delete() in ' . get_called_class() . 'is not yet implemented');
  }

  /**
   * Method is called when trying to save DB_RECORD_LOADED but unchanged container
   *
   * Generally in this case no need in DB operations
   * If any entity require to save empty data (for updating timestamp for ex.) it should override this method
   *
   * @param EntityContainer $cEntity
   *
   * @throws \Exception
   */
  protected function onSaveUnchanged($cEntity) {
    // TODO - or just save nothing ?????
//    throw new \Exception('EntityModel isNotEmpty, have dbId and not CHANGED! It can\'t be!');
    throw new \Exception(__CLASS__ . '::unchanged() in ' . get_called_class() . 'is not yet implemented');
  }

  /**
   * Method is called when trying to save newly created DB_RECORD_NEW and Empty container
   *
   * If it is needed to really save empty container (for log purposes, for ex.) child should override this method
   *
   * @param EntityContainer $cEntity
   *
   * @throws \Exception
   */
  protected function onSaveNew($cEntity) {
    // Just created container and doesn't use it
//    throw new \Exception('EntityModel isEmpty but not loaded! It can\'t be!');
    throw new \Exception(__CLASS__ . '::emptyAction() in ' . get_called_class() . 'is not yet implemented');
  }

  /**
   * Saves entity to DB
   *
   * @param EntityContainer $cEntity
   */
  protected function save($cEntity) {
    if ($this->isEmpty($cEntity)) {
      if ($cEntity->isLoaded) {
        $this->delete($cEntity);
      } else {
        $this->onSaveNew($cEntity);
      }
    } else {
      if (empty($cEntity->dbId)) {
        $this->insert($cEntity);
      } elseif (method_exists($cEntity, 'isChanged') && $cEntity->isChanged()) {
        $this->update($cEntity);
      } else {
        $this->onSaveUnchanged($cEntity);
      }
    }
  }


  //
  // Protected properties accessors ====================================================================================

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
