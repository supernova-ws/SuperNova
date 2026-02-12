<?php

/*
#############################################################################
#  Filename: chat_advanced.mo.php
#  Project: SuperNova.WS
#  Website: http://supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2012-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Chinese]
* @version 45d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

//$lang = array_merge($lang,
//$lang->merge(
$a_lang_array = (array(
  'chat_advanced_chat_players' => '聊天中的玩家',
  'chat_advanced_online_players' => '聊天中的玩家',
  'chat_advanced_online_invisibles' => '包括隐形',
  'chat_advanced_invisibility' => '隐形',

  'chat_advanced_frame_on' => '固定',
  'chat_advanced_frame_off' => '取消固定',

  'chat_advanced_smile_tooltip' => '点击选择表情符号',

  'chat_advanced_visible' => array(
    0 => '您对其他玩家可见',
    1 => '您对其他玩家隐形',
  ),

  'chat_advanced_help_description' => "使用语法 \"/help <命令>\" 获取有关特定命令的更多信息。例如，\"/help whisper\"",
  'chat_advanced_help_commands_accessible' => '您可以使用的聊天命令：',
  'chat_advanced_help_command' => '命令 "/%s"',
  'chat_advanced_help_command_aliases' => '此命令的别名： ',

  'chat_advanced_whisper_recipient_prefix' => '',
  'chat_advanced_whisper_recipient_midfix' => ' -> ',
  'chat_advanced_whisper_recipient_suffix' => '> ',
  'chat_advanced_whisper_sender_prefix' => '',
  'chat_advanced_whisper_sender_midfix' => ' -> ',
  'chat_advanced_whisper_sender_suffix' => '> ',

  'chat_advanced_command_reason' => '。原因：%s',
  'chat_advanced_command_reason2' => '原因：',
  'chat_advanced_command_mute' => '玩家 "%1$s" 被禁止在聊天中发言，直到服务器时间 %2$s%3$s',
  'chat_advanced_command_unmute' => '玩家 "%s" 再次可以在聊天中发言',
  'chat_advanced_command_ban' => '玩家 "%1$s" 被封禁并处于假期模式，直到服务器时间 %2$s',
  'chat_advanced_command_ban_no_vacancy' => '玩家 "%1$s" 被封禁且无假期模式，直到服务器时间 %2$s',
  'chat_advanced_command_unban' => '玩家 "%s" 已解封',

  'chat_advanced_command_interval' => array(
    '1h' => '1 小时',
    '3h' => '3 小时',
    '6h' => '6 小时',
    '12h' => '12 小时',
    '1d' => '1 天',
    '3d' => '3 天',
    '1w' => '1 周',
    '2w' => '2 周',
    '1m' => '30 天',
    '2m' => '60 天',
    '3m' => '90 天',
    '10y' => '永久*',
  ),
  'chat_advanced_ban_vacancy' => '假期模式',

  'chat_advanced_online_ban' => '封禁玩家 "%1$s" ...',
  'chat_advanced_online_mute' => '禁止玩家 "%1$s" 在聊天中发言 ...',
  'chat_advanced_online_unmute' => '允许玩家 "%1$s" 在聊天中发言',
  'chat_advanced_online_invisible' => '隐形玩家',
  'chat_advanced_online_banned_via_chat' => '从聊天中封禁',

  'chat_advanced_help' => array(
    'help' => "命令 '/help' 允许您获取有关所有可用聊天命令的详细帮助\r\n
               命令格式：/help [<命令名称>]\r\n
               <命令名称> 是可选参数。如果没有它，将打印可用命令列表。您可以使用别名代替命令名称，例如 '/help w' 代替 '/help whisper'",
    'whisper' => "命令 '/whisper' 允许您向特定玩家发送私信。私信出现在所有类型的聊天中 - 一般和联盟 - 并且只有您和接收它的玩家可见。点击在线玩家列表中的名称将相应的命令添加到消息行中 - 您只需输入消息并发送\r\n
                  可以向隐形玩家、不在聊天中的玩家和离线玩家发送私信。在后两种情况下，他们将在历史记录中看到您的消息\r\n
                  命令格式：/whisper <玩家名称> <消息>\r\n
                  如果玩家名称包含空格、撇号、正斜杠或反斜杠 - 请将玩家名称放在双引号中。例如：\r\n
                  /w \"带空格的名称\" 你好！",
    'ban' => "命令 '/ban' 在指定时间内阻止玩家访问游戏。在线玩家列表中的相应图标将封禁玩家 1 周\r\n
              命令格式：/ban id <玩家 ID> <封禁期限>[!] [<封禁原因>]\r\n
              可以通过将鼠标悬停在在线玩家列表中的昵称上来找到玩家 ID\r\n
              <封禁期限> 的形式为：<数字>{y|m|w|d|h}，其中 h 表示小时，d 表示天，w 表示周，m 表示月，y 表示年\r\n
              如果在封禁期限后加上感叹号，则用户将在没有假期模式的情况下被封禁\r\n
              <封禁原因> - 可选参数。如果指定 - 将添加到聊天消息和封禁表中",
    'unban' => "命令 '/unban' 允许您解封以前被封禁的玩家\r\n
                命令格式：/unban id <玩家 ID>",
    'mute'  => "命令 '/mute' 禁止玩家在聊天中发言。该禁令适用于所有频道，也适用于私人聊天消息。在线玩家列表中的相应图标禁止玩家在聊天中发言 1 小时\r\n
                命令格式：/mute id <玩家 ID> <禁令持续时间> [<禁令原因>]\r\n
                可以通过将鼠标悬停在在线玩家列表中的昵称上来找到玩家 ID\r\n
                <禁令持续时间> 的形式为：<数字>{y|m|w|d|h}，其中 h 表示持续时间（小时），d - 天，w - 周，m - 月，y - 年\r\n
                <禁令原因> - 可选参数。如果指定 - 将添加到聊天消息中",
    'unmute' =>  "命令 '/unmute' 允许您恢复以前被剥夺在聊天中发言权利的玩家的权利\r\n
                  命令格式：/unmute id <玩家 ID>\r\n
                  可以通过将鼠标悬停在在线玩家列表中的昵称上来找到玩家 ID",
    'invisible' => "命令 '/invisible' 允许您在聊天中对其他玩家隐形。切换“隐形”复选框具有相同的效果。\r\n
                    隐形是全局的 - 即它适用于一般聊天和联盟聊天\r\n
                    命令格式：/invisible [on|off]\r\n
                    /invisible on - 使您对聊天中的其他玩家隐形\r\n
                    /invisible off - 使您对聊天中的其他玩家可见\r\n
                    /invisible - 显示您的隐形状态",
  ),

  'chat_advanced_help_short' => array(
    'help' => '/help',
    'whisper' => '/whisper',
    'ban' => '/ban',
    'unban' => '/unban',
    'mute' => '/mute',
    'unmute' => '/unmute',
    'invisible' => '/invisible',
  ),



  'chat_advanced_err_command_inacessible' => '此聊天命令对您不可用。使用 "/help" 查看所有可用命令列表',
  'chat_advanced_err_command_unknown' => '未知命令',
  'chat_advanced_err_player_name_unknown' => '没有叫这个名字的玩家',
  'chat_advanced_err_message_empty' => '空消息将不会发送',
  'chat_advanced_err_message_player_empty' => '指定您要在聊天中向其发送消息的玩家名称',
  'chat_advanced_err_player_id_need' => '此命令需要玩家名称',
  'chat_advanced_err_player_id_incorrect' => '无效 ID',
  'chat_advanced_err_player_id_unknown' => '没有具有此 ID 的玩家',
  'chat_advanced_err_player_same' => '您不能对自己执行此命令',
  'chat_advanced_err_player_higher' => '您不能对具有更高或相同访问级别的玩家执行此命令',
  'chat_advanced_err_term_need' => '此命令需要持续时间',
  'chat_advanced_err_term_wrong' => '指定的命令持续时间无效',
));
