<?php

/**
 * Class PropertyHiderInArray
 *
 * Holding properties in internal container
 */
class PropertyHiderInArray extends PropertyHider {

  protected $_data = array();

  /**
   * Method checks if action is available for named property
   *
   * @param string $name
   * @param string $action
   *
   * @return bool
   */
  protected function isPropertyActionAvailable($name, $action = '') {
    // By default all action available for existing properties
    return true;
  }

  /**
   * Internal method that make real changes to property value
   * May be override in child class
   *
   * @param string $name
   * @param mixed  $value
   *
   * @return mixed
   */
  protected function setProperty($name, $value) {
    // TODO: Change property only if value differs ????
//      if($this->{$this->getPhysicalPropertyName($name)} !== $value) {
//      }
    return $this->_data[$name] = $value;
  }

  /**
   * Internal method that make really reads property value
   * May be override in child class
   *
   * @param string $name
   * @param mixed  $value - ignored. Used for compatibility
   *
   * @return mixed
   */
  protected function getProperty($name, $value = null) {
    return $this->_data[$name];
  }

  public function __isset($name) {
    return isset($this->_data[$name]);
  }

}
