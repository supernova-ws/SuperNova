<?php

/**
 * Project "SuperNova.WS" copyright (c) 2009-2017 Gorlum
 * @version #45d0#
 **/

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

global $user, $lang;

$delete = sys_get_param_str('delete');
$deleteall = sys_get_param_str('deleteall');

if ($delete) {
  doquery("DELETE FROM `{{chat}}` WHERE `messageid` = {$delete};");
} elseif ($deleteall == 'yes') {
  doquery("DELETE FROM `{{chat}}`;");
}

$template = SnTemplate::gettemplate('admin/admin_chat', true);

$query = doquery("SELECT * FROM `{{chat}}` ORDER BY `messageid` DESC LIMIT 25;");
while ($e = db_fetch($query)) {
  $template->assign_block_vars('message', array(
    'ID'          => $e['messageid'],
    'TIMESTAMP'   => str_replace(' ', '&nbsp;', date(FMT_DATE_TIME, $e['timestamp'])),
    'PLAYER_NAME' => $e['user'],
    'MESSAGE'     => nl2br($e['message']),
  ));
}

$template->assign_vars(array(
  'PAGE_HEADER' => $lang['adm_ch_ttle'],
  'msg_num'     => SN::$gc->db->db_num_rows($query),
));

SnTemplate::display($template);
