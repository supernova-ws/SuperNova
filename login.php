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

$InLogin = true;

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

$id_ref = intval($_GET['id_ref'] ? $_GET['id_ref'] : $_POST['id_ref']);
$username = mysql_real_escape_string($_GET['username'] ? $_GET['username'] : $_POST['username']);
$password = md5($_GET['password'] ? $_GET['password'] : $_POST['password']);

includeLang('login');

if ($username)
{
  $login = doquery("SELECT * FROM {{table}} WHERE `username` = '{$username}' LIMIT 1", 'users', true);

  if ($login)
  {
    if ($login['password'] == $password)
    {
      if (isset($_POST["rememberme"]))
      {
        $expiretime = time() + 31536000;
        $rememberme = 1;
      }
      else
      {
        $expiretime = 0;
        $rememberme = 0;
      }

      @include('config.php');
      $cookie = $login["id"] . "/%/" . $login["username"] . "/%/" . md5($login["password"] . "--" . $dbsettings["secretword"]) . "/%/" . $rememberme;
      setcookie($config->COOKIE_NAME, $cookie, $expiretime, "/", "", 0);

      unset($dbsettings);

      if ($login['urlaubs_modus'] == 1)
      {
        $time_out = ((($login['urlaubs_modus_time'] + VOCATION_TIME)-time())/3600);

        if (time() >= $login['urlaubs_modus_time'] + VOCATION_TIME )
        {
          header("Location: ./options.php");
          exit;
        }
        else
        {
          message($lang['vacation_mode'].floor($time_out).$lang['hours'], $lang['vacations']);
        }
      }
      else
      {
        header("Location: ./overview.php");
      }
      exit;
    }
    else
    {
      message($lang['Login_FailPassword'], $lang['Login_Error']);
    }
  }
  else
  {
    message($lang['Login_FailUser'], $lang['Login_Error']);
  }
}
elseif(!empty($_COOKIE[$config->COOKIE_NAME]))
{
  $user = CheckTheUser();

  if($user)
  {
    header("Location: ./index.php");
    exit;
  }
/*
  $cookie = explode('/%/',$_COOKIE[$config->COOKIE_NAME]);
  $login = doquery("SELECT * FROM {{table}} WHERE `username` = '" . mysql_real_escape_string($cookie[1]) . "' LIMIT 1", "users", true);

  if ($login)
  {
    @include('config.php');

    if (md5($login["password"] . "--" . $dbsettings["secretword"]) == $cookie[2])
    {
      unset($dbsettings);
      header("Location: ./index.php");
      exit;
    }
  }
*/
}

$parse = $lang;
$query = doquery('SELECT username FROM {{table}} ORDER BY register_time DESC', 'users', true);
$parse['last_user'] = $query['username'];
$query = doquery("SELECT COUNT(DISTINCT(id)) FROM {{table}} WHERE onlinetime>" . (time()-900), 'users', true);
$parse['online_users'] = $query[0];
$parse['users_amount'] = $config->users_amount;
$parse['servername'] = $config->game_name;
$parse['forum_url'] = $config->forum_url;
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