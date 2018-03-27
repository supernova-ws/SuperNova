<?php
/**
 * Created by Gorlum 14.02.2017 11:21
 */

/**
 * Back-compatibility functions
 */
if (!function_exists('is_iterable')) {
  /**
   * Check is variable iterable
   *
   * Compatibility function for PHP 5.x
   *
   * @param mixed $var
   *
   * @return bool
   */
  function is_iterable($var) {
    return is_array($var) || $var instanceof Traversable;
  }
}