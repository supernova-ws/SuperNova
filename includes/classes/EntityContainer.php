<?php

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
 */
class EntityContainer extends ContainerAccessors {
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
   * Set properties data from external source
   *
   * @param array $properties
   */
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
        if ($processor == P_CONTAINER_IMPORT) {
          $this->$propertyName = $row[$fieldName];
        } else {
          $row[$fieldName] = $this->$propertyName;
        }
      }
      // Otherwise it's internal field - filled and used internally
    }

  }

  /**
   * Import DB row state into object properties
   *
   * @param array $row
   */
  public function importRow($row) {
    $this->clearProperties();

    if (is_array($row) && !empty($row)) {
      $this->processRow($row, P_CONTAINER_IMPORT);
    }

    return true;
  }

  /**
   * Exports object properties to DB row state WITHOUT ID
   *
   * Useful for INSERT operations
   *
   * @return array
   */
  public function exportRow() {
    $row = array();
    $this->processRow($row, P_CONTAINER_EXPORT);

    return $row;
  }

  /**
   * Clears only properties which declared in $properties array
   */
  public function clearProperties() {
    foreach ($this->properties as $propertyName => $propertyData) {
      unset($this->$propertyName);
    }
  }

}
