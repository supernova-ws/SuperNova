<?php

use Vector\Vector;

use Common\GlobalContainer;

class classSupernova {
  /**
   * @var GlobalContainer $gc
   */
  public static $gc;

  /**
   * @var array $sn_mvc
   */
  public static $sn_mvc = array();

  /**
   * @var array $functions
   */
  public static $functions = array();

  /**
   * @var array[] $design
   */
  public static $design = array(
    'bbcodes' => array(),
    'smiles'  => array(),
  );

  /**
   * Основная БД для доступа к данным
   *
   * @var db_mysql $db
   */
  public static $db;
  public static $db_name = '';
  /**
   * @var \DBAL\DbTransaction $transaction
   */
  public static $transaction;

  /**
   * Настройки из файла конфигурации
   *
   * @var string $cache_prefix
   */
  public static $cache_prefix = '';
  public static $sn_secret_word = '';

  /**
   * Конфигурация игры
   *
   * @var classConfig $config
   */
  public static $config;

  /**
   * External cache
   *
   * @var classCache $cache
   */
  public static $cache;

  /**
   * @var core_auth $auth
   */
  public static $auth = null;


  public static $user = array();
  /**
   * @var userOptions $user_options
   */
  public static $user_options;

  /**
   * @var debug $debug
   */
  public static $debug = null;

  public static $options = array();

  public static function log_file($message, $spaces = 0) {
    if (self::$debug) {
      self::$debug->log_file($message, $spaces);
    }
  }

  public static function init_0_prepare() {
    static::$gc = new GlobalContainer();
  }

  public static function init_3_load_config_file() {
    $dbsettings = array();
    require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);
    self::$cache_prefix = !empty($dbsettings['cache_prefix']) ? $dbsettings['cache_prefix'] : $dbsettings['prefix'];
    self::$db_name = $dbsettings['name'];
    self::$sn_secret_word = $dbsettings['secretword'];
    unset($dbsettings);
  }

  public static function init_global_objects() {
    self::$debug = self::$gc->debug;
    self::$db = self::$gc->db;
    self::$db->sn_db_connect();

    self::$user_options = new userOptions(0);

    // Initializing global 'cacher' object
    self::$cache = self::$gc->cache;

    empty(static::$cache->tables) ? sys_refresh_tablelist() : false;
    empty(static::$cache->tables) ? die('DB error - cannot find any table. Halting...') : false;

    // Initializing global "config" object
    static::$config = self::$gc->config;

    // Initializing statics
    Vector::_staticInit(static::$config);
  }

  public static function init_debug_state() {
    if ($_SERVER['SERVER_NAME'] == 'localhost' && !defined('BE_DEBUG')) {
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

    if (defined('BE_DEBUG') || static::$config->debug) {
      @define('BE_DEBUG', true);
      @ini_set('display_errors', 1);
      @error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
    } else {
      @define('BE_DEBUG', false);
      @ini_set('display_errors', 0);
    }

  }

}
