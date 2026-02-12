<?php

/*
#############################################################################
#  Filename: options.mo
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
* @version 45d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = [
  'opt_account' => '账号',
  'opt_int_options' => '界面',
  'opt_settings_statistics' => '玩家统计',
  'opt_settings_info' => '玩家信息',
  'opt_alerts' => '警报',
  'opt_common' => '常规',
  'opt_tutorial' => '教程',

  'opt_birthday' => '生日',

  'opt_header' => '用户选项',
  'opt_messages' => '自动警报',
  'opt_msg_saved' => '选项已成功保存',
  'opt_msg_name_changed' => '用户名已成功更改',
  'opt_msg_pass_changed' => '密码已成功更改',
  'opt_err_pass_wrong' => '旧密码错误。密码未更改',
  'opt_err_pass_unmatched' => '新密码确认与新密码不一致。密码未更改',

  'opt_msg_name_change_err_used_name' => '该用户名已被占用',
  'opt_msg_name_change_err_no_dm' => '暗物质不足，无法更改用户名',

  'username_old' => '当前名称',
  'username_new' => '新名称',
  'username_change_confirm' => '更改名称',
  'username_change_confirm_payed' => '花费',

  'changue_pass' => '更改密码',
  'Download' => '下载',
  'userdata' => '信息',
  'username' => '用户名',
  'lastpassword' => '旧密码',
  'newpassword' => '新密码<br>(最少 8 个字符)',
  'newpasswordagain' => '重复新密码',
  'emaildir' => '电子邮件地址',
  'emaildir_tip' => '此地址可以随时更改。如果 7 天内未被修改，该地址将成为主地址。',
  'permanentemaildir' => '主电子邮件地址',
  'opt_planet_sort_title' => '星球排序方式',
  'opt_planet_sort_options' => [
    SORT_ID       => '殖民时间',
    SORT_LOCATION => '坐标',
    SORT_NAME     => '名称',
    SORT_SIZE     => '大小',
  ],
  'opt_planet_sort_ascending' => [
    SORT_ASCENDING  => '升序',
    SORT_DESCENDING => '降序',
  ],

  'opt_navbar_title' => '导航面板',
  'opt_navbar_description' => '导航栏（或简称“navbar”）位于屏幕的最上方。此部分允许您自定义导航栏的外观。',
  'opt_navbar_resourcebar_description' => '资源栏 - 资源面板',
  'opt_navbar_buttons_title' => '设置导航栏按钮',
  'opt_player_options' => [
    PLAYER_OPTION_NAVBAR_PLANET_VERTICAL        => '垂直资源栏',
    PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE => '在资源栏中禁用存储容量显示',
    PLAYER_OPTION_NAVBAR_PLANET_OLD             => '使用旧版表格资源视图',

    PLAYER_OPTION_NAVBAR_RESEARCH_WIDE          => '宽版研究按钮（旧外观）',
    PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH       => '禁用研究按钮',
    PLAYER_OPTION_NAVBAR_DISABLE_PLANET         => '禁用星球按钮',
    PLAYER_OPTION_NAVBAR_DISABLE_HANGAR         => '禁用船坞按钮',
    PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE        => '禁用防御按钮',
    PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS    => '禁用探险按钮',
    PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS  => '禁用飞行舰队按钮',
    PLAYER_OPTION_NAVBAR_DISABLE_QUESTS         => '禁用任务按钮',
    PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER    => '禁用暗物质按钮',

    PLAYER_OPTION_UNIVERSE_OLD                  => '使用“宇宙概览”的旧视图',
    PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE     => '禁用殖民按钮',
    PLAYER_OPTION_DESIGN_DISABLE_BORDERS        => '禁用表格边框（如果有）',
    PLAYER_OPTION_TECH_TREE_TABLE               => '以表格形式查看科技树（旧视图）',
    PLAYER_OPTION_FLEET_SHIP_SELECT_OLD         => '使用旧版舰队选择视图',
    PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED         => '不显示舰船速度',
    PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY      => '不显示舰船容量',
    PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION   => '不显示舰船燃料消耗',
    PLAYER_OPTION_TUTORIAL_DISABLED             => '禁用教程',
    PLAYER_OPTION_TUTORIAL_WINDOWED             => '在弹出窗口中显示教程',
    PLAYER_OPTION_TUTORIAL_CURRENT              => '重置教程 - 教程将从头开始',

    PLAYER_OPTION_PLANET_SORT_INVERSE           => '反向排序',
    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE        => '隐藏自动转换按钮',

    PLAYER_OPTION_SOUND_ENABLED                 => '启用游戏音效',
    PLAYER_OPTION_ANIMATION_DISABLED            => '禁用动画效果',
    PLAYER_OPTION_PROGRESS_BARS_DISABLED        => '禁用进度条',
  ],

  'opt_chk_skin' => '使用皮肤',
  'opt_adm_title' => '管理选项',
  'opt_adm_planet_prot' => '星球保护',
  'thanksforregistry' => '感谢您的注册。<br />几分钟后您将收到包含密码的消息。',
  'general_settings' => '常规设置',
  'skins_example' => '皮肤',

  'opt_avatar' => '头像',
  'opt_avatar_remove' => '移除头像',
  'opt_avatar_search' => '在 Google 中搜索',
  'opt_upload' => '上传',

  'opt_msg_avatar_removed' => '头像已成功移除',
  'opt_msg_avatar_uploaded' => '头像已成功更改',
  'opt_msg_avatar_error_delete' => '删除头像文件时出错。请联系服务器管理员',
  'opt_msg_avatar_error_writing' => '保存头像文件时出错。请联系服务器管理员',
  'opt_msg_avatar_error_upload' => '加载头像图像 %1 时出错。请联系服务器管理员',
  'opt_msg_avatar_error_unsupported' => '上传的图像格式不支持。仅支持 JPG、GIF、PNG，最大 200KB',

  'untoggleip' => '禁用 IP 检查',
  'untoggleip_tip' => 'IP 检查意味着您将无法使用两个不同的 IP 登录同一个账号。这能为您提供安全优势！',
  'galaxyvision_options' => '宇宙',
  'spy_cant' => '探测器数量',
  'spy_cant_tip' => '当您关注某人时发送的探测器数量。',
  'tooltip_time' => '显示工具提示前的延迟',
  'mess_ammount_max' => '最大舰队通信数量',
  'seconds' => '秒',
  'shortcut' => '快速访问',
  'show' => '显示',
  'write_a_messege' => '写消息',
  'spy' => '间谍',
  'add_to_buddylist' => '添加为好友',
  'attack_with_missile' => '导弹攻击',
  'show_report' => '查看报告',
  'delete_vacations' => '账户管理',
  'mode_vacations' => '开启假期模式',
  'vacations_tip' => '假期模式用于在您离开时保护星球。',
  'deleteaccount' => '删除账户',
  'deleteaccount_tip' => '账户将在 45 天无登录后被删除。',
  'deleteaccount_on' => '如果没有活动，此配置文件将在以下日期被删除',
  'save_settings' => '保存更改',
  'exit_vacations' => '退出假期',
  'Vaccation_mode' => '假期模式已启用。它将持续直到： ',
  'You_cant_exit_vmode' => '在时间到期前您无法退出假期',
  'Error' => '错误',
  'cans_resource' => '停止星球上的资源开采',
  'cans_reseach' => '停止星球上的研究',
  'cans_build' => '停止星球上的建设',
  'cans_fleet_build' => '停止舰船和防御的建造',
  'cans_fly_fleet2' => '外星舰队接近中……您可以进入假期模式',
  'vacations_exit' => '假期模式已禁用',
  'select_skin_path' => '选择',
  'opt_language' => '界面语言',
  'opt_compatibility' => '兼容性 - 旧界面',
  'opt_compat_structures' => '旧版建筑界面',
  'opt_vacation_err_your_fleet' => '至少有一个您的舰队在飞行中，无法进入假期',
  'opt_vacation_err_building' => '您正在 %s 上进行建造或研究，因此无法进入假期',
  'opt_vacation_err_research' => '您的科学家正在进行研究，因此无法进入假期',
  'opt_vacation_err_que' => '有研究正在进行和/或某些星球队列不为空，因此您无法进入假期。使用帝国概览查看正在发生的事情',
  'opt_vacation_err_timeout' => '假期冷却时间未到',
  'opt_vacation_next' => '下一次假期将在以下时间后可用',
  'opt_vacation_min' => '至少',
  'succeful_changepass' => '',

  'opt_time_diff_clear' => '测量玩家端时间与服务器时间的差异',
  'opt_time_diff_manual' => '手动设置时间差',
  'opt_time_diff_explain' => '当时间差设置正确时，导航栏中的“玩家时间”时钟应与玩家设备上的时钟秒秒对应<br />
  通常游戏会自动检测时间差。但是，当玩家设备上的时区错误，或玩家使用多个设备进行游戏，或互联网连接不佳时，您应手动设置时间差',

  'opt_custom' => [
    'opt_uni_avatar_user' => '显示用户头像',
    'opt_uni_avatar_ally' => '显示联盟 Logo',
    'opt_int_struc_vertical' => '垂直结构队列',
    'opt_int_navbar_resource_force' => '始终显示资源栏',
    'opt_int_overview_planet_columns' => '星球列表中的列数',
    'opt_int_overview_planet_columns_hint' => '0 - 根据最大行数计算',
    'opt_int_overview_planet_rows' => '星球列表中的最大行数',
    'opt_int_overview_planet_rows_hint' => '如果有列数则忽略',
  ],

  'opt_mail_optional_description' => '来自其他玩家的个人消息和关于内部游戏事件（如战斗报告、探险报告等）的通知将发送到此电子邮件',
  'opt_mail_permanent_description' => '您的游戏账户永久链接到此电子邮件。所有系统通知（如密码更改确认）将发送到此地址。此电子邮件只能输入一次',

  'opt_account_name' => '您的登录名<br />登录名用于进入游戏。通常这是您注册时输入的电子邮件',
  'opt_game_user_name' => '游戏内名称（昵称）<br />游戏中的其他玩家将看到您的昵称 - 而不是您的登录名',

  'opt_universe_title' => '宇宙',

  'option_fleets' => '舰队',
  'option_fleet_send' => '舰队发送',

  'option_change_nick_disabled' => '服务器设置禁止更改玩家昵称',

  'opt_ignores' => '忽略列表',
  'opt_unignore_do' => '从忽略列表中移除',
  'opt_ignore_list_empty' => '您的忽略列表为空',

];
