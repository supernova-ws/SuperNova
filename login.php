<?php

/**
 * login.php
 *
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

$id_ref = intval($_GET['id_ref'] ? $_GET['id_ref'] : $_POST['id_ref']);

$username = $_GET['username'] ? $_GET['username'] : $_POST['username'];
$password = $_GET['password'] ? $_GET['password'] : $_POST['password'];
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
    header("Location: ./index.{$phpEx}");
    exit;
  }
  die();
}

$query = doquery('SELECT username FROM {{table}} ORDER BY register_time DESC', 'users', true);
$parse['last_user'] = $query['username'];
$query = doquery("SELECT COUNT(DISTINCT(id)) FROM {{table}} WHERE onlinetime>" . (time()-900), 'users', true);
$parse['online_users'] = $query[0];
$parse['users_amount'] = $config->users_amount;
$parse['servername'] = $config->game_name;
$parse['PasswordLost'] = $lang['PasswordLost'];
if($id_ref)
{
  $parse['referral'] = "?id_ref=$id_ref";
}

$page = parsetemplate(gettemplate('login_body', true), $parse);
display($page, $lang['Login'], false, '', false, false);

// -----------------------------------------------------------------------------------------------------------
// History version
?>