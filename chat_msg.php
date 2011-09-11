<?php
// print(iconv('CP1251', 'UTF-8', 'Chat temporary disabled'));
// die();

/*
 chat_msg.php
   AJAX-called code to show last X chat messages/history

 Changelog:
   3.0 copyright (c) 2009-2011 by Gorlum for http://supernova.ws
     [!] Almost full rewrote
     [+] Utilize PTE
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

if ($config->_MODE != CACHER_NO_CACHE && $config->chat_timeout && $microtime - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout)
{
  die($lang['chat_timeout']);
}

$history = sys_get_param_str('history');

/*
if(!$history && $microtime - $config->array_get('users', $user['id'], 'chat_last_refresh') < 1)
{
// print($microtime - $config->array_get('users', $user['id'], 'chat_last_refresh'));
 die();
}
*/

$template = gettemplate('chat_messages', true);

$page_limit = 25; // Chat rows Limit

$alliance = sys_get_param_str('ally');
if($alliance && $user['ally_id'])
{
  $alliance = $user['ally_id'];
}
else
{
  $alliance = 0;
}

if ($history)
{
  $rows = doquery("SELECT count(1) AS CNT FROM {{chat}} WHERE ally_id = '{$alliance}';", '', true);
  $page_count = ceil($rows['CNT'] / $page_limit);

  for($i = 0; $i < $page_count; $i++)
  {
    $template->assign_block_vars('page', array(
      'NUMBER' => $i
    ));
  }

  $page = min($page_count, sys_get_param_int('page'));
}
else
{
  $page = 0;
}

$chat = array();
$start_row = $page * $page_limit;
$query = doquery("SELECT * FROM {{chat}} WHERE ally_id = '{$alliance}' ORDER BY messageid DESC LIMIT {$start_row}, {$page_limit};");
while($chat_row = mysql_fetch_object($query))
{
  // Little magik here - to retain HTML codes from DB and stripping HTML codes from nick
  $nick_stripped = htmlentities(strip_tags($chat_row->user), ENT_QUOTES, 'utf-8');
  $nick = str_replace(strip_tags($chat_row->user), $nick_stripped, $chat_row->user);
  if(!$history)
  {
    $nick = "<span style=\"cursor: pointer;\" onclick=\"addSmiley('[{$nick_stripped}]');\">{$nick}</span>";
  }

  $chat[] = array(
    'TIME' => date(FMT_DATE_TIME, htmlentities($chat_row->timestamp, ENT_QUOTES, 'utf-8')),
    'NICK' => $nick,
    'TEXT' => cht_message_parse(htmlentities($chat_row->message, ENT_QUOTES, 'utf-8')),
  );
}

$chat = array_reverse($chat);
foreach($chat as $chat_item)
{
  $template->assign_block_vars('chat', $chat_item);
}

$template->assign_vars(array(
  'PAGE' => $page,
  'ALLY' => $alliance,
  'HISTORY' => $history,
));

$config->array_set('users', $user['id'], 'chat_last_refresh', $microtime);

if($history)
{
  display(parsetemplate($template), "{$lang['chat_history']} - {$lang[$alliance ? 'chat_ally' : 'chat_common']}");
}
else
{
  displayP(parsetemplate($template));
}

?>
