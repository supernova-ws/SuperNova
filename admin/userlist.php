<?php

/**
 *
 * Project "SuperNova.WS" copyright (c) 2009-2012 Gorlum
 * Release 34
 *
 * userlist.php v2
 *
**/

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

lng_include('admin');

$sort_fields = array(
  SORT_ID => 'id',
  SORT_NAME => 'username',
  SORT_EMAIL => 'email',
  SORT_IP => 'user_lastip',
  SORT_TIME_REGISTERED => 'register_time',
  SORT_TIME_LAST_VISIT => 'onlinetime',
  SORT_TIME_BAN_UNTIL => 'banaday',
);
$sort = sys_get_param_int('sort', SORT_ID);
$sort = $sort_fields[$sort] ? $sort : SORT_ID;

if(($action = sys_get_param_int('action')) && ($user_id = sys_get_param_id('uid')))
{
  $user_selected = doquery("SELECT id, username, password, authlevel FROM {{users}} WHERE `id` = {$user_id} LIMIT 1;", true);
  if($user_selected['authlevel'] < $user['authlevel'] && $user['authlevel'] >= 3)
  {
    switch($action)
    {
      case ACTION_DELETE:
        DeleteSelectedUser($user_id);
        sys_redirect("{$_SERVER['SCRIPT_NAME']}?sort={$sort}");
      break;

      case ACTION_USE:
        // Impersonate
        sn_sys_impersonate($user_selected);
      break;
    }
  }
  else
  {
    // Restricted try to delete user higher or equal level
    AdminMessage($lang['adm_err_denied']);
  }
}

$template = gettemplate('admin/userlist', true);

$multi_ip = array();
$ip_query = doquery("SELECT COUNT(*) as ip_count, user_lastip FROM {{users}} WHERE user_as_ally IS NULL GROUP BY user_lastip HAVING COUNT(*)>1;");
while($ip = mysql_fetch_assoc($ip_query))
{
  $multi_ip[$ip['user_lastip']] = $ip['ip_count'];
}

$query = doquery("SELECT * FROM {{users}} WHERE user_as_ally IS NULL ORDER BY `{$sort_fields[$sort]}` ASC;");
while ($user_row = mysql_fetch_assoc($query))
{
  if($user_row['banaday'])
  {
    $ban_details = doquery("SELECT * FROM {{banned}} WHERE `ban_user_id` = {$user_row['id']} ORDER BY ban_id DESC LIMIT 1", true);
  }

  $template->assign_block_vars('user', array(
    'ID' => $user_row['id'],
    'NAME' => $user_row['username'],
    'NAME_JS' => js_safe_string($user_row['username']),
    'EMAIL' => $user_row['email'],
    'IP' => $user_row['user_lastip'],
    'IP_MULTI' => intval($multi_ip[$user_row['user_lastip']]),
    'TIME_REGISTERED' => date(FMT_DATE_TIME, $user_row['register_time']),
    'TIME_PLAYED' => date(FMT_DATE_TIME, $user_row['onlinetime']),
    'BANNED' => $user_row['banaday'] ? date(FMT_DATE_TIME, $user_row['banaday']) : 0,
    'BAN_DATE' => date(FMT_DATE_TIME, $ban_details['ban_time']),
    'BAN_ISSUER' => $ban_details['ban_issuer_name'],
    'BAN_REASON' => $ban_details['ban_reason'],
    'ACTION' => $user_row['authlevel'] < $user['authlevel'],
    'RESTRICTED' => $user['authlevel'] < 3,
  ));
}

$template->assign_vars(array(
  'USER_COUNT' => mysql_num_rows($query),
  'SORT' => $sort,
));

display($template, $lang['adm_ul_title'], false, '', true);

?>
