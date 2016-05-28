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

global $user;

$mode = sys_get_param_str('msg_delete') ? 'delete' : sys_get_param_str('mode');
$current_class = sys_get_param_int('message_class');
if (!isset(DBStaticMessages::$snMessageClassList[$current_class])) {
  $current_class = 0;
  $mode = '';
}

$template = null;

switch ($mode) {
  case 'write':
    $template = DBStaticMessages::messageWrite($user);
  break;

  case 'delete':
    DBStaticMessages::messageDelete($user, $current_class);

  case 'show':
    $template = DBStaticMessages::messageShow($user, $current_class);
  break;
}

if (!$template) {
  $template = gettemplate('msg_message_class', true);

  $query = DBStaticMessages::db_message_count_by_owner_and_type($user);
  while ($row = db_fetch($query)) {
    $messages_total[$row['message_type']] = $row['message_count'];
    $messages_total[MSG_TYPE_NEW] += $row['message_count'];
  }

  $messages_total[MSG_TYPE_OUTBOX] = DBStaticMessages::db_message_count_outbox($user);

  foreach (DBStaticMessages::$snMessageClassList as $message_class_id => $message_class) {
    $template->assign_block_vars('message_class', array(
      'ID'     => $message_class_id,
      'STYLE'  => $message_class['name'],
      'TEXT'   => classLocale::$lang['msg_class'][$message_class_id],
      'UNREAD' => $user[$message_class['name']],
      'TOTAL'  => intval($messages_total[$message_class_id]),
    ));
  }

  $template->assign_vars(array(
    'PAGE_HINT' => classLocale::$lang['msg_page_hint_class'],
  ));
}

display($template, classLocale::$lang['msg_page_header']);
