<?php

/**
 * buddy.php
 *   Friend system
 *
 * v3.0 Fully rewrote by Gorlum for http://supernova.ws
 *   [!] Full rewrote from scratch
 *
 * Idea from buddy.php Created by Perberos. All rights reversed (C) 2006
 * */
include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('buddy');

$result = array();
try
{
  sn_db_transaction_start();

  if($buddy_id = sys_get_param_id('buddy_id'))
  {
    $buddy_row = doquery("SELECT BUDDY_SENDER_ID, BUDDY_OWNER_ID, BUDDY_STATUS FROM {{buddy}} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1 FOR UPDATE;", true);
    if(!is_array($buddy_row))
    {
      throw new exception('buddy_err_not_exist', ERR_ERROR);
    }

    switch($mode = sys_get_param_str('mode'))
    {
      case 'accept':
        if($buddy_row['BUDDY_SENDER_ID'] == $user['id'])
        {
          throw new exception('buddy_err_accept_own', ERR_ERROR);
        }

        if($buddy_row['BUDDY_OWNER_ID'] != $user['id'])
        {
          throw new exception('buddy_err_accept_alien', ERR_ERROR);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE)
        {
          throw new exception('buddy_err_accept_already', ERR_WARNING);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED)
        {
          throw new exception('buddy_err_accept_denied', ERR_ERROR);
        }

        doquery("UPDATE {{buddy}} SET `BUDDY_STATUS` = " . BUDDY_REQUEST_ACTIVE . " WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
        if(SN::$db->db_affected_rows())
        {
          msg_send_simple_message($buddy_row['BUDDY_SENDER_ID'], $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, $user['username'], $lang['buddy_msg_accept_title'],
            sprintf($lang['buddy_msg_accept_text'], $user['username']));
          sn_db_transaction_commit();
          throw new exception('buddy_err_accept_none', ERR_NONE);
        }
        else
        {
          throw new exception('buddy_err_accept_internal', ERR_ERROR);
        }
      break;

      case 'delete':
        if($buddy_row['BUDDY_SENDER_ID'] != $user['id'] && $buddy_row['BUDDY_OWNER_ID'] != $user['id'])
        {
          throw new exception('buddy_err_delete_alien', ERR_ERROR);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE) // Existing friendship
        {
          $ex_friend_id = $buddy_row['BUDDY_SENDER_ID'] == $user['id'] ? $buddy_row['BUDDY_OWNER_ID'] : $buddy_row['BUDDY_SENDER_ID'];

          msg_send_simple_message($ex_friend_id, $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, $user['username'], $lang['buddy_msg_unfriend_title'],
            sprintf($lang['buddy_msg_unfriend_text'], $user['username']));

          doquery("DELETE FROM {{buddy}} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
          sn_db_transaction_commit();
          throw new exception('buddy_err_unfriend_none', ERR_NONE);
        }
        elseif($buddy_row['BUDDY_SENDER_ID'] == $user['id']) // Player's outcoming request - either denied or waiting
        {
          doquery("DELETE FROM {{buddy}} WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
          sn_db_transaction_commit();
          throw new exception('buddy_err_delete_own', ERR_NONE);
        }
        elseif($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_WAITING) // Deny incoming request
        {
          msg_send_simple_message($buddy_row['BUDDY_SENDER_ID'], $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, $user['username'], $lang['buddy_msg_deny_title'],
            sprintf($lang['buddy_msg_deny_text'], $user['username']));

          doquery("UPDATE {{buddy}} SET `BUDDY_STATUS` = " . BUDDY_REQUEST_DENIED . " WHERE `BUDDY_ID` = {$buddy_id} LIMIT 1;");
          sn_db_transaction_commit();
          throw new exception('buddy_err_deny_none', ERR_NONE);
        }
      break;
    }
  }

  // New request?
  // Checking for user ID - in case if it was request from outside buddy system
  if($new_friend_id = sys_get_param_id('request_user_id'))
  {
    $new_friend_row = db_user_by_id($new_friend_id, true, '`id`, `username`');
  }
  elseif($new_friend_name = sys_get_param_str_unsafe('request_user_name'))
  {
    $new_friend_row = db_user_by_username($new_friend_name);
    $new_friend_name = db_escape($new_friend_name);
  }

  if($new_friend_row['id'] == $user['id'])
  {
    unset($new_friend_row);
    throw new exception('buddy_err_adding_self', ERR_ERROR);
  }

  // Checking for user name & request text - in case if it was request to adding new request
  if(isset($new_friend_row['id']) && ($new_request_text = sys_get_param_str('request_text')))
  {
    $check_relation = doquery("SELECT `BUDDY_ID` FROM {{buddy}} WHERE
      (`BUDDY_SENDER_ID` = {$user['id']} AND `BUDDY_OWNER_ID` = {$new_friend_row['id']})
      OR
      (`BUDDY_SENDER_ID` = {$new_friend_row['id']} AND `BUDDY_OWNER_ID` = {$user['id']})
      LIMIT 1 FOR UPDATE;"
    , true);
    if(isset($check_relation['BUDDY_ID']))
    {
      throw new exception('buddy_err_adding_exists', ERR_WARNING);
    }

    msg_send_simple_message($new_friend_row['id'], $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, $user['username'], $lang['buddy_msg_adding_title'],
      sprintf($lang['buddy_msg_adding_text'], $user['username']));

    doquery($q = "INSERT INTO {{buddy}} SET `BUDDY_SENDER_ID` = {$user['id']}, `BUDDY_OWNER_ID` = {$new_friend_row['id']}, `BUDDY_REQUEST` = '{$new_request_text}';");
    sn_db_transaction_commit();
    throw new exception('buddy_err_adding_none', ERR_NONE);
  }
}
catch(exception $e)
{
  $result[] = array(
    'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
    'MESSAGE' => $lang[$e->getMessage()],
  );
}
// TODO - Это просто заглушка. Дойдут руки - разобраться, в чём проблема
sn_db_transaction_rollback();

$query = db_buddy_list_by_user($user['id']);
while($row = db_fetch($query))
{
  $row['BUDDY_REQUEST'] = HelperString::nl2br($row['BUDDY_REQUEST']);

  $row['BUDDY_ACTIVE'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE;
  $row['BUDDY_DENIED'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED;
  $row['BUDDY_INCOMING'] = $row['BUDDY_OWNER_ID'] == $user['id'];
  $row['BUDDY_ONLINE'] = floor((SN_TIME_NOW - $row['onlinetime']) / 60);

  $template_result['.']['buddy'][] = $row;
}

$template_result += array(
  'PAGE_HEADER' => $lang['buddy_buddies'],
  'PAGE_HINT' => $lang['buddy_hint'],
  'USER_ID' => $user['id'],
  'REQUEST_USER_ID' => isset($new_friend_row['id']) ? $new_friend_row['id'] : 0,
  'REQUEST_USER_NAME' => isset($new_friend_row['username']) ? $new_friend_row['username'] : '',
);

$template_result['.']['result'] = is_array($template_result['.']['result']) ? $template_result['.']['result'] : array();
$template_result['.']['result'] += $result;

$template = SnTemplate::gettemplate('buddy', true);
$template->assign_recursive($template_result);

SnTemplate::display($template);
