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
  const ACCESS_SET = null;
  const ACCESS_DELTA_INC = +1;
  const ACCESS_DELTA_DEC = -1;

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

  /**
   * Stored delta value which would be applied on next __set() call
   *
   * @var int|float $_currentDelta
   */
  protected $_currentDelta = self::ACCESS_SET;

  protected function blockChange($name) {
    if (array_key_exists($name, $this->_deltas)) {
      throw new \Exception(get_called_class() . '::' . $name . ' already INCREMENTED/DECREMENTED - can not CHANGE', ERR_ERROR);
    }
  }

  protected function blockDelta($name) {
    if (array_key_exists($name, $this->_changes)) {
      throw new \Exception(get_called_class() . '::' . $name . ' already changed - can not use DELTA', ERR_ERROR);
    }
  }

  protected function valueSet($name, $value) {
    if ($this->__isset($name)) {
      $this->blockChange($name);

      $this->_changes[$name] = $value;
    } else {
      $this->_startValues[$name] = $value;
    }

    parent::__set($name, $value);
  }

  protected function valueDelta($name, $value) {
    $this->blockDelta($name);

    !isset($this->_deltas[$name]) ? $this->_deltas[$name] = 0 : false;
    !isset($this->_startValues[$name]) ? $this->_startValues[$name] = 0 : false;

    $value *= $this->_currentDelta === self::ACCESS_DELTA_DEC ? -1 : +1;

    $this->_deltas[$name] += $value;

    parent::__set($name, parent::__get($name) + $value);

    $this->_currentDelta = self::ACCESS_SET;
  }

  public function __set($name, $value) {
    if ($this->_currentDelta === self::ACCESS_SET) {
      $this->valueSet($name, $value);
    } else {
      $this->valueDelta($name, $value);
    }
  }

//  /**
//   * Changes field by value
//   *
//   * Changes only $values and $_deltas
//   *
//   * @param string    $name
//   * @param int|float $value Default: 1
//   */
//  public function delta($name, $value = 1) {
//    $this->valueDelta($name, $value);
//  }
//
//  /**
//   * Increments property by value
//   *
//   * Changes only $values and $_deltas
//   *
//   * @param string    $name
//   * @param int|float $value Default: 1
//   */
//  public function inc($name, $value = 1) {
//    $this->delta($name, +$value);
//  }
//
//  /**
//   * Decrements property by value
//   *
//   * Changes only $values and $_deltas
//   *
//   * @param string    $name
//   * @param int|float $value Default: 1
//   */
//  public function dec($name, $value = 1) {
//    $this->delta($name, -$value);
//  }

  /**
   * Mark next set operation as delta increment
   *
   * @return $this
   */
  public function inc() {
    $this->_currentDelta = self::ACCESS_DELTA_INC;

    return $this;
  }

  /**
   * Mark next set operation as delta decrement
   *
   * @return $this
   */
  public function dec() {
    $this->_currentDelta = self::ACCESS_DELTA_DEC;

    return $this;
  }

  public function getChanges() {
    return $this->_changes;
  }

  public function getDeltas() {
    return $this->_deltas;
  }

  /**
   * Accepts changes
   *
   * Makes current values a start one and resets changes/deltas
   */
  public function acceptChanges() {
    $this->_startValues = $this->values;
    $this->_changes = [];
    $this->_deltas = [];
  }

  public function clear() {
    parent::clear();
    $this->acceptChanges();
  }

}
