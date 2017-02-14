<?php
/**
 * Copyright © 2009-2017 Gorlum for http://supernova.ws
 *
 */

function sn_chat_model()
{
  global $config, $user, $template_result, $lang;

  $config->array_set('users', $user['id'], 'chat_last_activity', SN_TIME_MICRO);
  $config->array_set('users', $user['id'], 'chat_last_refresh', 0);

  $user_auth_level = isset($user['authlevel']) ? $user['authlevel'] : AUTH_LEVEL_ANONYMOUS;

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
  foreach(classSupernova::$design['smiles'] as $auth_level => $replaces) {
    if($auth_level > $user_auth_level) {
      continue;
    }

    foreach($replaces as $bbcode => $filename)
    {
      $template_result['.']['smiles'][] = array(
        'BBCODE' => $bbcode,
        'FILENAME' => $filename,
      );
    }
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
  global $skip_fleet_update, $config, $user;

  define('IN_AJAX', true);
  $skip_fleet_update = true;

  if($config->_MODE != CACHER_NO_CACHE && $config->chat_timeout && SN_TIME_MICRO - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout)
  {
    die();
  }

  if(($message = sys_get_param_str('message')) && $user['username'])
  {
    $ally_id = sys_get_param('ally') && $user['ally_id'] ? $user['ally_id'] : 0;
    $nick = db_escape(player_nick_compact(player_nick_render_current_to_array($user, array('color' => true, 'icons' => true, 'ally' => !$ally_id))));

    $message = preg_replace("#(?:https?\:\/\/(?:.+)?\/index\.php\?page\=battle_report\&cypher\=([0-9a-zA-Z]{32}))#", "[ube=$1]", $message);

    doquery("INSERT INTO {{chat}} (chat_message_sender_id, user, ally_id, message, timestamp) VALUES ('{$user['id']}', '{$nick}', '{$ally_id}', '{$message}', " . SN_TIME_NOW . ");");

    $config->array_set('users', $user['id'], 'chat_last_activity', SN_TIME_MICRO);
  }

  die();
}
function sn_chat_msg_view($template = null)
{
  global $config, $skip_fleet_update, $user, $lang;

  define('IN_AJAX', true);
  $skip_fleet_update = true;

  $history = sys_get_param_str('history');
  if(!$history)
  {
    $config->array_set('users', $user['id'], 'chat_last_refresh', SN_TIME_MICRO);
  }

  $page = 0;
  $last_message = '';
  $alliance = 0;
  $template_result['.']['chat'] = array();
  if(!$history && $config->_MODE != CACHER_NO_CACHE && $config->chat_timeout && SN_TIME_MICRO - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout)
  {
    $result['disable'] = true;
    $template_result['.']['chat'][] = array(
      'TIME' => date(FMT_DATE_TIME, htmlentities(SN_CLIENT_TIME_LOCAL, ENT_QUOTES, 'utf-8')),
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
    $query = doquery(
      "SELECT c.*, u.authlevel
      FROM
        {{chat}} AS c
        LEFT JOIN {{users}} AS u ON u.id = c.chat_message_sender_id
      WHERE c.chat_message_recipient_id IS NULL AND c.ally_id = '{$alliance}' {$where_add} ORDER BY messageid DESC LIMIT {$start_row}, {$page_limit};");
    while($chat_row = db_fetch($query))
    {
      // Little magik here - to retain HTML codes from DB and stripping HTML codes from nick
      $chat_row['user'] = player_nick_render_to_html($chat_row['user']);
      $nick_stripped = htmlentities(strip_tags($chat_row['user']), ENT_QUOTES, 'utf-8');
      $nick = str_replace(strip_tags($chat_row['user']), $nick_stripped, $chat_row['user']);
      if(!$history)
      {
        $nick = "<span style=\"cursor: pointer;\" onclick=\"addSmiley('({$nick_stripped})');\">{$nick}</span>";
      }

      $template_result['.']['chat'][] = array(
        'TIME' => BBCodeParser::parseStatic(date(FMT_DATE_TIME, $chat_row['timestamp'] + SN_CLIENT_TIME_DIFF), false, AUTH_LEVEL_REGISTERED),
        'NICK' => $nick,
        'TEXT' => BBCodeParser::parseStatic($chat_row['message'], false, intval($chat_row['authlevel'])),
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
