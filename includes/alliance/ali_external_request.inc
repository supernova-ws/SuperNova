<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if ($user['ally_id'])
{
  message($lang['ali_req_inAlly'], $lang['ali_req_title']);
}

if ($user_request['id_ally']) {
  $ally = db_ally_get_by_id($user_request['id_ally']);

  if (sys_get_param_str('bcancel'))
  {
    db_ally_request_delete_by_user($user);
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

  display(gettemplate('ali_request_waiting', true), $lang['ali_req_title']);
}

$id_ally = sys_get_param_id('a');
$POST_text = sys_get_param_str('text');
if ($POST_text)
{
  db_ally_request_insert($user, $id_ally, $POST_text);
  header('Location: alliance.php');
}

$ally = db_ally_get_by_id($id_ally);

if(!$ally)
{
  message($lang['ali_sys_notFound'], $lang['ali_req_title']);
}

if($ally['ally_request_notallow'])
{
  message($lang['ali_req_not_allowed'], $lang['ali_req_title']);
}

$text_apply = $ally['ally_request'] ? $ally['ally_request'] : $lang['ali_req_template'];

$template = gettemplate('ali_request', true);

$template->assign_vars(array(
  'allyid' => $id_ally,
  'chars_count' => strlen($text_apply),
  'text_apply' => $text_apply,
  'ali_req_title' => "{$lang['ali_req_title']} [{$ally['ally_tag']}]",
));

display($template, $lang['ali_req_title']);

?>
