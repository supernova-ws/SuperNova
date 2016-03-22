<?php
// Pretty Safe
// TODO: Add ally_tag to usertable

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$ally_tag_raw = sys_get_param_str_unsafe('tag');
$ally_tag = db_escape($ally_tag_raw);
$ally_name_raw = sys_get_param_str_unsafe('name');
$ally_name = db_escape($ally_name_raw);

if($ally_tag) {
  if(!$ally_name_raw || !$ally_tag_raw) {
    message($lang['have_not_name'], $lang['make_alliance']);
  }

  $query = db_ally_get_by_name_or_tag($ally_tag, $ally_name);
  if($query) {
    message(str_replace('%s', $query['ally_tag'] == $ally_tag_raw ? $ally_tag_raw : $ally_name_raw, $lang['always_exist']), $lang['make_alliance']);
  }

  db_ally_insert($ally_name, $ally_tag, $user, $lang);
  $ally_id = db_insert_id();
  db_user_set_by_id($user['id'], "`ally_tag` = '{$ally_tag}', `ally_id`= {$ally_id}, `ally_name`='{$ally_name}', `ally_register_time`= " . SN_TIME_NOW . "");

  $ally_user = classSupernova::db_ins_record(LOC_USER, "`username` = '[{$ally_tag}]', `register_time` = " . SN_TIME_NOW . ", `user_as_ally` = {$ally_id}");
  // $ally_user_id = db_insert_id();
  $ally_user_id = is_array($ally_user) ? $ally_user['id'] : 'NULL';
  db_ally_update_ally_user($ally_user_id, $ally_id);

  message(str_replace('%s', $ally_tag_raw, $lang['ally_been_maked']), str_replace('%s', $ally_tag_raw, $lang['ally_maked']));
} else {
  $page .= parsetemplate(gettemplate('ali_external_make'), $lang);
}

display($page, $lang['make_alliance']);
