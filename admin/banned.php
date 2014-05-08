<?php

/**
 * banned.php
 *
 * @version 1.3  copyright (c) 2009 by Gorlum for http://supernova.ws
 *   [~] Optimized SQL-queries
 * @version 1.2 - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.1  - (c) Copyright by Gorlum for http://supernova.ws
 * @version 1.0  - copyright 2008 by Chlorel for XNova
 *
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if ($user['authlevel'] < 1)
{
  AdminMessage($lang['adm_err_denied']);
}

$mode = sys_get_param_str('mode', 'banit');
$name_unsafe = sys_get_param_str_raw('name');
$name_output = sys_safe_output($name_unsafe);
$action = sys_get_param_str('action');

$player_banned_row = db_user_by_username($name_unsafe);
if($mode == 'banit' && $action)
{
  if($player_banned_row)
  {
    $reas = $_POST['why'];
    $days = $_POST['days'];
    $hour = $_POST['hour'];
    $mins = $_POST['mins'];
    $secs = $_POST['secs'];
//    $isVacation = $_POST['isVacation'];

    $BanTime = $days * 86400;
    $BanTime += $hour * 3600;
    $BanTime += $mins * 60;
    $BanTime += $secs;
//    $BannedUntil = $time_now + $BanTime;

    sys_admin_player_ban($user, $player_banned_row, $BanTime, $is_vacation = sys_get_param_int('isVacation'), sys_get_param_str('why'));

    $DoneMessage = "{$lang['adm_bn_thpl']} {$name_output} {$lang['adm_bn_isbn']}";

    if($is_vacation)
    {
      $DoneMessage .= $lang['adm_bn_vctn'];
    }

    $DoneMessage .= $lang['adm_bn_plnt'];
  }
  else
  {
    $DoneMessage = sprintf($lang['adm_bn_errr'], $name_output);
  }

  AdminMessage($DoneMessage, $lang['adm_ban_title']);
}
elseif($mode == 'unbanit' && $action)
{
  sys_admin_player_ban_unset($user, $player_banned_row, ($reason = sys_get_param_str('why')) ? $reason : $lang['sys_unbanned']);

  $DoneMessage = $lang['adm_unbn_thpl'] . " " . $name_output . " " . $lang['adm_unbn_isbn'];
  AdminMessage($DoneMessage, $lang['adm_unbn_ttle']);
};

$parse['name'] = $name_output;
$parse['mode'] = $mode;

display(parsetemplate(gettemplate("admin/admin_ban", true), $parse), $lang['adm_ban_title'], false, '', true);
