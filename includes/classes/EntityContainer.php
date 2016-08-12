<?php

use \Common\GlobalContainer;

/**
 * Class EntityContainer
 *
 * Introduces linked models and export/import operations
 *
 * Importer is a callable like
 *    function ($that, &$row[, $propertyName[, $fieldName]]) {}
 *
 * Exporter is a callable like
 *    function ($that, &$row[, $propertyName[, $fieldName]]) {}
 *
 * @property int|float $dbId Entity DB ID
 */
class EntityContainer extends ContainerAccessors implements IEntityContainer {
  protected static $exceptionClass = 'EntityException';

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


//  /**
//   * @var  \Common\GlobalContainer $gc
//   */
//  protected $gc;


  /**
   * BuddyContainer constructor.
   *
   */
  public function __construct() {
    // TODO - remove. No dependenceon container - we should extract all needed info here
//    $this->gc = $gc;
//    $this->model = new static::$modelClass($gc);
//    static::$dbStatic = $gc->db;
//    static::$rowOperator = $gc->dbRowOperator;
  }

  public function setProperties($properties) {
    $this->properties = $properties;
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

  public function isEmpty() {
    // TODO - empty container - only properties
    return empty($this->dbId);
  }

  public function isNew() {
    return empty($this->dbId);
  }

  public function clearProperties() {
    foreach ($this->properties as $propertyName => $propertyData) {
      unset($this->$propertyName);
    }
  }

}
