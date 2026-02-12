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
* @system [Chinese]
* @version 43a16.13
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$a_lang_array = (array(
  'qst_quest' => '任务',
  'qst_quest_of' => '任务',
  'qst_name' => '名称',
  'qst_description' => '描述',
  'qst_adm_conditions' => '要求',
  'qst_conditions' => '需要建造/研究',
  'qst_rewards' => '奖励',
  'qst_total' => '任务',
  'qst_status' => '状态',
  'qst_status_list' => array(
    QUEST_STATUS_ALL => '-- 所有任务 --',
    QUEST_STATUS_NOT_STARTED => '未开始',
    QUEST_STATUS_STARTED => '进行中',
    QUEST_STATUS_EXCEPT_COMPLETE => '全部 - 除已完成',
    QUEST_STATUS_COMPLETE => '已完成',
  ),

  'qst_filter_by_status' => '按状态显示任务',

  'qst_add' => '添加任务',
  'qst_edit' => '编辑任务',
  'qst_copy' => '复制任务',
  'qst_mode_add' => '添加',
  'qst_mode_edit' => '编辑',
  'qst_mode_copy' => '复制',
  'qst_adm_err_unit_id' => '不支持的单位',
  'qst_adm_err_unit_amount' => '错误的单位数量',
  'qst_adm_err_reward_amount' => '错误的奖励数量',
  'qst_adm_err_reward_type' => '错误的奖励类型',
  'qst_adm_err_reward_empty' => '任务奖励为空',
));
