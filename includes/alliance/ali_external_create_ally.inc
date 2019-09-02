<?php
// Pretty Safe
// TODO: Add ally_tag to usertable

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$ally_tag_raw = sys_get_param_str_unsafe('tag');
$ally_tag     = db_escape($ally_tag_raw);
$ally_name_raw = sys_get_param_str_unsafe('name');
$ally_name     = db_escape($ally_name_raw);

if($ally_tag)
{
  if(!$ally_name_raw || !$ally_tag_raw)
  {
    SnTemplate::messageBox($lang['have_not_name'], $lang['make_alliance']);
  }

  $query = doquery("SELECT ally_tag FROM {{alliance}} WHERE `ally_tag` = '{$ally_tag}' or `ally_name` = '{$ally_name}' LIMIT 1;", true);
  if($query)
  {
    SnTemplate::messageBox(str_replace('%s', $query['ally_tag'] == $ally_tag_raw ? $ally_tag_raw : $ally_name_raw, $lang['always_exist']), $lang['make_alliance']);
  }

  $ally = doquery("INSERT INTO {{alliance}} SET
    `ally_name` = '{$ally_name}',
    `ally_tag` = '{$ally_tag}',
    `ally_owner` = '{$user['id']}',
    `ally_owner_range` = '{$lang['ali_leaderRank']}',
    `ally_members` = 1,
    `ranklist` = '{$lang['ali_defaultRankName']},0,0,0,0,0',
    `ally_register_time`= " . SN_TIME_NOW
  );
  $ally_id = db_insert_id();
  db_user_set_by_id($user['id'], "`ally_tag` = '{$ally_tag}', `ally_id`= {$ally_id}, `ally_name`='{$ally_name}', `ally_register_time`= " . SN_TIME_NOW . "");

  $ally_user = SN::db_ins_record(LOC_USER, "`username` = '[{$ally_tag}]', `register_time` = " . SN_TIME_NOW . ", `user_as_ally` = {$ally_id}");
  // $ally_user_id = db_insert_id();
  $ally_user_id = is_array($ally_user) ? $ally_user['id'] : 'NULL';
  doquery("UPDATE {{alliance}} SET ally_user_id = {$ally_user_id} WHERE id = {$ally_id} LIMIT 1;");

  SnTemplate::messageBox(str_replace('%s', $ally_tag_raw, $lang['ally_been_maked']), str_replace('%s', $ally_tag_raw, $lang['ally_maked']));
}

$template = SnTemplate::gettemplate('ali_external_make', true);
SnTemplate::display($template, $lang['make_alliance']);
