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
try {
  sn_db_transaction_start();

  if($buddy_id = sys_get_param_id('buddy_id')) {
    $buddy_row = db_buddy_get_row($buddy_id);
    if(!is_array($buddy_row)) {
      throw new exception('buddy_err_not_exist', ERR_ERROR);
    }

    switch($mode = sys_get_param_str('mode')) {
      case 'accept':
        if($buddy_row['BUDDY_SENDER_ID'] == $user['id']) {
          throw new exception('buddy_err_accept_own', ERR_ERROR);
        }

        if($buddy_row['BUDDY_OWNER_ID'] != $user['id']) {
          throw new exception('buddy_err_accept_alien', ERR_ERROR);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE) {
          throw new exception('buddy_err_accept_already', ERR_WARNING);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED) {
          throw new exception('buddy_err_accept_denied', ERR_ERROR);
        }

        db_buddy_update_status($buddy_id, BUDDY_REQUEST_ACTIVE);
        if(classSupernova::$db->db_affected_rows()) {
          msg_send_simple_message($buddy_row['BUDDY_SENDER_ID'], $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, $user['username'], classLocale::$lang['buddy_msg_accept_title'],
            sprintf(classLocale::$lang['buddy_msg_accept_text'], $user['username']));
          sn_db_transaction_commit();
          throw new exception('buddy_err_accept_none', ERR_NONE);
        } else {
          throw new exception('buddy_err_accept_internal', ERR_ERROR);
        }
      break;

      case 'delete':
        if($buddy_row['BUDDY_SENDER_ID'] != $user['id'] && $buddy_row['BUDDY_OWNER_ID'] != $user['id']) {
          throw new exception('buddy_err_delete_alien', ERR_ERROR);
        }

        if($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE) // Existing friendship
        {
          $ex_friend_id = $buddy_row['BUDDY_SENDER_ID'] == $user['id'] ? $buddy_row['BUDDY_OWNER_ID'] : $buddy_row['BUDDY_SENDER_ID'];

          msg_send_simple_message($ex_friend_id, $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, $user['username'], classLocale::$lang['buddy_msg_unfriend_title'],
            sprintf(classLocale::$lang['buddy_msg_unfriend_text'], $user['username']));

          db_buddy_delete($buddy_id);
          sn_db_transaction_commit();
          throw new exception('buddy_err_unfriend_none', ERR_NONE);
        } elseif($buddy_row['BUDDY_SENDER_ID'] == $user['id']) // Player's outcoming request - either denied or waiting
        {
          db_buddy_delete($buddy_id);
          sn_db_transaction_commit();
          throw new exception('buddy_err_delete_own', ERR_NONE);
        } elseif($buddy_row['BUDDY_STATUS'] == BUDDY_REQUEST_WAITING) // Deny incoming request
        {
          msg_send_simple_message($buddy_row['BUDDY_SENDER_ID'], $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, $user['username'], classLocale::$lang['buddy_msg_deny_title'],
            sprintf(classLocale::$lang['buddy_msg_deny_text'], $user['username']));

          db_buddy_update_status($buddy_id, BUDDY_REQUEST_DENIED);
          sn_db_transaction_commit();
          throw new exception('buddy_err_deny_none', ERR_NONE);
        }
      break;
    }
  }

  // New request?
  // Checking for user ID - in case if it was request from outside buddy system
  if($new_friend_id = sys_get_param_id('request_user_id')) {
    $new_friend_row = DBStaticUser::db_user_by_id($new_friend_id, true, '`id`, `username`');
  } elseif($new_friend_name = sys_get_param_str_unsafe('request_user_name')) {
    $new_friend_row = DBStaticUser::db_user_by_username($new_friend_name, true, '`id`, `username`');
    $new_friend_name = db_escape($new_friend_name);
  }

  if($new_friend_row['id'] == $user['id']) {
    unset($new_friend_row);
    throw new exception('buddy_err_adding_self', ERR_ERROR);
  }

  // Checking for user name & request text - in case if it was request to adding new request
  if(isset($new_friend_row['id']) && ($new_request_text = sys_get_param_str('request_text'))) {
    $check_relation = db_buddy_check_relation($user, $new_friend_row);
    if(isset($check_relation['BUDDY_ID'])) {
      throw new exception('buddy_err_adding_exists', ERR_WARNING);
    }

    msg_send_simple_message($new_friend_row['id'], $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, $user['username'], classLocale::$lang['buddy_msg_adding_title'],
      sprintf(classLocale::$lang['buddy_msg_adding_text'], $user['username']));

    db_buddy_insert($user, $new_friend_row, $new_request_text);
    sn_db_transaction_commit();
    throw new exception('buddy_err_adding_none', ERR_NONE);
  }
} catch(Exception $e) {
  $result[] = array(
    'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
    'MESSAGE' => classLocale::$lang[$e->getMessage()],
  );
}
// TODO - Это просто заглушка. Дойдут руки - разобраться, в чём проблема
sn_db_transaction_rollback();

$query = db_buddy_list_by_user($user['id']);
while($row = db_fetch($query)) {
  $row['BUDDY_REQUEST'] = sys_bbcodeParse($row['BUDDY_REQUEST']);

  $row['BUDDY_ACTIVE'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE;
  $row['BUDDY_DENIED'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED;
  $row['BUDDY_INCOMING'] = $row['BUDDY_OWNER_ID'] == $user['id'];
  $row['BUDDY_ONLINE'] = floor((SN_TIME_NOW - $row['onlinetime']) / 60);

  $template_result['.']['buddy'][] = $row;
}

$template_result += array(
  'PAGE_HEADER'       => classLocale::$lang['buddy_buddies'],
  'PAGE_HINT'         => classLocale::$lang['buddy_hint'],
  'USER_ID'           => $user['id'],
  'REQUEST_USER_ID'   => isset($new_friend_row['id']) ? $new_friend_row['id'] : 0,
  'REQUEST_USER_NAME' => isset($new_friend_row['username']) ? $new_friend_row['username'] : '',
);

$template_result['.']['result'] = is_array($template_result['.']['result']) ? $template_result['.']['result'] : array();
$template_result['.']['result'] += $result;

$template = gettemplate('buddy', true);
$template->assign_recursive($template_result);

display($template);
