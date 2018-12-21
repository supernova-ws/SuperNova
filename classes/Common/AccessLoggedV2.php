<?php
/**
 * Created by Gorlum 16.06.2017 14:32
 */

namespace Common;


use Exception;

/**
 * Class AccessLoggedV2
 *
 * Logs property changes. It's necessary for delta and/or partial DB updates
 *
 * This class differs from AccessLogged that first assignment does goes into $values and does not fills $_startValues
 *
 * @package Common
 */
class AccessLoggedV2 extends AccessLoggedAbstract {
  public function __set($name, $value) {
    if ($this->_currentOperation === self::ACCESS_SET) {
      $this->valueSet($name, $value);
    } else {
      $this->valueDelta($name, $value);
    }
  }


  /**
   * @param string $name
   * @param mixed $value
   *
   * @throws Exception
   */
  protected function valueSet($name, $value) {
    $this->blockChange($name);

    $this->_changes[$name] = $value;

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

    $value *= $this->_currentOperation === self::ACCESS_DELTA_DEC ? -1 : +1;

    $this->_deltas[$name] += $value;

    parent::__set($name, parent::__get($name) + $value);

    $this->_currentOperation = self::ACCESS_SET;
  }

}
