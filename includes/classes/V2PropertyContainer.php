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

  // TODO - batch assign
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
      $this->accessors[$name][P_CONTAINER_GETTER_PIMPLE] = $value;
    } elseif (is_callable($this->accessors[$name][P_CONTAINER_SETTER])) {
      call_user_func($this->accessors[$name][P_CONTAINER_SETTER], $value);
    } else {
      $this->values[$name] = $value;
    }
  }

  public function __get($name) {
    if (is_callable($this->accessors[$name][P_CONTAINER_GETTER_PIMPLE])) {
      return call_user_func($this->accessors[$name][P_CONTAINER_GETTER_PIMPLE], $this);
    } elseif (is_callable($this->accessors[$name][P_CONTAINER_GETTER])) {
      return call_user_func($this->accessors[$name][P_CONTAINER_GETTER]);
    } else {
      return $this->values[$name];
    }
  }

  public function __isset($name) {
    // TODO - or here already can isset($this->name) ????
    $value = $this->$name;
    return isset($value);
  }

  public function clearProperties() {
    foreach ($this->properties as $propertyName => $propertyData) {
      unset($this->values[$propertyName]);
    }
  }

}
