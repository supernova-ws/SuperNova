<?php
/**
 chat.php
   Main chat window

 Changelog:
   4.0 copyright © 2009-2012 Gorlum for http://supernova.ws
     [!] Another rewrite
     [+] preMVC-compatible
   3.0 copyright © 2009-2011 Gorlum for http://supernova.ws
     [!] Almost full rewrote
     [+] Complies with PCG1
   2.0 copyright © 2009-2010 Gorlum for http://supernova.ws
     [+] Rewrote to remove unnecessary code dupes
   1.5 copyright © 2009-2010 Gorlum for http://supernova.ws
     [~] More DDoS-realted fixes
   1.4 copyright © 2009-2010 Gorlum for http://supernova.ws
     [~] DDoS-realted fixes
   1.3 copyright © 2009-2010 Gorlum for http://supernova.ws
     [~] Security checks for SQL-injection
   1.2 by Ihor
   1.0 Shoutbox copyright © 2008 by e-Zobar for XNova
**/

$sn_mvc['model']['chat'][] = 'sn_chat_model';
$sn_mvc['view']['chat'][] = 'sn_chat_view';
$sn_mvc['model']['chat_add'][] = 'sn_chat_add_model';
$sn_mvc['view']['chat_msg'][] = 'sn_chat_msg_view';

function sn_chat_model()
{
  global $config, $user, $microtime, $template_result, $lang, $supernova;

  $config->array_set('users', $user['id'], 'chat_last_activity', $microtime);
  $config->array_set('users', $user['id'], 'chat_last_refresh', 0);

  $mode = sys_get_param_int('mode');
  switch($mode)
  {
    case CHAT_MODE_ALLY:
      $template_result['ALLY'] = intval($user['ally_id']);
      $page_title = $lang['chat_ally'];
    break;

    case CHAT_MODE_COMMON:
    default:
      $page_title = $lang['chat_common'];
    break;
  }

  $template_result['.']['smiles'] = array();
  foreach($supernova->design['smiles'] as $bbcode => $filename)
  {
    $template_result['.']['smiles'][] = array(
      'BBCODE' => $bbcode,
      'FILENAME' => $filename,
    );
  }

  $template_result['PAGE_HEADER'] = $page_title;
}

function sn_chat_view($template = null)
{
  $template = gettemplate('chat_body', $template);

  return $template;
}

function sn_chat_add_model()
{
  global $skip_fleet_update, $config, $microtime, $user, $time_now;

  $skip_fleet_update = true;

  if($config->_MODE != CACHER_NO_CACHE && $config->chat_timeout && $microtime - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout)
  {
    die();
  }

  if(($message = sys_get_param_str('message')) && $user['username'])
  {
    $ally_id = sys_get_param('ally') && $user['ally_id'] ? $user['ally_id'] : 0;
    $nick = mysql_real_escape_string(render_player_nick($user, array('color' => true, 'icons' => true, 'ally' => !$ally_id)));
    $message = preg_replace("#(?:https?\:\/\/(?:.+)?\/index\.php\?page\=battle_report\&cypher\=([0-9a-zA-Z]{32}))#", "[ube=$1]", $message);

    doquery("INSERT INTO {{chat}} (user, ally_id, message, timestamp) VALUES ('{$nick}', '{$ally_id}', '{$message}', '{$time_now}');");

    $config->array_set('users', $user['id'], 'chat_last_activity', $microtime);
  }

  die();
}

function sn_chat_msg_view($template = null)
{
  define('IN_AJAX', true);

  global $config, $skip_fleet_update, $microtime, $user, $time_now, $time_local, $time_diff, $lang;

  $skip_fleet_update = true;

  $history = sys_get_param_str('history');
  if(!$history)
  {
    $config->array_set('users', $user['id'], 'chat_last_refresh', $microtime);
  }

  $page = 0;
  $last_message = '';
  $alliance = 0;
  $template_result['.']['chat'] = array();
  if(!$history && $config->_MODE != CACHER_NO_CACHE && $config->chat_timeout && $microtime - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout)
  {
    $result['disable'] = true;
    $template_result['.']['chat'][] = array(
      'TIME' => date(FMT_DATE_TIME, htmlentities($time_local, ENT_QUOTES, 'utf-8')),
      'DISABLE' => true,
    );
  }
  else
  {
    $alliance = sys_get_param_str('ally') && $user['ally_id'] ? $user['ally_id'] : 0;

    $page_limit = 20; // Chat rows Limit

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

      $page = min($page_count, max(0, sys_get_param_int('sheet')));
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
        'TIME' => cht_message_parse(date(FMT_DATE_TIME, $chat_row['timestamp'] + $time_diff)),
        'NICK' => $nick,
        'TEXT' => cht_message_parse($chat_row['message']),
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

  $template = gettemplate('chat_messages', $template);
  $template->assign_recursive($template_result);

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
  die();
}
