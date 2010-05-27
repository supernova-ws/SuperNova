<?php

if ($ally['ally_owner'] != $user['id'] && !$user_admin_applications) {
  message($lang['Denied_access'], $lang['requests_admin']);
}

if ($POST_action == $lang['Accept_cand']) {
  $u = doquery("SELECT * FROM {{table}} WHERE `id` =$show", 'users', true);
  // agrega los puntos al unirse el user a la alianza
  doquery("UPDATE {{table}} SET
  `ally_members` = ally_members+1
  WHERE `id` ='{$ally['id']}'", 'alliance');

  doquery("UPDATE {{table}} SET
  `ally_name` ='{$ally['ally_name']}',
  `ally_request_text` ='',
  `ally_request` ='0',
  `ally_id` ='{$ally['id']}',
  `new_message` =new_message+1,
  `mnl_alliance` =mnl_alliance+1
  WHERE `id` ='{$show}'", 'users');
  // Se envia un mensaje avizando...

  doquery("INSERT INTO {{table}} SET
  `message_owner`='{$show}',
  `message_sender`='{$user['id']}' ,
  `message_time`='" . time() . "',
  `message_type`='2',
  `message_from`='{$ally['ally_tag']}',
  `message_subject`='[" . $ally['ally_name'] . "] {$lang['Aplication_acepted_subjet']}',
  `message_text`='{$lang['Aplication_hello']}<b>" . $ally['ally_name'] . "</b> {$lang['Aplication_acepted']}" . $POST_text . "'", "messages");

  header('Location:alliance.php?mode=admin&edit=requests');
  die();

} elseif ($POST_action == $lang['Reject_cand'] && $POST_action != '') {
  $POST_text = mysql_escape_string(strip_tags($POST_text));

  doquery("UPDATE {{table}} SET ally_request_text='',ally_request='0',ally_id='0',new_message=new_message+1, mnl_alliance=mnl_alliance+1 WHERE id='{$show}'", 'users');
  // Se envia un mensaje avizando...
  doquery("INSERT INTO {{table}} SET
  `message_owner`='{$show}',
  `message_sender`='{$user['id']}' ,
  `message_time`='" . time() . "',
  `message_type`='2',
  `message_from`='{$ally['ally_tag']}',
  `message_subject`='[" . $ally['ally_name'] . "] {$lang['Aplication_rejected_subjet']}',
  `message_text`='{$lang['Aplication_hello']}<b>" . $ally['ally_name'] . "</b> {$lang['Aplication_rejected']}" . $POST_text . "'", "messages");

  header('Location:alliance.php?mode=admin&edit=requests');
  die();
}

$row = gettemplate('alliance_admin_request_row');
$i = 0;
$parse = $lang;
$query = doquery("SELECT id,username,ally_request_text,ally_register_time FROM {{table}} WHERE ally_request='{$ally['id']}'", 'users');
while ($r = mysql_fetch_array($query)) {
  // recolectamos los datos del que se eligio.
  if (isset($show) && $r['id'] == $show) {
    $s['username'] = $r['username'];
    $s['ally_request_text'] = nl2br($r['ally_request_text']);
    $s['id'] = $r['id'];
  }
  // la fecha de cuando se envio la solicitud
  $r['time'] = date("Y-m-d h:i:s", $r['ally_register_time']);
  $parse['list'] .= parsetemplate($row, $r);
  $i++;
}
if ($parse['list'] == '') {
  $parse['list'] = '<tr><th colspan=2> Больше нет заявок </th></tr>';
}
// Con $show
if (isset($show) && $show != 0 && $parse['list'] != '') {
  // Los datos de la solicitud
  $s['Request_from'] = str_replace('%s', $s['username'], $lang['Request_from']);
  $s['Motive_optional'] = $lang['Motive_optional'];
  $s['Request_answer'] = $lang['Request_answer'];
  $s['Accept_cand'] = $lang['Accept_cand'];
  $s['Reject_cand'] = $lang['Reject_cand'];
  // el formulario
  $parse['request'] = parsetemplate(gettemplate('alliance_admin_request_form'), $s);
  $parse['request'] = parsetemplate($parse['request'], $lang);
} else {
  $parse['request'] = '';
}

$parse['ally_tag'] = $ally['ally_tag'];
$parse['Back'] = $lang['Back'];

$parse['There_is_hanging_request'] = str_replace('%n', $i, $lang['There_is_hanging_request']);
// $parse['list'] = $lang['Return_to_overview'];
$page = parsetemplate(gettemplate('alliance_admin_request_table'), $parse);
display($page, $lang['requests_admin']);

?>