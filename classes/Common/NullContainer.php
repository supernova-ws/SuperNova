<?php
/**
 * Created by Gorlum 08.01.2018 15:31
 */

namespace Common;

use Core\Singleton;

class NullContainer extends Singleton implements Interfaces\IContainer {

  public function __set($name, $value) {
  }

  public function __isset($name) {
    return false;
  }

  public function __get($name) {
    return null;
  }

  public function __unset($name) {
  }

  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return true;
  }

  /**
   * Clears container contents
   */
  public function clear() {
  }

}
