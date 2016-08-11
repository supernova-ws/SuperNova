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
   * Unsetter is a callable like
   *    function () use ($that) {}
   *
   * Use setDirect() and getDirect() methods to access same variable for setter/getter function!
   * If setter works with other object properties it needs an unsetter to handle clearProperties() method
   *
   *
   * Importer is a callable like
   *    function (&$row) use ($this) {}
   *
   * Exporter is a callable like
   *    function (&$row) use ($this) {}
   *
   * @var callable[][]
   */
  protected $accessors = array();

  public function setProperties($properties) {
    $this->properties = $properties;
  }

  public function setDirect($name, $value) {
    parent::__set($name, $value);
  }

  public function assignAccessor($varName, $type, $callable) {
    if (empty($callable)) {
      return;
    }

    if (is_callable($callable)) {
      $this->accessors[$varName][$type] = $callable;
    } else {
      throw new Exception('Error assigning callable in ' . get_called_class() . '! Callable typed [' . $type . '] is not a callable or not accessible in the scope');
    }
  }

  public function __set($name, $value) {
    if(is_callable($value)) {
      $this->accessors[$name][P_CONTAINER_GETTER_PIMPLE] = $value;
    } elseif (!empty($this->accessors[$name][P_CONTAINER_SETTER]) && is_callable($this->accessors[$name][P_CONTAINER_SETTER])) {
      call_user_func($this->accessors[$name][P_CONTAINER_SETTER], $value);
    } else {
      parent::__set($name, $value);
    }
  }

  public function __get($name) {
    if (
      !empty($this->accessors[$name][P_CONTAINER_GETTER_PIMPLE])
      &&
      is_callable($this->accessors[$name][P_CONTAINER_GETTER_PIMPLE])
    ) {
      return call_user_func($this->accessors[$name][P_CONTAINER_GETTER_PIMPLE], $this);
    } elseif (
      !empty($this->accessors[$name][P_CONTAINER_GETTER])
      &&
      is_callable($this->accessors[$name][P_CONTAINER_GETTER])
    ) {
      return call_user_func($this->accessors[$name][P_CONTAINER_GETTER]);
    } else {
      return parent::__get($name);
    }
  }

  public function __isset($name) {
    // TODO - or here already can isset($this->name) ????
    $value = $this->$name;
    return isset($value);
  }

  public function __unset($name) {
    if (
      !empty($this->accessors[$name][P_CONTAINER_UNSETTER])
      &&
      is_callable($this->accessors[$name][P_CONTAINER_UNSETTER])
    ) {
      return call_user_func($this->accessors[$name][P_CONTAINER_UNSETTER]);
    } else {
      return parent::__unset($name);
    }
  }

  public function clearProperties() {
    foreach ($this->properties as $propertyName => $propertyData) {
//      unset($this->values[$propertyName]);
      unset($this->$propertyName);
    }
  }

}
