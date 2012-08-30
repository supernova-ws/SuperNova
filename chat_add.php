<?php
/**
 chat_add.php
   AJAX-called code to post message to chat

 Changelog:
   4.0 copyright © 2009-2012 Gorlum for http://supernova.ws
     [!] Another rewrite
     [+] Chat is now incremental
   3.0 copyright (c) 2009-2011 by Gorlum for http://supernova.ws
     [!] Almost full rewrote
     [+] Complies with PCG1
   2.0 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
     [+] Rewrote to remove unnecessary code dupes
   1.5 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
     [~] More DDoS-realted fixes
   1.4 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
     [~] DDoS-realted fixes
   1.3 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
     [~] Security checks for SQL-injection
   1.2 by Ihor
   1.0 copyright 2008 by e-Zobar for XNova
*/

$skip_fleet_update = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));
if($config->_MODE != CACHER_NO_CACHE && $config->chat_timeout && $microtime - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout)
{
  die();
}

if(($message = sys_get_param_str('message')) && $user['username'])
{
  $nick = mysql_real_escape_string(render_player_nick($user, true));
  $message = preg_replace("#(?:http\:\/\/(?:.+)?\/rw\.php\?raport\=([0-9a-fA-F]{32}))#", "[rw=$1]", $message);
  $ally_id = sys_get_param('ally') && $user['ally_id'] ? $user['ally_id'] : 0;

  $query = doquery("INSERT INTO {{chat}} (user, ally_id, message, timestamp) VALUES ('{$nick}', '{$ally_id}', '{$message}', '{$time_now}');");

  $config->array_set('users', $user['id'], 'chat_last_activity', $microtime);
}

?>
