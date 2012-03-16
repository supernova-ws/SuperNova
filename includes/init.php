<?php
//define('BE_DEBUG', true);

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

header('Content-type: text/html; charset=utf-8');

ob_start();

ini_set('error_reporting', E_ALL ^ E_NOTICE);

if($_SERVER['SERVER_NAME'] == 'localhost')
{
  define('BE_DEBUG', true);
}

$phpEx = substr(strrchr(__FILE__, '.'), 1);
$phpEx = strpos($phpEx, '/') === false ? $phpEx : '';

$sn_root_relative = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1);
if(strpos($sn_root_relative, 'admin/') !== false)
{
  $sn_root_relative = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], 'admin/'));
}
if(strpos($sn_root_relative, '.local/') !== false)
{
  $sn_root_relative = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '.local/'));
}
$sn_root_physical = str_replace(array('//', '//'), '/', $_SERVER['DOCUMENT_ROOT'] . $sn_root_relative);
$phpbb_root_path  = $sn_root_physical;

define('SN_ROOT_RELATIVE', $sn_root_relative);
define('SN_ROOT_PHYSICAL', $sn_root_physical);
define('SN_ROOT_VIRTUAL' , 'http://' . $_SERVER['HTTP_HOST'] . $sn_root_relative);
define('PHP_EX', $phpEx); // PHP extension on this server

define('SN_TIME_NOW', $time_now = time());
define('SN_TIME_MICRO', $microtime = microtime(true));

$user          = array();
$lang          = array();
$sn_modules    = array();
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
  define('BE_DEBUG', true);
  ini_set('display_errors', 1);
  error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
}
else
{
  define('BE_DEBUG', false);
  ini_set('display_errors', 0);
}

require_once("{$sn_root_physical}includes/vars.{$phpEx}");

$update_file = "{$sn_root_physical}includes/update.{$phpEx}";
if(file_exists($update_file))
{
  if(filemtime($update_file) > $config->db_loadItem('var_db_update') || $config->db_loadItem('db_version') < DB_VERSION)
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
}
unset($db_name);

// Initializing constants
define('SN_COOKIE'        , $config->COOKIE_NAME ? $config->COOKIE_NAME : 'SuperNova');
define('SN_COOKIE_I'      , SN_COOKIE . '_I');
define('TEMPLATE_NAME'    , $config->game_default_template ? $config->game_default_template : 'OpenGame');
define('TEMPLATE_DIR'     , SN_ROOT_PHYSICAL . 'design/templates/' . TEMPLATE_NAME);
define('DEFAULT_SKINPATH' , $config->game_default_skin ? $config->game_default_skin : 'skins/EpicBlue/');
define('DEFAULT_LANG'     , $config->game_default_language ? $config->game_default_language : 'ru');
define('FMT_DATE'         , $config->int_format_date ? $config->int_format_date : 'd.m.Y');
define('FMT_TIME'         , $config->int_format_time ? $config->int_format_time : 'H:i:s');
define('FMT_DATE_TIME'    , FMT_DATE . ' ' . FMT_TIME);

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

// Now including all functions
require_once("{$sn_root_physical}includes/general.{$phpEx}");
require_once("{$sn_root_physical}includes/template.{$phpEx}");
sn_sys_load_php_files("{$sn_root_physical}includes/functions/", $phpEx);

$sn_module = array();
sn_sys_load_php_files("{$sn_root_physical}modules/", $phpEx, true);

sn_db_connect();

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

// ------------------------------------------------------------------------------------------------------------------------------
function sn_sys_load_php_files($dir_name, $phpEx = 'php', $modules = false)
{
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

?>
