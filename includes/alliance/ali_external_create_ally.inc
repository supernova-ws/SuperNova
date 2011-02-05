<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

/* Aca se crean las alianzas... */
if ($POST_tag) {
  if (!$POST_name) {
    message($lang['have_not_name'], $lang['make_alliance']);
  }

  $tagquery = doquery("SELECT * FROM {{alliance}} WHERE `ally_tag` ='{$POST_tag}'", '', true);

  if ($tagquery) {
    message(str_replace('%s', $POST_tag, $lang['always_exist']), $lang['make_alliance']);
  }

  $tagquery = doquery("SELECT * FROM {{alliance}} WHERE `ally_name` ='{$POST_name}'", '', true);

  if ($tagquery) {
    message(str_replace('%s', $POST_name, $lang['always_exist']), $lang['make_alliance']);
  }

  doquery("INSERT INTO {{alliance}} SET
  `ally_name`='{$POST_name}',
  `ally_tag`='{$POST_tag}' ,
  `ally_owner`='{$user['id']}',
  `ally_owner_range`='{$lang['ali_leaderRank']}',
  `ally_members`='1',
  `ranklist` = '{$lang['ali_defaultRankName']},0,0,0,0,0',
  `ally_register_time`=" . time());

  $allyquery = doquery("SELECT * FROM {{alliance}} WHERE `ally_tag` ='{$POST_tag}'", '', true);

  doquery("UPDATE {{users}} SET
  `ally_id`='{$allyquery['id']}',
  `ally_name`='{$allyquery['ally_name']}',
  `ally_register_time`='" . time() . "'
  WHERE `id`='{$user['id']}'");

  $page = MessageForm(str_replace('%s', $POST_tag, $lang['ally_maked']),

    str_replace('%s', $POST_tag, $lang['ally_been_maked']) . "<br><br>", "", $lang['Ok']);
} else {
  $page .= parsetemplate(gettemplate('ali_external_make'), $lang);
}

display($page, $lang['make_alliance']);
?>