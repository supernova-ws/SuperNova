<?php

class HelperArray {

  /**
   * Convert $delimiter delimited string to array
   *
   * @param mixed  $string
   * @param string $delimiter
   *
   * @return array
   */
  public static function stringToArray($string, $delimiter = ',') {
    return is_string($string) && !empty($string) ? explode($delimiter, $string) : array();
  }

  /**
   * Convert single value to array
   *
   * @param mixed $value
   *
   * @return array
   */
  public static function makeArray(&$value, $index = 0) {
    return !is_array($value) ? array($index => $value) : $value;
  }

  /**
   * @param mixed    $array
   * @param callable $callback
   *
   * @return array
   */
  public static function filter(&$array, $callback) {
    $result = array();

    if (is_array($array) && !empty($array)) {
      foreach ($array as $value) {
        if (call_user_func($callback, $value)) {
          $result[] = $value;
        }
      }

    }

    return $result;
  }

  protected static function isNotEmpty($value) {
    return !empty($value);
  }

  /**
   * Filter empty() values from array
   *
   * @param $array
   *
   * @return array
   */
  public static function filterEmpty($array) {
    return static::filter($array, array(get_called_class(), 'isNotEmpty'));
  }

  public static function stringToArrayFilterEmpty($string, $delimiter = ',') {
    return static::filterEmpty(static::stringToArray($string, $delimiter));
  }

}
