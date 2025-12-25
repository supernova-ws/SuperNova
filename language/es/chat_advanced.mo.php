<?php

/*
#############################################################################
#  Filename: chat_advanced.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Juego de estrategia espacial masivo multijugador en línea
#
#  Copyright © 2012-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Spanish]
* @version 46d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = array(
  'chat_advanced_chat_players' => 'Jugadores en el chat',
  'chat_advanced_online_players' => 'Jugadores en línea',
  'chat_advanced_online_invisibles' => 'Incluyendo invisibles',
  'chat_advanced_invisibility' => 'Invisibilidad',

  'chat_advanced_frame_on' => 'Fijar',
  'chat_advanced_frame_off' => 'Desfijar',

  'chat_advanced_smile_tooltip' => 'Haz clic para seleccionar un emoticono',

  'chat_advanced_visible' => array(
    0 => 'Eres visible para otros jugadores',
    1 => 'Eres invisible para otros jugadores',
  ),

  'chat_advanced_help_description' => "Usa el comando \"/help <comando>\" para obtener información detallada. Ejemplo: \"/help whisper\"",
  'chat_advanced_help_commands_accessible' => 'Comandos de chat disponibles:',
  'chat_advanced_help_command' => 'Comando "/%s"',
  'chat_advanced_help_command_aliases' => 'Alias: ',

  'chat_advanced_whisper_recipient_prefix' => '',
  'chat_advanced_whisper_recipient_midfix' => ' -> ',
  'chat_advanced_whisper_recipient_suffix' => '> ',
  'chat_advanced_whisper_sender_prefix' => '',
  'chat_advanced_whisper_sender_midfix' => ' -> ',
  'chat_advanced_whisper_sender_suffix' => '> ',

  'chat_advanced_command_reason' => '. Razón: %s',
  'chat_advanced_command_reason2' => 'Razón:',
  'chat_advanced_command_mute' => 'Jugador "%1$s" silenciado hasta %2$s (hora del servidor)%3$s',
  'chat_advanced_command_unmute' => 'Jugador "%s" puede escribir nuevamente',
  'chat_advanced_command_ban' => 'Jugador "%1$s" baneado (modo vacaciones) hasta %2$s',
  'chat_advanced_command_ban_no_vacancy' => 'Jugador "%1$s" baneado SIN MODO VACACIONES hasta %2$s',
  'chat_advanced_command_unban' => 'Jugador "%s" desbaneado',

  'chat_advanced_command_interval' => array(
    '1h' => '1 hora',
    '3h' => '3 horas',
    '6h' => '6 horas',
    '12h' => '12 horas',
    '1d' => '1 día',
    '3d' => '3 días',
    '1w' => '1 semana',
    '2w' => '2 semanas',
    '1m' => '30 días',
    '2m' => '60 días',
    '3m' => '90 días',
    '10y' => 'Permanentemente*',
  ),
  'chat_advanced_ban_vacancy' => 'Modo vacaciones',

  'chat_advanced_online_ban' => 'Banear a "%1$s" por...',
  'chat_advanced_online_mute' => 'Silenciar a "%1$s" por...',
  'chat_advanced_online_unmute' => 'Permitir que "%1$s" escriba',
  'chat_advanced_online_invisible' => 'Jugador invisible',
  'chat_advanced_online_banned_via_chat' => 'Baneado desde el chat',

  'chat_advanced_help' => array(
    'help' => "El comando '/help' muestra información sobre comandos disponibles\r\n
               Uso: /help [<comando>]\r\n
               Sin parámetros muestra la lista completa. Ejemplo: '/help w' equivale a '/help whisper'",
    'whisper' => "El comando '/whisper' envía mensajes privados\r\n
                  Visibles solo para el remitente y destinatario, incluso si están offline\r\n
                  Uso: /whisper <nombre> <mensaje>\r\n
                  Nombres con espacios: /w \"nombre largo\" Hola!",
    'ban' => "El comando '/ban' restringe el acceso al juego\r\n
              Uso: /ban id <ID> <tiempo>[!] [razón]\r\n
              <tiempo> formato: <número>{y|m|w|d|h}\r\n
              ¡Sin modo vacaciones!",
    'unban' => "El comando '/unban' elimina restricciones\r\n
                Uso: /unban id <ID>",
    'mute'  => "El comando '/mute' prohíbe escribir en el chat\r\n
                Uso: /mute id <ID> <tiempo> [razón]\r\n
                <tiempo> formato: <número>{y|m|w|d|h}",
    'unmute' =>  "El comando '/unmute' restaura permisos de chat\r\n
                  Uso: /unmute id <ID>",
    'invisible' => "El comando '/invisible' controla tu visibilidad\r\n
                    Uso: /invisible [on|off]\r\n
                    Sin parámetros muestra el estado actual",
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

  'chat_advanced_err_command_inacessible' => 'Comando no disponible. Usa "/help" para ver la lista',
  'chat_advanced_err_command_unknown' => 'Comando desconocido',
  'chat_advanced_err_player_name_unknown' => 'Jugador no encontrado',
  'chat_advanced_err_message_empty' => 'No se pueden enviar mensajes vacíos',
  'chat_advanced_err_message_player_empty' => 'Especifica el nombre del destinatario',
  'chat_advanced_err_player_id_need' => 'Se requiere ID de jugador',
  'chat_advanced_err_player_id_incorrect' => 'ID incorrecto',
  'chat_advanced_err_player_id_unknown' => 'ID no existe',
  'chat_advanced_err_player_same' => 'No puedes usarlo contigo mismo',
  'chat_advanced_err_player_higher' => 'No puedes usarlo con jugadores de mayor nivel',
  'chat_advanced_err_term_need' => 'Se requiere duración',
  'chat_advanced_err_term_wrong' => 'Duración incorrecta',
);