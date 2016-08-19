<?php

class PropertyHiderInObject extends PropertyHider {
  private function getPhysicalPropertyName($name) {
    return '_' . $name;
  }

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
    return property_exists($this, $this->getPhysicalPropertyName($name));
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
    return $this->{$this->getPhysicalPropertyName($name)} = $value;
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
    return $this->{$this->getPhysicalPropertyName($name)};
  }

  /**
   * Magic method that checks if named property is set
   * May be override in child class
   *
   * @param $name
   *
   * @return bool
   */
  public function __isset($name) {
    return isset($this->{$this->getPhysicalPropertyName($name)});
  }

}
