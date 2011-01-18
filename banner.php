<?php
/**
* banner.php
* @version 1.0s - Security checks by Gorlum for http://supernova.ws
* @version 1.0
*
* Simple wrapper for INT_createBanner.php
* Create banner or userbar
* banner.php?id=<userid>&type=<banner|userbar>&format=<png>
*
* @copyright 2010 by Gorlum for http://supernova.ws
*
*/

define('INSIDE' , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once("{$ugamela_root_path}includes/init.{$phpEx}");

includeLang('overview');

$id = intval($_GET['id']);
$type = SYS_mysqlSmartEscape($_GET['type']);
$format = SYS_mysqlSmartEscape($_GET['format']);

INT_createBanner($id, $type, $format);
?>
