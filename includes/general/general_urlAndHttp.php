<?php
/**
 * Created by Gorlum 04.12.2017 4:45
 */

// ----------------------------------------------------------------------------------------------------------------
function sys_redirect($url) {
  header("Location: {$url}");
  ob_end_flush();
  die();
}

/**
 * Redirects via JS-script
 *
 * @param string $url
 */
function sys_redirect_js($url) {
  ob_end_flush();

  $redirectTemplate = SnTemplate::gettemplate('_redirect');
  $redirectTemplate->assign_vars(array(
    'URL' => js_safe_string($url),
  ));

  SnTemplate::display($redirectTemplate);
  die();
}


// ----------------------------------------------------------------------------------------------------------------
/**
 * Wrapper for header() function
 *
 * @param string $header
 */
function setHeader($header) {
  header($header);
}


// ----------------------------------------------------------------------------------------------------------------
function sn_get_url_contents($url) {
  if (function_exists('curl_init')) {
    $crl = curl_init();
    $timeout = 5;
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $return = curl_exec($crl);
    curl_close($crl);
  } else {
    $return = @file_get_contents($url);
  }

  return $return;
}

/**
 * @param $url
 * @param array $data
 *
 * @return bool|false|string
 */
function sn_post_url_contents($url, $data) {
  if (function_exists('curl_init')) {
    $crl = curl_init();
    $timeout = 5;
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($crl, CURLOPT_POST, true);
    curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
    $return = curl_exec($crl);

//    var_dump('error: ' . curl_error($crl));

    curl_close($crl);
  } else {
    $return = @file_get_contents($url);
  }

  return $return;
}

function invokeUrl($url) {
  exec("curl $url > /dev/null 2>&1 &");
}

// ----------------------------------------------------------------------------------------------------------------
function sn_setcookie($name, $value = null, $expire = null, $path = SN_ROOT_RELATIVE, $domain = null, $secure = null, $httponly = null) {
  $_COOKIE[$name] = $value;

  return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}


// ----------------------------------------------------------------------------------------------------------------
/**
 * Возвращает информацию об IPv4 адресах пользователя
 *
 * НЕ ПОДДЕРЖИВАЕТ IPv6!
 *
 * @return array
 */
function sec_player_ip() {
  // TODO - IPv6 support
  $ip = array(
    'ip'          => $_SERVER["REMOTE_ADDR"],
    'proxy_chain' => $_SERVER["HTTP_X_FORWARDED_FOR"]
      ? $_SERVER["HTTP_X_FORWARDED_FOR"]
      : ($_SERVER["HTTP_CLIENT_IP"]
        ? $_SERVER["HTTP_CLIENT_IP"]
        : '' // $_SERVER["REMOTE_ADDR"]
      ),
  );

  // Quick hack to support IPv6 at least on local host
  if($ip['ip'] == '::1') {
    $ip['ip'] = '127.0.0.1';
  }

  return array_map('db_escape', $ip);
}
