<?php
/**
 * Created by Gorlum 08.01.2018 15:16
 */

namespace Traits;


trait TContainer {
  /**
   * @return \IContainer
   */
  public function _getContainer() {
    return null;
  }

  public function __set($name, $value) {
    is_object($this->_getContainer()) ? $this->_getContainer()->__set($name, $value) : null;
  }

  public function __isset($name) {
    return is_object($this->_getContainer()) ? $this->_getContainer()->__isset($name) : null;
  }

  public function __get($name) {
    return is_object($this->_getContainer()) ? $this->_getContainer()->__get($name) : null;
  }

  public function __unset($name) {
    is_object($this->_getContainer()) ? $this->_getContainer()->__unset($name) : null;
  }

  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return is_object($this->_getContainer()) ? $this->_getContainer()->isEmpty() : false;
  }

  /**
   * Clears container contents
   */
  public function clear() {
    is_object($this->_getContainer()) ? $this->_getContainer()->clear() : null;
  }

  public function __call($name, $arguments) {
    return is_object($this->_getContainer()) ? call_user_func_array([$this->_getContainer(), $name], $arguments) : null;
  }

}
