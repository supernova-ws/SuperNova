<?php

require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

define('IN_AJAX', true);

/*
$time_local  = $time_server + $time_diff
$time_diff   = $time_local  - $time_server
$time_server = $time_local  - $time_diff
*/

// $time_diff = ($time_local = intval($_POST['localtime'] / 1000)) ? $time_local - $time_now + intval($_POST['utcoffset']) - date('Z') : 0;

if(isset($user['user_time_diff_forced']) && $user['user_time_diff_forced']) {
  $time_diff = $user['user_time_diff'];
} else {
  $time_diff = ($time_local = intval($_POST['localtime'] / 1000)) ? $time_local - SN_TIME_NOW : 0;
  $time_utcoffset = ($time_local_utcoffset = intval($_POST['utcoffset'])) ? $time_local_utcoffset - date('Z') : 0;
  if(isset($user['id']) && $user['id'])  {
    db_user_set_by_id($user['id'], "`user_time_diff` = {$time_diff}, `user_time_utc_offset` = {$time_utcoffset}, `user_time_measured` = " . SN_TIME_NOW);
  }
}

echo $time_diff;
