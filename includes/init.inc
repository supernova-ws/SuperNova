<?php
if(defined('INIT'))
{
  return;
}

function sys_refresh_tablelist()
{
  global $cache, $config;

  $query = doquery('SHOW TABLES;');

  while ( $row = mysql_fetch_assoc($query) )
  {
    foreach($row as $row)
    {
      $tl[] = str_replace($config->db_prefix, '', $row);
    }
  }
  $cache->tables = $tl;
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
define('INIT'    , true);
define('INSTALL' , false);

$user          = array();
$lang          = array();
$IsUserChecked = false;

// define('DEFAULT_SKINPATH' , 'skins/xnova/');
define('DEFAULT_SKINPATH' , 'skins/EpicBlue/');
define('TEMPLATE_DIR'     , 'templates/');
define('TEMPLATE_NAME'    , 'OpenGame');
define('DEFAULT_LANG'     , 'ru');

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

$time_now = time();

$ugamela_root_path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']) . '/';
$phpbb_root_path = $ugamela_root_path;

include_once("{$ugamela_root_path}includes/extensions.inc");

include_once("{$ugamela_root_path}includes/debug.class.{$phpEx}");
$debug = new debug();

include_once("{$ugamela_root_path}includes/constants.{$phpEx}");
include_once("{$ugamela_root_path}includes/functions.{$phpEx}");
include_once("{$ugamela_root_path}includes/unlocalised.{$phpEx}");

$dir = opendir("{$ugamela_root_path}includes/functions");
while (($file = readdir($dir)) !== false)
{
  $extension = '.' . substr($file, -3);
  if ($extension == ".{$phpEx}")
  {
    require_once "{$ugamela_root_path}includes/functions/{$file}";
  }
}

$dir = opendir("{$ugamela_root_path}includes/classes");
while (($file = readdir($dir)) !== false)
{
  $extension = '.' . substr($file, -3);
  if ($extension == ".{$phpEx}"){
    require_once "{$ugamela_root_path}includes/classes/{$file}";
  }
}

include_once($ugamela_root_path . 'language/'. DEFAULT_LANG .'/lang_info.cfg');
include_once("{$ugamela_root_path}includes/vars.{$phpEx}");
include_once("{$ugamela_root_path}includes/db.{$phpEx}");

includeLang ('system');
includeLang ('tech');

// Initializing global "config" object
include_once("{$ugamela_root_path}config.{$phpEx}");
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

$cache = new classCache($config->db_prefix);
if(!isset($cache->tables))
{
  sys_refresh_tablelist();
}

$update_file = "{$_SERVER['DOCUMENT_ROOT']}/includes/update.{$phpEx}";
$flag_file   = "{$_SERVER['DOCUMENT_ROOT']}/includes/update.last";

if(file_exists($update_file))
{
  if(filemtime($update_file) != filemtime($flag_file))
  {
    require_once($update_file);
    sys_refresh_tablelist();

    if(!file_exists($flag_file))
    {
      fclose(fopen($flag_file, 'w'));
    }

    touch($flag_file, filemtime($update_file));
  }
}
?>