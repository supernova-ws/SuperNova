<?php
if(defined('INIT'))
{
  return;
}

set_magic_quotes_runtime(0);
ini_set('error_reporting', E_ALL ^ E_NOTICE);

if($_SERVER['SERVER_NAME'] == 'localhost')
{
  define('BE_DEBUG', true);
  ini_set('display_errors', 1);
//  ini_set('error_reporting', E_ALL);
}
else
{
  ini_set('display_errors', 0);
}

define('VERSION','v. 0000');

define('INSIDE'  , true);
define('INSTALL' , false);

$user          = array();
$lang          = array();
$IsUserChecked = false;

$time_now = time();

$ugamela_root_path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']) . '/';
$phpbb_root_path = $ugamela_root_path;

// required for any other code
include_once("{$ugamela_root_path}includes/extensions.inc");
// required for db.php
include_once("{$ugamela_root_path}includes/debug.class.{$phpEx}");
// required for class_cache.php
include_once("{$ugamela_root_path}includes/db.{$phpEx}");

$dir = opendir("{$ugamela_root_path}includes/classes");
while (($file = readdir($dir)) !== false)
{
  $extension = '.' . substr($file, -3);
  if ($extension == ".{$phpEx}"){
    require_once "{$ugamela_root_path}includes/classes/{$file}";
  }
}

// Initializing global 'debug' object
$debug = new debug();

require("{$ugamela_root_path}config.{$phpEx}");

// Initializing global 'cacher' object
$cache = new classCache($dbsettings['prefix']);
if(!isset($cache->tables))
{
  sys_refresh_tablelist();
}

// Initializing global "config" object
$config = new classConfig($dbsettings['prefix']);
$config->db_prefix = $dbsettings['prefix'];
unset($dbsettings);

if($config->debug)
{
  ini_set('display_errors', 1);
  if(!defined('BE_DEBUG'))
  {
    define('BE_DEBUG', true);
  }
}

$update_file = "{$_SERVER['DOCUMENT_ROOT']}/includes/update.{$phpEx}";
if(file_exists($update_file))
{
  if(filemtime($update_file) > $config->var_db_update)
  {
    if($time_now > $config->var_db_update_end)
    {
      $config->db_saveItem('var_db_update_end', $time_now + 600);

      require_once($update_file);
      sys_refresh_tablelist();

      $config->db_saveItem('var_db_update', $time_now);
      $config->db_saveItem('var_db_update_end', $time_now);
    }
    elseif(filemtime($update_file) > $config->var_db_update)
    {
      $timeout = $config->var_db_update_end - $time_now;
      die("Database update in progress. It may take up to {$timeout} seconds more. Please wait...");
    }
  }
}

// Initializing constants
define('TEMPLATE_DIR'     , 'templates/');
define('TEMPLATE_NAME'    , $config->game_default_template ? $config->game_default_template : 'OpenGame');
define('DEFAULT_SKINPATH' , $config->game_default_skin ? $config->game_default_skin : 'skins/EpicBlue/');
define('DEFAULT_LANG'     , $config->game_default_language ? $config->game_default_language : 'ru');
define('FMT_DATE'         , $config->int_format_date ? $config->int_format_date : 'd.m.Y');
define('FMT_TIME'         , $config->int_format_time ? $config->int_format_time : 'h:i:s');
define('FMT_DATE_TIME'    , FMT_DATE . ' ' . FMT_TIME);

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

// Now including all functions
include_once("{$ugamela_root_path}includes/constants.{$phpEx}");
include_once("{$ugamela_root_path}includes/functions.{$phpEx}");
include_once("{$ugamela_root_path}includes/vars.{$phpEx}");

include_once("{$ugamela_root_path}includes/template.{$phpEx}");
include_once("{$ugamela_root_path}language/" . DEFAULT_LANG .'/lang_info.cfg');

$dir = opendir("{$ugamela_root_path}includes/functions");
while (($file = readdir($dir)) !== false)
{
  $extension = '.' . substr($file, -3);
  if ($extension == ".{$phpEx}")
  {
    require_once "{$ugamela_root_path}includes/functions/{$file}";
  }
}

includeLang ('system');
includeLang ('tech');

function sys_refresh_tablelist()
{
  global $cache, $dbsettings;

  $query = doquery('SHOW TABLES;');

  while ( $row = mysql_fetch_assoc($query) )
  {
    foreach($row as $row)
    {
      $tl[] = str_replace($dbsettings['prefix'], '', $row);
    }
  }
  $cache->tables = $tl;
}

define('INIT', true);

?>
