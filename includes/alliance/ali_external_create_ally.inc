<?php
// Pretty Safe
// TODO: Add ally_tag to usertable

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
  classSupernova::$debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$ally_tag_unsafe = sys_get_param_str_unsafe('tag');
$ally_tag = db_escape($ally_tag_unsafe);
$ally_name_unsafe = sys_get_param_str_unsafe('name');
$ally_name = db_escape($ally_name_unsafe);

if($ally_tag) {
  if(!$ally_name_unsafe || !$ally_tag_unsafe) {
    message(classLocale::$lang['have_not_name'], classLocale::$lang['make_alliance']);
  }

  $query = DBStaticAlly::db_ally_get_by_name_or_tag($ally_tag, $ally_name);
  if($query) {
    message(str_replace('%s', $query['ally_tag'] == $ally_tag_unsafe ? $ally_tag_unsafe : $ally_name_unsafe, classLocale::$lang['always_exist']), classLocale::$lang['make_alliance']);
  }

  DBStaticAlly::db_ally_insert($ally_name_unsafe, $ally_tag_unsafe, $user['id']);
  $ally_id = classSupernova::$db->db_insert_id();
  DBStaticUser::db_user_set_by_id_DEPRECATED($user['id'], "`ally_tag` = '{$ally_tag}', `ally_id`= {$ally_id}, `ally_name`='{$ally_name}', `ally_register_time`= " . SN_TIME_NOW . "");

  $ally_user = classSupernova::$gc->cacheOperator->db_ins_record(LOC_USER, array(
    'username'      => "[" . $ally_tag_unsafe . "]",
    'register_time' => SN_TIME_NOW,
    'user_as_ally'  => $ally_id,
  ));
  // $ally_user_id = db_insert_id();
  $ally_user_id = is_array($ally_user) ? $ally_user['id'] : null;
  DBStaticAlly::db_ally_update_ally_user($ally_user_id, $ally_id);

  message(str_replace('%s', $ally_tag_unsafe, classLocale::$lang['ally_been_maked']), str_replace('%s', $ally_tag_unsafe, classLocale::$lang['ally_maked']));
} else {
  $page .= parsetemplate(gettemplate('ali_external_make'), classLocale::$lang);
}

display($page, classLocale::$lang['make_alliance']);
