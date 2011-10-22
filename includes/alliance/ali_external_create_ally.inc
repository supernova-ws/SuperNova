<?php
// Pretty Safe
// TODO: Add ally_tag to usertable

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$ally_tag_raw = sys_get_param_str_raw('tag');
$ally_tag     = mysql_real_escape_string($ally_tag_raw);
$ally_name_raw = sys_get_param_str_raw('name');
$ally_name     = mysql_real_escape_string($ally_name_raw);

if($ally_tag)
{
  if(!$ally_name)
  {
    message($lang['have_not_name'], $lang['make_alliance']);
  }

  $query = doquery("SELECT ally_tag FROM {{alliance}} WHERE `ally_tag` = '{$ally_tag}' LIMIT 1;", '', true);
  if($query)
  {
    message(str_replace('%s', $ally_tag_raw, $lang['always_exist']), $lang['make_alliance']);
  }

  $query = doquery("SELECT * FROM {{alliance}} WHERE `ally_name` = '{$ally_name}' LIMIT 1;", '', true);
  if ($query)
  {
    message(str_replace('%s', $ally_name_raw, $lang['always_exist']), $lang['make_alliance']);
  }

  doquery("INSERT INTO {{alliance}} SET
    `ally_name` = '{$ally_name}',
  	 `ally_tag` = '{$ally_tag}' ,
    `ally_owner` = '{$user['id']}',
    `ally_owner_range` = '{$lang['ali_leaderRank']}',
    `ally_members` = 1,
    `ranklist` = '{$lang['ali_defaultRankName']},0,0,0,0,0',
    `ally_register_time`= {$time_now}"
  );

  $query = doquery("SELECT id FROM {{alliance}} WHERE `ally_tag` ='{$ally_tag}' LIMIT 1;", '', true);
  doquery("UPDATE {{users}} SET `ally_tag` = '{$ally_tag}', `ally_id`='{$query['id']}', `ally_name`='{$ally_name}', `ally_register_time`= {$time_now} WHERE `id`= {$user['id']} LIMIT 1;");

  $page = MessageForm(str_replace('%s', $ally_tag_raw, $lang['ally_maked']), str_replace('%s', $ally_tag_raw, $lang['ally_been_maked']) . "<br><br>", "", $lang['Ok']);
}
else
{
  $page .= parsetemplate(gettemplate('ali_external_make'), $lang);
}

display($page, $lang['make_alliance']);

?>
