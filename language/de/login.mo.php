<?php

/*
#############################################################################
#  Filename: login.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massen-Mehrspieler-Online-Browser-Weltraum-Strategiespiel
#
#  Copyright © 2009-2018 Gorlum für Projekt "SuperNova.WS"
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

global $config;

$a_lang_array = (array(
  'Login' => 'Anmeldung',
  'User_name' => 'Benutzername:',
  'Authorization' => 'Autorisierung',
  'Please_Login' => 'Bitte <a href="login.php" target="_main">anmelden...</a>',
  'Please_Wait' => 'Bitte warten',
  'Remember_me' => 'Angemeldet bleiben',
  'Register' => 'Fehlerinformation',
  'Login_Error' => 'Fehler',
  'PleaseWait' => 'Bitte warten',
  'PasswordLost' => 'Passwort zurücksetzen',
  'Login_Ok' => 'Erfolgreich verbunden, <a href="./"><blink>Weiterleitung...</blink></a><br><center><img src="design/images/progressbar.gif"></center>',
  'Login_FailPassword' => 'Falscher Benutzername und/oder Passwort<br /><a href="login.php" target="_top">Zurück</a>',
  'Login_FailUser' => 'Dieser Spieler existiert nicht.<br><a href=login.php>Zurück</a>',
  'log_univ' => 'Willkommen in unserem Universum!',
  'log_reg' => 'Registrieren',
  'log_reg_main' => 'Registrieren',
  'log_menu' => 'Menü',
  'log_stat_menu' => 'Statistik',
  'log_enter' => 'Einloggen',
  'log_news' => 'Server-Neuigkeiten',
  'log_cred' => 'Über den Server',
  'log_faq' => 'Spiel-FAQ',
  'log_forums' => 'Forum',
  'log_contacts' => 'Administration',
  'log_desc' => '<strong>SuperNova ist ein Online-Multiplayer-Browser-Weltraum-Strategiespiel.</strong> Tausende von Spielern treten gleichzeitig gegeneinander an. Für das Spiel benötigen Sie nur einen normalen Browser.',
  'log_toreg' => 'Jetzt registrieren!',
  'log_online' => 'Spieler online',
  'log_lastreg' => 'Neuling',
  'log_numbreg' => 'Gesamtkonten',
  'log_welcome' => 'Willkommen in',
  'vacation_mode' => 'Sie sind im Urlaubsmodus<br> Urlaubsmodus kann in deaktiviert werden ',
  'hours' => ' Stunden',
  'vacations' => 'Urlaubsmodus',
  'log_scr1' => 'Screenshot der Werft, hier werden Schiffe für den aktuellen Planeten gebaut und bestellt. Klicken Sie auf das Bild, um es zu vergrößern.',
  'log_scr2' => 'Screenshot der Statistik, hier wird Ihre Bewertung im Vergleich zu anderen Spielern nach verschiedenen Parametern angezeigt. Klicken Sie auf das Bild, um es zu vergrößern.',
  'log_scr3' => 'Screenshot des Universums, hier können Sie Ihren Planeten im Universum sehen sowie Planeten anderer Spieler finden. Klicken Sie auf das Bild, um es zu vergrößern.',
  'log_rules' => 'Spielregeln',
  'log_banned' => 'Liste der gebannten Spieler',
  'log_see_you' => 'Wir hoffen, Sie wieder in den Weiten unseres Universums zu sehen. Viel Glück!<br><a href="login.php">Zur Anmeldeseite</a>',
  'log_session_closed' => 'Sitzung beendet.',
  'registry' => 'Registrierung',
  'form' => 'Registrierungsformular',
  'Undefined' => '- nicht definiert -',
  'Male' => 'Männlich',
  'Female' => 'Weiblich',
  'Multiverse' => 'XNova',
  'E-Mail' => 'E-Mail-Adresse',
  'MainPlanet' => 'Name des Hauptplaneten',
  'GameName' => 'Name',
  'gender' => 'Geschlecht',
  'accept' => 'Ich stimme den Regeln zu',
  'reg_i_agree' => 'Ich habe die gelesen und stimme den',
  'reg_with_rules' => 'Spielregeln',
  'signup' => 'Registrieren',
  'Languese' => 'Sprache',
  'log_reg_text0' => 'Bitte lesen Sie die vor der Registrierung',
  'log_reg_text1' => 'Die Registrierung bedeutet, dass Sie alle Punkte der Regeln vollständig gelesen und zugestimmt haben. Wenn Sie mit irgendeinem Punkt der Regeln nicht einverstanden sind - bitte registrieren Sie sich nicht.',
  'thanksforregistry' => 'Herzlichen Glückwunsch zur erfolgreichen Registrierung! Sie werden in 10 Sekunden zur Hauptseite Ihres Planeten weitergeleitet. Falls dies nicht geschieht, klicken Sie auf diesen <a href=overview.php><u>Link!</u></a>',
  'welcome_to_universe' => 'Willkommen bei OGame!!!',
  'please_click_url' => 'Um das Konto zu nutzen, müssen Sie es aktivieren, indem Sie auf diesen Link klicken',
  'regards' => 'Viel Glück!',
  'error_lang' => 'Diese Sprache wird nicht unterstützt!<br />',
  'error_mail' => 'Ungültige E-Mail!<br />',
  'error_planet' => 'Ein anderer Planet hat bereits denselben Namen!<br />',
  'error_hplanetnum' => 'Der Planetname darf NUR mit lateinischen Buchstaben geschrieben werden!<br />',
  'error_character' => 'Ungültiger Name!<br />',
  'error_charalpha' => 'Sie dürfen NUR lateinische Buchstaben verwenden!<br />',
  'error_password' => 'Das Passwort muss mindestens 4 Zeichen lang sein!<br />',
  'error_rgt' => 'Sie müssen den Regeln zustimmen!<br />',
  'error_userexist' => 'Dieser Name wird bereits verwendet!<br />',
  'error_emailexist' => 'Diese E-Mail wird bereits verwendet!<br />',
  'error_sex' => 'Fehler bei der Geschlechtsauswahl!<br />',
  'error_mailsend' => 'Fehler beim Senden der E-Mail, Ihr Passwort: ',
  'reg_welldone' => 'Registrierung abgeschlossen! Ihr Passwort wurde an die bei der Registrierung angegebene E-Mail-Adresse gesendet. Hier ist es noch einmal zur Sicherheit:<br>',
  'error_captcha' => 'Falscher grafischer Code!<br/>',
  'error_v' => 'Bitte versuchen Sie es erneut!<br />',
  'log_login_page' => 'Zum Spiel anmelden',
  'log_reg_already' => 'Bereits registriert? Nutzen Sie den Link ',
  'log_reg_already_lost' => 'Passwort vergessen? Nutzen Sie den Link ',

  'log_lost_header' => 'Passwort zurücksetzen',
  'log_lost_code' => 'Bestätigungscode',
  'log_lost_description2' => 'Wenn Sie einen Bestätigungscode haben, geben Sie ihn unten ein und klicken Sie auf "Passwort zurücksetzen". Eine E-Mail mit einem neuen Passwort wird an Ihre E-Mail-Adresse gesendet.<br /><br />
    Wenn Sie bereits einen Bestätigungscode angefordert haben, aber keine E-Mail in Ihrem Posteingang sehen - überprüfen Sie den Spam-Ordner. Ihr E-Mail-Server könnte unsere E-Mail als "unerwünscht" markiert haben.<br /><span style="color: red;">ACHTUNG! mail.ru und seine Projekte blockieren E-Mails vom Spiel! Schreiben Sie eine E-Mail an den Admin - siehe unten</span><br /><br />
    Wenn Sie dort auch keine E-Mail finden - schreiben Sie eine E-Mail an die Serveradministration an <span class="ok">' . $config->server_email . '</span>',
  'log_lost_reset_pass' => 'Passwort zurücksetzen',
  'log_lost_send_mail' => 'Bestätigungscode senden',
  'log_lost_sent_code' => 'Eine E-Mail mit weiteren Anweisungen zum Zurücksetzen des Passworts wurde an die angegebene E-Mail gesendet',
  'log_lost_sent_pass' => 'Eine E-Mail mit Ihrem neuen Passwort wurde ebenfalls an Ihre E-Mail-Adresse gesendet',

  'log_lost_err_email' => 'Die angegebene E-Mail ist nicht in der Datenbank registriert. Dies kann eines der folgenden bedeuten:<br>Sie haben sich bei der Eingabe der E-Mail geirrt. Gehen Sie zurück und versuchen Sie es erneut<br>Ihr Konto wurde aufgrund von Inaktivität gelöscht. Registrieren Sie sich neu<br>Sie versuchen, sich in einem falschen Spieluniversum anzumelden. Überprüfen Sie den Namen des aktuellen Universums und melden Sie sich gegebenenfalls im richtigen Universum an',
  'log_lost_err_sending' => 'Fehler beim Senden der Nachricht an die angegebene E-Mail. Melden Sie den Fehler dem Administrator',
  'log_lost_err_code' => 'Der angegebene Bestätigungscode ist nicht in der Datenbank registriert. Dies kann eines der folgenden bedeuten:<br>Sie haben sich bei der Eingabe des Bestätigungscodes geirrt. Gehen Sie zurück und geben Sie den Code sorgfältig ein<br>Sie versuchen, den Bestätigungscode in einem anderen Universum einzugeben, als dem, für das er generiert wurde. Überprüfen Sie den Namen des aktuellen Universums<br>Ihr Konto wurde aufgrund von Inaktivität gelöscht. Registrieren Sie sich neu<br>Der Bestätigungscode ist abgelaufen. Überprüfen Sie das Ablaufdatum in der E-Mail. Wenn es abgelaufen ist, fordern Sie einen neuen Bestätigungscode an',
  'log_lost_err_admin' => 'Mitglieder des Serverteams (Moderatoren, Operatoren, Administratoren usw.) können die Passwortzurücksetzungsfunktion nicht nutzen. Wenden Sie sich an den Serveradministrator, um Ihr Passwort zu ändern',
  'log_lost_err_change' => 'Fehler beim Ändern des Passworts in der Datenbank. Melden Sie den Fehler dem Administrator',

  'log_lost_description1' => 'Geben Sie die primäre E-Mail-Adresse ein, mit der Ihr Konto registriert ist. Eine E-Mail mit einem Bestätigungscode zum Zurücksetzen Ihres Passworts wird an diese Adresse gesendet',
  'login_register_offer' => 'Klicken Sie hier, um sich zu registrieren',
  'login_password_restore_offer' => 'Klicken Sie hier, um Ihr Passwort zurückzusetzen',

  'login_register_email_hint' => 'Geben Sie eine funktionierende E-Mail-Adresse an - der Inhaber der angegebenen E-Mail-Adresse gilt als Kontoinhaber<br />
    <span style="color: red;">ACHTUNG! Verwenden Sie keine E-Mail-Adressen von "@mail.ru" und seinen Projekten! Sie werden keine E-Mails vom Spiel erhalten!</span>',

  'login_account_name_or_email' => 'E-Mail',

));