<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

// Code - CLEAR!
// Template - CLEAR!

if ($user['ally_id']) {
  message($lang['ali_req_inAlly'], $lang['ali_req_title']);
}

if ($user_request['id_ally'])
{
  $ally = doquery("SELECT * FROM {{alliance}} WHERE `id` ='{$user_request['id_ally']}'", '', true);

  if (sys_get_param_str('bcancel'))
  {
    doquery("DELETE FROM {{alliance_requests}} WHERE `id_user` = {$user['id']};");
    header('Location: alliance.php');
  }

  if($user_request['request_denied'])
  {
    $lang['request_text'] = sprintf($lang['ali_req_deny_msg'], $ally['ally_tag'], $user_request['request_text']);
  }
  else
  {
    $lang['request_text'] = sprintf($lang['ali_req_waiting'], $ally['ally_tag']);
  }
  $page = parsetemplate(gettemplate('ali_request_waiting'), $lang);

  display($page, $lang['ali_req_title']);
}

$POST_text = sys_get_param_str('text');
if ($POST_text)
{
  $id_ally = sys_get_param_int('a');
  doquery("INSERT INTO {{alliance_requests}} SET `id_user` = {$user['id']}, `id_ally`='{$id_ally}', request_text ='{$POST_text}', request_time='{$time_now}';");
  header('Location: alliance.php');
}

$ally = doquery("SELECT * FROM {{alliance}} WHERE `id` ='{$id_ally}'", '', true);

if(!$ally)
{
  message($lang['ali_sys_notFound'], $lang['ali_req_title']);
}

if($ally['ally_request_notallow'])
{
  message($lang['ali_req_not_allowed'], $lang['ali_req_title']);
}

$text_apply = $ally['ally_request'] ? $ally['ally_request'] : $lang['ali_req_template'];

$parse = $lang;
$parse['allyid'] = $id_ally;
$parse['chars_count'] = strlen($text_apply);
$parse['text_apply'] = $text_apply;
$parse['ali_req_title'] = "{$lang['ali_req_title']} [{$ally['ally_tag']}]";

display(parsetemplate(gettemplate('ali_request'), $parse), $lang['ali_req_title']);

?>
