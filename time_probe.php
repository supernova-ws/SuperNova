<?php
//  header('Content-type: text/xml');
//  echo '<time>' . time() . '</time>';
//  echo time();
  require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

/*
$time_local  = $time_server + $time_diff
$time_diff   = $time_local  - $time_server
$time_server = $time_local  - $time_diff
*/

//  $time_diff = ($time_local = intval($_POST['localtime'] / 1000)) ? $time_local - $time_now : 0;
  $time_diff = ($time_local = intval($_POST['localtime'] / 1000)) ? $time_local - $time_now + intval($_POST['utcoffset']) - date('Z') : 0;
  if($user['id'] && !$user['user_time_diff_forced'])
  {
    doquery("UPDATE {{users}} SET `user_time_diff` = {$time_diff} WHERE `id` = {$user['id']} LIMIT 1;");
  }
  echo $time_diff;
?>
