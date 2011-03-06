<?php

/**
 * contact.php
 *
 * List of authorities: admin, ops, moders
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

$allow_anonymous = true;
$skip_ban_check = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

includeLang('contact');

$RowsTPL = gettemplate('contact_body_rows');
$parse   = $lang;

$QrySelectUser  = "SELECT `username`, `email`, `authlevel` ";
$QrySelectUser .= "FROM {{users}} ";
$QrySelectUser .= "WHERE `authlevel` != '0' ORDER BY `authlevel` DESC;";
$GameOps = doquery ( $QrySelectUser);

while( $Ops = mysql_fetch_assoc($GameOps) ) {
  $bloc['ctc_data_name']    = $Ops['username'];
  $bloc['ctc_data_auth']    = $lang['user_level'][$Ops['authlevel']];
  $bloc['ctc_data_mail']    = "<a href=mailto:".$Ops['email'].">".$Ops['email']."</a>";
  $parse['ctc_admin_list'] .= parsetemplate($RowsTPL, $bloc);
}

display(parsetemplate(gettemplate('contact_body'), $parse), $lang['ctc_title']);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Mise au propre (Virer tout ce qui ne sert pas a une prise de contact en fait)

?>
