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
* @version 38a3.1
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$lang = array_merge($lang, array(
  'qst_quest' => 'Квест',
  'qst_quest_of' => 'квеста',
  'qst_name' => 'Название',
  'qst_description' => 'Описание',
  'qst_conditions' => 'Требования',
  'qst_rewards' => 'Награда',
  'qst_total' => 'Квестов',
  'qst_status' => 'Статус',
  'qst_status_list' => array(
    QUEST_STATUS_NOT_STARTED => 'Не&nbsp;выполнен',
    QUEST_STATUS_STARTED => 'Начат',
    QUEST_STATUS_COMPLETE => 'Выполнен',
  ),

  'qst_add' => 'Добавление квеста',
  'qst_edit' => 'Редактирование квеста',
  'qst_copy' => 'Копирование квеста',
  'qst_mode_add' => 'Добавление',
  'qst_mode_edit' => 'Редактирование',
  'qst_mode_copy' => 'Копирование',
  'qst_adm_err_unit_id' => 'Неправильный юнит',
  'qst_adm_err_unit_amount' => 'Неправильное количество юнитов',
  'qst_adm_err_reward_amount' => 'Неправильный размер награды',
  'qst_adm_err_reward_type' => 'Неправильный тип награды',
  'qst_adm_err_reward_empty' => 'Пустая награда квеста',
));

?>
