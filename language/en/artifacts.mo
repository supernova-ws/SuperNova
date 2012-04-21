<?php

/*
#############################################################################
#  Filename: artifacts.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2011 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 31a13
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$lang = array_merge($lang, array(
  'art_use'             => 'Use artifact',

  'art_lhc_from'        => 'Large Hadron Collider',
  'art_lhc_subj'        => 'Creating moon...',
  'art_lhc_moon_create' => 'LHC\'s gravity wave connects large pieces of debris resulting to creation new moon %s on coordinates %s!',
  'art_lhc_moon_exists' => 'There is already moon on moon orbit on current coordinates',
  'art_lhc_moon_fail'   => 'Unfortunatly LHC\'s gravity wave was not enough to create a new moon',

  'art_rcd_from'        => 'Rapid Colony Deployer',
  'art_rcd_subj'        => 'Colony deployed',
  'art_rcd_ok'          => '%1$s succesfully deployed colony on planet  %2$s coordinates %3$s',
  'art_rcd_err_moon'    => 'RCD can be deployed on planet',
  'art_rcd_err_no_sense'=> 'RCD detected that there will be no improvement to current buildings and aborted deployment',
  'art_rcd_err_que'     => 'RCD can not be deployed on planet where building ongoing. Cancel all construction tasks and try to deploy RCD again',

  'art_err_no_artifact' => 'You did not have this artifact',

  'art_page_hint'       => '<ul>
    <li>Artifacts are rare objects with unique properties</li>
    <li>Artifacts are expendables i.e. after use Artifact disappears</li>
    <li>Some Artifacts too powerfull that can exist only within limit numbers in one Empire</li>
    <li>Usually Artifact effect extends only on planet where it used but some of them has Empire-wide effect.
    Rarest and powerfullest Artifacts extends their effect to whole solar system, galaxy or even Universe!</li>
  </ul>',
));

?>
