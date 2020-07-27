<?php

/*
#############################################################################
#  Filename: messages.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 45d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = [
  'msg_page_header' => 'Personal messages',
  'msg_head_type' => 'Category',
  'msg_head_count' => 'Unread',
  'msg_head_total' => 'Total',
  'msg_mark_select' => '-- SELECT RANGE --',
  'msg_mark_checked' => 'Marked messages',
  'msg_mark_unchecked' => 'Unmarked messages',
  'msg_mark_class' => 'All messages in category',
  'msg_mark_all' => 'ALL PERSONAL MESSAGES',
  'msg_select_all' => 'Select All',
  'msg_delete_checked' => 'Delete marked messages',
  'msg_show_all' => 'Show all',
  'msg_date' => 'Date',
  'msg_from' => 'From',
  'msg_recipient' => 'To',
  'msg_subject' => 'Subject',
  'msg_answer' => 'Answer',
  'msg_answer_prefix' => 'RE:',
  'msg_compose' => 'Write message',
  'msg_text' => 'Message',
  'msg_subject_default' => 'New message',
  'msg_not_message_sent' => 'Message succesfully sent',
  'msg_warn_no_messages' => 'No messages in this category',
  'msg_err_player_not_found' => 'Player not found',
  'msg_err_no_text' => 'You can not send empty message',
  'msg_err_self_send' => 'You can not send message to yourself',
  'msg_del_class' => 'Delete all messages in this category',
  'msg_page_hint_class' =>
    '<ul>
      <li>Category "Sent messages" contains messages sent by you AND did not yet deleted by recipient. You can not delete messages from this category</li>
      <li>To delete all messages of one category press delete icon in according row</li>
      <li>Deleting messages from category "All messages" will lead to clear whole messagebox</li>
      <li>Slow connection and/or large ammount of messages on one category can lead to unability to browse through messages. In such case you should clear according message category and/or clear whole messagebox</li>
    </ul>',
  'msg_header_dialog' => 'Dialog with',

  'msg_ignore' => 'Игнорировать',
  'msg_ignore_title' => "Добавить игрока [PLAYER_NAME] в игнор-лист?",
  'msg_ignore_message' => "Вы больше не увидите личных сообщений от игрока в игнор-листе.<br><br>Вы можете управлять своим игнор-листом на странице 'Настройки'.<br><br>Добавить игрока [PLAYER_NAME] в игнор-лист?",
  'msg_message' => 'Сообщение',
  'msg_ignored_messages' => 'сообщений от пользователей в игнор-листе не показано',
  'msg_ignore_control' => 'Вы можете управлять игнор-листом на странице "Настройки"',
];
