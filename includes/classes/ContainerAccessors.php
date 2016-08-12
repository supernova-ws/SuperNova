<?php

use \Common\ContainerMagic;
use \Common\IContainerAccessors;

/**
 * Class ContainerAccessors
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
class ContainerAccessors extends ContainerMagic implements IContainerAccessors {
  /**
   * Array of accessors - getters/setters/etc
   *
   * @var callable[][]
   */
  protected $accessors = array();

  public function setDirect($name, $value) {
    parent::__set($name, $value);
  }

  public function getDirect($name) {
    parent::__get($name);
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

  /**
   * Performs $processor operation on property with specified name
   *
   * @param string     $processor
   * @param string     $name
   * @param null|mixed $value
   *
   * @return mixed
   */
  protected function performMagic($processor, $name, $value = null) {
    if (
      !empty($this->accessors[$name][$processor])
      &&
      is_callable($this->accessors[$name][$processor])
    ) {
      return call_user_func($this->accessors[$name][$processor], $this, $value);
    } else {
      return parent::$processor($name, $value);
    }
  }

  public function __set($name, $value) {
    if (is_callable($value)) {
      $this->accessors[$name][P_CONTAINER_GET] = $value;
    } else {
      $this->performMagic(P_CONTAINER_SET, $name, $value);
    }
  }

  public function __get($name) {
    return $this->performMagic(P_CONTAINER_GET, $name, null);
  }

  public function __unset($name) {
    $this->performMagic(P_CONTAINER_UNSET, $name, null);
  }

  public function __isset($name) {
    // TODO - or here already can isset($this->name) ????
    $value = $this->$name;

    return isset($value);
  }

}
