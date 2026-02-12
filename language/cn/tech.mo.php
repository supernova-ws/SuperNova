<?php

/*
#############################################################################
#  Filename: tech.mo
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
* @system [Chinese Simplified]
* @version 46d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'tech_storage_max' => '最大存储容量',
  'tech_storage' => '库存',
  'tech_storage_energy' => '消耗',
  'tech_storage_energy_max' => '产量',
  'tech_storage_energy_fullness' => '负载',
  'Tech' => '科技',
  'Requirements' => '需求',
  'Metal' => '金属',
  'Crystal' => '晶体',
  'Deuterium' => '重氢',
  'Energy' => '能量',
  'dark_matter' => '暗物质',
  'ds' => '消息',
  'Message' => '消息',
  'level' => '等级',
  'treeinfo' => '[i]',
  'comingsoon' => '即将推出',
  'te_dt_tx_pre' => '产量微弱',
  'tech_fullness' => '充满度',

  'type_mission' => array(
    MT_NONE => '-- 未知 --',
    MT_ATTACK => '攻击',
    MT_AKS => '联合攻击',
    MT_TRANSPORT => '运输',
    MT_RELOCATE => '部署',
    MT_HOLD => '驻守',
    MT_SPY => '间谍',
    MT_COLONIZE => '殖民',
    MT_RECYCLE => '回收',
    MT_DESTROY => '毁灭',
    MT_MISSILE => '导弹攻击',
    MT_EXPLORE => '探险',
  ),
  'fleet_events' => array(
    EVENT_FLEET_NONE   => '无事件',
    EVENT_FLEET_ARRIVE => '到达',
    EVENT_FLEET_STAY   => '驻守/探险',
    EVENT_FLEET_RETURN => '返回',
  ),

  'tech' => array(
    UNIT_STRUCTURES => '建筑',
    STRUC_MINE_METAL => '金属矿',
    STRUC_MINE_CRYSTAL => '晶体矿',
    STRUC_MINE_DEUTERIUM => '重氢合成器',
    STRUC_MINE_SOLAR => '太阳能电站',
    STRUC_MINE_FUSION => '聚变反应堆',
    STRUC_FACTORY_ROBOT => '机器人工厂',
    STRUC_FACTORY_NANO => '纳米工厂',
    STRUC_FACTORY_HANGAR => '船厂',
    STRUC_STORE_METAL => '金属仓库',
    STRUC_STORE_CRYSTAL => '晶体仓库',
    STRUC_STORE_DEUTERIUM => '重氢储罐',
    STRUC_LABORATORY => '研究实验室',
    STRUC_TERRAFORMER => '地形改造器',
    STRUC_ALLY_DEPOSIT => '联盟仓库',
    STRUC_LABORATORY_NANO => '特殊建筑',

    UNIT_STRUCTURES_SPECIAL => '月球建筑',
    STRUC_MOON_STATION => '月球基地',
    STRUC_MOON_PHALANX => '感应阵列',
    STRUC_MOON_GATE => '跳跃门',
    STRUC_SILO => '导弹井',

    UNIT_TECHNOLOGIES => '科技',
    TECH_ENERGY => '能源技术',
    TECH_COMPUTER => '计算机技术',
    TECH_SPY => '间谍技术',
    TECH_ARMOR => '装甲技术',
    TECH_WEAPON => '武器技术',
    TECH_SHIELD => '护盾技术',
    TECH_ENGINE_CHEMICAL => '燃烧引擎',
    TECH_ENGINE_ION => '脉冲引擎',
    TECH_ENGINE_HYPER => '超空间引擎',
    TECH_LASER => '激光技术',
    TECH_ION => '离子技术',
    TECH_PLASMA => '等离子技术',
    TECH_HYPERSPACE => '超空间技术',
    TECH_EXPEDITION => '探险技术',
    TECH_COLONIZATION => '殖民技术',
    TECH_ASTROTECH => '天体物理学',
    TECH_GRAVITON => '引力技术',
    TECH_RESEARCH => '跨星系研究网络',

    UNIT_SHIPS               => '飞船',
    SHIP_SATTELITE_SOLAR     => '太阳能卫星',
    SHIP_SPY                 => '间谍探测器',
    SHIP_CARGO_SMALL         => '小型运输机',
    SHIP_CARGO_BIG           => '大型运输机',
    SHIP_CARGO_SUPER         => '超级运输机',
    SHIP_CARGO_HYPER         => '超空间运输机',
    SHIP_RECYCLER            => '回收船',
    SHIP_COLONIZER           => '殖民船',
    SHIP_SMALL_FIGHTER_LIGHT => '轻型战斗机',
    SHIP_SMALL_FIGHTER_HEAVY => '重型战斗机',
    SHIP_MEDIUM_DESTROYER    => '驱逐舰',
    SHIP_LARGE_CRUISER       => '巡洋舰',
    SHIP_LARGE_BOMBER        => '轰炸机',
    SHIP_LARGE_BATTLESHIP    => '战列舰',
    SHIP_LARGE_DESTRUCTOR    => '毁灭者',
    SHIP_HUGE_DEATH_STAR     => '死星',
    SHIP_HUGE_SUPERNOVA      => '&quot;超新星&quot; 级巡洋舰',

    UNIT_DEFENCE                 => '防御',
    UNIT_DEF_TURRET_MISSILE      => '火箭发射器',
    UNIT_DEF_TURRET_LASER_SMALL  => '轻型激光炮',
    UNIT_DEF_TURRET_LASER_BIG    => '重型激光炮',
    UNIT_DEF_TURRET_GAUSS        => '高斯炮',
    UNIT_DEF_TURRET_ION          => '离子炮',
    UNIT_DEF_TURRET_PLASMA       => '等离子炮',
    UNIT_DEF_SHIELD_SMALL        => '小型防护罩',
    UNIT_DEF_SHIELD_BIG          => '大型防护罩',
    UNIT_DEF_SHIELD_PLANET       => '行星护盾',
    UNIT_DEF_MISSILE_INTERCEPTOR => '拦截导弹',
    UNIT_DEF_MISSILE_INTERPLANET => '星际导弹',

    UNIT_MERCENARIES => '雇佣兵',
    MRC_STOCKMAN => '货物大师',
    MRC_SPY => '间谍',
    MRC_ACADEMIC => '院士',
//    MRC_DESTRUCTOR => 'Destroyer',
    MRC_ADMIRAL => '海军上将',
    MRC_COORDINATOR => '协调员',
    MRC_NAVIGATOR => '领航员',
//    MRC_ASSASIN => 'Assassin',

    UNIT_GOVERNORS => '总督',
    MRC_TECHNOLOGIST => '技术专家',
    MRC_ENGINEER => '工程师',
    MRC_FORTIFIER => '防御专家',

    UNIT_RESOURCES => '资源',
    RES_METAL => '金属',
    RES_CRYSTAL => '晶体',
    RES_DEUTERIUM => '重氢',
    RES_ENERGY => '能量',
    RES_DARK_MATTER => '暗物质',

    UNIT_ARTIFACTS => '神器',
    ART_LHC => '大型强子对撞机',
    ART_HOOK_SMALL => '小型钩子',
    ART_HOOK_MEDIUM => '中型钩子',
    ART_HOOK_LARGE => '大型钩子',
    ART_RCD_SMALL => '小型 RCD',
    ART_RCD_MEDIUM => '中型 RCD',
    ART_RCD_LARGE => '大型 RCD',
    ART_HEURISTIC_CHIP => '启发式芯片',
    ART_NANO_BUILDER   => '纳米建造者',

    UNIT_PLANS => '图纸',
    UNIT_PLAN_STRUC_MINE_FUSION => '图纸 &quot;热核电站&quot;',
    UNIT_PLAN_SHIP_CARGO_SUPER => '图纸 &quot;超级运输机&quot;',
    UNIT_PLAN_SHIP_CARGO_HYPER => '图纸 &quot;超空间运输机&quot;',
    UNIT_PLAN_SHIP_DEATH_STAR => '图纸 &quot;死星&quot;',
    UNIT_PLAN_SHIP_SUPERNOVA => '图纸 &quot;超新星级巡洋舰&quot;',
    UNIT_PLAN_DEF_SHIELD_PLANET => '图纸 &quot;行星防御&quot;',

    UNIT_PREMIUM => '高级',
    UNIT_CAPTAIN => '舰长',

    UNIT_PLANET_DENSITY => '密度',

    UNIT_CAN_NOT_BE_BUILD => '玩家无法建造该单位',
  ),

  'tech_short' => array(
    UNIT_SHIPS               => '飞船',
    SHIP_SATTELITE_SOLAR     => '太阳能卫星',
    SHIP_SPY                 => '探测器',
    SHIP_CARGO_SMALL         => '小运',
    SHIP_CARGO_BIG           => '大运',
    SHIP_CARGO_SUPER         => '超运',
    SHIP_CARGO_HYPER         => '超空间运',
    SHIP_RECYCLER            => '回收',
    SHIP_COLONIZER           => '殖民',
    SHIP_SMALL_FIGHTER_LIGHT => '轻歼',
    SHIP_SMALL_FIGHTER_HEAVY => '重歼',
    SHIP_MEDIUM_DESTROYER    => '驱逐',
    SHIP_LARGE_CRUISER       => '巡洋',
    SHIP_LARGE_BOMBER        => '轰炸',
    SHIP_LARGE_BATTLESHIP    => '战列',
    SHIP_LARGE_DESTRUCTOR    => '毁灭',
    SHIP_HUGE_DEATH_STAR     => '死星',
    SHIP_HUGE_SUPERNOVA      => '超新星',

    UNIT_RESOURCES => '资源',
    RES_METAL => '金',
    RES_CRYSTAL => '晶',
    RES_DEUTERIUM => '氢',
    RES_ENERGY => '能',
    RES_DARK_MATTER => '暗',
    RES_METAMATTER => '元',

    UNIT_CAN_NOT_BE_BUILD => '不可建造',
  ),

));
