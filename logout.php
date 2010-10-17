<?php

/**
 * logout.php
 *
 * @version 1.0
 * @copyright 2008 by ?????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

includeLang('login');

setcookie($config->COOKIE_NAME, "", time()-100000, "/", "", 0);

unset($user);

message ( $lang['log_see_you'], $lang['log_session_closed'], "login.{$phpEx}", 5, false );

// -----------------------------------------------------------------------------------------------------------
// History version
?>