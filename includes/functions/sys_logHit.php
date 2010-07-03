<?php
function sys_logHit(){
  global $time_now, $user;
  doquery("INSERT INTO {{table}} (`time`, `page`, `url`, `user_id`, `ip`) VALUES ('{$time_now}', '{$_SERVER['PHP_SELF']}', '{$_SERVER['REQUEST_URI']}', '{$user['id']}', '{$_SERVER['REMOTE_ADDR']}');", 'counter');
}
?>