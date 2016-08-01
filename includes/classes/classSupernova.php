<?php

use Vector\Vector;

use Common\GlobalContainer;

class classSupernova {
  /**
   * @var GlobalContainer $gc
   */
  public static $gc;

  /**
   * ex $sn_mvc
   *
   * @var array
   */
  public static $sn_mvc = array();

  /**
   * ex $functions
   *
   * @var array
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
   * @var \DBAL\DbTransaction
   */
  public static $transaction;

  /**
   * @var SnCache $dbCache
   */
  public static $dbCache;

  /**
   * Настройки из файла конфигурации
   *
   * @var string
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
   * Кэш игры
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
   * @var userOptions
   */
  public static $user_options;

  /**
   * @var debug $debug
   */
  public static $debug = null;

  public static $options = array();

  // Кэш индексов - ключ MD5-строка от суммы ключевых строк через | - менять | на что-то другое перед поиском и назад - после поиска
  // Так же в индексах могут быть двойные вхождения - например, названия планет да и вообще
  // Придумать спецсимвол для NULL

  /*
  TODO Кэш:
  1. Всегда дешевле использовать процессор, чем локальную память
  2. Всегда дешевле использовать локальную память, чем общую память всех процессов
  3. Всегда дешевле использовать общую память всех процессов, чем обращаться к БД

  Кэш - многоуровневый: локальная память-общая память-БД
  БД может быть сверхкэширующей - см. HyperNova. Это реализуется на уровне СН-драйвера БД
  Предусмотреть вариант, когда уровни кэширования совпадают, например когда нет xcache и используется общая память
  */

  // TODO Автоматически заполнять эту таблицу. В случае кэша в памяти - делать show table при обращении к таблице
//  public static $location_info = array(
//    LOC_USER => array(
//      P_TABLE_NAME => 'users',
//      P_ID         => 'id',
//      P_OWNER_INFO => array(),
//    ),
//
//    LOC_PLANET => array(
//      P_TABLE_NAME => 'planets',
//      P_ID         => 'id',
//      P_OWNER_INFO => array(
//        LOC_USER => array(
//          P_LOCATION    => LOC_USER,
//          P_OWNER_FIELD => 'id_owner',
//        ),
//      ),
//    ),
//
//    LOC_UNIT => array(
//      P_TABLE_NAME => 'unit',
//      P_ID         => 'unit_id',
//      P_OWNER_INFO => array(
//        LOC_USER => array(
//          P_LOCATION    => LOC_USER,
//          P_OWNER_FIELD => 'unit_player_id',
//        ),
//      ),
//    ),
//
//    LOC_QUE => array(
//      P_TABLE_NAME => 'que',
//      P_ID         => 'que_id',
//      P_OWNER_INFO => array(
//        array(
//          P_LOCATION    => LOC_USER,
//          P_OWNER_FIELD => 'que_player_id',
//        ),
//
//        array(
//          P_LOCATION    => LOC_PLANET,
//          P_OWNER_FIELD => 'que_planet_id_origin',
//        ),
//
//        array(
//          P_LOCATION    => LOC_PLANET,
//          P_OWNER_FIELD => 'que_planet_id',
//        ),
//      ),
//    ),
//
//    LOC_FLEET => array(
//      P_TABLE_NAME => 'fleets',
//      P_ID         => 'fleet_id',
//      P_OWNER_INFO => array(
//        array(
//          P_LOCATION    => LOC_USER,
//          P_OWNER_FIELD => 'fleet_owner',
//        ),
//
//        array(
//          P_LOCATION    => LOC_USER,
//          P_OWNER_FIELD => 'fleet_target_owner',
//        ),
//
//        array(
//          P_LOCATION    => LOC_PLANET,
//          P_OWNER_FIELD => 'fleet_start_planet_id',
//        ),
//
//        array(
//          P_LOCATION    => LOC_PLANET,
//          P_OWNER_FIELD => 'fleet_end_planet_id',
//        ),
//      ),
//    ),
//  );


  public static function log_file($message, $spaces = 0) {
    if (self::$debug) {
      self::$debug->log_file($message, $spaces);
    }
  }

  // que_process не всегда должна работать в режиме прямой работы с БД !! Она может работать и в режиме эмуляции
  // !!!!!!!! После que_get брать не [0] элемент, а first() - тогда можно в индекс элемента засовывать que_id из таблицы


  public static function init_0_prepare() {
    // Отключаем magic_quotes
    ini_get('magic_quotes_sybase') ? die('SN is incompatible with \'magic_quotes_sybase\' turned on. Disable it in php.ini or .htaccess...') : false;
    if (@get_magic_quotes_gpc()) {
      $gpcr = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
      array_walk_recursive($gpcr, function (&$value, $key) {
        $value = stripslashes($value);
      });
    }
    if (function_exists('set_magic_quotes_runtime')) {
      @set_magic_quotes_runtime(0);
      @ini_set('magic_quotes_runtime', 0);
      @ini_set('magic_quotes_sybase', 0);
    }
  }

  public static function init_1_globalContainer() {
    static::$gc = new GlobalContainer();
    $gc = static::$gc;

    // Default db
    $gc->db = function ($c) {
      $db = new db_mysql($c);
      $db->sn_db_connect();

      return $db;
    };

    $gc->debug = function ($c) {
      return new debug();
    };

    $gc->cache = function ($c) {
      return new classCache(classSupernova::$cache_prefix);
    };

    $gc->config = function ($c) {
      return new classConfig(classSupernova::$cache_prefix);
    };

    $gc->localePlayer = function (GlobalContainer $c) {
      return new classLocale($c->config->server_locale_log_usage);
    };

    $gc->dbRowOperator = function ($c) {
      return new DbRowDirectOperator($c);
    };

    $gc->buddyClass = 'Buddy\BuddyModel';
    $gc->buddy = $gc->factory(function (GlobalContainer $c) {
      return new $c->buddyClass($c);
    });

    $gc->query = $gc->factory(function (GlobalContainer $c) {
      return new DbQueryConstructor($c->db);
    });

    $gc->unit = $gc->factory(function (GlobalContainer $c) {
      return new \V2Unit\V2UnitModel($c);
    });

    $gc->cacheOperator = function(GlobalContainer $gc) {
      return new SnDbCachedOperator($gc);
    };

// TODO
//    $container->vector = $container->factory(function (GlobalContainer $c) {
//      return new Vector($c->db);
//    });
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
