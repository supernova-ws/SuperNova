<?php

/*
#############################################################################
#  Filename: market.mo
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
* @system [Chinese]
* @version 43a16.13
* @condition clear
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$a_lang_array = (array(
  'eco_mrk_title' => '市场',
  'eco_mrk_description' => '非常奇怪，但您无法在文档中识别出此菜单项的任何提及。它从哪里来的？',
  'eco_mrk_service' => '服务',
  'eco_mrk_service_cost' => '服务成本',

  'eco_mrk_trader_do' => '交换资源',
  'eco_mrk_trader' => '交换资源',
  'eco_mrk_trader_cost' => '资源共享成本',
  'eco_mrk_trader_exchange' => '交换',
  'eco_mrk_trader_to' => '交换为',
  'eco_mrk_trader_course' => '汇率',
  'eco_mrk_trader_left' => '交换结果',
  'eco_mrk_trader_resources_all' => '所有资源',
  'eco_mrk_trader_exchange_dm_confirm' => '您确定要用 {0} 暗物质交换资源吗？',

  'eco_mrk_scraper_do' => '将船只报废为资源',
  'eco_mrk_scraper' => '将船只报废为资源',
  'eco_mrk_scraper_price' => '废料产出',
  'eco_mrk_scraper_perShip' => '来自船只',
  'eco_mrk_scraper_total' => '总计',
  'eco_mrk_scraper_cost' => '出售船只的废料成本',
  'eco_mrk_scraper_onOrbit' => '在轨道上',
  'eco_mrk_scraper_to' => '允许报废',
  'eco_mrk_scraper_res' => '以下废料：',
  'eco_mrk_scraper_ships' => '将以下船只报废：',
  'eco_mrk_scraper_noShip' => '轨道上有航天器',

  'eco_mrk_stockman_do' => '购买二手船只',
  'eco_mrk_stockman' => '购买二手船只',
  'eco_mrk_stockman_price' => '价格',
  'eco_mrk_stockman_perShip' => '船只',
  'eco_mrk_stockman_onStock' => '来自卖家',
  'eco_mrk_stockman_buy' => '购买船只',
  'eco_mrk_stockman_res' => '购买船只的成本：',
  'eco_mrk_stockman_ships' => '购买了以下船只：',
  'eco_mrk_stockman_noShip' => '卖家现在没有船只出售',

  'eco_mrk_exchange' => '资源交换',
  'eco_mrk_banker' => '银行家',
  'eco_mrk_pawnshop' => '当铺',

  'eco_mrk_info_do' => '购买信息',
  'eco_mrk_info' => '信息交易者',
  'eco_mrk_info_description' => '您在收件箱中发现了来自未知来源的信件。确切地说：',
  'eco_mrk_info_description_2' => '我可以访问大量感兴趣的信息。我可以与您分享...当然是为了奖励。按需只需花费您',
  'eco_mrk_info_buy' => '购买信息包',

  'eco_mrk_info_player' => '关于玩家的信息',
  'eco_mrk_info_player_description' => '我可以告诉您哪些雇佣兵目前在玩家的统治下工作',
  'eco_mrk_info_player_message' => '据我所知，玩家 ID %1$d [%2$s] 的雇佣兵列表目前如下：',

  'eco_mrk_info_not_hired' => '未雇佣',

  'eco_mrk_info_ally' => '关于联盟的信息',
  'eco_mrk_info_online' => '宇宙中的当前活动',

  'eco_mrk_info_msg_from' => '无法追踪的来源',

  'eco_mrk_error_title' => '市场 - 错误',
  'eco_mrk_errors' => array(
    MARKET_RESOURCES => '操作成功',
    MARKET_SCRAPPER => '资源交换成功发生',
    MARKET_NOT_A_SHIP => '不要试图出售除船只以外的任何东西！',
    MARKET_STOCKMAN => '缺少暗物质来完成操作',
    MARKET_NO_RESOURCES => '资源不足以完成操作',
    MARKET_PAWNSHOP => '您试图发送报废的船只数量超过了轨道上的数量',
    MARKET_NO_STOCK => '您试图购买的船只数量超过了卖家拥有的数量。这可能没有选择船只，其他人已经购买了它们',
    MARKET_ZERO_DEAL => '不要指定用于共享的资源数量',
    MARKET_NOTHING => '选择出售的船只',
    MARKET_ZERO_RES_STOCK => '选择购买的船只',
    MARKET_NEGATIVE_SHIPS => '不要试图出售负数数量的船只！',

    MARKET_NO_DM => '暗物质不足以完成操作',
    MARKET_INFO_WRONG => '我不卖这种信息',
    MARKET_INFO_PLAYER => '信息购买成功。检查您的个人邮件',
    MARKET_INFO_PLAYER_WRONG => '您应该指定玩家名称或 ID',
    MARKET_INFO_PLAYER_NOT_FOUND => '无法识别玩家。如果玩家名称由不可读符号或纯数字组成 - 尝试使用玩家 ID',
    MARKET_INFO_PLAYER_SAME => '您为什么要为关于自己的信息付费？',
  ),

));
