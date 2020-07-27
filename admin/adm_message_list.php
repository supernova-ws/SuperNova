<?php

/**
 * admin/adm_message_list.php
 *
 * @version 2
 * @copyright 2014 by Gorlum for http://supernova.ws/
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

$template = SnTemplate::gettemplate('admin/adm_messagelist', true);

$int_type_selected = sys_get_param_int('int_type_selected', -1);
$allowed_types = array(
  -1 => array(
    'VALUE' => -1,
    'TEXT' => $lang['adm_pay_filter_all']
  ),
);
$template->assign_block_vars('int_type_selected', $allowed_types[-1]);
foreach($sn_message_class_list as $key => $value)
{
  if($key == MSG_TYPE_NEW || $key == MSG_TYPE_OUTBOX)
  {
    continue;
  }

  $template->assign_block_vars('int_type_selected', $allowed_types[$key] = array(
    'VALUE' => $key,
    'TEXT' => $lang['msg_class'][$key],
  ));
}


$message_delete = sys_get_param_id('msg_del');
if(sys_get_param('str_delete_selected') && is_array($message_delete = sys_get_param('selected')) && !empty($message_delete))
{
  $message_delete = implode(', ', $message_delete);
}

if($message_delete)
{
  doquery("DELETE FROM {{messages}} WHERE `message_id` in ({$message_delete});");
  $template->assign_block_vars('result', array('MESSAGE' => sprintf($lang['mlst_messages_deleted'], $message_delete)));
}


if(sys_get_param('str_delete_date') && checkdate($month = sys_get_param_id('delete_month'), $day = sys_get_param_id('delete_day'), $year = sys_get_param_id('delete_year')))
{
  $delete_date = "{$year}-{$month}-{$day}";
  doquery("DELETE FROM {{messages}} WHERE message_time <= UNIX_TIMESTAMP('{$delete_date}')" . ($int_type_selected >= 0 ? " AND `message_type` = {$int_type_selected}" : ''));
  $template->assign_block_vars('result', array('MESSAGE' => sprintf($lang['mlst_messages_deleted_date'], $allowed_types[$int_type_selected]['TEXT'], $delete_date)));
}


$page_max = doquery('SELECT COUNT(*) AS `max` FROM `{{messages}}`' . ($int_type_selected >= 0 ? " WHERE `message_type` = {$int_type_selected};" : ''), true);
$page_max = ceil($page_max['max'] / 25);

$int_page_current = min(sys_get_param_id('int_page_current', 1), $page_max);

if(sys_get_param('page_prev') && $int_page_current > 1)
{
  $int_page_current--;
}
elseif(sys_get_param('page_next') && $int_page_current < $page_max)
{
  $int_page_current++;
}

for($i = 1; $i <= $page_max; $i++)
{
  $template->assign_block_vars('page', array('NUMBER' => $i));
}

$StartRec = ($int_page_current - 1) * 25;

$Messages = db_message_list_admin_by_type($int_type_selected, $StartRec);
while($row = db_fetch($Messages))
{
  $row['FROM'] = htmlentities($row['FROM'], ENT_COMPAT, 'UTF-8');
  $row['OWNER_NAME'] = htmlentities($row['OWNER_NAME'], ENT_COMPAT, 'UTF-8');
  $row['TEXT'] = nl2br($row['TEXT']);
  $template->assign_block_vars('message', $row);
}

$template->assign_vars(array(
  'PAGE_MAX' => $page_max,
  'PAGE_CURRENT' => $int_page_current,
  'TYPE_SELECTED' => $int_type_selected,
));

SnTemplate::display($template, $lang['mlst_title']);
