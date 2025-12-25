<?php

/*
#############################################################################
#  Filename: buddy.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massen-Mehrspieler-Online-Browser-Weltraumstrategiespiel
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [German]
* @version 46d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'buddy_buddies' => 'Freunde',
  'buddy_request_text' => 'Anfragetext',
  'buddy_request_text_default' => 'Ich bitte um Aufnahme in Ihre Freundesliste',
  'buddy_request_none' => 'Keine Freunde oder Freundschaftsanfragen vorhanden',
  'buddy_request_write_header' => 'Freundschaftsanfrage senden',
  'buddy_request_player_name' => 'Spielername',
  'buddy_request_accept' => 'Spieler zur Freundesliste hinzufügen',

  'buddy_status' => 'Status',
  'buddy_status_active' => 'Dies ist Ihr gegenseitiger Freund',
  'buddy_status_incoming_waiting' => 'Sie haben eine Freundschaftsanfrage erhalten',
  'buddy_status_incoming_denied' => 'Sie haben die Freundschaftsanfrage abgelehnt',
  'buddy_status_outcoming_waiting' => 'Ihre Anfrage wurde gesendet. Bitte warten Sie auf Antwort',
  'buddy_status_outcoming_denied' => 'Ihre Anfrage wurde abgelehnt',

  // Ergebnisnachrichten
  'buddy_err_not_exist' => 'Die angegebene Anfrage existiert nicht. Möglicherweise wurde sie gelöscht, abgelehnt oder vom Absender zurückgezogen',

  'buddy_err_accept_own' => 'Sie können Ihre eigene Anfrage nicht annehmen',
  'buddy_err_accept_alien' => 'Sie können keine Anfrage annehmen, die nicht an Sie gerichtet ist',
  'buddy_err_accept_already' => 'Sie haben diese Anfrage bereits angenommen und sind mit diesem Spieler befreundet',
  'buddy_err_accept_denied' => 'Sie haben diese Anfrage bereits abgelehnt und können sie nun nicht mehr annehmen',
  'buddy_err_accept_internal' => 'Bei der Annahme der Anfrage ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut. Falls der Fehler bestehen bleibt, wenden Sie sich an die Serveradministration',
  'buddy_err_accept_none' => 'Anfrage erfolgreich angenommen',

  'buddy_err_delete_alien' => 'Diese Anfrage wurde nicht von Ihnen erstellt! Mischen Sie sich nicht in die Beziehungen anderer ein! Suchen Sie sich lieber eigene Freunde!',
  'buddy_err_unfriend_none' => 'Sie haben die Freundschaft beendet',
  'buddy_err_delete_own' => 'Ihre Anfrage wurde erfolgreich gelöscht',

  'buddy_err_deny_none' => 'Sie haben die Freundschaft mit diesem Spieler abgelehnt. Warum?',

  'buddy_err_adding_exists' => 'Anfrage nicht möglich - Sie sind bereits befreundet oder es existieren bereits Freundschaftsanfragen zwischen Ihnen',
  'buddy_err_adding_none' => 'Ihre Freundschaftsanfrage wurde gesendet',
  'buddy_err_adding_self' => 'Sie können sich nicht selbst als Freund hinzufügen',

  // PN-Nachrichten
  'buddy_msg_accept_title' => 'Sie haben einen neuen Freund!',
  'buddy_msg_accept_text' => 'Spieler %s hat Sie zu seiner Freundesliste hinzugefügt!',
  'buddy_msg_unfriend_title' => 'Sie haben einen Freund verloren!',
  'buddy_msg_unfriend_text' => 'Spieler %s hat die Freundschaft mit Ihnen beendet und Sie von seiner Freundesliste entfernt. Wie traurig...',
  'buddy_msg_deny_title' => 'Freundschaftsanfrage abgelehnt',
  'buddy_msg_deny_text' => 'Spieler %s möchte nicht mit Ihnen befreundet sein',
  'buddy_msg_adding_title' => 'Freundschaftsanfrage',
  'buddy_msg_adding_text' => 'Spieler %s möchte mit Ihnen befreundet sein',

  'buddy_hint' => '
    <li>Freundschaftsanfragen können über das Menü <a href="search.php">Suche</a> gesendet werden</li>
    <li>Sie können den Status Ihrer Freunde sehen - ob sie online oder offline sind. Beachten Sie jedoch, dass auch Ihre Freunde Ihren Status sehen können. Bedenken Sie dies, bevor Sie Freundschaftsanfragen annehmen.</li>
    <li>Wenn Sie eine Freundschaftsanfrage abgelehnt haben, können Sie erst dann eine Freundschaft mit diesem Spieler eingehen, wenn er seine Anfrage löscht</li>',
));