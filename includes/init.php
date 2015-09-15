<?php

// Защита от двойного инита
if(defined('INIT')) {
  return;
}

// Замеряем начальные параметры
define('SN_TIME_MICRO', microtime(true));
define('SN_MEM_START', memory_get_usage());
define('INIT', true);

// Бенчмарк
register_shutdown_function(function() {
  if(defined('IN_AJAX')) {
    return;
  }

  global $user, $lang;

  print('<hr><div class="benchmark">Benchmark ' . (microtime(true) - SN_TIME_MICRO) . 's, memory: ' . number_format(memory_get_usage() - SN_MEM_START) . '</div>');
  if($user['authlevel'] >= 2 && file_exists(SN_ROOT_PHYSICAL . 'badqrys.txt') && @filesize(SN_ROOT_PHYSICAL . 'badqrys.txt') > 0) {
    echo '<a href="badqrys.txt" target="_blank" style="color:red">', $lang['ov_hack_alert'], '</a>';
  }
});

!defined('INSIDE') ? define('INSIDE', true) : false;
!defined('INSTALL') ? define('INSTALL', false) : false;
!defined('IN_PHPBB') ? define('IN_PHPBB', true) : false;

// Эти три строки должны быть В ЭТОМ ФАЙЛЕ, ПО ЭТОМУ ПУТИ и ПЕРЕД ЭТИМ ИНКЛЮДОМ!!!
$sn_root_physical = str_replace('\\', '/', __FILE__);
$sn_root_physical = str_replace('includes/init.php', '', $sn_root_physical);
define('SN_ROOT_PHYSICAL', $sn_root_physical);

//version_compare(PHP_VERSION, '5.3.1', '==') ? die('FATAL ERROR: you using PHP 5.3.1. Due to bug in PHP 5.3.1 SuperNova is incompatible with this version. Please upgrade or downgrade your PHP. Read more <a href="https://bugs.php.net/bug.php?id=50394">here</a>.') : false;
version_compare(PHP_VERSION, '5.3.2') < 0 ? die('FATAL ERROR: SuperNova REQUIRE PHP version > 5.3.2') : false;

require_once('constants.php');

require_once('classes/supernova.php');

classSupernova::init_0_prepare();
classSupernova::init_1_constants();
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
$debug = new debug();
classSupernova::debug_set_handler($debug);

require_once(SN_ROOT_PHYSICAL . "includes/db" . DOT_PHP_EX);
require_once('classes/db_mysql_v4.php');
require_once('classes/db_mysql_v5.php');
require_once('classes/db_mysql.php');
classSupernova::init_main_db(new db_mysql());


require_once('classes/cache.php');
require_once('classes/locale.php');
require_once('classes/template.php');
require_once('classes/functions_template.php');
require_once('classes/module.php');
require_once('classes/RequestInfo.php');
require_once('classes/Account.php');
require_once('classes/auth.php');
// require_once('auth_provider.php');
require_once('classes/auth_local.php');
require_once('classes/sn_module_payment.php');
require_once('classes/user_options.php');
require_once(SN_ROOT_PHYSICAL . "includes/init/init_functions" . DOT_PHP_EX);

/**
 * @var classConfig $config
 */
global $supernova, $sn_cache, $config;

classSupernova::init_global_objects();

// Отладка
// define('BE_DEBUG', true); // Отладка боевого движка
classSupernova::init_debug_state();

require_once(SN_ROOT_PHYSICAL . "includes/vars" . DOT_PHP_EX);
require_once(SN_ROOT_PHYSICAL . "includes/general" . DOT_PHP_EX);

init_update($config);

// Initializing constants
$sn_page_name_original = isset($_GET['page'])
  ? trim(strip_tags($_GET['page']))
  : str_replace(DOT_PHP_EX, '', str_replace(SN_ROOT_RELATIVE, '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])));
define('INITIAL_PAGE', $sn_page_name_original);
define('SN_COOKIE'        , ($config->COOKIE_NAME ? $config->COOKIE_NAME : 'SuperNova') . (defined('SN_GOOGLE') ? '_G' : ''));
define('SN_COOKIE_I'      , SN_COOKIE . '_I');
define('SN_COOKIE_D'      , SN_COOKIE . '_D');
define('SN_COOKIE_T'      , SN_COOKIE . '_T'); // Time measure cookie
define('SN_COOKIE_F'      , SN_COOKIE . '_F'); // Font size cookie
define('SN_COOKIE_U'      , SN_COOKIE . '_U'); // Current user cookie aka user ID
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
// Здесь - потому что auth модуль лежит в другом каталоге и его нужно инициализировать отдельно
// TODO - переработать этот костыль
new auth();
// new auth_local();
// pdump($sn_module);

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
//  100000 - default load order
//  999999 - core_ship_constructor
//  2000000000 - that requires that all possible modules loaded already
//  2000100000 - game_skirmish

// Генерируем список требуемых модулей
$load_order = array();
$sn_req = array();

foreach($sn_module as $loaded_module_name => $module_data) {
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
}
while($prev_order != $load_order);

asort($load_order);

// Инициализируем модули
// По нормальным делам это должна быть загрузка модулей и лишь затем инициализация - что бы минимизировать размер процесса в памяти
foreach($load_order as $loaded_module_name => $load_order_order) {
  if($load_order_order >= 0) {
    $sn_module[$loaded_module_name]->check_status();
    if(!$sn_module[$loaded_module_name]->manifest['active']) {
      unset($sn_module[$loaded_module_name]);
      continue;
    }

    $sn_module[$loaded_module_name]->initialize();
    $sn_module_list[$sn_module[$loaded_module_name]->manifest['package']][$loaded_module_name] = &$sn_module[$loaded_module_name];
  } else {
    unset($sn_module[$loaded_module_name]);
  }
}

// Скрипач не нужон
unset($load_order);
unset($sn_req);

// А теперь проверяем - поддерживают ли у нас загруженный код такую страницу
if(!isset($sn_data['pages'][$sn_page_name])) {
  $sn_page_name = '';
}



//pdump(array_keys($sn_module_list));
//pdump(array_keys($sn_module_list['core']));
//pdump(array_keys($sn_module_list['auth']));
//die();


// classSupernova::$db->sn_db_connect(); // Не нужно. Делаем раньше

global $lang;
$lang = new classLocale(DEFAULT_LANG, $config->server_locale_log_usage);
$lang->lng_switch(sys_get_param_str('lang'));


if($config->server_updater_check_auto && $config->server_updater_check_last + $config->server_updater_check_period <= SN_TIME_NOW) {
  include(SN_ROOT_PHYSICAL . 'ajax_version_check' . DOT_PHP_EX);
}

if($config->user_birthday_gift && SN_TIME_NOW - $config->user_birthday_celebrate > PERIOD_DAY) {
  require_once(SN_ROOT_PHYSICAL . "includes/includes/user_birthday_celebrate" . DOT_PHP_EX);
  sn_user_birthday_celebrate();
}

if(!$config->var_online_user_count || $config->var_online_user_time + 30 < SN_TIME_NOW) {
  $config->db_saveItem('var_online_user_count', db_user_count(true));
  $config->db_saveItem('var_online_user_time', SN_TIME_NOW);
  if($config->server_log_online) {
    doquery("INSERT IGNORE INTO {{log_users_online}} SET online_count = {$config->var_online_user_count};");
  }
}


global $skip_fleet_update;
$skip_fleet_update = $skip_fleet_update || $supernova->options['fleet_update_skip'] || defined('IN_ADMIN');
if(!$skip_fleet_update && SN_TIME_NOW - strtotime($config->fleet_update_last) > $config->fleet_update_interval) {
  require_once(SN_ROOT_PHYSICAL . "includes/includes/flt_flying_fleet_handler2" . DOT_PHP_EX);
  flt_flying_fleet_handler($skip_fleet_update);
}


//pdump($sn_module);die();

global $user;
$result = auth::login();


if(!empty(auth::$providers_authorised) && empty(auth::$user['id'])) {
  die('{Тут должна быть ваша реклама. Точнее - ввод имени игрока}');
}

//pdump($result[F_LOGIN_STATUS], LOGIN_SUCCESS);
// die();

// pdump($result);die();

$user = !empty($result[F_USER]) ? $result[F_USER] : false;

unset($result[F_USER]);
$template_result += $result;
unset($result);
// В этой точке пользователь либо авторизирован - и есть его запись - либо пользователя гарантированно нет в базе

$template_result[F_ACCOUNT_IS_AUTHORIZED] = $sys_user_logged_in = !empty($user) && isset($user['id']) && $user['id'];
//pdump($template_result[F_ACCOUNT_IS_AUTHORIZED]);die();

if(!empty($user['id'])) {
  classSupernova::$user_options->user_change($user['id']);
}

// Если сообщение пустое - заполняем его по коду
$template_result[F_LOGIN_MESSAGE] =
  isset($template_result[F_LOGIN_MESSAGE]) && $template_result[F_LOGIN_MESSAGE]
    ? $template_result[F_LOGIN_MESSAGE]
    : ($template_result[F_LOGIN_STATUS] != LOGIN_UNDEFINED
        ? $lang['sys_login_messages'][$template_result[F_LOGIN_STATUS]]
        : false
      );

if($template_result[F_LOGIN_STATUS] == LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS) {
  $prohibited_characters = array_map(function($value) {
    return "'" . htmlentities($value, ENT_QUOTES, 'UTF-8') . "'";
  }, str_split(LOGIN_REGISTER_CHARACTERS_PROHIBITED));
  $template_result[F_LOGIN_MESSAGE] .= implode(', ', $prohibited_characters);
}


if(defined('DEBUG_AUTH') && DEBUG_AUTH && !defined('IN_AJAX')) {
  pdump("Отключи отладку перед продакшном!");
}


// Это уже переключаемся на пользовательский язык с откатом до языка в параметрах запроса
$lang->lng_switch(sys_get_param_str('lang'));
global $dpath;
$dpath = $user["dpath"] ? $user["dpath"] : DEFAULT_SKINPATH;

$config->db_loadItem('game_disable') == GAME_DISABLE_INSTALL
  ? define('INSTALL_MODE', GAME_DISABLE_INSTALL)
  : false;

if($template_result[F_GAME_DISABLE] = $config->game_disable) {
  $template_result[F_GAME_DISABLE_REASON] = sys_bbcodeParse(
    $config->game_disable == GAME_DISABLE_REASON
      ? $config->game_disable_reason
      : $lang['sys_game_disable_reason'][$config->game_disable]
  );
  if(defined('IN_API')) {
    return;
  }

  if(
    ($user['authlevel'] < 1 || !(defined('IN_ADMIN') && IN_ADMIN))
    &&
    !(defined('INSTALL_MODE') && defined('LOGIN_LOGOUT'))
  ) {
    message($template_result[F_GAME_DISABLE_REASON], $config->game_name);
    ob_end_flush();
    die();
  }
}

// TODO ban
// TODO $skip_ban_check
if($template_result[F_BANNED_STATUS] && !$skip_ban_check) {
  if(defined('IN_API')) {
    return;
  }

  $bantime = date(FMT_DATE_TIME, $template_result[F_BANNED_STATUS]);
  // TODO: Add ban reason. Add vacation time. Add message window
  // sn_sys_logout(false, true);
  // auth::logout(false, true);
  message("{$lang['sys_banned_msg']} {$bantime}", $lang['ban_title']);
  die("{$lang['sys_banned_msg']} {$bantime}");
}

// TODO !!! Просто $allow_anonymous используется в платежных модулях !!!
$allow_anonymous = $allow_anonymous || (isset($sn_page_data['allow_anonymous']) && $sn_page_data['allow_anonymous']);

// pdump($allow_anonymous, '$allow_anonymous');
// pdump($sys_user_logged_in, '$sys_user_logged_in');

if($sys_user_logged_in && INITIAL_PAGE == 'login') {
  sys_redirect(SN_ROOT_VIRTUAL . 'overview.php');
}

if(!$allow_anonymous && !$sys_user_logged_in) {
// die('Редирект на фход');
  // sn_setcookie(SN_COOKIE, '', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
  sys_redirect(SN_ROOT_VIRTUAL . 'login.php');
}

$user_time_diff = user_time_diff_get();
global $time_diff;
define('SN_CLIENT_TIME_DIFF', $time_diff = $user_time_diff[PLAYER_OPTION_TIME_DIFF] + $user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET]);
define('SN_CLIENT_TIME_LOCAL', SN_TIME_NOW + SN_CLIENT_TIME_DIFF);

!empty($user) && sys_get_param_id('only_hide_news') ? die(nws_mark_read($user)) : false;
!empty($user) && sys_get_param_id('survey_vote') ? die(survey_vote($user)) : false;

!empty($sn_mvc['i18n']['']) ? lng_load_i18n($sn_mvc['i18n']['']) : false;
$sn_page_name && !empty($sn_mvc['i18n'][$sn_page_name]) ? lng_load_i18n($sn_mvc['i18n'][$sn_page_name]) : false;

execute_hooks($sn_mvc['model'][''], $template);
