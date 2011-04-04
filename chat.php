<?php
/**
 chat.php
   Main chat window

 Changelog:
   2.0 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [+] Rewrote to remove unnecessary code dupes
   1.5 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] More DDoS-realted fixes
   1.4 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] DDoS-realted fixes
   1.3 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] Security checks for SQL-injection
   1.2 by Ihor
   1.0 copyright 2008 by e-Zobar for XNova
**/

$skip_fleet_update = true;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('chat');

$nick = $user['username'];
$parse = $lang;

if ($_GET) {
  if($_GET["chat_type"]=="ally"){
    $parse['chat_type'] = $_GET["chat_type"];
    $parse['ally_id']   = $user['ally_id'];
  }
}

$config->array_set('users', $user['id'], 'chat_lastUpdate', $time_now);

$page = parsetemplate(gettemplate('chat_body'), $parse);
display($page, $lang['Chat']);

// Shoutbox by e-Zobar - Copyright XNova Team 2008
?>