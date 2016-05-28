<?php

class HelperArray {

  /**
   * Overwrites old array with new
   */
  const OVERWRITE = 0;
  /**
   * Merges old array with new with array_merge()
   * String keys replaced, numeric keys renumbered
   */
  const MERGE_PHP = 1;

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
   * Convert single value to array by reference
   *
   * @param mixed &$value
   */
  public static function makeArrayRef(&$value, $index = 0) {
    if(!is_array($value)) {
      $value = array($index => $value);
    }
  }


  /**
   * Convert single value to array
   *
   * @param mixed $value
   *
   * @return array
   */
  public static function makeArray($value, $index = 0) {
    static::makeArrayRef($value, $index);

    return $value;
  }

  /**
   * Filters array by callback
   *
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

  /**
   * Filter empty() values from array
   *
   * @param $array
   *
   * @return array
   */
  public static function filterEmpty($array) {
    return static::filter($array, 'Validators::isNotEmpty');
  }

  /**
   * @param string $string
   * @param string $delimiter
   *
   * @return array
   */
  public static function stringToArrayFilterEmpty($string, $delimiter = ',') {
    return static::filterEmpty(static::stringToArray($string, $delimiter));
  }

  /**
   * @param mixed|array &$arrayOld
   * @param mixed|array $arrayNew
   * @param int         $mergeStrategy - default is HelperArray::ARRAY_REPLACE
   */
  public static function merge(&$arrayOld, $arrayNew = array(), $mergeStrategy = HelperArray::OVERWRITE) {
    static::makeArrayRef($arrayNew);
    static::makeArrayRef($arrayOld);

    switch ($mergeStrategy) {
      case HelperArray::MERGE_PHP:
        $arrayOld = array_merge($arrayOld, $arrayNew);
      break;

      default:
        $arrayOld = $arrayNew;
      break;
    }
  }

}
