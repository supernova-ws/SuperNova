<?php

/*
#############################################################################
#  Filename: artifacts.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2011-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 43a16.13
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'art_use'             => 'Use artifact',

  'art_lhc_from'        => 'Large Hadron Collider',
  'art_lhc_subj'        => 'Creating moon...',
  'art_moon_create'   => array(
    ART_LHC => 'LHC\'s gravity wave connects large pieces of debris resulting to creation new moon %s on coordinates %s!',
    ART_HOOK_SMALL => 'Small Hook launches new moon %1$s diameter %3$s kilometers on coordinates %2$s!',
    ART_HOOK_MEDIUM => 'Medium Hook launches new moon %1$s diameter %3$s kilometers on coordinates %2$s!',
    ART_HOOK_LARGE => 'Large Hook launches new moon %1$s diameter %3$s kilometers on coordinates %2$s!',
  ),
  'art_moon_exists' => 'There is already moon on moon orbit on current coordinates',
  'art_lhc_moon_fail'   => 'Unfortunatly LHC\'s gravity wave was not enough to create a new moon',

  'art_rcd_from'        => 'Rapid Colony Deployer',
  'art_rcd_subj'        => 'Colony deployed',
  'art_rcd_ok'          => '%1$s succesfully deployed colony on planet  %2$s coordinates %3$s',
  'art_rcd_err_moon'    => 'RCD can be deployed on planet',
  'art_rcd_err_no_sense'=> 'RCD detected that there will be no improvement to current buildings and aborted deployment',
  'art_rcd_err_que'     => 'RCD can not be deployed on planet where building ongoing. Cancel all construction tasks and try to deploy RCD again',

  'art_heurestic_chip_ok' => 'Decreased research time for %s level %d down by %s',
  'art_heurestic_chip_subj' => 'Research speed up',
  'art_heurestic_chip_no_research' => 'There is no research currently ongoing or there is less then 1 minute on current research',

  'art_nano_builder_ok' => '%s time of "%s" level %d on planet %s %s decreased down by %s',
  'art_nano_builder_build' => 'Building',
  'art_nano_builder_destroy' => 'Destruction',
  'art_nano_builder_subj' => 'Building operation speed up',
  'art_nano_builder_no_que' => 'There is no construction operation performed on planet or current construction time is less then 1 minute',

  'art_err_no_artifact' => 'You did not have this artifact',

  'art_page_hint'       => '<ul>
    <li>Artifacts are rare objects with unique properties</li>
    <li>Artifacts are expendables i.e. after use Artifact disappears</li>
    <li>Some Artifacts too powerfull that can exist only within limit numbers in one Empire</li>
    <li>Usually Artifact effect extends only on planet where it used but some of them has Empire-wide effect.
    Rarest and powerfullest Artifacts extends their effect to whole solar system, galaxy or even Universe!</li>
  </ul>',
));
