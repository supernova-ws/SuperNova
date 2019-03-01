<?php
/**
 * Created by Gorlum 24.09.2017 17:15
 */

use Fleet\DbFleetStatic;

/**
 * Class StatUpdateLauncher
 *
 * Part of the scheduling process. Response for Stat Updater launch
 *
 */
class StatUpdateLauncher {

  public static function unlock() {
    if (
      SN::$config->game_disable != GAME_DISABLE_STAT || SN_TIME_NOW - strtotime(SN::$config->pass()->var_stat_update_end) <= STATS_RUN_INTERVAL_MINIMUM
    ) {
      return;
    }
    $next_run = date(FMT_DATE_TIME_SQL, sys_schedule_get_prev_run(SN::$config->stats_schedule, SN::$config->var_stat_update, true));
    SN::$config->pass()->game_disable = GAME_DISABLE_NONE;
    SN::$config->pass()->var_stat_update = SN_TIME_SQL;
    SN::$config->pass()->var_stat_update_next = $next_run;
    SN::$config->pass()->var_stat_update_end = SN_TIME_SQL;
    SN::$debug->warning('Stat worked too long - watchdog unlocked', 'Stat WARNING');
  }


  public static function scheduler_process() {
    global $user, $lang;

    $config = SN::$config;
    $debug = SN::$debug;

    $is_admin_request = false;

    $ts_var_stat_update = strtotime($config->pass()->var_stat_update);
    $ts_scheduled_update = sys_schedule_get_prev_run($config->pass()->stats_schedule, $config->var_stat_update);

    if (sys_get_param_int('admin_update')) {
      define('USER_LEVEL', isset($user['authlevel']) ? $user['authlevel'] : -1);
      if (USER_LEVEL > 0) {
        $is_admin_request = true;
        $ts_scheduled_update = SN_TIME_NOW;
      }
    }

    if ($ts_scheduled_update > $ts_var_stat_update) {
      lng_include('admin');
      sn_db_transaction_start();
      $ts_var_stat_update_end = strtotime($config->pass()->var_stat_update_end);
      if (SN_TIME_NOW > $ts_var_stat_update_end) {
        $old_server_status = $config->pass()->game_disable;
        $config->pass()->game_disable = GAME_DISABLE_STAT;

        $statMinimalInterval = intval($config->pass()->stats_minimal_interval);
        $config->pass()->var_stat_update_end= date(FMT_DATE_TIME_SQL, SN_TIME_NOW + ($statMinimalInterval ? $statMinimalInterval : STATS_RUN_INTERVAL_MINIMUM));
        $config->pass()->var_stat_update_msg = 'Update started';
        sn_db_transaction_commit();

        $msg = $is_admin_request ? 'admin request' : 'scheduler';
        $next_run = date(FMT_DATE_TIME_SQL, sys_schedule_get_prev_run($config->stats_schedule, $config->pass()->var_stat_update, true));
        $msg = "Running stat updates: {$msg}. Config->var_stat_update = " . $config->var_stat_update .
          ', $ts_scheduled_update = ' . date(FMT_DATE_TIME_SQL, $ts_scheduled_update) .
          ', next_stat_update = ' . $next_run;
        $debug->warning($msg, 'Stat update', LOG_INFO_STAT_PROCESS);
        $total_time = microtime(true);

        StatCalculator::sys_stat_calculate();

        DbFleetStatic::db_fleet_acs_purge();

        $total_time = microtime(true) - $total_time;
        $msg = "Stat update complete in {$total_time} seconds.";
        $debug->warning($msg, 'Stat update', LOG_INFO_STAT_PROCESS);

        $msg = "{$lang['adm_done']}: {$total_time} {$lang['sys_sec']}."; // . date(FMT_DATE_TIME, $ts_scheduled_update) . ' ' . date(FMT_DATE_TIME, $config->var_stat_update);

        // TODO: Analyze maintenance result. Add record to log if error. Add record to log if OK
        $maintenance_result = sys_maintenance();

        $config->pass()->var_stat_update = SN_TIME_SQL;
        $config->pass()->var_stat_update_msg = $msg;
        $config->pass()->var_stat_update_next = $next_run;
        $config->pass()->var_stat_update_admin_forced = SN_TIME_SQL;
        $config->pass()->var_stat_update_end = SN_TIME_SQL;

        $config->pass()->game_disable = $old_server_status;
      } elseif ($ts_scheduled_update > $ts_var_stat_update) {
        $timeout = strtotime($config->pass()->var_stat_update_end) - SN_TIME_NOW;
        $msg = $config->pass()->var_stat_update_msg;
        $msg = "{$msg} ETA {$timeout} seconds. Please wait...";
      }
      sn_db_transaction_rollback();
    } elseif ($is_admin_request) {
      $msg = 'Stat is up to date';
    }

    return $msg;
  }

}
