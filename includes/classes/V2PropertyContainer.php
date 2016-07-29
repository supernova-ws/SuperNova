<?php

use \Common\IMagicProperties;
use \Common\IPropertyContainer;

class V2PropertyContainer implements IMagicProperties, IPropertyContainer {

  /**
   * Property descriptions
   *
   * @var array[]
   */
  protected $properties = array();

  /**
   * Property values
   *
   * @var array
   */
  protected $values = array();
  /**
   * Array of getters
   *
   * Getter is a callable like
   *    function ($value) use ($that) {}
   *
   * @var callable[]
   */
  protected $setters = array();
  /**
   * Array of setters
   *
   * Setter is a callable like
   *    function () use ($that) {}
   *
   * @var callable[]
   */
  protected $getters = array();
  /**
   * Array of importers
   *
   * Importer is a callable like
   *    function (&$row) use ($this) {}
   *
   * @var callable[]
   */
  protected $importers;
  /**
   * Array of exporters
   *
   * Exporter is a callable like
   *    function (&$row) use ($this) {}
   *
   * @var callable[]
   */
  protected $exporters = array();

  /**
   * Magic checker for property set
   *
   * @param string $name
   *
   * @return boolean
   */
  public function __isset($name) {
    return isset($this->values[$name]);
  }

  /**
   * Magic un-setter
   *
   * @param string $name
   */
  public function __unset($name) {
    unset($this->values[$name]);
  }

  public function __set($name, $value) {
    if (is_callable($this->setters[$name])) {
      call_user_func($this->setters[$name], $value);
    } else {
      $this->values[$name] = $value;
    }
  }

  public function __get($name) {
    if (is_callable($this->getters[$name])) {
      return call_user_func($this->getters[$name]);
    } else {
      return $this->values[$name];
    }
  }

  public function importRow($row) {
    foreach ($this->properties as $propertyName => $propertyData) {
      if (is_callable($this->importers[$propertyName])) {
        call_user_func($this->importers[$propertyName], &$row);
      } elseif (!empty($propertyData[P_DB_FIELD])) {
        $this->$propertyName = $row[$propertyData[P_DB_FIELD]];
      }
      // Otherwise it's internal field - filled and used internally
    }
  }

  public function exportRow() {
    $row = array();

    foreach ($this->properties as $propertyName => $propertyData) {
      if (is_callable($this->exporters[$propertyName])) {
        call_user_func($this->exporters[$propertyName], &$row);
      } elseif (!empty($propertyData[P_DB_FIELD])) {
        $row[$propertyData[P_DB_FIELD]] = $this->$propertyName;
      }
      // Otherwise it's internal field - filled and used internally
    }

    return $row;
  }

  public function setProperties($properties) {
    $this->properties = $properties;
  }

  public function assignAccessor($varName, $type, $callable) {
    if (empty($callable)) {
      return;
    }

    if (is_callable($callable)) {
      $this->{$type}[$varName] = $callable;
    } else {
      throw new Exception('Error assigning callable in ' . get_called_class() . '! Callable typed [' . $type . '] is not a callable or not accessible in the scope');
    }
  }


  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return empty($this->values);
  }

}
