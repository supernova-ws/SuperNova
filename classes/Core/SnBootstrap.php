<?php
/**
 * Created by Gorlum 11.06.2017 9:58
 */

namespace Core;


use core_auth;
use DBAL\db_mysql;
use \SN;

class SnBootstrap {

  public static function install_benchmark() {
    register_shutdown_function(function () {
      if (defined('IN_AJAX')) {
        return;
      }

      global $user, $locale_cache_statistic;

      $now       = microtime(true);
      $totalTime = round($now - SN_TIME_MICRO, 6);
      !defined('SN_TIME_RENDER_START') ? define('SN_TIME_RENDER_START', microtime(true)) : false;
      $executionTime = round(SN_TIME_RENDER_START - SN_TIME_MICRO, 6);
      $displayTime   = round($now - SN_TIME_RENDER_START, 6);

      $benchmarkResults =
        '[' . SN_TIME_SQL . '] '
        . 'Benchmark ' . $totalTime . 's'
        . (defined('SN_TIME_RENDER_START')
          ?
          " (exec: {$executionTime}s" .
          ", display: {$displayTime}s"
          . (class_exists('SN') && is_object(SN::$db) ? ', DB: ' . round(SN::$db->time_mysql_total, 6) . 's' : '')
          . ")"
          : ''
        )
        . ', memory: ' . number_format(memory_get_usage() - SN_MEM_START)
        . (!empty($locale_cache_statistic['misses']) ? ', LOCALE MISSED' : '')
        . '';

      $benchPrefix = '<div id="benchmark" class="benchmark" style="flex-grow: 1;flex-shrink: 1;"><hr>';
      $benchSuffix = '</div>';
      if (class_exists(SN::class, false) && SN::$gSomethingWasRendered) {
//        print "<script type='text/javascript'>document.body.innerHTML += '{$benchPrefix}" . htmlentities($benchmarkResults, ENT_QUOTES, 'UTF-8') . "{$benchSuffix}';</script>";
//        print "<script type='text/javascript'>document.addEventListener('DOMContentLoaded', function() {document.body.innerHTML += '{$benchPrefix}" . htmlentities($benchmarkResults, ENT_QUOTES, 'UTF-8') . "{$benchSuffix}';});</script>";
        print
"<script type='text/javascript'>
(function (document) {
    var prefix = '{$benchPrefix}';
    var suffix = '{$benchSuffix}';
    var result = '" . htmlentities($benchmarkResults, ENT_QUOTES, 'UTF-8') . "';
    var element = document.getElementById('debug');

    if(element) {
      element.innerHTML += prefix + result + suffix;
    } else {
      document.write(prefix + result + suffix);
    }
}(document));
</script>";
      } else {
        print($benchPrefix . $benchmarkResults . $benchSuffix);
      }


      if (isset($user['authlevel']) && $user['authlevel'] >= 2 && file_exists(SN_ROOT_PHYSICAL . 'badqrys.txt') && @filesize(SN_ROOT_PHYSICAL . 'badqrys.txt') > 0) {
        echo '<a href="badqrys.txt" target="_blank" style="color:red">', 'HACK ALERT!', '</a>';
      }

      if (!empty($locale_cache_statistic['misses'])) {
        print('<!--');
        pdump($locale_cache_statistic);
        print('-->');
      }

      $error = error_get_last();
      if ($error['type'] === E_ERROR) {
        $fName  = SN_ROOT_PHYSICAL . '_error.txt';
        $output = [
          "\n\n",
          SN_TIME_SQL . " - ERROR",
          var_export($error, true),
//          var_export(debug_backtrace(), true),
          var_export(core_auth::$device, true),
        ];
        file_put_contents($fName, implode("\n", $output), FILE_APPEND | LOCK_EX);

        if (!empty($error['file']) && strpos($error['file'], 'classCache.php') !== false) {
          print('<span style="color: red">Looks like cache clearing takes too long... Try to restart your web-server and/or cache engine</span>');
        }
      }
    });
  }

  public static function init_debug_state() {
    if ($_SERVER['SERVER_NAME'] == 'localhost' && !defined('BE_DEBUG')) {
      define('BE_DEBUG', true);
    }

    // Declaring PHP-constants from server config
    /** @see \classConfig::$DEBUG_SQL_FILE_LOG */
    foreach ([
      'DEBUG_SQL_FILE_LOG'     => ['DEBUG_SQL_ERROR' => true, 'DEBUG_SQL_COMMENT_LONG' => true,],
      'DEBUG_SQL_ERROR'        => ['DEBUG_SQL_COMMENT' => true,],
      'DEBUG_SQL_COMMENT_LONG' => ['DEBUG_SQL_COMMENT' => true,],
      'DEBUG_SQL_COMMENT'      => []
    ] as $constantName => $implications) {
      if (!empty(SN::$config->$constantName) && !defined($constantName)) {
        define($constantName, true);
      }
      foreach ($implications as $impliedConstantName => $impliedValue) {
        if (!defined($impliedConstantName)) {
          define($impliedConstantName, $impliedValue);
        }
      }
    }

    if (defined('BE_DEBUG') || SN::$config->debug) {
      @define('BE_DEBUG', true);
      @ini_set('display_errors', 1);
      if(SN::$config->debug == 1) {
        @error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
      } else {
        @error_reporting(SN::$config->debug);
      }
    } else {
      @define('BE_DEBUG', false);
      @ini_set('display_errors', 0);
    }

  }

  /**
   * @param \classConfig $config
   */
  public static function performUpdate($config) {
    if (
      !file_exists($update_file = SN_ROOT_PHYSICAL . "includes/update.php")
      ||
      (
        filemtime($update_file) <= $config->pass()->var_db_update
        &&
        $config->pass()->db_version >= DB_VERSION
      )
    ) {
      return;
    }

    if (defined('IN_ADMIN') || !$config->pass()->game_installed) {
      db_mysql::db_transaction_start(); // Для защиты от двойного запуска апдейта - начинаем транзакцию. Так запись в базе будет блокирована
      if (SN_TIME_NOW >= $config->pass()->var_db_update_end) {
        $config->pass()->var_db_update_end = SN_TIME_NOW + $config->upd_lock_time;
        db_mysql::db_transaction_commit();

        require_once($update_file);

        $current_time                      = time();
        $config->pass()->var_db_update     = $current_time;
        $config->pass()->var_db_update_end = $current_time;
        $config->pass()->game_installed    = 1;
      } elseif (filemtime($update_file) > $config->var_db_update) {
        $timeout = $config->var_db_update_end - SN_TIME_NOW;
        die(
        "Обновляется база данных. Рассчетное время окончания - {$timeout} секунд (время обновления может увеличиваться). Пожалуйста, подождите...<br />
        Obnovljaetsja baza dannyh. Rasschetnoe vremya okonchanija - {$timeout} secund. Pozhalujsta, podozhdute...<br />
        Database update in progress. Estimated update time {$timeout} seconds (can increase depending on update process). Please wait..."
        );
      }
      db_mysql::db_transaction_rollback();
    } else {
      die(
      'Происходит обновление сервера - пожалуйста, подождите...<br />
      Proishodit obnovlenie servera - pozhalujsta, podozhdute...<br />
      Server upgrading now - please wait...<br />
      <a href="admin/overview.php">Admin link</a>'
      );
    }
  }

}
