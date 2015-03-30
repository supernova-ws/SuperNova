<?php

// Защита от двойного инита
if(defined('INIT')) {
  return;
}
define('INIT', true);

// Замеряем начальные параметры
global $microtime, $time_now;
define('SN_TIME_MICRO', $microtime = microtime(true));
define('SN_TIME_NOW', $time_now = intval(SN_TIME_MICRO));
define('SN_TIME_ZONE_OFFSET', date('Z'));
define('SN_MEM_START', memory_get_usage());

define('FMT_DATE_TIME_SQL', 'Y-m-d H:i:s');
define('SN_TIME_SQL', date(FMT_DATE_TIME_SQL, SN_TIME_NOW));

version_compare(PHP_VERSION, '5.3.1', '==') ? die('FATAL ERROR: you using PHP 5.3.1. Due to bug in PHP 5.3.1 SuperNova is incompatible with this version. Please upgrade or downgrade your PHP. Read more <a href="https://bugs.php.net/bug.php?id=50394">here</a>.') : false;

// Бенчмарк
register_shutdown_function(function() {
  if(!defined('IN_AJAX')) {
    print('<hr><div class="benchmark">Benchmark ' . (microtime(true) - SN_TIME_MICRO) . 's, memory: ' . number_format(memory_get_usage() - SN_MEM_START) . '</div>');
  }
});

// Отладка
// define('BE_DEBUG', true); // Отладка боевого движка
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

//if($_SERVER['REMOTE_ADDR'] == "109.86.195.192") {
//} else {
//  // print('Производится обновление сервера. Ждите...');die();
//}
strpos(strtolower($_SERVER['SERVER_NAME']), 'google.') !== false ? define('SN_GOOGLE', true) : false;


!defined('INSIDE') ? define('INSIDE', true) : false;
!defined('INSTALL') ? define('INSTALL', false) : false;
!defined('IN_PHPBB') ? define('IN_PHPBB', true) : false;

// Отключаем magic_quotes
ini_get('magic_quotes_sybase') ? die('SN is incompatible with \'magic_quotes_sybase\' turned on. Disable it in php.ini or .htaccess...') : false;
if(@get_magic_quotes_gpc()) {
  $gpcr = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
  array_walk_recursive($gpcr, function (&$value, $key) {
    $value = stripslashes($value);
  });
}
if(function_exists('set_magic_quotes_runtime')) {
  @set_magic_quotes_runtime(0);
  @ini_set('magic_quotes_runtime', 0);
  @ini_set('magic_quotes_sybase', 0);
}


header('Content-type: text/html; charset=utf-8');
ob_start();
ini_set('error_reporting', E_ALL ^ E_NOTICE);

$phpEx = strpos($phpEx = substr(strrchr(__FILE__, '.'), 1), '/') === false ? $phpEx : '';
define('PHP_EX', $phpEx); // PHP extension on this server
define('DOT_PHP_EX', '.' . PHP_EX); // PHP extension on this server

$sn_root_physical = str_replace('\\', '/', __FILE__);
$sn_root_physical = str_replace('includes/init.php', '', $sn_root_physical);
define('SN_ROOT_PHYSICAL', $sn_root_physical);

$sn_root_relative = str_replace('\\', '/', getcwd());
$sn_root_relative .= $sn_root_relative[strlen($sn_root_relative) - 1] == '/' ? '' : '/';
$sn_root_relative = str_replace(SN_ROOT_PHYSICAL, '', $sn_root_relative);
$sn_root_relative .= basename($_SERVER['SCRIPT_NAME']);
$sn_root_relative = str_replace($sn_root_relative, '', $_SERVER['SCRIPT_NAME']);
define('SN_ROOT_RELATIVE', $sn_root_relative);

define('SN_ROOT_VIRTUAL' , 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . SN_ROOT_RELATIVE);
define('SN_ROOT_VIRTUAL_PARENT' , str_replace('//google.', '//', SN_ROOT_VIRTUAL));


// Это нужно для работы PTL
global $phpbb_root_path;
$phpbb_root_path = SN_ROOT_PHYSICAL;

global $db_prefix, $db_name, $sn_secret_word, $user;

$user = array();

require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);
$db_prefix = $dbsettings['prefix'];
$db_name = $dbsettings['name'];
$sn_secret_word = $dbsettings['secretword'];
unset($dbsettings);

require_once(SN_ROOT_PHYSICAL . "includes/constants" . DOT_PHP_EX);

// required for db.php
// Initializing global 'debug' object
require_once(SN_ROOT_PHYSICAL . "includes/debug.class" . DOT_PHP_EX);
global $debug;
$debug = new debug();

require_once(SN_ROOT_PHYSICAL . "includes/classes/_classes" . DOT_PHP_EX);
require_once(SN_ROOT_PHYSICAL . "includes/db" . DOT_PHP_EX);
require_once(SN_ROOT_PHYSICAL . "includes/init/init_functions" . DOT_PHP_EX);

global $supernova;
$supernova = new classSupernova();

doquery("SET NAMES 'utf8';");

// Initializing global 'cacher' object
global $sn_cache;
$sn_cache = new classCache($db_prefix);
empty($sn_cache->tables) && sys_refresh_tablelist($db_prefix);
empty($sn_cache->tables) && die('DB error - cannot find any table. Halting...');

// Initializing global "config" object
$config = new classConfig($db_prefix);
//$config->db_saveItem('db_prefix', $db_prefix);
//$config->db_saveItem('secret_word', $sn_secret_word);
$config->db_prefix = $db_prefix;
$config->secret_word = $sn_secret_word;

if(defined('BE_DEBUG') || $config->debug) {
  @define('BE_DEBUG', true);
  @ini_set('display_errors', 1);
  @error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
} else {
  @define('BE_DEBUG', false);
  @ini_set('display_errors', 0);
}

require_once(SN_ROOT_PHYSICAL . "includes/vars" . DOT_PHP_EX);
require_once(SN_ROOT_PHYSICAL . "includes/general" . DOT_PHP_EX);

init_update($config);
unset($db_name);

// Initializing constants
$sn_page_name_original = isset($_GET['page'])
  ? trim(strip_tags($_GET['page']))
  : str_replace(DOT_PHP_EX, '', str_replace(SN_ROOT_RELATIVE, '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])));
define('INITIAL_PAGE', $sn_page_name_original);
define('SN_COOKIE'        , ($config->COOKIE_NAME ? $config->COOKIE_NAME : 'SuperNova') . (defined('SN_GOOGLE') ? '_G' : ''));
define('SN_COOKIE_I'      , SN_COOKIE . '_I');
define('SN_COOKIE_D'      , SN_COOKIE . '_D');
define('SN_COOKIE_T'      , SN_COOKIE . '_T'); // Time measure cookie
define('TEMPLATE_NAME'    , $config->game_default_template ? $config->game_default_template : 'OpenGame');
define('TEMPLATE_PATH'    , 'design/templates/' . TEMPLATE_NAME);
define('TEMPLATE_DIR'     , SN_ROOT_PHYSICAL . TEMPLATE_PATH);
define('DEFAULT_SKINPATH' , $config->game_default_skin ? $config->game_default_skin : 'skins/EpicBlue/');
define('DEFAULT_LANG'     , $config->game_default_language ? $config->game_default_language : 'ru');
define('FMT_DATE'         , $config->int_format_date ? $config->int_format_date : 'd.m.Y');
define('FMT_TIME'         , $config->int_format_time ? $config->int_format_time : 'H:i:s');
define('FMT_DATE_TIME'    , FMT_DATE . ' ' . FMT_TIME);

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

require_once(SN_ROOT_PHYSICAL . "includes/template" . DOT_PHP_EX);
$template_result = array('.' => array('result' => array()));

sn_sys_load_php_files(SN_ROOT_PHYSICAL . "includes/functions/", PHP_EX);


// Подключаем все модули
// По нормальным делам тут надо подключать манифесты
// И читать конфиги - вдруг модуль отключен?
// Конфиг - часть манифеста?
$sn_module = array();
$sn_module_list = array();
sn_sys_load_php_files(SN_ROOT_PHYSICAL . "modules/", PHP_EX, true);

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
//  $sn_page_data
/*
  if(basename($sn_page_data) == $sn_page_data)
  {
    require_once('includes/pages/' . $sn_page_data . '.' . $phpEx);
  }
*/
}

// load_order:
//  999999 - core_ship_constructor
//  2000000000 - that requires that all possible modules loaded already

// Генерируем список требуемых модулей
$load_order = array();
$sn_req = array();
foreach($sn_module as $loaded_module_name => $module_data) {
  $load_order[$loaded_module_name] = isset($module_data->manifest['load_order']) && !empty($module_data->manifest['load_order']) ? $module_data->manifest['load_order'] : 1;
  if(isset($module_data->manifest['require']) && !empty($module_data->manifest['require'])) {
    foreach($module_data->manifest['require'] as $require_name) {
      $sn_req[$loaded_module_name][$require_name] = 0;
    }
  }
}

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
}
while($prev_order != $load_order);

asort($load_order);

// Инициализируем модули
// По нормальным делам это должна быть загрузка модулей и лишь затем инициализация - что бы минимизировать размер процесса в памяти
foreach($load_order as $loaded_module_name => $load_order) {
  if($load_order < 0) {
    continue;
  }
  $sn_module[$loaded_module_name]->initialize();
  $sn_module_list[$sn_module[$loaded_module_name]->manifest['package']][$loaded_module_name] = &$sn_module[$loaded_module_name];
}

// Скрипач не нужон
unset($load_order);
unset($sn_req);

// А теперь проверяем - поддерживают ли у нас загруженный код такую страницу
if(!isset($sn_data['pages'][$sn_page_name])) {
  $sn_page_name = '';
}

sn_db_connect();

global $lang;
$lang = new classLocale(DEFAULT_LANG, $config->server_locale_log_usage);
$lang->lng_switch(sys_get_param_str('lang'));


if($config->server_updater_check_auto && $config->server_updater_check_last + $config->server_updater_check_period <= $time_now) {
  include(SN_ROOT_PHYSICAL . 'ajax_version_check' . DOT_PHP_EX);
}

if($config->user_birthday_gift && $time_now > $config->user_birthday_celebrate + PERIOD_DAY) {
  require_once(SN_ROOT_PHYSICAL . "includes/includes/user_birthday_celebrate" . DOT_PHP_EX);
  sn_user_birthday_celebrate();
}

if(!$config->var_online_user_count || $config->var_online_user_time + 30 < SN_TIME_NOW) {
  $config->db_saveItem('var_online_user_count', db_user_count(true));
  $config->db_saveItem('var_online_user_time', $time_now);
  if($config->server_log_online) {
    doquery("INSERT IGNORE INTO {{log_users_online}} SET online_count = {$config->var_online_user_count};");
  }
}

// pdump($skip_fleet_update, '$skip_fleet_update');
// pdump($supernova->options['fleet_update_skip'], '$supernova->options[fleet_update_skip]');
global $skip_fleet_update;
$skip_fleet_update = $skip_fleet_update || $supernova->options['fleet_update_skip'] || defined('IN_ADMIN');
if(!$skip_fleet_update && SN_TIME_NOW - strtotime($config->fleet_update_last) > $config->fleet_update_interval) {
  require_once(SN_ROOT_PHYSICAL . "includes/includes/flt_flying_fleet_handler2" . DOT_PHP_EX);
  flt_flying_fleet_handler($skip_fleet_update);
}

$result = sec_login();
$user = $result[F_LOGIN_USER];
unset($result[F_LOGIN_USER]);
$template_result += $result;
unset($result);
// В этой точке пользователь либо авторизирован - и есть его запись - либо пользователя гарантированно нет в базе

// Если сообщение пустое - заполняем его по коду
$template_result[F_LOGIN_MESSAGE] = isset($template_result[F_LOGIN_MESSAGE]) && $template_result[F_LOGIN_MESSAGE]
  ? $template_result[F_LOGIN_MESSAGE]
  : $lang['sys_login_messages'][$template_result[F_LOGIN_STATUS]];

// Это уже переключаемся на пользовательский язык с откатом до языка в параметрах запроса
$lang->lng_switch(sys_get_param_str('lang'));
global $dpath;
$dpath = $user["dpath"] ? $user["dpath"] : DEFAULT_SKINPATH;

$config->db_loadItem('game_disable') == GAME_DISABLE_INSTALL ? define('INSTALL_MODE', GAME_DISABLE_INSTALL) : false;

if($template_result[F_GAME_DISABLE] = $config->game_disable) {
  $template_result[F_GAME_DISABLE_REASON] = sys_bbcodeParse($config->game_disable == GAME_DISABLE_REASON ? $config->game_disable_reason : $lang['sys_game_disable_reason'][$config->game_disable]);
  if(defined('IN_API')) {
    return;
  }

  if(($user['authlevel'] < 1 || !(defined('IN_ADMIN') && IN_ADMIN)) && !(defined('INSTALL_MODE') && defined('LOGIN_LOGOUT'))) {
    message($template_result[F_GAME_DISABLE_REASON], $config->game_name);
    ob_end_flush();
    die();
  }
}

sec_login_change_state();

// TODO ban
if($template_result[F_BANNED_STATUS] && !$skip_ban_check) {
  if(defined('IN_API')) {
    return;
  }

  $bantime = date(FMT_DATE_TIME, $template_result[F_BANNED_STATUS]);
  // TODO: Add ban reason. Add vacation time. Add message window
  sn_sys_logout(false, true);
  message("{$lang['sys_banned_msg']} {$bantime}", $lang['ban_title']);
  die("{$lang['sys_banned_msg']} {$bantime}");
}

$template_result[F_USER_AUTHORIZED] = $sys_user_logged_in = !empty($user) && isset($user['id']) && $user['id'];

// !!! Просто $allow_anonymous используется в платежных модулях !!!
$allow_anonymous = $allow_anonymous || (isset($sn_page_data['allow_anonymous']) && $sn_page_data['allow_anonymous']);

if(!$allow_anonymous && !$sys_user_logged_in) {
  sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
  sys_redirect(SN_ROOT_VIRTUAL . 'login.php');
}


$user_time_diff = user_time_diff_get();
//defined('SN_CLIENT_TIME_DIFF_SECONDS') or define('SN_CLIENT_TIME_DIFF_SECONDS', $user_time_diff[PLAYER_OPTION_TIME_DIFF]);
//defined('SN_CLIENT_TIME_UTC_OFFSET') or define('SN_CLIENT_TIME_UTC_OFFSET', $user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET]);
//$time_diff = SN_CLIENT_TIME_DIFF_SECONDS + SN_CLIENT_TIME_UTC_OFFSET;
//defined('SN_CLIENT_TIME_DIFF') or define('SN_CLIENT_TIME_DIFF', $time_diff);
//defined('SN_CLIENT_TIME_LOCAL') or define('SN_CLIENT_TIME_LOCAL', SN_TIME_NOW + SN_CLIENT_TIME_DIFF);
global $time_diff;
define('SN_CLIENT_TIME_DIFF', $time_diff = $user_time_diff[PLAYER_OPTION_TIME_DIFF] + $user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET]);
define('SN_CLIENT_TIME_LOCAL', SN_TIME_NOW + SN_CLIENT_TIME_DIFF);

!empty($user) && sys_get_param_id('only_hide_news') ? die(nws_mark_read($user)) : false;
!empty($user) && sys_get_param_id('survey_vote') ? die(survey_vote($user)) : false;

lng_load_i18n($sn_mvc['i18n'][$sn_page_name]);
execute_hooks($sn_mvc['model'][''], $template);
