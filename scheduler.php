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

function scheduler_process() {
  global $config, $user, $debug, $lang;

  lng_include('admin');
  //if($_SERVER['HTTP_REFERER'] == SN_ROOT_VIRTUAL . 'admin/statbuilder.php')
  $is_admin_request = false;

/*
$config->db_saveItem('var_stat_update', '2014-11-23 11:09:55');
$config->db_saveItem('stats_schedule', '04:00:00, 06:00:00, 23:15:30:00, 24:02:00:00, 23:20:27:00');
*/

  $ts_var_stat_update = strtotime($config->db_loadItem('var_stat_update'));
  $ts_scheduled_update = sys_schedule_get_prev_run($config->db_loadItem('stats_schedule'), $config->var_stat_update);

/*
pdump($ts_scheduled_update);
print(      $msg = "Running stat updates: {$msg}. Config->var_stat_update = " . $config->var_stat_update .
  ', $ts_scheduled_update = ' . date(FMT_DATE_TIME_SQL, $ts_scheduled_update) .
  ', next_stat_update = ' . date(FMT_DATE_TIME_SQL, sys_schedule_get_prev_run($config->stats_schedule, $config->var_stat_update, true)));
// die();
*/

  if(sys_get_param_int('admin_update'))
  {
    define('USER_LEVEL', isset($user['authlevel']) ? $user['authlevel'] : -1);
    if(USER_LEVEL > 0)
    {
      $is_admin_request = true;
      $ts_scheduled_update = SN_TIME_NOW;
    }
  }

  if($ts_scheduled_update > $ts_var_stat_update) {
    sn_db_transaction_start();
    $ts_var_stat_update_end = strtotime($config->db_loadItem('var_stat_update_end'));
    if(SN_TIME_NOW > $ts_var_stat_update_end) {
      $old_server_status = $config->db_loadItem('game_disable');
      $config->db_saveItem('game_disable', GAME_DISABLE_STAT);

      $config->db_saveItem('var_stat_update_end', date(FMT_DATE_TIME_SQL, SN_TIME_NOW + ($config->db_loadItem('stats_minimal_interval') ? $config->stats_minimal_interval : 600)));
      $config->db_saveItem('var_stat_update_msg', 'Update started');
      sn_db_transaction_commit();

      $msg = $is_admin_request ? 'admin request' : 'scheduler';
      $next_run = date(FMT_DATE_TIME_SQL, sys_schedule_get_prev_run($config->stats_schedule, $config->var_stat_update, true));
      $msg = "Running stat updates: {$msg}. Config->var_stat_update = " . $config->var_stat_update .
        ', $ts_scheduled_update = ' . date(FMT_DATE_TIME_SQL, $ts_scheduled_update) .
        ', next_stat_update = ' . $next_run;
      $debug->warning($msg, 'Stat update', LOG_INFO_STAT_PROCESS);
      $total_time = microtime(true);

      require_once('includes/includes/sys_stat.php');
      sys_stat_calculate();

      $total_time = microtime(true) - $total_time;
      $msg = "Stat update complete in {$total_time} seconds.";
      $debug->warning($msg, 'Stat update', LOG_INFO_STAT_PROCESS);

      $msg = "{$lang['adm_done']}: {$total_time} {$lang['sys_sec']}."; // . date(FMT_DATE_TIME, $ts_scheduled_update) . ' ' . date(FMT_DATE_TIME, $config->var_stat_update);

      // TODO: Analyze maintenance result. Add record to log if error. Add record to log if OK
      $maintenance_result = sys_maintenance();

      $config->db_saveItem('var_stat_update', SN_TIME_SQL);
      $config->db_saveItem('var_stat_update_msg', $msg);
      $config->db_saveItem('var_stat_update_next', $next_run);
      $config->db_saveItem('var_stat_update_admin_forced', SN_TIME_SQL);
      $config->db_saveItem('var_stat_update_end', SN_TIME_SQL);

      $config->db_saveItem('game_disable', $old_server_status);
    } elseif($ts_scheduled_update > $ts_var_stat_update) {
      $timeout = strtotime($config->db_loadItem('var_stat_update_end')) - SN_TIME_NOW;
      $msg = $config->db_loadItem('var_stat_update_msg');
      $msg = "{$msg} ETA {$timeout} seconds. Please wait...";
    }
    sn_db_transaction_rollback();
  } elseif($is_admin_request) {
    $msg = 'Stat is up to date';
  }

  return $msg;
}

if(($result = scheduler_process()) && !defined('IN_ADMIN')) {
  $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
  print(json_encode($result));
}
