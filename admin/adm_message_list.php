<?php

/**
 * admin/adm_message_list.php
 *
 * @version 2
 * @copyright 2014 by Gorlum for http://supernova.ws/
 *
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$template = gettemplate('admin/adm_messagelist', true);

$int_type_selected = sys_get_param_int('int_type_selected', -1);
$allowed_types = array(
  -1 => array(
    'VALUE' => -1,
    'TEXT'  => classLocale::$lang['adm_pay_filter_all']
  ),
);
$template->assign_block_vars('int_type_selected', $allowed_types[-1]);
foreach(DBStaticMessages::$snMessageClassList as $key => $value) {
  if($key == MSG_TYPE_NEW || $key == MSG_TYPE_OUTBOX) {
    continue;
  }

  $template->assign_block_vars('int_type_selected', $allowed_types[$key] = array(
    'VALUE' => $key,
    'TEXT'  => classLocale::$lang['msg_class'][$key],
  ));
}


$deletedMessages = '';
if($idMessageDelete = sys_get_param_id('msg_del')) {
  DBStaticMessages::db_message_delete_by_id($idMessageDelete);
  $deletedMessages = $idMessageDelete;
} elseif(sys_get_param('str_delete_selected') && is_array($message_delete = sys_get_param('selected')) && !empty($message_delete)) {
  $message_delete = implode(', ', $message_delete);
  DBStaticMessages::db_message_list_delete_set($message_delete);
  $deletedMessages = $message_delete;
}

if($deletedMessages) {
  $template->assign_block_vars('result', array('MESSAGE' => sprintf(classLocale::$lang['mlst_messages_deleted'], $deletedMessages)));
}


if(sys_get_param('str_delete_date') && checkdate($month = sys_get_param_id('delete_month'), $day = sys_get_param_id('delete_day'), $year = sys_get_param_id('delete_year'))) {
  $delete_date = "{$year}-{$month}-{$day}";
  DBStaticMessages::db_message_list_delete_by_date($delete_date, $int_type_selected);
  $template->assign_block_vars('result', array('MESSAGE' => sprintf(classLocale::$lang['mlst_messages_deleted_date'], $allowed_types[$int_type_selected]['TEXT'], $delete_date)));
}


$page_max = DBStaticMessages::db_message_count_by_type($int_type_selected);
$page_max = ceil($page_max['max'] / 25);

$int_page_current = min(sys_get_param_id('int_page_current', 1), $page_max);

if(sys_get_param('page_prev') && $int_page_current > 1) {
  $int_page_current--;
} elseif(sys_get_param('page_next') && $int_page_current < $page_max) {
  $int_page_current++;
}

for($i = 1; $i <= $page_max; $i++) {
  $template->assign_block_vars('page', array('NUMBER' => $i));
}


$StartRec = ($int_page_current - 1) * 25;

$Messages = DBStaticMessages::db_message_list_admin_by_type($int_type_selected, $StartRec);
while($row = db_fetch($Messages)) {
  $row['FROM'] = htmlentities($row['FROM'], ENT_COMPAT, 'UTF-8');
  $row['OWNER_NAME'] = htmlentities($row['OWNER_NAME'], ENT_COMPAT, 'UTF-8');
  $row['TEXT'] = nl2br($row['TEXT']);
  $template->assign_block_vars('message', $row);
}

$template->assign_vars(array(
  'PAGE_MAX'      => $page_max,
  'PAGE_CURRENT'  => $int_page_current,
  'TYPE_SELECTED' => $int_type_selected,
));

display(parsetemplate($template), classLocale::$lang['mlst_title'], false, '', true);
