<?php

/**
 * scheduler.php
 * Built-in autorun scheduler
 *
 * @package statistics
 * @version 2
 *
 * Revision History
 * ================
 *    2 - copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *      [+] Added locking mechanic made impossible to run several updates at once
 *      [~] Complies to PCG1
 *
 *    1 - copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *      [!] Initial revision wrote from scratch
 *
 */

require_once('includes/init.php');

define('IN_AJAX', true);

if(($result = StatUpdateLauncher::scheduler_process()) && !defined('IN_ADMIN')) {
  $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
  print(json_encode($result));
}

if(!defined('IN_ADMIN')) {
  die();
}
