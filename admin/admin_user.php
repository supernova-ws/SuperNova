<?php

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage($lang['adm_err_denied']);
}

lng_include('admin');

$user_id = sys_get_param_id('uid');
if(!($user_row = db_user_by_id($user_id))) {
  AdminMessage(sprintf($lang['adm_dm_user_none'], $user_id));
}

$template = gettemplate('admin/admin_user', true);

$template->assign_vars($user_row);

display($template, htmlentities("[{$user_row['id']}] {$user_row['username']}", ENT_QUOTES, 'UTF-8'), false, '', true);
