<?php

namespace Common;

/**
 * Class Common\AccessAccessors
 *
 * Support accessors for properties: getter, setter, unsetter
 *
 * Direct access to container (passing accessors) made via ArrayAccess like
 *     AccessAccessors[$propertyName]
 *
 * Signature to accessor call:
 *    function ($this, $varName = '', $arg...)
 * $this - current object passing to accessor
 * If $varName omitted then no args can be supplied
 * For setter - third argument is variable value
 *
 * Getter/Unsetter/Issetter is a callable like
 *    function ($that) {}
 *
 * Setter is a callable like
 *    function ($that, $value)  {}
 *
 * To pass accessors and set/get property directly use ArrayAccess (i.e.
 *
 */
class AccessAccessors implements \ArrayAccess {
  /**
   * @var AccessorsV2 $accessors
   */
  protected $accessors;

  /**
   * Class name for $_data property
   *
   * @var string
   */
  protected $_dataClass = 'Common\AccessMagic';
  /**
   * @var \Common\Interfaces\IContainer $_data
   */
  protected $_data;


  public function __construct() {
    $this->_data = new $this->_dataClass;
    $this->accessors = new AccessorsV2();
  }

  /**
   * @param AccessorsV2 $accessors
   */
  public function setAccessors($accessors) {
    $this->accessors = $accessors;
  }

  public function __call($accessor, $arguments) {

    // $varName could be empty - thus we calling some object-wide function
    $functionName = $accessor . (!empty($arguments[0]) ? $arguments[0] : '');
    if (isset($this->accessors->$functionName)) {
      // Inserting link to current object as first argument
      array_unshift($arguments, $this);
      $result = $this->accessors->__call($functionName, $arguments);

      return $result;
    } else {
      $result = call_user_func_array(array($this->_data, $accessor), $arguments);
    }

    return $result;
  }


  public function __set($name, $value) {
    $this->__call(P_ACCESSOR_SET, array($name, $value));
  }

  public function __get($name) {
    return $this->__call(P_ACCESSOR_GET, array($name));
  }

  public function __unset($name) {
    $this->__call(P_ACCESSOR_UNSET, array($name));
  }

  public function __isset($name) {
    return $this->__call(P_ACCESSOR_ISSET, array($name));
  }

  public function isEmpty() {
    return $this->_data->isEmpty();
  }

  public function clear() {
    $this->_data->clear();
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
    return isset($this->_data->$offset);
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
    return $this->_data->$offset;
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
    $this->_data->$offset = $value;
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
    unset($this->_data->$offset);
  }

}
