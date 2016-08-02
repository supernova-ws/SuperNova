<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
  classSupernova::$debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_admin_applications) {
  message(classLocale::$lang['Denied_access'], classLocale::$lang['requests_admin']);
}

$d = sys_get_param_id('d');

if($d) {
  DBStaticAlly::db_ally_request_deny($d);
}

$id_user = sys_get_param_id('id_user');

if($id_user) {
  $ally_name_safe = db_escape($ally['ally_name']);
  $ally_tag_safe = db_escape($ally['ally_tag']);
  DBStaticUser::db_user_set_by_id($id_user, "`ally_id` = '{$ally['id']}', `ally_name` = '{$ally_name_safe}', `ally_tag` = '{$ally_tag_safe}', `ally_register_time` = " . SN_TIME_NOW . ", `ally_rank_id` = 0");
  DBStaticAlly::db_ally_update_member_increase($ally);
  DBStaticAlly::db_ally_request_delete_all_when_accepted($id_user);
}

$template = gettemplate('ali_admin_request', true);

$query = DBStaticAlly::db_ally_request_list($ally['id']);
while ($ally_request_row = db_fetch($query)) {
  $template->assign_block_vars('alliance_request', array(
    'USER_ID'   => $ally_request_row['id_user'],
    'USER_NAME' => $ally_request_row['username'],
    'TIME'      => date(FMT_DATE_TIME, $ally_request_row['request_time']),
    'TEXT'      => sys_bbcodeParse($ally_request_row['request_text']),
    'DENIED'    => $ally_request_row['request_denied'],
  ));
}

$template->assign_vars(array(
  'ally_tag' => $ally['ally_tag'],
));

display(parsetemplate($template), classLocale::$lang['requests_admin']);
