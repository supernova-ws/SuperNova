<?php

/**
 * md5enc.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

	if ($user['authlevel'] >= "1") {
		includeLang('admin/md5enc');

		$parse   = $lang;

		if ($_POST['md5q'] != "") {
			$parse['md5_md5'] = $_POST['md5q'];
			$parse['md5_enc'] = md5 ($_POST['md5q']);
		} else {
			$parse['md5_md5'] = "";
			$parse['md5_enc'] = md5 ("");
		}

		$PageTpl = gettemplate("admin/md5enc");
		$Page    = parsetemplate( $PageTpl, $parse);

		display( $Page, $lang['md5_title'], false, '', true );
	} else {
		message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
	}
?>