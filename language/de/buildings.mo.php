<?php

/*
#############################################################################
#  Filename: buildings.mo
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

$a_lang_array = array(
  'built' => 'Gebaut',
  'Fleet' => 'Flotte',
  'fleet' => 'Flotte',
  'Defense' => 'Verteidigung',
  'defense' => 'Verteidigung',
  'Research' => 'Forschung',
  'level' => 'Stufe',
  'dispo' => 'Verfügbar',
  'load_det' => 'Klicken Sie auf das Bild für 3D-Ansicht',
  'off_det' => 'Erneut klicken deaktiviert 3D-Ansicht',
  'allowed_aya' => 'Verfügbare',
  'allowed_ye' => 'Verfügbare',
  'allowed_yi' => 'Verfügbarer',
  'mech_info' => 'Technische Spezifikationen',
  'fst_bld_load' => 'Auftrag wird bearbeitet.<br>Bitte warten...',
  'fst_bld' => 'Schnellauftrag:',
  'price' => 'Kosten',
  'builds' => 'Gebäude',
  'destroy_price' => 'Abbaukosten',
  'no_fields' => 'Keine freien Felder auf dem Planeten',
  'can_build' => 'Bau möglich: ',
  'Requirements' => 'Voraussetzungen: ',
  'Requires' => 'Benötigte Ressourcen ',
  'Rest_ress' => 'Verbleibende Ressourcen ',
  'Rest_ress_fleet' => 'Inkl. ankommender Flotten',
  'Rechercher' => 'Erforschen',
  'ConstructionTime' => 'Bauzeit ',
  'DestructionTime' => 'Abbauzeit ',
  'ResearchTime' => 'Forschungszeit ',
  'Construire' => 'Bauen',
  'BuildFirstLevel' => 'Bauen',
  'BuildNextLevel' => 'Nächste Stufe bauen ',
  'completed' => 'Abgeschlossen',
  'in_working' => 'Beschäftigt',
  'work_todo' => 'Beschäftigt',
  'total_left_time' => 'Verbleibende Zeit',
  'only_one' => 'Sie können nur einen Schildgenerator bauen.',
  'b_no_silo_space' => 'Raketensilo ist voll.',
  'que_full' => 'Bauwarteschlange ist voll!',
  'Build_lab' => 'Baufehler',
  'NoMoreSpace' => 'Planet ist voll!',
  'InBuildQueue' => 'In Bauwarteschlange',
  'bld_usedcells' => 'Belegte Felder',
  'bld_theyare' => 'Es sind',
  'bld_cellfree' => 'freie Felder vorhanden',
  'DelFromQueue' => 'abbrechen',
  'DelFirstQueue' => 'Pausieren',
  'cancel' => 'Abbrechen',
  'continue' => 'Fortsetzen',
  'ready' => 'Warten',
  'destroy' => 'Abbauen',
  'on' => 'auf',
  'attention' => 'Achtung! Hackversuch festgestellt! Aktion wurde protokolliert!',
  'no_laboratory' => 'Forschungslabor nicht gebaut!',
  'need_hangar' => 'Werft nicht gebaut!',
  'labo_on_update' => 'Labor wird aktualisiert!',
  'fleet_on_update' => 'Werft wird modernisiert!',
  'Total_techs' => 'Gesamte Forschungen',
  'eco_bld_page_hint' => '<ul><li>Für Einheiteninformationen Mauszeiger über Bild bewegen</li>
  <li>Klick auf Bild wählt Einheit. Erneuter Klick hebt Auswahl auf</li>
  <li>Detaillierte Beschreibung durch Klick auf blaues "i"-Symbol</li>
  <li>Bau möglich durch Klick auf "+" oder "Bauen"-Link</li>
  <li>Abbau durch Klick auf "-" oder entsprechenden Link</li></ul>',
  'eco_price' => 'Kosten',
  'eco_left' => 'Rest',
  'eco_bld_resources_not_enough' => 'Nicht genug Ressourcen für Bauaufträge',

  'eco_bld_msg_err_research_in_progress' => 'Forschung läuft bereits',
  'eco_bld_msg_err_not_research' => 'Nur Technologien können erforscht werden',
  'eco_bld_msg_err_requirements_not_meet' => 'Forschungsvoraussetzungen nicht erfüllt',
  'eco_bld_msg_err_laboratory_upgrading' => 'Forschungslabore werden umgebaut.<br/><br/>Während Bau/Umbau von Laboren (auch in Warteschlange) ist Forschung nicht möglich.<br/><br/>Entfernen Sie alle Labor-Bauaufträge um Forschung zu starten',

  'eco_bld_unit_info_extra_show' => 'Zusatzinformationen anzeigen',
  'eco_bld_unit_info_extra_hide' => 'Zusatzinformationen verbergen',
  'eco_bld_unit_info_extra_none' => 'Keine Zusatzinformationen',

  'eco_bld_autoconvert' => 'Autokonvertierung',
  'eco_bld_autoconvert_explain' => 'Fehlende Ressourcen werden automatisch umgewandelt (Metall, Kristall, Deuterium) und Bau/Forschung gestartet.\r\n\r\n',
  'eco_bld_autoconvert_dark_matter_none' => 'Für Autokonvertierung fehlen {0} Dunkle Materie.',
  'eco_bld_autoconvert_confirm' => 'Diese Aktion kostet {0} Dunkle Materie.\r\n\r\nFortfahren?',

  'eco_que_clear_dialog_title' => 'Warteschlange leeren',
  'eco_que_clear_dialog_text' => 'Diese Aktion löscht die gesamte Warteschlange!<br /><br />Alle unfertigen Bauten/Forschungen werden abgebrochen.<br />Ressourcen werden zurückerstattet.<br /><br />Wirklich fortfahren?',

  'eco_que_artifact_dialog_title' => '{0} verwenden',
  'eco_que_artifact_dialog_text' => "Artefakt \"{0}\" beschleunigt Bau/Forschung.<br /><br />Bei >1h Restzeit: Halbierung<br />Bei <1h: Sofortige Fertigstellung<br /><br />Nicht nutzbar bei <1min Restzeit",

  'eco_bld_research_page_name' => 'Technologieforschung',
  'eco_bld_research_page_novapedia' => 'Technologieliste in Novapedia',
);