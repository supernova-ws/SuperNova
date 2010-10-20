<?php
if(defined('INIT'))
{
  return;
}

define('INIT'    , true);
define('INSIDE'  , true);
define('IN_PHPBB', true);
define('INSTALL' , false);
define('VERSION' , '2010-10-19-00-03');

set_magic_quotes_runtime(0);
ini_set('error_reporting', E_ALL ^ E_NOTICE);

if($_SERVER['SERVER_NAME'] == 'localhost')
{
  define('BE_DEBUG', true);
  ini_set('display_errors', 1);
}
else
{
  ini_set('display_errors', 0);
}

$time_now      = time();
$user          = array();
$lang          = array();
$IsUserChecked = false;

$ugamela_root_path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']) . '/';
$phpbb_root_path = $ugamela_root_path;
$phpEx = substr(strrchr(__FILE__, '.'), 1);

require("{$ugamela_root_path}config.{$phpEx}");
$db_prefix = $dbsettings['prefix'];
$sn_secret_word = $dbsettings['secretword'];
unset($dbsettings);

// required for db.php
include_once("{$ugamela_root_path}includes/debug.class.{$phpEx}");
include_once("{$ugamela_root_path}includes/db.{$phpEx}");
// Initializing global 'debug' object
$debug = new debug();

//$dbms = 'mysql';
//include_once("{$ugamela_root_path}includes/db/{$dbms}.{$phpEx}");
// $db      = new $sql_db();

$dir = opendir("{$ugamela_root_path}includes/classes");
while (($file = readdir($dir)) !== false)
{
  $extension = '.' . substr($file, -3);
  if ($extension == ".{$phpEx}"){
    require_once "{$ugamela_root_path}includes/classes/{$file}";
  }
}

// Initializing global 'cacher' object
$sn_cache = new classCache($db_prefix);
if(!isset($sn_cache->tables))
{
  sys_refresh_tablelist($db_prefix);
}

// Initializing global "config" object
$config = new classConfig($db_prefix);
$config->db_prefix = $db_prefix;
$config->secret_word = $sn_secret_word;

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
    if($time_now >= $config->var_db_update_end)
    {
      $config->db_saveItem('var_db_update_end', $time_now + 60);

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
define('TEMPLATE_DIR'     , 'design/templates/');
define('TEMPLATE_NAME'    , $config->game_default_template ? $config->game_default_template : 'OpenGame');
define('DEFAULT_SKINPATH' , $config->game_default_skin ? $config->game_default_skin : 'skins/EpicBlue/');
define('DEFAULT_LANG'     , $config->game_default_language ? $config->game_default_language : 'ru');
define('FMT_DATE'         , $config->int_format_date ? $config->int_format_date : 'd.m.Y');
define('FMT_TIME'         , $config->int_format_time ? $config->int_format_time : 'H:i:s');
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

sn_db_connect();

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

?>
