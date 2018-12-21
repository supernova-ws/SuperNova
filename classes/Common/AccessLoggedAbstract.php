<?php
/**
 * Created by Gorlum 14.11.2018 0:43
 */

namespace Common;


use Exception;

abstract class AccessLoggedAbstract extends AccessMagic {
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
   * Current operation: direct store or positive/negative delta which would be applied on next __set() call
   *
   * @var int|float $_currentOperation
   */
  protected $_currentOperation = self::ACCESS_SET;

  public function clear() {
    parent::clear();
    $this->accept();
  }

  /**
   * @param string $name
   *
   * @throws Exception
   */
  protected function blockChange($name) {
    if (array_key_exists($name, $this->_deltas)) {
      throw new Exception(get_called_class() . '::' . $name . ' already INCREMENTED/DECREMENTED - can not CHANGE', ERR_ERROR);
    }
  }

  /**
   * @param string $name
   *
   * @throws Exception
   */
  protected function blockDelta($name) {
    if (array_key_exists($name, $this->_changes)) {
      throw new Exception(get_called_class() . '::' . $name . ' already changed - can not use DELTA', ERR_ERROR);
    }
  }

//  /**
//   * Stub to pass call down
//   *
//   * @param string $name
//   * @param mixed  $value
//   */
//  public function __set($name, $value) {
//    parent::__set($name, $value);
//  }

//  /**
//   * Stub to pass call down
//   *
//   * @param string $name
//   *
//   * @return mixed
//   */
//  public function __get($name) {
//    return parent::__get($name);
//  }

//  public function __set($name, $value) {
//    if ($this->_currentOperation === self::ACCESS_SET) {
//      $this->valueSet($name, $value);
//    } else {
//      $this->valueDelta($name, $value);
//    }
//  }

  /**
   * @param string $name
   * @param $value
   *
   * @throws Exception
   */
  abstract protected function valueSet($name, $value);

  /**
   * @param string $name
   * @param mixed $value
   *
   * @throws Exception
   */
  abstract protected function valueDelta($name, $value);


    /**
   * Mark next set operation as delta increment
   *
   * @return $this
   */
  public function inc() {
    $this->_currentOperation = self::ACCESS_DELTA_INC;

    return $this;
  }

  /**
   * Mark next set operation as delta decrement
   *
   * @return $this
   */
  public function dec() {
    $this->_currentOperation = self::ACCESS_DELTA_DEC;

    return $this;
  }

  /**
   * Accepts changes
   *
   * Makes current values a start one and resets changes/deltas
   */
  public function accept() {
    $this->_startValues      = $this->values;
    $this->_currentOperation = self::ACCESS_SET;
    $this->_changes          = [];
    $this->_deltas           = [];
  }

  /**
   * Rolls changes back
   */
  public function reject() {
    $this->values            = $this->_startValues;
    $this->_currentOperation = self::ACCESS_SET;
    $this->_changes          = [];
    $this->_deltas           = [];
  }

  /**
   * Is container was changed?
   *
   * @return bool
   */
  public function isChanged() {
    return
      ! empty($this->_changes)
      ||
      ! empty($this->_deltas);
  }

  public function getChanges() {
    return $this->_changes;
  }

  public function getDeltas() {
    return $this->_deltas;
  }

}
