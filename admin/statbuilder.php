<?php

/**
 * StatBuilder.php
 *
 * @version 1.1 (c) copyright 2010 by Gorlum for http://supernova.ws
 *   [*] All calculations moved to StatFunctions.php - thus we can utilize them in automatized stats calculations
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

include($ugamela_root_path . 'admin/statfunctions.' . $phpEx);

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);

if ($user['authlevel'] < 1) {
  AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
};

includeLang('admin');

$start = microtime(true);
SYS_statCalculate();
$totaltime = microtime(true) - $start;

AdminMessage ( $lang['adm_done'] . ' - ' . $totaltime . ' ' . $lang['sys_sec'], $lang['adm_stat_title'] );
?>