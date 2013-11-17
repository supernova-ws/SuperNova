<?php

/*
#############################################################################
#  Filename: mercenary.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 38a3.1
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

if (empty($lang) || !is_array($lang))
{
  $lang = array();
}

// Officers/mercenaries
$lang = array_merge($lang, array(
  'mrc_up_to' => 'up to',
  'mrc_hire' => 'Hire',
  'mrc_hire_for' => 'Hire for',
  'mrc_allowed' => 'Allowed',
  'mrc_msg_error_wrong_mercenary' => 'Wrong mercenary',
  'mrc_msg_error_wrong_level' => 'Wrong mercenary level - too big or too small',
  'mrc_msg_error_wrong_period' => 'Unacceptable hire period',
  'mrc_msg_error_already_hired' => 'Mercenary already hired. Wait until hire period ends',
  'mrc_msg_error_no_resource' => 'Not enough Dark Matter to hire mercenary',
  'mrc_msg_error_requirements' => 'Requirements not met',

  'mrc_dismiss' => 'Dismiss',
  'mrc_dismiss_confirm' => 'When you dismissing mercenary you loose all DM that you spent for hiring this merc before! Are you sure that you want do dismiss mercenary?',
  'mrc_dismiss_before_hire' => 'To change level of recruited Mercenary you should before fire current one. You will lose all DM spent for current Mercenary.',

));

?>
