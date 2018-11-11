<?php
/**
 * Created by Gorlum 16.06.2017 14:32
 */

namespace Common;


use Exception;

/**
 * Class AccessLogged
 *
 * Logs property changes. It's necessary for delta and/or partial DB updates
 *
 * On first property change it goes to start values
 *
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
   * Current operation: direct store or positive/negative delta which would be applied on next __set() call
   *
   * @var int|float $_currentOperation
   */
  protected $_currentOperation = self::ACCESS_SET;

  public function __set($name, $value) {
    if ($this->_currentOperation === self::ACCESS_SET) {
      $this->valueSet($name, $value);
    } else {
      $this->valueDelta($name, $value);
    }
  }

  public function clear() {
    parent::clear();
    $this->commit();
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

  /**
   * @param string $name
   * @param $value
   *
   * @throws Exception
   */
  protected function valueSet($name, $value) {
    if ($this->__isset($name)) {
      $this->blockChange($name);

      $this->_changes[$name] = $value;
    } else {
      $this->_startValues[$name] = $value;
    }

    parent::__set($name, $value);
  }

  /**
   * @param string $name
   * @param mixed $value
   *
   * @throws Exception
   */
  protected function valueDelta($name, $value) {
    $this->blockDelta($name);

    !isset($this->_deltas[$name]) ? $this->_deltas[$name] = 0 : false;
    !isset($this->_startValues[$name]) ? $this->_startValues[$name] = 0 : false;

    $value *= $this->_currentOperation === self::ACCESS_DELTA_DEC ? -1 : +1;

    $this->_deltas[$name] += $value;

    parent::__set($name, parent::__get($name) + $value);

    $this->_currentOperation = self::ACCESS_SET;
  }

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
  public function commit() {
    $this->_startValues      = $this->values;
    $this->_currentOperation = self::ACCESS_SET;
    $this->_changes          = [];
    $this->_deltas           = [];
  }

  /**
   * Rolls changes back
   */
  public function rollback() {
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
