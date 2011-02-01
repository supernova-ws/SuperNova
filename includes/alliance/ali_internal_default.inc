<?php
// Imagen de la alianza
if ($ally['ally_image'])
  $ally['ally_image'] = "<tr><th colspan=2><img src=\"{$ally['ally_image']}\"></th></tr>";

if ($ally['ally_owner'] == $user['id']) {
  $range = ($ally['ally_owner_range'] != '')?$lang['Founder']:$ally['ally_owner_range'];
} elseif ($user['ally_rank_id'] != 0 && isset($ranks[$user['ally_rank_id']]['name'])) {
  $range = $ranks[$user['ally_rank_id']]['name'];
} else {
  $range = $lang['member'];
}

if ($user_admin) {
  $lang['alliance_admin'] = "<a href=\"?mode=admin&edit=ally\">{$lang['ally_admin']}</a>";
} elseif ($user_can_kick){
  $lang['alliance_admin'] = "<a href=\"?mode=admin&edit=members\">{$lang['members_admin']}</a>";
}

// El link de enviar correo circular
if (!$user_can_send_mails)
  $lang['isCanMassMail'] = 'class="hide"';

// El link para ver las solicitudes
$request = doquery("SELECT COUNT(*) AS request_count FROM {{alliance_requests}} WHERE `id_ally` ='{$ally['id']}'", '', true);
if($request['request_count']){
  $lang['request_count'] = $lang['ali_req_requestCount'] . ': ' . intval($request['request_count']);
}else{
  $lang['request_count'] = $lang['ali_req_emptyList'];
}
if (!$user_admin_applications)
    $lang['isShowRequests'] = 'class="hide"';

if ($ally['ally_owner'] != $user['id']) {
  $lang['ally_owner'] .= MessageForm($lang['Exit_of_this_alliance'], "", "?mode=exit", $lang['Continue']);
}

// La imagen de logotipo
$lang['ally_image'] = $ally['ally_image'] ? $ally['ally_image'] :'';
$lang['range'] = $range;
// codigo raro
$patterns[] = "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi";
$replacements[] = '<font color="\1">\2</font>';
$patterns[] = '#\[img\](.*?)\[/img\]#Smi';
$replacements[] = '<img src="\1" alt="\1" style="border:0px;" />';
$patterns[] = "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi";
$replacements[] = '<font color="\1">';
$patterns[] = "#\[/f\]#Ssi";
$replacements[] = '</font>';
$ally['ally_description'] = preg_replace($patterns, $replacements, $ally['ally_description']);
$lang['ally_description'] = nl2br($ally['ally_description']);

$ally['ally_text'] = preg_replace($patterns, $replacements, $ally['ally_text']);
$lang['ally_text'] = nl2br($ally['ally_text']);

$lang['ally_web'] = $ally['ally_web'];
$lang['ally_tag'] = $ally['ally_tag'];
$lang['ally_members'] = $ally['ally_members'];
$lang['ally_name'] = $ally['ally_name'];

display(parsetemplate(gettemplate('ali_internal'), $lang), $lang['your_alliance']);
?>