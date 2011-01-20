<?php
/**
 * @filename sys_security.php
 * @previously CheckCookies.php & CheckUser.php
 * @description Security-related functions
 * @package supernova
 * @version 2
 *
 * Revision History
 * ================
 *  2.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *    [~] Merged CheckCookie into sn_autologin
 *    [~] Rewrote internal logic
 *    [~] Cookie now cleared when there is error with it
 *  1.1 - copyright 2008 By Chlorel for XNova
 */
// TheCookie[0] = `id`
// TheCookie[1] = `username`
// TheCookie[2] = md5(Password . '--' . SecretWord)
// TheCookie[3] = rememberme

function sn_set_cookie($user, $rememberme)
{
  global $config;

  if ($rememberme)
  {
    $expiretime = $GLOBALS['time_now'] + 31536000;
    $rememberme = 1;
  }
  else
  {
    $expiretime = 0;
    $rememberme = 0;
  }

  $md5pass = md5("{$user['password']}--{$config->secret_word}");
  $cookie = "{$user['id']}/%/{$user['username']}/%/{$md5pass}/%/{$rememberme}";
  $result = setcookie($config->COOKIE_NAME, $cookie, $expiretime, '/', '', 0);
}

function sn_autologin($abort = true)
{
  global $config, $IsUserChecked;
  $lang = $GLOBALS['lang'];
  $time_now = $GLOBALS['time_now'];

  $IsUserChecked = false;
  if (!isset($_COOKIE[$config->COOKIE_NAME]))
  {
    return false;
  }

  $TheCookie  = explode("/%/", $_COOKIE[$config->COOKIE_NAME]);
  $TheCookie[0] = intval($TheCookie[0]);
  $TheCookie[1] = mysql_real_escape_string($TheCookie[1]);
  $user = doquery("SELECT * FROM `{{users}}` WHERE `id` = '{$TheCookie[0]}' LIMIT 1;", '', true);

  if (!$user || md5("{$user['password']}--{$config->secret_word}") !== $TheCookie[2])
  {
    setcookie($config->COOKIE_NAME, "", time() - 3600*25);
    if($abort)
    {
      message($lang['err_cookie']);
    }
    return false;
  }

  sn_set_cookie($user, $TheCookie[3]);
  sys_user_options_unpack($user);

  $ip = sys_get_user_ip();
  $user_agent = mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);
  doquery("UPDATE `{{users}}` SET `onlinetime`  = '{$time_now}', `user_lastip` = '{$ip['client']}', `user_proxy`  = '{$ip['proxy']}', `user_agent`  = '{$user_agent}' WHERE `id` = '{$user['id']}' LIMIT 1;");

  if(!$GLOBALS['skip_ban_check'] && $user['banaday'])
  {
    if ($user['banaday'] > $time_now)
    {
      $bantime = date(FMT_DATE_TIME, $user['banaday']);
      die ("{$lang['sys_banned_msg']} {$bantime}");
    }
    doquery("UPDATE {{users}} SET bana=0, `vacation` = '{$time_now}', banaday=0 WHERE id='{$user['id']}' LIMIT 1;");
  }

  $IsUserChecked = is_array($user);

  return $user;
}

function sn_login($username, $password, $remember_me = '1')
{
  global $lang;

  $username = mysql_real_escape_string($username);

  $login = doquery("SELECT * FROM {{users}} WHERE `username` = '{$username}' LIMIT 1;", '', true);
  if (!$login)
  {
    $status = LOGIN_ERROR_USERNAME;
    $error_msg = $lang['Login_FailUser'];
    $login = array();
  }
  elseif ($login['password'] != md5($password))
  {
    $status = LOGIN_ERROR_PASSWORD;
    $error_msg = $lang['Login_FailPassword'];
    $login = array();
  }
  else
  {
    sys_user_options_unpack($login);
    sn_set_cookie($login, $remember_me);
    $status = LOGIN_SUCCESS;
    $error_msg = '';
  }

  return array('status' => $status, 'error_msg' => $error_msg, 'user_row' => $login);
}

function sys_is_multiaccount($user1, $user2)
{
 return $user1['user_lastip'] == $user2['user_lastip'];
}

?>
