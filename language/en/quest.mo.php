<?php

/*
#############################################################################
#  Filename: quest.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
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
  'qst_quest' => 'Quest',
  'qst_quest_of' => 'quest',
  'qst_name' => 'Name',
  'qst_description' => 'Description',
  'qst_adm_conditions' => 'Requirements',
  'qst_conditions' => 'Need to build/research',
  'qst_rewards' => 'Reward',
  'qst_total' => 'Quests',
  'qst_status' => 'Status',
  'qst_status_list' => array(
    QUEST_STATUS_ALL => '-- All quests --',
    QUEST_STATUS_NOT_STARTED => 'Not&nbsp;started',
    QUEST_STATUS_STARTED => 'Started',
    QUEST_STATUS_EXCEPT_COMPLETE => 'All&nbsp;-&nbsp;except&nbsp;completed',
    QUEST_STATUS_COMPLETE => 'Completed',
  ),

  'qst_filter_by_status' => 'Show quest by status',

  'qst_add' => 'Add quest',
  'qst_edit' => 'Edit quest',
  'qst_copy' => 'Copy quest',
  'qst_mode_add' => 'Add',
  'qst_mode_edit' => 'Edit',
  'qst_mode_copy' => 'Copy',
  'qst_adm_err_unit_id' => 'Unsupported unit',
  'qst_adm_err_unit_amount' => 'Wrong unit amount',
  'qst_adm_err_reward_amount' => 'Wrong reward amount',
  'qst_adm_err_reward_type' => 'Wrong reward type',
  'qst_adm_err_reward_empty' => 'Quest reward empty',
));
