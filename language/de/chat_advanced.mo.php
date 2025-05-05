<?php

/*
#############################################################################
#  Filename: chat_advanced.mo.php
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massen-Mehrspieler-Online-Browser-Weltraumstrategiespiel
#
#  Copyright © 2012-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [German]
* @version 46a158
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'chat_advanced_chat_players' => 'Spieler im Chat',
  'chat_advanced_online_players' => 'Spieler online',
  'chat_advanced_online_invisibles' => 'Davon unsichtbar',
  'chat_advanced_invisibility' => 'Unsichtbarkeit',

  'chat_advanced_frame_on' => 'Anheften',
  'chat_advanced_frame_off' => 'Lösen',

  'chat_advanced_smile_tooltip' => 'Klicken um Smiley auszuwählen',

  'chat_advanced_visible' => array(
    0 => 'Sie sind für andere Spieler sichtbar',
    1 => 'Sie sind für andere Spieler unsichtbar',
  ),

  'chat_advanced_help_description' => "Verwenden Sie \"/help <Befehl>\" für detaillierte Informationen zu einem bestimmten Befehl. Zum Beispiel: \"/help whisper\"",
  'chat_advanced_help_commands_accessible' => 'Folgende Chat-Befehle stehen Ihnen zur Verfügung:',
  'chat_advanced_help_command' => 'Befehl "/%s"',
  'chat_advanced_help_command_aliases' => 'Aliase dieses Befehls: ',

  'chat_advanced_whisper_recipient_prefix' => '',
  'chat_advanced_whisper_recipient_midfix' => ' -> ',
  'chat_advanced_whisper_recipient_suffix' => '> ',
  'chat_advanced_whisper_sender_prefix' => '',
  'chat_advanced_whisper_sender_midfix' => ' -> ',
  'chat_advanced_whisper_sender_suffix' => '> ',

  'chat_advanced_command_reason' => '. Grund: %s',
  'chat_advanced_command_reason2' => 'Grund:',
  'chat_advanced_command_mute' => 'Spieler "%1$s" wurde bis %2$s Serverzeit gesperrt%3$s',
  'chat_advanced_command_unmute' => 'Spieler "%s" darf wieder chatten',
  'chat_advanced_command_ban' => 'Spieler "%1$s" wurde bis %2$s Serverzeit gebannt',
  'chat_advanced_command_ban_no_vacancy' => 'Spieler "%1$s" wurde OHNE URLAUBSMODUS bis %2$s Serverzeit gebannt',
  'chat_advanced_command_unban' => 'Spieler "%s" wurde entbannt',

  'chat_advanced_command_interval' => array(
    '1h' => '1 Stunde',
    '3h' => '3 Stunden',
    '6h' => '6 Stunden',
    '12h' => '12 Stunden',
    '1d' => '1 Tag',
    '3d' => '3 Tage',
    '1w' => '1 Woche',
    '2w' => '2 Wochen',
    '1m' => '30 Tage',
    '2m' => '60 Tage',
    '3m' => '90 Tage',
    '10y' => 'Permanent*',
  ),
  'chat_advanced_ban_vacancy' => 'Urlaubsmodus',

  'chat_advanced_online_ban' => 'Spieler "%1$s" bannen für...',
  'chat_advanced_online_mute' => 'Spieler "%1$s" chatten sperren für...',
  'chat_advanced_online_unmute' => 'Spieler "%1$s" Chatsperre aufheben',
  'chat_advanced_online_invisible' => 'Unsichtbarer Spieler',
  'chat_advanced_online_banned_via_chat' => 'Vom Chat gebannt',

  'chat_advanced_help' => array(
    'help' => "Der Befehl '/help' bietet detaillierte Hilfe zu allen verfügbaren Chat-Befehlen\r\n
               Syntax: /help [<Befehlsname>]\r\n
               <Befehlsname> ist optional. Ohne Angabe wird eine Befehlsliste angezeigt. Aliase sind möglich, z.B. '/help w' statt '/help whisper'",
    'whisper' => "Der Befehl '/whisper' sendet eine private Nachricht an einen bestimmten Spieler. Private Nachrichten erscheinen in allen Chat-Kanälen, sind aber nur für Sie und den Empfänger sichtbar.\r\n
                  Klicken Sie auf einen Namen in der Online-Liste, um den Befehl automatisch einzufügen.\r\n
                  Syntax: /whisper <Spielername> <Nachricht>\r\n
                  Bei Spielernamen mit Leerzeichen oder Sonderzeichen setzen Sie den Namen in Anführungszeichen:\r\n
                  /w \"Name mit Leerzeichen\" Hallo!",
    'ban' => "Der Befehl '/ban' sperrt einen Spieler für die angegebene Zeit. Das entsprechende Symbol in der Online-Liste bannt für 1 Woche.\r\n
              Syntax: /ban id <Spieler-ID> <Dauer>[!] [<Grund>]\r\n
              Die Spieler-ID sehen Sie beim Überfahren des Namens in der Online-Liste.\r\n
              <Dauer> Format: <Zahl>{y|m|w|d|h} (Jahre|Monate|Wochen|Tage|Stunden)\r\n
              Ein '!' deaktiviert den Urlaubsmodus.\r\n
              <Grund> ist optional und wird im Chat und den Ban-Protokollen angezeigt.",
    'unban' => "Der Befehl '/unban' hebt einen Bann auf.\r\n
                Syntax: /unban id <Spieler-ID>",
    'mute'  => "Der Befehl '/mute' sperrt einen Spieler für den Chat. Die Sperre gilt für alle Kanäle und private Nachrichten. Das entsprechende Symbol in der Online-Liste sperrt für 1 Stunde.\r\n
                Syntax: /mute id <Spieler-ID> <Dauer> [<Grund>]\r\n
                <Dauer> Format: <Zahl>{y|m|w|d|h}\r\n
                <Grund> ist optional und wird im Chat angezeigt.",
    'unmute' =>  "Der Befehl '/unmute' hebt eine Chat-Sperre auf.\r\n
                  Syntax: /unmute id <Spieler-ID>",
    'invisible' => "Der Befehl '/invisible' macht Sie im Chat unsichtbar. Das Häkchen \"Unsichtbarkeit\" hat denselben Effekt.\r\n
                    Die Unsichtbarkeit gilt global für alle Chat-Kanäle.\r\n
                    Syntax: /invisible [on|off]\r\n
                    /invisible on - Unsichtbar schalten\r\n
                    /invisible off - Sichtbar schalten\r\n
                    /invisible - Zeigt Ihren Unsichtbarkeitsstatus",
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

  'chat_advanced_err_command_inacessible' => 'Dieser Chat-Befehl ist nicht verfügbar. Nutzen Sie "/help" für eine Befehlsliste',
  'chat_advanced_err_command_unknown' => 'Unbekannter Befehl',
  'chat_advanced_err_player_name_unknown' => 'Spieler existiert nicht',
  'chat_advanced_err_message_empty' => 'Leere Nachrichten können nicht gesendet werden',
  'chat_advanced_err_message_player_empty' => 'Geben Sie einen Spielernamen für die Nachricht an',
  'chat_advanced_err_player_id_need' => 'Für diesen Befehl wird eine Spieler-ID benötigt',
  'chat_advanced_err_player_id_incorrect' => 'Ungültige ID',
  'chat_advanced_err_player_id_unknown' => 'Spieler-ID existiert nicht',
  'chat_advanced_err_player_same' => 'Dieser Befehl kann nicht auf sich selbst angewendet werden',
  'chat_advanced_err_player_higher' => 'Dieser Befehl kann nicht auf Spieler mit gleichen oder höheren Rechten angewendet werden',
  'chat_advanced_err_term_need' => 'Für diesen Befehl wird eine Dauer benötigt',
  'chat_advanced_err_term_wrong' => 'Ungültige Dauerangabe',
));