<?php

use Common\Tools\VersionCheckerDeprecated;
use Core\Autoloader;
use \Core\SnBootstrap;
use Fleet\TaskDispatchFleets;
use Player\playerTimeDiff;

// Защита от двойного инита
if(defined('INIT')) {
  return;
}

define('INIT', true);

// Замеряем начальные параметры
define('SN_TIME_MICRO', microtime(true));
define('SN_MEM_START', memory_get_usage());
define('SN_ROOT_PHYSICAL', str_replace('\\', '/', realpath(dirname(__DIR__))) . '/');
define('SN_ROOT_PHYSICAL_STR_LEN', strlen(SN_ROOT_PHYSICAL));
define('SN_ROOT_MODULES', SN_ROOT_PHYSICAL . 'modules/');

version_compare(PHP_VERSION, '5.6') < 0 ? die('FATAL ERROR: SuperNova REQUIRE PHP version >= 5.6') : false;

//define('DEBUG_UBE', true);
//define('DEBUG_FLYING_FLEETS', true);
//define('SN_DEBUG_LOG', true);
//define('SN_DEBUG_PDUMP_CALLER', true);

!defined('INSIDE') ? define('INSIDE', true) : false;
!defined('INSTALL') ? define('INSTALL', false) : false;
!defined('IN_PHPBB') ? define('IN_PHPBB', true) : false;

header('Content-type: text/html; charset=utf-8');
ob_start();
ini_set('error_reporting', E_ALL ^ E_NOTICE);

// Installing autoloader
require_once SN_ROOT_PHYSICAL . 'classes/Core/Autoloader.php';
Autoloader::register('classes/');
Autoloader::register('classes/UBE/');


require_once SN_ROOT_PHYSICAL . 'includes/constants/constants.php';

// Бенчмарк
SnBootstrap::install_benchmark();
// Loading functions - can't be inserted into function
require_once SN_ROOT_PHYSICAL . 'includes/db.php';
require_once(SN_ROOT_PHYSICAL . 'includes/general/general.php');
sn_sys_load_php_files(SN_ROOT_PHYSICAL . 'includes/functions/', PHP_EX);

SN::loadFileSettings();
SN::init_global_objects();

// AFTER init global objects 'cause vars.php uses some from config
require_once(SN_ROOT_PHYSICAL . 'includes/vars.php');

// Some time required to start from cold cache
set_time_limit(60); // TODO - Optimize

// Отладка
// define('BE_DEBUG', true); // Отладка боевого движка
SnBootstrap::init_debug_state();

SnBootstrap::performUpdate(SN::$config);

// init constants from db
// Moved from SnBootstrap for phpStorm autocomplete
// TODO - Should be removed someday - there should NOT be constants that depends on configuration!
define('SN_COOKIE', (SN::$config->COOKIE_NAME ? SN::$config->COOKIE_NAME : 'SuperNova') . (defined('SN_GOOGLE') ? '_G' : ''));
define('SN_COOKIE_I', SN_COOKIE . AUTH_COOKIE_IMPERSONATE_SUFFIX);
define('SN_COOKIE_D', SN_COOKIE . '_D');
define('SN_COOKIE_T', SN_COOKIE . '_T'); // Time measure cookie
define('SN_COOKIE_F', SN_COOKIE . '_F'); // Font size cookie
define('SN_COOKIE_U', SN_COOKIE . '_U'); // Current user cookie aka user ID
define('SN_COOKIE_U_I', SN_COOKIE_U . AUTH_COOKIE_IMPERSONATE_SUFFIX); // Current impersonator user cookie aka impersonator user ID
define('SN_COOKIE_WEBP', SN_COOKIE . '_WEBP'); // WebP support cookie

define('DEFAULT_SKIN_NAME', 'EpicBlue');
define('DEFAULT_SKINPATH', SN::$config->game_default_skin ? SN::$config->game_default_skin : 'skins/' . DEFAULT_SKIN_NAME . '/');

define('DEFAULT_LANG', SN::$config->game_default_language ? SN::$config->game_default_language : 'ru');

define('FMT_DATE', SN::$config->int_format_date ? SN::$config->int_format_date : 'd.m.Y');
define('FMT_TIME', SN::$config->int_format_time ? SN::$config->int_format_time : 'H:i:s');
define('FMT_DATE_TIME', FMT_DATE . ' ' . FMT_TIME);


/**
 * @var classCache  $sn_cache
 * @var classConfig $config
 * @var debug       $debug
 */
global $sn_cache, $config, $auth, $debug, $lang;


global $sn_page_name;
empty($sn_page_name) ? $sn_page_name = INITIAL_PAGE : false;
global $template_result;
$template_result = ['.' => ['result' => []]];

SN::$lang = $lang = new classLocale(SN::$config->server_locale_log_usage);

global $sn_data, $sn_mvc;

// Подключаем все модули
// По нормальным делам тут надо подключать манифесты
// И читать конфиги - вдруг модуль отключен?
// Конфиг - часть манифеста?

// TODO
// Здесь - потому что core_auth модуль лежит в другом каталоге и его нужно инициализировать отдельно
// И надо инициализировать после загрузки других модулей. Когда-то это казалось отличной идеей, бля...
SN::$auth = new \core_auth();
SN::$gc->modules->registerModule(SN::$auth->manifest['name'], SN::$auth);
//SN::$gc->modules->registerModule(core_auth::$main_provider->manifest['name'], core_auth::$main_provider);

SN::$gc->modules->loadModules(SN_ROOT_MODULES);
SN::$gc->modules->initModules();


// Подключаем дефолтную страницу
// По нормальным делам её надо подключать в порядке загрузки обработчиков
// Сейчас мы делаем это здесь только для того, что бы содержание дефолтной страницы оказалось вверху. Что не факт, что нужно всегда
// Но нужно, пока у нас есть не MVC-страницы
$sn_page_data      = $sn_mvc['pages'][$sn_page_name];
$sn_page_name_file = 'includes/pages/' . $sn_page_data['filename'] . DOT_PHP_EX;
if($sn_page_name) {
  // Merging page options to global option pull
  if(is_array($sn_page_data['options'])) {
    SN::$options = array_merge(SN::$options, $sn_page_data['options']);
  }

  if(isset($sn_page_data) && file_exists($sn_page_name_file)) {
    require_once($sn_page_name_file);
  }
}

if((defined('IN_AJAX') && IN_AJAX === true) || (defined('IN_ADMIN') && IN_ADMIN === true) || (!empty(SN::$options[PAGE_OPTION_ADMIN]))) {
  SN::$options[PAGE_OPTION_FLEET_UPDATE_SKIP] = true;
}


// А теперь проверяем - поддерживают ли у нас загруженный код такую страницу
// TODO - костыль, что бы работали старые модули. Убрать!
if(is_array($sn_data['pages'])) {
  $sn_mvc['pages'] = array_merge($sn_mvc['pages'], $sn_data['pages']);
}
if(!isset($sn_mvc['pages'][$sn_page_name])) {
  $sn_page_name = '';
}

$lang->lng_switch(sys_get_param_str('lang'));


if(SN::$config->server_updater_check_auto && SN::$config->server_updater_check_last + SN::$config->server_updater_check_period <= SN_TIME_NOW) {
  VersionCheckerDeprecated::performCheckVersion();
}

SN::$gc->watchdog->register(new TaskDispatchFleets(), TaskDispatchFleets::class);
SN::$gc->worker->registerWorker('dispatchFleets', function () {
  \Core\Worker::detachIncomingRequest();

  $result = SN::$gc->fleetDispatcher->flt_flying_fleet_handler();

  return ['message' => 'Fleets dispatched', 'code' => $result];
});

// TODO Check URL timestamp when checking signature
if (INITIAL_PAGE === 'worker' && SN::$gc->request->url->isSigned()) {
  if (!defined('IN_AJAX')) {
    define('IN_AJAX', true);
  }

  $result = [];

  if (!empty($mode = sys_get_param_str('mode'))) {
    $result = SN::$gc->worker->$mode();
  }

  die(json_encode($result));
}

if(SN::$config->user_birthday_gift && SN_TIME_NOW - SN::$config->user_birthday_celebrate > PERIOD_DAY) {
  require_once(SN_ROOT_PHYSICAL . 'includes/includes/user_birthday_celebrate.php');
  sn_user_birthday_celebrate();
}

if(!SN::$config->var_online_user_count || SN::$config->var_online_user_time + SN::$config->game_users_update_online < SN_TIME_NOW) {
  dbUpdateUsersCount(db_user_count());
  dbUpdateUsersOnline(db_user_count(true));
  SN::$config->pass()->var_online_user_time = SN_TIME_NOW;
  if(SN::$config->server_log_online) {
    /** @noinspection SqlResolve */
    doquery("INSERT IGNORE INTO `{{log_users_online}}` SET online_count = " . SN::$config->var_online_user_count . ";");
  }
}




global $user;
$result = SN::$auth->login();

global $account_logged_in;
$account_logged_in = !empty(SN::$auth->account) && $result[F_LOGIN_STATUS] == LOGIN_SUCCESS;

$user = !empty($result[F_USER]) ? $result[F_USER] : false;

unset($result[F_USER]);
$template_result += $result;
unset($result);
// В этой точке пользователь либо авторизирован - и есть его запись - либо пользователя гарантированно нет в базе

$template_result[F_ACCOUNT_IS_AUTHORIZED] = $sys_user_logged_in = !empty($user) && isset($user['id']) && $user['id'];

if(!empty($user['id'])) {
  SN::$user_options->user_change($user['id']);
}

// Если сообщение пустое - заполняем его по коду
$template_result[F_LOGIN_MESSAGE] =
  !empty($template_result[F_LOGIN_MESSAGE])
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
  pdump('Отключи отладку перед продакшном!');
}


// Это уже переключаемся на пользовательский язык с откатом до языка в параметрах запроса
$lang->lng_switch(sys_get_param_str('lang'));

SN::$config->db_loadItem('game_disable') == GAME_DISABLE_INSTALL
  ? define('INSTALL_MODE', GAME_DISABLE_INSTALL)
  : false;

// TODO - to scheduler
StatUpdateLauncher::unlock();

if($template_result[F_GAME_DISABLE] = SN::$config->game_disable) {
  $template_result[F_GAME_DISABLE_REASON] = HelperString::nl2br(
    SN::$config->game_disable == GAME_DISABLE_REASON
      ? SN::$config->game_disable_reason
      : $lang['sys_game_disable_reason'][SN::$config->game_disable]
  );

  // For API - just skipping all checks
  // TODO: That is ideologically wrong and should be redone
  if(defined('IN_API')) {
    return;
  }

  // Actions for install mode
  if(defined('INSTALL_MODE') && INSTALL_MODE) {
    // Handling log out - should work even in install mode
    if(strtolower(INITIAL_PAGE) === 'logout') {
      SN::$auth->logout(true);
      die();
    }

    // If user not logged in AND we are not on login page - redirect user there
    if(!$sys_user_logged_in && !defined('LOGIN_LOGOUT')) {
      header('Location: login.php');
      die();
    }

    // If user is type of admin AND in user pages - redirecting him to admin interface
    // You really shouldn't mess in user interface until game not configured!
    if($user['authlevel'] >= 1 && !defined('IN_ADMIN')) {
      header('Location: ' . SN_ROOT_VIRTUAL_PARENT . 'admin/overview.php');
      die();
    }
  }

  if(
    ($user['authlevel'] < 1 || !(defined('IN_ADMIN') && IN_ADMIN))
    &&
    !(defined('INSTALL_MODE') && defined('LOGIN_LOGOUT'))
    &&
    empty(SN::$options[PAGE_OPTION_ADMIN])
  ) {
    SnTemplate::messageBox($template_result[F_GAME_DISABLE_REASON], SN::$config->game_name, '', 5, false);
    ob_end_flush();
    die();
  }
}

// TODO ban
// TODO $skip_ban_check
global $skip_ban_check;
if($template_result[F_BANNED_STATUS] && !$skip_ban_check) {
  if(defined('IN_API')) {
    return;
  }

  $bantime = date(FMT_DATE_TIME, $template_result[F_BANNED_STATUS]);
  // TODO: Add ban reason. Add vacation time. Add message window
  SnTemplate::messageBox("{$lang['sys_banned_msg']} {$bantime}", $lang['ban_title']);
  die("{$lang['sys_banned_msg']} {$bantime}");
}

// TODO !!! Просто $allow_anonymous используется в платежных модулях !!!
$allow_anonymous = $allow_anonymous || (isset($sn_page_data['allow_anonymous']) && $sn_page_data['allow_anonymous']);


if($sys_user_logged_in && INITIAL_PAGE == 'login') {
  sys_redirect(SN_ROOT_VIRTUAL . 'overview.php');
} elseif($account_logged_in && !$sys_user_logged_in) { // empty(core_auth::$user['id'])
} elseif(!$allow_anonymous && !$sys_user_logged_in) {
  sys_redirect(SN_ROOT_VIRTUAL . 'login.php');
}

playerTimeDiff::defineTimeDiff();

// TODO: ...to controller
!empty($user) && sys_get_param_id('only_hide_news') ? die(nws_mark_read($user)) : false;
!empty($user) && sys_get_param_id('survey_vote') ? die(survey_vote($user)) : false;

!empty($sn_mvc['i18n']['']) ? lng_load_i18n($sn_mvc['i18n']['']) : false;
$sn_page_name && !empty($sn_mvc['i18n'][$sn_page_name]) ? lng_load_i18n($sn_mvc['i18n'][$sn_page_name]) : false;

execute_hooks($sn_mvc['model'][''], $template, 'model', '');

SN::$gc->watchdog->execute();

//ini_set('error_reporting', E_ALL);

//SN::$gc->watchdog->checkConfigTimeDiff(
//  'fleet_update_last',
//  SN::$config->fleet_update_interval,
//  // Promise
//  function () {SN::$gc->fleetDispatcher->dispatch();},
//  classConfig::DATE_TYPE_SQL_STRING,
//  false
//);

StatUpdateLauncher::scheduler_process();
