<?php

function sn_setcookie($name, $value = null, $expire = null, $path = SN_ROOT_RELATIVE, $domain = null, $secure = null, $httponly = null) {
  $_COOKIE[$name] = $value;
  return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

function sec_player_ip() {
  $ip = array(
    'ip' => $_SERVER["REMOTE_ADDR"],
    'proxy_chain' => $_SERVER["HTTP_X_FORWARDED_FOR"]
      ? $_SERVER["HTTP_X_FORWARDED_FOR"]
      : ($_SERVER["HTTP_CLIENT_IP"]
        ? $_SERVER["HTTP_CLIENT_IP"]
        : '' // $_SERVER["REMOTE_ADDR"]
        ),
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

function sn_set_cookie($user, $rememberme) {
  global $config, $time_now;

  if($rememberme) {
    $expiretime = $time_now + 31536000;
    $rememberme = 1;
  } else {
    $expiretime = 0;
    $rememberme = 0;
  }

  $md5pass = md5("{$user['password']}--{$config->secret_word}");
  $cookie = "{$user['id']}/%/{$user['username']}/%/{$md5pass}/%/{$rememberme}";
  return sn_setcookie(SN_COOKIE, $cookie, $expiretime, SN_ROOT_RELATIVE);
}

function sec_login_username($username_unsafe, $password_raw, $remember_me = 1) {
  // TODO - Логин по старым именам
  // $status = LOGIN_UNDEFINED;
  $username_safe = mysql_real_escape_string($username_unsafe);
  if(!$username_safe)
  {
    $status = LOGIN_ERROR_USERNAME;
  } elseif(!$password_raw) {
    $status = LOGIN_ERROR_PASSWORD;
  } else {
    $user = db_user_by_username($username_unsafe);
    // TODO: try..catch
    if(empty($user) || (isset($user['user_as_ally']) && $user['user_as_ally'])) {
      $status = LOGIN_ERROR_USERNAME;
    } elseif(!$user['password'] || $user['password'] != md5($password_raw)) {
      $status = LOGIN_ERROR_PASSWORD;
    } else {
      sec_set_cookie_by_fields($user['id'], $user['username'], $user['password'], $remember_me);
      $status = LOGIN_SUCCESS;
    }
  }
  // Это можно раскомментить для большей безопасности - что бы не подбирали пароли
  // $status = $status == LOGIN_ERROR_PASSWORD ? LOGIN_ERROR_USERNAME : $status;

  return $status;
}









function sec_restore_password_send_email($email_unsafe) {
  global $lang, $config;

  try {
    $user_id = db_user_by_email($email_unsafe, false, false, 'id, authlevel');
    if(!isset($user_id['id']) || !$user_id['id']) {
      throw new exception(PASSWORD_RESTORE_ERROR_WRONG_EMAIL);
    }

    if($user_id['authlevel']) {
      throw new exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT);
    }


    // TODO - уникальный индекс по id_user и type - и делать не INSERT, а REPLACE
    $last_confirm = doquery("SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `id_user`= '{$user_id['id']}' AND `type` = " . CONFIRM_PASSWORD_RESET . " LIMIT 1;", true);
    if(isset($last_confirm['unix_time']) && SN_TIME_NOW - $last_confirm['unix_time'] < PERIOD_HOUR) {
      throw new exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN);
    }
    doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id']}' LIMIT 1;");

    do {
      $confirm_code = sys_random_string(8, SN_SYS_SEC_CHARS_CONFIRMATION);
      $confirm_code_safe = mysql_real_escape_string($confirm_code);
      $query = doquery("SELECT count(*) FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}'", true);
    } while(!$query);
    $email_safe = mysql_real_escape_string($email_unsafe);
    doquery("INSERT INTO {{confirmations}} SET `id_user`= '{$user_id['id']}', `type` = " . CONFIRM_PASSWORD_RESET . ", `code` = '{$confirm_code}', `email` = '{$email_safe}';");

    @$result = mymail($email_unsafe, sprintf($lang['log_lost_email_title'], $config->game_name), sprintf($lang['log_lost_email_code'], SN_ROOT_VIRTUAL . $_SERVER['PHP_SELF'], $confirm_code, date(FMT_DATE_TIME, SN_TIME_NOW + 3*24*60*60), $config->game_name));

    $result = $result ? PASSWORD_RESTORE_SUCCESS_CODE_SENT : PASSWORD_RESTORE_ERROR_SENDING;
  } catch(exception $e) {
     $result = $e->getMessage();
  }

  return $result;
}


function sec_restore_password_confirm($confirm_safe, &$result) {
  global $lang, $config;

  try {
    $last_confirm = doquery("SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `code` = '{$confirm_safe}' LIMIT 1;", true);
    if(!isset($last_confirm['id'])) {
      throw new exception(PASSWORD_RESTORE_ERROR_CODE_WRONG);
    }

    if(SN_TIME_NOW - $last_confirm['unix_time'] > PERIOD_DAY) {
      throw new exception(PASSWORD_RESTORE_ERROR_CODE_TOO_OLD);
    }


    $new_password = sys_random_string(8, SN_SYS_SEC_CHARS_CONFIRMATION);
    $md5 = md5($new_password);
    if(!db_user_set_by_id($last_confirm['id_user'], "`password` = '{$md5}'")) {
      throw new exception(PASSWORD_RESTORE_ERROR_CHANGE);
    }

    $message = sprintf($lang['log_lost_email_pass'], $config->game_name, $new_password);
    @$operation_result = mymail($last_confirm['email'], sprintf($lang['log_lost_email_title'], $config->game_name), htmlspecialchars($message));
    $message = sys_bbcodeParse($message) . '<br><br>';

    $result[F_PASSWORD_NEW] = $new_password;
    $result[F_LOGIN_STATUS] = $operation_result ? PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT : PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR;
    $result[F_LOGIN_MESSAGE] = $message . ($operation_result ? $lang['log_lost_sent_pass'] : $lang['log_lost_err_sending']);
    doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id_user']}' LIMIT 1;");
    /*
    message($message, $lang['log_lost_header']);



    if($last_confirm['id'] && ($time_now - $last_confirm['unix_time'] <= 3*24*60*60)) {

      $user_data = db_user_by_id($last_confirm['id_user']);
      if(!$user_data['id']) {
        message($lang['log_lost_err_code'], $lang['sys_error']);
      }

      if($user_data['authlevel']) {
        message($lang['log_lost_err_admin'], $lang['sys_error']);
      }


    }
    else {
      message($lang['log_lost_err_code'], $lang['sys_error']);
    }
    */
  } catch(exception $e) {
    $result[F_LOGIN_STATUS] = $e->getMessage();
  }
}


// once OK
function sec_login() {
  $result = array(
    F_DEVICE_ID => -1,
    F_DEVICE_CYPHER => $_COOKIE[SN_COOKIE_D],
    F_LOGIN_STATUS => LOGIN_UNDEFINED,
    F_LOGIN_MESSAGE => '',
    F_LOGIN_USER => array(),
    AUTH_LEVEL => AUTH_LEVEL_ANONYMOUS,
    F_BANNED_STATUS => 0,
    F_VACATION_STATUS => 0,
    F_PASSWORD_NEW => '',
  );

  sn_db_transaction_start();
  if($result[F_DEVICE_CYPHER]) {
    $cypher_safe = mysql_real_escape_string($result[F_DEVICE_CYPHER]);
    $device_id = doquery("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE", true);
    if(isset($device_id['device_id']) && $device_id['device_id']) {
      $result[F_DEVICE_ID] = $device_id['device_id'];
    }
  }

  if($result[F_DEVICE_ID] <= 0) {
    do {
      $cypher_safe = mysql_real_escape_string($result[F_DEVICE_CYPHER] = sys_random_string());
      $row = doquery("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE", true);
    } while (!empty($row));
    doquery("INSERT INTO {{security_device}} (`device_cypher`) VALUES ('{$cypher_safe}');");
    $result[F_DEVICE_ID] = mysql_insert_id();
    sn_setcookie(SN_COOKIE_D, $result[F_DEVICE_CYPHER], PERIOD_FOREVER, SN_ROOT_RELATIVE);
  }
  sn_db_transaction_commit();

//  $cypher_safe = mysql_real_escape_string($_COOKIE[SN_COOKIE_D]);
//  $device_id = doquery("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1;", true);
//  $result[F_DEVICE_CYPHER] = $_COOKIE[SN_COOKIE_D];
//  $result[F_DEVICE_ID] = $device_id['device_id'];

  $username_unsafe = sys_get_param_str_unsafe('username');
  $password_raw = sys_get_param('password');
//  pdump($username_unsafe);
//  pdump($password_raw);

  // Проверяем регу
  if(sys_get_param('register')) {
    $result[F_LOGIN_STATUS] = sec_login_register($username_unsafe, $password_raw, sys_get_param_int('rememberme'));
  }

  // Если есть в параметрах логин и пароль...
//  if($username_unsafe && $password_raw) {
//    }

  if(sys_get_param('login') && in_array($result['status'], array(LOGIN_UNDEFINED, REGISTER_SUCCESS))) {
    $result[F_LOGIN_STATUS] = sec_login_username($username_unsafe, $password_raw, sys_get_param_int('rememberme'));
  } elseif(sys_get_param('confirm_code_send') && $email_unsafe = sys_get_param_str_unsafe('email')) {
    // TODO - test
    $result[F_LOGIN_STATUS] = sec_restore_password_send_email($email_unsafe);
  } elseif(sys_get_param('confirm_code_submit') && $confirm_safe = sys_get_param_str('confirm')) {
    // TODO - test
    sec_restore_password_confirm($confirm_safe, $result);
  }
  // Тут всякие логины по внешним плагинам
//pdump($result, 'security');
  // В этой точке должен быть установлена кука СН - логинимся по ней
  if(in_array($result['status'], array(LOGIN_UNDEFINED, REGISTER_SUCCESS))) {
    sec_login_cookie($result);
  }

  // TODO -          ЗАМЕНИТЬ F_LOGIN_MESSAGE       на сообщения по   F_LOGIN_STATUS

  return $result;
}

// once OK
function sec_login_cookie(&$result) {
  global $user_impersonator;

  // Проверяем куку имперсонатора на доступ
  if($_COOKIE[SN_COOKIE_I]) {
    $user_impersonator = sec_user_by_cookie($_COOKIE[SN_COOKIE_I]);
    if(empty($user_impersonator) || $user_impersonator['authlevel'] < 3) {
      sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
      sn_setcookie(SN_COOKIE_I, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
    }
  }

  $result[F_LOGIN_USER] = array();
  // Пытаемся войти по куке
  if(!isset($_COOKIE[SN_COOKIE]) || !$_COOKIE[SN_COOKIE]) {
    // Ошибка кукеса или не найден пользователь по кукесу
    sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
    if(!empty($user_impersonator)) {
      // Если это был корректный имперсонатор - просто выходим и редиректимся в админку
      sn_sys_logout();
    }
  } else {
    $result[F_LOGIN_USER] = sec_user_by_cookie($_COOKIE[SN_COOKIE]);
    if(empty($result[F_LOGIN_USER])) {
      $result[F_LOGIN_STATUS] = LOGIN_UNDEFINED;
    } else {
      $result[AUTH_LEVEL] = $result[F_LOGIN_USER]['authlevel'];
      sec_login_process($result);
    }
  }
}

// once OK
function sec_login_process(&$result) {
  $user = &$result[F_LOGIN_USER];
  sys_user_options_unpack($user);

  if($user['banaday'] && $user['banaday'] <= SN_TIME_NOW) {
    $user['banaday'] = 0;
    $user['vacation'] = SN_TIME_NOW;
  }

  $ip = sec_player_ip();
  $user['user_lastip'] = $ip['ip'];
  $user['user_proxy'] = $ip['proxy_chain'];

  $result[F_BANNED_STATUS] = $user['banaday'];
  $result[F_VACATION_STATUS] = $user['vacation'];
}

function sec_login_change_state() {
  global $user, $user_impersonator, $template_result;

  if(isset($user['id']) && intval($user['id']) && !$user_impersonator) {
    sn_db_transaction_start();
    $browser_safe = mysql_real_escape_string($template_result[F_BROWSER] = $_SERVER['HTTP_USER_AGENT']);
    $browser_id = doquery("SELECT `browser_id` FROM {{security_browser}} WHERE `browser_user_agent` = '{$browser_safe}' LIMIT 1 FOR UPDATE", true);
    if(!isset($browser_id['browser_id']) || !$browser_id['browser_id']) {
      doquery("INSERT INTO {{security_browser}} (`browser_user_agent`) VALUES ('{$browser_safe}');");
      $template_result[F_BROWSER_ID] = mysql_insert_id();
    } else {
      $template_result[F_BROWSER_ID] = $browser_id['browser_id'];
    }
    $proxy_safe = mysql_real_escape_string($user['user_proxy']);
    doquery(
      "INSERT IGNORE INTO {{security_player_entry}} (`player_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`)
        VALUES ({$user['id']},{$template_result[F_DEVICE_ID]},{$template_result[F_BROWSER_ID]},INET_ATON('{$user['user_lastip']}'), '{$proxy_safe}');"
    );
    sn_db_transaction_commit();

    //if(in_array($template_result[F_DEVICE_ID], array(463735, 86823))) {
    //  die();
    //}

    db_user_set_by_id($user['id'], "`onlinetime` = " . SN_TIME_NOW . ", `banaday` = {$user['banaday']}, `vacation` = {$user['vacation']},
      `user_lastip` = '{$user['user_lastip']}', `user_last_proxy` = '{$proxy_safe}', `user_last_browser_id` = {$template_result[F_BROWSER_ID]}"
    );
  }
}

// once OK
function sec_login_register($username_unsafe, $password_raw, $remember_me = 1) {
  global $lang, $config;


  sn_db_transaction_start();
  try {
    if($config->game_mode == GAME_BLITZ) {
      throw new exception(REGISTER_ERROR_USERNAME_WRONG, ERR_ERROR);
    }

    if(!$username_unsafe) {
      throw new exception(REGISTER_ERROR_USERNAME_WRONG, ERR_ERROR);
    }

    $username_safe = mysql_real_escape_string($username_unsafe);
    $db_check = doquery("SELECT `player_id` FROM {{player_name_history}} WHERE `player_name` = '{$username_safe}' LIMIT 1;", true);
    if(!empty($db_check)) {
      throw new exception(REGISTER_ERROR_USERNAME_EXISTS, ERR_ERROR);
    }

    if(strlen(trim($password_raw)) < 4 || strlen(trim($password_raw)) <> strlen($password_raw)) {
      throw new exception(REGISTER_ERROR_PASSWORD_INSECURE, ERR_ERROR);
    }
    $password_raw = trim($password_raw);

    $password_repeat_raw = trim(sys_get_param('password_repeat'));
    if($password_raw <> $password_repeat_raw) {
      throw new exception(REGISTER_ERROR_PASSWORD_DIFFERENT, ERR_ERROR);
    }

    $email_unsafe = sys_get_param_str_unsafe('email');
    $email = sys_get_param_str('email');
    if(db_user_by_email($email, true)) {
      throw new exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
    }

    $md5pass = md5($password_raw);
    $language = sys_get_param_str('lang', DEFAULT_LANG);
    $skin = DEFAULT_SKINPATH;
    // `id_planet` = 0, `gender` = '{$gender}', `design` = '1',
    $user_new = classSupernova::db_ins_record(LOC_USER, "`email` = '{$email}', `email_2` = '{$email}', `username` = '{$username_safe}', `dpath` = '{$skin}',
      `lang` = '{$language}', `register_time` = " . SN_TIME_NOW . ", `password` = '{$md5pass}',
      `options` = 'opt_mnl_spy^1|opt_email_mnl_spy^0|opt_email_mnl_joueur^0|opt_email_mnl_alliance^0|opt_mnl_attaque^1|opt_email_mnl_attaque^0|opt_mnl_exploit^1|opt_email_mnl_exploit^0|opt_mnl_transport^1|opt_email_mnl_transport^0|opt_email_msg_admin^1|opt_mnl_expedition^1|opt_email_mnl_expedition^0|opt_mnl_buildlist^1|opt_email_mnl_buildlist^0|opt_int_navbar_resource_force^1|';");

    $user['id'] = $user_new['id'];
    doquery("REPLACE INTO {{player_name_history}} SET `player_id` = {$user['id']}, `player_name` = \"{$username_safe}\"");

    if($id_ref = sys_get_param_int('id_ref')) {
      $referral_row = db_user_by_id($id_ref, true);
      if($referral_row) {
        doquery("INSERT INTO {{referrals}} SET `id` = {$user['id']}, `id_partner` = {$id_ref}");
      }
    }

    $galaxy = $config->LastSettedGalaxyPos;
    $system = $config->LastSettedSystemPos;
    $segment_size = floor($config->game_maxPlanet / 3);
    $segment = floor($config->LastSettedPlanetPos / $segment_size);
    $segment++;
    $planet = mt_rand(1 + $segment * $segment_size, ($segment + 1) * $segment_size);

    $new_planet_id = 0;
    while(true) {
      if($planet > $config->game_maxPlanet) {
        $planet = mt_rand(0, $segment_size - 1) + 1;
        $system++;
      }
      if($system > $config->game_maxSystem) {
        $system = 1;
        $galaxy++;
      }
      $galaxy = $galaxy > $config->game_maxGalaxy ? 1 : $galaxy;

      $galaxy_row = db_planet_by_gspt($galaxy, $system, $planet, PT_PLANET, true, 'id');
      if(!$galaxy_row['id']) {
        $config->db_saveItem(array(
          'LastSettedGalaxyPos' => $galaxy,
          'LastSettedSystemPos' => $system,
          'LastSettedPlanetPos' => $planet
        ));
        $new_planet_id = uni_create_planet($galaxy, $system, $planet, $user['id'], $username_unsafe . ' ' . $lang['sys_capital'], true);
        break;
      }
      $planet += 3;
    }

    sys_player_new_adjust($user['id'], $new_planet_id);

    db_user_set_by_id($user['id'], "`id_planet` = '{$new_planet_id}', `current_planet` = '{$new_planet_id}', `galaxy` = '{$galaxy}', `system` = '{$system}', `planet` = '{$planet}'");

    $config->db_saveItem('users_amount', $config->users_amount + 1);

    sn_db_transaction_commit();

    $email_message  = sprintf($lang['log_reg_email_text'], $config->game_name, SN_ROOT_VIRTUAL, sys_safe_output($username_unsafe), sys_safe_output($password_raw));
    @mymail($email_unsafe, sprintf($lang['log_reg_email_title'], $config->game_name), $email_message);

    sec_set_cookie_by_fields($user['id'], $username_unsafe, $md5pass, $remember_me);

    $result = REGISTER_SUCCESS;
  } catch(exception $e) {
    sn_db_transaction_rollback();
    $result = $e->getMessage();
  }

  return $result;
}

// twice OK
function sec_set_cookie_by_fields($user_id, $username_unsafe, $password_hash, $remember_me) {
  global $config;

  $expire_time = ($remember_me = intval($remember_me)) ? SN_TIME_NOW + PERIOD_YEAR : 0;

  $md5pass = md5("{$password_hash}--{$config->secret_word}");
  $cookie = "{$user_id}/%/{$username_unsafe}/%/{$md5pass}/%/{$remember_me}";

  return sn_setcookie(SN_COOKIE, $cookie, $expire_time, SN_ROOT_RELATIVE);
}

// 2 OK
// 2 deprecated SN_AUTOLOGIN
function sec_user_by_cookie($cookie) {
  global $config;

  list($user_id_unsafe, $user_name, $password_hash_salted, $user_remember_me) = explode("/%/", $cookie);

  $user = db_user_by_id($user_id_unsafe, false, '*', true);
  if(!empty($user) && md5("{$user['password']}--{$config->secret_word}") == $password_hash_salted) {
    $user['user_remember_me'] = $user_remember_me;
  } else {
    $user = false;
  }

  return $user;
}














function sys_is_multiaccount($user1, $user2) {
  global $config;

  return $user1['user_lastip'] == $user2['user_lastip'] && !$config->game_multiaccount_enabled;
}

function sn_sys_impersonate($user_selected) {
  global $user;

  if($_COOKIE[SN_COOKIE_I]) {
    die('You already impersonating someone. Go back to living other\'s life! Or clear your cookies and try again'); // TODO: Log it
  }

  if($user['authlevel'] < 3) {
    die('You can\'t impersonate - too low level'); // TODO: Log it
  }

  sn_setcookie(SN_COOKIE_I, $_COOKIE[SN_COOKIE], 0, SN_ROOT_RELATIVE);
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
function sn_sys_logout($redirect = true, $only_impersonator = false) {
  global $user_impersonator;

  if($only_impersonator && !$user_impersonator) {
    return;
  }

  if($_COOKIE[SN_COOKIE_I] && $user_impersonator['authlevel'] >= 3) {
    sn_set_cookie($user_impersonator, 1);
    $redirect = $redirect === true ? 'admin/userlist.php' : $redirect;
  } else {
    sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
  }

  sn_setcookie(SN_COOKIE_I, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);

  if($redirect === true) {
    sys_redirect(SN_ROOT_RELATIVE . 'login.php');
  } elseif($redirect !== false) {
    sys_redirect($redirect);
  }
}

function DeleteSelectedUser ($UserID) {
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


  classSupernova::db_del_record_by_id(LOC_USER, $UserID);
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

  db_planet_set_by_owner($banned['id'],
    "`metal_mine_porcent` = 0, `crystal_mine_porcent` = 0, `deuterium_sintetizer_porcent` = 0, `solar_plant_porcent` = 0,
    `fusion_plant_porcent` = 0, `solar_satelit_porcent` = 0, `ship_sattelite_sloth_porcent` = 0"
  );
}

function sys_admin_player_ban_unset($banner, $banned, $reason = '') {
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
