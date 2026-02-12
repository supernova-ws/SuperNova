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
* @system [Chinese]
* @version 43a16.13
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'art_use'             => '使用神器',

  'art_lhc_from'        => '大型强子对撞机',
  'art_lhc_subj'        => '创造月球...',
  'art_moon_create'   => array(
    ART_LHC => 'LHC 的引力波连接大块碎片，导致在坐标 %s 处产生新月球 %s！',
    ART_HOOK_SMALL => '小钩子在坐标 %2$s 处发射直径 %3$s 公里的新月球 %1$s！',
    ART_HOOK_MEDIUM => '中钩子在坐标 %2$s 处发射直径 %3$s 公里的新月球 %1$s！',
    ART_HOOK_LARGE => '大钩子在坐标 %2$s 处发射直径 %3$s 公里的新月球 %1$s！',
  ),
  'art_moon_exists' => '当前坐标的月球轨道上已有月球',
  'art_lhc_moon_fail'   => '不幸的是，LHC 的引力波不足以创造新月球',

  'art_rcd_from'        => '快速殖民地部署器',
  'art_rcd_subj'        => '殖民地已部署',
  'art_rcd_ok'          => '%1$s 在星球 %2$s 坐标 %3$s 处成功部署了殖民地',
  'art_rcd_err_moon'    => 'RCD 可以部署在星球上',
  'art_rcd_err_no_sense'=> 'RCD 检测到当前建筑不会有任何改进，因此中止了部署',
  'art_rcd_err_que'     => 'RCD 无法部署在正在进行建筑的星球上。取消所有建造任务并尝试再次部署 RCD',

  'art_heurestic_chip_ok' => '将 %s 等级 %d 的研究时间减少了 %s',
  'art_heurestic_chip_subj' => '研究加速',
  'art_heurestic_chip_no_research' => '当前没有正在进行的研究，或者当前研究剩余时间不到 1 分钟',

  'art_nano_builder_ok' => '星球 %s %s 上 "%s" 等级 %d 的时间减少了 %s',
  'art_nano_builder_build' => '建造',
  'art_nano_builder_destroy' => '销毁',
  'art_nano_builder_subj' => '建筑操作加速',
  'art_nano_builder_no_que' => '星球上没有执行建筑操作，或者当前建筑时间不到 1 分钟',

  'art_err_no_artifact' => '您没有此神器',

  'art_page_hint'       => '<ul>
    <li>神器是具有独特属性的稀有物品</li>
    <li>神器是消耗品，即使用后神器消失</li>
    <li>有些神器过于强大，在一个帝国中只能限量存在</li>
    <li>通常神器效果仅扩展到使用它的星球，但其中一些具有全帝国范围的效果。
    最稀有和最强大的神器将其效果扩展到整个太阳系、星系甚至宇宙！</li>
  </ul>',
));
