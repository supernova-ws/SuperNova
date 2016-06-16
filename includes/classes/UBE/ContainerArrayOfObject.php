<?php

/**
 * Class ContainerArrayOfObject
 *
 * Access to object container as to array by index
 * Features:
 * - applying predefined functions to container content via __call();
 * - get sum of specified property of containing objects;
 * - aggregate value of property of containing objects;
 *
 */
class ContainerArrayOfObject extends ArrayAccessV2 {

  /**
   * Automatically apply all non-exist function from static::$_call list to $_container content
   *
   * @param string $method_name
   * @param array  $arguments
   */
  public function __call($method_name, array $arguments) {
    foreach($this->_container as $object) {
      if(is_object($object) && method_exists($object, $method_name)) {
        call_user_func_array(array($object, $method_name), $arguments);
      }
    }
  }

  /**
   * Summarize property values of contained objects
   *
   * @param string $property_name
   *
   * @return float
   */
  public function getSumProperty($property_name) {
    $result = 0.0;
    foreach($this->_container as $object) {
      if(is_object($object) && property_exists($object, $property_name)) {
        $result += $object->$property_name;
      }
    }

    return $result;
  }

  /**
   * Aggregate value of $property_name in containing object by $method_name
   *
   * @param string $method_name
   *
   * @return mixed
   */
  public function aggregateByMethod($method_name, &$result) {
    foreach($this->_container as $object) {
      if(is_object($object) && method_exists($object, $method_name)) {
        call_user_func(array($object, $method_name), $result);
      }
    }
  }

}
