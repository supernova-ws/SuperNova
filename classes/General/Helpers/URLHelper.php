<?php
/**
 * Created by Gorlum 25.11.2017 22:10
 */

namespace General\Helpers;


class URLHelper {

  public static function addParam($url, $param, $value = '') {
    list($urlItself, $strParams) = explode('?', $url);

    $paramList = explode('&', $strParams);
    foreach ($paramList as $key => $val) {
      list($thisName, $thisParam) = explode('=', $val);
      if ($thisName == $param) {
        unset($paramList[$key]);
      }
    }

    if(!empty($paramList)) {
      $strParams = implode('&', $paramList);
    }

    $url = $urlItself . '?' . $strParams;

    return $url . (strpos($url, '?') === false ? '?' : '&') . $param . '=' . $value;
  }

}
