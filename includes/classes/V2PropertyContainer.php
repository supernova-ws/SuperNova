<?php

use \Common\ContainerMagic;
use \Common\IPropertyContainer;

/**
 * Class V2PropertyContainer
 *
 * Support accessors for properties: getter, setter, unsetter
 *
 * Below $that - is a shortcut for container object which will be passed to accessor
 *
 * Getter is a callable like
 *    function ($this) {}
 *
 * Setter is a callable like
 *    function ($that, $value)  {}
 *
 * Unsetter is a callable like
 *    function ($that) {}
 *
 * Use setDirect() and getDirect() methods to access same variable for setter/getter function!
 * If setter works with other object properties it needs an unsetter to handle clearProperties() method
 *
 */
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
      $this->accessors[$name][P_CONTAINER_GETTER] = $value;
    } elseif (!empty($this->accessors[$name][P_CONTAINER_SETTER]) && is_callable($this->accessors[$name][P_CONTAINER_SETTER])) {
      call_user_func($this->accessors[$name][P_CONTAINER_SETTER], $this, $value);
    } else {
      parent::__set($name, $value);
    }
  }

  public function __get($name) {
    if (
      !empty($this->accessors[$name][P_CONTAINER_GETTER])
      &&
      is_callable($this->accessors[$name][P_CONTAINER_GETTER])
    ) {
      return call_user_func($this->accessors[$name][P_CONTAINER_GETTER], $this);
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
      return call_user_func($this->accessors[$name][P_CONTAINER_UNSETTER], $this);
    } else {
      return parent::__unset($name);
    }
  }

  public function clearProperties() {
    foreach ($this->properties as $propertyName => $propertyData) {
      unset($this->$propertyName);
    }
  }

}
