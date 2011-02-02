<?php

/**
 * login.php
 *
 * @version 2.0 Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1 Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ?????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}includes/init.{$phpEx}");

includeLang('login');
includeLang('admin');

$id_ref = sys_get_param_int('id_ref');
$username = sys_get_param('username');
$password = sys_get_param('password');
if ($username)
{
  $result = sn_login($username, $password, $_POST['rememberme']);

  switch($result['status'])
  {
    case LOGIN_SUCCESS:
      $user = $result['user_row'];
      header('Location: overview.php');
    break;

    case LOGIN_ERROR_USERNAME:
    case LOGIN_ERROR_PASSWORD:
      message($result['error_msg'], $lang['Login_Error']);
    break;

    default:

  }
  die();
}
elseif(!empty($_COOKIE[$config->COOKIE_NAME]))
{
  $user = sn_autologin();

  if($user['id'])
  {
    ob_start();
    header("Location: ./index.{$phpEx}");
    ob_end_flush();
  }
  die();
}

$query = doquery('SELECT username FROM {{users}} ORDER BY register_time DESC LIMIT 1;', '', true);
$query1 = doquery("SELECT COUNT(DISTINCT(id)) AS users_online FROM {{users}} WHERE onlinetime>" . (time()-900), '', true);

$template = gettemplate('login_body', true);
$template->assign_vars(array(
  'last_user' => $query['username'],
  'online_users' => $query1['users_online']
));
if($id_ref)
{
  $template->assign_var('referral', "?id_ref={$id_ref}");
}

display(parsetemplate($template, $parse), $lang['Login'], false, '', false, false);

?>
