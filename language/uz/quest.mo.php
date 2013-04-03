<?php

/*
#############################################################################
#  Filename: quest.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#  Copyright © 2009 MSW
#############################################################################
*/

/**
*
* @package language
* @system [Russian]
* @version 37a5.8
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$lang = array_merge($lang, array(
  'qst_quest' => 'Kvest',
  'qst_quest_of' => 'Kvestlarning',
  'qst_name' => 'Nomlanishi',
  'qst_description' => 'Tavsifi',
  'qst_conditions' => 'Shartlari',
  'qst_rewards' => 'Mukofot',
  'qst_total' => 'Kvestlar soni',
  'qst_status' => 'Holati',
  'qst_status_list' => array(
    QUEST_STATUS_NOT_STARTED => '&nbsp;maslikni boshlash',
    QUEST_STATUS_STARTED => 'Boshlash',
    QUEST_STATUS_COMPLETE => 'Tugatish',
  ),

  'qst_add' => 'Kvestlarni qo`shish',
  'qst_edit' => 'Kvestlarni tahrirlash',
  'qst_copy' => 'Kvestlarni k`ochirish',
  'qst_mode_add' => 'Qo`shish',
  'qst_mode_edit' => 'Tahrirlash',
  'qst_mode_copy' => 'Ko`chirish',
  'qst_adm_err_unit_id' => 'Noto`g`ri bo`lim',
  'qst_adm_err_unit_amount' => 'Noto`g`ri bo`limlar soni',
  'qst_adm_err_reward_amount' => 'Noto`g`ri mukofotlar o`lchami',
  'qst_adm_err_reward_type' => 'Noto`g`ri mukofotlar turi',
  'qst_adm_err_reward_empty' => 'Mukofotsiz kvest',
));

?>
