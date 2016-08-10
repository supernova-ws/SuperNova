<?php

// Защита от двойного инита
if(defined('INIT')) {
  return;
}

define('INIT', true);

// Замеряем начальные параметры
define('SN_TIME_MICRO', microtime(true));
define('SN_MEM_START', memory_get_usage());

version_compare(PHP_VERSION, '5.3.2') < 0 ? die('FATAL ERROR: SuperNova REQUIRE PHP version > 5.3.2') : false;

define('DEBUG_UBE', true);
define('DEBUG_FLYING_FLEETS', true);
// define('SN_DEBUG_LOG', true);
define('SN_DEBUG_PDUMP_CALLER', true);

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

global $phpEx, $phpbb_root_path; // Это нужно для работы PTL
define('SN_TIME_NOW', intval(SN_TIME_MICRO));
define('SN_TIME_ZONE_OFFSET', date('Z'));

define('FMT_DATE_TIME_SQL', 'Y-m-d H:i:s');
define('SN_TIME_SQL', date(FMT_DATE_TIME_SQL, SN_TIME_NOW));

define('SN_TIME_NOW_GMT_STRING', gmdate(DATE_ATOM, SN_TIME_NOW));

if(strpos(strtolower($_SERVER['SERVER_NAME']), 'google.') !== false) {
  define('SN_GOOGLE', true);
}

// Эти три строки должны быть В ЭТОМ ФАЙЛЕ, ПО ЭТОМУ ПУТИ и ПЕРЕД ЭТИМ ИНКЛЮДОМ!!!
define('SN_ROOT_PHYSICAL', str_replace(array('\\', '//'), '/', dirname(__DIR__) . '/'));
define('SN_ROOT_PHYSICAL_STR_LEN', strlen(SN_ROOT_PHYSICAL));
$phpbb_root_path = SN_ROOT_PHYSICAL; // Это нужно для работы PTL

$sn_root_relative = str_replace(array('\\', '//'), '/', getcwd() . '/');
//$sn_root_relative .= $sn_root_relative[strlen($sn_root_relative) - 1] == '/' ? '' : '/';
$sn_root_relative = str_replace(SN_ROOT_PHYSICAL, '', $sn_root_relative);
$sn_root_relative .= basename($_SERVER['SCRIPT_NAME']);
$sn_root_relative = str_replace($sn_root_relative, '', $_SERVER['SCRIPT_NAME']);
define('SN_ROOT_RELATIVE', $sn_root_relative);

define('SN_ROOT_VIRTUAL', 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . SN_ROOT_RELATIVE);
define('SN_ROOT_VIRTUAL_PARENT', str_replace('//google.', '//', SN_ROOT_VIRTUAL));

$phpEx = strpos($phpEx = substr(strrchr(__FILE__, '.'), 1), '/') === false ? $phpEx : '';
define('PHP_EX', $phpEx); // PHP extension on this server
define('DOT_PHP_EX', '.' . PHP_EX); // PHP extension on this server


header('Content-type: text/html; charset=utf-8');
ob_start();
ini_set('error_reporting', E_ALL ^ E_NOTICE);

empty($classRoot) ? $classRoot = SN_ROOT_PHYSICAL . 'includes/classes/' : false;
spl_autoload_register(function ($class) use ($classRoot) {
  $class = str_replace('\\', '/', $class);
  if (file_exists($classRoot . $class . '.php')) {
    require_once $classRoot . $class . '.php';
  } elseif (file_exists($classRoot . 'UBE/' . $class . '.php')) {
    require_once $classRoot . 'UBE/' . $class . '.php';
  }
});

require_once 'constants.php';
require_once SN_ROOT_PHYSICAL . "includes/db" . DOT_PHP_EX;
require_once(SN_ROOT_PHYSICAL . "includes/init/init_functions" . DOT_PHP_EX);

/**
 * @var classConfig    $config
 */
global $auth;

classSupernova::init_0_prepare();
classSupernova::init_1_globalContainer();
classSupernova::init_3_load_config_file();

// required for db.php
// Initializing global 'debug' object
classSupernova::init_global_objects();

// Отладка
// define('BE_DEBUG', true); // Отладка боевого движка
classSupernova::init_debug_state();

require_once(SN_ROOT_PHYSICAL . "includes/vars/vars" . DOT_PHP_EX);
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


// Подключаем все модули
// По нормальным делам тут надо подключать манифесты
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
    classSupernova::$options = array_merge(classSupernova::$options, $sn_page_data['options']);
  }
}

sn_module::orderModules();



// А теперь проверяем - поддерживают ли у нас загруженный код такую страницу
if(!isset($sn_data['pages'][$sn_page_name])) {
  $sn_page_name = '';
}

global $lang;
classLocale::$lang = $lang = classSupernova::$gc->localePlayer;
classLocale::$lang->lng_switch(sys_get_param_str('lang'));

if(!defined('DEBUG_INIT_SKIP_SECONDARY') || DEBUG_INIT_SKIP_SECONDARY !== true) {
  require_once "init_secondary.php";
}
