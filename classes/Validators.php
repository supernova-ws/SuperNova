<?php

/**
 * Class Validators
 */
// TODO - перенести сюда все базовые валидаторы
class Validators {

  /**
   * @param mixed &$value
   *
   * @return bool
   */
  public static function isNotEmptyByRef(&$value) {
    return !empty($value);
  }

  /**
   * @param mixed $value
   *
   * @return bool
   */
  public static function isNotEmpty($value) {
    return self::isNotEmptyByRef($value);
  }

}
