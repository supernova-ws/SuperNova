<?php

/**
* annonce.php
*
* Announces for trading between players
*
* @version 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
* @version 1.0
* @copyright 2008 by ??????? for XNova
*/

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$users = doquery("SELECT `username`,`galaxy`,`system` FROM {{table}} WHERE `id` ='".$user['id']."';", 'users',true);
$action = intval($_GET['action']);
$GET_id = intval($_GET['id']);

includeLang('announce');
switch($action){
case 1://on veut poster une annonce
$page .='<HTML>
<center>
<br>
<table width="600">
<td class="c" colspan="10" align="center"><b><font color="white">'.$lang['Classifieds'].'</font></b></td></tr>
<form action="annonce.php?action=2" method="post">
<td class="c" colspan="10" align="center"><b>'.$lang['Resources_to_be_sold'].'</font></b></td>
<tr><th colspan="5">'.$lang['metal'].'</th><th colspan="5"><input type="texte" value="0" name="metalvendre" /></th></tr>
<tr><th colspan="5">'.$lang['crystal'].'</th><th colspan="5"><input type="texte" value="0" name="cristalvendre" /></th></tr>
<tr><th colspan="5">'.$lang['deuterium'].'</th><th colspan="5"><input type="texte" value="0" name="deutvendre" /></th></tr>

<td class="c" colspan="10" align="center"><b>'.$lang['Desired_resources'].'</font></b></td></tr>
<tr><th colspan="5">'.$lang['metal'].'</th><th colspan="5"><input type="texte" value="0" name="metalsouhait" /></th></tr>
<tr><th colspan="5">'.$lang['crystal'].'</th><th colspan="5"><input type="texte" value="0" name="cristalsouhait" /></th></tr>
<tr><th colspan="5">'.$lang['deuterium'].'</th><th colspan="5"><input type="texte" value="0" name="deutsouhait" /></th></tr>
<tr><th colspan="10"><input type="submit" value="'.$lang['send'].'" /></th></tr>

<form>
</table>
</HTML>';

display($page);
break;

case 2:// On vient d'envoyer une annonce, on l'enregistre et on affiche un message comme quoi on l'a bien fait
foreach($_POST as $name => $value){
$$name=SYS_mysqlSmartEscape($value);
}
if(($metalvendre!=0 && $metalsouhait==0) ||($cristalvendre!=0 && $cristalsouhait==0) || ($deutvendre!=0 && $deutsouhait==0)){
doquery("INSERT INTO {{table}} SET `user` ='{$users['username']}', `galaxie` ='{$users['galaxy']}', `systeme` ='{$users['system']}', `metala` ='{$metalvendre}', `cristala` ='{$cristalvendre}', `deuta` ='{$deutvendre}', `metals` ='{$metalsouhait}', `cristals` ='{$cristalsouhait}', `deuts` ='{$deutsouhait}'" , "annonce");

message ($lang['Your_announce_was_recorded'], $lang['announce_status'],"annonce.php");
}

else{
message ($lang['Your_announce_not_recorded'], $lang['announce_status'],"annonce.php?action=1");
}

break;

case 3://Suppression d'annonce

doquery("DELETE FROM {{table}} WHERE `id` = {$GET_id}" , "annonce");
message ($lang['Your_announce_was_deleted'], $lang['announce_status'],"annonce.php");
break;

default://Sinon on affiche la liste des annonces
$annonce = doquery("SELECT * FROM {{table}} ORDER BY `id` DESC ", "annonce");

$page2 = "<HTML>
<center>
<br>
<table width=\"600\">
<td class=\"c\" colspan=\"10\"><font color=\"#FFFFFF\">{$lang['Classifieds']}</font></td></tr>
<tr><th colspan=\"3\">{$lang['Infos_of_delivery']}</th><th colspan=\"3\">{$lang['Resources_to_be_sold']}</th><th colspan=\"3\">{$lang['Desired_resources']}</th><th>{$lang['Action']}</th></tr>
<tr><th>{$lang['Salesman']}</th><th>{$lang['Galaxy']}</th><th>{$lang['Solar_system']}</th><th>{$lang['metal']}</th><th>{$lang['crystal']}</th><th>{$lang['deuterium']}</th><th>{$lang['metal']}</th><th>{$lang['crystal']}</th><th>{$lang['deuterium']}</th><th>{$lang['Delete']}</th></tr>";

while ($b = mysql_fetch_assoc($annonce)) {
$page2 .= '<tr><th> ';
foreach($b as $name => $value){
if($name!='id')
{$page2 .= $value ;
$page2 .= '</th><th>';}}
$page2 .= ($b['user']==$users['username'])?"<a href=\"annonce.php?action=3&id={$b[id]}\">X</a></th></tr>":"</th></tr>";
}

$page2 .= "<tr><th colspan=\"10\" align=\"center\"><a href=\"annonce.php?action=1\">{$lang['add_announce']}</a></th></tr>
</td>
</table>
</HTML>";

display($page2);
break;
}

// Créé par Tom1991 Copyright 2008
// Modifié par BenjaminV
?>
