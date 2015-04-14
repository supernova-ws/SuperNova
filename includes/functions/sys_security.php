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

  return array_map('db_escape', $ip);
}

function sec_password_cookie_encode($password) {
  global $config;

  return md5("{$password}--{$config->secret_word}");
}
/**
 * Функция кодирует пароль, просаливая его перед этим
 *
 * @param $password
 * @param $salt
 *
 * @return string
 */
function sec_password_encode($password, $salt) {
  return md5($password . $salt);
}
function sec_password_salt_generate() {
  // TODO ВКЛЮЧИТЬ ГЕНЕРАЦИЮ СОЛИ !!!
  return ''; // sys_random_string(16);
}


// TheCookie[0] = `id`
// TheCookie[1] = `username`
// TheCookie[2] = md5(Password . '--' . SecretWord)
// TheCookie[3] = rememberme
// DEPRECATED
//function sn_cookie_set_user($user, $rememberme) {
//  if($rememberme) {
//    $expiretime = SN_TIME_NOW + 31536000;
//    $rememberme = 1;
//  } else {
//    $expiretime = 0;
//    $rememberme = 0;
//  }
//
//  $md5pass = sec_password_cookie_encode($user['password']);
//  $cookie = "{$user['id']}/%/{$user['username']}/%/{$md5pass}/%/{$rememberme}";
//  return sn_setcookie(SN_COOKIE, $cookie, $expiretime, SN_ROOT_RELATIVE);
//}

//function sec_set_cookie_by_fields($user_id, $username_unsafe, $password_hash, $remember_me) {
//  $expire_time = ($remember_me = intval($remember_me)) ? SN_TIME_NOW + PERIOD_YEAR : 0;
//
//  $password_encoded = sec_password_cookie_encode($password_hash);
//  $cookie = "{$user_id}/%/{$username_unsafe}/%/{$password_encoded}/%/{$remember_me}";
//
//  return sn_setcookie(SN_COOKIE, $cookie, $expire_time, SN_ROOT_RELATIVE);
//}
function sec_set_cookie_by_user($user, $remember_me) {
  $expire_time = ($remember_me = intval($remember_me)) ? SN_TIME_NOW + PERIOD_YEAR : 0;

  $password_encoded = sec_password_cookie_encode($user['password']);
  $cookie = "{$user['id']}/%/{$user['username']}/%/{$password_encoded}/%/{$remember_me}";

  return sn_setcookie(SN_COOKIE, $cookie, $expire_time, SN_ROOT_RELATIVE);
}
function sec_cookie_user_check($cookie) {
  list($user_id_unsafe, $user_name, $password_hash_salted, $user_remember_me) = explode("/%/", $cookie);

  $user = db_user_by_id($user_id_unsafe, false, '*', true);
  if(!empty($user) && sec_password_cookie_encode($user['password']) == $password_hash_salted) {
    $user['user_remember_me'] = $user_remember_me;
  } else {
    $user = false;
  }

  return $user;
}

// Процедура по установке значения поля из словаря по данным
function sec_login_set_fields(&$template_result, $field_value, $security_field_name, $security_field_id, $db_field_id, $db_table_name, $db_field_name) {
  $browser_safe = db_escape($template_result[$security_field_name] = $field_value);
  $browser_id = doquery("SELECT `{$db_field_id}` AS id_field FROM {{{$db_table_name}}} WHERE `{$db_field_name}` = '{$browser_safe}' LIMIT 1 FOR UPDATE", true);
  if(!isset($browser_id['id_field']) || !$browser_id['id_field']) {
    doquery("INSERT INTO {{{$db_table_name}}} (`{$db_field_name}`) VALUES ('{$browser_safe}');");
    $template_result[$security_field_id] = db_insert_id();
  } else {
    $template_result[$security_field_id] = $browser_id['id_field'];
  }
}


function sec_login_prepare(&$result) {
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
    $cypher_safe = db_escape($result[F_DEVICE_CYPHER]);
    $device_id = doquery("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE", true);
    if(!empty($device_id['device_id'])) {
      $result[F_DEVICE_ID] = $device_id['device_id'];
    }
  }

  if($result[F_DEVICE_ID] <= 0) {
    do {
      $cypher_safe = db_escape($result[F_DEVICE_CYPHER] = sys_random_string());
      $row = doquery("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE", true);
    } while (!empty($row));
    doquery("INSERT INTO {{security_device}} (`device_cypher`) VALUES ('{$cypher_safe}');");
    $result[F_DEVICE_ID] = db_insert_id();
    sn_setcookie(SN_COOKIE_D, $result[F_DEVICE_CYPHER], PERIOD_FOREVER, SN_ROOT_RELATIVE);
  }

  sec_login_set_fields($result, $_SERVER['HTTP_USER_AGENT'], F_BROWSER, F_BROWSER_ID, 'browser_id', 'security_browser', 'browser_user_agent');
  sn_db_transaction_commit();
}

function sec_login(&$result) {
  sec_login_prepare($result);

  $username_unsafe = sys_get_param_str_unsafe('username');
  $password_raw = sys_get_param('password');
  $email_unsafe = sys_get_param_str_unsafe('email');

  // Проверяем регу
  if(sys_get_param('register')) {
    $password_repeat_raw = trim(sys_get_param('password_repeat'));
    $language = sys_get_param_str('lang', DEFAULT_LANG);
    if($password_raw <> $password_repeat_raw) {
      // throw new exception(REGISTER_ERROR_PASSWORD_DIFFERENT, ERR_ERROR);
      $result[F_LOGIN_STATUS] = REGISTER_ERROR_PASSWORD_DIFFERENT;
    } else {
      $result[F_LOGIN_STATUS] = sec_login_register($username_unsafe, $password_raw, $email_unsafe, $language, sys_get_param_int('rememberme'));
    }
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

  // return $result;
}

function sec_login_username($username_unsafe, $password_raw, $remember_me = 1) {
  // TODO - Логин по старым именам
  // $status = LOGIN_UNDEFINED;
  $username_safe = db_escape($username_unsafe);
  if(!$username_safe)
  {
    $status = LOGIN_ERROR_USERNAME;
  } elseif(!$password_raw) {
    $status = LOGIN_ERROR_PASSWORD;
  } else {
    $user = db_user_by_username_security($username_unsafe);
    // TODO: try..catch
    if(empty($user) || (isset($user['user_as_ally']) && $user['user_as_ally'])) {
      $status = LOGIN_ERROR_USERNAME;
    // } elseif(!$user['password'] || $user['password'] != sec_password_encode($password_raw, $user['salt'])) {
    } elseif(!$user['password'] || !sec_password_check($user, $password_raw)) {
      $status = LOGIN_ERROR_PASSWORD;
    } else {
      // sec_set_cookie_by_fields($user['id'], $user['username'], $user['password'], $remember_me);
      sec_set_cookie_by_user($user, $remember_me);
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
      $confirm_code_safe = db_escape($confirm_code);
      $query = doquery("SELECT count(*) FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}'", true);
    } while(!$query);
    $email_safe = db_escape($email_unsafe);
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
    $last_confirm = doquery("SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `code` = '{$confirm_safe}' AND `type` = " . CONFIRM_PASSWORD_RESET . " LIMIT 1;", true);
    if(!isset($last_confirm['id'])) {
      throw new exception(PASSWORD_RESTORE_ERROR_CODE_WRONG);
    }

    if(SN_TIME_NOW - $last_confirm['unix_time'] > PERIOD_DAY) {
      throw new exception(PASSWORD_RESTORE_ERROR_CODE_TOO_OLD);
    }

    $new_password = sys_random_string(8, SN_SYS_SEC_CHARS_CONFIRMATION);
    // $salt_unsafe = sec_password_salt_generate();
    // $md5 = sec_password_encode($new_password, $salt_unsafe);
    // $salt_safe = db_escape($salt_unsafe);
    //if(!db_user_set_by_id($last_confirm['id_user'], "`password` = '{$md5}', `salt` = '{$salt_safe}'")) {
    if(!sec_password_change($last_confirm['id_user'], $new_password, false, 1)) { // OK
      throw new exception(PASSWORD_RESTORE_ERROR_CHANGE);
    }

    $message = sprintf($lang['log_lost_email_pass'], $config->game_name, $new_password);
    @$operation_result = mymail($last_confirm['email'], sprintf($lang['log_lost_email_title'], $config->game_name), htmlspecialchars($message));
    $message = sys_bbcodeParse($message) . '<br><br>';

    $result[F_PASSWORD_NEW] = $new_password;
    $result[F_LOGIN_STATUS] = $operation_result ? PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT : PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR;
    $result[F_LOGIN_MESSAGE] = $message . ($operation_result ? $lang['log_lost_sent_pass'] : $lang['log_lost_err_sending']);
    doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id_user']}' AND `type` = " . CONFIRM_PASSWORD_RESET . " LIMIT 1;");

    // sys_redirect('login.php');
  } catch(exception $e) {
    $result[F_LOGIN_STATUS] = $e->getMessage();
  }
}


function sec_login_cookie(&$result) {
  global $user_impersonator;

  // Проверяем куку имперсонатора на доступ
  if($_COOKIE[SN_COOKIE_I]) {
    $user_impersonator = sec_cookie_user_check($_COOKIE[SN_COOKIE_I]);
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
    $result[F_LOGIN_USER] = sec_cookie_user_check($_COOKIE[SN_COOKIE]);
    if(empty($result[F_LOGIN_USER])) {
      $result[F_LOGIN_STATUS] = LOGIN_UNDEFINED;
    } else {
      $result[AUTH_LEVEL] = $result[F_LOGIN_USER]['authlevel'];
      sec_login_process($result);
    }
  }
}

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

  sec_login_change_state($result);
}

function sec_login_change_state(&$result) {
  global $user_impersonator, $config, $sys_stop_log_hit, $is_watching;
  $user = $result[F_LOGIN_USER];

  if(!empty($user['id']) && intval($user['id']) && !$user_impersonator) {
    sn_db_transaction_start();

    $proxy_safe = db_escape($user['user_proxy']);
    $ip_address = ip2longu($user['user_lastip']);
    doquery(
      "INSERT IGNORE INTO {{security_player_entry}} (`player_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`)
        VALUES ({$user['id']},{$result[F_DEVICE_ID]},{$result[F_BROWSER_ID]},{$ip_address}, '{$proxy_safe}');"
    );

    if(!$sys_stop_log_hit && $config->game_counter) {
      $is_watching = true;
      sec_login_set_fields($result, $_SERVER['PHP_SELF'], F_PAGE, F_PAGE_ID, 'url_id', 'security_url', 'url_string');
      sec_login_set_fields($result, $_SERVER['REQUEST_URI'], F_URL, F_URL_ID, 'url_id', 'security_url', 'url_string');

      doquery(
        "INSERT INTO {{counter}}
          (`visit_time`, `user_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`, `page_url_id`, `plain_url_id`)
        VALUES
          ('" . SN_TIME_SQL. "', {$user['id']}, {$result[F_DEVICE_ID]},{$result[F_BROWSER_ID]},
            {$ip_address},'{$proxy_safe}', {$result[F_PAGE_ID]}, {$result[F_URL_ID]});");
      $is_watching = false;
    }
    sn_db_transaction_commit();

    db_user_set_by_id($user['id'], "`onlinetime` = " . SN_TIME_NOW . ", `banaday` = {$user['banaday']}, `vacation` = {$user['vacation']},
      `user_lastip` = '{$user['user_lastip']}', `user_last_proxy` = '{$proxy_safe}', `user_last_browser_id` = {$result[F_BROWSER_ID]}"
    );

    if($extra = $config->security_ban_extra) {
      $extra = explode(',', $extra);
      array_walk($extra,'trim');
      in_array($result[F_DEVICE_ID], $extra) and die();
    }
  }

  // Не должно никуда уходить
  unset($result[F_DEVICE_ID]);
  unset($result[F_DEVICE_CYPHER]);
}

function sec_login_register($username_unsafe, $password_raw, $email_unsafe, $language, $remember_me = 1) {
  return sn_function_call('sec_login_register', array($username_unsafe, $password_raw, $email_unsafe, $language, $remember_me, &$result));
}
function sn_sec_login_register($username_unsafe, $password_raw, $email_unsafe, $language, $remember_me = 1, &$result) {
  global $lang, $config;

  sn_db_transaction_start();
  try {
    if($config->game_mode == GAME_BLITZ) {
      throw new exception(REGISTER_ERROR_USERNAME_WRONG, ERR_ERROR);
    }

    if(!$username_unsafe) {
      throw new exception(REGISTER_ERROR_USERNAME_WRONG, ERR_ERROR);
    }

    $username_safe = db_escape($username_unsafe);
    $db_check = doquery("SELECT `player_id` FROM {{player_name_history}} WHERE `player_name` = '{$username_safe}' LIMIT 1;", true);
    if(!empty($db_check)) {
      throw new exception(REGISTER_ERROR_USERNAME_EXISTS, ERR_ERROR);
    }

    if(strlen(trim($password_raw)) < 4 || strlen(trim($password_raw)) <> strlen($password_raw)) {
      throw new exception(REGISTER_ERROR_PASSWORD_INSECURE, ERR_ERROR);
    }
    $password_raw = trim($password_raw);

//    $password_repeat_raw = trim(sys_get_param('password_repeat'));
//    if($password_raw <> $password_repeat_raw) {
//      throw new exception(REGISTER_ERROR_PASSWORD_DIFFERENT, ERR_ERROR);
//    }

    $email = db_escape($email_unsafe);
    if(db_user_by_email($email, true)) {
      throw new exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
    }


    $skin = DEFAULT_SKINPATH;
//    $salt_unsafe = sec_password_salt_generate();
//    $md5pass = sec_password_encode($password_raw, $salt_unsafe);
//    $salt_safe = db_escape($salt_unsafe);
//    // `id_planet` = 0, `gender` = '{$gender}', `design` = '1',
//    $user_new = classSupernova::db_ins_record(LOC_USER, "`email` = '{$email}', `email_2` = '{$email}', `username` = '{$username_safe}', `dpath` = '{$skin}',
//      `lang` = '{$language}', `register_time` = " . SN_TIME_NOW . ", `password` = '{$md5pass}', `salt` = '{$salt_safe}',
//      `options` = 'opt_mnl_spy^1|opt_email_mnl_spy^0|opt_email_mnl_joueur^0|opt_email_mnl_alliance^0|opt_mnl_attaque^1|opt_email_mnl_attaque^0|opt_mnl_exploit^1|opt_email_mnl_exploit^0|opt_mnl_transport^1|opt_email_mnl_transport^0|opt_email_msg_admin^1|opt_mnl_expedition^1|opt_email_mnl_expedition^0|opt_mnl_buildlist^1|opt_email_mnl_buildlist^0|opt_int_navbar_resource_force^1|';");
    $user_new = classSupernova::db_ins_record(LOC_USER, "`email` = '{$email}', `email_2` = '{$email}', `username` = '{$username_safe}', `dpath` = '{$skin}',
      `lang` = '{$language}', `register_time` = " . SN_TIME_NOW . ",
      `options` = 'opt_mnl_spy^1|opt_email_mnl_spy^0|opt_email_mnl_joueur^0|opt_email_mnl_alliance^0|opt_mnl_attaque^1|opt_email_mnl_attaque^0|opt_mnl_exploit^1|opt_email_mnl_exploit^0|opt_mnl_transport^1|opt_email_mnl_transport^0|opt_email_msg_admin^1|opt_mnl_expedition^1|opt_email_mnl_expedition^0|opt_mnl_buildlist^1|opt_email_mnl_buildlist^0|opt_int_navbar_resource_force^1|';");
    sec_password_change($user_new, $password_raw, false, $remember_me);// OK
    $user = db_user_by_id($user_new['id']);
    // sec_set_cookie_by_user($user, $remember_me);

    // $user['id'] = $user_new['id'];
    doquery("REPLACE INTO {{player_name_history}} SET `player_id` = {$user['id']}, `player_name` = '{$username_safe}'");

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

    // sec_set_cookie_by_fields($user['id'], $user['username'], $user['password'], $remember_me);

    $result = REGISTER_SUCCESS;
  } catch(exception $e) {
    sn_db_transaction_rollback();
    $result = $e->getMessage();
  }

  return $result;
}


function sys_user_vacation($user) {
  global $config;

  if(sys_get_param_str('vacation') == 'leave') {
    if ($user['vacation'] < SN_TIME_NOW) {
      $user['vacation'] = 0;
      $user['vacation_next'] = SN_TIME_NOW + $config->player_vacation_timeout;
      db_user_set_by_id($user['id'], "`vacation` = {$user['vacation']}, `vacation_next` = {$user['vacation_next']}");
    }
  }

  if($user['vacation']) {
    sn_sys_logout(false, true);

    $template = gettemplate('vacation', true);

    $template->assign_vars(array(
      'NAME' => $user['username'],
      'VACATION_END' => date(FMT_DATE_TIME, $user['vacation']),
      'CAN_LEAVE' => $user['vacation'] <= SN_TIME_NOW,
      'RANDOM' => mt_rand(1, 2),
    ));

    display(parsetemplate($template), '', false, '', false, false);
  }

  return false;
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
  // sn_cookie_set_user($user_selected, 0);
  // sec_set_cookie_by_fields($user_selected['id'], $user_selected['username'], $user_selected['password'], 0);
  sec_set_cookie_by_user($user_selected, 0);
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
/**
 * @param bool|string $redirect нужно ли сделать перенаправление после логаута
 * <p><b>false</b> - не перенаправлять</p>
 * <p><i><b>true</b></i> - перенаправить на главную страницу</p>
 * <p><b>string</b> - перенаправить на указанный URL</p>
 *
 * @param bool $only_impersonator Если установлен - то логаут происходит только при имперсонации
 */
function sn_sys_logout($redirect = true, $only_impersonator = false) {
  global $user_impersonator;

  if($only_impersonator && !$user_impersonator) {
    return;
  }

  if($_COOKIE[SN_COOKIE_I] && $user_impersonator['authlevel'] >= 3) {
    // sn_cookie_set_user($user_impersonator, 1);
    // sec_set_cookie_by_fields($user_impersonator['id'], $user_impersonator['username'], $user_impersonator['password'], 1);
    sec_set_cookie_by_user($user_impersonator, 1);
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

/**
 * @param $UserID
 */
function DeleteSelectedUser($UserID) {
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
  $config->db_saveItem('users_amount', $config->db_loadItem('users_amount') - 1);
  sn_db_transaction_commit();
}

/**
 * @param        $banner
 * @param        $banned
 * @param        $term
 * @param bool   $is_vacation
 * @param string $reason
 */
function sys_admin_player_ban($banner, $banned, $term, $is_vacation = true, $reason = '') {
  $ban_current = db_user_by_id($banned['id'], false, 'banaday');
  $ban_until = ($ban_current['banaday'] ? $ban_current['banaday'] : SN_TIME_NOW) + $term;

  db_user_set_by_id($banned['id'], "`banaday` = {$ban_until} " . ($is_vacation ? ", `vacation` = '{$ban_until}' " : ''));

  $banned['username'] = db_escape($banned['username']);
  $banner['username'] = db_escape($banner['username']);
  doquery(
    "INSERT INTO
      {{banned}}
    SET
      `ban_user_id` = '{$banned['id']}',
      `ban_user_name` = '{$banned['username']}',
      `ban_reason` = '{$reason}',
      `ban_time` = " . SN_TIME_NOW . ",
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

/**
 * @param        $banner
 * @param        $banned
 * @param string $reason
 */
function sys_admin_player_ban_unset($banner, $banned, $reason = '') {
  db_user_set_by_id($banned['id'], "`banaday` = 0, `vacation` = " . SN_TIME_NOW . "");

  $banned['username'] = db_escape($banned['username']);
  $banner['username'] = db_escape($banner['username']);
  $reason = db_escape($reason);
  doquery(
    "INSERT INTO {{banned}}
    SET
      `ban_user_id` = '{$banned['id']}',
      `ban_user_name` = '{$banned['username']}',
      `ban_reason` = '{$reason}',
      `ban_time` = 0,
      `ban_until` = " . SN_TIME_NOW . ",
      `ban_issuer_id` = '{$banner['id']}',
      `ban_issuer_name` = '{$banner['username']}',
      `ban_issuer_email` = '{$banner['email']}'
  ");
}

/**
 * Получение пароля пользователя по его ID или записи
 *
 * @param int|array $user ID или запись пользователя
 *
 * @return array|bool Массив [password, salt] или FALSE в случае неудачи
 */
function sec_password_get($user) {
  if(!is_array($user) && ($user = intval($user))) {
    // Это ID
    $user = db_user_by_id($user);
  }

  if(is_array($user) && !empty($user['password'])) {
    return array('password' => $user['password'], 'salt' => $user['salt']);
  } else {
    return false;
  }
}

/**
 * Проверка совпадения пароля пользователя с указанной строкой
 *
 * @param int|array $user ID пользователя или его запись
 * @param string $check_against Строка, который нужно проверить на совпадение с паролем
 *
 * @return bool TRUE в случае совпадения и FALSE - в обратном случае
 */
function sec_password_check($user, $check_against) {
  $password_match = false;
  if(empty($user['password'])) {
    $user = sec_password_get($user);
  }

  if(!empty($user['password']) && isset($user['salt'])) {
    $password_match = sec_password_encode($check_against, $user['salt']) == $user['password'];
  }

  return $password_match;
}

/**
 * @param int|array   $user
 *
 * @param string      $new_password_unsafe
 *
 * @param bool|string $old_password_unsafe
 *    <p>false - Проверка текущего пароля не будет производиться</p>
 *    <p>string - Будет произведена проверка текущего пароля на равенство указанной строке</p>
 *
 * @param bool|int $remember_me
 *    <p>false - Кука пользователя изменена не будет</p>
 *    <p>int - Будет изменена кука пользователя и поле rememberme установлено в значение параметра</p>
 *
 * @return array|bool|resource <p><b>true</b> - если пароль изменен успешно<p>
 *    <p>true - если пароль изменен успешно<p>
 *    <p>false - в остальных случаях<p>
 */
function sec_password_change($user, $new_password_unsafe, $old_password_unsafe, $remember_me = false) {
  // Если старый пароль не равен true - значит надо провести проверку пароля
  // Проверяем старый пароль и меняем только если всё ОК
  if($old_password_unsafe !== false && !sec_password_check($user, $old_password_unsafe)) {
    return false;
  }

  $salt_unsafe = sec_password_salt_generate();
  $password_encoded = sec_password_encode($new_password_unsafe, $salt_unsafe);
  $salt_safe = db_escape($salt_unsafe);

  $user_id = is_array($user) && !empty($user['id']) ? $user['id'] : $user;

  $result = sec_password_set($user_id, $password_encoded, $salt_safe);
  if($result && $remember_me !== false) {
    sec_set_cookie_by_user(array('id' => $user_id, 'username' => '', 'password' => $password_encoded), $remember_me);
  }

  return $result;
}

/**
 * @param $user_id
 * @param $encoded_pass_safe
 * @param $salt_safe
 *
 * @return array|bool|resource
 */
function sec_password_set($user_id, $encoded_pass_safe, $salt_safe) {
  return db_user_set_by_id($user_id, "`password` = '{$encoded_pass_safe}', `salt` = '{$salt_safe}'");
}
