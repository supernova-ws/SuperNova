<?php

/*
#############################################################################
#  Filename: system.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#  Copyright В© 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright В© 2005 - 2008 KGsystem
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

use Fleet\Constants;

if (!defined('INSIDE')) {
	die('Hack attempt!');
}

global $config;

$a_lang_array = (array(
  'sys_administration' => '超级新星管理',
  'sys_birthday' => '生日',
  'sys_birthday_message' => '%1$s! 超级新星管理团队热烈祝贺你的生日 %2$s 并送给你一个小礼物 - %3$d %4$s!',

  'adm_err_denied' => '访问被拒绝。你没有足够的权限使用此管理页面',

  'sys_empire'          => '帝国',
  'VacationMode'			=> "由于你处于假期模式，生产已停止",
  'sys_moon_destruction_report' => "月球毁灭报告",
  'sys_moon_destroyed' => "你的死星发射了一道强大的引力波，摧毁了月球！",
  'sys_rips_destroyed' => "你的死星发射了一道强大的引力波，但由于月球体积过大，能量不足以将其摧毁。引力波从月球表面反射回来，摧毁了你的舰队。",
  'sys_rips_come_back' => "你的死星没有足够的能量摧毁这颗月球。你的舰队未能摧毁月球。",
  'sys_chance_moon_destroy' => "月球毁灭几率：",
  'sys_chance_rips_destroy' => "舰队毁灭几率：",

  'sys_impersonate' => '扮演',
  'sys_impersonate_done' => '停止扮演',
  'sys_impersonated_as' => '警告！目前你正在扮演玩家 %1$s。别忘了你实际上是 %2$s！要停止扮演，请选择相应的菜单项。',

  'menu_admin_mining'          => '采矿统计',
  'menu_admin_units'          => '单位',
  'menu_admin_ube_balance'          => 'UBE 平衡',

  'sys_day' => "天",
  'sys_hrs' => "小时",
  'sys_min' => "分钟",
  'sys_sec' => "秒",
  'sys_day_short' => "天",
  'sys_hrs_short' => "时",
  'sys_min_short' => "分",
  'sys_sec_short' => "秒",

  'sys_ask_admin' => '问题和建议发送至',

  'sys_wait' => '正在执行查询。请稍候。',

  'sys_fleets'       => '舰队',
  'sys_expeditions'  => '探险',
  'sys_fleet'        => '舰队',
  'sys_expedition'   => '探险',
  'sys_event_next'   => '下一个事件：',
  'sys_event_arrive' => '将到达',
  'sys_event_stay'   => '将结束任务',
  'sys_event_return' => '将返回',

  'sys_total'           => "总计",
  'sys_need'				=> '需要',
  'sys_register_date'   => '注册日期',

  'sys_attacker' 		=> "攻击者",
  'sys_defender' 		=> "防御者",

  'COE_combatSimulator' => "战斗模拟器",
  'COE_simulate'        => "运行模拟器",
  'COE_fleet'           => "舰队",
  'COE_defense'         => "防御",
  'sys_coe_combat_start'=> "战斗开始",
  'sys_coe_combat_end'  => "战斗结果",
  'sys_coe_round'       => "回合",

  'sys_coe_attacker_turn'=> '攻击者射击造成 %1$s 伤害。防御者护盾吸收了 %2$s<br />',
  'sys_coe_defender_turn'=> '防御者射击造成 %1$s 伤害。攻击者护盾吸收了 %2$s<br /><br /><br />',
  'sys_coe_outcome_win'  => '防御者赢得战斗！<br />',
  'sys_coe_outcome_loss' => '攻击者赢得战斗！<br />',
  'sys_coe_outcome_loot' => '他掠夺了 %1$s 金属，%2$s 晶体，%3$s 重氢<br />',
  'sys_coe_outcome_draw' => '战斗以平局结束...<br />',
  'sys_coe_attacker_lost'=> '攻击者损失了 %1$s 单位<br />',
  'sys_coe_defender_lost'=> '防御者损失了 %1$s 单位<br />',
  'sys_coe_debris_left'  => '星球周围的废墟中有 %1$s 金属和 %2$s 晶体。<br /><br />',
  'sys_coe_moon_chance'  => '月球形成几率为 %1$s%%<br />',
  'sys_coe_rw_time'      => '报告生成耗时 %1$s 秒<br />',

  'sys_resources'       => "资源",
  'sys_ships'           => "飞船",
  'sys_metal'          => "金属",
  'sys_metal_sh'       => "金",
  'sys_crystal'        => "晶体",
  'sys_crystal_sh'     => "晶",
  'sys_deuterium'      => "重氢",
  'sys_deuterium_sh'   => "氢",
  'sys_energy'         => "能量",
  'sys_energy_sh'      => "能",
  'sys_dark_matter'    => "暗物质",
  'sys_dark_matter_sh' => "暗",

  'sys_reset'           => "重置",
  'sys_send'            => "发送",
  'sys_characters'      => "字符",
  'sys_back'            => "返回",
  'sys_return'          => "返回",
  'sys_delete'          => "删除",
  'sys_writeMessage'    => "写消息",
  'sys_hint'            => "提示",

  'sys_alliance'        => "联盟",
  'sys_player'          => "玩家",
  'sys_coordinates'     => "坐标",

  'sys_online'          => "在线",
  'sys_offline'         => "离线",
  'sys_status'          => "状态",

  'sys_universe'        => "宇宙",
  'sys_goto'            => "前往",

  'sys_time'            => "时间",
  'sys_temperature'     => '温度',

  'sys_no_task'         => "无任务",

  'sys_affilates'       => "邀请的玩家",

  'sys_fleet_arrived'   => "舰队到达",

  'sys_planet_type' => array(
    PT_PLANET => '行星',
    PT_DEBRIS => '废墟',
    PT_MOON   => '月球',
  ),

  'sys_planet_type_sh' => array(
    PT_PLANET => '(行)',
    PT_DEBRIS => '(废)',
    PT_MOON   => '(月)',
  ),

  'sys_planet_expedition' => '未探索空间',

  'sys_capacity' 			=> '负载能力',
  'sys_cargo_bays' 			=> '货舱',

  'sys_supernova' 			=> '超级新星',
  'sys_server' 			=> '服务器',

  'sys_unbanned'			=> '已解禁',

  'sys_date_time'			=> '日期和时间',
  'sys_from_person'	   => '来自',
  'sys_from_speed'	   => '来自',

  'sys_from'		  => '来自',

// Resource page
  'res_planet_production' => '行星产量',
  'res_basic_starting_resources' => '行星初始资源',
  'res_basic_income' => '基本收入',
  'res_basic_storage_size' => '行星存储容量',
  'res_total' => '总计',
  'res_calculate' => '计算',
  'res_hourly' => '每小时',
  'res_daily' => '每天',
  'res_weekly' => '每周',
  'res_monthly' => '每月',
  'res_storage_fill' => '存储占用',
  'res_hint' => '<ul><li>资源产量 <100% 意味着能量短缺。建造更多电站或减少资源生产<li>如果你的产量是 0%，可能是你刚从假期模式回来，你需要启动所有工厂<li>要使所有工厂立即开采，请使用资源表中的下拉菜单。特别是在假期模式之后使用很方便</ul>',

// Build page
  'bld_destroy' => '拆除',
  'bld_create'  => '建造',
  'bld_research' => '研究',
  'bld_hire' => '雇佣',

// Imperium page
  'imp_imperator' => "皇帝",
  'imp_overview' => "帝国概览",
  'imp_fleets' => "飞行中的舰队",
  'imp_production' => "生产",
  'imp_name' => "名称",
  'imp_research' => "研究",
  'imp_exploration' => "探索",
  'imp_imperator_none' => "这个宇宙中没有这样的皇帝！",
  'sys_fields' => "领域",

// Cookies
  'err_cookie' => "错误！无法通过 cookie 中的信息验证用户。<br />清除浏览器 cookie，然后重新 <a href='login" . DOT_PHP_EX . "'>登录</a> 游戏或 <a href='reg" . DOT_PHP_EX . "'>注册新账号</a>。",

// Supported languages
  'ru'              	  => '俄语',
  'en'              	  => '英语',
  'cn'                    => '简体中文',

  'sys_vacation'        => '你正在休假直到',
  'sys_vacation_leave'  => '我已经休息好了 - 结束假期！',
  'sys_vacation_in'     => '休假中',
  'sys_level'           => '等级',
  'sys_level_short'     => '级',
  'sys_level_max'       => '最高等级',

  'sys_yes'             => '是',
  'sys_no'              => '否',

  'sys_on'              => '启用',
  'sys_off'             => '禁用',

  'sys_confirm'         => '确认',
  'sys_save'            => '保存',
  'sys_create'          => '创建',
  'sys_write_message'   => '写消息',

// top bar
  'top_of_year' => '年',
  'top_online'			=> '在线玩家',

  'sys_first_round_crash_1'	=> '与受影响舰队失去联系。',
  'sys_first_round_crash_2'	=> '这意味着它在战斗的第一回合就被摧毁了。',

  'sys_ques' => array(
    QUE_STRUCTURES => '建筑',
    QUE_HANGAR     => '船厂',
    SUBQUE_DEFENSE => '防御',
    QUE_RESEARCH   => '研究',
  ),

  'navbar_button_expeditions_short' => '探险',
  'navbar_button_fleets' => '舰队',
  'navbar_button_quests' => '任务',
  'navbar_font' => '字体',
  'navbar_font_normal' => '正常',
  'sys_que_structures' => '建筑',
  'sys_que_hangar' => '机库',
  'sys_que_defense' => '防御',
  'sys_que_research' => '研究',
  'sys_que_research_short' => '科学',

  'eco_que'          => '队列',
  'eco_que_empty'    => '队列为空',
  'eco_que_clear'    => '清空队列',
  'eco_que_trim'     => '撤销上一个队列',
  'eco_que_artifact' => '使用神器',

  'sys_cancel' => '取消',

  'sys_overview'			=> '概览',
  'mod_marchand'			=> '商人',
  'sys_galaxy'			=> '星系',
  'sys_system'			=> '太阳系',
  'sys_planet'			=> '行星',
  'sys_planet_title'			=> '行星类型',
  'sys_planet_title_short'			=> '类型',
  'sys_moon'			=> '月球',
  'sys_error'			=> '错误',
  'sys_done'				=> '完成',
  'sys_no_vars'			=> '变量初始化，请参阅管理！',
  'sys_attacker_lostunits'		=> '攻击者损失 %s 单位。',
  'sys_defender_lostunits'		=> '防御者损失 %s 单位。',
  'sys_gcdrunits' 			=> '现在在这些坐标有 %s %s 和 %s %s。',
  'sys_moonproba' 			=> '月球几率为： %d %% ',
  'sys_moonbuilt' 			=> '由于巨大的能量，巨大的金属块和晶体结合在一起形成了新的月球 %s %s！',
  'sys_attack_title'    		=> '%s。以下舰队之间发生了战斗：',
  'sys_attack_attacker_pos'      	=> '攻击者 %s [%s:%s:%s]',
  'sys_attack_techologies' 	=> '武器：%d %% 护盾：%d %% 装甲：%d %% ',
  'sys_attack_defender_pos' 	=> '防御者 %s [%s:%s:%s]',
  'sys_ship_type' 			=> '类型',
  'sys_ship_count' 		=> '数量',
  'sys_ship_weapon' 		=> '武器',
  'sys_ship_shield' 		=> '护盾',
  'sys_ship_armour' 		=> '装甲',
  'sys_ship_speed' 		=> '速度',
  'sys_ship_consumption' 		=> '消耗',
  'sys_ship_capacity' 		=> '容量/油箱',
  'sys_destroyed' 			=> '已摧毁',
  'sys_attack_attack_wave' 	=> '攻击者正以 %s 的总火力射击防御者。护盾吸收了 %s 的射击。',
  'sys_attack_defend_wave'		=> '防御者正以 %s 的总火力射击攻击者。护盾吸收了 %s 的射击。',
  'sys_attacker_won' 		=> '攻击者赢得了战斗！',
  'sys_defender_won' 		=> '防御者赢得了战斗！',
  'sys_both_won' 			=> '战斗以平局结束！',
  'sys_stealed_ressources' 	=> '攻击者获得了 %s 金属，%s 晶体和 %s 重氢。',
  'sys_rapport_build_time' 	=> '报告生成时间 %s 秒',
  'sys_mess_tower' 		=> '运输',
  'sys_coe_lost_contact' 		=> '你与舰队失去了联系',
  'sys_spy_activity' => '你的星球周围有一些间谍活动',
  'sys_spy_materials' 		=> '原材料',
  'sys_spy_fleet' 			=> '舰队',
  'sys_spy_defenses' 		=> '防御',
  'sys_mess_qg' 			=> '舰队司令部',
  'sys_mess_spy_report' 		=> '间谍报告',
  'sys_mess_spy_lostproba' 	=> '间谍探测器收到信息的准确性 %d %% ',
  'sys_mess_spy_detect_chance' 	=> '探测几率 %d%%',
  'sys_mess_spy_detect_chance_no_percent' 	=> '探测几率',
  'sys_mess_spy_control' 		=> '反情报',
  'sys_mess_spy_activity' 		=> '间谍活动',
  'sys_mess_spy_enemy_fleet' 	=> '外星舰队',
  'sys_mess_spy_seen_at'		=> '在行星附近被发现',
  'sys_mess_spy_destroyed'		=> '间谍舰队被摧毁',
  'sys_mess_spy_destroyed_enemy'		=> '敌方间谍舰队被摧毁',
  'sys_object_arrival'		=> '到达行星',
  'sys_stay_mess_stay' => '离开舰队',
  'sys_stay_mess_start' 		=> '你的舰队到达了行星',
  'sys_stay_mess_back'		=> '你的舰队回来了 ',
  'sys_stay_mess_end'		=> ' 并交付：',
  'sys_stay_mess_bend'		=> ' 并交付以下资源：',
  'sys_address_planet' 		=> '[%s:%s:%s]',
  'sys_stay_mess_goods' 		=> '%s : %s, %s : %s, %s : %s',
  'sys_colo_mess_from' 		=> '殖民',
  'sys_colo_mess_report' 		=> '殖民报告',
  'sys_colo_default_name' 		=> '殖民地',
  'sys_colo_arrival' 		=> '舰队到达坐标 ',
  'sys_colo_max_colo' 		=> '，但你无法殖民，因为你的殖民地数量已达到等级上限',
  'sys_colo_all_is_ok' 		=> '，殖民者开始在新的星球定居。',
  'sys_colo_bad_pos'  			=> '，殖民者发现环境不适合你的帝国。殖民任务返回。',
  'sys_colo_not_free' 			=> '，殖民者在这些坐标没有发现行星。他们不得不灰心丧气地返航。',
  'sys_colo_no_colonizer'     => '舰队中没有殖民船',
  'sys_colo_planet'  		=> ' 行星被殖民！',
  'sys_expe_report' 		=> '探险报告',
  'sys_recycler_report' 		=> '回收船信息',
  'sys_expe_blackholl_1' 		=> '你的舰队撞上了黑洞，你损失了一部分舰队！',
  'sys_expe_blackholl_2' 		=> '你的舰队撞上了黑洞，你的舰队被完全吸入！',
  'sys_expe_nothing_1' 		=> '你的研究人员目睹了超新星爆发！你的引擎能够吸收一部分能量。',
  'sys_expe_nothing_2' 		=> '你的研究人员什么也没发现！',
  'sys_expe_found_goods' 		=> '你的研究人员发现了一个富含原材料的星球！<br>你获得了 %s %s, %s %s 和 %s %s',
  'sys_expe_found_ships' 		=> '你的研究人员发现了一支完好无损的新舰队！<br>你获得了： ',
  'sys_expe_back_home' 		=> '你的舰队回来了。',
  'sys_mess_transport' 		=> '运输',
  // 'sys_tran_mess_owner' 		=> 'One of your fleet reaches the planet %s %s and delivers %s %s, %s  %s and %s %s.',
  'sys_tran_mess_user'  		=> '你发送到行星 %s %s 的舰队到达了 %s %s 并交付了 %s %s, %s %s 和 %s %s。',
  'sys_relocate_mess_user'  		=> '此外，以下单位被重新部署：<br />',
  'sys_mess_fleetback' 		=> '返回',
  'sys_tran_mess_back' 		=> '你的一支舰队返回了行星 %s %s。',
  'sys_recycler_gotten' 		=> '你的一支舰队，回收了 %s %s 和 %s %s 返回行星。',
  'sys_notenough_money' 		=> '你没有足够的资源建造：%s。你现在有：%s %s , %s %s 和 %s %s。建造需要：%s %s , %s %s 和 %s %s。',
  'sys_nomore_level'		=> '你不能再升级它了。它已达到最高等级 ( %s )。',
  'sys_buildlist' 			=> '建筑列表',
  'sys_buildlist_fail' 		=> '无建筑',
  'sys_gain' 			=> '开采： ',
  'sys_debris' 			=> '废墟： ',
  'sys_noaccess' 			=> '访问被拒绝',
  'sys_noalloaw' 			=> '你已获得此区域的访问权限！',
  'sys_governor'        => '总督',

  'flt_error_duration_wrong' => '无法发送舰队 - 没有可用的延迟间隔。研究更多等级的天体物理学',
  'flt_stay_duration' => '时间',

  'flt_mission_expedition' => array(
    'msg_sender' => '探险报告',
    'msg_title' => '探险报告',

    'found_dark_matter' => '获得 %1$d 单位暗物质',
    'found_resources' => "发现资源：\r\n",
    'found_fleet' => "发现飞船：\r\n",
    'lost_fleet' => "损失以下飞船：\r\n",

    'outcomes' => array(
      Constants::OUTCOME_NONE => array(
        'messages' => array(
          '你的研究人员什么也没发现',
        ),
      ),

      Constants::EXPEDITION_OUTCOME_LOST_FLEET => array(
        'messages' => array(
          '舰队进入黑洞并部分丢失',
        ),
      ),

      Constants::EXPEDITION_OUTCOME_LOST_FLEET_ALL => array(
        'messages' => array(
          '如果你能看到这个！它太美了... 它在召唤我... (与舰队失去联系)',
          '舰队报告 %1$s。我们完成了该扇区的探索。船员不高兴 嘿，你在舰桥上做什么？！(与舰队失去联系)',
          '舰队报告 %1$s。一切平静 (静电干扰) (与舰队失去联系)',
          '啊啊啊！那是什么？！它从哪里来 (与舰队失去联系)',
          '发现未知物体。它不响应标准协议请求。发送探测器进行研究 (与舰队失去联系)',
        ),
      ),

      Constants::EXPEDITION_OUTCOME_FOUND_FLEET => [
        'no_result' => '不幸的是，所有舰队计算机的总功率甚至不足以控制最小的飞船。尝试发送更多飞船和/或更大的飞船',
        'messages' => [
          0 => [
            '你发现了一支全新的舰队',
          ],
          1 => [
            '你发现了一支舰队',
          ],
          2 => [
            '你发现了一支二手舰队',
          ],
        ],
      ],

      Constants::EXPEDITION_OUTCOME_FOUND_RESOURCES => array(
        'no_result' => '你的舰队货舱无法容纳哪怕一个资源集装箱。尝试发送更多运输船',
        'messages' => array(
          0 => array(
            '你发现了一个海盗的资源藏匿处。为了收集这么多货物，摧毁了多少飞船？',
          ),
          1 => array(
            '你发现了一个废弃的小行星基地。我想知道它的居民去哪儿了？探索废墟后，你发现了一些完好的仓库',
          ),
          2 => array(
            '你遇到了一个被摧毁的运输车队。搜索破碎飞船的货舱，你发现了一些资源',
          ),
        ),
      ),

      Constants::EXPEDITION_OUTCOME_FOUND_DM => array(
        'no_result' => '不幸的是，所有的舰队蓄能器都不足以收集哪怕一点点暗物质。尝试发送更大的舰队',
        'messages' => '你的舰队目睹了超新星的诞生',
      ),

    ),
  ),

  // News page & a bit of imperator page
  'news_fresh'      => '新鲜事',
  'news_all'        => '所有新闻',
  'news_title'      => '新闻',
  'news_none'       => '无新闻',
  'news_new'        => '新',
  'news_future'     => '公告',
  'news_more'       => '阅读更多...',
  'news_hint'       => '要关闭新鲜事列表 - 点击标题 "[ 新闻 ]" 阅读所有新闻',

  'news_date'       => '日期',
  'news_announce'   => '目录',
  'news_detail_url' => '更多信息链接',
  'news_mass_mail'  => '发送新闻给所有玩家',

  'news_total'      => '总新闻： ',

  'news_add'        => '提交新闻',
  'news_edit'       => '编辑新闻',
  'news_copy'       => '复制新闻',
  'news_mode_new'   => '新',
  'news_mode_edit'  => '编辑中',
  'news_mode_copy'  => '复制中',

  // Shortcuts
  'shortcut_title'     => '快捷方式',
  'shortcut_none'      => '无快捷方式',
  'shortcut_new'       => '新',
  'shortcut_text'      => '文本',

  'shortcut_add'       => '添加快捷方式',
  'shortcut_edit'      => '编辑快捷方式',
  'shortcut_copy'      => '复制快捷方式',
  'shortcut_mode_new'  => '新',
  'shortcut_mode_edit' => '编辑中',
  'shortcut_mode_copy' => '复制中',

  // Misc
  'sys_game_rules' => '游戏规则',
  'sys_game_documentation' => '文档',
  'sys_max' => '最大',
  'sys_banned_msg' => '你已被封禁。有关更多信息，请访问 <a href="banned.php">这里</a>。账号封禁时间： ',
  'sys_total_time' => '总时间',
  'sys_total_time_short' => '队列时间',

  // Univers
  'uni_moon_of_planet' => '行星',

  // Combat reports
  'cr_view_title'  => "查看战斗报告",
  'cr_view_button' => "查看报告",
  'cr_view_prompt' => "输入代码",
  'cr_view_my'     => "我的战斗记录",
  'cr_view_hint'   => "此页面允许你查看共享的战斗报告。所有战斗报告底部都有一个代码。要共享战斗报告，只需将该代码提供给他们。然后他们可以在此处输入并查看你的战斗报告。",

  // Fleet
  'flt_gather_all'    => '收集资源',

  // Ban system
  'ban_title'      => '黑名单',
  'ban_name'       => '名称',
  'ban_reason'     => '封禁原因',
  'ban_from'       => '封禁日期',
  'ban_to'         => '封禁期限',
  'ban_by'         => '执行者',
  'ban_no'         => '没有被封禁的玩家',
  'ban_thereare'   => '总计',
  'ban_players'    => '被封禁',
  'ban_banned'     => '被封禁玩家： ',

  // Contacts
  'ctc_title' => '管理团队',
  'ctc_intro' => '在这里你可以找到所有游戏管理员和运营人员的联系方式以进行反馈',
  'ctc_name'  => '名称',
  'ctc_rank'  => '级别',
  'ctc_mail'  => '电子邮件',

  // Records page
  'rec_title'  => '宇宙记录',
  'rec_build'  => '建筑',
  'rec_specb'  => '特殊建筑',
  'rec_playe'  => '玩家',
  'rec_defes'  => '防御',
  'rec_fleet'  => '舰队',
  'rec_techn'  => '科技',
  'rec_level'  => '等级',
  'rec_nbre'   => '数量',
  'rec_rien'   => '-',

  // Credits page
  'cred_link'    => '互联网',
  'cred_site'    => '站点',
  'cred_forum'   => '论坛',
  'cred_credit'  => '作者',
  'cred_creat'   => '总监',
  'cred_prog'    => '程序员',
  'cred_master'  => '版主',
  'cred_design'  => '设计师',
  'cred_web'     => '站长',
  'cred_thx'     => '致谢',
  'cred_based'   => '建立 XNova 的基础',
  'cred_start'   => 'XNova 首次亮相的地方',

  // Built-in chat
  'chat_common'  => '公共聊天',
  'chat_ally'    => '联盟聊天',
  'chat_history' => '历史',
  'chat_message' => '消息',
  'chat_send'    => '发送',
  'chat_page'    => '页',
  'chat_timeout' => '由于你不活动，聊天已禁用。刷新页面。',

  // ----------------------------------------------------------------------------------------------------------
  // Interface of Jump Gate
  'gate_start_moon' => '起始月球',
  'gate_dest_moon'  => '目的地月球',
  'gate_use_gate'   => '使用跳跃门',
  'gate_ship_sel'   => '选择飞船',
  'gate_ship_dispo' => '照片',
  'gate_jump_btn'   => '跳跃！！',
  'gate_jump_done'  => '跳跃门正在重新充能！<br>跳跃门将在以下时间后准备就绪： ',
  'gate_wait_dest'  => '目的地跳跃门正在准备中！跳跃门将在以下时间后准备就绪： ',
  'gate_no_dest_g'  => '最终目的地没有打开跳跃门来移动舰队',
  'gate_no_src_ga'  => '当前月球上没有跳跃门',
  'gate_wait_star'  => '跳跃门正在重新充能！<br>跳跃门将在以下时间后准备就绪： ',
  'gate_wait_data'  => '错误，没有数据进行跳跃！',
  'gate_vacation'   => '错误，你无法跳跃，因为你处于假期模式！',
  'gate_ready'      => '跳跃门准备跳跃',

  // quests
  'qst_quests'               => '任务',
  'qst_msg_complete_subject' => '你完成了任务！',
  'qst_msg_complete_body'    => '你完成了任务 "%s"。',
  'qst_msg_your_reward'      => '你的奖励： ',

  // Messages
  'msg_from_admin' => '宇宙管理',
  'msg_class' => array(
    MSG_TYPE_OUTBOX => '已发送消息',
    MSG_TYPE_SPY => '间谍报告',
    MSG_TYPE_PLAYER => '玩家消息',
    MSG_TYPE_ALLIANCE => '联盟通讯',
    MSG_TYPE_COMBAT => '军事报告',
    MSG_TYPE_RECYCLE => '回收处理',
    MSG_TYPE_TRANSPORT => '舰队到达',
    MSG_TYPE_ADMIN => '管理消息',
    MSG_TYPE_EXPLORE => '探险报告',
    MSG_TYPE_QUE => '建筑队列消息',
    MSG_TYPE_NEW => '所有消息',
  ),

  'msg_que_research_from'    => '科学家',
  'msg_que_research_subject' => '科学发现',
  'msg_que_research_message' => '新科技 "%s" 等级 %d 已被发现',

  'msg_que_planet_from'    => '总督',

  'msg_que_hangar_subject' => '机库建造完成',
  'msg_que_hangar_message' => "机库 %s 完成了它的工作",

  'msg_que_built_subject'   => '行星建筑工作完成',
  'msg_que_built_message'   => "%1\$s 上的 '%2\$s' 建造完成。建造等级：%3\$d",
  'msg_que_destroy_message' => "%1\$s 上的 '%2\$s' 拆除完成。拆除等级：%3\$d",

  'msg_personal_messages' => '个人消息',

  'sys_id' => 'ID',
  'sys_identifier' => '标识符',

  'sys_email'   => '电子邮件',
  'sys_ip' => 'IP',

  'sys_maximum' => '最大',
  'sys_maximum_level' => '最高等级',

  'sys_user_name' => '用户名',
  'sys_player_name' => '玩家名',
  'sys_user_name_short' => '名称',

  'sys_planets' => '行星',
  'sys_moons' => '月球',

  'sys_quantity' => '数量',
  'sys_quantity_maximum' => '最大数量',
  'sys_qty' => '数量',
  'sys_quantity_total' => '总计',

  'sys_buy_for' => '购买花费',
  'sys_buy' => '购买',

  'sys_eco_lack_dark_matter' => '暗物质不足',

  'time_local' => '玩家时间',
  'time_server' => '服务器时间',

  'topnav_imp_attack' => '你的帝国受到攻击！',

  'sys_result' => array(
    'error_dark_matter_not_enough' => '没有足够的暗物质来完成操作',
    'error_dark_matter_change' => '更改暗物质数量时出错！请重试。如果错误持续存在 - 请联系服务器管理',
  ),

  // Arrays
  'sys_build_result' => array(
    BUILD_ALLOWED => '可以建造',
    BUILD_REQUIRE_NOT_MEET => '未满足要求',
    BUILD_AMOUNT_WRONG => '太多',
    BUILD_QUE_WRONG => '队列不存在',
    BUILD_QUE_UNIT_WRONG => '错误队列',
    BUILD_INDESTRUCTABLE => '无法摧毁',
    BUILD_NO_RESOURCES => '资源不足',
    BUILD_NO_UNITS => '无单位',
    BUILD_UNIT_BUSY => array(
      0 => '忙碌',
      STRUC_LABORATORY => '研究进行中',
      STRUC_LABORATORY_NANO => '研究进行中',
    ),
    BUILD_QUE_FULL => '队列已满',
    BUILD_SILO_FULL => '导弹井已满',
    BUILD_MAX_REACHED => '你已经建造和/或排队了该类型单位的最大数量',
    BUILD_SECTORS_NONE => '无空闲扇区',
    BUILD_AUTOCONVERT_AVAILABLE => '自动转换可用',
    BUILD_HIGHSPOT_NOT_ACTIVE => '节日热点未激活',
  ),

  'sys_game_mode' => array(
    GAME_SUPERNOVA => 'SuperNova',
    GAME_OGAME     => 'oGame',
    GAME_BLITZ     => 'Blitz',
  ),

  'months' => array(
    '01'=>'一月',
    '02'=>'二月',
    '03'=>'三月',
    '04'=>'四月',
    '05'=>'五月',
    '06'=>'六月',
    '07'=>'七月',
    '08'=>'八月',
    '09'=>'九月',
    '10'=>'十月',
    '11'=>'十一月',
    '12'=>'十二月'
  ),

  'weekdays' => array(
    0 => '星期日',
    1 => '星期一',
    2 => '星期二',
    3 => '星期三',
    4 => '星期四',
    5 => '星期五',
    6 => '星期六'
  ),

  'user_level' => array(
    0 => '玩家',
    1 => '版主',
    2 => '操作员',
    3 => '管理员',
    4 => '开发者',
  ),

  'user_level_shortcut' => array(
    0 => '玩',
    1 => '版',
    2 => '操',
    3 => '管',
    4 => '开',
  ),

  'sys_lessThen15min'   => '&lt; 15 分钟',

  'sys_no_points'        => '你没有足够的暗物质！',
  'sys_dark_matter_obtain_header' => '如何获得暗物质',
  'sys_dark_matter_desc' => '暗物质 - 使用标准方法制造，占宇宙质量的 23%。从那里你可以获得难以置信的能量。因此，由于与其提取相关的复杂性，暗物质的价值非常高。',
  'sys_dark_matter_hint' => '借助这种物质，你可以雇佣军官和指挥官。',

  'sys_dark_matter_what_header' => '什么是 <span class="dark_matter">暗物质</span>',
  'sys_dark_matter_description_header' => '为什么你需要 <span class="dark_matter">暗物质</span>',
  'sys_dark_matter_description_text' => '<span class="dark_matter">暗物质</span> 是游戏内货币，在游戏中你可以进行各种操作：
    <ul>
      <li>购买 <a href="index.php?page=premium"><span class="link">高级账户</span></a></li>
      <li>为帝国招募 <a href="officer.php?mode=600"><span class="link">雇佣兵</span></a></li>
      <li>雇佣总督并购买额外扇区 <a href="overview.php?mode=manage"><span class="link">用于行星</span></a></li>
      <li>购买 <a href="officer.php?mode=1100"><span class="link">图纸</span></a></li>
      <li>购买 <a href="artifacts.php"><span class="link">神器</span></a></li>
      <li>使用 <a href="market.php"><span class="link">黑市</span></a>：交换资源；出售飞船；购买飞船；购买情报等</li>
      <li>...还有很多其他事情</li>
    </ul>',
  'sys_dark_matter_obtain_text' => '你在游戏过程中获得 <span class="dark_matter">暗物质</span>：在袭击敌方行星获得等级、研究科技、建造和摧毁建筑时。
    有时探险也能获得一些 <span class="dark_matter">暗物质</span>。',

  'sys_dark_matter_obtain_text_convert' => '<br />如果你缺少 <span class="dark_matter">暗物质</span> - 购买 <span class="metamatter">元物质</span>。如果你的 <span class="dark_matter">暗物质</span> 不足，将使用所需数量的 <span class="metamatter">元物质</span> 代替 <span class="dark_matter">暗物质</span>',

  'sys_msg_err_update_dm' => '更新暗物质数量出错！',

  'sys_na' => '不可用',
  'sys_na_short' => 'N/A',

  'sys_ali_res_title' => '联盟资源',

  'sys_bonus' => '加成',

  'sys_of_ally' => '联盟的',

  'sys_hint_player_name' => '你可以通过 ID 或名称搜索玩家。如果玩家名称包含奇怪的符号或仅由数字组成 - 你应该使用玩家 ID 进行搜索。',
  'sys_hint_ally_name' => '你可以通过 ID、标签或名称搜索联盟。如果联盟的标签或名称包含奇怪的符号或仅由数字组成 - 你应该使用联盟 ID 进行搜索。',

  'sys_fleet_and' => '+ 舰队',

  'sys_on_planet' => '在行星上',
  'fl_on_stores' => '库存',

  'sys_ali_bonus_members' => '联盟加成的最小联盟规模 ',

  'sys_premium' => '高级账户',

  'mrc_period_list' => array(
    PERIOD_MINUTE    => '1 分钟',
    PERIOD_MINUTE_3  => '3 分钟',
    PERIOD_MINUTE_5  => '5 分钟',
    PERIOD_MINUTE_10 => '10 分钟',
    PERIOD_DAY       => '1 天',
    PERIOD_DAY_3     => '3 天',
    PERIOD_WEEK      => '1 周',
    PERIOD_WEEK_2    => '2 周',
    PERIOD_MONTH     => '30 天',
    PERIOD_MONTH_2   => '60 天',
    PERIOD_MONTH_3   => '90 天',
  ),

  'sys_sector_buy' => '购买 1 个扇区',

  'sys_select_confirm' => '确认选择',

  'sys_capital' => '首都',

  'sys_result_operation' => '结果',

  'sys_password' => '密码',
  'sys_password_length' => '密码长度',
  'sys_password_seed' => '使用的字符',

  'sys_msg_ube_report_err_not_found' => '未找到战斗报告 - 检查密码密钥。战斗报告可能因过期而被删除',

  'sys_mess_attack_report' 	=> '战斗报告',
  'sys_perte_attaquant' 		=> '攻击者损失',
  'sys_perte_defenseur' 		=> '防御者损失',

  'ube_report_info_page_header' => '战斗报告',
  'ube_report_info_page_header_cypher' => '访问代码',
  'ube_report_info_main' => '主要战斗信息',
  'ube_report_info_date' => '日期和时间',
  'ube_report_info_location' => '位置',
  'ube_report_info_rounds_number' => '回合数',
  'ube_report_info_outcome' => '战斗结果',
  'ube_report_info_outcome_win' => '攻击者胜利',
  'ube_report_info_outcome_loss' => '攻击者失败',
  'ube_report_info_outcome_draw' => '平局',
  'ube_report_info_link' => '战斗报告链接',
  'ube_report_info_bbcode' => '用于聊天的 BBCode',
  'ube_report_info_sfr' => '战斗在第一回合由攻击者失败结束<br />可能是 SFR',
  'ube_report_info_debris' => '轨道上的废墟',
  'ube_report_info_debris_simulator' => '(不计算月球)',
  'ube_report_info_loot' => '掠夺',
  'ube_report_info_loss' => '战斗损失',
  'ube_report_info_generate' => '页面生成时间',

  'ube_report_moon_was' => '该行星已有月球',
  'ube_report_moon_chance' => '月球几率',
  'ube_report_moon_created' => '行星轨道上出现了直径为 %s 的新月球',

  'ube_report_moon_reapers_none' => '所有带有引力引擎的飞船在战斗中被摧毁',
  'ube_report_moon_reapers_wave' => '攻击者的飞船产生了聚焦引力波',
  'ube_report_moon_reapers_chance' => '月球毁灭几率',
  'ube_report_moon_reapers_success' => '月球已毁灭',
  'ube_report_moon_reapers_failure' => '引力波能量不足以摧毁月球',

  'ube_report_moon_reapers_outcome' => '引力引擎自毁几率',
  'ube_report_moon_reapers_survive' => '引力引擎成功同步并补偿了引力反冲',
  'ube_report_moon_reapers_died' => '引力引擎自毁并摧毁了你所有的舰队',

  'ube_report_side_attacker' => '攻击者',
  'ube_report_side_defender' => '防御者',

  'ube_report_round' => '回合',
  'ube_report_unit' => '单位',
  'ube_report_attack' => '攻击',
  'ube_report_shields' => '护盾',
  'ube_report_shields_passed' => '穿透',
  'ube_report_armor' => '装甲',
  'ube_report_damage' => '伤害',
  'ube_report_loss' => '损失',

  'ube_report_info_restored' => '防御单位恢复',
  'ube_report_info_loss_final' => '总单位损失',
  'ube_report_info_loss_resources' => '资源损失',
  'ube_report_info_loss_dropped' => '由于货舱容量减少导致的资源损失',
  'ube_report_info_loot_lost' => '从行星掠夺的资源',
  'ube_report_info_loss_gained' => '由于行星掠夺导致的资源损失',
  'ube_report_info_loss_in_metal' => '以金属计算的总资源损失',

  'ube_report_msg_body_common' => '在 %s 轨道 %s [%d:%d:%d] %s 发生战斗<br />%s<br /><br />',
  'ube_report_msg_body_debris' => '行星轨道上出现废墟：<br />',
  'ube_report_msg_body_sfr' => '你与舰队失去了联系',

  'ube_report_capture' => '行星占领',
  'ube_report_capture_result' => array(
    UBE_CAPTURE_DISABLED => '行星占领已禁用',
    UBE_CAPTURE_NON_PLANET => '只能占领行星',
    UBE_CAPTURE_NOT_A_WIN_IN_1_ROUND => '要占领行星，战斗必须在第一回合以胜利结束',
    UBE_CAPTURE_TOO_MUCH_FLEETS => '占领行星时，战斗中只能有占领舰队和行星舰队参与',
    UBE_CAPTURE_NO_ATTACKER_USER_ID => '内部错误 - 没有攻击者 ID！请报告给开发者！',
    UBE_CAPTURE_NO_DEFENDER_USER_ID => '内部错误 - 没有防御者 ID！请报告给开发者！',
    UBE_CAPTURE_CAPITAL => '无法占领首都',
    UBE_CAPTURE_TOO_LOW_POINTS => '只能占领总积分至少是攻击者 2 倍的玩家的行星',
    UBE_CAPTURE_NOT_ENOUGH_SLOTS => '没有更多的行星占领槽位',
    UBE_CAPTURE_SUCCESSFUL => '行星被攻击者玩家占领',
  ),

  'sys_kilometers_short' => 'km',

  'ube_simulation' => '模拟',

  'sys_hire_do' => '雇佣',

  'sys_captains' => '舰长',

  'sys_fleet_composition' => '舰队组成',

  'sys_continue' => '继续',

  'uni_planet_density_types' => array(
    PLANET_DENSITY_NONE => '从未发生',
    PLANET_DENSITY_ICE_HYDROGEN => '氢冰',
    PLANET_DENSITY_ICE_METHANE => '甲烷冰',
    PLANET_DENSITY_ICE_WATER => '水冰',
    PLANET_DENSITY_CRYSTAL_RAW => '晶体',
    PLANET_DENSITY_CRYSTAL_SILICATE => '硅酸盐',
    PLANET_DENSITY_CRYSTAL_STONE => '石头',
    PLANET_DENSITY_STANDARD => '标准',
    PLANET_DENSITY_METAL_ORE => '矿石',
    PLANET_DENSITY_METAL_PERIDOT => '橄榄石',
    PLANET_DENSITY_METAL_RAW => '金属',
  ),

  'sys_planet_density' => '密度',
  'sys_planet_density_units' => 'kg/m&sup3;',
  'sys_planet_density_core' => '核心类型',

  'sys_change' => '更改',
  'sys_show' => '显示',
  'sys_hide' => '隐藏',
  'sys_close' => '关闭',
  'sys_unlimited' => '无限',

  'ov_core_type_current' => '当前核心类型',
  'ov_core_change_to' => '更改为',
  'ov_core_err_none' => '行星核心类型成功从 "%s" 更改为 "%s"。<br />新平均行星密度 %d kg/m3',
  'ov_core_err_not_a_planet' => '只能更改行星核心类型',
  'ov_core_err_denisty_type_wrong' => '错误的核心类型',
  'ov_core_err_same_density' => '新核心类型与当前类型没有区别 - 无需更改',
  'ov_core_err_no_dark_matter' => '没有足够的暗物质来更改核心类型',

  'sys_color'  => "颜色",
  'topnav_imp_attack' => '你的帝国受到攻击',
  'topnav_user_rank' => '你在统计中的当前位置',
  'topnav_users' => '注册玩家数量',
  'topnav_users_online' => '当前在线',

  'topnav_refresh_page' => '刷新页面',

  'sys_colonies' => '殖民地',
  'sys_radio' => '"太空" 广播',

  'sys_auth_provider_list' => array(
    ACCOUNT_PROVIDER_NONE => 'USERS 表',
    ACCOUNT_PROVIDER_LOCAL => 'ACCOUNT 表',
    ACCOUNT_PROVIDER_CENTRAL => '中央 ACCOUNT 表',
  ),

  'sys_login_messages' => array(
    LOGIN_UNDEFINED => '登录过程未开始',
    LOGIN_SUCCESS => '登录成功',
    LOGIN_ERROR_USERNAME_EMPTY => '玩家名不能为空',
    LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS => '玩家名和登录名中不允许使用的字符 ',
    LOGIN_ERROR_USERNAME => '没有该名称的玩家',
    LOGIN_ERROR_USERNAME_ALLY_OR_BOT => '此名称属于联盟或机器人。不能使用它登录... 至少目前不能',
    LOGIN_ERROR_PASSWORD_EMPTY => '密码不能为空',
    LOGIN_ERROR_PASSWORD_TRIMMED => '密码不能以空格、制表符或换行符开头或结尾',
    LOGIN_ERROR_PASSWORD => '密码错误',

    REGISTER_SUCCESS => '注册成功完成',
    REGISTER_ERROR_BLITZ_MODE => 'Blitz 服务器模式下新玩家注册已禁用',
    REGISTER_ERROR_USERNAME_WRONG => '错误的玩家名',
    REGISTER_ERROR_ACCOUNT_NAME_EXISTS => '账户名已注册',
    REGISTER_ERROR_PASSWORD_INSECURE => '不安全或错误的密码。密码长度至少应为 ' . PASSWORD_LENGTH_MIN . ' 个字符，且不能以空格开头或结尾',
    REGISTER_ERROR_USERNAME_SHORT => '名字太短。名字必须至少包含 ' . LOGIN_LENGTH_MIN. ' 个字符',
    REGISTER_ERROR_PASSWORD_DIFFERENT => '密码与确认密码不匹配',
    REGISTER_ERROR_EMAIL_EMPTY => '电子邮件不能为空',
    REGISTER_ERROR_EMAIL_WRONG => '输入的电子邮件不是有效的电子邮件地址',
    REGISTER_ERROR_EMAIL_EXISTS => '此电子邮件已注册。如果你已经注册，请尝试密码重置选项。否则请使用其他电子邮件地址',

    PASSWORD_RESTORE_ERROR_EMAIL_NOT_EXISTS => '没有使用该基础电子邮件的玩家',
    PASSWORD_RESTORE_ERROR_TOO_OFTEN => '你每 10 分钟只能请求一次密码恢复代码。请检查你的垃圾邮件文件夹中的恢复代码，或通过你的主电子邮件（你用于注册的电子邮件）联系服务器管理人员 <span class="ok">' . $config->server_email . '</span>',
    PASSWORD_RESTORE_ERROR_SENDING => '发送带有恢复代码的电子邮件时出错。请通过电子邮件 <span class="ok">' . $config->server_email . '</span> 联系服务器管理人员',
    PASSWORD_RESTORE_SUCCESS_CODE_SENT => '恢复代码已成功发送',

    PASSWORD_RESTORE_ERROR_CODE_EMPTY => '恢复代码不能为空',
    PASSWORD_RESTORE_ERROR_CODE_WRONG => '恢复代码错误',
    PASSWORD_RESTORE_ERROR_CODE_TOO_OLD => '恢复代码太旧。获取新的',
    PASSWORD_RESTORE_ERROR_CODE_OK_BUT_NO_ACCOUNT_FOR_EMAIL => '恢复代码正确，但未找到该电子邮件的任何账户。它可能已被删除或发生了内部错误。请联系服务器管理人员',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT => '带有新密码的电子邮件已成功发送到你的邮箱',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR => '发送新密码时出错。获取新的恢复代码并重试',


    REGISTER_ERROR_PLAYER_NAME_TRIMMED => '玩家名不能以“空字符”（如“空格”、“制表符”、“换行符”等）开头或结尾',
    REGISTER_ERROR_PLAYER_NAME_EMPTY => '玩家名不能为空',
    REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS => '玩家名包含禁止的字符',
    REGISTER_ERROR_PLAYER_NAME_SHORT => '玩家名至少应有 ' . LOGIN_LENGTH_MIN . ' 个字符',
    REGISTER_ERROR_PLAYER_NAME_EXISTS => '此玩家名已被他人拥有。请选择其他名称',


    // 内部错误
    AUTH_ERROR_INTERNAL_PASSWORD_CHANGE_ON_RESTORE => '密码更改错误。请联系服务器管理人员',
    PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT => '禁止恢复服务器团队成员的密码。请直接联系管理员',
    REGISTER_ERROR_ACCOUNT_CREATE => '创建账户出错！请向管理人员报告此错误！',
    LOGIN_ERROR_SYSTEM_ACCOUNT_TRANSLATION => '系统错误 - 提供商翻译表失败！请通知服务器管理人员！',
    PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS => '账户未找到！请联系服务器管理人员！',
    AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION => '内部错误！确认码无账户更改密码。请向宇宙管理人员报告！',
    LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET => '内部错误！cookie_set() 无账户！请向宇宙管理人员报告！',
  ),

  'log_reg_email_title' => "你在超级新星游戏服务器 %1\$s 的注册",
  'log_reg_email_text' => "账户 %3\$s 的注册确认\r\n\r\n
  这封信包含超级新星游戏服务器 %1\$s 的注册数据
  请将此数据保存在安全的地方\r\n\r\n
  服务器地址：%2\$s\r\n
  你的登录名：%3\$s\r\n
  你的密码：%4\$s\r\n\r\n
  感谢你在我们的服务器上注册！祝你游戏愉快！\r\n
  服务器管理 %1\$s %2\$s\r\n\r\n
  由开源引擎 'Project SuperNova.WS' 提供支持。点亮你的超级新星 http://supernova.ws/",

  'log_lost_email_title' => 'Supernova，宇宙 %s：密码重置',
  'log_lost_email_code' => "有人（可能是你）请求重置 SuperNova 宇宙 %4\$s 的密码。如果你没有请求重置密码 - 请忽略此电子邮件。\r\n\r\n要重置密码，请转到地址 \r\n%1\$s?password_reset_confirm=1&password_reset_code=%2\$s#tab_password_reset\r\n或在页面 %1\$s#tab_password_reset 上输入确认码 \"%2\$s\"（不带双引号！）。此代码有效期至 %3\$s。密码重置后，你需要请求新的确认码",
  'log_lost_email_pass' => "你更改了 SuperNova 宇宙 %1\$s 的密码。\r\n\r\n你的登录名：\r\n%2\$s\r\n\r\n你的新密码：\r\n%3\$s\r\n\r\n记住它！\r\n\r\n你可以通过以下链接进入游戏 " . SN_ROOT_VIRTUAL . "login.php 并使用提供的登录名和密码",

  'sys_password_reset_message_body' => "你访问此宇宙的密码已重置。\r\n\r\n你的新密码是：\r\n\r\n%1\$s\r\n\r\n记住你的密码！\r\n\r\n你可以随时在 '设置' 菜单中将其更改为你喜欢的密码",

  'sys_login_password_show' => '显示密码',
  'sys_login_password_hide' => '隐藏密码',
  'sys_password_repeat' => '确认密码',

  'sys_game_disable_reason' => array(
    GAME_DISABLE_NONE => '游戏已启用',
    GAME_DISABLE_REASON => '游戏已禁用。玩家将看到原因',
    GAME_DISABLE_UPDATE => '游戏正在更新',
    GAME_DISABLE_STAT => '统计更新进行中',
    GAME_DISABLE_INSTALL => '游戏尚未配置',
    GAME_DISABLE_EVENT_BLACK_MOON => '黑月升起！',
  ),

  'sys_sector_purchase_log' => '用户 {%2$d} {%1$s} 在行星 {%5$d} {%3$s} 类型 "%4$s" 上购买了 1 个扇区，花费 %6$d 暗物质',

  'sys_notes' => '笔记',
  'sys_notes_priorities' => array(
    0 => '低优先级',
    1 => '低于正常',
    2 => '正常',
    3 => '重要',
    4 => '非常重要',
  ),

  'sys_milliseconds' => '毫秒',

  'sys_gender' => '性别',
  'sys_gender_list' => array(
    GENDER_UNKNOWN => '长大后决定',
    GENDER_MALE => '男',
    GENDER_FEMALE => '女',
  ),

  'imp_stat_header' => '变化图表',
  'imp_stat_types' => array(
    'TOTAL_RANK' => '总排名',
    'TOTAL_POINTS' => '总积分',
    // 'TOTAL_COUNT' => 'Total resources count',
    'TECH_RANK' => '研究排名',
    'TECH_POINTS' => '研究积分',
    // 'TECH_COUNT' => 'Research count',
    'BUILD_RANK' => '建筑排名',
    'BUILD_POINTS' => '建筑积分',
    // 'BUILD_COUNT' => '',
    'DEFS_RANK' => '防御排名',
    'DEFS_POINTS' => '防御积分',
    //'DEFS_COUNT' => '',
    'FLEET_RANK' => '舰队排名',
    'FLEET_POINTS' => '舰队积分',
    //'FLEET_COUNT' => '',
    'RES_RANK' => '资源排名',
    'RES_POINTS' => '资源积分',
    //'RES_COUNT' => '',
  ),

  'sys_date' => '日期',

  'sys_blitz_global_button' => 'Blitz 服务器',
  'sys_blitz_page_disabled' => 'Blitz 模式下此页面不可用',
  'sys_blitz_registration_disabled' => 'Blitz 服务器注册已禁用',
  'sys_blitz_registration_no_users' => '没有注册玩家',
  'sys_blitz_registration_player_register' => '注册游戏',
  'sys_blitz_registration_player_register_un' => '取消注册',
  'sys_blitz_registration_closed' => '注册已关闭。请稍后再试',
  'sys_blitz_registration_player_generate' => '生成登录名和密码',
  'sys_blitz_registration_player_import_generated' => '导入生成的字符串',
  'sys_blitz_registration_player_name' => '你的 Blitz 登录名：',
  'sys_blitz_registration_player_password' => '你的 Blitz 密码：',
  'sys_blitz_registration_server_link' => 'Blitz 服务器链接',
  'sys_blitz_registration_player_blitz_name' => 'Blitz 上的名称',
  'sys_blitz_registration_price' => '申请费用',
  'sys_blitz_registration_mode_list' => array(
    BLITZ_REGISTER_DISABLED => '注册已禁用',
    BLITZ_REGISTER_OPEN => '注册已开启',
    BLITZ_REGISTER_CLOSED => '注册已关闭',
    BLITZ_REGISTER_SHOW_LOGIN => '显示登录名和密码',
    BLITZ_REGISTER_DISCLOSURE_NAMES => '结果摘要',
  ),

  'survey' => '调查',
  'survey_questions' => '选择选项',
  'survey_questions_hint' => '每行 1 个选项',
  'survey_questions_hint_edit' => '编辑调查将重置结果',
  'survey_until' => '调查持续时间（默认 1 天）',

  'survey_votes_total_none' => '还没有人投票... 成为第一个投票的人！',
  'survey_votes_total_voted' => '已投票：',
  'survey_votes_total_voted_join' => '投票 - 否则你会输！',
  'survey_votes_total_voted_has_answer' => '你已经投过票了。和你一起投票的人',

  'survey_lasts_until' => '调查持续至',

  'survey_select_one' => '选择一个答案并点击',
  'survey_confirm' => '投票！',
  'survey_result_sent' => '你的投票已计算。刷新页面或使用链接 <a class="link" href="announce.php">新闻</a> 查看当前调查结果',
  'survey_complete' => '调查完成',

  'player_option_fleet_ship_sort' => array(
    PLAYER_OPTION_SORT_DEFAULT => '标准',
    PLAYER_OPTION_SORT_NAME => '按名称',
    PLAYER_OPTION_SORT_SPEED => '按飞船速度',
    PLAYER_OPTION_SORT_COUNT => '按飞船数量',
    PLAYER_OPTION_SORT_ID => '按 ID',
  ),

  'player_option_building_sort' => array(
    PLAYER_OPTION_SORT_DEFAULT => '标准',
    PLAYER_OPTION_SORT_NAME => '按名称',
    PLAYER_OPTION_SORT_ID => '按 ID',
    PLAYER_OPTION_SORT_CREATE_TIME_LENGTH => '按建造时间',
  ),

  'sys_sort' => '排序',
  'sys_sort_inverse' => '反向排序',

  'sys_blitz_reward_log_message' => 'Blitz 服务器 %1$d 奖金位 Blitz 名称 "%2$s"',
  'sys_blitz_registration_view_stat' => '查看 Blitz 服务器统计',

  'sys_login_register_message_title' => "你的游戏登录名和密码",
  'sys_login_register_message_body' => "你的游戏登录名\r\n%1\$s\r\n\r\n你的游戏密码\r\n%2\$s\r\n\r\n写下来或记住这些数据！",

  'auth_provider_list' => array(
    ACCOUNT_PROVIDER_NONE => 'users 表',
    ACCOUNT_PROVIDER_LOCAL => 'account 表',
    ACCOUNT_PROVIDER_CENTRAL => '中央存储',
  ),

  'bld_autoconvert' => '在行星 %3$s 上创建单位 {%1$d} "%4$s" 数量 %2$d 时的自动转换',

  'news_show_rest' => '显示新闻文本',

  'wiki_requrements' => '需要',
  'wiki_grants' => '授予',

  'que_slot_length' => '槽位',

  'sys_confirm_action_title' => '确认你的操作',
  'sys_confirm_action' => '你确定要这样做吗？',

  'sys_system_speed_original' => '原始速度',
  'sys_system_speed_for_action' => '动作/事件期间的速度',

  'menu_info_best_battles' => '最佳战斗',

  'sys_cost' => '成本',
  'sys_price' => '价格',

  'sys_governor_none' => '未雇佣总督',
  'sys_governor_hire' => '雇佣总督',
  'sys_governor_upgrade_or_change' => '升级或更换总督',

  'tutorial_prev' => '<< 下一个',
  'tutorial_next' => '上一个 >>',
  'tutorial_finish' => '完成',
  'tutorial_window' => '在窗口中打开',
  'tutorial_window_off' => '返回页面',

  'tutorial_error_load' => "加载教程出错 - 请重试。如果错误持续存在 - 请联系游戏管理",
  'tutorial_error_next' => "错误 - 教程中没有下一页 - 请联系游戏管理",
  'tutorial_error_prev' => "错误 - 教程中没有上一页 - 请联系游戏管理",

  'sys_click_here_to_continue' => '点击这里继续',

  'sys_module_error_not_found' => '奖励模块 "%1$s" 未找到或已禁用！',

  'rank_page_title' => '军衔列表',
  'rank' => '军衔',
  'ranks' => [
    0  => '学员',
    1  => '新兵',
    2  => '列兵',
    3  => '下士',
    4  => '中士',
    5  => '军士长',
    6  => '军士长',
    7  => '见习官',
    8  => '准尉',
    9  => '少尉',
    10 => '中尉',
    11 => '上尉',
    12 => '少校',
    13 => '中校',
    14 => '上校',
    15 => '少将',
    16 => '中将',
    17 => '上将',
    18 => '海军上将',
    19 => '元帅',
    20 => '大元帅',
  ],

));
