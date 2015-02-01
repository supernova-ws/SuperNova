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

// $time_diff = ($time_local = intval($_POST['localtime'] / 1000)) ? $time_local - $time_now + intval($_POST['utc_offset']) - date('Z') : 0;

$user_time_diff = user_time_diff_get();
if($user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED]) {
  $time_diff = intval($user_time_diff[PLAYER_OPTION_TIME_DIFF]);
} else {
//  $user_time_diff = user_time_diff_probe();
//  $time_diff = ($time_local = intval(sys_get_param('localtime') / 1000)) ? $time_local - SN_TIME_NOW : 0;
//  $time_utc_offset = ($time_local_utc_offset = sys_get_param_int('utc_offset')) ? $time_local_utc_offset - date('Z') : 0;
//  user_time_diff_set($time_diff, $time_utc_offset);
  $user_time_diff = user_time_diff_probe();
  user_time_diff_set($user_time_diff);
  $time_diff = $user_time_diff[PLAYER_OPTION_TIME_DIFF];
}

echo $time_diff;
