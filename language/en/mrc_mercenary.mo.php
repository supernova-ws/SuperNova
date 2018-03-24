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
* @system [English]
* @version 43a16.13
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

$a_lang_array = array(
  'mrc_up_to' => 'up to',
  'mrc_hire' => 'Hire',
  'mrc_hire_for' => 'Hire for',
  'mrc_allowed' => 'Allowed',
  'mrc_msg_error_wrong_mercenary' => 'Wrong Mercenary ID',
  'mrc_msg_error_wrong_level' => 'Wrong Mercenary level - too big or too small',
  'mrc_msg_error_wrong_period' => 'Unacceptable hire period',
  'mrc_msg_error_already_hired' => 'Mercenary already hired. Dismiss him or wait until hire period ends',
  'mrc_msg_error_no_resource' => 'Not enough Dark Matter to hire Mercenary',
  'mrc_msg_error_requirements' => 'Requirements not met',

  'mrc_dismiss' => 'Dismiss',
  'mrc_dismiss_confirm' => 'When you dismissing Mercenary you loose all DM that you spent for hiring this merc before! Are you sure that you want do dismiss Mercenary?',
  'mrc_dismiss_before_hire' => 'To change level of recruited Mercenary you should before fire current one. You will lose all DM spent for current Mercenary!',

  'mrc_mercenary_hired_log' => 'Hired Mercenary "%1$s" ID %2$d for %3$d DM for %4$d days',
  'mrc_mercenary_dismissed_log' => 'LOST %7$d hire days and %8$d DM (current prices)! Dismissed Mercenary "%1$s" ID %2$d, hired for %4$d days (from %5$s to %6$s) which costs now %3$d DM',
  'mrc_plan_bought_log' => 'Purchased Plan "%1$s" ID %2$d for %3$d DM',
);
