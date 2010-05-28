<?php
// Imagen de la alianza
if ($ally['ally_image'])
  $ally['ally_image'] = "<tr><th colspan=2><img src=\"{$ally['ally_image']}\"></th></tr>";

// temporalmente...
if ($ally['ally_owner'] == $user['id']) {
  $range = ($ally['ally_owner_range'] != '')?$lang['Founder']:$ally['ally_owner_range'];
} elseif ($user['ally_rank_id'] != 0 && isset($ranks[$user['ally_rank_id']]['name'])) {
  $range = $ranks[$user['ally_rank_id']]['name'];
} else {
  $range = $lang['member'];
}

// Link de la lista de miembros
if ($user_can_watch_memberlist)
  $lang['members_list'] = " (<a href=\"?mode=memberslist\">{$lang['Members_list']}</a>)";

// El link de adminstrar la allianza
if ($user_admin) {
  $lang['alliance_admin'] = "<a href=\"?mode=admin&edit=ally\">{$lang['ally_admin']}</a>";
} elseif ($user_can_kick){
  $lang['alliance_admin'] = "<a href=\"?mode=admin&edit=members\">{$lang['members_admin']}</a>";
}

// El link de enviar correo circular
if ($user_can_send_mails)
  $lang['send_circular_mail'] = "<tr><th>{$lang['Circular_message']}</th><th><a href=\"?mode=circular\">{$lang['Send_circular_mail']}</a></th></tr>";

// El link para ver las solicitudes
$lang['requests'] = '';
$request = doquery("SELECT id FROM {{table}} WHERE `ally_request` ='{$ally['id']}'", 'users');
$request_count = mysql_num_rows($request);
if ($request_count && $user_admin_applications)
    $lang['requests'] = "<tr><th>{$lang['Requests']}</th><th><a href=\"alliance.php?mode=admin&edit=requests\">{$request_count} {$lang['XRequests']}</a></th></tr>";

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

display(parsetemplate(gettemplate('alliance_frontpage'), $lang), $lang['your_alliance']);
?>