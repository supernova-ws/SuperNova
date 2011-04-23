<?php
/**
 chat.php
   Main chat window

 Changelog:
   3.0 copyright (c) 2009-2011 Gorlum for http://supernova.ws
     [!] Almost full rewrote
     [+] Complies with PCG1
   2.0 copyright (c) 2009-2010 Gorlum for http://supernova.ws
     [+] Rewrote to remove unnecessary code dupes
   1.5 copyright (c) 2009-2010 Gorlum for http://supernova.ws
     [~] More DDoS-realted fixes
   1.4 copyright (c) 2009-2010 Gorlum for http://supernova.ws
     [~] DDoS-realted fixes
   1.3 copyright (c) 2009-2010 Gorlum for http://supernova.ws
     [~] Security checks for SQL-injection
   1.2 by Ihor
   1.0 Shoutbox copyright 2008 by e-Zobar for XNova
**/

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = gettemplate('chat_body', true);

$ally = sys_get_param_str('ally');
if ($ally)
{
  $template->assign_var('ALLY', intval($user['ally_id']));
  $page_title = $lang['chat_ally'];
}
else
{
  $page_title = $lang['chat_common'];
}

$config->array_set('users', $user['id'], 'chat_last_activity', $microtime);
$config->array_set('users', $user['id'], 'chat_last_refresh', 0);
display($template, $page_title);

?>
