<?php

global $lang;

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_can_send_mails) {
  SnTemplate::messageBox($lang['Denied_access'], $lang['Send_circular_mail']);
}

$POST_text = sys_get_param_str('text');
if ($POST_text)
{
  SnTemplate::messageBox($lang['members_who_recived_message'] . msg_ali_send($POST_text, $lang['ali_adm_msg_subject'], sys_get_param_int('r')), $lang['Circular_sended'], "alliance.php", '');
}

$page = SnTemplate::gettemplate('ali_admin_mail', true);

if ($ranks) {
  foreach($ranks as $id => $array) {
    $page->assign_block_vars('ranks', array(
      'NAME' => $array['name'],
      'VALUE' => $id,
    ));
  }
}

$page->assign_vars(array(
  'PAGE_HEADER' => $lang['Send_circular_mail'],
));

SnTemplate::display($page);
