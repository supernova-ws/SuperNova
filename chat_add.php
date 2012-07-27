<?php
/**
 chat_add.php
   AJAX-called code to post message to chat

 Changelog:
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

$message = sys_get_param_str('message');
if(sys_get_param_id('ally'))
{
  $ally_id = $user['ally_id'];
}
else
{
  $ally_id = 0;
}

if ($message && $user['username'])
{
  $nick = trim(strip_tags($user['username']));// . ($user['ally_tag'] ? '[' . trim(strip_tags($user['ally_tag'])) . ']' : '');

  $nick = render_player_nick($user, true);
/*
  if($user['authlevel'])
  {
    switch($user['authlevel'])
    {
      case 3:
        $highlight = $config->chat_highlight_admin;
      break;

      case 2:
        $highlight = $config->chat_highlight_operator;
      break;

      case 1:
        $highlight = $config->chat_highlight_moderator;
      break;
    }
  }
  elseif(mrc_get_level($user, false, UNIT_PREMIUM))
  {
    $highlight = $config->chat_highlight_premium;
  }
*/
  $nick = preg_replace("#(.+)#", $highlight ? $highlight : '$1', $nick);

  $nick = mysql_real_escape_string($nick);

  $message = preg_replace("#(?:http\:\/\/(?:.+)?\/rw\.php\?raport\=([0-9a-fA-F]{32}))#", "[rw=$1]", $message);

  $query = doquery("INSERT INTO {{chat}} (user, ally_id, message, timestamp) VALUES ('{$nick}', '{$ally_id}', '{$message}', '{$time_now}');");
  $config->array_set('users', $user['id'], 'chat_last_activity', $microtime);
}

?>
