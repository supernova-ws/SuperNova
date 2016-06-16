<?php

/**
 * Class ArrayAccessV2
 *
 * Simple data container
 * Features:
 * - translates property operation to container elements operation
 * - implements access to properties as to array
 * - class is traversable
 * - clone options: deep, shallow, none;
 */
class ArrayAccessV2 implements ArrayAccess, Iterator {

  /**
   * If container data need to be additionally cloned
   *
   * @var int
   */
  public static $_clonable = HelperArray::CLONE_DEEP;

  /**
   * Data container
   *
   * @var array
   */
  public $_container = array();


  /**
   * @return stdClass
   *
   * @version 41a50.9
   */
  public function _createElement() {
    return new stdClass();
  }


  /**
   * @param $name
   *
   * @return bool
   */
  public function __isset($name) {
    return array_key_exists($name, $this->_container);
  }

  /**
   * @param $name
   */
  public function __unset($name) {
    unset($this->_container[$name]);
  }

  /**
   * @param mixed $name
   *
   * @return mixed|null
   */
  public function __get($name) {
    return $this->__isset($name) ? $this->_container[$name] : null;
  }

  /**
   * @param $name
   * @param $value
   */
  public function __set($name, $value) {
    $this->_container[$name] = $value;
  }

  public function __clone() {
    if (static::$_clonable == HelperArray::CLONE_NONE) {
      return;
    }

    HelperArray::cloneDeep($this->_container, static::$_clonable);
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
    return $this->__isset($offset);
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
    return $this->__get[$offset];
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
    $this->__set($offset, $value);
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
    $this->__unset($offset);
  }


  /**
   * Get element count in container
   *
   * @return int
   */
  public function count() {
    return count($this->_container);
  }

  /**
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   * @since 5.0.0
   */
  public function current() {
    return current($this->_container);
  }

  /**
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function next() {
    next($this->_container);
  }

  /**
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   * @since 5.0.0
   */
  public function key() {
    return key($this->_container);
  }

  /**
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   * @since 5.0.0
   */
  public function valid() {
    return false !== current($this->_container);
  }

  /**
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function rewind() {
    reset($this->_container);
  }

}
