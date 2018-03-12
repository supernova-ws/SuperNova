<?php

/**
 * Created by Gorlum 30.07.2016 9:54
 */

namespace Common;

/**
 * Class AccessMagic
 *
 * Implements all magic methods for accessing non-exists property
 * Used to distinguish how class properties should be accessed like ArrayAccess
 *
 * @package Common
 */
class AccessMagic implements \IContainer {

  /**
   * @var GlobalContainer $services
   */
  protected $services;

  /**
   * Property values
   *
   * @var array
   */
  protected $values = array();

  /**
   * AccessMagic constructor.
   *
   * @param GlobalContainer|null $services
   */
  public function __construct(GlobalContainer $services = null) {
    $this->services = empty($services) ? \SN::$gc : $services;
  }


  /**
   * Magic setter
   *
   * @param string $name
   * @param mixed  $value
   */
  public function __set($name, $value) {
    $this->values[$name] = $value;
  }

  /**
   * Magic getter
   *
   * @param string $name
   *
   * @return mixed
   */
  public function __get($name) {
    return $this->__isset($name) ? $this->values[$name] : null;
  }

  /**
   * Magic checker for property set
   *
   * @param string $name
   *
   * @return boolean
   */
  public function __isset($name) {
    return array_key_exists($name, $this->values);
  }

  /**
   * Magic un-setter
   *
   * @param string $name
   */
  public function __unset($name) {
    unset($this->values[$name]);
  }

  /**
   * Extracts values as array [$propertyName => $propertyValue]
   *
   * @return array
   */
  public function asArray() {
    return $this->values;
  }

  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return empty($this->values);
  }

  /**
   * Clears container contents
   */
  public function clear() {
    $this->values = array();
  }

}
