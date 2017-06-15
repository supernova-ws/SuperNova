<?php
/**
 * Created by Gorlum 11.06.2017 9:58
 */

namespace Core;


use \classSupernova;

class SnBootstrap {

  public static function install_benchmark() {
    register_shutdown_function(function () {
      if(defined('IN_AJAX')) {
        return;
      }

      global $user, $locale_cache_statistic;

      $now = microtime(true);
      $totalTime = round($now - SN_TIME_MICRO, 6);
      $executionTime = round(SN_TIME_RENDER_START - SN_TIME_MICRO, 6);
      $displayTime = round($now - SN_TIME_RENDER_START, 6);

      $otherTime = defined('SN_TIME_RENDER_START') ? " (exec: {$executionTime}, display: {$displayTime})" : '';

      print('<div id="benchmark" class="benchmark"><hr>Benchmark ' . $totalTime . 's' . $otherTime . ', memory: ' . number_format(memory_get_usage() - SN_MEM_START) .
        (!empty($locale_cache_statistic['misses']) ? ', LOCALE MISSED' : '') .
        (class_exists('classSupernova') && is_object(classSupernova::$db) ? ', DB time: ' . round(classSupernova::$db->time_mysql_total, 6) . 's' : '') .
        '</div>');
      if($user['authlevel'] >= 2 && file_exists(SN_ROOT_PHYSICAL . 'badqrys.txt') && @filesize(SN_ROOT_PHYSICAL . 'badqrys.txt') > 0) {
        echo '<a href="badqrys.txt" target="_blank" style="color:red">', 'HACK ALERT!', '</a>';
      }

      if(!empty($locale_cache_statistic['misses'])) {
        print('<!--');
        pdump($locale_cache_statistic);
        print('-->');
      }
    });
  }

  public static function init_debug_state() {
    if($_SERVER['SERVER_NAME'] == 'localhost' && !defined('BE_DEBUG')) {
      define('BE_DEBUG', true);
    }
    // define('DEBUG_SQL_ONLINE', true); // Полный дамп запросов в рил-тайме. Подойдет любое значение
    define('DEBUG_SQL_ERROR', true); // Выводить в сообщении об ошибке так же полный дамп запросов за сессию. Подойдет любое значение
    define('DEBUG_SQL_COMMENT_LONG', true); // Добавлять SQL запрос длинные комментарии. Не зависим от всех остальных параметров. Подойдет любое значение
    define('DEBUG_SQL_COMMENT', true); // Добавлять комментарии прямо в SQL запрос. Подойдет любое значение
    // Включаем нужные настройки
    defined('DEBUG_SQL_ONLINE') && !defined('DEBUG_SQL_ERROR') ? define('DEBUG_SQL_ERROR', true) : false;
    defined('DEBUG_SQL_ERROR') && !defined('DEBUG_SQL_COMMENT') ? define('DEBUG_SQL_COMMENT', true) : false;
    defined('DEBUG_SQL_COMMENT_LONG') && !defined('DEBUG_SQL_COMMENT') ? define('DEBUG_SQL_COMMENT', true) : false;

    if(defined('BE_DEBUG') || classSupernova::$config->debug) {
      @define('BE_DEBUG', true);
      @ini_set('display_errors', 1);
      @error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
    } else {
      @define('BE_DEBUG', false);
      @ini_set('display_errors', 0);
    }

  }

  /**
   * @param \classConfig $config
   */
  public static function performUpdate(&$config) {
    $update_file = SN_ROOT_PHYSICAL . "includes/update.php";
    if(
      !file_exists($update_file)
      ||
      (
        filemtime($update_file) <= $config->db_loadItem('var_db_update')
        &&
        $config->db_loadItem('db_version') >= DB_VERSION
      )
    ) {
      return;
    }

    if(defined('IN_ADMIN')) {
      sn_db_transaction_start(); // Для защиты от двойного запуска апдейта - начинаем транзакцию. Так запись в базе будет блокирована
      if(SN_TIME_NOW >= $config->db_loadItem('var_db_update_end')) {
        $config->db_saveItem('var_db_update_end', SN_TIME_NOW + ($config->upd_lock_time ? $config->upd_lock_time : 300));
        sn_db_transaction_commit();

        require_once($update_file);

        $current_time = time();
        $config->db_saveItem('var_db_update', $current_time);
        $config->db_saveItem('var_db_update_end', $current_time);
      } elseif(filemtime($update_file) > $config->var_db_update) {
        $timeout = $config->var_db_update_end - SN_TIME_NOW;
        die(
        "Обновляется база данных. Рассчетное время окончания - {$timeout} секунд (время обновления может увеличиваться). Пожалуйста, подождите...<br />
        Obnovljaetsja baza dannyh. Rasschetnoe vremya okonchanija - {$timeout} secund. Pozhalujsta, podozhdute...<br />
        Database update in progress. Estimated update time {$timeout} seconds (can increase depending on update process). Please wait..."
        );
      }
      sn_db_transaction_rollback();
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
