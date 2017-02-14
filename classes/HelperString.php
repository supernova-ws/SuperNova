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
    $params & HTML_ENCODE_PREFORM ? $string = htmlentities($string, ENT_COMPAT, 'UTF-8') : false;
    $params & HTML_ENCODE_NL2BR ? $string = self::nl2br($string) : false;
    $params & HTML_ENCODE_JS_SAFE ? $string = str_replace(array("\r", "\n"), array('\r', '\n'), addslashes($string)) : false;

    return $string;
  }

  /**
   * @param $string
   *
   * @return mixed
   */
  public static function nl2br($string) {
    return str_replace(array("\r", "\n"), '', nl2br($string));
  }

  protected function explodeCallable(&$string) {
    $string = explode('>', $string);
  }

  protected function implodeCallable(&$array) {
    $array = implode('>', $array);
  }

  protected function encode2ndValue(&$value) {
    var_dump($value);

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

}
