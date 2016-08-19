<?php

use \Common\ContainerMagic;

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
class ContainerAccessors extends ContainerMagic {
  /**
   * Array of accessors - getters/setters/etc
   *
   * @var callable[][]
   */
  protected $accessors = array();

  /**
   * @param array $array
   */
  public function setAccessors($array) {
    $this->accessors = $array;
  }

  /**
   * Direct access to parent class setter
   *
   * @param string $name
   * @param mixed  $value
   */
  public function setDirect($name, $value) {
    ContainerMagic::__set($name, $value);
  }

  /**
   * Direct access to parent class getter
   *
   * @param string $name
   *
   * @return mixed
   */
  public function getDirect($name) {
    return ContainerMagic::__get($name);
  }

  /**
   * Direct access to parent class unsetter
   *
   * @param string $name
   */
  public function unsetDirect($name) {
    ContainerMagic::__unset($name);
  }

  /**
   * Assign accessor to a named variable
   *
   * Different accessors have different signatures - you should look carefully before assigning accessor
   *
   * @param string   $varName
   * @param string   $processor - type of accessor getter/setter/importer/exporter/etc
   * @param callable $callable
   *
   * @throws Exception
   */
  public function setAccessor($varName, $processor, $callable) {
    if (empty($callable)) {
      return;
    }

    if (!is_callable($callable)) {
      throw new Exception('Error assigning callable in ' . get_called_class() . '! Callable typed [' . $processor . '] is not a callable or not accessible in the scope');
    }

    $this->accessors[$varName][$processor] = $callable;
  }

  /**
   * @param $varName
   * @param $processor
   *
   * @return callable|null
   */
  protected function getAccessor($varName, $processor) {
    return isset($this->accessors[$varName][$processor]) ? $this->accessors[$varName][$processor] : null;
  }

  /**
   * Performs $processor operation on property with specified name
   *
   * @param string     $name
   * @param string     $processor
   * @param null|mixed $value
   *
   * @return mixed
   */
  protected function performMagic($name, $processor, $value = null) {
    if ($accessor = $this->getAccessor($name, $processor)) {
      return call_user_func($accessor, $this, $value);
//    if (isset($this->accessors[$name][$processor])) {
//      return call_user_func($this->accessors[$name][$processor], $this, $value);
    } else {
      return parent::$processor($name, $value);
    }
  }

  public function __set($name, $value) {
    if (is_callable($value)) {
      $this->setAccessor($name, P_CONTAINER_GET, $value);
    } else {
      $this->performMagic($name, P_CONTAINER_SET, $value);
    }
  }

  public function __get($name) {
    return $this->performMagic($name, P_CONTAINER_GET, null);
  }

  public function __unset($name) {
    $this->performMagic($name, P_CONTAINER_UNSET, null);
  }

  public function __isset($name) {
    // TODO - or here already can isset($this->name) ????
    $value = $this->$name;

    return isset($value);
  }

}
