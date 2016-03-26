<?php

/**
 * chat.php
 *
 * @version 1.1  - Remade with more robust template by Gorlum for http://supernova.ws
 * @version 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By e-Zobar for XNova
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

if($delete = sys_get_param_str('delete')) {
  db_chat_message_delete($delete);
} elseif(sys_get_param_str('deleteall') == 'yes') {
  db_chat_message_purge();
}

$template = gettemplate('admin/admin_chat', true);

$query = db_chat_message_get_last_25();
$i = 0;
while($e = db_fetch($query)) {
  $i++;
  $template->assign_block_vars('messages', array(
    'ID' => $e['messageid'],
    'TIMESTAMP' => str_replace(' ', '&nbsp;', date(FMT_DATE_TIME, $e['timestamp'])),
    'USER' => $e['user'],
    'MESSAGE' => nl2br($e['message']),
  ));
}

$template->assign_vars(array(
'MSG_NUM'=>  $i,
));

display(parsetemplate($template), "Chat", false, '', true);
