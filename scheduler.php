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

function scheduler_process()
{
  global $allow_anonymous, $config, $user, $sys_user_logged_in, $debug, $lang;

  lng_include('admin');
  //if($_SERVER['HTTP_REFERER'] == SN_ROOT_VIRTUAL . 'admin/statbuilder.php')
  $next_stat_update = sys_schedule_get_prev_run($config->stats_schedule, $config->var_stat_update, SN_TIME_NOW);
  if(sys_get_param_int('admin_update') || IN_ADMIN)
  {
    $user = sn_autologin(!$allow_anonymous);
    $sys_user_logged_in = is_array($user) && isset($user['id']) && $user['id'];
    define('USER_LEVEL', isset($user['authlevel']) ? $user['authlevel'] : -1);
    if(USER_LEVEL > 0)
    {
      $is_admin_request = true;
      $next_stat_update = SN_TIME_NOW;
    }
  }

  if($next_stat_update > $config->var_stat_update)
  {
    if(SN_TIME_NOW >= $config->var_stat_update_end)
    {
      $config->db_saveItem('var_stat_update_end', SN_TIME_NOW + 120);
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

      $msg = "{$lang['adm_done']}: {$total_time} {$lang['sys_sec']}."; // . date(FMT_DATE_TIME, $next_stat_update) . ' ' . date(FMT_DATE_TIME, $config->var_stat_update);

      // TODO: Analyze maintenance result. Add record to log if error. Add record to log if OK
      $maintenance_result = sys_maintenance();

      $config->db_saveItem('var_stat_update', $next_stat_update);
      $config->db_saveItem('var_stat_update_end', SN_TIME_NOW);
      $config->db_saveItem('var_stat_update_msg', $msg);
      $config->db_saveItem('var_stat_update_next', sys_schedule_get_prev_run($config->stats_schedule, $next_stat_update, SN_TIME_NOW, true));
    }
    elseif($next_stat_update > $config->var_stat_update)
    {
      $timeout = $config->var_stat_update_end - SN_TIME_NOW;
      $msg = $config->db_loadItem('var_stat_update_msg');
      $msg = "{$msg} ETA {$timeout} seconds. Please wait...";
    }
  }
  elseif($is_admin_request)
  {
    $msg = 'Stat is up to date';
  }

  return $msg;
}

if(($result = scheduler_process()) && !defined('IN_ADMIN'))
{
  $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
  print(json_encode($result));
}
