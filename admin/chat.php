<?php

/**
 * chat.php
 *
 * @version 1
 * @copyright 2008 By e-Zobar for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

includeLang('admin');
$parse = $lang;

	if ($user['authlevel'] >= 3) {

		// Système de suppression
		extract($_GET);
		if (isset($delete)) {
			doquery("DELETE FROM {{table}} WHERE `messageid`=$delete", 'chat');
		} elseif ($deleteall == 'yes') {
			doquery("DELETE FROM {{table}}", 'chat');
		}

		// Affichage des messages
		$query = doquery("SELECT * FROM {{table}} ORDER BY messageid DESC LIMIT 25", 'chat');
		$i = 0;
		while ($e = mysql_fetch_array($query)) {
			$i++;
			$parse['msg_list'] .= stripslashes("<tr><td class=n rowspan=2>{$e['messageid']}</td>" .
			"<td class=n><center>[<a href=?delete={$e['messageid']}>X</a>]</center></td>" .
			"<td class=n><center>{$e['user']}</center></td>" .
			"<td class=n><center>" . date('d/m/Y - h:i:s', $e['timestamp']) . "</center></td></tr><tr>" .
			"<td class=b colspan=4 width=500>" . nl2br($e['message']) . "</td></tr>");
		}
		$parse['msg_list'] .= "<tr><th class=b colspan=4>{$i} ".$lang['adm_ch_nbs']."</th></tr>";

		display(parsetemplate(gettemplate('admin/chat_body'), $parse), "Chat", false, '', true);
	} else {
		message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
	}
?>