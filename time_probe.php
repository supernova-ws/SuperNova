<?php

global $skip_fleet_update;
$skip_fleet_update = true;

require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

define('IN_AJAX', true);

/*
$time_local  = $time_server + $time_diff
$time_diff   = $time_local  - $time_server
$time_server = $time_local  - $time_diff
*/

$user_time_diff = user_time_diff_get();
if($user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED]) {
  $time_diff = intval($user_time_diff[PLAYER_OPTION_TIME_DIFF]);
} else {
  $user_time_diff = user_time_diff_probe();
  user_time_diff_set($user_time_diff);
  $time_diff = $user_time_diff[PLAYER_OPTION_TIME_DIFF] + $user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET];
}

echo $time_diff;
