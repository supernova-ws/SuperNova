<?php

/*
#############################################################################
#  Filename: mercenary.mo
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
* @version 46a158
*
* @clean - all constants is used
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE'))
{
  exit;
}

$a_lang_array = (array(
  'mrc_up_to' => 'bis',
  'mrc_hire' => 'Anheuern',
  'mrc_hire_for' => 'Anheuern für',
  'mrc_allowed' => 'Verfügbare',
  'mrc_msg_error_wrong_mercenary' => 'Falsche Söldner-ID',
  'mrc_msg_error_wrong_level' => 'Falsches Söldner-Level',
  'mrc_msg_error_wrong_period' => 'Ungültige Anheuerungsdauer',
  'mrc_msg_error_already_hired' => 'Söldner bereits angeheuert. Entlassen Sie ihn oder warten Sie das Ende der Anheuerungszeit ab',
  'mrc_msg_error_no_resource' => 'Nicht genug Dunkle Materie zum Anheuern',
  'mrc_msg_error_requirements' => 'Anheuerungsvoraussetzungen nicht erfüllt',

  'mrc_dismiss' => 'Entlassen',
  'mrc_dismiss_confirm' => 'Bei Entlassung des Söldners geht die gesamte für seine Anheuerung ausgegebene DM verloren! Möchten Sie den Söldner wirklich entlassen?',
  'mrc_dismiss_before_hire' => 'Um das Level eines angeheuerten Söldners zu ändern, müssen Sie zuerst den aktuellen entlassen - mit Verlust der für die Anheuerung ausgegebenen DM',

  'mrc_mercenary_hired_log' => 'Söldner "%1$s" ID %2$d für %3$d DM auf %4$d Tage angeheuert',
  'mrc_mercenary_dismissed_log' => 'VERLOREN: %7$d Tage Anheuerung und %8$d DM zum aktuellen Preis! Söldner "%1$s" ID %2$d entlassen, angeheuert auf %4$d Tage (von %5$s bis %6$s) mit aktuellem Wert von %3$d DM',
  'mrc_plan_bought_log' => '"%1$s" ID %2$d für %3$d DM gekauft',
));