<?php

/**
 * erreurs.php
 *
 * @version 1.0
 * @copyright 2008 by e-Zobar for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = '../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('admin');
$parse = $lang;

	if ($user['authlevel'] >= 3) {

		// Supprimer les erreurs
		extract($_GET);
		if (isset($delete)) {
			doquery("DELETE FROM `{{table}}` WHERE `error_id`=$delete", 'errors');
		} elseif ($deleteall == 'yes') {
			doquery("TRUNCATE TABLE `{{table}}`", 'errors');
		}

		// Afficher les erreurs
		$query = doquery("SELECT * FROM `{{table}}`", 'errors');
		$i = 0;
		while ($u = mysql_fetch_array($query)) {
			$i++;
			$parse['errors_list'] .= "
			<tr><td width=\"25\" class=n>". $u['error_id'] ."</td>
			<td width=\"170\" class=n>". $u['error_type'] ."</td>
			<td width=\"230\" class=n>". date('d/m/Y h:i:s', $u['error_time']) ."</td>
			<td width=\"95\" class=n><a href=\"?delete=". $u['error_id'] ."\"><img src=\"../images/r1.png\"></a></td></tr>
			<tr><td colspan=\"4\" class=b>". $u['error_page'] ."</td></tr>
			<tr><td colspan=\"4\" class=b>".  nl2br($u['error_text'])."</td></tr>";
		}
		$parse['errors_list'] .= "<tr>
			<th class=b colspan=5>". $i ." ". $lang['adm_er_nbs'] ."</th>
		</tr>";

		display(parsetemplate(gettemplate('admin/errors_body'), $parse), "Bledy", false, '', true);
	} else {
		message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
	}

?>
