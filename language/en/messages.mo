<?php

if(!defined('INSIDE'))
{
  die('Hack attempt!');
}

$lang = array_merge($lang, array(
  'msg_page_header' => 'Personal messges',

  'msg_mark_select'      => '-- SELECT RANGE --',
  'msg_mark_checked'     => 'Marked messages',
  'msg_mark_unchecked'   => 'Unmarked messages',
  'msg_mark_class'       => 'All messages in current category',
  'msg_mark_all'         => 'ALL PERSONAL MESSAGES',
  
  'msg_compose'     => 'Write message',
  'msg_recipient'   => 'To',
  'msg_subject'     => 'Subject',
  'msg_text'        => 'Message',

  'msg_subject_default'  => 'New message',

  'msg_not_message_sent' => 'Message succesfully sent',

  'msg_warn_no_messages' => 'No messages in this category',

  'msg_err_player_not_found' => 'Player not found',
  'msg_err_no_text'          => 'You can not sent empty message',
  'msg_err_self_send'        => 'You can not sent message to yourself',
));

$lang['alliance']		= 'Alliance';
$lang['title']			= "Mailbox";
$lang['head_type']		= "Category";
$lang['head_count']		= "Unread";
$lang['head_total']		= "Total";

$lang['mes_sent']		= "Message sent";
$lang['mess_pagetitle']		= "Send a message";
$lang['mess_error']		= "Error";
$lang['mess_no_ownerid']	= "Error! Try again, if you fail to contact the Administration";
$lang['mess_no_ownerpl']	= "Error! The player who you want to send a letter of no more planets!";
$lang['mess_no_owner']		= "Player does not exist.";
$lang['mess_recipient']		= "Recipient";
$lang['mess_message']		= "Message";
$lang['mess_characters']	= "Characters";
$lang['mess_subject']		= "Subject";
$lang['mess_envoyer']		= " Send ";
$lang['mess_no_subject']	= "Message subject";
$lang['mess_no_text']		= "We will send an empty message?";
$lang['mess_sended']		= "Message sent!";
$lang['mess_partialreport'] 	= "Intelligence show partly";
$lang['mess_deleteunmarked']	= "Delete unchecked messages";
$lang['mess_deletemarked']	= "Delete checked messages";
$lang['mess_deleteall']		= "Delete All";
$lang['mess_its_ok']		= " ok ";
$lang['mess_from']		= "From";
$lang['mess_action']		= "Action";
$lang['mess_date']		= "Date";
$lang['mess_answer']		= "Answer";
$lang['mess_answer_prefix']	= "Reply:";

$lang['Player_say']		= '<font color="#7f7f7f">Player</font> %s <font color="#7f7f7f">wrote:</font><br>';

$lang['mess_comp']		= "To complain of flagged messages";

?>
