<?php
/* Aca se crean las alianzas... */
if ($POST_tag) {
  if (!$POST_name) {
    message($lang['have_not_name'], $lang['make_alliance']);
  }

  $tagquery = doquery("SELECT * FROM {{table}} WHERE `ally_tag` ='{$POST_tag}'", 'alliance', true);

  if ($tagquery) {
    message(str_replace('%s', $POST_tag, $lang['always_exist']), $lang['make_alliance']);
  }

  $tagquery = doquery("SELECT * FROM {{table}} WHERE `ally_name` ='{$POST_name}'", 'alliance', true);

  if ($tagquery) {
    message(str_replace('%s', $POST_name, $lang['always_exist']), $lang['make_alliance']);
  }

  doquery("INSERT INTO {{table}} SET
  `ally_name`='{$POST_name}',
  `ally_tag`='{$POST_tag}' ,
  `ally_owner`='{$user['id']}',
  `ally_owner_range`='{$lang['ali_leaderRank']}',
  `ally_members`='1',
  `ranklist` = '{$lang['ali_defaultRankName']},0,0,0,0,0',
  `ally_register_time`=" . time() , "alliance");

  $allyquery = doquery("SELECT * FROM {{table}} WHERE `ally_tag` ='{$POST_tag}'", 'alliance', true);

  doquery("UPDATE {{table}} SET
  `ally_id`='{$allyquery['id']}',
  `ally_name`='{$allyquery['ally_name']}',
  `ally_register_time`='" . time() . "'
  WHERE `id`='{$user['id']}'", "users");

  $page = MessageForm(str_replace('%s', $POST_tag, $lang['ally_maked']),

    str_replace('%s', $POST_tag, $lang['ally_been_maked']) . "<br><br>", "", $lang['Ok']);
} else {
  $page .= parsetemplate(gettemplate('ali_external_make'), $lang);
}

display($page, $lang['make_alliance']);
?>