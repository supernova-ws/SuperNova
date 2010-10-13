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
define('INIT'    , true);
define('INSTALL' , false);

$user          = array();
$lang          = array();
$IsUserChecked = false;

$time_now = time();

$ugamela_root_path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']) . '/';
$phpbb_root_path = $ugamela_root_path;

include_once("{$ugamela_root_path}includes/extensions.inc");

include_once("{$ugamela_root_path}includes/debug.class.{$phpEx}");
$debug = new debug();

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

include_once("{$ugamela_root_path}includes/db.{$phpEx}");

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

define('TEMPLATE_DIR'     , 'templates/');
define('TEMPLATE_NAME'    , $config->game_default_template ? $config->game_default_template : 'OpenGame');
define('DEFAULT_SKINPATH' , $config->game_default_skin ? $config->game_default_skin : 'skins/EpicBlue/');
define('DEFAULT_LANG'     , $config->game_default_language ? $config->game_default_language : 'ru');

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

include_once("{$ugamela_root_path}includes/constants.{$phpEx}");
include_once("{$ugamela_root_path}includes/functions.{$phpEx}");
include_once("{$ugamela_root_path}includes/unlocalised.{$phpEx}");

include_once("{$ugamela_root_path}language/" . DEFAULT_LANG .'/lang_info.cfg');
include_once("{$ugamela_root_path}includes/vars.{$phpEx}");

includeLang ('system');
includeLang ('tech');

$cache = new classCache($config->db_prefix);
if(!isset($cache->tables))
{
  sys_refresh_tablelist();
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

?>
