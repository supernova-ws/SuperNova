<?php

use Alliance\DBStaticAlly;

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_admin_applications) {
  SnTemplate::messageBox($lang['Denied_access'], $lang['requests_admin']);
}

$d = sys_get_param_id('d');
if($d) {
  doquery("UPDATE {{alliance_requests}} SET `request_denied` = 1, `request_text` = '{$lang['ali_req_deny_reason']}' WHERE `id_user`= {$d} LIMIT 1;");
}

$id_user = sys_get_param_id('id_user');
if($id_user) {
  $ally_name_safe = db_escape($ally['ally_name']);
  $ally_tag_safe = db_escape($ally['ally_tag']);
  db_user_set_by_id($id_user, "`ally_id` = '{$ally['id']}', `ally_name` = '{$ally_name_safe}', `ally_tag` = '{$ally_tag_safe}', `ally_register_time` = " . SN_TIME_NOW . ", `ally_rank_id` = 0");
  doquery("UPDATE {{alliance}} SET `ally_members`= `ally_members` + 1 WHERE `id`='{$ally['id']}'");
  doquery("DELETE FROM {{alliance_requests}} WHERE `id_user`= '{$id_user}' LIMIT 1;");
}

$template = SnTemplate::gettemplate('ali_admin_request', true);

$query = DBStaticAlly::db_ally_request_list($ally['id']);
while ($ally_request_row = db_fetch($query)) {
  $template->assign_block_vars('alliance_request', array(
    'USER_ID'   => $ally_request_row['id_user'],
    'USER_NAME' => $ally_request_row['username'],
    'TIME'      => date(FMT_DATE_TIME, $ally_request_row['request_time']),
    'TEXT'      => HelperString::nl2br($ally_request_row['request_text']),
    'DENIED'    => $ally_request_row['request_denied'],
  ));
}

$template->assign_vars(array(
  'ally_tag' => $ally['ally_tag'],
));

SnTemplate::display($template, $lang['requests_admin']);
