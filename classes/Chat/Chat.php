<?php
/**
 * Created by Gorlum 13.05.2020 13:02
 */

namespace Chat;


use classCache;
use Common\Traits\TSingleton;
use SN;
use SnTemplate;

class Chat {

  use TSingleton;

  protected $_chat_aliases = array(
    'h'         => 'help',
    '?'         => 'help',
    'help'      => 'help',
    'w'         => 'whisper',
    'whisper'   => 'whisper',
    'b'         => 'ban',
    'ban'       => 'ban',
    'ub'        => 'unban',
    'unban'     => 'unban',
    'm'         => 'mute',
    'mute'      => 'mute',
    'um'        => 'unmute',
    'unmute'    => 'unmute',
    'iv'        => 'invisible',
    'invisible' => 'invisible',
    // * /i /ignore
    // * /ck /kick
    // * /ci /invite
    // * /cj /join
    // * /cc /create
  );

  protected $_chat_commands = array(
    'invisible' => array(
      'access'  => array(0, 1, 2, 3, 4),
      'options' => array(
        CHAT_OPTION_SWITCH => CHAT_OPTION_SWITCH,
      ),
    ),
    'whisper'   => array(
      'access'  => array(0, 1, 2, 3, 4),
      'options' => array(),
    ),
    'mute'      => array(
      'access'  => array(1, 2, 3, 4),
      'options' => array(),
    ),
    'unmute'    => array(
      'access'  => array(1, 2, 3, 4),
      'options' => array(),
    ),
    'ban'       => array(
      'access'  => array(1, 2, 3, 4),
      'options' => array(),
    ),
    'unban'     => array(
      'access'  => array(1, 2, 3, 4),
      'options' => array(),
    ),
    'help'      => array(
      'access'  => array(0, 1, 2, 3, 4),
      'options' => array(),
    ),
  );


  public static function chatModel() {
    static::me()->_chatModel();
  }

  public static function chatAddModel() {
    static::me()->_chatAddModel();
  }

  public static function chatView($template = null) {
    return static::me()->_chatView($template);
  }

  public static function chatMsgView($template = null) {
    static::me()->_chatMsgView($template);
  }

  public static function chatFrameView($template = null) {
    static::me()->_chatFrameView($template);
  }

  protected function _chatView($template = null) {
    defined('IN_AJAX') or define('IN_AJAX', true);

    global $config, $lang;

    $iframe = sys_get_param_id('iframe');

//    $template = $this->addModuleTemplate('chat_body', $template);
    $template = SnTemplate::gettemplate('chat/chat_body', $template);
    $template->assign_var('CHAT_REFRESH_RATE', $config->chat_refresh_rate);
    $template->assign_var('CHAT_MODE', $iframe);
    $template->assign_var('MENU', !$iframe);
    $template->assign_var('NAVBAR', !$iframe);
    $template->assign_var('CHAT_IFRAME', $iframe);
    foreach ($lang['chat_advanced_command_interval'] as $interval => $locale) {
      $template->assign_block_vars('chat_advanced_command_interval', array(
        'INTERVAL' => $interval,
        'NAME'     => $locale,
      ));
    }

    return $template;
  }

  protected function _chatMsgView($template = null) {
    defined('IN_AJAX') or define('IN_AJAX', true);

    global $config, $user, $lang;


    $history = sys_get_param_str('history');
    if (!$history) {
      $config->array_set('users', $user['id'], 'chat_last_refresh', SN_TIME_MICRO);
      // $chat_player_row = $this->sn_chat_advanced_get_chat_player_record($user['id']);
      doquery("UPDATE {{chat_player}} SET `chat_player_refresh_last` = " . SN_TIME_NOW . " WHERE `chat_player_player_id` = {$user['id']} LIMIT 1;");
    }

    $page                         = 0;
    $last_message                 = '';
    $alliance                     = 0;
    $template_result['.']['chat'] = array();
    if (!$history && $config->getMode() != classCache::CACHER_NO_CACHE && $config->chat_timeout && SN_TIME_MICRO - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout) {
      $result['disable']              = true;
      $template_result['.']['chat'][] = array(
        'TIME'    => date(FMT_DATE_TIME, htmlentities(SN_TIME_NOW + SN_CLIENT_TIME_DIFF, ENT_QUOTES, 'utf-8')),
        'DISABLE' => true,
      );
    } else {
      $alliance = sys_get_param_str('ally') && $user['ally_id'] ? $user['ally_id'] : 0;

      $page_limit = sys_get_param_id('line_per_page', 20); // Chat rows Limit

      $where_add    = '';
      $last_message = 0;
      if ($history) {
        $rows       = doquery("SELECT count(1) AS CNT
          FROM {{chat}}
          WHERE
          (
            (ally_id = '{$alliance}' AND `chat_message_recipient_id` IS NULL) OR
            (ally_id = 0 AND `chat_message_recipient_id` = {$user['id']}) OR
            (ally_id = 0 AND `chat_message_sender_id` = {$user['id']} AND `chat_message_recipient_id` IS NOT NULL) OR
            (ally_id = 0 AND `chat_message_sender_id` IS NULL AND `chat_message_recipient_id` IS NULL)
          )
        ", true);
        $page_count = ceil($rows['CNT'] / $page_limit);

        for ($i = 1; $i <= $page_count; $i++) {
          $template_result['.']['page'][] = array(
            'NUMBER' => $i
          );
        }

        $page = min($page_count, max(1, sys_get_param_int('sheet')));
      } else {
        $last_message = sys_get_param_id('last_message');
        $where_add    = $last_message ? "AND `messageid` > {$last_message}" : '';
        $page         = 1;
      }

      $start_row = ($page - 1) * $page_limit;// OR `chat_message_recipient_id` = {$user['id']}
      $start_row = $start_row < 0 ? 0 : $start_row;
      $query     = doquery(
        "SELECT c.*, u.authlevel
        FROM
          {{chat}} AS c
          LEFT JOIN {{users}} AS u ON u.id = c.chat_message_sender_id
        WHERE
          (
            (c.ally_id = '{$alliance}' AND `chat_message_recipient_id` IS NULL) OR
            (c.ally_id = 0 AND `chat_message_recipient_id` = {$user['id']}) OR
            (c.ally_id = 0 AND `chat_message_sender_id` = {$user['id']} AND `chat_message_recipient_id` IS NOT NULL) OR
            (c.ally_id = 0 AND `chat_message_sender_id` IS NULL AND `chat_message_recipient_id` IS NULL)
          )
          {$where_add}
        ORDER BY messageid DESC
        LIMIT {$start_row}, {$page_limit}");
      while ($chat_row = db_fetch($query)) {
        $chat_row['user']               = player_nick_render_to_html($chat_row['user']);
        $nick_stripped                  = htmlentities(strip_tags($chat_row['user']), ENT_QUOTES, 'utf-8');
        $template_result['.']['chat'][] = array(
          'TIME'                => SN::$gc->bbCodeParser->expandBbCode(date(FMT_DATE_TIME, $chat_row['timestamp'] + SN_CLIENT_TIME_DIFF)),
          'NICK'                => $chat_row['user'],
          'NICK_STRIPPED'       => $nick_stripped,
          'TEXT'                => SN::$gc->bbCodeParser->expandBbCode($chat_row['message'], $chat_row['authlevel']),
          'SENDER_ID'           => $chat_row['chat_message_sender_id'],
          'SENDER_NAME'         => $safe_name = sys_safe_output($chat_row['chat_message_sender_name']),
          'SENDER_NAME_SAFE'    => $safe_name <> $chat_row['chat_message_sender_name'] || strpbrk($safe_name, ' /\\\'') ? '&quot;' . $safe_name . '&quot;' : $safe_name,
          'RECIPIENT_ID'        => $chat_row['chat_message_recipient_id'],
          'RECIPIENT_NAME'      => $safe_name_recipient = sys_safe_output($chat_row['chat_message_recipient_name']),
          'RECIPIENT_NAME_SAFE' => $safe_name_recipient <> $chat_row['chat_message_recipient_name'] || strpbrk($safe_name_recipient, ' /\\\'') ? '&quot;' . $safe_name_recipient . '&quot;' : $safe_name_recipient,
        );

        $last_message = max($last_message, $chat_row['messageid']);
      }
    }

    $template_result['.']['chat'] = array_reverse($template_result['.']['chat']);

    $template_result += array(
      'PAGE'        => $page,
      'PAGES_TOTAL' => $page_count,
      'PAGE_NEXT'   => $page < $page_count ? $page + 1 : $page,
      'PAGE_PREV'   => $page > 1 ? $page - 1 : $page,
      'ALLY'        => $alliance,
      'HISTORY'     => $history,
      'USER_ID'     => $user['id'],
    );

//    $template = $this->addModuleTemplate('chat_messages', $template);
    $template = SnTemplate::gettemplate('chat/chat_messages', $template);
    $template->assign_recursive($template_result);

    if ($history) {
      $pageTitle = "{$lang['chat_history']} - {$lang[$alliance ? 'chat_ally' : 'chat_common']}";
      SnTemplate::display($template, $pageTitle);
    } else {
      $result['last_message'] = $last_message;
      $result['html']         = SnTemplate::templateRenderToHtml($template);

//      $template = $this->addModuleTemplate('chat_online', $template);
      $template                              = SnTemplate::gettemplate('chat/chat_online', null);
//      $template->assign_recursive($template_result);
      $chat_online_players                   = 0;
      $chat_online_invisibles                = 0;
      $chat_player_invisible                 = 0;
      $template_result['.']['online_player'] = array();
      $ally_add                              = $alliance ? "AND u.ally_id = {$alliance}" : '';

      $chat_player_list = db_chat_player_list_online($config->chat_refresh_rate, $ally_add);

      // TODO: Добавить ограничение по бану
      while ($chat_player_row = db_fetch($chat_player_list)) {
        $chat_online_players++;
        if ($chat_player_row['chat_player_invisible']) {
          $chat_online_invisibles++;
        }

        if (!$chat_player_row['chat_player_invisible'] || $user['authlevel']) {
          $safe_name                               = sys_safe_output($chat_player_row['username']);
          $safe_name                               = $chat_player_row['username'] <> $safe_name || strpbrk($safe_name, ' /\\\'') !== false ? '&quot;' . $safe_name . '&quot;' : $safe_name;
          $template_result['.']['online_player'][] = array(
            'ID'          => $chat_player_row['id'],
            'NAME'        => player_nick_render_to_html($chat_player_row, array(
              'color' => true,
              'icons' => true,
              'ally'  => !$alliance,
              'class' => 'class="chat_nick_whisper" safe_name="' . $safe_name . '"',
            )),
            'NAME_SAFE'   => $safe_name,
            'MUTED'       => $chat_player_row['chat_player_muted'] && $chat_player_row['chat_player_muted'] >= SN_TIME_NOW ? date(FMT_DATE_TIME, $chat_player_row['chat_player_muted']) : false,
            'MUTE_REASON' => htmlentities($chat_player_row['chat_player_mute_reason'], ENT_COMPAT, 'UTF-8'),
            'INVISIBLE'   => $chat_player_row['chat_player_invisible'],
            'AUTH_LEVEL'  => $chat_player_row['authlevel'],
          );
        }
        if ($user['id'] == $chat_player_row['id']) {
          $chat_player_invisible = $chat_player_row['chat_player_invisible'];
        }
      }

      $chat_commands   = &$this->_chat_commands;
      $template_result = array_merge($template_result, array(
        'USER_ID'         => $user['id'],
        'USER_AUTHLEVEL'  => $user['authlevel'],
        'USER_CAN_BAN'    => in_array($user['authlevel'], $chat_commands['ban']['access']),
        'USER_CAN_MUTE'   => in_array($user['authlevel'], $chat_commands['mute']['access']),
        'USER_CAN_UNMUTE' => in_array($user['authlevel'], $chat_commands['unmute']['access']),
      ));
      $template->assign_recursive($template_result);
      $result['online'] = SnTemplate::templateRenderToHtml($template);

      $result['online_players']        = $chat_online_players;
      $result['online_invisibles']     = $chat_online_invisibles;
      $result['chat_player_invisible'] = $chat_player_invisible;
      $result['users_total']           = SN::$config->users_amount;
      $result['users_online']          = SN::$config->var_online_user_count;
      print(json_encode($result));
    }
    die();
  }

  protected function _chatFrameView($template = null) {
    defined('IN_AJAX') or define('IN_AJAX', true);

//    $template = $this->addModuleTemplate('chat_frame', $template);
    $template = SnTemplate::gettemplate('chat/chat_frame', $template);
    require_once($template->files['chat_frame']);
    die();
  }


  protected function _chatModel() {
    global $user, $template_result, $lang;

    $this->update_chat_activity();

    $mode = sys_get_param_int('mode');
    switch ($mode) {
      case CHAT_MODE_ALLY:
        $template_result['ALLY']      = intval($user['ally_id']);
        $template_result['CHAT_MODE'] = CHAT_MODE_ALLY;
        $page_title                   = $lang['chat_ally'];
      break;

      case CHAT_MODE_COMMON:
      default:
        $page_title                   = $lang['chat_common'];
        $template_result['CHAT_MODE'] = CHAT_MODE_COMMON;
      break;
    }

    $template_result['PAGE_HEADER'] = $page_title;

    $template_result['.']['smiles'] = array();

    foreach (SN::$gc->design->getSmilesList() as $auth_level => $replaces) {
      if ($auth_level > $user['authlevel']) {
        continue;
      }

      foreach ($replaces as $bbcode => $filename) {
        $template_result['.']['smiles'][] = array(
          'BBCODE'   => htmlentities($bbcode, ENT_COMPAT, 'UTF-8'),
          'FILENAME' => $filename,
        );
      }
    }
  }

  protected function _chatAddModel() {
    defined('IN_AJAX') or define('IN_AJAX', true);

    global $config, $user, $lang;
//    $chat_commands = get_unit_param(P_CHAT, P_CHAT_COMMANDS);
//    $chat_aliases  = get_unit_param(P_CHAT, P_CHAT_ALIASES);
    $chat_commands = &$this->_chat_commands;
    $chat_aliases  = &$this->_chat_aliases;

    if ($config->getMode() != classCache::CACHER_NO_CACHE && $config->chat_timeout && SN_TIME_MICRO - $config->array_get('users', $user['id'], 'chat_last_activity') > $config->chat_timeout) {
      die();
    }

    if (($message = sys_get_param_str_unsafe('message')) && $user['username']) {
      $this->update_chat_activity();

      $chat_message_sender_id      = 'NULL';
      $chat_message_sender_name    = '';
      $chat_message_recipient_id   = $user['id'];
      $chat_message_recipient_name = db_escape($user['username']);
      $nick                        = '';
      $ally_id                     = 0;
      $chat_command_issued         = '';

      $chat_player_row   = $this->sn_chat_advanced_get_chat_player_record($user['id']);
      $chat_player_muted = $chat_player_row['chat_player_muted'] && $chat_player_row['chat_player_muted'] >= SN_TIME_NOW ? $chat_player_row['chat_player_muted'] : false;
      if (preg_match("#^\/([\w\?]+)\s*({on|off|ID\s*[0-9]+|[а-яёА-ЯЁa-zA-Z0-9\_\-\[\]\(\)\+\{\}]+|\".+\"}*)*\s*(.*)#iu", $message, $chat_command_parsed)) {
        $chat_command_exists = array_key_exists(strtolower($chat_command_parsed[1]), $chat_aliases) ? true : strtolower($chat_command_parsed[1]);
        $chat_command_issued = $chat_command_exists === true ? $chat_aliases[$chat_command_parsed[1]] : 'help';
        if ($chat_command_accessible = in_array($user['authlevel'], $chat_commands[$chat_command_issued]['access'])) {
          switch ($chat_command_issued) {
            case 'invisible':
              //$chat_player_row = $this->sn_chat_advanced_get_chat_player_record($user['id'], '`chat_player_invisible`', true);
              $chat_directive = strtolower($chat_command_parsed[2]) == 'on' || $chat_command_parsed[2] == 1 ? 1 : (strtolower($chat_command_parsed[2]) == 'off' || (string)$chat_command_parsed[2] === '0' ? 0 : '');
              if ($chat_directive !== '') {
                doquery("UPDATE {{chat_player}} SET `chat_player_invisible` = {$chat_directive} WHERE `chat_player_player_id` = {$user['id']} LIMIT 1");
              } else {
                $chat_directive = $chat_player_row['chat_player_invisible'];
              }
              $message = "[c=lime]{$lang['chat_advanced_visible'][$chat_directive]}[/c]";
            break;

            case 'whisper':
              if ($chat_player_muted) {
                $chat_command_issued = '';
              } elseif ($chat_command_parsed[3] && $chat_command_parsed[2]) {
                $chat_command_parsed[2] = trim($chat_command_parsed[2], '"');
                $recipient_info         = db_user_by_username($chat_command_parsed[2]);
                $chat_command_parsed[2] = db_escape($chat_command_parsed[2]);
                if ($recipient_info['id']) {
                  $message                     = $chat_command_parsed[3];
                  $nick                        = db_escape(player_nick_compact(player_nick_render_current_to_array($user, array('color' => true, 'icons' => true, 'ally' => false))));
                  $chat_message_recipient_id   = $recipient_info['id'];
                  $chat_message_recipient_name = db_escape($recipient_info['username']);
                  $chat_message_sender_id      = $user['id'];
                  $chat_message_sender_name    = db_escape($user['username']);
                } else {
                  $message = "[c=red]{$lang['chat_advanced_err_player_name_unknown']}[/c]";
                }
              } elseif (!$chat_command_parsed[2]) {
                $message = "[c=red]{$lang['chat_advanced_err_message_player_empty']}[/c]";
              } elseif (!$chat_command_parsed[3]) {
                $message = "[c=red]{$lang['chat_advanced_err_message_empty']}[/c]";
              }
            break;

            case 'mute':
            case 'ban':
            case 'unmute':
            case 'unban':
              if ($chat_command_parsed[2] && ($chat_command_parsed[3] || $chat_command_issued == 'unmute' || $chat_command_issued == 'unban')) {
                $chat_command_parsed[2] = strtolower($chat_command_parsed[2]);
                if (strpos($chat_command_parsed[2], 'id ') !== false && is_id($player_id = substr($chat_command_parsed[2], 3))) {
                  $chat_player_subject = db_user_by_id($player_id, false, '`id`, `authlevel`, `username`');
                  if ($chat_player_subject) {
                    if ($chat_player_subject['id'] == $user['id']) {
                      $message = "[c=red]{$lang['chat_advanced_err_player_same']}[/c]";
                    } elseif ($chat_player_subject['authlevel'] >= $user['authlevel']) {
                      $message = "[c=red]{$lang['chat_advanced_err_player_higher']}[/c]";
                    } else {
                      $chat_message_recipient_id   = 'NULL';
                      $chat_message_recipient_name = '';
                      if ($chat_command_issued == 'unmute' || $chat_command_issued == 'unban') {
                        $temp = db_escape($chat_command_parsed[3]);
                        if ($chat_command_issued == 'unban') {
                          sys_admin_player_ban_unset($user, $chat_player_subject, $temp);
                          $message = $lang['chat_advanced_command_unban'];
                        } elseif ($chat_command_issued == 'unmute') {
                          doquery("UPDATE {{chat_player}} SET `chat_player_muted` = 0, `chat_player_mute_reason` = '{$temp}' WHERE `chat_player_player_id` = {$chat_player_subject['id']} LIMIT 1");
                          $message = $lang['chat_advanced_command_unmute'];
                        } else {
                          $message = '';
                        }

                        if ($message) {
                          $message = sprintf($message, $chat_player_subject['username']);
                          $message .= $chat_command_parsed[3] ? sprintf($lang['chat_advanced_command_reason'], $chat_command_parsed[3]) : '';
                          $message = "[c=lime]{$message}[/c]";
                        }
                      } elseif (preg_match("#(\d+)(y|m|w|d|h)(\!)?\s*(.*)#iu", $chat_command_parsed[3], $chat_command_parsed_two)) {
                        //TODO Localize [\s\pL\w]*
                        $date_to_timestamp = array(
                          'y' => PERIOD_YEAR,
                          'm' => PERIOD_MONTH,
                          'w' => PERIOD_WEEK,
                          'd' => PERIOD_DAY,
                          'h' => PERIOD_HOUR,
                        );
                        $this->sn_chat_advanced_get_chat_player_record($chat_player_subject['id'], '`chat_player_muted`', false);

                        $term                       = $date_to_timestamp[$chat_command_parsed_two[2]] * $chat_command_parsed_two[1];
                        $date_compiled              = $term + SN_TIME_NOW;
                        $chat_command_parsed_two[4] = db_escape($chat_command_parsed_two[4]);

                        doquery("UPDATE {{chat_player}} SET `chat_player_muted` = {$date_compiled}, `chat_player_mute_reason` = '{$chat_command_parsed_two[4]}' WHERE `chat_player_player_id` = {$chat_player_subject['id']} LIMIT 1");
                        if ($chat_command_issued == 'ban') {
                          sys_admin_player_ban($user, $chat_player_subject, $term, $chat_command_parsed_two[3] != '!', $chat_command_parsed_two[4]);
                          $message = $chat_command_parsed_two[3] == '!' ? $lang['chat_advanced_command_ban_no_vacancy'] : $lang['chat_advanced_command_ban'];
                        } else {
                          $message = $lang['chat_advanced_command_mute'];
                        }
//                        $message = sprintf($message, $chat_player_subject['username'], date(FMT_DATE_TIME, $date_compiled));
//                        $message .= $chat_command_parsed_two[4] ? sprintf($lang['chat_advanced_command_reason'], $chat_command_parsed_two[4]) : '';
                        $message = sprintf($message, $chat_player_subject['username'], date(FMT_DATE_TIME, $date_compiled), $chat_command_parsed_two[4] ? sprintf($lang['chat_advanced_command_reason'], $chat_command_parsed_two[4]) : '');
                        $message = "[c=red]{$message}[/c]";
                      } else {
                        $message = "[c=red]{$lang['chat_advanced_err_term_wrong']}[/c]";
                      }
                    }
                  } else {
                    $message = "[c=red]{$lang['chat_advanced_err_player_id_unknown']}[/c]";
                  }
                } else {
                  $message = "[c=red]{$lang['chat_advanced_err_player_id_incorrect']}[/c]";
                }
              } elseif (!$chat_command_parsed[2]) {
                $message = "[c=red]{$lang['chat_advanced_err_player_id_need']}[/c]";
              } elseif (!$chat_command_parsed[3]) {
                $message = "[c=red]{$lang['chat_advanced_err_term_need']}[/c]";
              }
            break;

            default:
              $message                = array();
              $chat_command_parsed[2] = strtolower($chat_command_parsed[2]);

              $chat_directive = $chat_command_parsed[2] && array_key_exists($chat_command_parsed[2], $chat_aliases) ? $chat_aliases[$chat_command_parsed[2]] : '';

              if (!$chat_directive) {
                $commands_available = array();
                $message[]          = $lang['chat_advanced_help_description'];
                foreach ($chat_commands as $chat_command_listed => $chat_command_info) {
                  if (in_array($user['authlevel'], $chat_command_info['access'])) {
                    $commands_available[] = $lang['chat_advanced_help_short'][$chat_command_listed];
                  }
                }
                $message[] = $lang['chat_advanced_help_commands_accessible'] . ' ' . implode(', ', $commands_available);
              } else {
                $message[] = sprintf($lang['chat_advanced_help_command'], $chat_directive);
                $message[] = $lang['chat_advanced_help'][$chat_directive];
                $aliases   = array();
                foreach ($chat_aliases as $chat_command_alias => $chat_command_real) {
                  if ($chat_command_real == $chat_directive) {
                    $aliases[] = '/' . $chat_command_alias;
                  }
                }
                $message[] = $lang['chat_advanced_help_command_aliases'] . implode(', ', $aliases);
              }
              $message = implode(chr(13) . chr(10), $message);
              $message = "[c=lime]{$message}[/c]";

              if ($chat_command_exists !== true) {
                $message = "[c=red]{$lang['chat_advanced_err_command_unknown']} \"/{$chat_command_exists}\"[/c]" . chr(13) . chr(10) . $message;
              }
            break;
          }
        } else {
          $message = "[c=red]{$lang['chat_advanced_err_command_inacessible']}[/c]";
        }
        $message = "[b]{$message}[/b]";
      }

      if (!$chat_command_issued && !$chat_player_muted) {
        $chat_message_sender_id      = $user['id'];
        $chat_message_sender_name    = db_escape($user['username']);
        $chat_message_recipient_id   = 'NULL';
        $chat_message_recipient_name = '';
        $ally_id                     = sys_get_param('ally') && $user['ally_id'] ? $user['ally_id'] : 0;
        $nick                        = db_escape(player_nick_compact(player_nick_render_current_to_array($user, array('color' => true, 'icons' => true, 'ally' => !$ally_id, 'class' => 'class="chat_nick_msg"'))));

        // Replacing news://xxx link with BBCode
        $message = preg_replace("#news\:\/\/(\d+)#", "[news=$1]", $message);
        // Replacing news URL with BBCode
        $message = preg_replace("#(?:https?\:\/\/(?:.+)?\/announce\.php\?id\=(\d+))#", "[news=$1]", $message);
        $message = preg_replace("#(?:https?\:\/\/(?:.+)?\/index\.php\?page\=battle_report\&cypher\=([0-9a-zA-Z]{32}))#", "[ube=$1]", $message);

        if ($color = sys_get_param_str('color')) {
          $message = "[c={$color}]{$message}[/c]";
        }
      } elseif (!$chat_command_issued && $chat_player_muted) {
        $chat_message_recipient_id   = $user['id'];
        $chat_message_recipient_name = db_escape($user['username']);
        $message                     = sprintf($lang['chat_advanced_command_mute'], $user['username'], date(FMT_DATE_TIME, $chat_player_muted)) .
          ($chat_player_row['chat_player_muted_reason'] ? sprintf($lang['chat_advanced_command_mute_reason'], $chat_player_row['chat_player_muted_reason']) : '');
        $message                     = "[c=red]{$message}[/c]";
      }
      $message = db_escape($message);

      doquery(
        "INSERT INTO
          {{chat}}
        SET
          `user` = '{$nick}',
          `ally_id` = '{$ally_id}',
          `message` = '{$message}',
          `timestamp` = " . SN_TIME_NOW . ",
          `chat_message_sender_id` = {$chat_message_sender_id},
          `chat_message_sender_name` = '{$chat_message_sender_name}',
          `chat_message_recipient_id` = {$chat_message_recipient_id},
          `chat_message_recipient_name` = '{$chat_message_recipient_name}'"
      );
    }

    die();
  }


  protected function update_chat_activity($refresh = false) {
    global $config, $user;

    $config->array_set('users', $user['id'], 'chat_last_activity', SN_TIME_MICRO);
    $config->array_set('users', $user['id'], 'chat_last_refresh', $refresh ? 0 : SN_TIME_MICRO);

    $activity_row = doquery("SELECT `chat_player_id` FROM {{chat_player}} WHERE `chat_player_player_id` = {$user['id']} LIMIT 1", true);
    if (!$activity_row) {
      doquery("INSERT INTO {{chat_player}} SET `chat_player_player_id` = {$user['id']}");
    } else {
      doquery("UPDATE {{chat_player}} SET `chat_player_activity` = '" . SN::$db->db_escape(SN_TIME_SQL) . "' WHERE `chat_player_player_id` = {$user['id']} LIMIT 1");
    }
  }

  protected function sn_chat_advanced_get_chat_player_record($player_id, $fields = '*', $return_data = true) {
    $result = false;
    if($player_id) {
      if(!($result = doquery("SELECT {$fields} FROM {{chat_player}} WHERE `chat_player_player_id` = {$player_id} LIMIT 1", true))) {
        doquery("INSERT INTO {{chat_player}} SET `chat_player_player_id` = {$player_id}");
        if($return_data) {
          $result = doquery("SELECT {$fields} FROM {{chat_player}} WHERE `chat_player_player_id` = {$player_id} LIMIT 1", true);
        }
      }
    }

    return $result;
  }


}
