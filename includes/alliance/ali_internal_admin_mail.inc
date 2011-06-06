<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_can_send_mails) {
  message($lang['Denied_access'], $lang['Send_circular_mail']);
}

$POST_text = sys_get_param_str('text');
if ($POST_text)
{
  $page = MessageForm($lang['Circular_sended'], $lang['members_who_recived_message'] . msg_ali_send($POST_text, $lang['ali_adm_msg_subject'], sys_get_param_int('r')), "alliance.php", "", true);
}

$lang['r_list'] = "<option value=\"-1\">{$lang['All_players']}</option>";
if ($ranks) {
  foreach($ranks as $id => $array) {
    $lang['r_list'] .= "<option value=\"" . $id  . "\">" . $array['name'] . "</option>";
  }
}

$page_title = $lang['Send_circular_mail'];

$page .= parsetemplate(gettemplate('ali_admin_mail'), $lang);
display($page, $page_title);

?>
