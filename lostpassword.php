<?php

/**
 * lostpassword.php
 *
 * @version 2.0 copyright (c) 2009-2011 by Gorlum for http://supernova.ws
 *   [!] Fully rewrote
 *   [+] Confrimation code system
 *   [+] Random password generation
 * @version 1.1 copyright (c) 2009-2011 by Gorlum for http://supernova.ws
 *   [~] Security checks & tests
 * @version 1.0 copyright 2008 by Tom1991 for XNova
 *   [!] Création (Tom)
**/

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}includes/init.{$phpEx}");

includeLang('login');

$email   = sys_get_param_str('email');
$confirm = sys_get_param_str('confirm');

$confirm_password_reset = CONFIRM_PASSWORD_RESET;

if ($confirm)
{
  $last_confirm = doquery("SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `code` = '{$confirm}' LIMIT 1;", '', true);
  if($last_confirm['id'] && ($time_now - $last_confirm['unix_time'] <= 3*24*60*60))
  {
    doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id']}' LIMIT 1;");

    $user_data = doquery("SELECT * FROM {{users}} WHERE `id` = '{$last_confirm['id_user']}' LIMIT 1;", '', true);
    if(!$user_data['id'])
    {
      message($lang['log_lost_err_code'], $lang['sys_error']);
    }

    if($user_data['authlevel'])
    {
      message($lang['log_lost_err_admin'], $lang['sys_error']);
    }

    $new_password = sys_random_string();
    $md5 = md5($new_password);
    $result = doquery("UPDATE {{users}} SET `password` = '{$md5}' WHERE `id` = '{$last_confirm['id_user']}' LIMIT 1;");
    if($result)
    {
      $message = sprintf($lang['log_lost_email_pass'], $new_password);
      @$result = mymail($last_confirm['email'], $lang['log_lost_email_title'], htmlspecialchars($message));
      $message = sys_bbcodeParse($message) . '<br><br>';

      if($result)
      {
        $message = $message . $lang['log_lost_sent_pass'];
      }
      else
      {
        $message = $message . $lang['log_lost_err_sending'];
      }

      message($message, $lang['log_lost_header']);

    }
    else
    {
      message($lang['log_lost_err_change'], $lang['sys_error']);
    }

  }
  else
  {
    message($lang['log_lost_err_code'], $lang['sys_error']);
  }
}
elseif ($email)
{
  $user_id = doquery("SELECT `id` FROM {{users}} WHERE `email_2` = '{$email}' LIMIT 1;", '', true);

  if (!$user_id['id'])
  {
    message($lang['log_lost_err_email'], $lang['sys_error']);
  }
  else
  {
    $last_confirm = doquery("SELECT *, UNIX_TIMESTAMP(`create_time`) as `unix_time` FROM {{confirmations}} WHERE `id_user`= '{$user_id['id']}' AND `type` = '{$confirm_password_reset}' LIMIT 1;", '', true);
    if($last_confirm['unix_time'])
    {
      doquery("DELETE FROM {{confirmations}} WHERE `id` = '{$last_confirm['id']}' LIMIT 1;");
    }

    $confirm_code = sys_random_string();

    @$result = mymail($email, $lang['log_lost_email_title'], sprintf($lang['log_lost_email_code'], "http://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}", $confirm_code, date(FMT_DATE_TIME, $time_now + 3*24*60*60)));

    doquery("INSERT INTO {{confirmations}} SET `id_user`= '{$user_id['id']}', `type` = '{$confirm_password_reset}', `code` = '{$confirm_code}', `email` = '{$email}';");

    if($result)
    {
      message($lang['log_lost_sent_code'], $lang['log_lost_header']);
    }
    else
    {
      message($lang['log_lost_err_sending'], $lang['sys_error']);
    }
  }

  message('Le nouveau mot de passe a &eacute;t&eacute; envoy&eacute; avec succ&egrave;s !', 'OK');
}

display(parsetemplate(gettemplate('lostpassword', true)), $lang['system'], false, '', false, false);

?>
