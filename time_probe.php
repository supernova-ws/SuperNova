<?php

require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

/*
$time_local  = $time_server + $time_diff
$time_diff   = $time_local  - $time_server
$time_server = $time_local  - $time_diff
*/

// $time_diff = ($time_local = intval($_POST['localtime'] / 1000)) ? $time_local - $time_now + intval($_POST['utcoffset']) - date('Z') : 0;
$time_diff = ($time_local = intval($_POST['localtime'] / 1000)) ? $time_local - $time_now : 0;
$time_utcoffset = ($time_local_utcoffset = intval($_POST['utcoffset'])) ? $time_local_utcoffset - date('Z') : 0;
if($user['id'] && !$user['user_time_diff_forced'])
{
  doquery("UPDATE {{users}} SET `user_time_diff` = {$time_diff}, `user_time_utc_offset` = {$time_utcoffset} WHERE `id` = {$user['id']} LIMIT 1;");
}
echo $time_diff;
