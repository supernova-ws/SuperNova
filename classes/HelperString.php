<?php

/**
 * Created by Gorlum 12.02.2017 11:12
 */
class HelperString {

  /**
   * Encodes non-html characters in UTF-8
   *
   * @param string $string
   * @param int    $params - bit mask:
   *    HTML_ENCODE_PREFORM
   *    HTML_ENCODE_NL2BR
   *    HTML_ENCODE_STRIP_HTML
   *    HTML_ENCODE_JS_SAFE
   *
   * @return string
   */
  public static function htmlEncode($string, $params = HTML_ENCODE_PREFORM) {
    $params & HTML_ENCODE_STRIP_HTML ? $string = strip_tags($string) : false;
    $params & HTML_ENCODE_PREFORM ? $string = self::htmlSafe($string) : false;
    $params & HTML_ENCODE_NL2BR ? $string = self::nl2br($string) : false;
    $params & HTML_ENCODE_JS_SAFE ? $string = self::jsSafe($string) : false;
    $params & HTML_ENCODE_SPACE ? $string = self::space2nbsp($string) : false;

    return $string;
  }

  /**
   * @param $string
   *
   * @return string
   */
  public static function htmlSafe($string) {
    return htmlentities($string, ENT_COMPAT, 'UTF-8');
  }


  /**
   * @param $string
   *
   * @return mixed
   */
  public static function nl2br($string) {
    return str_replace(array("\r", "\n"), '', nl2br($string));
  }

  /**
   * Make string JS-safe
   *
   * @param $string
   *
   * @return mixed
   */
  public static function jsSafe($string) {
    return str_replace(array("\r", "\n"), array('\r', '\n'), addslashes($string));
  }

  /**
   * @param $string
   *
   * @return mixed
   */
  public static function space2nbsp($string) {
    return str_replace(' ', '&nbsp;', $string);
  }

  protected function explodeCallable(&$string) {
    $string = explode('>', $string);
  }

  protected function implodeCallable(&$array) {
    $array = implode('>', $array);
  }

  protected function encode2ndValue(&$value) {
    if (is_array($value) && !empty($value)) {
      $value[count($value) - 1] = self::htmlEncode($value[count($value) - 1]);
    }
  }

  protected function encodeSkipHtml($string) {
    $lt = explode('<', $string);
    array_walk($lt, array($this, 'explodeCallable'));
    array_walk($lt, array($this, 'encode2ndValue'));
    array_walk($lt, array($this, 'implodeCallable'));
    // Now every [0] element in all subarray is a HTML tag
    $string = implode('<', $lt);

    return $string;
  }

  public static function camelToUnderscore($string) {
    return strtolower(trim(preg_replace(/** @lang RegExp */ '/(?<!^)[A-Z]/', '_\0', $string), '_'));
  }

  /**
   * Just format number to Cyrillic format
   *
   * @param int|float $number
   * @param int       $decimals
   */
  public static function numberFormat($number, $decimals) {
    return number_format($number, $decimals, ',', '.');
  }

  /**
   * Formats FLOORED (!) number to string in Cyrillic format
   *
   * @param int|float $number
   * @param int       $decimals
   *
   * @return string
   */
  public static function numberFloorAndFormat($number) {
    return static::numberFormat(floor($number), 0);
  }

}
