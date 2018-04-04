<?php

if (!defined('INSIDE')) {
  die('Hack attempt!');
}

lng_include('menu');

$active_payment_modules = SN::$gc->modules->countModulesInGroup('payment') > 0;

global $sn_version_check_class, $template_result, $user, $config, $lang;
global $sn_menu, $sn_menu_admin;


/*
  'menu_triolan' => array(                     // This should be used as ID for both internal submenu insert AND as "id" attribute of Tx HTML-tag (see below)
    'LEVEL'    => 'submenu',                   // Which Tx HTML tag to use. 'header' - would be used TH; 'submenu' - TD
    'TYPE'     => 'image',                     // Menu item type: 'image' (wrapped by IMG tag), 'text' (puts "as-is") or 'lang' for late biding with $lang[ITEM] values. Default is 'text'
    'CLASS'    => 'c_c',                       // Class for TD/TH element. Can be c_c, c_l, c_r or any other custom. 'c_c' default for 'header', 'c_l' default for 'text'
    'TITLE'    => 'Triolan.COM',               // TITLE tag for Tx HTML-element
    'ICON'     => 'menu_icon.png',             // Icon filename - would be searched in skinPath/icons/. If 'true' - icon name would be generated from menu item ID plus ".png"
    'ITEM'     => 'images/triolan.gif',        // Item: text, relative image URL or lang reference. Lang reference supports constants and multilevel arrays i.e. 'info[STRUC_MINE_METAL][description]'
    'LINK'     => 'http://www.triolan.com/',   // URL
    'BLANK'    => true,                        // Should link open in new window/tab?
    'SPAN'     => 'lm_overview',               // Class for internal SPAN - to override <A> style. NOT COMPATIBLE WITH STYLE!
    'STYLE'    => 'color: white',              // CSS-class for internal SPAN - to override <A> style. NOT COMPATIBLE WITH SPAN!
    'ALT'      => 'Triolan.COM',               // ALT-tag for image

    'HIDE'     => {0|1},                       // Should be this item hide?

    'WRAP_START' => 'html',                    // HTML-code to put after Tx element - before menu render
    'ITEM_FINISH => 'html',                    // HTML-code to put as last element before potential </a> tag close
    'WRAP_END' => 'html',                      // HTML-code to put before /Tx element - after menu render

    'AUTH_LEVEL' => (int),                     // Меню будет видно только пользователям с уровнем доступа выше указанного
    'DISABLED'  => {0|1},                      // DISABLED == 1 - пункт не будет показан

    'LOCATION' => '+menu_supernova_logo',      // Special atrtribute for modules' $extra_menu. SHOULD BE USE EXCLUSIVE IN MODULES!
                                               // Format
                                               // [-|+][<menu_item_id>]
                                               // <menu_item_id> identifies menu item aginst new menu item would be placed. When ommited new item placed against whole menu
                                               // -/+ indicates that new item should be placed before/after identified menu item (or whole menu). If ommited and menu item exists - new item will replace previous one
                                               // Empty or non-existent LOCATION equivalent to '+' - place item at end of menu
                                               // Non-existent menu_item_id treated as empty
  ),
*/
$sn_menu = [
  'menu_server_name'   => [
    'LEVEL'    => 'text',
    'CLASS'    => 'menu_text_t',
    'ITEM'     => $config->game_name,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => !empty($config->game_name) && !empty($config->menu_server_name_disabled),
  ],
  'menu_server_launch' => [
    'LEVEL'    => 'text',
    'CLASS'    => 'menu_text_t',
    'ITEM'     => "{$lang['sys_from']} {$config->server_start_date}",
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => !empty($config->menu_launch_date_disabled),
  ],
  'menu_server_logo'   => [
    'LEVEL'    => 'text',
    'CLASS'    => 'menu_text_b',
    'TYPE'     => 'image',
    'ITEM'     => empty($config->menu_server_logo) ? 'design/images/supernova.png' : $config->menu_server_logo,
    'LINK'     => '.',
    'ALT'      => $config->game_name,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => !empty($config->menu_server_logo_disabled),
  ],
  'menu_admin'         => [
    'LEVEL'    => 'header',
    'ITEM'     => $lang['user_level'][$user['authlevel']],
    'LINK'     => 'admin/overview.php',
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => $user['authlevel'] < 1,
  ],
  'menu_impersonator'  => [
    'LEVEL'    => 'header',
    'TYPE'     => 'lang',
    'ITEM'     => 'sys_impersonate_done',
    'LINK'     => 'logout.php',
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => $template_result[F_IMPERSONATE_STATUS] == LOGIN_UNDEFINED,
  ],


  'menu_faq'               => [
    'LEVEL'    => 'header',
//    'LEVEL' => 'submenu',
    'TYPE'     => 'lang',
    'ITEM'     => 'm_faq',
    'LINK'     => $config->url_faq,
    'BLANK'    => true,
    'ICON'     => true,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => empty($config->url_faq),
  ],
  'menu_planet_overview'   => [
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_planet',
    'LINK'  => 'overview.php',
    'ICON'  => true,
  ],
  'menu_planet_structures' => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_STRUCTURES]',
    'LINK'  => 'buildings.php?mode=' . QUE_STRUCTURES,
    'ICON'  => true,
  ],
  'menu_planet_shipyard'   => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Shipyard',
    'LINK'  => 'buildings.php?mode=' . SUBQUE_FLEET,
    'ICON'  => true,
  ],
  'menu_planet_defense'    => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Defense',
    'LINK'  => 'buildings.php?mode=' . SUBQUE_DEFENSE,
    'ICON'  => true,
  ],
  'menu_planet_resources'  => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Resources',
    'LINK'  => 'resources.php',
    'ICON'  => true,
  ],
  'menu_planet_fleets'     => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_fleet_orbiting',
    'LINK'  => 'fleet.php',
    'ICON'  => true,
  ],

  'menu_empire_overview' => [
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_empire',
    'LINK'  => 'index.php?page=imperium',
    'ICON'  => true,
  ],
  'menu_info_research'   => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Research',
    'LINK'  => 'buildings.php?mode=' . QUE_RESEARCH,
    'ICON'  => true,
  ],
  'menu_empire_techtree' => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Technology',
    'LINK'  => 'index.php?page=techtree',
    'ICON'  => true,
  ],
  'menu_empire_fleets'   => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'imp_fleets',
    'LINK'  => 'flying_fleets.php',
    'ICON'  => true,
  ],

  'menu_empire_universe'    => [
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'menu_universe_overview',
    'LINK'  => 'galaxy.php?mode=0',
  ],
  'menu_empire_emperor'     => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'imp_imperator',
    'LINK'  => 'index.php?page=imperator',
    'ICON'  => true,
  ],
  'menu_ally'               => [
//    'LEVEL' => 'header',
    'LEVEL'    => 'submenu',
    'TYPE'     => 'lang',
    'ITEM'     => 'sys_alliance',
    'LINK'     => 'alliance.php',
    'ICON'     => true,
    'LOCATION' => '+menu_empire_emperor',
    'DISABLED' => $config->game_mode == GAME_BLITZ,
  ],
  'menu_info_stats'         => [
    'LEVEL' => 'submenu', // header
    'TYPE'  => 'lang',
    'ITEM'  => 'menu_stat_players',
    'LINK'  => 'stat.php',
    'ICON'  => true,
  ],
  'menu_info_records'       => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'menu_stat_records',
    'LINK'  => 'records.php',
    'ICON'  => true,
  ],
  'menu_empire_quests'      => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'menu_quest_list',
    'LINK'  => 'quest.php',
    'ICON'  => true,
  ],

//  'menu_metamatter' => !defined('SN_GOOGLE') ? array(
//    'LEVEL' => 'header',
//    'TYPE'  => 'lang',
//    'ITEM'  => 'sys_metamatter',
//    'LINK'  => 'metamatter.php',
//  ) : array(),
  'menu_dark_matter'        => [
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_dark_matter',
    'LINK'  => 'dark_matter.php',
  ],
  'menu_empire_market'      => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'rinok',
    'LINK'  => 'market.php',
    'ICON'  => true,
  ],
  'menu_empire_mercenaries' => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_MERCENARIES]',
    'LINK'  => 'officer.php?mode=' . UNIT_MERCENARIES,
    'ICON'  => true,
  ],
  'menu_empire_schematics'  => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_PLANS]',
    'LINK'  => 'officer.php?mode=' . UNIT_PLANS,
    'ICON'  => true,
  ],
  'menu_empire_artifacts'   => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_ARTIFACTS]',
    'LINK'  => 'artifacts.php',
    'ICON'  => true,
  ],
  'menu_affiliates'         => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_affilates',
    'LINK'  => 'affilates.php',
    'ICON'  => true,
  ],

  'menu_comm_messages' => [
//    'LEVEL' => 'submenu',
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'Messages',
    'LINK'  => 'messages.php',
    'ICON'  => true,
  ],
  'menu_comm_chat'     => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Chat',
    'LINK'  => 'index.php?page=chat&mode=' . CHAT_MODE_COMMON,
    'ICON'  => true,
  ],
  'menu_ally_chat'     => [
    'LEVEL'    => 'submenu',
    'TYPE'     => 'lang',
    'ITEM'     => 'AllyChat',
    'LINK'     => 'index.php?page=chat&mode=' . CHAT_MODE_ALLY,
    'ICON'     => true,
    'DISABLED' => $config->game_mode == GAME_BLITZ,
  ],

  'menu_comm_forum'      => [
    'LEVEL'    => 'submenu',
    'TYPE'     => 'lang',
    'ITEM'     => 'm_forum',
    'LINK'     => $config->url_forum,
    'BLANK'    => true,
    'ICON'     => true,
    'DISABLED' => empty($config->url_forum),
  ],

//  'menu_utils' => array(
//    'LEVEL' => 'header',
//    'TYPE'  => 'lang',
//    'ITEM'  => 'm_others',
//  ),
  'menu_utils_search'    => [
    'LEVEL'    => 'header',
//    'LEVEL' => 'submenu',
    'TYPE'     => 'lang',
    'ITEM'     => 'Search',
    'LINK'     => 'search.php',
    'ICON'     => true,
    'DISABLED' => $config->game_mode == GAME_BLITZ,
  ],
  'menu_utils_shortcuts' => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_shortcuts',
    'LINK'  => 'notes.php',
    'ICON'  => true,
  ],
  'menu_utils_buddies'   => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Buddylist',
    'LINK'  => 'buddy.php',
    'ICON'  => true,
  ],
  'menu_utils_reports'   => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_combat_reports',
    'LINK'  => 'viewreport.php',
    'ICON'  => true,
  ],
  'menu_utils_simulator' => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_simulator',
    'LINK'  => 'simulator.php',
    'ICON'  => true,
  ],
  'menu_rules'           => [
    'LEVEL'    => 'header',
    'TYPE'     => 'lang',
    'ITEM'     => 'sys_game_rules',
    'LINK'     => $config->url_rules,
    'BLANK'    => true,
    'ICON'     => true,
    'DISABLED' => empty($config->url_rules),
  ],

  'menu_news'          => [
    'LEVEL'       => 'submenu',
    'ITEM'        => $lang['news_title'],
    'ITEM_FINISH' => ($user['news_lastread'] < $config->var_news_last ? "&nbsp;<span class=\"fresh\">{$lang['lm_announce_fresh']}</span>" : ''),
    'LINK'        => 'announce.php',
    'ICON'        => true,
  ],
  'menu_documentation' => [
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_game_documentation',
    'LINK'  => 'docs/html/readme.html',
    'BLANK' => true,
    'ICON'  => true,
  ],
  'menu_info_ban'      => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_banned',
    'LINK'  => 'banned.php',
    'ICON'  => true,
  ],
  'menu_info_server'   => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_server_info',
    'LINK'  => 'server_info.php',
    'ICON'  => true,
  ],
  'menu_info_admins'   => [
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'commun',
    'LINK'  => 'index.php?page=contact',
    'ICON'  => true,
  ],

  'menu_options' => [
    'LEVEL'    => 'header',
    'TYPE'     => 'lang',
    'ITEM'     => 'Options',
    'LINK'     => 'index.php?page=options',
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
  ],

  'menu_logout' => [
    'LEVEL'    => 'header',
    'TYPE'     => 'lang',
    'ITEM'     => 'Logout',
    'LINK'     => 'logout.php',
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
  ],

  'menu_extra' => [
    'LEVEL'    => 'submenu',
    'CLASS'    => 'c_c',
    'ITEM'     => $config->advGoogleLeftMenuCode,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' =>
      !$config->advGoogleLeftMenuIsOn
      || empty($config->advGoogleLeftMenuCode)
      || empty($user)
      || SN_TIME_NOW - $user['register_time'] < PERIOD_WEEK
      || mrc_get_level($user, [], UNIT_PREMIUM)
    ,
  ],

  'menu_supernova_logo' => [
    'LEVEL'    => 'submenu',
    'TYPE'     => 'image',
    'CLASS'    => 'c_c',
    'ITEM'     => 'design/images/supernova.png',
    'LINK'     => 'http://supernova.ws/index-ru.html',
    'ALT'      => 'Powered by \'Project "SuperNova.WS"\' engine',
    'BLANK'    => true,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
  ],

  /*
    'menu_triolan' => array(
      'LEVEL' => 'submenu',
      'TYPE'  => 'image',
      'CLASS' => 'c_c',
      'ITEM'  => 'images/triolan.gif',
      'LINK'  => 'http://www.triolan.com/',
      'BLANK' => true,
      'ALT'   => 'Hosted @ Triolan.COM',
    ),
  */
];


//$sn_menu_admin = defined('IN_ADMIN') && IN_ADMIN === true ? array(
$sn_menu_admin = [
  'menu_admin_server_name' => [
    'LEVEL' => 'header',
    'TYPE'  => 'text',
    'ITEM'  => $config->game_name,
    MENU_FIELD_AUTH_LEVEL => AUTH_LEVEL_MODERATOR,
  ],
  'menu_admin_server_time' => [
    'TYPE' => 'text',
    'ITEM' => '',
    'AUTH_LEVEL' => AUTH_LEVEL_MODERATOR,
  ],

  'menu_admin_version_check' => [
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'adm_opt_ver_check',
    'AUTH_LEVEL' => AUTH_LEVEL_MODERATOR,
  ],
  'menu_admin_version_info'  => [
    'TYPE' => 'text',
    'ITEM' => ($config->server_updater_check_last ? date(FMT_DATE, $config->server_updater_check_last) : '') . '<div class="' .
      $sn_version_check_class[$config->server_updater_check_result] . '">' . $lang['adm_opt_ver_response_short'][$config->server_updater_check_result] . '</div>',
    'AUTH_LEVEL' => AUTH_LEVEL_MODERATOR,
  ],

  'USER_AUTHLEVEL_NAME'       => [
    'LEVEL' => 'header',
    'ITEM'  => $lang['user_level'][$user['authlevel']],
    'LINK'  => 'index.php',
    'AUTH_LEVEL' => AUTH_LEVEL_MODERATOR,
  ],

  'menu_admin_overview'       => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_over',
    'LINK'       => 'admin/overview.php',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
  ],

  'menu_admin_configuration'  => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_conf',
    'LINK'       => 'admin/settings.php',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
  ],
  'menu_admin_modules'          => [
    'TYPE'       => 'lang',
    'ITEM'       => 'menu_admin_modules',
    'LINK'       => 'index.php?page=admin/admin_modules',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
  ],
  'menu_admin_quests'         => [
    'TYPE'       => 'lang',
    'ITEM'       => 'qst_quests',
    'LINK'       => 'admin/adm_quest.php',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
  ],

  'menu_admin_dark_matter'    => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'dark_matter',
    'LINK'       => 'admin/admin_darkmatter.php',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
  ],
  'menu_admin_matter_analyze' => [
    'TYPE'       => 'lang',
    'ITEM'       => 'matter_analyze',
    'LINK'       => 'admin/admin_analyze_matter.php',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
  ],

//  'menu_admin_metamatter_header'  => [
//    'LEVEL'      => 'header',
//    'TYPE'       => 'lang',
//    'ITEM'       => 'adm_metametter_payment',
//    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
//    'DISABLED'   => !$active_payment_modules,
//  ],
  'menu_admin_metamatter'         => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'sys_metamatter',
    'LINK'       => 'admin/adm_metamatter.php',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
    'DISABLED'   => !$active_payment_modules,
  ],
  'menu_admin_metametter_payment' => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_pay',
//    'LINK'       => 'admin/adm_payment.php',
    'LINK'       => 'index.php?page=admin/admin_payment',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
    'DISABLED'   => !$active_payment_modules,
  ],

  'menu_admin_player'     => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'player',
    'LINK'       => 'admin/userlist.php',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
  ],
  'menu_admin_player_ban' => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_ban_unban',
    'LINK'       => 'admin/banned.php',
    'AUTH_LEVEL' => AUTH_LEVEL_MODERATOR,
  ],
  'menu_admin_mining'      => [
    'TYPE'       => 'lang',
    'ITEM'       => 'menu_admin_mining',
    'LINK'       => 'index.php?page=admin/admin_mining',
    'AUTH_LEVEL' => AUTH_LEVEL_ADMINISTRATOR,
  ],
//  'menu_admin_player_list' => array(
//    'TYPE'       => 'lang',
//    'ITEM'       => 'adm_plrlst',
//    'LINK'       => 'admin/userlist.php',
//    'AUTH_LEVEL' => 3,
//  ),

  'menu_admin_ally'                => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'menu_admin_ally',
    'LINK'       => 'index.php?page=admin/admin_ally',
    'AUTH_LEVEL' => 3,
  ],

//  'menu_admin_universe'            => array(
//    'LEVEL'      => 'header',
//    'TYPE'       => 'lang',
//    'ITEM'       => 'sys_universe',
//    'AUTH_LEVEL' => 3,
//  ),
  'menu_admin_planet_list_active'  => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_planet_active',
    'LINK'       => 'admin/adm_planet_list.php?planet_active=1',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_planet_list_planets' => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_pltlst',
    'LINK'       => 'admin/adm_planet_list.php?planet_type=' . PT_PLANET,
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_planet_list_moons'   => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_moonlst',
    'LINK'       => 'admin/adm_planet_list.php?planet_type=' . PT_MOON,
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_planet_moon_add'     => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_addmoon',
    'LINK'       => 'admin/add_moon.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_planet_compensate'   => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_lm_compensate',
    'LINK'       => 'admin/planet_compensate.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_fleets'              => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_fleet',
    'LINK'       => 'admin/adm_flying_fleets.php',
    'AUTH_LEVEL' => 3,
  ],

  'menu_admin_utilites'    => [
    'LEVEL'      => 'header',
    'TYPE'       => 'lang',
    'ITEM'       => 'tool',
    'CLASS'      => 'link',
    'LINK'       => 'admin/tools.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_statbuilder' => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_updpt',
    'LINK'       => 'admin/statbuilder.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_languages'   => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_lng_title',
    'LINK'       => 'admin/admin_locale.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_maintenance' => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_maint',
    'LINK'       => 'admin/maintenance.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_backup'      => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_backup',
    'LINK'       => 'admin/sxd/index.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_messages'    => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_msg',
    'LINK'       => 'admin/adm_message_list.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_chat'        => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_chat',
    'LINK'       => 'admin/admin_chat.php',
    'AUTH_LEVEL' => 3,
  ],
  'menu_admin_logs'        => [
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_log_main',
    'LINK'       => 'admin/adm_log_main.php',
    'AUTH_LEVEL' => 3,
  ],

  'menu_admin_exit' => [
    'LEVEL'      => 'header',
    'CLASS'      => 'link',
    'TYPE'       => 'lang',
    'ITEM'       => 'adm_back',
    'LINK'       => 'index.php',
    'AUTH_LEVEL' => AUTH_LEVEL_MODERATOR,
  ],
];
