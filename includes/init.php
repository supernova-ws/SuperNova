<?php

if(defined('INIT'))
{
  return;
}

define('INIT'    , true);
define('INSIDE'  , true);
define('IN_PHPBB', true);
define('INSTALL' , false);

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

ob_start();

if(function_exists('set_magic_quotes_runtime'))
{
  @set_magic_quotes_runtime(0);
}
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
define('SN_ROOT_VIRTUAL',  'http://' . $_SERVER['HTTP_HOST'] . $sn_root_relative);
define('PHP_EX', $phpEx); // PHP extension on this server

$time_now      = time();
$microtime     = microtime(true);
$user          = array();
$lang          = array();
$sn_modules    = array();
$IsUserChecked = false;

require("{$sn_root_physical}config.{$phpEx}");
$db_prefix = $dbsettings['prefix'];
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

$dir_name = "{$sn_root_physical}includes/classes";
$dir = opendir($dir_name);
while (($file = readdir($dir)) !== false)
{
  $extension = substr(strrchr($file, '.'), 1); //$extension = '.' . substr($file, -3);
  if(strpos($extension, '/') !== false)
  {
    $extension = '';
  }

  if ($extension == $phpEx){
    require_once("{$dir_name}/{$file}");
  }
}

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
$config->db_prefix = $db_prefix;
$config->secret_word = $sn_secret_word;

if(!defined('BE_DEBUG'))
{
  if($config->debug)
  {
    define('BE_DEBUG', true);
    ini_set('display_errors', 1);
  }
  else
  {
    define('BE_DEBUG', false);
    ini_set('display_errors', 0);
  }
}

$update_file = "{$sn_root_physical}includes/update.{$phpEx}";
if(file_exists($update_file))
{
  if(filemtime($update_file) > $config->var_db_update || $config->db_version < DB_VERSION)
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

// Initializing constants
define('TEMPLATE_NAME'    , $config->game_default_template ? $config->game_default_template : 'OpenGame');
define('TEMPLATE_DIR'     , SN_ROOT_PHYSICAL . 'design/templates/' . TEMPLATE_NAME);
define('DEFAULT_SKINPATH' , $config->game_default_skin ? $config->game_default_skin : 'skins/EpicBlue/');
define('DEFAULT_LANG'     , $config->game_default_language ? $config->game_default_language : 'ru');
define('FMT_DATE'         , $config->int_format_date ? $config->int_format_date : 'd.m.Y');
define('FMT_TIME'         , $config->int_format_time ? $config->int_format_time : 'H:i:s');
define('FMT_DATE_TIME'    , FMT_DATE . ' ' . FMT_TIME);

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

// Now including all functions
require_once("{$sn_root_physical}includes/functions.{$phpEx}");
require_once("{$sn_root_physical}includes/vars.{$phpEx}");

require_once("{$sn_root_physical}includes/template.{$phpEx}");
require_once("{$sn_root_physical}language/" . DEFAULT_LANG .'/language.mo');
$lang['LANG_INFO'] = $lang_info;
unset($lang_info);

$dir = opendir("{$sn_root_physical}includes/functions");
while (($file = readdir($dir)) !== false)
{
  $extension = '.' . substr($file, -3);
  if ($extension == ".{$phpEx}")
  {
    require_once "{$sn_root_physical}includes/functions/{$file}";
  }
}

lng_include('system');
lng_include('tech');

sn_db_connect();

?>
