<?php

/**
 * @copyright Copyright (c) 2009 by Gorlum for http://supernova.ws
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_MODERATOR);

global $lang, $user;

$mode = sys_get_param_str('mode', 'banit');
$name_unsafe = sys_get_param_str_unsafe('name');
$name_output = sys_safe_output($name_unsafe);
$action = sys_get_param_str('action');

$player_banned_row = db_user_by_username($name_unsafe);
if ($mode == 'banit' && $action) {
  if ($player_banned_row) {
    $reas = $_POST['why'];
    $days = $_POST['days'];
    $hour = $_POST['hour'];
    $mins = $_POST['mins'];
    $secs = $_POST['secs'];

    $BanTime = $days * 86400;
    $BanTime += $hour * 3600;
    $BanTime += $mins * 60;
    $BanTime += $secs;

    sys_admin_player_ban($user, $player_banned_row, $BanTime, $is_vacation = sys_get_param_int('isVacation'), sys_get_param_str('why'));

    $DoneMessage = "{$lang['adm_bn_thpl']} {$name_output} {$lang['adm_bn_isbn']}";

    if ($is_vacation) {
      $DoneMessage .= $lang['adm_bn_vctn'];
    }

    $DoneMessage .= $lang['adm_bn_plnt'];
  } else {
    $DoneMessage = sprintf($lang['adm_bn_errr'], $name_output);
  }

  SnTemplate::messageBoxAdmin($DoneMessage, $lang['adm_ban_title']);
} elseif ($mode == 'unbanit' && $action) {
  sys_admin_player_ban_unset($user, $player_banned_row, ($reason = sys_get_param_str('why')) ? $reason : $lang['sys_unbanned']);

  $DoneMessage = $lang['adm_unbn_thpl'] . " " . $name_output . " " . $lang['adm_unbn_isbn'];
  SnTemplate::messageBoxAdmin($DoneMessage, $lang['adm_unbn_ttle']);
};

$parsetemplate = SnTemplate::gettemplate("admin/admin_ban", true);

$parsetemplate->assign_vars(array(
  'name' => $name_output,
  'mode' => $mode,
));

SnTemplate::display($parsetemplate, $lang['adm_ban_title']);
