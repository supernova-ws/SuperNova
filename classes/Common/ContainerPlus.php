<?php

namespace Common;

use Common\Pimple\Container;

/**
 * Class ContainerPlus
 *
 * Extends original container to allow access to properties and function to document them
 *
 * @package Pimple
 */
class ContainerPlus extends Container implements Interfaces\IContainer {

  public function __set($name, $value) {
    $this->offsetSet($name, $value);
  }

  public function __get($name) {
    return $this->offsetGet($name);
  }

  public function __isset($name) {
    return $this->offsetExists($name);
  }

  public function __unset($name) {
    $this->offsetUnset($name);
  }

  public function __call($name, $arguments) {
    return call_user_func_array($this->$name, $arguments);
  }

  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    $keys = $this->keys(); // TODO PHP 5.5
    return empty($keys);
  }

  /**
   * Clears container contents
   *
   * @return mixed
   */
  public function clear() {
    throw new \Exception(get_class() . '::clear() not implemented - inherited from ContainerPlus');
  }

}
