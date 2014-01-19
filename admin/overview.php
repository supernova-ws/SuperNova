<?php

/**
 *
 * admin/overview.php
 *
 * @version 2.0 copyright (c) 2014 Gorlum for http://supernova.ws
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 1)
{
  AdminMessage($lang['adm_err_denied']);
}
elseif($user['authlevel'] < 3)
{
  sys_redirect(SN_ROOT_VIRTUAL . 'admin/banned.php');
}

$TypeSort = sys_get_param_str('type', 'id');
$template = gettemplate('admin/adm_overview', true);

$Last15Mins = doquery("SELECT `id` AS `ID`, `username` AS `NAME`, `user_agent` AS `BROWSER`, `ally_name` AS `ALLY`, `total_points` AS `STAT_POINTS`, `onlinetime` AS `ACTIVITY` FROM {{users}} WHERE `onlinetime` >= '". (time() - 15 * 60) ."' ORDER BY user_as_ally, `". $TypeSort ."` ASC;");
$Count      = 0;
while($TheUser = mysql_fetch_assoc($Last15Mins))
{
  $TheUser['NAME'] = htmlentities($TheUser['NAME'], ENT_COMPAT, 'UTF-8');
  $TheUser['BROWSER'] = htmlentities($TheUser['BROWSER'], ENT_COMPAT, 'UTF-8');
  $TheUser['ALLY'] = htmlentities($TheUser['ALLY'], ENT_COMPAT, 'UTF-8');
  $TheUser['STAT_POINTS'] = pretty_number($TheUser['STAT_POINTS']);
  $TheUser['ACTIVITY'] = pretty_time(SN_TIME_NOW - $TheUser['ACTIVITY']);

  $template->assign_block_vars('user', $TheUser);
  $Count++;
}

$template->assign_vars(array(
  'USERS' => $Count,
  'PAGE_HINT' => $lang['adm_ov_hint'],
));

display($template, $lang['sys_overview'], false, '', true);
