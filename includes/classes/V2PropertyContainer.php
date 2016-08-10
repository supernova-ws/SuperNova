<?php

use \Common\ContainerMagic;
use \Common\IPropertyContainer;

class V2PropertyContainer extends ContainerMagic implements IPropertyContainer {

  /**
   * Property descriptions
   *
   * @var array[]
   */
  protected $properties = array();

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
   * Array of accessors - getters/setters/etc
   *
   * Getter is a callable like
   *    function () use ($that) {}
   *  or Pimple-like (P_CONTAINER_GETTER_PIMPLE)
   *    function ($this) {}
   *
   * Setter is a callable like
   *    function ($value) use ($that) {}
   *
   * Importer is a callable like
   *    function (&$row) use ($this) {}
   *
   * Exporter is a callable like
   *    function (&$row) use ($this) {}
   *
   * @var callable[][]
   */
  protected $accessors;

  public function setProperties($properties) {
    $this->properties = $properties;
  }
  public function setTableName($value) {
    $this->tableName = $value;
  }
  public function setIdField($value) {
    $this->idField = $value;
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
   * @return string
   */
  public function getIdFieldName() {
    return $this->idField;
  }



  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return empty($this->values);
  }

  public function assignAccessor($varName, $type, $callable) {
    if (empty($callable)) {
      return;
    }

    if (is_callable($callable)) {
      $this->accessors[$type][$varName] = $callable;
    } else {
      throw new Exception('Error assigning callable in ' . get_called_class() . '! Callable typed [' . $type . '] is not a callable or not accessible in the scope');
    }
  }

  public function __set($name, $value) {

    if(is_callable($value)) {
      $this->accessors[P_CONTAINER_GETTER_PIMPLE][$name] = $value;
    } elseif (is_callable($this->accessors[P_CONTAINER_SETTER][$name])) {
      call_user_func($this->accessors[P_CONTAINER_SETTER][$name], $value);
    } else {
      $this->values[$name] = $value;
    }
  }

  public function __get($name) {
    if (is_callable($this->accessors[P_CONTAINER_GETTER_PIMPLE][$name])) {
      return call_user_func($this->accessors[P_CONTAINER_GETTER_PIMPLE][$name], $this);
    } elseif (is_callable($this->accessors[P_CONTAINER_GETTER][$name])) {
      return call_user_func($this->accessors[P_CONTAINER_GETTER][$name]);
    } else {
      return $this->values[$name];
    }
  }

  public function importRow($row) {
    // TODO - reset container in more convinient way
    $this->values = array();

    if(empty($row)) {
      return;
    }

    foreach ($this->properties as $propertyName => $propertyData) {
      if (is_callable($this->accessors[P_CONTAINER_IMPORTER][$propertyName])) {
        call_user_func($this->accessors[P_CONTAINER_IMPORTER][$propertyName], &$row);
      } elseif (!empty($propertyData[P_DB_FIELD])) {
        $this->$propertyName = $row[$propertyData[P_DB_FIELD]];
      }
      // Otherwise it's internal field - filled and used internally
    }
  }

  public function exportRow($withDbId = Entity::ENTITY_DB_ID_INCLUDE) {
    $row = array();

    foreach ($this->properties as $propertyName => $propertyData) {
      if (is_callable($this->accessors[P_CONTAINER_EXPORTER][$propertyName])) {
        call_user_func($this->accessors[P_CONTAINER_EXPORTER][$propertyName], &$row);
      } elseif (!empty($propertyData[P_DB_FIELD])) {
        $row[$propertyData[P_DB_FIELD]] = $this->$propertyName;
      }
      // Otherwise it's internal field - filled and used internally
    }

    return $row;
  }

}
