<?php

function sys_get_user_ip()
{
  $ip = array(
    'proxy' => $_SERVER["HTTP_X_FORWARDED_FOR"] ? ($_SERVER["HTTP_CLIENT_IP"] ? $_SERVER["HTTP_CLIENT_IP"] : $_SERVER["REMOTE_ADDR"]) : '',
    'client' => $_SERVER["HTTP_X_FORWARDED_FOR"] ? $_SERVER["HTTP_X_FORWARDED_FOR"] : 
      ($_SERVER["HTTP_CLIENT_IP"] ? $_SERVER["HTTP_CLIENT_IP"] : $_SERVER["REMOTE_ADDR"]),
  );

  return array_map('mysql_real_escape_string', $ip);
}

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
  global $config, $time_now;

  if($rememberme)
  {
    $expiretime = $time_now + 31536000;
    $rememberme = 1;
  }
  else
  {
    $expiretime = 0;
    $rememberme = 0;
  }

  $md5pass = md5("{$user['password']}--{$config->secret_word}");
  $cookie = "{$user['id']}/%/{$user['username']}/%/{$md5pass}/%/{$rememberme}";
  return setcookie(SN_COOKIE, $cookie, $expiretime, SN_ROOT_RELATIVE);
}

function sn_sys_cookie_check($cookie)
{
  global $config;

  list($user_id, $user_name, $user_pass_hash, $user_remember_me) = explode("/%/", $cookie);
  $user_name = mysql_real_escape_string($user_name);

  $user_id = intval($user_id);
  $user = db_user_player_by_id($user_id);
  if(!$user || md5("{$user['password']}--{$config->secret_word}") != $user_pass_hash)
  {
    $user = false;
  }
  else
  {
    $user['user_remember_me'] = $user_remember_me;
  }

  return $user;
}

function sn_autologin($abort = true)
{
  global $config, $IsUserChecked, $user_impersonator, $time_now, $lang, $skip_ban_check;

  $IsUserChecked = false;
  if(!isset($_COOKIE[SN_COOKIE]))
  {
    return false;
  }

  if($_COOKIE[SN_COOKIE_I])
  {
    $user_impersonator = sn_sys_cookie_check($_COOKIE[SN_COOKIE_I]);
    if(!$user_impersonator || $user_impersonator['authlevel'] < 3)
    {
      // TODO: Log here
      setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
      setcookie(SN_COOKIE_I, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
      sys_redirect(SN_ROOT_RELATIVE);
    }
  }
  if(!$user = sn_sys_cookie_check($_COOKIE[SN_COOKIE]))
  {
    setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
    if($user_impersonator)
    {
      sn_sys_logout();
    }
    if($abort)
    {
      message($lang['err_cookie']);
    }
    return false;
  }

  sys_user_options_unpack($user);
  sn_set_cookie($user, $user['user_remember_me']);

  $ip = sys_get_user_ip();
  $user_agent = mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);
  db_user_set_by_id($user['id'], "`onlinetime` = " . SN_TIME_NOW .
    (!$user_impersonator ? ", `user_lastip` = '{$ip['client']}', `user_proxy`  = '{$ip['proxy']}', `user_agent`  = '{$user_agent}'" : ''));

  if(!$skip_ban_check && $user['banaday'])
  {
    if($user['banaday'] > $time_now)
    {
      $bantime = date(FMT_DATE_TIME, $user['banaday']);
      if($abort)
      {
        // TODO: Add ban reason. Add vacation time. Add message window
        sn_sys_logout(false, true);
        message("{$lang['sys_banned_msg']} {$bantime}", $lang['ban_title']);
        die("{$lang['sys_banned_msg']} {$bantime}");
      }
      else
      {
        unset($user);
      }
    }
    else
    {
      db_user_set_by_id($user['id'], "`vacation` = '{$time_now}', banaday=0");
    }
  }

  $IsUserChecked = is_array($user);

  return $user;
}

function sn_login($username, $password, $remember_me = 1)
{
  global $lang;

  $login = array();
  $username_safe = mysql_real_escape_string($username);
  if(!$username_safe || !$password)
  {
    $status = LOGIN_ERROR_USERNAME;
    $error_msg = $lang['Login_FailUser'];
  }
  else
  {
    $login = db_user_by_username($username);
    {
      // TODO: try..catch
      if(!is_array($login) || (isset($login['user_as_ally']) && $login['user_as_ally']))
      {
        $status = LOGIN_ERROR_USERNAME;
        $error_msg = $lang['Login_FailUser'];
        $login = array();
      }
      elseif(!$login['password'] || $login['password'] != md5($password))
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
        // break;
      }
    }

    if(empty($login))
    {
      $status = LOGIN_ERROR_USERNAME;
      $error_msg = $lang['Login_FailUser'];
      $login = array();
    }
  }

  return array('status' => $status, 'error_msg' => $error_msg, 'user_row' => $login);
}

function sys_is_multiaccount($user1, $user2)
{
  global $config;

 return $user1['user_lastip'] == $user2['user_lastip'] && !$config->game_multiaccount_enabled;
}

function sn_sys_impersonate($user_selected)
{
  global $user;

  if($_COOKIE[SN_COOKIE_I])
  {
    // TODO: Log here
    die('You already impersonating someone. Go back to living other\'s life! Or clear your cookies and try again');
  }

  if($user['authlevel'] < 3)
  {
    // TODO: Log here
    die('You can\'t impersonate - too low level');
  }

  setcookie(SN_COOKIE_I, $_COOKIE[SN_COOKIE], 0, SN_ROOT_RELATIVE);
  sn_set_cookie($user_selected, 0);
  sys_redirect(SN_ROOT_RELATIVE);
}

//
// Log outs user from game. Cancels impersonate if user impersonated
// 
// $redirect manages what happens after logout
//   true  - redirect to main page
//   false - do not redirect
//   'string' - redirect to 'string' URL
//
function sn_sys_logout($redirect = true, $only_impersonator = false)
{
  global $user_impersonator, $config;

  if($only_impersonator && !$user_impersonator)
  {
    return;
  }

  if($_COOKIE[SN_COOKIE_I] && $user_impersonator['authlevel'] >= 3)
  {
    sn_set_cookie($user_impersonator, 1);
    $redirect = $redirect === true ? 'admin/userlist.php' : $redirect;
  }
  else
  {
    setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
  }

  setcookie(SN_COOKIE_I, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);

  if($redirect === true)
  {
    sys_redirect(SN_ROOT_RELATIVE);
  }
  elseif($redirect !== false)
  {
    sys_redirect($redirect);
  }
}

/**
 * DeleteSelectedUser.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function DeleteSelectedUser ( $UserID )
{
  // TODO: Full rewrite
  sn_db_transaction_start();
  $TheUser = db_user_by_id($UserID);
  if ( $TheUser['ally_id'] != 0 ) {
    $TheAlly = doquery ( "SELECT * FROM `{{alliance}}` WHERE `id` = '" . $TheUser['ally_id'] . "';", '', true );
    $TheAlly['ally_members'] -= 1;
    if ( $TheAlly['ally_members'] > 0 ) {
      doquery ( "UPDATE `{{alliance}}` SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';");
    } else {
      doquery ( "DELETE FROM `{{alliance}}` WHERE `id` = '" . $TheAlly['id'] . "';");
      doquery ( "DELETE FROM `{{statpoints}}` WHERE `stat_type` = '2' AND `id_owner` = '" . $TheAlly['id'] . "';");
    }
  }
  doquery ( "DELETE FROM `{{statpoints}}` WHERE `stat_type` = '1' AND `id_owner` = '" . $UserID . "';");

  db_planet_list_delete_by_owner($UserID);

  doquery ( "DELETE FROM `{{messages}}` WHERE `message_sender` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{messages}}` WHERE `message_owner` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{notes}}` WHERE `owner` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{fleets}}` WHERE `fleet_owner` = '" . $UserID . "';");
//  doquery ( "DELETE FROM `{{rw}}` WHERE `id_owner1` = '" . $UserID . "';");
//  doquery ( "DELETE FROM `{{rw}}` WHERE `id_owner2` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{buddy}}` WHERE `BUDDY_SENDER_ID` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{buddy}}` WHERE `BUDDY_OWNER_ID` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{annonce}}` WHERE `user` = '" . $UserID . "';");


  db_user_delete_by_id($UserID);
  doquery ( "DELETE FROM `{{referrals}}` WHERE (`id` = '{$UserID}') OR (`id_partner` = '{$UserID}');");
  global $config;
  $config->db_saveItem('users_amount', $config->users_amount - 1);
  sn_db_transaction_commit();
}

function sys_admin_player_ban($banner, $banned, $term, $is_vacation = true, $reason = '')
{
  global $time_now;

  $ban_current = db_user_by_id($banned['id'], false, 'banaday');
  $ban_until = ($ban_current['banaday'] ? $ban_current['banaday'] : $time_now) + $term;

  db_user_set_by_id($banned['id'], "`banaday` = {$ban_until} " . ($is_vacation ? ", `vacation` = '{$ban_until}' " : ''));

  $banned['username'] = mysql_real_escape_string($banned['username']);
  $banner['username'] = mysql_real_escape_string($banner['username']);
  doquery("
    INSERT INTO
      {{banned}}
    SET
      `ban_user_id` = '{$banned['id']}',
      `ban_user_name` = '{$banned['username']}',
      `ban_reason` = '{$reason}',
      `ban_time` = {$time_now},
      `ban_until` = {$ban_until},
      `ban_issuer_id` = '{$banner['id']}',
      `ban_issuer_name` = '{$banner['username']}',
      `ban_issuer_email` = '{$banner['email']}'
  ");

  db_planet_list_set_by_owner($banned['id'],
    "`metal_mine_porcent` = 0, `crystal_mine_porcent` = 0, `deuterium_sintetizer_porcent` = 0, `solar_plant_porcent` = 0,
    `fusion_plant_porcent` = 0, `solar_satelit_porcent` = 0, `ship_sattelite_sloth_porcent` = 0"
  );
}

function sys_admin_player_ban_unset($banner, $banned, $reason = '')
{
  global $time_now;

  db_user_set_by_id($banned['id'], "`banaday` = 0, `vacation` = {$time_now}");

  $banned['username'] = mysql_real_escape_string($banned['username']);
  $banner['username'] = mysql_real_escape_string($banner['username']);
  $reason = mysql_real_escape_string($reason);
  doquery("
    INSERT INTO {{banned}}
    SET
      `ban_user_id` = '{$banned['id']}',
      `ban_user_name` = '{$banned['username']}',
      `ban_reason` = '{$reason}',
      `ban_time` = 0,
      `ban_until` = '{$time_now}',
      `ban_issuer_id` = '{$banner['id']}',
      `ban_issuer_name` = '{$banner['username']}',
      `ban_issuer_email` = '{$banner['email']}'
  ");
}

?>
