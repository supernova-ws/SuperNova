<?php

function sec_player_ip() {
  // НЕ ПЕРЕКРЫВАТЬ
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
  // НЕ ПЕРЕКРЫВАТЬ - написать СВОЮ реализацию
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
  // НЕ ПЕРЕКРЫВАТЬ
  // TODO ВКЛЮЧИТЬ ГЕНЕРАЦИЮ СОЛИ !!!
  return ''; // sys_random_string(16);
}


// TheCookie[0] = `id`
// TheCookie[1] = `username`
// TheCookie[2] = md5(pass. '--' . SecretWord)
// TheCookie[3] = rememberme

function sec_set_cookie_by_fields($user_id, $username_unsafe, $password_hash, $remember_me) {
  // ПЕРЕКРЫТЬ - надо перекрыть - вытаскивать аккаунт по пользователю и выставлять куки на родительском домене
  $expire_time = ($remember_me = intval($remember_me)) ? SN_TIME_NOW + PERIOD_YEAR : 0;

  $password_encoded = sec_password_cookie_encode($password_hash);
  $cookie = "{$user_id}/%/{$username_unsafe}/%/{$password_encoded}/%/{$remember_me}";

  return sn_setcookie(SN_COOKIE, $cookie, $expire_time, SN_ROOT_RELATIVE);
}
function sec_set_cookie_by_user($user, $remember_me) {
  $account = db_account_by_user($user);
  return sec_set_cookie_by_fields($user['id'], '', $account['account_password'], $remember_me);
}
function sec_cookie_user_check($cookie) {
  // НЕ ПЕРЕКРЫВАТЬ
  // Тут всё в порядке - мы определяем локального пользователя по его ID
  list($user_id_unsafe, $user_name, $password_hash_salted, $user_remember_me) = explode("/%/", $cookie);

  $user = db_user_by_id($user_id_unsafe, false, '*', true);
  $account = db_account_by_user($user);
  if(!empty($user) && sec_password_cookie_encode($account['account_password']) == $password_hash_salted) {
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
    F_LOGIN_USER => null,
    F_LOGIN_ACCOUNT => null,
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
      // WORK OK 2015-04-19 23:46:50 40a0.0
      sec_login_register($username_unsafe, $password_raw, $email_unsafe, $language, sys_get_param_int('rememberme'), $result);
    }
  }

  // Тут всякие логины по внешним плагинам... ИЛИ НИЖЕ!


  // Если есть в параметрах логин и пароль...
  // if(in_array($result[F_LOGIN_STATUS], array(LOGIN_UNDEFINED, REGISTER_SUCCESS)) && sys_get_param('login')) {
  if(empty($result[F_LOGIN_USER]) && sys_get_param('login')) {
    // WORK OK 2015-04-19 23:46:50 40a0.0
    sec_login_username($username_unsafe, $password_raw, sys_get_param_int('rememberme'), $result);
  } elseif(sys_get_param('confirm_code_send') && $email_unsafe = sys_get_param_str_unsafe('email')) {
    // WORK OK 2015-04-19 23:46:50 40a0.0
    $result[F_LOGIN_STATUS] = sec_restore_password_send_email($email_unsafe);
  } elseif(sys_get_param('confirm_code_submit') && $confirm_safe = sys_get_param_str('confirm')) {
    // WORK OK 2015-04-19 23:46:50 40a0.0
    sec_restore_password_confirm($confirm_safe, $result);
  }
  // Тут всякие логины по внешним плагинам... ИЛИ ВЫШЕ!
//pdump($result, 'security');

  // В этой точке должен быть установлена кука СН - логинимся по ней
//  if(in_array($result[F_LOGIN_STATUS], array(LOGIN_UNDEFINED, REGISTER_SUCCESS))) {
  if(empty($result[F_LOGIN_USER])) {
    // WORK OK 2015-04-19 23:46:50 40a0.0
    sec_login_cookie($result);
  }

  // TODO -          ЗАМЕНИТЬ F_LOGIN_MESSAGE       на сообщения по   F_LOGIN_STATUS

  // return $result;
}

function sec_login_username($username_unsafe, $password_raw, $remember_me = 1, &$result) {
  // TODO - Логин по старым именам
  if(!($username_safe = db_escape($username_unsafe))) {
    // Экономим несколько запросов к БД - не переносить ниже!
    $result[F_LOGIN_STATUS] = LOGIN_ERROR_USERNAME;
  } elseif(!$password_raw) {
    $result[F_LOGIN_STATUS] = LOGIN_ERROR_PASSWORD;
  } else {
    $user = db_user_by_account_name($username_unsafe);
    // TODO: try..catch
    if(empty($user) || (isset($user['user_as_ally']) && $user['user_as_ally'])) {
      $result[F_LOGIN_STATUS] = LOGIN_ERROR_USERNAME;
    } elseif(!sec_password_check($user['id'], $password_raw)) {
      $result[F_LOGIN_STATUS] = LOGIN_ERROR_PASSWORD;
    } else {
      sec_set_cookie_by_user($user, $remember_me);
      $result[F_LOGIN_STATUS] = LOGIN_SUCCESS;
    }
  }
  // Это можно раскомментить для большей безопасности - что бы не подбирали пароли
  // $status = $status == LOGIN_ERROR_PASSWORD ? LOGIN_ERROR_USERNAME : $status;
  $result[F_LOGIN_USER] = !empty($user) && ($result[F_LOGIN_STATUS] == LOGIN_SUCCESS) ?  $user : null;
}


function sec_restore_password_send_email($email_unsafe) {
  global $lang, $config;

  try {
    sn_db_transaction_start();
    // $user_id = db_user_by_email($email_unsafe, false, false, 'id, authlevel');
    $account = db_account_by_email($email_unsafe);
    if(empty($account['account_email'])) {
      throw new exception(PASSWORD_RESTORE_ERROR_WRONG_EMAIL);
    }
    $user = db_user_by_account_id($account['account_id']);
    if($user['authlevel'] > 0) {
      throw new exception(PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT);
    }

    // TODO - уникальный индекс по id_user и type - и делать не INSERT, а REPLACE
    $last_confirm = doquery("SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `id_user`= '{$user['id']}' AND `type` = " . CONFIRM_PASSWORD_RESET . " ORDER BY create_time DESC;", true);
    if(isset($last_confirm['unix_time']) && SN_TIME_NOW - $last_confirm['unix_time'] < PERIOD_MINUTE_10) {
      throw new exception(PASSWORD_RESTORE_ERROR_TOO_OFTEN);
    }
    // Удаляем предыдущие записи продтверждения сброса пароля
    !empty($last_confirm['id']) or doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id']}' LIMIT 1;");

    do {
      // Ну, если у нас > 999.999.999 подтверждений - тут нас ждут проблемы...
      $confirm_code_safe = db_escape($confirm_code = sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION));
      $query = doquery("SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' AND `type` = " . CONFIRM_PASSWORD_RESET, true);
    } while($query);
    $email_safe = db_escape($email_unsafe);
    doquery("INSERT INTO {{confirmations}} SET `id_user`= '{$user['id']}', `type` = " . CONFIRM_PASSWORD_RESET . ", `code` = '{$confirm_code_safe}', `email` = '{$email_safe}';");

    @$result = mymail($email_unsafe, sprintf($lang['log_lost_email_title'], $config->game_name), sprintf($lang['log_lost_email_code'], SN_ROOT_VIRTUAL . $_SERVER['PHP_SELF'], $confirm_code, date(FMT_DATE_TIME, SN_TIME_NOW + 3*24*60*60), $config->game_name));

    $result = $result ? PASSWORD_RESTORE_SUCCESS_CODE_SENT : PASSWORD_RESTORE_ERROR_SENDING;
    sn_db_transaction_commit();
  } catch(exception $e) {
    sn_db_transaction_rollback();
    $result = $e->getMessage();
  }

  return $result;
}


function sec_restore_password_confirm($confirm_safe, &$result) {
  global $lang, $config;

  try {
    $last_confirm = doquery($q = "SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `code` = '{$confirm_safe}' AND `type` = " . CONFIRM_PASSWORD_RESET, true);
    if(!isset($last_confirm['id'])) {
      throw new exception(PASSWORD_RESTORE_ERROR_CODE_WRONG);
    }

    if(SN_TIME_NOW - $last_confirm['unix_time'] > PERIOD_DAY) {
      throw new exception(PASSWORD_RESTORE_ERROR_CODE_TOO_OLD);
    }

    $new_password = sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION);
    if(!sec_password_change($last_confirm['id_user'], $new_password, false, 1)) { // OK
      throw new exception(PASSWORD_RESTORE_ERROR_CHANGE);
    }

    $message = sprintf($lang['log_lost_email_pass'], $config->game_name, $new_password);
    @$operation_result = mymail($last_confirm['email'], sprintf($lang['log_lost_email_title'], $config->game_name), htmlspecialchars($message));
    $message = sys_bbcodeParse($message) . '<br><br>';

    $result[F_PASSWORD_NEW] = $new_password;
    $result[F_LOGIN_STATUS] = $operation_result ? PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT : PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR;
    $result[F_LOGIN_MESSAGE] = $message . ($operation_result ? $lang['log_lost_sent_pass'] : $lang['log_lost_err_sending']);
    doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id']}' AND `type` = " . CONFIRM_PASSWORD_RESET . " LIMIT 1;");

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
  if(empty($_COOKIE[SN_COOKIE])) {
    // Ошибка кукеса или не найден пользователь по кукесу
    sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
    if(!empty($user_impersonator)) {
      // Если это был корректный имперсонатор - просто выходим и редиректимся в админку
      sn_sys_logout();
    }
  } else {
    $result[F_LOGIN_USER] = sec_cookie_user_check($_COOKIE[SN_COOKIE]); // WORK OK 2015-04-19 23:46:50 40a0.0
    if(empty($result[F_LOGIN_USER])) {
      $result[F_LOGIN_STATUS] = LOGIN_UNDEFINED;
    } else {
      $result[AUTH_LEVEL] = $result[F_LOGIN_USER]['authlevel'];
      sec_login_process($result); // WORK OK 2015-04-19 23:46:50 40a0.0
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

function sec_login_register($username_unsafe, $password_raw, $email_unsafe, $language, $remember_me = 1, &$result = null) {return sn_function_call(__FUNCTION__, array($username_unsafe, $password_raw, $email_unsafe, $language, $remember_me, &$result));}
function sn_sec_login_register($username_unsafe, $password_raw, $email_unsafe, $language, $remember_me = 1, &$result) {
  global $lang, $config;

  if(!empty($result[F_LOGIN_USER])) {
    return;
  }

  try {
    if($config->game_mode == GAME_BLITZ) {
      throw new exception(REGISTER_ERROR_USERNAME_WRONG, ERR_ERROR);
    }

    $username_safe = db_escape($username_unsafe);
    if($username_safe != $username_unsafe) {
      throw new exception(REGISTER_ERROR_USERNAME_WRONG, ERR_ERROR);
    }

    if(!$username_unsafe) {
      throw new exception(REGISTER_ERROR_USERNAME_WRONG, ERR_ERROR);
    }

    sn_db_transaction_start();
    $db_check = doquery("SELECT `player_id` FROM {{player_name_history}} WHERE `player_name` = '{$username_safe}'", true);
    if(!empty($db_check)) {
      throw new exception(REGISTER_ERROR_USERNAME_EXISTS, ERR_ERROR);
    }
    $db_check = db_account_by_name($username_safe);
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

    if(db_account_by_email($email_unsafe)) {
      throw new exception(REGISTER_ERROR_EMAIL_EXISTS, ERR_ERROR);
    }

    $result[F_LOGIN_USER] = player_create($username_unsafe, $password_raw, $email_unsafe, array(
      'partner_id' => $partner_id = sys_get_param_int('id_ref', sys_get_param_int('partner_id')),
      'language_iso' => $language,
      'remember_me' => $remember_me,
    ));

    sn_db_transaction_commit();

    $email_message  = sprintf($lang['log_reg_email_text'], $config->game_name, SN_ROOT_VIRTUAL, sys_safe_output($username_unsafe), sys_safe_output($password_raw));
    @mymail($email_unsafe, sprintf($lang['log_reg_email_title'], $config->game_name), $email_message);

    $result[F_LOGIN_STATUS] = REGISTER_SUCCESS;
  } catch(exception $e) {
    sn_db_transaction_rollback();
    $result[F_LOGIN_STATUS] = $e->getMessage();
  }
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
 * Проверка совпадения пароля пользователя с указанной строкой
 *
 * @param int|array $user_id_safe ID пользователя или его запись
 * @param string $check_against Строка, который нужно проверить на совпадение с паролем
 *
 * @return bool TRUE в случае совпадения и FALSE - в обратном случае
 */
function sec_password_check($user_id_safe, $check_against) {
  return
    ($account = db_account_by_user_id($user_id_safe))
    &&
    (sec_password_encode($check_against, $account['account_salt']) == $account['account_password']);
}

/**
 * @param int|array   $user_id_safe
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
function sec_password_change($user_id_safe, $new_password_unsafe, $old_password_unsafe, $remember_me = false) {
  global $lang;

  // Если старый пароль не равен false - значит надо провести проверку пароля
  // Проверяем старый пароль и меняем только если всё ОК
  if($old_password_unsafe !== false && !sec_password_check($user_id_safe, $old_password_unsafe)) {
    return false;
  }

  $salt_unsafe = sec_password_salt_generate();
  $password_encoded = sec_password_encode($new_password_unsafe, $salt_unsafe);
  $salt_safe = db_escape($salt_unsafe);

  // $user_id_safe = round(floatval(is_array($user_id_safe) && !empty($user_id_safe['id']) ? $user_id_safe['id'] : $user_id_safe));

  $result = sec_password_set($user_id_safe, $password_encoded, $salt_safe);

  if($result) {
    $user_id_safe = db_user_by_id($user_id_safe);
    $user_id_safe = $user_id_safe['id'];
    sec_set_cookie_by_fields($user_id_safe, '', $password_encoded, $remember_me);
    $account = db_account_by_user_id($user_id_safe);
    msg_send_simple_message($user_id_safe, 0, SN_TIME_NOW, MSG_TYPE_ADMIN,
      $lang['sys_administration'], $lang['sys_login_register_message_title'],
      sprintf($lang['sys_login_register_message_body'], $account['account_name'], $new_password_unsafe)
    );
  }

  return $result;
}

/**
 * @param $user_id_safe
 * @param $encoded_pass_safe
 * @param $salt_safe
 *
 * @return array|bool|resource
 */
function sec_password_set($user_id_safe, $encoded_pass_safe, $salt_safe) {
  return db_account_set_by_user_id($user_id_safe, "`account_password` = '{$encoded_pass_safe}', `account_salt` = '{$salt_safe}'");
}
