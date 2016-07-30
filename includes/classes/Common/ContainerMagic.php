<?php

/**
 * Created by Gorlum 30.07.2016 9:54
 */

namespace Common;

class ContainerMagic implements IMagicAccess {

  /**
   * Property values
   *
   * @var array
   */
  protected $values = array();

  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return empty($this->values);
  }

  public function __isset($name) {
    return isset($this->values[$name]);
  }

  public function __unset($name) {
    unset($this->values[$name]);
  }

  public function __set($name, $value) {
    $this->values[$name] = $value;
  }

  public function __get($name) {
    return $this->values[$name];
  }

}
