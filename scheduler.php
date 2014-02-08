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

lng_include('admin');

//if($_SERVER['HTTP_REFERER'] == SN_ROOT_VIRTUAL . 'admin/statbuilder.php')
if(sys_get_param_int('admin_update'))
{
  $user = sn_autologin(!$allow_anonymous);
  $sys_user_logged_in = is_array($user) && isset($user['id']) && $user['id'];
  define('USER_LEVEL', isset($user['authlevel']) ? $user['authlevel'] : -1);
  if(USER_LEVEL > 0)
  {
    $is_admin_request = true;
    $next_stat_update = time();
  }
}
else
{
  $next_stat_update = sys_schedule_get_next_run($config->stats_schedule, $config->var_stat_update, $time_now);
}

if($next_stat_update > $config->var_stat_update)
{
  if($time_now >= $config->var_stat_update_end)
  {
    $config->db_saveItem('var_stat_update_end', $time_now + 60);
    $config->db_saveItem('var_stat_update_msg', 'Update started');

    if($is_admin_request)
    {
      $msg = 'admin request';
    }
    else
    {
      $msg = 'scheduler';
    };
    $msg = "Running stat updates: {$msg}. Config->var_stat_update = " . date(FMT_DATE_TIME, $config->var_stat_update) . ', nextStatUpdate = ' . date(FMT_DATE_TIME, $next_stat_update);
    $debug->warning($msg, 'Stat update', 190);
    $total_time = microtime(true);

    require_once('includes/includes/sys_stat.php');
    sys_stat_calculate();

    $total_time = microtime(true) - $total_time;
    $msg = "Stat update complete in {$total_time} seconds.";
    $debug->warning($msg, 'Stat update', 192);

    $time_now = time();

    $msg = "{$lang['adm_done']}: {$total_time} {$lang['sys_sec']}.";

    // TODO: Analyze maintenance result. Add record to log if error. Add record to log if OK
    $maintenance_result = sys_maintenance();

    $time_now = time();

    $config->db_saveItem('var_stat_update', $time_now);
    $config->db_saveItem('var_stat_update_end', $time_now);
    $config->db_saveItem('var_stat_update_msg', $msg);
    $config->db_saveItem('var_stat_update_next', sys_schedule_get_next_run($config->stats_schedule, $time_now, $time_now, false));
  }
  elseif($next_stat_update > $config->var_stat_update)
  {
    $timeout = $config->var_stat_update_end - $time_now;
    $msg = $config->db_loadItem('var_stat_update_msg');
    $msg = "{$msg} ETA {$timeout} seconds. Please wait...";
  }
}
elseif($is_admin_request)
{
  $msg = 'Stat is up to date';
}

if($msg)
{
  $msg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
  $msg = "<message>{$msg}</message>";

  header('Content-type: text/xml');
  echo $msg;
}

?>
