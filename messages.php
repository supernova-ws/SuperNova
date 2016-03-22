<?php

/**
 * messages.php
 * Handles internal message system
 *
 * @package messages
 * @version 3.0
 *
 * Revision History
 * ================
 *
 * 3.0 - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
 *   [!] Full rewrite
 *
 * 2.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [!] Fully rewrote MessPageMode = 'show' part
 *   [~] All HTML code from 'show' part moved to messages.tpl
 *   [~] Tweaks and optimizations
 *
 * 1.5 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [~] Replaced table 'galaxy' with table 'planets'
 *
 * 1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [~] Security checked & verified for SQL-injection by Gorlum for http://supernova.ws
 *
 * 1.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [+] "Outbox" added
 *
 * 1.2 - copyright 2008 by Chlorel for XNova
 *   [+] Regroupage des 2 fichiers vers 1 seul plus simple a mettre en oeuvre et a gerer !
 *
 * 1.1 - Mise a plat, linearisation, suppression des doublons / triplons / 'n'gnions dans le code (Chlorel)
 *
 * 1.0 - Version originelle (Tom1991)
 *
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('messages');

$mode = sys_get_param_str('msg_delete') ? 'delete' : sys_get_param_str('mode');
$current_class = sys_get_param_int('message_class');
if(!isset($sn_message_class_list[$current_class])) {
  $current_class = 0;
  $mode = '';
}

switch($mode) {
  case 'write':
    $error_list = array();
    $template = gettemplate('msg_message_compose', true);

    $recipient_name = sys_get_param_str_unsafe('recipient_name');
    if($recipient_name) {
      $recipient_row = db_user_by_username($recipient_name);
    }

    if(!$recipient_row) {
      $recipient_id = sys_get_param_id('id');
      $recipient_row = db_user_by_id($recipient_id);
      if(!$recipient_row) {
        $recipient_id = 0;
      }
    }

    if($recipient_row) {
      $recipient_id = $recipient_row['id'];
      $recipient_name = $recipient_row['username'];
    }

    if($recipient_id == $user['id']) {
      $error_list[] = array('MESSAGE' => $lang['msg_err_self_send'], 'STATUS' => ERR_ERROR);
    }

    $re = 0;
    $subject = sys_get_param_str('subject');
    while(strpos($subject, $lang['msg_answer_prefix']) !== false) {
      $subject = substr($subject, strlen($lang['msg_answer_prefix']));
      $re++;
    }
    $re ? $subject = $lang['msg_answer_prefix'] . $subject : false;

    if(sys_get_param_str('msg_send')) {
      $subject = $subject ? $subject : $lang['msg_subject_default'];

      if(!$recipient_id) {
        $error_list[] = array('MESSAGE' => $lang['msg_err_player_not_found'], 'STATUS' => ERR_ERROR);
      }

      $text = sys_get_param_str('text');
      if(!$text) {
        $error_list[] = array('MESSAGE' => $lang['msg_err_no_text'], 'STATUS' => ERR_ERROR);
      }

      if(empty($error_list)) {
        $error_list[] = array('MESSAGE' => $lang['msg_not_message_sent'], 'STATUS' => ERR_NONE);

        $user_safe_name = db_escape($user['username']);
        $recipient_name = db_escape($recipient_name);
        msg_send_simple_message($recipient_id, $user['id'], SN_TIME_NOW, MSG_TYPE_PLAYER, "{$user_safe_name} [{$user['galaxy']}:{$user['system']}:{$user['planet']}]", $subject, $text, true);

        //$recipient_id = 0;
        //$recipient_name = '';
        //$subject = '';
        $text = '';

        $msg_sent = true;
      } else {
        $subject = sys_get_param_str_unsafe('subject');
        $text = sys_get_param_str_unsafe('text');
      }
      $recipient_name = sys_get_param_str_unsafe('recipient_name');
    }

    $subject = $subject ? $subject : $lang['msg_subject_default'];

    $template->assign_vars(array(
      'RECIPIENT_ID'   => $recipient_id,
      'RECIPIENT_NAME' => htmlspecialchars($recipient_name),
      'SUBJECT'        => htmlspecialchars($subject),
      'TEXT'           => htmlspecialchars($text),
    ));

    foreach($error_list as $error_message) {
      $template->assign_block_vars('result', $error_message);
    }

//    $message_query = doquery(
//      "SELECT * FROM {{messages}}
//        WHERE
//          `message_type` = '" . MSG_TYPE_PLAYER . "' AND
//          ((`message_owner` = '{$user['id']}' AND `message_sender` = '{$recipient_id}')
//          OR
//          (`message_sender` = '{$user['id']}' AND `message_owner` = '{$recipient_id}')) ORDER BY `message_time` DESC LIMIT 20;");
    $message_query = db_message_list_get_last_20($user, $recipient_id);
    while($message_row = db_fetch($message_query)) {
      $template->assign_block_vars('messages', array(
        'ID'   => $message_row['message_id'],
        'DATE' => date(FMT_DATE_TIME, $message_row['message_time'] + SN_CLIENT_TIME_DIFF),
        'FROM' => htmlspecialchars($message_row['message_from']),
        'SUBJ' => htmlspecialchars($message_row['message_subject']),
        'TEXT' => in_array($message_row['message_type'], array(MSG_TYPE_PLAYER, MSG_TYPE_ALLIANCE)) && $message_row['message_sender'] ? nl2br(htmlspecialchars($message_row['message_text'])) : nl2br($message_row['message_text']),

        'FROM_ID' => $message_row['message_sender'],
      ));
    }

  break;

  case 'delete':
    $query_add = '';

    $message_range = sys_get_param_str('message_range');

    switch($message_range) {
      case 'unchecked':
      case 'checked':
        $marked_message_list = sys_get_param('mark', array());
        if($message_range == 'checked' && empty($marked_message_list)) {
          break;
        }

        $query_add = implode(',', $marked_message_list);
        if($query_add) {
          $query_add = "IN ({$query_add})";
          if($message_range == 'unchecked') {
            $query_add = "NOT {$query_add}";
          }
          $query_add = " AND `message_id` {$query_add}";
        }

      case 'class':
        if($current_class != MSG_TYPE_OUTBOX && $current_class != MSG_TYPE_NEW) {
          $query_add .= " AND `message_type` = {$current_class}";
        }
      case 'all':
        $query_add = $query_add ? $query_add : true;
      break;
    }

    if($query_add) {
      $query_add = $query_add === true ? '' : $query_add;
//      doquery("DELETE FROM `{{messages}}` WHERE `message_owner` = '{$user['id']}'{$query_add};");
      db_message_list_delete($user, $query_add);
    }

  case 'show':
    if($current_class == MSG_TYPE_OUTBOX) {
      $message_query = db_message_list_outbox_by_user_id($user['id']);
    } else {
      if($current_class == MSG_TYPE_NEW) {
        $SubUpdateQry = array();
        foreach($sn_message_class_list as $message_class_id => $message_class) {
          if($message_class_id != MSG_TYPE_OUTBOX) {
            $SubUpdateQry[] = "`{$message_class['name']}` = '0'";
            $user[$message_class['name']] = 0;
          }
        }
        $SubUpdateQry = implode(',', $SubUpdateQry);
      } else {
        $SubUpdateQry = "`{$sn_message_class_list[$current_class]['name']}` = '0', `{$sn_message_class_list[MSG_TYPE_NEW]['name']}` = `{$sn_message_class_list[MSG_TYPE_NEW]['name']}` - '{$user[$sn_message_class_list[$current_class]['name']]}'";
        $SubSelectQry = "AND `message_type` = '{$current_class}'";

        $user[$sn_message_class_list[MSG_TYPE_NEW]['name']] -= $user[$sn_message_class_list[$current_class]['name']];
        $user[$sn_message_class_list[$current_class]['name']] = 0;
      }

      db_user_set_by_id($user['id'], $SubUpdateQry);
//      $message_query = "SELECT * FROM {{messages}} WHERE `message_owner` = '{$user['id']}' {$SubSelectQry} ORDER BY `message_time` DESC;";
//      $message_query = doquery($message_query);
      $message_query = db_message_list_by_owner_and_string($user, $SubSelectQry);
    }

    if(sys_get_param_int('return')) {
      header('Location: messages.php');
      die();
    }

    $template = gettemplate('msg_message_list', true);
    while($message_row = db_fetch($message_query)) {
      $template->assign_block_vars('messages', array(
        'ID'   => $message_row['message_id'],
        'DATE' => date(FMT_DATE_TIME, $message_row['message_time'] + SN_CLIENT_TIME_DIFF),
        'FROM' => htmlspecialchars($message_row['message_from']),
        'SUBJ' => htmlspecialchars($message_row['message_subject']),
        'TEXT' => in_array($message_row['message_type'], array(MSG_TYPE_PLAYER, MSG_TYPE_ALLIANCE)) && $message_row['message_sender'] ? nl2br(htmlspecialchars($message_row['message_text'])) : nl2br($message_row['message_text']),

        'FROM_ID'        => $message_row['message_sender'],
        'SUBJ_SANITIZED' => htmlspecialchars($message_row['message_subject']),
        'STYLE'          => $current_class == MSG_TYPE_OUTBOX ? $sn_message_class_list[MSG_TYPE_OUTBOX]['name'] : $sn_message_class_list[$message_row['message_type']]['name'],
      ));
    }

    $current_class_text = $lang['msg_class'][$current_class];

    $template->assign_vars(array(
      "MESSAGE_CLASS"      => $current_class,
      "MESSAGE_CLASS_TEXT" => $current_class_text,
    ));
  break;
}

if(!$template) {
  $template = gettemplate('msg_message_class', true);

//  $query = doquery("SELECT message_owner, message_type, COUNT(message_owner) AS message_count FROM {{messages}} WHERE `message_owner` = {$user['id']} GROUP BY message_owner, message_type ORDER BY message_owner ASC, message_type;");
  $query = db_message_count_by_owner_and_type($user);
  while($message_row = db_fetch($query)) {
    $messages_total[$message_row['message_type']] = $message_row['message_count'];
    $messages_total[MSG_TYPE_NEW] += $message_row['message_count'];
  }

//  $query = doquery("SELECT COUNT(message_sender) AS message_count FROM {{messages}} WHERE `message_sender` = '{$user['id']}' AND message_type = 1 GROUP BY message_sender;", '', true);
//  $messages_total[MSG_TYPE_OUTBOX] = intval($query['message_count']);
  $messages_total[MSG_TYPE_OUTBOX] = db_message_count_outbox($user);

  foreach($sn_message_class_list as $message_class_id => $message_class) {
    $template->assign_block_vars('message_class', array(
      'ID'     => $message_class_id,
      'STYLE'  => $message_class['name'],
      'TEXT'   => $lang['msg_class'][$message_class_id],
      'UNREAD' => $user[$message_class['name']],
      'TOTAL'  => intval($messages_total[$message_class_id]),
    ));
  }

  $template->assign_vars(array(
    'PAGE_HINT' => $lang['msg_page_hint_class'],
  ));
}

display($template, $lang['msg_page_header']);
