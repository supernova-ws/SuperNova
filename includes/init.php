<?php

// Защита от двойного инита
if(defined('INIT')) {
  return;
}

define('INIT', true);

define('DEBUG_UBE', true);
define('DEBUG_FLYING_FLEETS', true);
// define('SN_DEBUG_LOG', true);

// Замеряем начальные параметры
define('SN_TIME_MICRO', microtime(true));
define('SN_MEM_START', memory_get_usage());

define('SN_DEBUG_PDUMP_CALLER', true);

version_compare(PHP_VERSION, '5.3.2') < 0 ? die('FATAL ERROR: SuperNova REQUIRE PHP version > 5.3.2') : false;

// Бенчмарк
register_shutdown_function(function () {
  if(defined('IN_AJAX')) {
    return;
  }

  global $user, $locale_cache_statistic;

  print('<hr><div class="benchmark">Benchmark ' . (microtime(true) - SN_TIME_MICRO) . 's, memory: ' . number_format(memory_get_usage() - SN_MEM_START) .
    (!empty($locale_cache_statistic['misses']) ? ', LOCALE MISSED' : '') .
    (class_exists('classSupernova') && is_object(classSupernova::$db) ? ', DB time: ' . classSupernova::$db->time_mysql_total . 'ms' : '') .
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

!defined('INSIDE') ? define('INSIDE', true) : false;
!defined('INSTALL') ? define('INSTALL', false) : false;
!defined('IN_PHPBB') ? define('IN_PHPBB', true) : false;

global $phpEx, $sn_root_physical, $phpbb_root_path; // Это нужно для работы PTL
define('SN_TIME_NOW', intval(SN_TIME_MICRO));
define('SN_TIME_ZONE_OFFSET', date('Z'));

define('FMT_DATE_TIME_SQL', 'Y-m-d H:i:s');
define('SN_TIME_SQL', date(FMT_DATE_TIME_SQL, SN_TIME_NOW));

define('SN_TIME_NOW_GMT_STRING', gmdate(DATE_ATOM, SN_TIME_NOW));

if(strpos(strtolower($_SERVER['SERVER_NAME']), 'google.') !== false) {
  define('SN_GOOGLE', true);
}

// Эти три строки должны быть В ЭТОМ ФАЙЛЕ, ПО ЭТОМУ ПУТИ и ПЕРЕД ЭТИМ ИНКЛЮДОМ!!!
$sn_root_physical = str_replace('\\', '/', __FILE__);
$sn_root_physical = str_replace('includes/init.php', '', $sn_root_physical);
define('SN_ROOT_PHYSICAL', $sn_root_physical);
// define('SN_ROOT_PHYSICAL_STR_LEN', mb_strlen($sn_root_physical));
define('SN_ROOT_PHYSICAL_STR_LEN', strlen($sn_root_physical));
$phpbb_root_path = SN_ROOT_PHYSICAL; // Это нужно для работы PTL

$sn_root_relative = str_replace('\\', '/', getcwd());
$sn_root_relative .= $sn_root_relative[strlen($sn_root_relative) - 1] == '/' ? '' : '/';
$sn_root_relative = str_replace(SN_ROOT_PHYSICAL, '', $sn_root_relative);
$sn_root_relative .= basename($_SERVER['SCRIPT_NAME']);
$sn_root_relative = str_replace($sn_root_relative, '', $_SERVER['SCRIPT_NAME']);
define('SN_ROOT_RELATIVE', $sn_root_relative);

define('SN_ROOT_VIRTUAL', 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . SN_ROOT_RELATIVE);
define('SN_ROOT_VIRTUAL_PARENT', str_replace('//google.', '//', SN_ROOT_VIRTUAL));

$phpEx = strpos($phpEx = substr(strrchr(__FILE__, '.'), 1), '/') === false ? $phpEx : '';
define('PHP_EX', $phpEx); // PHP extension on this server
define('DOT_PHP_EX', '.' . PHP_EX); // PHP extension on this server


require_once('constants.php');

require_once('classes/supernova.php');

classSupernova::init_0_prepare();
//classSupernova::init_1_constants();
classSupernova::init_3_load_config_file();

header('Content-type: text/html; charset=utf-8');
ob_start();
ini_set('error_reporting', E_ALL ^ E_NOTICE);


// TODO - Разобраться с порядком подключени и зависимостями объектов
require_once('classes/core_classes.php');

// required for db.php
// Initializing global 'debug' object
require_once(SN_ROOT_PHYSICAL . "includes/debug.class" . DOT_PHP_EX);
global $debug;
classSupernova::$debug = $debug = new debug();

spl_autoload_register(function ($class) {
  if(file_exists(SN_ROOT_PHYSICAL . 'includes/classes/' . $class . '.php')) {
    require_once SN_ROOT_PHYSICAL . 'includes/classes/' . $class . '.php';
  }
});

spl_autoload_register(function ($class) {
  if(file_exists(SN_ROOT_PHYSICAL . 'includes/classes/UBE/' . $class . '.php')) {
    require_once SN_ROOT_PHYSICAL . 'includes/classes/UBE/' . $class . '.php';
  }
});

require_once(SN_ROOT_PHYSICAL . "includes/db" . DOT_PHP_EX);
require_once('classes/db_mysql_v4.php');
require_once('classes/db_mysql_v5.php');
require_once('classes/db_mysql.php');
classSupernova::init_main_db(new db_mysql());


require_once('classes/cache.php');
require_once('classes/locale.php');
require_once('classes/functions_template.php');
require_once('classes/module.php');

require_once('classes/user_options.php');
require_once(SN_ROOT_PHYSICAL . "includes/init/init_functions" . DOT_PHP_EX);

/**
 * @var classConfig    $config
 * @var classSupernova $supernova
 */
global $supernova, $sn_cache, $auth;

classSupernova::init_global_objects();

// Отладка
// define('BE_DEBUG', true); // Отладка боевого движка
classSupernova::init_debug_state();

require_once(SN_ROOT_PHYSICAL . "includes/vars" . DOT_PHP_EX);
require_once(SN_ROOT_PHYSICAL . "includes/general" . DOT_PHP_EX);

init_update();

// Initializing constants
$sn_page_name_original = isset($_GET['page'])
  ? trim(strip_tags($_GET['page']))
  : str_replace(DOT_PHP_EX, '', str_replace(SN_ROOT_RELATIVE, '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])));
define('INITIAL_PAGE', $sn_page_name_original);
define('SN_COOKIE', (classSupernova::$config->COOKIE_NAME ? classSupernova::$config->COOKIE_NAME : 'SuperNova') . (defined('SN_GOOGLE') ? '_G' : ''));
define('SN_COOKIE_I', SN_COOKIE . AUTH_COOKIE_IMPERSONATE_SUFFIX);
define('SN_COOKIE_D', SN_COOKIE . '_D');
define('SN_COOKIE_T', SN_COOKIE . '_T'); // Time measure cookie
define('SN_COOKIE_F', SN_COOKIE . '_F'); // Font size cookie
define('SN_COOKIE_U', SN_COOKIE . '_U'); // Current user cookie aka user ID
define('SN_COOKIE_U_I', SN_COOKIE_U . AUTH_COOKIE_IMPERSONATE_SUFFIX); // Current impersonator user cookie aka impersonator user ID
define('TEMPLATE_NAME', classSupernova::$config->game_default_template ? classSupernova::$config->game_default_template : 'OpenGame');
define('TEMPLATE_PATH', 'design/templates/' . TEMPLATE_NAME);
define('TEMPLATE_DIR', SN_ROOT_PHYSICAL . TEMPLATE_PATH);
define('DEFAULT_SKINPATH', classSupernova::$config->game_default_skin ? classSupernova::$config->game_default_skin : 'skins/EpicBlue/');
define('DEFAULT_LANG', classSupernova::$config->game_default_language ? classSupernova::$config->game_default_language : 'ru');
define('FMT_DATE', classSupernova::$config->int_format_date ? classSupernova::$config->int_format_date : 'd.m.Y');
define('FMT_TIME', classSupernova::$config->int_format_time ? classSupernova::$config->int_format_time : 'H:i:s');
define('FMT_DATE_TIME', FMT_DATE . ' ' . FMT_TIME);

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

require_once(SN_ROOT_PHYSICAL . "includes/template" . DOT_PHP_EX);
$template_result = array('.' => array('result' => array()));

sn_sys_load_php_files(SN_ROOT_PHYSICAL . "includes/functions/", PHP_EX);

spl_autoload_register(function ($class) {
  if(file_exists('includes/classes/' . $class . '.php')) {
    require_once 'includes/classes/' . $class . '.php';
  } elseif(file_exists('includes/classes/UBE/' . $class . '.php')) {
    require_once 'includes/classes/UBE/' . $class . '.php';
  } else {
//    die("Can't find {$class} class");
  }
});



// Подключаем все модули
// По нормальным делам тут надо подключать манифесты
// И читать конфиги - вдруг модуль отключен?
// Конфиг - часть манифеста?
classSupernova::$auth = new core_auth();

sn_sys_load_php_files(SN_ROOT_PHYSICAL . "modules/", PHP_EX, true);
// Здесь - потому что core_auth модуль лежит в другом каталоге и его нужно инициализировать отдельно

// Подключаем дефолтную страницу
// По нормальным делам её надо подключать в порядке загрузки обработчиков
// Сейчас мы делаем это здесь только для того, что бы содержание дефолтной страницы оказалось вверху. Что не факт, что нужно всегда
// Но нужно, пока у нас есть не MVC-страницы
$sn_page_data = $sn_data['pages'][$sn_page_name];
$sn_page_name_file = 'includes/pages/' . $sn_page_data['filename'] . DOT_PHP_EX;
if($sn_page_name && isset($sn_page_data) && file_exists($sn_page_name_file)) {
  require_once($sn_page_name_file);
  if(is_array($sn_page_data['options'])) {
    $supernova->options = array_merge($supernova->options, $sn_page_data['options']);
  }
}

// load_order:
//  100000 - default load order
//  999999 - core_ship_constructor
//  2000000000 - that requires that all possible modules loaded already
//  2000100000 - game_skirmish

// Генерируем список требуемых модулей
$load_order = array();
$sn_req = array();

foreach(sn_module::$sn_module as $loaded_module_name => $module_data) {
  $load_order[$loaded_module_name] = isset($module_data->manifest['load_order']) && !empty($module_data->manifest['load_order']) ? $module_data->manifest['load_order'] : 100000;
  if(isset($module_data->manifest['require']) && !empty($module_data->manifest['require'])) {
    foreach($module_data->manifest['require'] as $require_name) {
      $sn_req[$loaded_module_name][$require_name] = 0;
    }
  }
}

// pdump($load_order, '$load_order');

// Создаем последовательность инициализации модулей
// По нормальным делам надо сначала читать их конфиги - вдруг какой-то модуль отключен?
do {
  $prev_order = $load_order;

  foreach($sn_req as $loaded_module_name => &$req_data) {
    $level = 1;
    foreach($req_data as $req_name => &$req_level) {
      if($load_order[$req_name] == -1 || !isset($load_order[$req_name])) {
        $level = $req_level = -1;
        break;
      } else {
        $level += $load_order[$req_name];
      }
      $req_level = $load_order[$req_name];
    }
    if($level > $load_order[$loaded_module_name] || $level == -1) {
      $load_order[$loaded_module_name] = $level;
    }
  }
} while($prev_order != $load_order);

asort($load_order);

// Инициализируем модули
// По нормальным делам это должна быть загрузка модулей и лишь затем инициализация - что бы минимизировать размер процесса в памяти
foreach($load_order as $loaded_module_name => $load_order_order) {
  if($load_order_order >= 0) {
    sn_module::$sn_module[$loaded_module_name]->check_status();
    if(!sn_module::$sn_module[$loaded_module_name]->manifest['active']) {
      unset(sn_module::$sn_module[$loaded_module_name]);
      continue;
    }

    sn_module::$sn_module[$loaded_module_name]->initialize();
    sn_module::$sn_module_list[sn_module::$sn_module[$loaded_module_name]->manifest['package']][$loaded_module_name] = &sn_module::$sn_module[$loaded_module_name];
  } else {
    unset(sn_module::$sn_module[$loaded_module_name]);
  }
}

// Скрипач не нужон
unset($load_order);
unset($sn_req);

// А теперь проверяем - поддерживают ли у нас загруженный код такую страницу
if(!isset($sn_data['pages'][$sn_page_name])) {
  $sn_page_name = '';
}

global $lang;
classLocale::$lang = $lang = new classLocale(classSupernova::$config->server_locale_log_usage);
classLocale::$lang->lng_switch(sys_get_param_str('lang'));

if(!defined('DEBUG_INIT_SKIP_SECONDARY') || DEBUG_INIT_SKIP_SECONDARY !== true) {
  require_once "init_secondary.php";
}
