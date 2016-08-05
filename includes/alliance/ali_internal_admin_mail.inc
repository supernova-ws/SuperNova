<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
  classSupernova::$debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if(!$user_can_send_mails) {
  message(classLocale::$lang['Denied_access'], classLocale::$lang['Send_circular_mail']);
}

$POST_text = sys_get_param_str('text');
$internalAliSendTextUnsafe = sys_get_param_str_unsafe('text');
if($internalAliSendTextUnsafe) {
  message(classLocale::$lang['members_who_recived_message'] . DBStaticMessages::msg_ali_send($internalAliSendTextUnsafe, classLocale::$lang['ali_adm_msg_subject'], sys_get_param_int('r')), classLocale::$lang['Circular_sended'], "alliance.php", '');
}

$r_list = "<option value=\"-1\">" . classLocale::$lang['All_players'] . "</option>";
if($ranks) {
  foreach($ranks as $id => $array) {
    $r_list .= "<option value=\"" . $id . "\">" . $array['name'] . "</option>";
  }
}

$page_title = classLocale::$lang['Send_circular_mail'];

$template = gettemplate('ali_admin_mail');
$template->assign_vars(array(
  'R_LIST' => $r_list,
));

$page .= parsetemplate($template);
display($page, $page_title);
