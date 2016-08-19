<?php

/**
 * Created by Gorlum 30.07.2016 9:54
 */

namespace Common;

/**
 * Class ContainerMagic
 *
 * Implements all magic methods for accessing non-exists property
 * Used to distinguish how class properties should be accessed like ArrayAccess
 *
 * @package Common
 */
class ContainerMagic {

  /**
   * Property values
   *
   * @var array
   */
  protected $values = array();

  /**
   * Magic setter
   *
   * @param string $name
   * @param mixed  $value
   */
  public function __set($name, $value) {
    $this->values[$name] = $value;
  }


  /**
   * Magic getter
   *
   * @param string $name
   *
   * @return mixed
   */
  public function __get($name) {
    return isset($this->values[$name]) ? $this->values[$name] : null;
  }

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


  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return empty($this->values);
  }

  /**
   * Clears container contents
   */
  public function clear() {
    $this->values = array();
  }

}
