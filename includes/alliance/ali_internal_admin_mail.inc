<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_can_send_mails) {
  message($lang['Denied_access'], $lang['Send_circular_mail']);
}

if ($POST_text) {
  $sq = "SELECT id,username FROM {{users}} WHERE ally_id='{$user['ally_id']}'";
  if ($POST_r >= 0) {
    $sq .= " AND ally_rank_id='{$POST_r}'";
  }
  $sq = doquery($sq);

  $list = '';
  while ($u = mysql_fetch_assoc($sq)) {
    $sendList[] = $u['id'];
    $list .= "<br>{$u['username']} ";
  }
  SendSimpleMessage($sendList, $user['id'], time(), 2, $ally['ally_tag'], $user['username'], sys_bbcodeParse($POST_text, true));

  $page = MessageForm($lang['Circular_sended'], $lang['members_who_recived_message'] . $list, "alliance.php", "", true);
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
