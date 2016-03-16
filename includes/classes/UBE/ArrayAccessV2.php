<?php

/**
 * Class ArrayAccessV2
 *
 * Access to object container as to array by index
 * Features:
 * - clone options: deep, shallow, none;
 * - applying predefined functions to container content via __call();
 * - get sum of specified property of containing objects;
 * - aggregate value of property of containing objects;
 */
class ArrayAccessV2 implements ArrayAccess {

  const CLONE_NONE = 0;
  const CLONE_SHALLOW = 1;
  const CLONE_DEEP = 2;

  /**
   * If container data need to be additionally cloned
   *
   * @var int
   */
  public static $_clonable = ArrayAccessV2::CLONE_DEEP;

  /**
   * Data container
   *
   * @var array
   */
  public $_container = array();

//  /**
//   * Method list that should support applying to container content
//   *
//   * @var string[]
//   */
//  protected static $_call = array();

//  /**
//   * Contained object property list that supported by property summarizer
//   *
//   * @var string[]
//   */
//  protected static $_sum_property = array();

  /**
   * @return stdClass
   *
   * @version 41a6.0
   */
  public function _createElement() {
    return new stdClass();
  }


  public function __clone() {
    if(static::$_clonable == ArrayAccessV2::CLONE_NONE) {
      return;
    }

    static::_deep_clone($this->_container);
  }

  protected static function _deep_clone(&$array) {
    foreach($array as &$value) {
      if(is_object($value)) {
        $value = clone $value;
      } elseif(is_array($value) && static::$_clonable == ArrayAccessV2::CLONE_DEEP) {
        static::_deep_clone($value);
      }
    }
  }

  /**
   * Automatically apply all non-exist function from static::$_call list to $_container content
   *
   * @param string $method_name
   * @param array  $arguments
   */
  public function __call($method_name, array $arguments) {
//    if(in_array($method_name, static::$_call))
    {
      $object_first = reset($this->_container);
      if($object_first !== false && method_exists($object_first, $method_name)) {
        foreach($this->_container as $unit_id => $object) {
          call_user_func_array(array($object, $method_name), $arguments);
        }
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

  /**
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   *
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   *
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   * @since 5.0.0
   */
  public function offsetExists($offset) {
    return array_key_exists($offset, $this->_container);
  }

  /**
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   *
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   *
   * @return mixed Can return all value types.
   * @since 5.0.0
   */
  public function offsetGet($offset) {
    return $this->_container[$offset];
  }

  /**
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   *
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   *
   * @return void
   * @since 5.0.0
   */
  public function offsetSet($offset, $value) {
    $this->_container[$offset] = $value;
  }

  /**
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   *
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   *
   * @return void
   * @since 5.0.0
   */
  public function offsetUnset($offset) {
    unset($this->_container[$offset]);
  }


  /**
   * Get element count in container
   *
   * @return int
   */
  public function count() {
    return count($this->_container);
  }

}
