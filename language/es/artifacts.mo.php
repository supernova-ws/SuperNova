<?php

/*
#############################################################################
#  Filename: artifacts.mo
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
  'art_use'             => 'Usar artefacto',

  'art_lhc_from'          => 'Gran Colisionador de Hadrones',
  'art_lhc_subj'          => 'Intento de creación de luna',
  'art_moon_create'   => array(
    ART_LHC => '¡La onda gravitacional del GCH fusionó fragmentos de metal y cristal en órbita, creando una nueva luna %s en las coordenadas %s!',
    ART_HOOK_SMALL => '¡El Gancho Pequeño lanzó la luna %1$s (diámetro: %3$s km) en %2$s!',
    ART_HOOK_MEDIUM => '¡El Gancho Mediano lanzó la luna %1$s (diámetro: %3$s km) en %2$s!',
    ART_HOOK_LARGE => '¡El Gancho Grande lanzó la luna %1$s (diámetro: %3$s km) en %2$s!',
  ),
  'art_moon_exists'   => 'Ya existe una luna en estas coordenadas',
  'art_lhc_moon_fail'     => 'La onda gravitacional del GCH no fue suficiente para formar una luna',

  'art_rcd_from'          => 'Complejo de Colonización Autónoma',
  'art_rcd_subj'          => 'Colonia desplegada',
  'art_rcd_ok'            => '%1$s desplegó con éxito una colonia en el planeta %2$s (coordenadas: %3$s)',
  'art_rcd_err_moon'      => 'El CCA solo puede desplegarse en planetas',
  'art_rcd_err_no_sense'  => 'El CCA detectó que ninguna mejora era posible y canceló el despliegue',
  'art_rcd_err_que'       => 'El CCA no puede desplegarse en planetas con construcciones en curso. Cancela todas las órdenes e inténtalo de nuevo',

  'art_heurestic_chip_ok' => 'Tiempo de investigación de "%s" (nivel %d) reducido en %s',
  'art_heurestic_chip_subj' => 'Aceleración de investigación',
  'art_heurestic_chip_no_research' => 'No hay investigaciones en curso o el tiempo restante es menor a 1 minuto',

  'art_nano_builder_ok' => 'Tiempo de %s del edificio "%s" (nivel %d) en el planeta %s %s reducido en %s',
  'art_nano_builder_build' => 'construcción',
  'art_nano_builder_destroy' => 'demolición',
  'art_nano_builder_subj' => 'Aceleración de operación',
  'art_nano_builder_no_que' => 'No hay operaciones en curso o el tiempo restante es menor a 1 minuto',

  'art_err_no_artifact'  => 'No tienes este artefacto',

  'art_page_hint'        => '<ul>
    <li>Los artefactos son objetos raros con efectos únicos</li>
    <li>Son de un solo uso: desaparecen después de activarse</li>
    <li>Algunos son tan poderosos que su cantidad por Imperio está limitada</li>
    <li>La mayoría afecta solo al planeta de uso, pero los más raros pueden influir en sistemas solares, galaxias o ¡todo el universo!</li>
  </ul>',
);