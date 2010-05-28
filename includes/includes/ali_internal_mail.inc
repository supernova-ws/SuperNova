<?php
/*
Mandar un correo circular.
creo que aqui tendria que ver yo como crear el sistema de mensajes...
*/
// un loop para mostrar losrangos
// comprobamos el permiso
if ($ally['ally_owner'] != $user['id'] && !$user_can_send_mails) {
  message($lang['Denied_access'], $lang['Send_circular_mail']);
}

if ($sendmail == 1) {
  $POST_text = mysql_escape_string(strip_tags($POST_text));

  if ($POST_r == 0) {
    $sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='{$user['ally_id']}'", "users");
  } else {
    $sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='{$user['ally_id']}' AND ally_rank_id='{$POST_r}'", "users");
  }
  // looooooop
  $list = '';
  while ($u = mysql_fetch_array($sq)) {
    doquery("INSERT INTO {{table}} SET
    `message_owner`='{$u['id']}',
    `message_sender`='{$user['id']}' ,
    `message_time`='" . time() . "',
    `message_type`='2',
    `message_from`='{$ally['ally_tag']}',
    `message_subject`='{$user['username']}',
    `message_text`='{$POST_text}'
    ", "messages");
    $list .= "<br>{$u['username']} ";
  }
  // doquery("SELECT id,username FROM {{table}} WHERE ally_id='{$user['ally_id']}' ORDER BY `id`","users");
  doquery("UPDATE {{table}} SET `new_message`=new_message+1 WHERE ally_id='{$user['ally_id']}' AND ally_rank_id='{$POST_r}'", "users");
  doquery("UPDATE {{table}} SET `mnl_alliance`=mnl_alliance+1 WHERE ally_id='{$user['ally_id']}' AND ally_rank_id='{$POST_r}'", "users");
  /*
  Aca un mensajito diciendo que a quien se mando.
*/
  $page = MessageForm($lang['Circular_sended'], $lang['members_who_recived_message'] . $list, "alliance.php", $lang['Ok'], true);
  display($page, $lang['Send_circular_mail']);
}

$lang['r_list'] = "<option value=\"0\">{$lang['All_players']}</option>";
if ($ranks) {
  foreach($ranks as $id => $array) {
    $lang['r_list'] .= "<option value=\"" . ($id + 1) . "\">" . $array['name'] . "</option>";
  }
}

$page_title = $lang['Send_circular_mail'];

$page .= parsetemplate(gettemplate('alliance_circular'), $lang);
display($page, $page_title);
?>