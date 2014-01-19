<?php
//define('BE_DEBUG', true);

define('SN_TIME_MICRO', $microtime = microtime(true));
// define('SN_TIME_NOW', $time_now = time());
define('SN_TIME_NOW', $time_now = intval($microtime));

define('SN_MEM_START', memory_get_usage());

if(defined('INIT'))
{
  return;
}

if(version_compare(PHP_VERSION, '5.3.1', '=='))
{
  die('FATAL ERROR: you using PHP 5.3.1. Due to bug in PHP 5.3.1 SuperNova is incompatible with this version. Please upgrade or downgrade your PHP. Read more <a href="https://bugs.php.net/bug.php?id=50394">here</a>.');
}

define('INIT', true);

if(!defined('INSIDE'))
{
  define('INSIDE', true);
}

if(!defined('INSTALL'))
{
  define('INSTALL', false);
}

if(!defined('IN_PHPBB'))
{
  define('IN_PHPBB', true);
}

if(ini_get('magic_quotes_sybase'))
{
  die('SN is incompatible with \'magic_quotes_sybase\' turned on. Disable it in php.ini or .htaccess...');
}

if(@get_magic_quotes_gpc())
{
  function sn_sys_unmagic_quotes(&$value, $key)
  {
    $value = stripslashes($value);
  }
  $gpcr = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
  array_walk_recursive($gpcr, 'sn_sys_unmagic_quotes');
}

if(function_exists('set_magic_quotes_runtime'))
{
  @set_magic_quotes_runtime(0);
  @ini_set('magic_quotes_runtime', 0);
  @ini_set('magic_quotes_sybase', 0);
}

header('Content-type: text/html; charset=utf-8');

ob_start();

ini_set('error_reporting', E_ALL ^ E_NOTICE);

if($_SERVER['SERVER_NAME'] == 'localhost')
{
  define('BE_DEBUG', true);
}


$supernova = new stdClass();
$supernova->options = array();

$phpEx = strpos($phpEx = substr(strrchr(__FILE__, '.'), 1), '/') === false ? $phpEx : '';
/*
$server_document_root = str_replace("\\", '/', realpath($_SERVER['DOCUMENT_ROOT'])) . '/';
$sn_root_relative = str_replace(array('//', '//'), '/', '/' . str_replace(array('\\', $server_document_root, 'includes/init.php'), array('/', '', ''), __FILE__));
$sn_root_physical = str_replace(array('//', '//'), '/', $server_document_root . $sn_root_relative);
*/


$sn_root_physical = str_replace('\\', '/', __FILE__);
$sn_root_physical = str_replace('includes/init.php', '', $sn_root_physical);

$sn_root_relative = str_replace('\\', '/', getcwd());
$sn_root_relative .= $sn_root_relative[strlen($sn_root_relative) - 1] == '/' ? '' : '/';
$sn_root_relative = str_replace($sn_root_physical, '', $sn_root_relative);
$sn_root_relative .= basename($_SERVER['SCRIPT_NAME']);
$sn_root_relative = str_replace($sn_root_relative, '', $_SERVER['SCRIPT_NAME']);

$phpbb_root_path  = $sn_root_physical;
define('SN_ROOT_RELATIVE', $sn_root_relative);
define('SN_ROOT_PHYSICAL', $sn_root_physical);
define('SN_ROOT_VIRTUAL' , 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $sn_root_relative);
define('PHP_EX', $phpEx); // PHP extension on this server

$user          = array();
$IsUserChecked = false;

require("{$sn_root_physical}config.{$phpEx}");
$db_prefix = $dbsettings['prefix'];
$db_name = $dbsettings['name'];
$sn_secret_word = $dbsettings['secretword'];
unset($dbsettings);

require_once("{$sn_root_physical}includes/constants.{$phpEx}");

// required for db.php
require_once("{$sn_root_physical}includes/debug.class.{$phpEx}");
require_once("{$sn_root_physical}includes/db.{$phpEx}");
// Initializing global 'debug' object
$debug = new debug();

//$dbms = 'mysql';
//require_once("{$sn_root_physical}includes/db/{$dbms}.{$phpEx}");
// $db      = new $sql_db();

sn_sys_load_php_files("{$sn_root_physical}includes/classes/", $phpEx);

// Initializing global 'cacher' object
$sn_cache = new classCache($db_prefix);
if(!$sn_cache->tables)
{
  sys_refresh_tablelist($db_prefix);
}

if(empty($sn_cache->tables))
{
  print('DB error - cannot find any table. Halting...');
  die();
}

// Initializing global "config" object
$config = new classConfig($db_prefix);
$config->db_saveItem('db_prefix', $db_prefix);
$config->db_saveItem('secret_word', $sn_secret_word);

if(defined('BE_DEBUG') || $config->debug)
{
  @define('BE_DEBUG', true);
  @ini_set('display_errors', 1);
  @error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
}
else
{
  @define('BE_DEBUG', false);
  @ini_set('display_errors', 0);
}

require_once("{$sn_root_physical}includes/vars.{$phpEx}");
// Now including all functions
require_once("{$sn_root_physical}includes/general.{$phpEx}");

$update_file = "{$sn_root_physical}includes/update.{$phpEx}";
if(file_exists($update_file))
{
  if(filemtime($update_file) > $config->db_loadItem('var_db_update') || $config->db_loadItem('db_version') < DB_VERSION)
  {
    if(defined('IN_ADMIN'))
    {
      if($time_now >= $config->var_db_update_end)
      {
        $config->db_saveItem('var_db_update_end', $time_now + $config->upd_lock_time);

        require_once($update_file);
        sys_refresh_tablelist($db_prefix);

        $time_now = time();
        $config->db_saveItem('var_db_update', $time_now);
        $config->db_saveItem('var_db_update_end', $time_now);
      }
      elseif(filemtime($update_file) > $config->var_db_update)
      {
        $timeout = $config->var_db_update_end - $time_now;
        die("Обновляется база данных. Рассчетное время окончания - {$timeout} секунд (время обновления может увеличиваться). Пожалуйста, подождите...<br>Obnovljaetsja baza dannyh. Rasschetnoe vremya okonchanija - {$timeout} secund. Pozhalujsta, podozhdute...<br>Database update in progress. Estimated update time {$timeout} seconds (can increase depending on update process). Please wait...");
      }
    }
    else
    {
      die('Происходит обновление сервера - пожалуйста, подождите...<br>Proishodit obnovlenie servera - pozhalujsta, podozhdute...<br>Server upgrading now - please wait...<br /><a href="admin/overview.php">Admin link</a>');
    }
  }
}
unset($db_name);

// Initializing constants
define('SN_COOKIE'        , $config->COOKIE_NAME ? $config->COOKIE_NAME : 'SuperNova');
define('SN_COOKIE_I'      , SN_COOKIE . '_I');
define('TEMPLATE_NAME'    , $config->game_default_template ? $config->game_default_template : 'OpenGame');
define('TEMPLATE_PATH'    , 'design/templates/' . TEMPLATE_NAME);
define('TEMPLATE_DIR'     , SN_ROOT_PHYSICAL . TEMPLATE_PATH);
define('DEFAULT_SKINPATH' , $config->game_default_skin ? $config->game_default_skin : 'skins/EpicBlue/');
define('DEFAULT_LANG'     , $config->game_default_language ? $config->game_default_language : 'ru');
define('FMT_DATE'         , $config->int_format_date ? $config->int_format_date : 'd.m.Y');
define('FMT_TIME'         , $config->int_format_time ? $config->int_format_time : 'H:i:s');
define('FMT_DATE_TIME'    , FMT_DATE . ' ' . FMT_TIME);

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

require_once("{$sn_root_physical}includes/template.{$phpEx}");
sn_sys_load_php_files("{$sn_root_physical}includes/functions/", $phpEx);

$template_result = array('.' => array());
$sn_page_name = isset($_GET['page']) ? trim(strip_tags($_GET['page'])) : '';

// Подключаем все модули
// По нормальным делам тут надо подключать манифесты
// И читать конфиги - вдруг модуль отключен?
// Конфиг - часть манифеста?
$sn_module = array();
$sn_module_list = array();
sn_sys_load_php_files("{$sn_root_physical}modules/", $phpEx, true);

// Подключаем дефолтную страницу
// По нормальным делам её надо подключать в порядке загрузки обработчиков
// Сейчас мы делаем это здесь только для того, что бы содержание дефолтной страницы оказалось вверху. Что не факт, что нужно всегда
// Но нужно, пока у нас есть не MVC-страницы
$sn_page_data = $sn_data['pages'][$sn_page_name];
$sn_page_name_file = 'includes/pages/' . $sn_page_data['filename'] . '.' . $phpEx;
if($sn_page_name && isset($sn_page_data) && file_exists($sn_page_name_file))
{
  require_once($sn_page_name_file);
  if(is_array($sn_page_data['options']))
  {
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

// Генерируем список требуемых модулей
$load_order = array();
$sn_req = array();
foreach($sn_module as $loaded_module_name => $module_data)
{
  $load_order[$loaded_module_name] = isset($module_data->manifest['load_order']) && !empty($module_data->manifest['load_order']) ? $module_data->manifest['load_order'] : 1;
  if(isset($module_data->manifest['require']) && !empty($module_data->manifest['require']))
  {
    foreach($module_data->manifest['require'] as $require_name)
    {
      $sn_req[$loaded_module_name][$require_name] = 0;
    }
  }
}

// Создаем последовательность инициализации модулей
// По нормальным делам надо сначала читать их конфиги - вдруг какой-то модуль отключен?
do
{
  $prev_order = $load_order;

  foreach($sn_req as $loaded_module_name => &$req_data)
  {
    $level = 1;
    foreach($req_data as $req_name => &$req_level)
    {
      if($load_order[$req_name] == -1 || !isset($load_order[$req_name]))
      {
        $level = $req_level = -1;
        break;
      }
      else
      {
        $level += $load_order[$req_name];
      }
      $req_level = $load_order[$req_name];
    }
    if($level > $load_order[$loaded_module_name] || $level == -1)
    {
      $load_order[$loaded_module_name] = $level;
    }
  }
}
while($prev_order != $load_order);
asort($load_order);

// Инициализируем модули
// По нормальным делам это должна быть загрузка модулей и лишь затем инициализация - что бы минимизировать размер процесса в памяти
foreach($load_order as $loaded_module_name => $load_order)
{
  if($load_order < 0)
  {
    continue;
  }
  $sn_module[$loaded_module_name]->initialize();
  $sn_module_list[$sn_module[$loaded_module_name]->manifest['package']][$loaded_module_name] = &$sn_module[$loaded_module_name];
}

// Скрипач не нужон
unset($load_order);
unset($sn_req);

// А теперь проверяем - поддерживают ли у нас загруженный код такую страницу
if(!isset($sn_data['pages'][$sn_page_name]))
{
  $sn_page_name = '';
}

sn_db_connect();

$lang          = array();
$lang          = new classLocale();
lng_switch(sys_get_param_str('lang'));


if($config->server_updater_check_auto && $config->server_updater_check_last + $config->server_updater_check_period <= $time_now)
{
  include(SN_ROOT_PHYSICAL . 'ajax_version_check.php');
}

if($config->user_birthday_gift && $time_now > $config->user_birthday_celebrate + PERIOD_DAY)
{
  require_once("{$sn_root_physical}includes/includes/user_birthday_celebrate.{$phpEx}");
  sn_user_birthday_celebrate();
}

if(!$config->var_online_user_count || $config->var_online_user_time + 30 < $time_now)
{
  $time = $time_now - 15*60;
  $online_count = doquery("SELECT COUNT(*) AS users_online FROM {{users}} WHERE `onlinetime`>'{$time}' AND `user_as_ally` IS NULL;", true);
  $config->db_saveItem('var_online_user_count', $online_count['users_online']);
  $config->db_saveItem('var_online_user_time', $time_now);
  if($config->server_log_online)
  {
    doquery("INSERT INTO {{log_users_online}} SET online_count = {$config->var_online_user_count};");
  }
}

// TODO Грязный хак!
if(sn_module_get_active_count('payment'))
{
/*
  $sn_menu_extra['menu_metamatter'] = array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_metamatter',
    'LINK'  => 'metamatter.php',
    'LOCATION' => '-menu_dark_matter',
  );
*/
}
else
{
}

// ------------------------------------------------------------------------------------------------------------------------------
function sn_sys_load_php_files($dir_name, $phpEx = 'php', $modules = false)
{
  global $sn_module, $lang;

  if(file_exists($dir_name))
  {
    $dir = opendir($dir_name);
    while(($file = readdir($dir)) !== false)
    {
      if($file == '..' || $file == '.')
      {
        continue;
      }

      $full_filename = $dir_name . $file;
      if($modules && is_dir($full_filename))
      {
        if(file_exists($full_filename = "{$full_filename}/{$file}.{$phpEx}"))
        {
          require_once($full_filename);
          // Registering module
          if(class_exists($file))
          {
            new $file($full_filename);
          }
        }
      }
      else
      {
        $extension = substr($full_filename, -strlen($phpEx));
        if($extension == $phpEx)
        {
          require_once($full_filename);
        }
      }
    }
  }
}

function sys_refresh_tablelist($db_prefix)
{
  global $sn_cache;

  $query = doquery('SHOW TABLES;');

  while ( $row = mysql_fetch_assoc($query) )
  {
    foreach($row as $row)
    {
      $tl[] = str_replace($db_prefix, '', $row);
    }
  }
  $sn_cache->tables = $tl;
}
