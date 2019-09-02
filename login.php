<?php

/**
 * login.php
 *
 * @version 2.0 Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1 Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ?????? for XNova
 */

define('LOGIN_LOGOUT', true);

$allow_anonymous = true;

include('includes/init.' . substr(strrchr(__FILE__, '.'), 1));
// die();
if($template_result[F_USER_IS_AUTHORIZED]) {
  sys_redirect('index' . DOT_PHP_EX);
}
lng_include('login');
lng_include('admin');

$username_unsafe = sys_get_param_str_unsafe('username');
$password_raw = trim(sys_get_param('password'));
$password_repeat_raw = trim(sys_get_param('password_repeat'));
$email = sys_get_param_str('email');


$template = SnTemplate::gettemplate('login_body', true);
$template->assign_vars(array(
  'last_user'    => db_user_last_registered_username(),
  'online_users' => db_user_count(true),
  'id_ref' => sys_get_param_int('id_ref'),
  'F_LOGIN_MESSAGE' => $template_result[F_LOGIN_MESSAGE],
  'F_LOGIN_STATUS' => $template_result[F_LOGIN_STATUS],
  'LOGIN_ERROR_USERNAME' => LOGIN_ERROR_USERNAME,
  'LOGIN_ERROR_PASSWORD' => LOGIN_ERROR_PASSWORD,
  'REGISTER_ERROR_EMAIL_EXISTS' => REGISTER_ERROR_EMAIL_EXISTS,
  'PASSWORD_RESTORE_ERROR_WRONG_EMAIL' => PASSWORD_RESTORE_ERROR_EMAIL_NOT_EXISTS,
  'USERNAME'     => htmlentities($username_unsafe, ENT_QUOTES, 'UTF-8'),
  'EMAIL'     => htmlentities($email, ENT_QUOTES, 'UTF-8'),
  'PASSWORD'     => htmlentities($password_raw, ENT_QUOTES, 'UTF-8'),
  'PASSWORD_REPEAT' => htmlentities($password_repeat_raw, ENT_QUOTES, 'UTF-8'),
  'URL_RULES'    => SN::$config->url_rules,
  'URL_FORUM'    => SN::$config->url_forum,
  'URL_FAQ'      => SN::$config->url_faq,
  'GAME_BLITZ'   => SN::$config->game_mode == GAME_BLITZ,
));

SnTemplate::tpl_login_lang($template);

SnTemplate::display($template, $lang['Login']);
