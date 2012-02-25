<?php

require_once('includes/init.' . substr(strrchr(__FILE__, '.'), 1));

$mode = sys_get_param_int('mode');

$url = 'http://supernova.ws/version_check.php?mode=' . $mode . '&db=' . DB_VERSION . '&release=' . SN_RELEASE . '&version=' . SN_VERSION;

$check_result = sn_get_url_contents($url);
if($check_result == intval($check_result))
{
  $check_status = $check_result;
}
else
{
  // JSON decode if string
}

$config->db_saveItem('server_updater_check_last', $time_now);
$config->db_saveItem('server_updater_check_result', $check_status);

if(sys_get_param_int('ajax'))
{
  print($check_status);
}

?>
