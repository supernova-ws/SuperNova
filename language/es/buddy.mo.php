<?php

/*
#############################################################################
#  Filename: buddy.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Juego de estrategia espacial masivo multijugador en línea
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
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
  'buddy_buddies' => 'Amigos',
  'buddy_request_text' => 'Texto de solicitud',
  'buddy_request_text_default' => 'Por favor, añádeme a tu lista de amigos',
  'buddy_request_none' => 'No tienes amigos ni solicitudes pendientes',
  'buddy_request_write_header' => 'Enviar solicitud de amistad',
  'buddy_request_player_name' => 'Nombre del jugador',
  'buddy_request_accept' => 'Añadir a la lista de amigos',

  'buddy_status' => 'Estado',
  'buddy_status_active' => 'Amistad mutua',
  'buddy_status_incoming_waiting' => 'Has recibido una solicitud de amistad',
  'buddy_status_incoming_denied' => 'Has rechazado la solicitud',
  'buddy_status_outcoming_waiting' => 'Solicitud enviada. Esperando respuesta',
  'buddy_status_outcoming_denied' => 'Tu solicitud ha sido rechazada',

  // Mensajes de resultado
  'buddy_err_not_exist' => 'La solicitud no existe. Puede que la hayas eliminado, rechazado o el autor la haya cancelado',

  'buddy_err_accept_own' => 'No puedes aceptar tu propia solicitud',
  'buddy_err_accept_alien' => 'No puedes aceptar una solicitud que no está dirigida a ti',
  'buddy_err_accept_already' => 'Ya has aceptado esta solicitud y eres amigo de este jugador',
  'buddy_err_accept_denied' => 'Ya has rechazado esta solicitud y no puedes aceptarla ahora',
  'buddy_err_accept_internal' => 'Error al procesar la solicitud. Inténtalo más tarde o contacta con los administradores',
  'buddy_err_accept_none' => 'Solicitud aceptada correctamente',

  'buddy_err_delete_alien' => '¡Esta solicitud no es tuya! No interfieras en las relaciones de otros. ¡Busca tus propios amigos!',
  'buddy_err_unfriend_none' => 'Has terminado la amistad',
  'buddy_err_delete_own' => 'Tu solicitud ha sido eliminada',
  'buddy_err_deny_none' => 'Has rechazado la amistad con este jugador',

  'buddy_err_adding_exists' => 'No puedes enviar solicitud: ya son amigos o existe una solicitud pendiente',
  'buddy_err_adding_none' => 'Solicitud de amistad enviada',
  'buddy_err_adding_self' => 'No puedes añadirte a ti mismo como amigo',

  // Mensajes privados
  'buddy_msg_accept_title' => '¡Tienes un nuevo amigo!',
  'buddy_msg_accept_text' => '¡El jugador %s te ha añadido a su lista de amigos!',
  'buddy_msg_unfriend_title' => '¡Has perdido un amigo!',
  'buddy_msg_unfriend_text' => 'El jugador %s ha roto su amistad contigo. ¡Qué triste!',
  'buddy_msg_deny_title' => 'Solicitud rechazada',
  'buddy_msg_deny_text' => 'El jugador %s ha rechazado tu solicitud de amistad',
  'buddy_msg_adding_title' => 'Solicitud de amistad',
  'buddy_msg_adding_text' => 'El jugador %s quiere ser tu amigo',

  'buddy_hint' => '
    <li>Envía solicitudes de amistad desde el menú <a href="search.php">Búsqueda</a></li>
    <li>Puedes ver el estado de tus amigos (online/offline), pero ellos también verán el tuyo. Tenlo en cuenta al aceptar solicitudes</li>
    <li>Si rechazas una solicitud, no podrás iniciar amistad hasta que el jugador elimine su petición</li>',
);