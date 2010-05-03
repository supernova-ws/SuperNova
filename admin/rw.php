<?php

/**
 * rw.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

	$raportrow = doquery("SELECT * FROM {{table}} WHERE `rid` = '". $_GET["raport"] ."';",'rw', true);
	$Page  = "<html>";
	$Page .= "<head>";
	$Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"". $dpath ."formate.css\">";
	$Page .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\" />";
	$Page .= "</head>";
	$Page .= "<body>";
	$Page .= "<center>";
	$Page .= "<table width=\"99%\">";
	$Page .= "<tr>";
	$Page .= "<td>". stripslashes( $raportrow["raport"] ) ."</td>";
	$Page .= "</tr>";
	$Page .= "</table>";
	$Page .= "</center>";
	$Page .= "</body>";
	$Page .= "</html>";
	echo $Page;

?>