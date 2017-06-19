<?php
/**
 * Created by Gorlum 16.06.2017 14:32
 */

namespace Common;


/**
 * Class AccessLogged
 * @package Common
 */
class AccessLogged extends AccessMagic {

  /**
   * Starting values of properties
   *
   * @var array $_startValues
   */
  protected $_startValues = [];
  /**
   * Changed values
   *
   * @var array $_changes
   */
  protected $_changes = [];
  /**
   * Increment/Decrement results AKA deltas
   *
   * @var array $_deltas
   */
  protected $_deltas = [];

  public function __set($name, $value) {
    if (array_key_exists($name, $this->values)) {
      if(array_key_exists($name, $this->_deltas)) {
        throw new \Exception(get_called_class() . '::' . $name . ' already INCREMENTED/DECREMENTED - can not CHANGE', ERR_ERROR);
      }

      $this->_changes[$name] = $value;
    } else {
      $this->_startValues[$name] = $value;
    }

    parent::__set($name, $value);
  }

  /**
   * Increments field by value
   *
   * @param string    $name
   * @param int|float $value Default: 1
   */
  public function inc($name, $value = 1) {
    if(array_key_exists($name, $this->_changes)) {
      throw new \Exception(get_called_class() . '::' . $name . ' already changed - can not use INCREMENT', ERR_ERROR);
    }
    $this->_deltas[$name] += $value;
    parent::__set($name, parent::__get($name) + $value);
  }

  /**
   * Decrements field by value
   *
   * @param string    $name
   * @param int|float $value Default: 1
   */
  public function dec($name, $value = 1) {
    if(array_key_exists($name, $this->_changes)) {
      throw new \Exception(get_called_class() . '::' . $name . ' already changed - can not use DECREMENT', ERR_ERROR);
    }
    $this->_deltas[$name] -= $value;
    parent::__set($name, parent::__get($name) - $value);
  }

  public function getChanges() {
    return $this->_changes;
  }

  public function getDeltas() {
    return $this->_deltas;
  }

  /**
   * Flushes changes
   *
   * Makes current values a start one and resets changes/deltas
   */
  public function ♦flush() {
    $this->_startValues = $this->values;
    $this->_changes = [];
    $this->_deltas = [];
  }

  public function clear() {
    parent::clear();
    $this->flush();
  }

}
