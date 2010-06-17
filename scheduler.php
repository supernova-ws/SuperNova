<?php
include_once('includes/init.inc');

$nextStatUpdate = SYS_scheduleGetNextRun($config->var_stats_schedule, $config->var_stats_lastUpdated, $time_now);

includeLang('admin');

if($_SERVER['HTTP_REFERER'] == 'http://' . $_SERVER['HTTP_HOST']. '/admin/statbuilder.php'){
  $isAdminRequest = true;
  $nextStatUpdate = time();
}

if($nextStatUpdate>$config->var_stats_lastUpdated){
  if($isAdminRequest){
    $msg = "admin request";
  }else{
    $msg = "scheduler. Config->var_stats_lastUpdated = " . date(DATE_TIME, $config->var_stats_lastUpdated) . ", nextStatUpdate = " . date(DATE_TIME, $nextStatUpdate);
  };
  $debug->warning("Running stat updates: " . $msg, "Stat update", 999);

  $config->var_stats_lastUpdated = $nextStatUpdate;
  $totaltime = microtime(true);
  SYS_statCalculate();
  $totaltime = microtime(true) - $totaltime;

  $msg = $lang['adm_done'] . ': ' . $totaltime . ' ' . $lang['sys_sec'];
  $debug->warning("Stat update complete: " . $msg, "Stat update", 999);
}else{
//  $msg = $lang['adm_schedule_none'];
}

if($msg){
  $msg = iconv('CP1251', 'UTF-8', $msg);
  $xml = "<ratings><message>" . $msg . "</message></ratings>";

  header('Content-type: text/xml');
  echo $xml;
}
?>