<?php

require_once('includes/init.' . substr(strrchr(__FILE__, '.'), 1));

$mode = sys_get_param_int('mode');
$ajax = sys_get_param_int('ajax');

$url = 'http://supernova.ws/version_check.php?mode=' . $mode 
  . '&db=' . DB_VERSION
  . '&release=' . SN_RELEASE
  . '&version=' . SN_VERSION
  . '&key=' . urlencode($config->server_updater_key)
  . '&id=' . urlencode($config->server_updater_id);
/*
//TODO REMOVE DEBUG!!!
$url = 'http://localhost/supernova_site/version_check.php?mode=' . $mode
. '&db=' . DB_VERSION
. '&release=' . SN_RELEASE
. '&version=' . SN_VERSION
. '&key=' . urlencode($config->server_updater_key)
. '&id=' . urlencode($config->server_updater_id);
*/
switch($mode)
{
  case SNC_MODE_REGISTER:
    if($config->server_updater_key || $config->server_updater_id)
    {
      if($ajax)
      {
        print(SNC_VER_REGISTER_ERROR_REGISTERED);
      }
      die();
    }
    $url .= "&name=" . urlencode($config->game_name) . "&url=" . urlencode(SN_ROOT_VIRTUAL);
//TODO REMOVE DEBUG!!!
//$url .= "&name=" . urlencode($config->game_name) . "&url=" . urlencode('http://supernova.ws/');
  break;
}

$check_result = sn_get_url_contents($url);
if(!$check_result)
{
  $version_check = SNC_VER_ERROR_CONNECT;
}
elseif(($version_check = intval($check_result)) && $version_check == $check_result)
{
  $version_check = $check_result;
}
else
{
  // JSON decode if string
  $check_result = json_decode($check_result, true);
  $version_check = $check_result === null ? SNC_VER_UNKNOWN_RESPONSE : $check_result['version_check'];

  switch($mode)
  {
    case SNC_MODE_REGISTER:
      if($check_result['site']['site_key'] && $check_result['site']['site_id'] && $check_result['site']['result'] == SNC_VER_REGISTER_REGISTERED)
      {
        $config->db_saveItem('server_updater_key', $check_result['site']['site_key']);
        $config->db_saveItem('server_updater_id', $check_result['site']['site_id']);
      }
      $version_check = $check_result['site']['result'];
    break;
  }
}
//debug($mode);

$config->db_saveItem('server_updater_check_last', SN_TIME_NOW);
$config->db_saveItem('server_updater_check_result', $version_check);

if($ajax)
{
  define('IN_AJAX', true);
  print($version_check);
}
