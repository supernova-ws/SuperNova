<?php

/**
 * logout.php
 *
 * @version 1.0
 * @copyright 2008 by ?????? for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

sn_sys_logout();
/*
lng_include('login');

if($_COOKIE[SN_COOKIE_I])
setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
unset($user);
message($lang['log_see_you'], $lang['log_session_closed'], "login." . PHP_EX, 5, false);
*/
// -----------------------------------------------------------------------------------------------------------
// History version
?>