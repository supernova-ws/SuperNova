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

$ugamela_root_path = './';

include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('overview');

$id = intval($_GET['id']);
$type = mysqlSmartEscape($_GET['type']);
$format = mysqlSmartEscape($_GET['format']);
if (!empty($id)) {
  INT_createBanner($id, $type, $format);
}
?>
