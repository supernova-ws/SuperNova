<?php

/**
 * logout.php
 *
 * @version 1.0
 * @copyright 2008 by ?????? for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('login');

setcookie($config->COOKIE_NAME, '', time() - PERIOD_WEEK);
//setcookie($config->COOKIE_NAME, '', time() - 2 * 24 * 60 * 60);
//setcookie($config->COOKIE_NAME); // , "", time()-100000, "/", "", 0

unset($user);

message ( $lang['log_see_you'], $lang['log_session_closed'], "login." . PHP_EX, 5, false );

// -----------------------------------------------------------------------------------------------------------
// History version
?>