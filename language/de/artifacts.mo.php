<?php

/*
#############################################################################
#  Filename: artifacts.mo
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
  'art_use'             => 'Artefakt verwenden',

  'art_lhc_from'          => 'Großer Hadronen-Speicherring',
  'art_lhc_subj'          => 'Monderschaffungsversuch',
  'art_moon_create'   => array(
    ART_LHC => 'Die Gravitationswelle des GHS verband große Metall- und Kristallbrocken in der Umlaufbahn, wodurch ein neuer Mond %s bei den Koordinaten %s entstand!',
    ART_HOOK_SMALL => 'Kleiner Haken erschuf Mond %1$s mit einem Durchmesser von %3$s Kilometern bei den Koordinaten %2$s!',
    ART_HOOK_MEDIUM => 'Mittlerer Haken erschuf Mond %1$s mit einem Durchmesser von %3$s Kilometern bei den Koordinaten %2$s!',
    ART_HOOK_LARGE => 'Großer Haken erschuf Mond %1$s mit einem Durchmesser von %3$s Kilometern bei den Koordinaten %2$s!',
  ),
  'art_moon_exists'   => 'In der Mondumlaufbahn an diesen Koordinaten existiert bereits ein Mond',
  'art_lhc_moon_fail'     => 'Die Gravitationswelle des GHS war nicht stark genug, um einen neuen Mond zu erschaffen',

  'art_rcd_from'          => 'Autonomes Kolonisierungs-Komplex',
  'art_rcd_subj'          => 'Kolonie errichtet',
  'art_rcd_ok'            => '%1$s hat erfolgreich eine Kolonie auf Planet %2$s bei den Koordinaten %3$s errichtet',
  'art_rcd_err_moon'      => 'AKK kann nur auf Planeten eingesetzt werden',
  'art_rcd_err_no_sense'  => 'AKK erkannte, dass keine Gebäude verbessert werden würden und brach den Einsatz ab',
  'art_rcd_err_que'       => 'AKK kann nicht auf Planeten mit laufenden Bauaufträgen eingesetzt werden. Bitte alle Bauaufträge abbrechen und den AKK erneut einsetzen',

  'art_heurestic_chip_ok' => 'Forschungszeit für Technologie "%s" (Stufe %d) wurde um %s reduziert',
  'art_heurestic_chip_subj' => 'Forschungszeitbeschleunigung',
  'art_heurestic_chip_no_research' => 'Derzeit wird keine Forschung durchgeführt oder die aktuelle Forschungszeit beträgt weniger als 1 Minute',

  'art_nano_builder_ok' => 'Die %s-Zeit für Gebäude "%s" (Stufe %d) auf Planet %s %s wurde um %s reduziert',
  'art_nano_builder_build' => 'Bau',
  'art_nano_builder_destroy' => 'Abriss',
  'art_nano_builder_subj' => 'Bauoperationsbeschleunigung',
  'art_nano_builder_no_que' => 'Derzeit werden auf diesem Planeten keine Bauoperationen durchgeführt oder die aktuelle Operationszeit beträgt weniger als 1 Minute',

  'art_err_no_artifact'  => 'Sie besitzen das benötigte Artefakt nicht',

  'art_page_hint'        => '<ul>
    <li>Artefakte sind seltene Objekte mit einzigartigen Eigenschaften</li>
    <li>Artefakte sind Einweg-Gegenstände - nach Gebrauch verschwinden sie</li>
    <li>Manche Artefakte sind so mächtig, dass ihre Anzahl in einem Imperium begrenzt ist</li>
    <li>Normalerweise wirken Artefakte auf den Anwendungsplaneten, aber einige haben imperiumsweite Effekte.
    Die seltensten und wertvollsten Artefakte können auf ganze Sonnensysteme, Galaxien oder sogar das gesamte Universum wirken!</li>
  </ul>',
));