<?php
/**
 * Created by Gorlum 08.01.2018 15:16
 */

namespace Common\Traits;


trait TContainer {
  /**
   * @return \Common\Interfaces\IContainer
   */
  public function _getContainer() {
    return null;
  }

  public function __set($name, $value) {
    is_object($this->_getContainer())
      ? $this->_getContainer()->__set($this->_containerTranslatePropertyName($name), $value)
      : null;
  }

  public function __isset($name) {
    return is_object($this->_getContainer())
      ? $this->_getContainer()->__isset($this->_containerTranslatePropertyName($name))
      : null;
  }

  public function __get($name) {
    return is_object($this->_getContainer())
      ? $this->_getContainer()->__get($this->_containerTranslatePropertyName($name))
      : null;
  }

  public function __unset($name) {
    is_object($this->_getContainer())
      ? $this->_getContainer()->__unset($this->_containerTranslatePropertyName($name))
      : null;
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

  protected function _containerTranslatePropertyName($name) {
    return $name;
  }

}
