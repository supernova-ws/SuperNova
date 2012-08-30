<?php
// print(iconv('CP1251', 'UTF-8', 'Chat temporary disabled'));
// die();

/*
 chat_msg.php
   AJAX-called code to show last X chat messages/history

 Changelog:
   4.0 copyright © 2009-2012 Gorlum for http://supernova.ws
     [!] Another rewrite
     [+] Chat is now incremental
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

$history = sys_get_param_str('history');
if(!$history)
{
  $config->array_set('users', $user['id'], 'chat_last_refresh', $microtime);
}

$template_result['.']['chat'] = array();
if(!$history && $config->_MODE != CACHER_NO_CACHE && $config->chat_timeout && $microtime - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout)
{
  $result['disable'] = true;
  $template_result['.']['chat'][] = array(
    'TIME' => date(FMT_DATE_TIME, htmlentities($time_now, ENT_QUOTES, 'utf-8')),
    'DISABLE' => true,
  );
}
else
{
  $alliance = sys_get_param_str('ally') && $user['ally_id'] ? $user['ally_id'] : 0;

  $page_limit = 20; // Chat rows Limit

  $page = 0;
  $where_add = '';
  $last_message = 0;
  if($history)
  {
    $rows = doquery("SELECT count(1) AS CNT FROM {{chat}} WHERE ally_id = '{$alliance}';", true);
    $page_count = ceil($rows['CNT'] / $page_limit);

    for($i = 0; $i < $page_count; $i++)
    {
      $template_result['.']['page'][] = array(
        'NUMBER' => $i
      );
    }

    $page = min($page_count, max(0, sys_get_param_int('page')));
  }
  else
  {
    $last_message = sys_get_param_id('last_message');
    $where_add = $last_message ? "AND `messageid` > {$last_message}" : '';
  }

  $start_row = $page * $page_limit;
  $query = doquery("SELECT * FROM {{chat}} WHERE ally_id = '{$alliance}' {$where_add} ORDER BY messageid DESC LIMIT {$start_row}, {$page_limit};");
  while($chat_row = mysql_fetch_assoc($query))
  {
    // Little magik here - to retain HTML codes from DB and stripping HTML codes from nick
    $nick_stripped = htmlentities(strip_tags($chat_row['user']), ENT_QUOTES, 'utf-8');
    $nick = str_replace(strip_tags($chat_row['user']), $nick_stripped, $chat_row['user']);
    if(!$history)
    {
      $nick = "<span style=\"cursor: pointer;\" onclick=\"addSmiley('({$nick_stripped})');\">{$nick}</span>";
    }

    $template_result['.']['chat'][] = array(
      'TIME' => htmlentities(date(FMT_DATE_TIME, $chat_row['timestamp']), ENT_QUOTES, 'utf-8'),
      'NICK' => $nick,
      'TEXT' => cht_message_parse(htmlentities($chat_row['message'], ENT_QUOTES, 'utf-8')),
    );

    $last_message = max($last_message, $chat_row['messageid']);
  }
}

$template_result['.']['chat'] = array_reverse($template_result['.']['chat']);

$template_result += array(
  'PAGE' => $page,
  'ALLY' => $alliance,
  'HISTORY' => $history,
);

$template = gettemplate('chat_messages', true);
$template->assign_recursive($template_result);
$template = parsetemplate($template);

if($history)
{
  display($template, "{$lang['chat_history']} - {$lang[$alliance ? 'chat_ally' : 'chat_common']}", true, '', false, true);
}
else
{
  $result['last_message'] = $last_message;
  ob_start();
  displayP($template);
  $result['html'] = ob_get_contents();
  ob_end_clean();
  print(json_encode($result));
}

?>
