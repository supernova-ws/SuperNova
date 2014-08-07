<?php

/**
 * login.php
 *
 * @version 2.0 Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1 Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ?????? for XNova
 */

$allow_anonymous = true;

include('includes/init.' . substr(strrchr(__FILE__, '.'), 1));
// die();
if($template_result[F_USER_AUTHORIZED])
{
  sys_redirect('index' . DOT_PHP_EX);
}
lng_include('login');
lng_include('admin');

$username_unsafe = sys_get_param_str_unsafe('username');
$password_raw = trim(sys_get_param('password'));
$password_repeat_raw = trim(sys_get_param('password_repeat'));
$email = sys_get_param_str('email');

/*
if(sys_get_param('confirm_code_send') && $email_unsafe = sys_get_param_str_unsafe('email')) {
  $email = mysql_real_escape_string($email_unsafe);

  $user_id = db_user_by_email($email_unsafe, false, false, 'id');
  if(!$user_id['id']) {
    message($lang['log_lost_err_email'], $lang['sys_error']);
  } else {
    // TODO - уникальный индекс по id_user и type - и делать не INSERT, а REPLACE
    $last_confirm = doquery("SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `id_user`= '{$user_id['id']}' AND `type` = '{$confirm_password_reset}' LIMIT 1;", '', true);
    if($last_confirm['unix_time']) {
      doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id']}' LIMIT 1;");
    }

    do {
      $confirm_code = sys_random_string(6, SN_SYS_SEC_CHARS_CONFIRMATION);
      $confirm_code_safe = mysql_real_escape_string($confirm_code);
      $query = doquery("SELECT count(*) FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}'", true);
    } while($query);

    @$result = mymail($email, sprintf($lang['log_lost_email_title'], $config->game_name), sprintf($lang['log_lost_email_code'], SN_ROOT_VIRTUAL . $_SERVER['PHP_SELF'], $confirm_code, date(FMT_DATE_TIME, $time_now + 3*24*60*60), $config->game_name));

    doquery("INSERT INTO {{confirmations}} SET `id_user`= '{$user_id['id']}', `type` = '{$confirm_password_reset}', `code` = '{$confirm_code}', `email` = '{$email}';");

    if($result) {
      message($lang['log_lost_sent_code'], $lang['log_lost_header']);
    }
    else {
      message($lang['log_lost_err_sending'], $lang['sys_error']);
    }
  }
}
*/


/*
$id_ref = sys_get_param_id('id_ref');
$confirm = sys_get_param_str('confirm');

$confirm_password_reset = CONFIRM_PASSWORD_RESET;

if($confirm) {
  $last_confirm = doquery("SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `code` = '{$confirm}' LIMIT 1;", '', true);
  if($last_confirm['id'] && ($time_now - $last_confirm['unix_time'] <= 3*24*60*60)) {
    doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id']}' LIMIT 1;");

    $user_data = db_user_by_id($last_confirm['id_user']);
    if(!$user_data['id']) {
      message($lang['log_lost_err_code'], $lang['sys_error']);
    }

    if($user_data['authlevel']) {
      message($lang['log_lost_err_admin'], $lang['sys_error']);
    }

    $new_password = sys_random_string();
    $md5 = md5($new_password);
    $result = db_user_set_by_id($last_confirm['id_user'], "`password` = '{$md5}'");
    if($result) {
      $message = sprintf($lang['log_lost_email_pass'], $config->game_name, $new_password);
      @$result = mymail($last_confirm['email'], sprintf($lang['log_lost_email_title'], $config->game_name), htmlspecialchars($message));
      $message = sys_bbcodeParse($message) . '<br><br>';

      if($result) {
        $message = $message . $lang['log_lost_sent_pass'];
      } else {
        $message = $message . $lang['log_lost_err_sending'];
      }

      message($message, $lang['log_lost_header']);
    } else {
      message($lang['log_lost_err_change'], $lang['sys_error']);
    }

  } else {
    message($lang['log_lost_err_code'], $lang['sys_error']);
  }
}
*/


$template = gettemplate('login_body', true);
$template->assign_vars(array(
  'last_user'    => db_user_last_registered_username(),
  'online_users' => db_user_count(true),
  'F_LOGIN_MESSAGE' => $template_result[F_LOGIN_MESSAGE],
  'F_LOGIN_STATUS' => $template_result[F_LOGIN_STATUS],
  'LOGIN_ERROR_USERNAME' => LOGIN_ERROR_USERNAME,
  'LOGIN_ERROR_PASSWORD' => LOGIN_ERROR_PASSWORD,
  'REGISTER_ERROR_EMAIL_EXISTS' => REGISTER_ERROR_EMAIL_EXISTS,
  'USERNAME'     => htmlentities($username_unsafe, ENT_QUOTES, 'UTF-8'),
  'EMAIL'     => htmlentities($email, ENT_QUOTES, 'UTF-8'),
  'PASSWORD'     => htmlentities($password_raw, ENT_QUOTES, 'UTF-8'),
  'PASSWORD_REPEAT' => htmlentities($password_repeat_raw, ENT_QUOTES, 'UTF-8'),
  'URL_RULES'    => $config->url_rules,
  'URL_FORUM'    => $config->url_forum,
  'URL_FAQ'      => $config->url_faq,
));

tpl_login_lang($template);

display($template, $lang['Login'], false, '', false, false);

/*
pdump(__FILE__);
pdump($template_result);
die();


$username = sys_get_param_str_unsafe('username');
$password = sys_get_param('password');
if($username)
{
  $result = sec_login_username($username, $password, $_POST['rememberme']);
  switch($result[F_LOGIN_STATUS])
  {
    case LOGIN_SUCCESS:
      $user = $result[F_LOGIN_USER];
      header('Location: overview.php');
    break;

    case LOGIN_ERROR_USERNAME:
    case LOGIN_ERROR_PASSWORD:
      message($result[F_LOGIN_MESSAGE], $lang['Login_Error']);
    break;

    default:

  }
  die();
}
elseif(!empty($_COOKIE[SN_COOKIE]))
{
  $user = sn_autologin();

  if($user['id'])
  {
    ob_start();
    header("Location: ./index." . PHP_EX);
    ob_end_flush();
  }
  die();
}
*/
