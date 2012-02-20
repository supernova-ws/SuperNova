<?php

require_once('includes/init.' . substr(strrchr(__FILE__, '.'), 1));

$url = 'http://supernova.ws/version_check.php?db=' . DB_VERSION . '&release=' . SN_RELEASE . '&version=' . SN_VERSION;

$check_result = sn_get_url_contents($url);
$config->db_saveItem('server_updater_check_last', $time_now);
$config->db_saveItem('server_updater_check_result', $check_result);

if(sys_get_param_int('ajax'))
{
  print($check_result);
}

?>
