<?php
/** @noinspection HtmlUnknownTarget */

/*
#############################################################################
#  Filename: options.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
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

$a_lang_array = [
  'opt_account' => 'Profil',
  'opt_int_options' => 'Interface',
  'opt_settings_statistics' => 'Spielerstatistiken',
  'opt_settings_info' => 'Spielerinformation',
  'opt_alerts' => 'Benachrichtigungen',
  'opt_common' => 'Allgemein',
  'opt_tutorial' => 'Tutorial',

  'opt_birthday' => 'Geburtstag',

  'opt_header' => 'Benutzereinstellungen',
  'opt_messages' => 'Automatische Benachrichtigungen',
  'opt_msg_saved' => 'Einstellungen erfolgreich geändert',
  'opt_msg_name_changed' => 'Benutzername erfolgreich geändert',
  'opt_msg_name_change_err_used_name' => 'Dieser Name gehört bereits einem anderen Spieler',
  'opt_msg_name_change_err_no_dm' => 'Nicht genug DM für Namensänderung',

  'username_old' => 'Aktueller Name',
  'username_new' => 'Neuer Name',
  'username_change_confirm' => 'Namen ändern',
  'username_change_confirm_payed' => 'für',

  'opt_msg_pass_changed' => 'Passwort erfolgreich geändert',
  'opt_err_pass_wrong' => 'Falsches aktuelles Passwort. Passwort wurde nicht geändert',
  'opt_err_pass_unmatched' => 'Die eingegebenen Passwörter stimmen nicht überein. Passwort wurde nicht geändert',
  'changue_pass' => 'Passwort ändern',
  'Download' => 'Download',
  'userdata' => 'Information',
  'username' => 'Name',
  'lastpassword' => 'Altes Passwort',
  'newpassword' => 'Neues Passwort<br>(min. 8 Zeichen)',
  'newpasswordagain' => 'Neues Passwort wiederholen',
  'emaildir' => 'E-Mail-Adresse',
  'emaildir_tip' => 'Diese Adresse kann jederzeit geändert werden. Die Adresse wird zur Hauptadresse, wenn sie innerhalb von 7 Tagen nicht geändert wird.',
  'permanentemaildir' => 'Haupt-E-Mail-Adresse',
  'opt_planet_sort_title' => 'Planeten sortieren nach',
  'opt_planet_sort_options' => [
    SORT_ID       => 'Kolonisierungszeit',
    SORT_LOCATION => 'Koordinaten',
    SORT_NAME     => 'Alphabet',
    SORT_SIZE     => 'Feldanzahl',
  ],
  'opt_planet_sort_ascending' => [
    SORT_ASCENDING  => 'Aufsteigend',
    SORT_DESCENDING => 'Absteigend',
  ],

  'opt_navbar_title' => 'Navigationsleiste',
  'opt_navbar_description' => 'Die Navigationsleiste (kurz "Navbar") befindet sich oben auf dem Bildschirm. Dieser Abschnitt ermöglicht die Anpassung der Navbar.',
  'opt_navbar_resourcebar_description' => 'Ressourcenleiste - Ressourcenpanel',
  'opt_navbar_buttons_title' => 'Navbar-Schaltflächen Einstellungen',
  'opt_player_options' => [
    PLAYER_OPTION_NAVBAR_PLANET_VERTICAL        => 'Vertikale Ressourcenleiste',
    PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE => 'Lagerkapazität in der Ressourcenleiste ausblenden',
    PLAYER_OPTION_NAVBAR_PLANET_OLD             => 'Alte Tabellenansicht der Ressourcen verwenden',

    PLAYER_OPTION_NAVBAR_RESEARCH_WIDE          => 'Breite Forschungsschaltfläche (alte Ansicht)',
    PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH       => 'Forschungsschaltfläche deaktivieren',
    PLAYER_OPTION_NAVBAR_DISABLE_PLANET         => 'Planetenschaltfläche deaktivieren',
    PLAYER_OPTION_NAVBAR_DISABLE_HANGAR         => 'Werftschaltfläche deaktivieren',
    PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE        => 'Verteidigungsschaltfläche deaktivieren',
    PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS    => 'Expeditionsschaltfläche deaktivieren',
    PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS  => 'Fliegende Flotten Schaltfläche deaktivieren',
    PLAYER_OPTION_NAVBAR_DISABLE_QUESTS         => 'Questschaltfläche deaktivieren',
    PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER    => 'MetaMaterie Schaltfläche deaktivieren',

    PLAYER_OPTION_UNIVERSE_OLD                  => 'Alte "Universumsübersicht" Ansicht verwenden',
    PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE     => 'Kolonisierungsschaltfläche deaktivieren',
    PLAYER_OPTION_DESIGN_DISABLE_BORDERS        => 'Tabellenrahmen-Bilder deaktivieren',
    PLAYER_OPTION_TECH_TREE_TABLE               => 'Technologieseite als Tabelle (alte Ansicht)',
    PLAYER_OPTION_FLEET_SHIP_SELECT_OLD         => 'Schiffsanzahl in separater Spalte (alte Ansicht)',
    PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED         => 'Schiffsgeschwindigkeit nicht anzeigen',
    PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY      => 'Lagerkapazität des Schiffes nicht anzeigen',
    PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION   => 'Treibstoffverbrauch des Schiffes nicht anzeigen',
    PLAYER_OPTION_TUTORIAL_DISABLED             => 'Tutorial komplett deaktivieren',
    PLAYER_OPTION_TUTORIAL_WINDOWED             => 'Tutorial-Text in Popup-Fenster anzeigen',
    PLAYER_OPTION_TUTORIAL_CURRENT              => 'Tutorial zurücksetzen - Tutorial beginnt von neuem',

    PLAYER_OPTION_PLANET_SORT_INVERSE           => 'In umgekehrter Reihenfolge',
    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE        => 'Autokonvertierungsschaltfläche ausblenden',

    PLAYER_OPTION_SOUND_ENABLED                 => 'Spielsounds aktivieren',
    PLAYER_OPTION_ANIMATION_DISABLED            => 'Animationseffekte deaktivieren',
    PLAYER_OPTION_PROGRESS_BARS_DISABLED        => 'Fortschrittsbalken deaktivieren',
  ],

  'opt_chk_skin' => 'Design verwenden',
  'opt_adm_title' => 'Administrationsoptionen',
  'opt_adm_planet_prot' => 'Planetenschutz',
  'thanksforregistry' => 'Danke für die Registrierung.<br />In wenigen Minuten erhalten Sie eine E-Mail mit Ihrem Passwort.',
  'general_settings' => 'Allgemeine Einstellungen',
  'skins_example' => 'Design',

  'opt_avatar' => 'Avatar',
  'opt_avatar_search' => 'In Google suchen',
  'opt_avatar_remove' => 'Avatar entfernen',
  'opt_upload' => 'Hochladen',

  'opt_msg_avatar_removed' => 'Avatar entfernt',
  'opt_msg_avatar_uploaded' => 'Avatar erfolgreich geändert',
  'opt_msg_avatar_error_delete' => 'Fehler beim Löschen der Avatar-Datei. Bitte wenden Sie sich an die Serveradministration',
  'opt_msg_avatar_error_writing' => 'Fehler beim Speichern der Avatar-Datei. Bitte wenden Sie sich an die Serveradministration',
  'opt_msg_avatar_error_upload' => 'Fehler beim Hochladen des Bildes %1. Bitte wenden Sie sich an die Serveradministration',
  'opt_msg_avatar_error_unsupported' => 'Das Format des hochgeladenen Bildes wird nicht unterstützt. Es werden nur JPG, GIF, PNG Dateien bis zu 200KB unterstützt',

  'untoggleip' => 'IP-Überprüfung deaktivieren',
  'untoggleip_tip' => 'IP-Überprüfung bedeutet, dass Sie sich nicht von zwei verschiedenen IPs aus mit Ihrem Namen anmelden können. Die Überprüfung erhöht Ihre Sicherheit!',
  'galaxyvision_options' => 'Universum',
  'spy_cant' => 'Anzahl der Sonden',
  'spy_cant_tip' => 'Anzahl der Sonden, die beim Spionieren versendet werden.',
  'tooltip_time' => 'Verzögerung vor dem Anzeigen des Tooltips',
  'mess_ammount_max' => 'Maximale Anzahl der Flottennachrichten',
  'seconds' => 'Sekunde(n)',
  'shortcut' => 'Schnellzugriff',
  'show' => 'Anzeigen',
  'write_a_messege' => 'Nachricht schreiben',
  'spy' => 'Spionieren',
  'add_to_buddylist' => 'Zu Freunden hinzufügen',
  'attack_with_missile' => 'Raketenangriff',
  'show_report' => 'Bericht anzeigen',
  'delete_vacations' => 'Profilverwaltung',
  'mode_vacations' => 'Urlaubsmodus aktivieren',
  'vacations_tip' => 'Der Urlaubsmodus schützt Ihre Planeten während Ihrer Abwesenheit.',
  'deleteaccount' => 'Profil deaktivieren',
  'deleteaccount_tip' => 'Das Profil wird nach 45 Tagen Inaktivität gelöscht.',
  'deleteaccount_on' => 'Bei Inaktivität des Accounts erfolgt die Löschung',
  'save_settings' => 'Änderungen speichern',
  'exit_vacations' => 'Urlaubsmodus verlassen',
  'Vaccation_mode' => 'Urlaubsmodus aktiviert. Er bleibt aktiv bis: ',
  'You_cant_exit_vmode' => 'Sie können den Urlaubsmodus nicht verlassen, bis die Mindestzeit abgelaufen ist',
  'Error' => 'Fehler',
  'cans_resource' => 'Stoppen Sie die Ressourcenproduktion auf den Planeten',
  'cans_reseach' => 'Stoppen Sie die Forschung auf den Planeten',
  'cans_build' => 'Stoppen Sie den Bau auf den Planeten',
  'cans_fleet_build' => 'Stoppen Sie den Flotten- und Verteidigungsbau',
  'cans_fly_fleet2' => 'Fremde Flotte nähert sich... Sie können nicht in den Urlaub gehen',
  'vacations_exit' => 'Urlaubsmodus deaktiviert... Bitte melden Sie sich erneut an',
  'select_skin_path' => 'AUSWÄHLEN',
  'opt_language' => 'Interface-Sprache',
  'opt_compatibility' => 'Kompatibilität - alte Interfaces',
  'opt_compat_structures' => 'Altes Gebäudebau-Interface',
  'opt_vacation_err_your_fleet' => 'Sie können nicht in den Urlaub gehen, solange mindestens eine Ihrer Flotten unterwegs ist',
  'opt_vacation_err_building' => 'Sie bauen oder forschen auf %s und können daher nicht in den Urlaub gehen',
  'opt_vacation_err_research' => 'Ihre Wissenschaftler forschen an einer Technologie und daher können Sie nicht in den Urlaub gehen',
  'opt_vacation_err_que' => 'Sie forschen entweder an einer Technologie oder bauen etwas auf einem Ihrer Planeten und können daher nicht in den Urlaub gehen. Verwenden Sie den Link "Imperium", um die Bauwarteschlangen auf den Planeten anzuzeigen',
  'opt_vacation_err_timeout' => 'Sie haben noch nicht genug für den Urlaub gearbeitet - die Timeout-Zeit für den Urlaub ist noch nicht abgelaufen',
  'opt_vacation_next' => 'Sie können in den Urlaub gehen nach',
  'opt_vacation_min' => 'mindestens bis',
  'succeful_changepass' => 'Passwort erfolgreich geändert.<br /><a href="login.php" target="_top">Zurück</a>',

  'opt_time_diff_clear' => 'Differenz zwischen Spielerzeit und Serverzeit messen',
  'opt_time_diff_manual' => 'Zeitdifferenz manuell einstellen',
  'opt_time_diff_explain' => 'Bei korrekt eingestellter Zeitdifferenz sollten die Uhren "Spielerzeit" in der Navbar sekundengenau mit den Uhren auf dem Gerät des Spielers übereinstimmen.<br />
  Normalerweise stellt das Spiel die richtige Zeitdifferenz automatisch ein. Bei falsch eingestellter Zeitzone auf dem Gerät des Spielers, beim Spielen mit mehreren Geräten oder bei sehr langsamer Internetverbindung muss die Zeitdifferenz manuell eingestellt werden.',

  'opt_custom' => [
    'opt_uni_avatar_user' => 'Benutzeravatar anzeigen',
    'opt_uni_avatar_ally' => 'Allianzlogo anzeigen',
    'opt_int_struc_vertical' => 'Vertikale Bauwarteschlange',
    'opt_int_navbar_resource_force' => 'Ressourcenleiste immer anzeigen',
    'opt_int_overview_planet_columns' => 'Anzahl der Spalten in der Planetenliste',
    'opt_int_overview_planet_columns_hint' => '0 - basierend auf maximaler Zeilenanzahl berechnen',
    'opt_int_overview_planet_rows' => 'Maximale Anzahl der Zeilen in der Planetenliste',
    'opt_int_overview_planet_rows_hint' => 'Wird ignoriert, wenn Spaltenanzahl angegeben ist',
  ],

  'opt_mail_optional_description' => 'An diese E-Mail-Adresse werden private Nachrichten von anderen Spielern und Benachrichtigungen über Spielereignisse (z.B. Expeditionsberichte und Spionageberichte) gesendet',
  'opt_mail_permanent_description' => 'Diese E-Mail-Adresse ist mit dem Spielaccount verknüpft. Sie kann nur einmal eingegeben werden. Alle Systembenachrichtigungen (z.B. Passwortänderung) werden an diese Adresse gesendet',

  'opt_account_name' => 'Ihr Login<br />Diesen Namen müssen Sie beim Einloggen ins Spiel eingeben',
  'opt_game_user_name' => 'Spielname (Nickname)<br />Unter diesem Namen sind Sie für andere Spieler des Servers sichtbar',

  'opt_universe_title' => 'Universum',

  'option_fleets' => 'Flotten',
  'option_fleet_send' => 'Flottenversand',

  'option_change_nick_disabled' => 'Nickname-Änderung ist durch Servereinstellungen deaktiviert',

  'opt_ignores' => 'Ignore-Liste',
  'opt_unignore_do' => 'Aus Ignore-Liste entfernen',
  'opt_ignore_list_empty' => 'Ihre Ignore-Liste ist leer',
];