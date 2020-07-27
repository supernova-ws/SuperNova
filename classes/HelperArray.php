<?php

class HelperArray {

  /**
   * Overwrites old array with new
   */
  const MERGE_OVERWRITE = 0;
  /**
   * Merges old array with new with array_merge()
   * String keys replaced, numeric keys renumbered
   */
  const MERGE_PHP = 1;

  /**
   * Merges old array with new with array_merge_recursive()
   * String keys replaced, numeric keys renumbered
   */
  const MERGE_RECURSIVE = 2;

  /**
   * Merges old array with new recursive
   * String keys merged, numeric keys merged
   */
  const MERGE_RECURSIVE_NUMERIC = 3;


  /**
   * No array cloning - just stay with same objects
   */
  const CLONE_ARRAY_NONE = 0;
  /**
   * Clone objects on first level of array
   */
  const CLONE_ARRAY_SHALLOW = 1;
  /**
   * Clone objects recursive on any array level
   */
  const CLONE_ARRAY_RECURSIVE = 2;

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
    if (!is_array($value)) {
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
      // TODO - array_filter
      foreach ($array as $value) {
        if (call_user_func($callback, $value)) {
          $result[] = $value;
        }
      }
    }

    return $result;
  }

  /**
   * Filters array by callback
   *
   * @param mixed    $array
   * @param callable $callback
   * @param bool     $withKeys
   *
   * @return array
   */
  public static function map(&$array, $callback, $withKeys = false) {
    $result = array();

    if (is_array($array) && !empty($array)) {
      if ($withKeys) {
        $result = array_map($callback, $array, array_keys($array));
      } else {
        $result = array_map($callback, $array);
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
   * @param int         $mergeStrategy - default is HelperArray::MERGE_OVERWRITE
   */
  public static function merge(&$arrayOld, $arrayNew = array(), $mergeStrategy = self::MERGE_OVERWRITE) {
    static::makeArrayRef($arrayNew);
    static::makeArrayRef($arrayOld);

    switch ($mergeStrategy) {
      case self::MERGE_PHP:
        $arrayOld = array_merge($arrayOld, $arrayNew);
      break;

      case self::MERGE_RECURSIVE:
        $arrayOld = array_merge_recursive($arrayOld, $arrayNew);
      break;

      case self::MERGE_RECURSIVE_NUMERIC:
        foreach ($arrayNew as $key => $value) {
          !isset($arrayOld[$key]) || !is_array($arrayOld[$key]) ? $arrayOld[$key] = $value : self::merge($arrayOld[$key], $value, self::MERGE_RECURSIVE_NUMERIC);
        }
      break;

      default:
        $arrayOld = $arrayNew;
      break;
    }
  }

  /**
   * Checks if key exists in array. If yes - return value on key otherwise return default value
   *
   * @param array &$array
   * @param mixed $key
   * @param mixed $default
   *
   * @return mixed
   */
  public static function keyExistsOr(&$array, $key, $default) {
    return array_key_exists($key, $array) ? $array[$key] : $default;
  }

  /**
   * Clone objects in array
   *
   * @param array &$array - Any dimensional array with presumed objects in there
   * @param int   $deep - HelperArray::CLONE_ARRAY_xxx constants
   */
  public static function cloneDeep(&$array, $deep = self::CLONE_ARRAY_RECURSIVE) {
    if ($deep == self::CLONE_ARRAY_NONE) {
      return;
    }

    foreach ($array as &$value) {
      if (is_object($value)) {
        $value = clone $value;
      } elseif (is_array($value) && $deep == self::CLONE_ARRAY_RECURSIVE) {
        static::cloneDeep($value, $deep);
      }
    }
  }

  /**
   * Repacking array to provided level, removing null elements
   *
   * @param array &$array
   * @param int   $level
   */
  public static function array_repack(&$array, $level = 0) {
    if (!is_array($array)) {
      return;
    }

    foreach ($array as $key => &$value) {
      if ($value === null) {
        unset($array[$key]);
      } elseif ($level > 0 && is_array($value)) {
        static::array_repack($value, $level - 1);
        if (empty($value)) {
          unset($array[$key]);
        }
      }
    }
  }


  /**
   * Parses array of stringified parameters to array of parameter
   *
   * Stringified parameter is a string of "<parmName><delimeter><paramValue>"
   *
   * @param array|string $array
   * @param string       $delimiter
   *
   * @return array
   */
  public static function parseParamStrings($array, $delimiter = '=') {
    !is_array($array) ? $array = array((string)$array) : false;

    $result = array();
    foreach ($array as $param) {
      $exploded = explode($delimiter, $param);
      $paramName = $exploded[0];
      unset($exploded[0]);
      $result[$paramName] = implode($delimiter, $exploded);
    }

    return $result;
  }

  /**
   * Finds maximum value of field in subarrays
   *
   * @param array[] $array
   * @param mixed   $fieldName
   *
   * @return float|null
   */
  public static function maxValueByField(&$array, $fieldName) {
    return array_reduce($array, function ($carry, $item) use ($fieldName) {
      if(is_array($item) && isset($item[$fieldName]) && (!isset($carry) || $carry < $item[$fieldName])) {
        $carry = $item[$fieldName];
      }

      return $carry;
    });
  }

  /**
   * @param $array
   * @param $fieldName
   */
  public static function topRecordsByField(&$array, $fieldName) {
    $maxValue = self::maxValueByField($array, $fieldName);

    return
      array_reduce($array,
        function ($carry, $item) use (&$fieldName, $maxValue) {
          if(is_array($item) && isset($item[$fieldName]) && $item[$fieldName] == $maxValue) {
            $carry[] = $item;
          }

          return $carry;
        },
        array()
      );
  }

  public static function intersectByKeys(array &$array1, array &$array2) {
    return array_uintersect_assoc($array1, $array2, function ($a, $b) {return 0;});
  }

  /**
   * Get first key of array
   * Polyfill until PHP 7.3
   *
   * @param array $array
   *
   * @return mixed|null
   */
  public static function array_key_first(&$array) {
    if (!is_array($array)) {
      return null;
    }
    reset($array);

    return key($array);
  }

}
