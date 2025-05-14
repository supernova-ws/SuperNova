<?php

/*
#############################################################################
#  Filename: system.mo.php
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massen-Mehrspieler-Online-Browser-Weltraumstrategiespiel
#
#  Copyright © 2009 Gorlum für Projekt "SuperNova.WS"
#  Copyright © 2009 MSW
#############################################################################
*/

/**
*
* @package language
* @system [Deutsch]
* @version #46a166#
*
*/

/**
* NICHT ÄNDERN
*/

use Fleet\Constants;

if (!defined('INSIDE'))
{
  exit;
}

// Systemweite Lokalisierung

global $config;

$a_lang_array = [
  'sys_birthday' => 'Geburtstag',
  'sys_birthday_message' => '%1$s! Die SuperNova-Administration gratuliert dir herzlich zu deinem Geburtstag am %2$s und überreicht dir als Geschenk %3$d %4$s! Wir wünschen dir viel Erfolg im Spiel und hohe Plätze in der Statistik! Diese Gratulation mag verspätet sein, aber besser spät als nie.',

  'adm_err_denied' => 'Zugriff verweigert. Sie haben nicht die erforderlichen Berechtigungen, um diese Seite der Serververwaltung zu nutzen',

  'sys_empire'          => 'Imperium',
  'VacationMode'			=> "Ihre Produktion ist eingestellt, da Sie sich im Urlaubsmodus befinden",
  'sys_moon_destruction_report' => "Mondzerstörungsbericht",
  'sys_moon_destroyed' => "Ihre Todessterne haben eine starke Gravitationswelle erzeugt, die den Mond zerstört hat! ",
  'sys_rips_destroyed' => "Ihre Todessterne haben eine starke Gravitationswelle erzeugt, aber ihre Stärke reichte nicht aus, um einen Mond dieser Größe zu zerstören. Die Gravitationswelle reflektierte von der Mondoberfläche und zerstörte Ihre Flotte.",
  'sys_rips_come_back' => "Ihre Todessterne haben nicht genug Energie, um diesen Mond zu beschädigen. Ihre Flotte kehrt zurück, ohne den Mond zu zerstören.",
  'sys_chance_moon_destroy' => "Chance der Mondzerstörung: ",
  'sys_chance_rips_destroy' => "Chance der Risszerstörung: ",

  'sys_impersonate' => 'Einloggen als',
  'sys_impersonate_done' => 'Ausloggen',
  'sys_impersonated_as' => 'WARNUNG! Sie sind aktuell als Spieler %1$s eingeloggt. Vergessen Sie nicht, dass Sie eigentlich %2$s sind! Sie können sich über das entsprechende Menüpunkt ausloggen.',

  'menu_admin_mining'          => 'Spielerabbau',
  'menu_admin_units'          => 'Einheiten',
  'menu_admin_ube_balance'          => 'UBE-Bilanz',

  'sys_day' => "Tage",
  'sys_hrs' => "Stunden",
  'sys_min' => "Minuten",
  'sys_sec' => "Sekunden",
  'sys_day_short' => "T",
  'sys_hrs_short' => "Std",
  'sys_min_short' => "Min",
  'sys_sec_short' => "Sek",

  'sys_ask_admin' => 'Fragen und Vorschläge senden an',

  'sys_wait'      => 'Anfrage wird bearbeitet. Bitte warten Sie.',

  'sys_fleets'       => 'Flotten',
  'sys_expeditions'  => 'Expeditionen',
  'sys_fleet'        => 'Flotte',
  'sys_expedition'   => 'Expedition',
  'sys_event_next'   => 'Nächstes Ereignis:',
  'sys_event_arrive' => 'wird ankommen',
  'sys_event_stay'   => 'wird Aufgabe beenden',
  'sys_event_return' => 'wird zurückkehren',

  'sys_total'           => "GESAMT",
  'sys_need'				=> 'Benötigt',
  'sys_register_date'   => 'Registrierungsdatum',

  'sys_attacker' 		=> "Angreifer",
  'sys_defender' 		=> "Verteidiger",

  'COE_combatSimulator' => "Kampfsimulator",
  'COE_simulate'        => "Simulator starten",
  'COE_fleet'           => "Flotte",
  'COE_defense'         => "Verteidigung",
  'sys_coe_combat_start'=> "Gegnerische Flotten sind aufeinandergetroffen",
  'sys_coe_combat_end'  => "Kampfergebnis",
  'sys_coe_round'       => "Runde",

  'sys_coe_attacker_turn'=> 'Der Angreifer feuert mit einer Gesamtstärke von %1$s. Die Schilde des Verteidigers absorbieren %2$s Schüsse<br />',
  'sys_coe_defender_turn'=> 'Der Verteidiger feuert mit einer Gesamtstärke von %1$s. Die Schilde des Angreifers absorbieren %2$s Schüsse<br /><br /><br />',
  'sys_coe_outcome_win'  => 'Der Verteidiger hat die Schlacht gewonnen!<br />',
  'sys_coe_outcome_loss' => 'Der Angreifer hat die Schlacht gewonnen!<br />',
  'sys_coe_outcome_loot' => 'Er erhält %1$s Metall, %2$s Kristall, %3$s Deuterium<br />',
  'sys_coe_outcome_draw' => 'Die Schlacht endete unentschieden.<br />',
  'sys_coe_attacker_lost'=> 'Der Angreifer hat %1$s Einheiten verloren.<br />',
  'sys_coe_defender_lost'=> 'Der Verteidiger hat %1$s Einheiten verloren.<br />',
  'sys_coe_debris_left'  => 'An diesen Raumkoordinaten befinden sich nun %1$s Metall und %2$s Kristall.<br /><br />',
  'sys_coe_moon_chance'  => 'Die Chance auf eine Mondentstehung beträgt %1$s%%<br />',
  'sys_coe_rw_time'      => 'Seitengenerierungszeit %1$s Sekunden<br />',

  'sys_resources'       => "Ressourcen",
  'sys_ships'           => "Schiffe",
  'sys_metal'          => "Metall",
  'sys_metal_sh'       => "M",
  'sys_crystal'        => "Kristall",
  'sys_crystal_sh'     => "K",
  'sys_deuterium'      => "Deuterium",
  'sys_deuterium_sh'   => "D",
  'sys_energy'         => "Energie",
  'sys_energy_sh'      => "E",
  'sys_dark_matter'    => "Dunkle Materie",
  'sys_dark_matter_sh' => "DM",
  'sys_metamatter'     => "Metamaterie",
  'sys_metamatter_sh'  => "MM",

  'sys_reset'           => "Zurücksetzen",
  'sys_send'            => "Senden",
  'sys_characters'      => "Zeichen",
  'sys_back'            => "Zurück",
  'sys_return'          => "Zurückkehren",
  'sys_delete'          => "Löschen",
  'sys_writeMessage'    => "Nachricht schreiben",
  'sys_hint'            => "Hinweis",

  'sys_alliance'        => "Allianz",
  'sys_player'          => "Spieler",
  'sys_coordinates'     => "Koordinaten",

  'sys_online'          => "Online",
  'sys_offline'         => "Offline",
  'sys_status'          => "Status",

  'sys_universe'        => "Universum",
  'sys_goto'            => "Gehe zu",

  'sys_time'            => "Zeit",
  'sys_temperature'		=> 'Temperatur',

  'sys_no_task'         => "keine Aufgabe",

  'sys_affilates'       => "Eingeladene Spieler",

  'sys_fleet_arrived'   => "Flotte angekommen",

  'sys_planet_type' => [
    PT_PLANET => 'Planet',
    PT_DEBRIS => 'Trümmerfeld',
    PT_MOON   => 'Mond',
  ],

  'sys_planet_type_sh' => [
    PT_PLANET => '(P)',
    PT_DEBRIS => '(T)',
    PT_MOON   => '(M)',
  ],

  'sys_planet_expedition' => 'unerforschter Raum',

  'sys_capacity' 			=> 'Ladekapazität',
  'sys_cargo_bays' 		=> 'Laderäume',

  'sys_supernova' 		=> 'SuperNova',
  'sys_server' 			=> 'Server',

  'sys_unbanned'			=> 'Entsperrt',

  'sys_date_time'			=> 'Datum und Zeit',
  'sys_from_person'	   => 'Von wem',
  'sys_from_speed'	   => 'von',

  'sys_from'		  => 'von',
  'tp_on'            => 'auf',

// Ressourcen-Seite
  'res_planet_production' => 'Ressourcenproduktion auf dem Planeten',
  'res_basic_starting_resources' => 'Startressourcen auf dem Planeten',
  'res_basic_income' => 'Natürliche Produktion',
  'res_basic_storage_size' => 'Lagergröße',
  'res_total' => 'GESAMT',
  'res_calculate' => 'Berechnen',
  'res_hourly' => 'Pro Stunde',
  'res_daily' => 'Pro Tag',
  'res_weekly' => 'Pro Woche',
  'res_monthly' => 'Pro Monat',
  'res_storage_fill' => 'Lagerfüllstand',
  'res_hint' => '<ul><li>Ressourcenproduktion <100% bedeutet Energiemangel. Bauen Sie zusätzliche Kraftwerke oder reduzieren Sie die Ressourcenproduktion<li>Wenn Ihre Produktion 0% beträgt, sind Sie wahrscheinlich aus dem Urlaubsmodus zurückgekehrt und müssen alle Fabriken einschalten<li>Um den Abbau für alle Fabriken gleichzeitig einzustellen, verwenden Sie die Dropdown-Liste in der Tabellenüberschrift. Besonders nützlich nach dem Urlaubsmodus</ul>',

// Bau-Seite
  'bld_destroy' => 'Zerstören',
  'bld_create'  => 'Bauen',
  'bld_research' => 'Erforschen',
  'bld_hire' => 'Einstellen',

// Imperium-Seite
  'imp_imperator' => "Imperator",
  'imp_overview' => "Imperiumsübersicht",
  'imp_fleets' => "Flotten im Flug",
  'imp_production' => "Produktion",
  'imp_name' => "Name",
  'imp_research' => "Forschung",
  'imp_exploration' => "Expeditionen",
  'imp_imperator_none' => "Kein solcher Imperator im Universum!",
  'sys_fields' => "Sektoren",

// Cookies
  'err_cookie' => "Fehler! Der Benutzer kann nicht anhand der Cookie-Informationen autorisiert werden.<br />Löschen Sie die Browser-Cookies und versuchen Sie erneut, sich <a href='login" . DOT_PHP_EX . "'>anzumelden</a> oder <a href='reg" . DOT_PHP_EX . "'>zu registrieren</a>.",

// Unterstützte Sprachen
  'ru'              	  => 'Russisch',
  'en'              	  => 'Englisch',

  'sys_vacation'        => 'Sie sind im Urlaubsmodus bis',
  'sys_vacation_leave'  => 'Ich habe mich ausgeruht - Urlaubsmodus beenden!',
  'sys_vacation_in'     => 'Im Urlaub',
  'sys_level'           => 'Level',
  'sys_level_short'     => 'Lvl',
  'sys_level_max'       => 'Maximales Level',

  'sys_yes'             => 'Ja',
  'sys_no'              => 'Nein',

  'sys_on'              => 'Ein',
  'sys_off'             => 'Aus',

  'sys_confirm'         => 'Bestätigen',
  'sys_save'            => 'Speichern',
  'sys_create'          => 'Erstellen',
  'sys_write_message'   => 'Nachricht schreiben',

// Top-Leiste
  'top_of_year' => 'Jahr',
  'top_online'			=> 'Spieler',

  'sys_first_round_crash_1'	=> 'Der Kontakt zur angegriffenen Flotte wurde verloren.',
  'sys_first_round_crash_2'	=> 'Das bedeutet, dass sie in der ersten Kampfrunde zerstört wurde.',

  'sys_ques' => [
    QUE_STRUCTURES => 'Gebäude',
    QUE_HANGAR     => 'Werft',
    SUBQUE_DEFENSE => 'Verteidigung',
    QUE_RESEARCH   => 'Forschung',
  ],

  'navbar_button_expeditions_short' => 'Expa',
  'navbar_button_fleets' => 'Flotten',
  'navbar_button_quests' => 'Quests',
  'navbar_font' => 'Schriftart',
  'navbar_font_normal' => 'Normal',
  'sys_que_structures' => 'Gebäude',
  'sys_que_hangar' => 'Werft',
  'sys_que_defense' => 'Verteidigung',
  'sys_que_research' => 'Forschung',
  'sys_que_research_short' => 'Wissenschaft',

  'eco_que' => 'Warteschlange',
  'eco_que_empty' => 'Warteschlange ist leer',
  'eco_que_clear' => 'Warteschlange leeren',
  'eco_que_trim'  => 'Letztes abbrechen',
  'eco_que_artifact'  => 'Artefakt verwenden',

  'sys_cancel' => 'Abbrechen',

  'sys_overview'			=> 'Übersicht',
  'mod_marchand'			=> 'Händler',
  'sys_galaxy'			=> 'Galaxie',
  'sys_system'			=> 'System',
  'sys_planet'			=> 'Planet',
  'sys_planet_title'			=> 'Planetentyp',
  'sys_planet_title_short'			=> 'Typ',
  'sys_moon'			=> 'Mond',
  'sys_error'			=> 'Fehler',
  'sys_done'				=> 'Fertig',
  'sys_no_vars'			=> 'Fehler bei der Variableninitialisierung, wenden Sie sich an die Administration!',
  'sys_attacker_lostunits'		=> 'Der Angreifer verlor %s Einheiten.',
  'sys_defender_lostunits'		=> 'Der Verteidiger verlor %s Einheiten.',
  'sys_gcdrunits' 			=> 'An diesen Raumkoordinaten befinden sich nun %s %s und %s %s.',
  'sys_moonproba' 			=> 'Die Chance auf eine Mondentstehung beträgt: %d %% ',
  'sys_moonbuilt' 			=> 'Dank der enormen Energie verbinden sich große Metall- und Kristallbrocken und bilden einen neuen Mond %s %s!',
  'sys_attack_title'    		=> '%s. Ein Kampf fand zwischen den folgenden Flotten statt:',
  'sys_attack_attacker_pos'      	=> 'Angreifer %s [%s:%s:%s]',
  'sys_attack_techologies' 	=> 'Bewaffnung: %d %% Schilde: %d %% Rüstung: %d %% ',
  'sys_attack_defender_pos' 	=> 'Verteidiger %s [%s:%s:%s]',
  'sys_ship_type' 			=> 'Typ',
  'sys_ship_count' 		=> 'Anzahl',
  'sys_ship_weapon' 		=> 'Bewaffnung',
  'sys_ship_shield' 		=> 'Schilde',
  'sys_ship_armour' 		=> 'Rüstung',
  'sys_ship_speed' 		=> 'Geschwindigkeit',
  'sys_ship_consumption' 		=> 'Verbrauch',
  'sys_ship_capacity' 		=> 'Laderaum/Tank',
  'sys_destroyed' 			=> 'zerstört',
  'sys_attack_attack_wave' 	=> 'Der Angreifer feuert mit einer Gesamtstärke von %s auf den Verteidiger. Die Schilde des Verteidigers absorbieren %s Schüsse.',
  'sys_attack_defend_wave'		=> 'Der Verteidiger feuert mit einer Gesamtstärke von %s auf den Angreifer. Die Schilde des Angreifers absorbieren %s Schüsse.',
  'sys_attacker_won' 		=> 'Der Angreifer hat die Schlacht gewonnen!',
  'sys_defender_won' 		=> 'Der Verteidiger hat die Schlacht gewonnen!',
  'sys_both_won' 			=> 'Die Schlacht endete unentschieden!',
  'sys_stealed_ressources' 	=> 'Er erhält %s Metall %s %s Kristall %s und %s Deuterium.',
  'sys_rapport_build_time' 	=> 'Seitengenerierungszeit %s Sekunden',
  'sys_mess_tower' 		=> 'Transport',
  'sys_coe_lost_contact' 		=> 'Die Verbindung zu Ihrer Flotte wurde verloren',
  'sys_spy_activity' => 'In der Nähe Ihrer Planeten wurde Spionageaktivität festgestellt',
  'sys_spy_materials' 		=> 'Rohstoffe auf',
  'sys_spy_fleet' 			=> 'Flotte',
  'sys_spy_defenses' 		=> 'Verteidigung',
  'sys_mess_qg' 			=> 'Flottenkommando',
  'sys_mess_spy_report' 		=> 'Spionagebericht',
  'sys_mess_spy_lostproba' 	=> 'Ungenauigkeit der vom Satelliten erhaltenen Informationen %d %% ',
  'sys_mess_spy_detect_chance' 	=> 'Die Chance, Ihre Spionageflotte zu entdecken, beträgt %d%%',
  'sys_mess_spy_detect_chance_no_percent' 	=> 'Chance, Ihre Spionageflotte zu entdecken',
  'sys_mess_spy_control' 		=> 'Spionageabwehr',
  'sys_mess_spy_activity' 		=> 'Spionageaktivität',
  'sys_mess_spy_enemy_fleet' 	=> 'Eine fremde Flotte vom Planeten',
  'sys_mess_spy_seen_at'		=> 'wurde in der Nähe des Planeten entdeckt',
  'sys_mess_spy_destroyed'		=> 'Die Spionageflotte wurde zerstört',
  'sys_mess_spy_destroyed_enemy'		=> 'Feindliche Spionageflotte zerstört',
  'sys_object_arrival'		=> 'Auf dem Planeten angekommen',
  'sys_stay_mess_stay' => 'Flottenverlegung',
  'sys_stay_mess_start' 		=> 'Ihre Flotte ist auf dem Planeten angekommen',
  'sys_stay_mess_back'		=> 'Ihre Flotte ist zurückgekehrt ',
  'sys_stay_mess_end'		=> ' und hat geliefert:',
  'sys_stay_mess_bend'		=> ' und hat folgende Ressourcen geliefert:',
  'sys_adress_planet' 		=> '[%s:%s:%s]',
  'sys_stay_mess_goods' 		=> '%s : %s, %s : %s, %s : %s',
  'sys_colo_mess_from' 		=> 'Kolonisierung',
  'sys_colo_mess_report' 		=> 'Kolonisierungsbericht',
  'sys_colo_defaultname' 		=> 'Kolonie',
  'sys_colo_arrival' 		=> 'Die Flotte erreicht die Koordinaten ',
  'sys_colo_maxcolo' 		=> ', aber der Planet kann nicht kolonisiert werden, die maximale Anzahl an Kolonien für Ihr Kolonisierungslevel wurde erreicht',
  'sys_colo_allisok' 		=> ', und die Kolonisten beginnen, einen neuen Planeten zu besiedeln.',
  'sys_colo_badpos'  			=> ', und die Kolonisten fanden die Umgebung für Ihr Imperium wenig vorteilhaft. Die Kolonisierungsmission kehrt zum Startplaneten zurück.',
  'sys_colo_notfree' 			=> ', und die Kolonisten fanden keinen Planeten an diesen Koordinaten. Sie müssen enttäuscht den Rückweg antreten.',
  'sys_colo_no_colonizer'     => 'In der Flotte befindet sich kein Kolonisierer',
  'sys_colo_planet'  		=> ' Planet kolonisiert!',
  'sys_expe_report' 		=> 'Expeditionsbericht',
  'sys_recy_report' 		=> 'Systeminformation',
  'sys_expe_blackholl_1' 		=> 'Ihre Flotte ist in ein schwarzes Loch geraten und teilweise verloren gegangen!',
  'sys_expe_blackholl_2' 		=> 'Ihre Flotte ist in ein schwarzes Loch geraten und vollständig verloren gegangen!',
  'sys_expe_nothing_1' 		=> 'Ihre Forscher waren Zeugen einer SuperNova! Und Ihre Speicher konnten einen Teil der freigesetzten Energie aufnehmen.',
  'sys_expe_nothing_2' 		=> 'Ihre Forscher haben nichts entdeckt!',
  'sys_expe_found_goods' 		=> 'Ihre Forscher haben einen rohstoffreichen Planeten gefunden!<br>Sie erhalten %s %s, %s %s und %s %s',
  'sys_expe_found_ships' 		=> 'Ihre Forscher haben eine makellos neue Flotte gefunden!<br>Sie erhalten: ',
  'sys_expe_back_home' 		=> 'Ihre Flotte kehrt zurück.',
  'sys_mess_transport' 		=> 'Transport',
  'sys_tran_mess_user'  		=> 'Eine Flotte vom Planeten %s %s ist auf %s %s angekommen und hat %s %s, %s %s und %s %s geliefert.',
  'sys_relocate_mess_user'  		=> 'Außerdem wurden folgende Kampfeinheiten auf den Planeten verlegt:<br />',
  'sys_mess_fleetback' 		=> 'Rückkehr',
  'sys_tran_mess_back' 		=> 'Eine Ihrer Flotten ist zum Planeten %s %s zurückgekehrt.',
  'sys_recy_gotten' 		=> 'Eine Ihrer Flotten hat %s %s und %s %s abgebaut. Kehrt zum Planeten zurück.',
  'sys_notenough_money' 		=> 'Ihnen fehlen die Ressourcen, um zu bauen: %s. Sie haben aktuell: %s %s , %s %s und %s %s. Für den Bau benötigt: %s %s , %s %s und %s %s.',
  'sys_nomore_level'		=> 'Sie können dies nicht weiter verbessern. Es hat das maximale Level (%s) erreicht.',
  'sys_buildlist' 			=> 'Bauauflistung',
  'sys_buildlist_fail' 		=> 'keine Gebäude',
  'sys_gain' 			=> 'Beute: ',
  'sys_debris' 			=> 'Trümmer: ',
  'sys_noaccess' 			=> 'Zugriff verweigert',
  'sys_noalloaw' 			=> 'Der Zugang zu diesem Bereich ist Ihnen verwehrt!',
  'sys_governor'        => 'Gouverneur',

  'flt_error_duration_wrong' => 'Flotte kann nicht gesendet werden - keine verfügbaren Intervalle für die Verzögerung. Studieren Sie weitere Astrokartographie-Level',
  'flt_stay_duration' => 'Zeit',

  'flt_mission_expedition' => [
    'msg_sender' => 'Expeditionsbericht',
    'msg_title' => 'Expeditionsbericht',

    'found_dark_matter' => '%1$d Einheiten DM erhalten',
    'found_resources' => "Ressourcen gefunden:\r\n",
    'found_fleet' => "Schiffe gefunden:\r\n",
    'lost_fleet' => "Folgende Schiffe verloren:\r\n",

    'outcomes' => [
      Constants::OUTCOME_NONE => [
        'messages' => [
          'Ihre Forscher haben nichts entdeckt',
        ],
      ],

      Constants::EXPEDITION_OUTCOME_LOST_FLEET => [
        'messages' => [
          'Die Flotte ist in ein schwarzes Loch geraten und teilweise verloren gegangen',
        ],
      ],

      Constants::EXPEDITION_OUTCOME_LOST_FLEET_ALL => [
        'messages' => [
          'Wenn Sie das nur sehen könnten! Es ist so schön... Es ruft... (Verbindung zur Flotte verloren)',
          'Flottenbericht %1$s. Wir haben die Sektorerforschung abgeschlossen. Die Crew ist unzufrieden. Hey, was machst du auf der Brücke?! (Verbindung zur Flotte verloren)',
          'Flottenbericht %1$s. Alles ruhig (Störungen) (Verbindung zur Flotte verloren)',
          'AAAAAH! WAS IST DAS?! WO KOMMT ES HER (Verbindung zur Flotte verloren)',
          'Unbekanntes Objekt entdeckt. Es reagiert nicht auf Standardprotokollanfragen. Wir senden eine Sonde zur Untersuchung aus (Verbindung zur Flotte verloren)',
        ],
      ],

      Constants::EXPEDITION_OUTCOME_FOUND_FLEET => [
        'no_result' => 'Leider reichte die kombinierte Leistung aller Flottencomputer nicht einmal aus, um das kleinste Schiff zu kontrollieren. Versuchen Sie, mehr Schiffe und/oder größere Schiffe zu senden',
        'messages' => [
          0 => [
            'Sie haben eine brandneue Flotte gefunden',
          ],
          1 => [
            'Sie haben eine Flotte gefunden',
          ],
          2 => [
            'Sie haben eine gebrauchte Flotte gefunden',
          ],
        ],
      ],

      Constants::EXPEDITION_OUTCOME_FOUND_RESOURCES => [
        'no_result' => 'Die Laderäume Ihrer Flotte waren nicht in der Lage, auch nur einen Ressourcencontainer aufzunehmen. Versuchen Sie, eine Flotte mit mehr Transportern zu senden',
        'messages' => [
          0 => [
            'Sie haben einen Piratenschatz mit Ressourcen gefunden. Wie viele Schiffe wurden zerstört, um so viel Beute zu sammeln?',
          ],
          1 => [
            'Sie haben eine verlassene Asteroidenbasis gefunden. Interessant, wohin ihre Bewohner verschwunden sind? Bei der Untersuchung der Ruinen fanden Sie einige intakte Lagerhäuser mit Ressourcen',
          ],
          2 => [
            'Sie sind auf einen zerstörten Transportkonvoi gestoßen. Bei der Durchsuchung der Laderäume der zerstörten Schiffe fanden Sie einige Ressourcen',
          ],
        ],
      ],

      Constants::EXPEDITION_OUTCOME_FOUND_DM => [
        'no_result' => 'Leider reichten alle Flottenspeicher nicht aus, um auch nur eine einzige DM zu sammeln. Versuchen Sie, eine größere Flotte zu senden',
        'messages' => 'Ihre Flotte war Zeuge der Geburt einer SuperNova',
      ],
    ],
  ],

  // Nachrichten-Seite & ein bisschen Imperator-Seite
  'news_fresh'      => 'Aktuelle Nachrichten',
  'news_all'        => 'Alle Nachrichten',
  'news_title'      => 'Nachrichten',
  'news_none'       => 'Keine Nachrichten',
  'news_new'        => 'NEU',
  'news_future'     => 'ANKÜNDIGUNG',
  'news_more'       => 'Mehr...',
  'news_hint'       => 'Um die Liste der letzten Nachrichten zu entfernen - lesen Sie sie alle, indem Sie auf die Überschrift "[ Nachrichten ]" klicken',

  'news_date'       => 'Datum',
  'news_announce'   => 'Inhalt',
  'news_detail_url' => 'Link zu Details',
  'news_mass_mail'  => 'Nachricht an alle Spieler senden',

  'news_total'      => 'Gesamte Nachrichten: ',

  'news_add'        => 'Nachricht hinzufügen',
  'news_edit'       => 'Nachricht bearbeiten',
  'news_copy'       => 'Nachricht kopieren',
  'news_mode_new'   => 'Neu',
  'news_mode_edit'  => 'Bearbeiten',
  'news_mode_copy'  => 'Kopie',

  'sys_administration' => 'Serveradministration',

  'note_add'        => 'Notiz hinzufügen',
  'note_del'        => 'Notiz löschen',
  'note_edit'        => 'Notiz ändern',

  // Verknüpfungen
  'shortcut_title'     => 'Lesezeichen',
  'shortcut_none'      => 'Keine Lesezeichen',
  'shortcut_new'       => 'NEU',
  'shortcut_text'      => 'Text',

  'shortcut_add'       => 'Lesezeichen hinzufügen',
  'shortcut_edit'      => 'Lesezeichen bearbeiten',
  'shortcut_copy'      => 'Lesezeichen kopieren',
  'shortcut_mode_new'  => 'Neu',
  'shortcut_mode_edit' => 'Bearbeiten',
  'shortcut_mode_copy' => 'Kopie',

  // Raketenbezogen
  'mip_h_launched'			=> 'Start von Interplanetarraketen',
  'mip_launched'				=> 'Interplanetarraketen gestartet: <b>%s</b>!',

  'mip_no_silo'				=> 'Unzureichendes Level der Raketensilos auf dem Planeten <b>%s</b>.',
  'mip_no_impulse'			=> 'Impulsantrieb muss erforscht werden.',
  'mip_too_far'				=> 'Die Rakete kann nicht so weit fliegen.',
  'mip_planet_error'			=> 'Fehler - mehr als ein Planet an einer Koordinate',
  'mip_no_rocket'				=> 'Nicht genug Raketen im Silo für einen Angriff.',
  'mip_hack_attempt'			=> ' Bist du ein Hacker? Noch so ein Witz und du wirst gebannt. IP-Adresse und Login wurden aufgezeichnet.',

  'mip_all_destroyed' 		=> 'Alle Interplanetarraketen wurden von Abfangraketen zerstört<br>',
  'mip_destroyed'				=> '%s Interplanetarraketen wurden von Abfangraketen zerstört.<br>',
  'mip_defense_destroyed'	=> 'Folgende Verteidigungsanlagen wurden zerstört:<br />',
  'mip_recycled'				=> 'Aus den Trümmern der Verteidigungsanlagen recycelt: ',
  'mip_no_defense'			=> 'Auf dem angegriffenen Planeten gab es keine Verteidigung!',

  'mip_sender_amd'			=> 'Raumfahrtraketentruppen',
  'mip_subject_amd'			=> 'Raketenangriff',
  'mip_body_attack'			=> 'Angriff mit Interplanetarraketen (%1$s Stück) vom Planeten %2$s <a href="galaxy.php?mode=3&galaxy=%3$d&system=%4$d&planet=%5$d">[%3$d:%4$d:%5$d]</a> auf den Planeten %6$s <a href="galaxy.php?mode=3&galaxy=%7$d&system=%8$d&planet=%9$d">[%7$d:%8$d:%9$d]</a><br><br>',

  // Verschiedenes
  'sys_game_rules' => 'Spielregeln',
  'sys_game_documentation' => 'Spielbeschreibung',
  'sys_banned_msg' => 'Sie sind gesperrt. Für weitere Informationen besuchen Sie <a href="banned.php">hier</a>. Sperrungsende des Kontos: ',
  'sys_total_time' => 'Gesamtzeit',
  'sys_total_time_short' => 'Warteschlange',
  'eco_que_finish' => 'Abschluss',

  // Universum
  'uni_moon_of_planet' => 'des Planeten',

  // Kampfberichte
  'cr_view_title'  => "Kampfberichte anzeigen",
  'cr_view_button' => "Bericht anzeigen",
  'cr_view_prompt' => "Code eingeben",
  'cr_view_my'     => "Meine Kampfberichte",
  'cr_view_hint'   => '<ul><li>Sie können Ihre eigenen Kampfberichte anzeigen, indem Sie auf den Link "Meine Kampfberichte" in der Überschrift klicken</li><li>Der Kampfberichtscode befindet sich in der letzten Zeile und ist eine Sequenz aus 32 Ziffern und lateinischen Buchstaben</li></ul>',

  // Flotte
  'flt_gather_all'    => 'Ressourcen sammeln',

  // Bann-System
  'ban_title'      => 'Schwarze Liste',
  'ban_name'       => 'Name',
  'ban_reason'     => 'Sperrungsgrund',
  'ban_from'       => 'Sperrungsdatum',
  'ban_to'         => 'Sperrungsdauer',
  'ban_by'         => 'Ausgestellt von',
  'ban_no'         => 'Keine gesperrten Spieler',
  'ban_thereare'   => 'Gesamt',
  'ban_players'    => 'gesperrt',
  'ban_banned'     => 'Spieler gesperrt: ',

  // Kontakte
  'ctc_title' => 'Administration',
  'ctc_intro' => 'Hier finden Sie die Adressen aller Administratoren und Operatoren des Spiels für Feedback',
  'ctc_name'  => 'Name',
  'ctc_rank'  => 'Rang',
  'ctc_mail'  => 'eMail',

  // Rekord-Seite
  'rec_title'  => 'Universumsrekorde',
  'rec_build'  => 'Gebäude',
  'rec_specb'  => 'Spezialgebäude',
  'rec_playe'  => 'Spieler',
  'rec_defes'  => 'Verteidigung',
  'rec_fleet'  => 'Flotte',
  'rec_techn'  => 'Technologien',
  'rec_level'  => 'Level',
  'rec_nbre'   => 'Anzahl',
  'rec_rien'   => '-',

  // Credits-Seite
  'cred_link'    => 'Internet',
  'cred_site'    => 'Website',
  'cred_forum'   => 'Forum',
  'cred_credit'  => 'Autoren',
  'cred_creat'   => 'Direktor',
  'cred_prog'    => 'Programmierer',
  'cred_master'  => 'Leiter',
  'cred_design'  => 'Designer',
  'cred_web'     => 'Webmaster',
  'cred_thx'     => 'Danksagungen',
  'cred_based'   => 'Basis für die Erstellung von XNova',
  'cred_start'   => 'Debütort von XNova',

  // Eingebauter Chat
  'chat_common'   => 'Allgemeiner Chat',
  'chat_ally'     => 'Allianz-Chat',
  'chat_history'  => 'Chat-Verlauf',
  'chat_message'  => 'Nachricht',
  'chat_send'     => 'Senden',
  'chat_page'     => 'Seite',
  'chat_timeout'  => 'Chat aufgrund Ihrer Inaktivität deaktiviert. Aktualisieren Sie die Seite.',

  // ----------------------------------------------------------------------------------------------------------
  // Interface des Sprungtors
  'gate_start_moon' => 'Startmond',
  'gate_dest_moon'  => 'Zielmond',
  'gate_use_gate'   => 'Tor benutzen',
  'gate_ship_sel'   => 'Schiffe auswählen',
  'gate_ship_dispo' => 'verfügbar',
  'gate_jump_btn'   => 'Sprung ausführen!!',
  'gate_jump_done'  => 'Das Tor befindet sich im Nachladezustand!<br>Das Tor wird in bereit sein: ',
  'gate_wait_dest'  => 'Das Zieltor befindet sich im Vorbereitungszustand! Das Tor wird in bereit sein: ',
  'gate_no_dest_g'  => 'Am Zielort wurde kein Tor zur Flottenverlegung gefunden',
  'gate_no_src_ga'  => 'Kein Tor zur Flottenverlegung vorhanden',
  'gate_wait_star'  => 'Das Tor befindet sich im Nachladezustand!<br>Das Tor wird in bereit sein: ',
  'gate_wait_data'  => 'Fehler, keine Daten für den Sprung!',
  'gate_vacation'   => 'Fehler, Sie können keinen Sprung durchführen, da Sie sich im Urlaubsmodus befinden!',
  'gate_ready'      => 'Tor sprungbereit',

  // Quests
  'qst_quests'               => 'Quests',
  'qst_msg_complete_subject' => 'Quest abgeschlossen',
  'qst_msg_complete_body'    => 'Sie haben die Quest "%s" abgeschlossen.',
  'qst_msg_your_reward'      => 'Ihre Belohnung:',

  // Nachrichten
  'msg_from_admin' => 'Universumsadministration',
  'msg_class' => [
    MSG_TYPE_OUTBOX => 'Gesendete Nachrichten',
    MSG_TYPE_SPY => 'Spionageberichte',
    MSG_TYPE_PLAYER => 'Nachrichten von Spielern',
    MSG_TYPE_ALLIANCE => 'Allianznachrichten',
    MSG_TYPE_COMBAT => 'Kampfberichte',
    MSG_TYPE_RECYCLE => 'Recyclingberichte',
    MSG_TYPE_TRANSPORT => 'Flottenankunft',
    MSG_TYPE_ADMIN => 'Administrationsnachrichten',
    MSG_TYPE_EXPLORE => 'Expeditionsberichte',
    MSG_TYPE_QUE => 'Bauwarteschlangennachrichten',
    MSG_TYPE_NEW => 'Alle Nachrichten',
  ],

  'msg_que_research_from'    => 'Forschungsinstitut',
  'msg_que_research_subject' => 'Neue Technologie',
  'msg_que_research_message' => 'Eine neue Technologie \'%s\' wurde erforscht. Neues Level - %d',

  'msg_que_planet_from'    => 'Gouverneur',

  'msg_que_hangar_subject' => 'Werftarbeit abgeschlossen',
  'msg_que_hangar_message' => "Die Werft auf %s hat die Arbeit abgeschlossen",

  'msg_que_built_subject'   => 'Planetare Arbeiten abgeschlossen',
  'msg_que_built_message'   => "Der Bau des Gebäudes '%2\$s' auf %1\$s wurde abgeschlossen. Gebaute Level: %3\$d",
  'msg_que_destroy_message' => "Die Zerstörung des Gebäudes '%2\$s' auf %1\$s wurde abgeschlossen. Zerstörte Level: %3\$d",

  'msg_personal_messages' => 'Persönliche Nachrichten',

  'sys_opt_bash_info'    => 'Einstellungen des Anti-Bashing-Systems',
  'sys_opt_bash_attacks' => 'Anzahl der Angriffe in einer Welle',
  'sys_opt_bash_interval' => 'Intervall zwischen Wellen',
  'sys_opt_bash_scope' => 'Bashing-Berechnungszeitraum',
  'sys_opt_bash_war_delay' => 'Moratorium nach Kriegserklärung',
  'sys_opt_bash_waves' => 'Anzahl der Wellen pro Zeitraum',
  'sys_opt_bash_disabled'    => 'Anti-Bashing-System deaktiviert',

  'sys_id' => 'ID',
  'sys_identifier' => 'Identifikator',

  'sys_email'   => 'E-Mail',
  'sys_ip' => 'IP',

  'sys_max' => 'Max',
  'sys_maximum' => 'Maximum',
  'sys_maximum_level' => 'Maximales Level',

  'sys_user_name' => 'Benutzername',
  'sys_player_name' => 'Spielername',
  'sys_user_name_short' => 'Name',

  'sys_planets' => 'Planeten',
  'sys_moons' => 'Monde',

  'sys_quantity' => 'Menge',
  'sys_quantity_maximum' => 'Maximale Menge',
  'sys_qty' => 'Anz',
  'sys_quantity_total' => 'Gesamtmenge',

  'sys_buy_for' => 'Kaufen für',
  'sys_buy' => 'Kaufen',
  'sys_for' => 'für',

  'sys_eco_lack_dark_matter' => 'Nicht genug Dunkle Materie',

  'time_local' => 'Spielerzeit',
  'time_server' => 'Serverzeit',

  'sys_result' => [
    'error_dark_matter_not_enough' => 'Nicht genug Dunkle Materie, um den Vorgang abzuschließen',
    'error_dark_matter_change' => 'Fehler bei der Änderung der Dunklen Materie! Wiederholen Sie den Vorgang. Wenn der Fehler erneut auftritt, informieren Sie die Serveradministration',
  ],

  // Arrays
  'sys_build_result' => [
    BUILD_ALLOWED => 'Kann gebaut werden',
    BUILD_REQUIRE_NOT_MEET => 'Anforderungen nicht erfüllt',
    BUILD_AMOUNT_WRONG => 'Zu viel',
    BUILD_QUE_WRONG => 'Ungültige Warteschlange',
    BUILD_QUE_UNIT_WRONG => 'Falsche Warteschlange',
    BUILD_INDESTRUCTABLE => 'Kann nicht zerstört werden',
    BUILD_NO_RESOURCES => 'Nicht genug Ressourcen',
    BUILD_NO_UNITS => 'Keine Einheiten',
    BUILD_UNIT_BUSY => [
      0 => 'Gebäude beschäftigt',
      STRUC_LABORATORY => 'Forschung läuft',
      STRUC_LABORATORY_NANO => 'Forschung läuft',
    ],
    BUILD_QUE_FULL => 'Warteschlange voll',
    BUILD_SILO_FULL => 'Raketensilo voll',
    BUILD_MAX_REACHED => 'Sie haben bereits die maximale Anzahl an Einheiten dieses Typs gebaut und/oder in die Warteschlange gestellt',
    BUILD_SECTORS_NONE => 'Keine freien Sektoren',
    BUILD_AUTOCONVERT_AVAILABLE => 'Autokonvertierung verfügbar',
    BUILD_HIGHSPOT_NOT_ACTIVE => 'Event nicht aktiv',
  ],

  'sys_game_mode' => [
    GAME_SUPERNOVA => 'SuperNova',
    GAME_OGAME     => 'oGame',
    GAME_BLITZ     => 'Blitz-Server',
  ],

  'months' => [
     1 =>'Januar',
     2 =>'Februar',
     3 =>'März',
     4 =>'April',
     5 =>'Mai',
     6 =>'Juni',
     7 =>'Juli',
     8 =>'August',
     9 =>'September',
    10 =>'Oktober',
    11 =>'November',
    12 =>'Dezember'
  ],

  'weekdays' => [
    0 => 'Sonntag',
    1 => 'Montag',
    2 => 'Dienstag',
    3 => 'Mittwoch',
    4 => 'Donnerstag',
    5 => 'Freitag',
    6 => 'Samstag'
  ],

  'user_level' => [
    0 => 'Spieler',
    1 => 'Moderator',
    2 => 'Operator',
    3 => 'Administrator',
    4 => 'Entwickler',
  ],

  'user_level_shortcut' => [
    0 => 'S',
    1 => 'M',
    2 => 'O',
    3 => 'A',
    4 => 'E',
  ],

  'sys_lessThen15min'   => '&lt; 15 Min',

  'sys_no_points'         => 'Sie haben nicht genug <span class="dark_matter">Dunkle Materie</span>!',
  'sys_dark_matter_obtain_header' => 'Wie man <span class="dark_matter">Dunkle Materie</span> erhält',
  'sys_dark_matter_desc' => 'Dunkle Materie ist eine mit Standardmethoden nicht nachweisbare nicht-baryonische Materie, die 23% der Masse des Universums ausmacht. Daraus kann eine unglaubliche Menge an Energie gewonnen werden. Aufgrund dessen und der mit ihrer Gewinnung verbundenen Schwierigkeiten ist Dunkle Materie sehr wertvoll.',
  'sys_dark_matter_hint' => 'Mit dieser Substanz können Sie Offiziere und Kommandeure anheuern.',

  'sys_dark_matter_what_why_how' => 'Was ist <span class="dark_matter">Dunkle Materie</span> und <span class="metamatter">Metamaterie</span>',
  'sys_dark_matter_what_header' => 'Was ist <span class="dark_matter">Dunkle Materie</span>',
  'sys_dark_matter_description_header' => 'Wofür wird <span class="dark_matter">Dunkle Materie</span> benötigt',
  'sys_dark_matter_description_text' => '<span class="dark_matter">Dunkle Materie</span> ist eine In-Game-Ressource, mit der Sie verschiedene Aktionen durchführen können:
    <ul>
      <li><a href="index.php?page=premium"><span class="link">Premium-Account</span></a> kaufen</li>
      <li><a href="officer.php?mode=600"><span class="link">Söldner</span></a> für Ihr Imperium anheuern</li>
      <li>Gouverneure anheuern und zusätzliche Sektoren <a href="overview.php?mode=manage"><span class="link">auf Planeten</span></a> kaufen</li>
      <li><a href="officer.php?mode=1100"><span class="link">Blaupausen</span></a> kaufen</li>
      <li><a href="artifacts.php"><span class="link">Artefakte</span></a> kaufen</li>
      <li><a href="market.php"><span class="link">Schwarzmarkt</span></a> nutzen: Ressourcen tauschen; Schiffe verkaufen; Gebrauchtschiffe kaufen usw.</li>
      <li>...und vieles mehr</li>
    </ul>',
  'sys_dark_matter_obtain_text' => 'Sie erhalten <span class="metamatter">Dunkle Materie</span> während des Spiels: durch Erfahrungspunkte für erfolgreiche Überfälle auf fremde Planeten, Erforschung neuer Technologien sowie durch Bau und Zerstörung von Gebäuden.
    Manchmal können auch Forschungsexpeditionen <span class="metamatter">DM</span> bringen.',
  'sys_dark_matter_obtain_text_convert' => '<br />Wenn Ihnen <span class="dark_matter">Dunkle Materie</span> fehlt - kaufen Sie <span class="metamatter">Metamaterie</span>. Bei Dunkle-Materie-Mangel wird die benötigte Menge <span class="metamatter">Metamaterie</span> anstelle von <span class="dark_matter">DM</span> verwendet',

  'sys_msg_err_update_dm' => 'Fehler beim Aktualisieren der DM-Menge!',

  'sys_na' => 'Nicht verfügbar',
  'sys_na_short' => 'N/V',

  'sys_ali_res_title' => 'Allianzressourcen',

  'sys_bonus' => 'Bonus',

  'sys_of_ally' => 'der Allianz',

  'sys_hint_player_name' => 'Die Spielersuche kann nach ID oder Name erfolgen. Wenn der Spielername aus nicht lesbaren Zeichen oder nur aus Zahlen besteht - verwenden Sie die ID für die Suche',
  'sys_hint_ally_name' => 'Die Allianzsuche kann nach ID, Tag oder Name erfolgen. Wenn der Tag oder Allianzname aus nicht lesbaren Zeichen oder nur aus Zahlen besteht - verwenden Sie die ID für die Suche',

  'sys_fleet_and' => '+ Flotten',

  'sys_on_planet' => 'Auf dem Planeten',
  'fl_on_stores' => 'Auf Lager',

  'sys_ali_bonus_members' => 'Mindestgröße der Allianz für den Bonus',

  'sys_premium' => 'Premium',

  'mrc_period_list' => [
    PERIOD_MINUTE    => '1 Minute',
    PERIOD_MINUTE_3  => '3 Minuten',
    PERIOD_MINUTE_5  => '5 Minuten',
    PERIOD_MINUTE_10 => '10 Minuten',
    PERIOD_DAY       => '1 Tag',
    PERIOD_DAY_3     => '3 Tage',
    PERIOD_WEEK      => '1 Woche',
    PERIOD_WEEK_2    => '2 Wochen',
    PERIOD_MONTH     => '30 Tage',
    PERIOD_MONTH_2   => '60 Tage',
    PERIOD_MONTH_3   => '90 Tage',
  ],

  'sys_sector_buy' => '1 Sektor kaufen',

  'sys_select_confirm' => 'Auswahl bestätigen',

  'sys_capital' => 'Hauptstadt',

  'sys_result_operation' => 'Meldungen',

  'sys_password' => 'Passwort',
  'sys_password_length' => 'Passwortlänge',
  'sys_password_seed' => 'Verwendete Zeichen',

  'sys_msg_ube_report_err_not_found' => 'Kampfbericht nicht gefunden. Überprüfen Sie den Schlüssel. Es besteht auch die Möglichkeit, dass der Bericht als veraltet gelöscht wurde',

  'sys_mess_attack_report' 	=> 'Kampfbericht',
  'sys_perte_attaquant' 		=> 'Angreifer verlor',
  'sys_perte_defenseur' 		=> 'Verteidiger verlor',


  'ube_report_info_page_header' => 'Kampfbericht',
  'ube_report_info_page_header_cypher' => 'Zugangscode',
  'ube_report_info_main' => 'Grundlegende Kampfinformationen',
  'ube_report_info_date' => 'Datum und Uhrzeit',
  'ube_report_info_location' => 'Ort',
  'ube_report_info_rounds_number' => 'Anzahl der Runden',
  'ube_report_info_outcome' => 'Kampfergebnis',
  'ube_report_info_outcome_win' => 'Angreifer hat den Kampf gewonnen',
  'ube_report_info_outcome_loss' => 'Angreifer hat den Kampf verloren',
  'ube_report_info_outcome_draw' => 'Kampf endete unentschieden',
  'ube_report_info_link' => 'Link zum Kampfbericht',
  'ube_report_info_bbcode' => 'BBCode für den Chat',
  'ube_report_info_sfr' => 'Der Kampf endete in einer Runde mit einer Niederlage des Angreifers<br />Wahrscheinlich RMF',
  'ube_report_info_debris' => 'Trümmer im Orbit',
  'ube_report_info_debris_simulator' => '(ohne Mondentstehung)',
  'ube_report_info_loot' => 'Beute',
  'ube_report_info_loss' => 'Kampfverluste',
  'ube_report_info_generate' => 'Seitengenerierungszeit',

  'ube_report_moon_was' => 'Dieser Planet hatte bereits einen Mond',
  'ube_report_moon_chance' => 'Chance auf Mondentstehung',
  'ube_report_moon_created' => 'Im Orbit des Planeten entstand ein Mond mit einem Durchmesser von',

  'ube_report_moon_reapers_none' => 'Alle Schiffe mit Gravitationsantrieben wurden während des Kampfes zerstört',
  'ube_report_moon_reapers_wave' => 'Die Schiffe des Angreifers erzeugten eine fokussierte Gravitationswelle',
  'ube_report_moon_reapers_chance' => 'Chance auf Mondzerstörung',
  'ube_report_moon_reapers_success' => 'Mond zerstört',
  'ube_report_moon_reapers_failure' => 'Die Wellenstärke reichte nicht aus, um den Mond zu zerstören',

  'ube_report_moon_reapers_outcome' => 'Chance auf Antriebsexplosion',
  'ube_report_moon_reapers_survive' => 'Die genaue Kompensation der Gravitationsfelder des Systems ermöglichte es, den Rückstoß der Mondzerstörung zu dämpfen',
  'ube_report_moon_reapers_died' => 'Da die zusätzlichen Gravitationsfelder des Systems nicht kompensiert werden konnten, wurde die Flotte zerstört',

  'ube_report_side_attacker' => 'Angreifer',
  'ube_report_side_defender' => 'Verteidiger',

  'ube_report_round' => 'Runde',
  'ube_report_unit' => 'Kampfeinheit',
  'ube_report_attack' => 'Angriff',
  'ube_report_shields' => 'Schilde',
  'ube_report_shields_passed' => 'Durchbruch',
  'ube_report_armor' => 'Rüstung',
  'ube_report_damage' => 'Schaden',
  'ube_report_loss' => 'Verluste',


  'ube_report_info_restored' => 'Verteidigungsanlagen wiederhergestellt',
  'ube_report_info_loss_final' => 'Endgültige Verluste an Kampfeinheiten',
  'ube_report_info_loss_resources' => 'Verluste in Ressourcen umgerechnet',
  'ube_report_info_loss_dropped' => 'Ressourcenverluste durch verringerte Laderäume',
  'ube_report_info_loot_lost' => 'Ressourcen von den Planetenlagern abtransportiert',
  'ube_report_info_loss_gained' => 'Verluste durch Ressourcenabtransport vom Planeten',
  'ube_report_info_loss_in_metal' => 'Gesamtverluste in Metall umgerechnet',


  'ube_report_msg_body_common' => 'Der Kampf fand %s im Orbit %s [%d:%d:%d] %s<br />%s<br /><br />',
  'ube_report_msg_body_debris' => 'Infolge des Kampfes entstanden im Orbit des Planeten Trümmer:<br />',
  'ube_report_msg_body_sfr' => 'Verbindung zur Flotte verloren',

  'ube_report_capture' => 'Planeteneroberung',
  'ube_report_capture_result' => [
    UBE_CAPTURE_DISABLED => 'Planeteneroberung deaktiviert',
    UBE_CAPTURE_NON_PLANET => 'Nur Planeten können erobert werden',
    UBE_CAPTURE_NOT_A_WIN_IN_1_ROUND => 'Für die Planeteneroberung muss der Kampf in der ersten Runde gewonnen werden',
    UBE_CAPTURE_TOO_MUCH_FLEETS => 'Beim Erobern eines Planeten dürfen nur die Eroberungsflotte und die planetare Flotte am Kampf teilnehmen',
    UBE_CAPTURE_NO_ATTACKER_USER_ID => 'INTERNER FEHLER - Keine Angreifer-ID! Melden Sie dies dem Entwickler!',
    UBE_CAPTURE_NO_DEFENDER_USER_ID => 'INTERNER FEHLER - Keine Verteidiger-ID! Melden Sie dies dem Entwickler!',
    UBE_CAPTURE_CAPITAL => 'Hauptstadt kann nicht erobert werden',
    UBE_CAPTURE_TOO_LOW_POINTS => 'Planeten können nur von Spielern erobert werden, deren Gesamtpunktzahl mindestens doppelt so hoch ist wie die des Angreifers',
    UBE_CAPTURE_NOT_ENOUGH_SLOTS => 'Keine Eroberungsslots mehr verfügbar',
    UBE_CAPTURE_SUCCESSFUL => 'Planet wurde vom angreifenden Spieler erobert',
  ],

  'sys_kilometers_short' => 'km',

  'ube_simulation' => 'Simulation',

  'sys_hire_do' => 'Anheuern',

  'sys_captains' => 'Kapitäne',

  'sys_fleet_composition' => 'Flottenzusammensetzung',

  'sys_continue' => 'Fortsetzen',

  'uni_planet_density_types' => [
    PLANET_DENSITY_NONE => 'Kommt nicht vor',
    PLANET_DENSITY_ICE_HYDROGEN => 'Wasserstoffeis',
    PLANET_DENSITY_ICE_METHANE => 'Methaneis',
    PLANET_DENSITY_ICE_WATER => 'Wassereis',
    PLANET_DENSITY_CRYSTAL_RAW => 'Kristall',
    PLANET_DENSITY_CRYSTAL_SILICATE => 'Silikat',
    PLANET_DENSITY_CRYSTAL_STONE => 'Stein',
    PLANET_DENSITY_STANDARD => 'Standard',
    PLANET_DENSITY_METAL_ORE => 'Erz',
    PLANET_DENSITY_METAL_PERIDOT => 'Peridot',
    PLANET_DENSITY_METAL_RAW => 'Metall',
  ],

  'sys_planet_density' => 'Dichte',
  'sys_planet_density_units' => 'kg/m&sup3;',
  'sys_planet_density_core' => 'Kerntyp',

  'sys_change' => 'Ändern',
  'sys_show' => 'Anzeigen',
  'sys_hide' => 'Ausblenden',
  'sys_close' => 'Schließen',
  'sys_unlimited' => 'Keine Begrenzung',

  'ov_core_type_current' => 'Aktueller Kerntyp',
  'ov_core_change_to' => 'Ändern zu',
  'ov_core_err_none' => 'Der Kerntyp des Planeten wurde erfolgreich von "%s" zu "%s" geändert.<br />Neue Planetendichte %d kg/m3',
  'ov_core_err_not_a_planet' => 'Nur auf Planeten kann die Kerndichte geändert werden',
  'ov_core_err_denisty_type_wrong' => 'Falscher Kerntyp',
  'ov_core_err_same_density' => 'Der neue Kerntyp unterscheidet sich nicht vom aktuellen - nichts zu ändern',
  'ov_core_err_no_dark_matter' => 'Nicht genug Dunkle Materie, um den Kerntyp zu ändern',

  'sys_color'    => "Farbe",

  'topnav_imp_attack' => 'Ihr Imperium wurde angegriffen!',
  'topnav_user_rank' => 'Ihr aktueller Platz in der Rangstatistik',
  'topnav_users' => 'Gesamtzahl registrierter Spieler',
  'topnav_users_online' => 'Aktuelle Anzahl online Spieler',

  'topnav_refresh_page' => 'Seite neu laden',

  'sys_colonies' => 'Kolonien',
  'sys_radio' => 'Radio "Kosmos"',

  'sys_auth_provider_list' => [
    ACCOUNT_PROVIDER_NONE => 'USERS-Tabelle',
    ACCOUNT_PROVIDER_LOCAL => 'ACCOUNT-Tabelle',
    ACCOUNT_PROVIDER_CENTRAL => 'Zentrale ACCOUNT-Tabelle',
  ],

  'sys_login_messages' => [
    LOGIN_UNDEFINED => 'Login-Prozess nicht gestartet',
    LOGIN_SUCCESS => 'Login erfolgreich',
    LOGIN_ERROR_USERNAME_EMPTY => 'Spielername darf nicht leer sein',
    LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS => 'Im Spielernamen und Login sind folgende Zeichen nicht erlaubt: ',
    LOGIN_ERROR_USERNAME => 'Spieler mit diesem Namen nicht gefunden',
    LOGIN_ERROR_USERNAME_ALLY_OR_BOT => 'Dieser Name gehört einer Allianz oder einem Bot. Man kann sich damit nicht einloggen... zumindest noch nicht',
    LOGIN_ERROR_PASSWORD_EMPTY => 'Passwort darf nicht leer sein',
    LOGIN_ERROR_PASSWORD_TRIMMED => 'Passwort darf nicht mit Leerzeichen, Tabulatoren oder Zeilenumbrüchen beginnen oder enden',
    LOGIN_ERROR_PASSWORD => 'Falsches Passwort',
  //    LOGIN_ERROR_COOKIE => '',

    REGISTER_SUCCESS => 'Registrierung erfolgreich abgeschlossen',
    REGISTER_ERROR_BLITZ_MODE => 'Die Registrierung neuer Spieler im Blitz-Server-Modus ist deaktiviert',
    REGISTER_ERROR_USERNAME_WRONG => 'Ungültiger Spielername',
    REGISTER_ERROR_ACCOUNT_NAME_EXISTS => 'Der Kontoname ist bereits vergeben. Versuchen Sie, sich mit diesem Namen und Ihrem Passwort anzumelden oder das Passwort zurückzusetzen',
    REGISTER_ERROR_PASSWORD_INSECURE => 'Ungültiges Passwort. Das Passwort muss mindestens ' . PASSWORD_LENGTH_MIN . ' Zeichen lang sein',
    REGISTER_ERROR_USERNAME_SHORT => 'Name zu kurz. Der Name muss mindestens ' . LOGIN_LENGTH_MIN. ' Zeichen lang sein',
    REGISTER_ERROR_PASSWORD_DIFFERENT => 'Passwort und Bestätigungspasswort stimmen nicht überein. Überprüfen Sie die Eingabe',
    REGISTER_ERROR_EMAIL_EMPTY => 'E-Mail darf nicht leer sein',
    REGISTER_ERROR_EMAIL_WRONG => 'Die eingegebene E-Mail ist keine gültige E-Mail-Adresse. Überprüfen Sie die Schreibweise oder verwenden Sie eine andere E-Mail-Adresse',
    REGISTER_ERROR_EMAIL_EXISTS => 'Diese E-Mail-Adresse ist bereits registriert. Wenn Sie sich bereits im Spiel registriert haben - versuchen Sie, das Passwort zurückzusetzen. Andernfalls - verwenden Sie eine andere E-Mail-Adresse',

    PASSWORD_RESTORE_ERROR_EMAIL_NOT_EXISTS => 'Kein Spieler mit dieser primären E-Mail',
    PASSWORD_RESTORE_ERROR_TOO_OFTEN => 'Wiederherstellungscode kann nur alle 10 Minuten angefordert werden. Wenn Sie keine E-Mail erhalten haben - überprüfen Sie den Spam-Ordner oder schreiben Sie eine E-Mail an die Serveradministration an die Adresse <span class="ok">' . $config->server_email . '</span> von der E-Mail, die Sie bei der Registrierung verwendet haben',
    PASSWORD_RESTORE_ERROR_SENDING => 'Fehler beim Senden der E-Mail. Schreiben Sie eine E-Mail an die Serveradministration an die Adresse <span class="ok">' . $config->server_email . '</span>',
    PASSWORD_RESTORE_SUCCESS_CODE_SENT => 'E-Mail mit Wiederherstellungscode erfolgreich gesendet',

    PASSWORD_RESTORE_ERROR_CODE_EMPTY => 'Wiederherstellungscode darf nicht leer sein',
    PASSWORD_RESTORE_ERROR_CODE_WRONG => 'Falscher Wiederherstellungscode',
    PASSWORD_RESTORE_ERROR_CODE_TOO_OLD => 'Wiederherstellungscode abgelaufen. Holen Sie einen neuen',
    PASSWORD_RESTORE_ERROR_CODE_OK_BUT_NO_ACCOUNT_FOR_EMAIL => 'Der Wiederherstellungscode ist korrekt, aber es wurde kein Konto mit dieser E-Mail gefunden. Möglicherweise wurde es gelöscht oder ein interner Fehler ist aufgetreten. Wenden Sie sich an die Serveradministration',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT => 'Passwort erfolgreich zurückgesetzt. Ihnen wurde eine E-Mail mit dem neuen Passwort gesendet',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR => 'Fehler beim Senden der E-Mail mit dem neuen Passwort. Holen Sie einen neuen Wiederherstellungscode und versuchen Sie es erneut',

    REGISTER_ERROR_PLAYER_NAME_TRIMMED => 'Der Spielername darf nicht mit Leerzeichen (Zeichen "Leerzeichen", "Tabulator", "Zeilenumbruch" usw.) beginnen oder enden',
    REGISTER_ERROR_PLAYER_NAME_EMPTY => 'Der Spielername darf nicht leer sein',
    REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS => 'Der Spielername enthält unzulässige Zeichen',
    REGISTER_ERROR_PLAYER_NAME_SHORT => 'Der Spielername darf nicht kürzer als ' . LOGIN_LENGTH_MIN . ' Zeichen sein',
    REGISTER_ERROR_PLAYER_NAME_EXISTS => 'Dieser Spielername ist bereits vergeben. Bitte wählen Sie einen anderen',

    // Interne Fehler
    AUTH_ERROR_INTERNAL_PASSWORD_CHANGE_ON_RESTORE => 'INTERNER FEHLER! MELDEN SIE DIES DER ADMINISTRATION! Fehler beim Passwortwechsel. Bitte melden Sie diesen Fehler der Universumsadministration!',
    PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT => 'Passwortwiederherstellung für das Serverteam verboten. Wenden Sie sich an den Administrator',
    REGISTER_ERROR_ACCOUNT_CREATE => 'Fehler beim Erstellen des Kontos! Bitte melden Sie dies der Administration!',
    LOGIN_ERROR_SYSTEM_ACCOUNT_TRANSLATION => 'SYSTEMFEHLER - FEHLER IN DER PROVIDER-ÜBERSETZUNGSTABELLE! Melden Sie dies der Serveradministration!',
    PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS => 'Interner Fehler - beim Zurücksetzen des Passworts wurde kein Konto gefunden! Melden Sie diesen Fehler der Administration!',
    AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION => 'INTERNER FEHLER! Keine Konten zum Zurücksetzen des Passworts bei korrektem Bestätigungscode! Bitte melden Sie diesen Fehler der Universumsadministration!',
    LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET => 'INTERNER FEHLER! MELDEN SIE DIES DER ADMINISTRATION! Kein Konto bei cookie_set() gesetzt! Bitte melden Sie diesen Fehler der Universumsadministration!',
  ],

  'log_reg_email_title' => "Ihre Registrierung auf dem Server %1\$s des Spiels SuperNova",
  'log_reg_email_text' => "Registrierungsbestätigung für %3\$s\r\n\r\n
  Diese E-Mail enthält Ihre Registrierungsdaten auf dem Server %1\$s des Spiels SuperNova\r\n
  Bewahren Sie diese Daten an einem sicheren Ort auf\r\n\r\n
  Serveradresse: %2\$s\r\n
  Ihr Login: %3\$s\r\n
  Ihr Passwort: %4\$s\r\n\r\n
  Vielen Dank für Ihre Registrierung auf unserem Server! Wir wünschen Ihnen viel Erfolg im Spiel!\r\n
  Die Administration des Servers %1\$s %2\$s\r\n\r\n
  Der Server läuft auf der freien Engine 'Project SuperNova.WS'. Entfache deine SuperNova http://supernova.ws/",

   'log_lost_email_title' => 'SuperNova, Universum %s: Passwort zurücksetzen',
  'log_lost_email_code' => "Jemand (möglicherweise Sie) hat eine Passwortzurücksetzung im Universum %1\$4 des Spiels SuperNova angefordert. Falls Sie dies nicht veranlasst haben, ignorieren Sie diese E-Mail einfach.\r\n\r\nUm Ihr Passwort zurückzusetzen, besuchen Sie die folgende Adresse:\r\n%1\$s?password_reset_confirm=1&password_reset_code=%2\$s#tab_password_reset\r\n oder geben Sie den Bestätigungscode \"%2\$s\" (OHNE ANFÜHRUNGSZEICHEN!) auf der Seite %1\$s#tab_password_reset ein.\r\n\r\nDieser Code ist gültig bis %3\$s. Danach müssen Sie einen neuen Bestätigungscode anfordern.",
  'log_lost_email_pass' => "Sie haben Ihr Passwort auf dem Server %1\$s des Spiels 'SuperNova' zurückgesetzt.\r\n\r\nIhr Spielername:\r\n%2\$s\r\n\r\nIhr neues Passwort:\r\n%3\$s\r\n\r\nMerken Sie es sich!\r\n\r\nSie können sich unter " . SN_ROOT_VIRTUAL . "login.php mit den oben genannten Daten anmelden.",

  'login_player_register_player_name' => 'Spielername',
  'login_player_register_description' => 'Nur noch ein Schritt! Wählen Sie einen Spielernamen - den Namen, der anderen Spielern in diesem Universum angezeigt wird.',
  'login_player_register_do' => 'Namen wählen',
  'login_player_register_logout' => 'Mit einem anderen Konto anmelden',
  'login_player_register_logout_description' => 'Wenn Sie sich mit einem anderen Konto anmelden möchten, klicken Sie auf die Schaltfläche',

  'sys_password_reset_message_body' => "Sie haben Ihr Passwort für den Zugang zum Spiel in diesem Universum zurückgesetzt.\r\n\r\nIhr neues Passwort:\r\n\r\n%1\$s\r\n\r\nMerken Sie es sich!\r\n\r\nSie können Ihr Passwort jederzeit unter 'Einstellungen' ändern.",

  'sys_login_password_show' => 'Passwort anzeigen',
  'sys_login_password_hide' => 'Passwort verbergen',
  'sys_password_repeat' => 'Passwort wiederholen',

  'sys_game_disable_reason' => [
    GAME_DISABLE_NONE => 'Spiel aktiviert',
    GAME_DISABLE_REASON => 'Spiel deaktiviert. Spieler sehen die Nachricht',
    GAME_DISABLE_UPDATE => 'Spiel wird aktualisiert',
    GAME_DISABLE_STAT => 'Statistik wird neu berechnet',
    GAME_DISABLE_INSTALL => 'Spiel ist noch nicht konfiguriert',
    GAME_DISABLE_MAINTENANCE => 'Wartung der Serverdatenbank',
    GAME_DISABLE_EVENT_BLACK_MOON => 'Schwarzer Mond!',
    GAME_DISABLE_EVENT_OIS => 'Objekte im Weltraum',
  ],

  'sys_sector_purchase_log' => 'Benutzer {%2$d} {%1$s} hat 1 Sektor auf Planet {%5$d} {%3$s} Typ "%4$s" für %6$d DM gekauft',

  'sys_notes' => 'Notizen',
  'sys_notes_priorities' => [
    0 => 'Ganz unwichtig',
    1 => 'Unwichtig',
    2 => 'Normal',
    3 => 'Wichtig',
    4 => 'Sehr wichtig',
  ],

  'sys_milliseconds' => 'Millisekunden',

  'sys_gender' => 'Geschlecht',
  'sys_gender_list' => [
    GENDER_UNKNOWN => 'Wird es selbst entscheiden',
    GENDER_MALE => 'Männlich',
    GENDER_FEMALE => 'Weiblich',
  ],

  'imp_stat_header' => 'Diagramm der Statistikänderungen',
  'imp_stat_types' => [
    'TOTAL_RANK' => 'Platz in der Gesamtstatistik',
    'TOTAL_POINTS' => 'Gesamtpunktzahl',
    'TECH_RANK' => 'Platz in der Forschungsstatistik',
    'TECH_POINTS' => 'Punkte für Forschung',
    'BUILD_RANK' => 'Platz in der Gebäudestatistik',
    'BUILD_POINTS' => 'Punkte für Gebäude',
    'DEFS_RANK' => 'Platz in der Verteidigungsstatistik',
    'DEFS_POINTS' => 'Punkte für Verteidigung',
    'FLEET_RANK' => 'Platz in der Flottenstatistik',
    'FLEET_POINTS' => 'Punkte für Flotten',
    'RES_RANK' => 'Platz in der Ressourcenstatistik',
    'RES_POINTS' => 'Punkte für freie Ressourcen',
  ],

  'sys_date' => 'Datum',

  'sys_blitz_global_button' => 'Blitz-Server',
  'sys_blitz_page_disabled' => 'Im Blitz-Server-Modus ist diese Seite nicht verfügbar',
  'sys_blitz_registration_disabled' => 'Registrierung für den Blitz-Server ist deaktiviert',
  'sys_blitz_registration_no_users' => 'Keine registrierten Spieler',
  'sys_blitz_registration_player_register' => 'Für das Spiel registrieren',
  'sys_blitz_registration_player_register_un' => 'Registrierung zurückziehen',
  'sys_blitz_registration_closed' => 'Registrierung ist derzeit geschlossen. Bitte versuchen Sie es später erneut',
  'sys_blitz_registration_player_generate' => 'Logins und Passwörter generieren',
  'sys_blitz_registration_player_import_generated' => 'Generierte Zeichenkette importieren',
  'sys_blitz_registration_player_name' => 'Ihr Login für den Blitz-Server:',
  'sys_blitz_registration_player_password' => 'Ihr Passwort für den Blitz-Server:',
  'sys_blitz_registration_server_link' => 'Link zum Blitz-Server',
  'sys_blitz_registration_player_blitz_name' => 'Name auf dem Blitz-Server',
  'sys_blitz_registration_price' => 'Kosten für die Bewerbung',
  'sys_blitz_registration_mode_list' => [
    BLITZ_REGISTER_DISABLED => 'Registrierung deaktiviert',
    BLITZ_REGISTER_OPEN => 'Registrierung geöffnet',
    BLITZ_REGISTER_CLOSED => 'Registrierung geschlossen',
    BLITZ_REGISTER_SHOW_LOGIN => 'Logins und Passwörter sichtbar',
    BLITZ_REGISTER_DISCLOSURE_NAMES => 'Ergebnisbekanntgabe',
  ],

  'survey' => 'Umfrage',
  'survey_questions' => 'Auswahlmöglichkeiten',
  'survey_questions_hint' => '1 Option pro Zeile',
  'survey_questions_hint_edit' => 'Das Bearbeiten der Umfrage setzt die Ergebnisse zurück',
  'survey_until' => 'Dauer der Umfrage (standardmäßig 1 Tag)',

  'survey_votes_total_none' => 'Noch hat niemand abgestimmt... Seien Sie der Erste!',
  'survey_votes_total_voted' => 'Bisher abgestimmt:',
  'survey_votes_total_voted_join' => 'Stimmen Sie ab - oder Sie verlieren!',
  'survey_votes_total_voted_has_answer' => 'Sie haben bereits abgestimmt. Zusammen mit Ihnen haben abgestimmt:',

  'survey_lasts_until' => 'Die Umfrage läuft bis',

  'survey_select_one' => 'Wählen Sie eine Antwortmöglichkeit und klicken Sie auf',
  'survey_confirm' => 'Abstimmen!',
  'survey_result_sent' => 'Ihre Stimme wurde gezählt. Aktualisieren Sie die Seite oder nutzen Sie den Link <a class="link" href="announce.php">Neuigkeiten</a>, um die aktuellen Umfrageergebnisse zu sehen.',
  'survey_complete' => 'Umfrage abgeschlossen',

  'player_option_fleet_ship_sort' => [
    PLAYER_OPTION_SORT_DEFAULT => 'Standard',
    PLAYER_OPTION_SORT_NAME => 'Nach Name',
    PLAYER_OPTION_SORT_ID => 'Nach ID',
    PLAYER_OPTION_SORT_SPEED => 'Nach Geschwindigkeit',
    PLAYER_OPTION_SORT_COUNT => 'Nach Anzahl',
  ],

  'player_option_building_sort' => [
    PLAYER_OPTION_SORT_DEFAULT => 'Standard',
    PLAYER_OPTION_SORT_NAME => 'Nach Name',
    PLAYER_OPTION_SORT_ID => 'Nach ID',
    PLAYER_OPTION_SORT_CREATE_TIME_LENGTH => 'Nach Bauzeit',
  ],

  'sys_sort' => 'Sortierung',
  'sys_sort_inverse' => 'In umgekehrter Reihenfolge',

  'sys_blitz_reward_log_message' => 'Blitz-Server %1$d Platz Blitz-Name "%2$s"',
  'sys_blitz_registration_view_stat' => 'Blitz-Server-Statistik anzeigen',

  'sys_login_register_message_title' => "Ihr Name und Passwort für den Spielzugang",
  'sys_login_register_message_body' => "Ihr Spielname (Login)\r\n%1\$s\r\n\r\nIhr Passwort\r\n%2\$s\r\n\r\nNotieren oder merken Sie sich diese Daten!",

  'auth_provider_list' => [
    ACCOUNT_PROVIDER_NONE => 'Users-Tabelle',
    ACCOUNT_PROVIDER_LOCAL => 'Account-Tabelle',
    ACCOUNT_PROVIDER_CENTRAL => 'Zentrale Speicherung',
  ],

  'bld_autoconvert' => 'Automatische Konvertierung bei der Erstellung von Einheit {%1$d} "%4$s" in Menge %2$d auf Planet %3$s zum Preis "%5$s". Debug: $resource_got = "%6$s", $exchange = %7$s""',

  'news_show_rest' => 'Nachrichtentext anzeigen',

  'wiki_requrements' => 'Voraussetzungen',
  'wiki_grants' => 'Gewährt',

  'que_slot_length' => 'Slots',
  'que_slot_length_long' => 'Warteschlangen-Slots',

  'sys_buy_doing' => 'Sie kaufen',
  'sys_planet_sector' => 'Sektor',
  'sys_planet_on' => 'auf',

  'sys_purchase_confirm' => 'Kauf bestätigen',

  'sys_confirm_action_title' => 'Bestätigen Sie Ihre Aktion',
  'sys_confirm_action' => 'Möchten Sie dies wirklich tun?',

  'sys_system_speed_original' => 'Originalgeschwindigkeit',
  'sys_system_speed_for_action' => 'Im Rahmen der Aktion',

  'menu_info_best_battles' => 'Beste Schlachten',

  'sys_cost' => 'Kosten',
  'sys_price' => 'Preis',

  'sys_governor_none' => 'Gouverneur nicht eingestellt',
  'sys_governor_hire' => 'Gouverneur einstellen',
  'sys_governor_upgrade_or_change' => 'Gouverneur verbessern oder wechseln',

  'tutorial_prev' => '<< Zurück',
  'tutorial_next' => 'Weiter >>',
  'tutorial_finish' => 'Abschließen',
  'tutorial_window' => 'In Fenster öffnen',
  'tutorial_window_off' => 'Zur Seite zurückkehren',

  'tutorial_error_load' => "Fehler beim Laden des Tutorials - versuchen Sie es erneut! Bei wiederholtem Fehler - melden Sie es der Spieladministration.",
  'tutorial_error_next' => "Fehler: Nächste Tutorialseite existiert nicht - melden Sie es der Spieladministration.",
  'tutorial_error_prev' => "Fehler: Vorherige Tutorialseite existiert nicht - melden Sie es der Spieladministration.",

  'sys_click_here_to_continue' => 'Klicken Sie hier, um fortzufahren',

  'sys_module_error_not_found' => 'Belohnungsmodul "%1$s" nicht gefunden oder deaktiviert!',

  'rank_page_title' => 'Militärränge',
  'rank' => 'Rang',
  'ranks' => [
    0  => 'Kadett',
    1  => 'Rekrut',
    2  => 'Gefreiter',
    3  => 'Obergefreiter',
    4  => 'Korporal',
    5  => 'Feldwebel',
    6  => 'Stabsfeldwebel',
    7  => 'Fähnrich',
    8  => 'Oberfähnrich',
    9  => 'Leutnant',
    10 => 'Oberleutnant',
    11 => 'Hauptmann',
    12 => 'Major',
    13 => 'Oberstleutnant',
    14 => 'Oberst',
    15 => 'Konteradmiral',
    16 => 'Vizeadmiral',
    17 => 'Admiral',
    18 => 'Flottenadmiral',
    19 => 'Marschall',
    20 => 'Generalissimus',
  ],
];