<?php

namespace Common;

use \Common\ContainerMagic;

/**
 * Class Common\ContainerAccessors
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
   * @var \Common\Accessors $accessors
   */
  protected $accessors;

  public function __construct() {
    $this->accessors = new \Common\Accessors();
  }

  /**
   * @param \Common\Accessors $accessors
   */
  public function setAccessors($accessors) {
    $this->accessors = $accessors;
  }

  /**
   * Performs accessor operation on property with specified name
   *
   * @param string     $varName
   * @param string     $accessor
   * @param null|mixed $value
   *
   * @return mixed
   */
  protected function performMagic($varName, $accessor, $value = null) {
    if ($this->accessors->haveAccessor($varName, $accessor)) {
      return $this->accessors->invokeAccessor($varName, $accessor, array($this, $value));
    } else {
      return parent::$accessor($varName, $value);
    }
  }


  public function __set($name, $value) {
    if (is_callable($value)) {
      $this->accessors->setAccessor($name, P_CONTAINER_GET, $value);
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

}
