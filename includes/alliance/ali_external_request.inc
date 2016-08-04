<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  classSupernova::$debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if ($user['ally_id'])
{
  message(classLocale::$lang['ali_req_inAlly'], classLocale::$lang['ali_req_title']);
}

if ($user_request['id_ally']) {
  $ally = DBStaticAlly::db_ally_get_by_id($user_request['id_ally']);

  if (sys_get_param_str('bcancel'))
  {
    DBStaticAlly::db_ally_request_delete_own($user['id'], $user_request['id_ally']);
    header('Location: alliance.php');
  }

  if($user_request['request_denied'])
  {
    $request_text = sprintf(classLocale::$lang['ali_req_deny_msg'], $ally['ally_tag'], $user_request['request_text']);
  }
  else
  {
    $request_text = sprintf(classLocale::$lang['ali_req_waiting'], $ally['ally_tag']);
  }

  $template = gettemplate('ali_request_waiting', true);
  $template->assign_vars(array(
    'REQUEST_TEXT' => $request_text,
  ));

  display($template, classLocale::$lang['ali_req_title']);
}

$id_ally = sys_get_param_id('a');
if ($requestTextUnsafe = sys_get_param_str_unsafe('text'))
{
  DBStaticAlly::db_ally_request_insert($user['id'], $id_ally, $requestTextUnsafe);
  header('Location: alliance.php');
}

$ally = DBStaticAlly::db_ally_get_by_id($id_ally);

if(!$ally)
{
  message(classLocale::$lang['ali_sys_notFound'], classLocale::$lang['ali_req_title']);
}

if($ally['ally_request_notallow'])
{
  message(classLocale::$lang['ali_req_not_allowed'], classLocale::$lang['ali_req_title']);
}

$text_apply = $ally['ally_request'] ? $ally['ally_request'] : classLocale::$lang['ali_req_template'];

$template = gettemplate('ali_request', true);

$template->assign_vars(array(
  'allyid' => $id_ally,
  'chars_count' => strlen($text_apply),
  'text_apply' => $text_apply,
  'ALLY_TAG' => $ally['ally_tag'],
));

display($template, classLocale::$lang['ali_req_title']);
