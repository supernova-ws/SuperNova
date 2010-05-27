<?php

// Changer le TAG l'alliance
$ally_ranks = unserialize($ally['ally_ranks']);

// Bon si on verifiait les autorisation ?
if ($ally['ally_owner'] != $user['id'] && !$user_admin) {
  message($lang['Denied_access'], $lang['Members_list']);
}

if ($POST_newtag) {
  // Y a le nouveau TAG
  $ally['ally_tag'] = mysql_escape_string(strip_tags($POST_newtag));
  doquery("UPDATE {{table}} SET `ally_tag` = '". $ally['ally_tag'] ."' WHERE `id` = '". $user['ally_id'] ."';", 'alliance');
}

$parse['question']           = str_replace('%s', $ally['ally_tag'], $lang['Future_allytag']);
$parse['New_name']           = $lang['New_tag'];
$parse['Change']             = $lang['Change'];
$parse['name']               = 'newtag';
$parse['Return_to_overview'] = $lang['Return_to_overview'];
$page .= parsetemplate(gettemplate('alliance_admin_rename'), $parse);
display($page, $lang['ally_admin']);

?>