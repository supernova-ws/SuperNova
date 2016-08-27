<?php

function scheduler_process() {
  global $user;
  $classLocale = classLocale::$lang;

  $is_admin_request = false;

  $ts_var_stat_update = strtotime(classSupernova::$config->db_loadItem('var_stat_update'));
  $ts_scheduled_update = sys_schedule_get_prev_run(classSupernova::$config->db_loadItem('stats_schedule'), classSupernova::$config->var_stat_update);

  if(sys_get_param_int('admin_update')) {
    define('USER_LEVEL', isset($user['authlevel']) ? $user['authlevel'] : -1);
    if(USER_LEVEL > 0) {
      $is_admin_request = true;
      $ts_scheduled_update = SN_TIME_NOW;
    }
  }

  if($ts_scheduled_update > $ts_var_stat_update) {
    lng_include('admin');
    sn_db_transaction_start();
    $ts_var_stat_update_end = strtotime(classSupernova::$config->db_loadItem('var_stat_update_end'));
    if(SN_TIME_NOW > $ts_var_stat_update_end) {
      $old_server_status = classSupernova::$config->db_loadItem('game_disable');
      classSupernova::$config->db_saveItem('game_disable', GAME_DISABLE_STAT);

      classSupernova::$config->db_saveItem('var_stat_update_end', date(FMT_DATE_TIME_SQL, SN_TIME_NOW + (classSupernova::$config->db_loadItem('stats_minimal_interval') ? classSupernova::$config->stats_minimal_interval : 600)));
      classSupernova::$config->db_saveItem('var_stat_update_msg', 'Update started');
      sn_db_transaction_commit();

      $msg = $is_admin_request ? 'admin request' : 'scheduler';
      $next_run = date(FMT_DATE_TIME_SQL, sys_schedule_get_prev_run(classSupernova::$config->stats_schedule, classSupernova::$config->var_stat_update, true));
      $msg = "Running stat updates: {$msg}. Config->var_stat_update = " . classSupernova::$config->var_stat_update .
        ', $ts_scheduled_update = ' . date(FMT_DATE_TIME_SQL, $ts_scheduled_update) .
        ', next_stat_update = ' . $next_run;
      classSupernova::$debug->warning($msg, 'Stat update', LOG_INFO_STAT_PROCESS);
      $total_time = microtime(true);

      // require_once('../includes/sys_stat.php');
      require_once(SN_ROOT_PHYSICAL . 'includes/includes/sys_stat.php');
      sys_stat_calculate();

      $total_time = microtime(true) - $total_time;
      $msg = "Stat update complete in {$total_time} seconds.";
      classSupernova::$debug->warning($msg, 'Stat update', LOG_INFO_STAT_PROCESS);

      $msg = "{$classLocale['adm_done']}: {$total_time} {$classLocale['sys_sec']}.";

      // TODO: Analyze maintenance result. Add record to log if error. Add record to log if OK
      $maintenance_result = sys_maintenance();

      classSupernova::$config->db_saveItem('var_stat_update', SN_TIME_SQL);
      classSupernova::$config->db_saveItem('var_stat_update_msg', $msg);
      classSupernova::$config->db_saveItem('var_stat_update_next', $next_run);
      classSupernova::$config->db_saveItem('var_stat_update_admin_forced', SN_TIME_SQL);
      classSupernova::$config->db_saveItem('var_stat_update_end', SN_TIME_SQL);

      if($old_server_status == GAME_DISABLE_STAT) {
        $old_server_status = GAME_DISABLE_NONE;
      }
      classSupernova::$config->db_saveItem('game_disable', $old_server_status);
    } elseif($ts_scheduled_update > $ts_var_stat_update) {
      $timeout = strtotime(classSupernova::$config->db_loadItem('var_stat_update_end')) - SN_TIME_NOW;
      $msg = classSupernova::$config->db_loadItem('var_stat_update_msg');
      $msg = "{$msg} ETA {$timeout} seconds. Please wait...";
    }
    sn_db_transaction_rollback();
  } elseif($is_admin_request) {
    $msg = 'Stat is up to date';
  }

  return $msg;
}
