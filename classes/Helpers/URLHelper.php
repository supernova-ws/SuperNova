<?php
/**
 * Created by Gorlum 25.11.2017 22:10
 */

namespace Helpers;


class URLHelper {

  public static function addParam($url, $param) {
    return $url . (strpos($url, '?') === false ? '?' : '&') . $param;
  }

}
