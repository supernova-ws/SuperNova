<?php

// comprobamos el permiso
if ($ally['ally_owner'] != $user['id'] && !$user_admin) {
  message($lang['Denied_access'], $lang['Members_list']);
}

if ($POST_newname) {
  // Y a le nouveau Nom
  $ally['ally_name'] = mysql_escape_string(strip_tags($POST_newname));
  doquery("UPDATE {{table}} SET `ally_name` = '". $ally['ally_name'] ."' WHERE `id` = '". $user['ally_id'] ."';", 'alliance');
  doquery("UPDATE {{table}} SET `ally_name` = '". $ally['ally_name'] ."' WHERE `ally_id` = '". $ally['id'] ."';", 'users');
}

$parse['question']           = str_replace('%s', $ally['ally_name'], $lang['Future_allyname']);
$parse['New_name']           = $lang['New_name'];
$parse['Change']             = $lang['Change'];
$parse['name']               = 'newname';
$parse['Return_to_overview'] = $lang['Return_to_overview'];
$page .= parsetemplate(gettemplate('alliance_admin_rename'), $parse);
display($page, $lang['ally_admin']);

?>