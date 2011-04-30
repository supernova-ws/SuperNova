<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_can_send_mails) {
  message($lang['Denied_access'], $lang['Send_circular_mail']);
}

if ($POST_text)
{
/*
  $ally_rank_id = sys_get_param_int('r');
  $query = "SELECT id, username FROM {{users}} WHERE ally_id = '{$user['ally_id']}'";
  if ($ally_rank_id >= 0) {
    $query .= " AND ally_rank_id = {$ally_rank_id}";
  }
  $query = doquery($query);

  $list = '';
  while ($u = mysql_fetch_assoc($query)) {
    $sendList[] = $u['id'];
    $list .= "<br>{$u['username']} ";
  }
  msg_send_simple_message($sendList, $user['id'], time(), 2, $ally['ally_tag'], $user['username'], sys_bbcodeParse($POST_text, true));
*/
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
