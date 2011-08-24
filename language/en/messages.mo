<?php

if (!defined('INSIDE')) die();

$lang = array_merge($lang, array(
  'msg_page_header' => 'Personal messges',
  'msg_head_type' => 'Category',
  'msg_head_count' => 'Unread',
  'msg_head_total' => 'Total',
  'msg_mark_select' => '-- SELECT RANGE --',
  'msg_mark_checked' => 'Marked messages',
  'msg_mark_unchecked' => 'Unmarked messages',
  'msg_mark_class' => 'All messages in current category',
  'msg_mark_all' => 'ALL PERSONAL MESSAGES',
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
  'msg_err_no_text' => 'You can not sent empty message',
  'msg_err_self_send' => 'You can not sent message to yourself',
  'msg_del_class' => 'Delete all messages in this category',
  'msg_page_hint_class' => '<ul>    <li>Category "" contains messages sent by you AND did not yet deleted by recipient.    You can not delete messages from this category</li>    <li>To delete all messages of one category press delete icon in according row</li>    <li>Deleting messages from category "" will lead to clear whole messagebox</li>    <li>Slow connection and/or large ammount of messages on one category can lead to unability to browse through messages.        In such case you should clear according message category and/or clear whole messagebox</li>  </ul>',
));

?>
