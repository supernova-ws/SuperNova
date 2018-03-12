<?php

/**
 * logout.php
 *
 * @version 2.0
 */

define('LOGIN_LOGOUT', true);

include('common.' . substr(strrchr(__FILE__, '.'), 1));

// sn_sys_logout(true);
// core_auth::logout(true);
SN::$auth->logout(true);
