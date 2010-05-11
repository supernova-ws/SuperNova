<?php
define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');

$ugamela_root_path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
set_magic_quotes_runtime(0);
$phpEx = "php";

$user          = array();
$lang          = array();
$IsUserChecked = false;

define('DEFAULT_SKINPATH' , 'skins/xnova/');
define('TEMPLATE_DIR'     , 'templates/');
define('TEMPLATE_NAME'    , 'OpenGame');
define('DEFAULT_LANG'     , 'ru');

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

include($ugamela_root_path . 'includes/debug.class.'.$phpEx);
$debug = new debug();

include($ugamela_root_path . 'includes/constants.'.$phpEx);
include($ugamela_root_path . 'includes/functions.'.$phpEx);
include_once($ugamela_root_path . 'includes/unlocalised.'.$phpEx);
include($ugamela_root_path . 'includes/todofleetcontrol.'.$phpEx);
include($ugamela_root_path . 'language/'. DEFAULT_LANG .'/lang_info.cfg');

$game_config   = $game_config_default;

$time_now = time();

include($ugamela_root_path . 'includes/vars.'.$phpEx);
include($ugamela_root_path . 'includes/db.'.$phpEx);
include($ugamela_root_path . 'includes/strings.'.$phpEx);

// Initializing global "config" object
include($ugamela_root_path . 'config.'.$phpEx);
$config = objConfig::getInstance($dbsettings['prefix']);
unset($dbsettings);

include($ugamela_root_path . 'admin/statfunctions.'.$phpEx);

$time_now = time();
$nextStatUpdate = SYS_scheduleGetNextRun($config->var_stats_schedule, $config->var_stats_lastUpdated, $time_now);

if($_SERVER['HTTP_REFERER'] == 'http://' . $_SERVER['HTTP_HOST']. '/admin/statbuilder.php'){
  $isAdminRequest = true;
  $nextStatUpdate = time();
}

if($nextStatUpdate>$config->var_stats_lastUpdated){
/*
  if($isAdminRequest){
    $msg = "admin request";
  }else{
    $msg = "scheduler. Config->var_stats_lastUpdated = " . date(DATE_TIME, $config->var_stats_lastUpdated) . ", nextStatUpdate = " . date(DATE_TIME, $nextStatUpdate);
  };
  $debug->warning("Stat update", "Running stat updates: " . $msg, 999);
*/
  $config->var_stats_lastUpdated = $nextStatUpdate;
  $totaltime = microtime(true);
  SYS_statCalculate();
  $totaltime = microtime(true) - $totaltime;
}

$xml = "<ratings><runtime>" . $totaltime . "</runtime></ratings>";
header('Content-type: text/xml');
echo $xml;
?>