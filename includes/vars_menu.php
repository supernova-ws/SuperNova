<?php

if(!defined('INSIDE')) {
  die('Hack attempt!');
}

lng_include('menu');

$active_payment_modules = sn_module_get_active_count('payment') > 0;

global $sn_version_check_class, $template_result, $user, $config, $lang; // , $sn_menu_admin_extra

$sn_menu = array(
/*
  'menu_triolan' => array(                     // This should be used as ID for both internal submenu insert AND as "id" attribute of Tx HTML-tag (see below)
    'LEVEL'    => 'submenu',                   // Which Tx HTML tag to use. 'header' - would be used TH; 'submenu' - TD
    'TYPE'     => 'image',                     // Menu item type: 'image' (wrapped by IMG tag), 'text' (puts "as-is") or 'lang' for late biding with $lang[ITEM] values. Default is 'text'
    'CLASS'    => 'c_c',                       // Class for TD/TH element. Can be c_c, c_l, c_r or any other custom. 'c_c' default for 'header', 'c_l' default for 'text'
    'TITLE'    => 'Triolan.COM',               // TITLE tag for Tx HTML-element
    'ICON'     => 'menu_icon.png',             // Icon filename - would be searched in $dpath/icons/. If 'true' - icon name would be generated from menu item ID plus ".png"
    'ITEM'     => 'images/triolan.gif',        // Item: text, relative image URL or lang reference. Lang reference supports constants and multilevel arrays i.e. 'info[STRUC_MINE_METAL][description]'
    'LINK'     => 'http://www.triolan.com/',   // URL
    'BLANK'    => true,                        // Should link open in new window/tab?
    'SPAN'     => 'lm_overview',               // Class for internal SPAN - to override <A> style. NOT COMPATIBLE WITH STYLE!
    'STYLE'    => 'color: white',              // CSS-class for internal SPAN - to override <A> style. NOT COMPATIBLE WITH SPAN!
    'ALT'      => 'Triolan.COM',               // ALT-tag for image

    'HIDE'     => {0|1},                       // Should be this item hide?

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

  'menu_server_name' => array(
    'LEVEL' => 'text',
    'CLASS' => 'menu_text_t',
    'ITEM' => "{$config->game_name}<br />{$lang['sys_from']} {$config->server_start_date}",
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
  ),
  'menu_server_logo' => array(
    'LEVEL' => 'text',
    'CLASS' => 'menu_text_b',
    'TYPE' => 'image',
    'ITEM' => 'design/images/supernova.png',
    'LINK' => '.',
    'ALT' => $config->game_name,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
  ),
  'menu_admin' => array(
    'LEVEL' => 'header',
    'ITEM'  => $lang['user_level'][$user['authlevel']],
    'LINK'  => 'admin/overview.php',
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => $user['authlevel'] < 1,
  ),
  'menu_impersonator' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_impersonate_done',
    'LINK'  => 'logout.php',
    'SPAN'  => 'important',
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => $template_result[F_IMPERSONATE_STATUS] == LOGIN_UNDEFINED,
  ),


/*
  'menu_planet' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_planet',
  ),
*/
  'menu_faq' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_faq',
    'LINK'  => $config->url_faq,
    'BLANK' => true,
    'ICON'  => true,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => empty($config->url_faq),
  ),
  'menu_planet_overview' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_planet',
    'LINK'  => 'overview.php',
    'ICON'  => true,
  ),
  'menu_planet_structures' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_STRUCTURES]',
    'LINK'  => 'buildings.php?mode=' . QUE_STRUCTURES,
    'ICON'  => true,
  ),
  'menu_planet_shipyard' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Shipyard',
    'LINK'  => 'buildings.php?mode=' . SUBQUE_FLEET,
    'ICON'  => true,
  ),
  'menu_planet_defense' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Defense',
    'LINK'  => 'buildings.php?mode=' . SUBQUE_DEFENSE,
    'ICON'  => true,
  ),
  'menu_planet_resources' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Resources',
    'LINK'  => 'resources.php',
    'ICON'  => true,
  ),
  'menu_planet_fleets' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_fleet_orbiting',
    'LINK'  => 'fleet.php',
    'ICON'  => true,
  ),

/*
  'menu_empire' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_empire',
  ),
*/
  'menu_empire_overview' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_empire',
    'LINK'  => 'index.php?page=imperium',
    'ICON'  => true,
  ),
  'menu_info_research' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Research',
    'LINK'  => 'buildings.php?mode=' . QUE_RESEARCH,
    'ICON'  => true,
  ),
  'menu_empire_techtree' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Technology',
    'LINK'  => 'index.php?page=techtree',
    'ICON'  => true,
  ),
  'menu_empire_fleets' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'imp_fleets',
    'LINK'  => 'flying_fleets.php',
    'ICON'  => true,
  ),
  'menu_empire_quests' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'menu_quest_list',
    'LINK'  => 'quest.php',
    'ICON'  => true,
  ),
  'menu_empire_universe' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'menu_universe_overview',
    'LINK'  => 'galaxy.php?mode=0',
  ),

/*
  'menu_stats' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'Statistics',
  ),
*/
  'menu_info_stats' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'menu_stat_players',
    'LINK'  => 'stat.php',
    'ICON'  => true,
  ),
  'menu_info_records' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'menu_stat_records',
    'LINK'  => 'records.php',
    'ICON'  => true,
  ),
  'menu_empire_emperor' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'imp_imperator',
    'LINK'  => 'index.php?page=imperator',
    'ICON'  => true,
  ),

//  'menu_dark_matter_h' => array(
//    'LEVEL' => 'header',
//    'TYPE'  => 'lang',
//    'ITEM'  => 'sys_dark_matter',
//    'ICON'  => true,
//  ),
  'menu_dark_matter' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_dark_matter',
    'LINK'  => 'dark_matter.php',
  ),
  'menu_empire_market' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'rinok',
    'LINK'  => 'market.php',
    'ICON'  => true,
  ),
  'menu_empire_mercenaries' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_MERCENARIES]',
    'LINK'  => 'officer.php?mode=' . UNIT_MERCENARIES,
    'ICON'  => true,
  ),
  'menu_empire_schematics' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_PLANS]',
    'LINK'  => 'officer.php?mode=' . UNIT_PLANS,
    'ICON'  => true,
  ),
  'menu_empire_artifacts' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_ARTIFACTS]',
    'LINK'  => 'artifacts.php',
    'ICON'  => true,
  ),
  'menu_affiliates' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_affilates',
    'LINK'  => 'affilates.php',
    'ICON'  => true,
  ),
  // 'menu_ally' => $config->game_mode == GAME_BLITZ ? null : array(
  'menu_ally' => array(
    'LEVEL' => 'header',
    //    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_alliance',
    'LINK'  => 'alliance.php',
    'ICON'  => true,
    'LOCATION' => '+menu_affiliates',
    'DISABLED' => $config->game_mode == GAME_BLITZ,
  ),
  // 'menu_ally_chat' => $config->game_mode == GAME_BLITZ ? null : array(
  'menu_ally_chat' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'AllyChat',
    'LINK'  => 'index.php?page=chat&mode=' . CHAT_MODE_ALLY,
    'ICON'  => true,
    'DISABLED' => $config->game_mode == GAME_BLITZ,
  ),

/*
  'menu_ally_overview' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_alliance',
  ),
*/

  'menu_comm' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_communication',
  ),
  'menu_comm_messages' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Messages',
    'LINK'  => 'messages.php',
    'ICON'  => true,
  ),
  'menu_comm_chat' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Chat',
    'LINK'  => 'index.php?page=chat&mode=' . CHAT_MODE_COMMON,
    'ICON'  => true,
  ),
  // 'menu_comm_forum' => !$config->url_forum ? array() : array(
  'menu_comm_forum' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_forum',
    'LINK'  => $config->url_forum,
    'BLANK' => true,
    'ICON'  => true,
    'DISABLED' => empty($config->url_forum),
  ),

  'menu_utils' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_others',
  ),
  // 'menu_utils_search' => $config->game_mode == GAME_BLITZ ? array() : array(
  'menu_utils_search' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Search',
    'LINK'  => 'search.php',
    'ICON'  => true,
    'DISABLED' => $config->game_mode == GAME_BLITZ,
  ),
  'menu_utils_shortcuts' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_shortcuts',
    'LINK'  => 'notes.php',
    'ICON'  => true,
  ),
  'menu_utils_buddies' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Buddylist',
    'LINK'  => 'buddy.php',
    'ICON'  => true,
  ),
//  'menu_utils_notes' => array(
//    'LEVEL' => 'submenu',
//    'TYPE'  => 'lang',
//    'ITEM'  => 'Notes',
//    'LINK'  => 'notes.php',
//    'ICON'  => true,
//  ),
  'menu_utils_reports' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_combat_reports',
    'LINK'  => 'viewreport.php',
    'ICON'  => true,
  ),
  'menu_utils_simulator' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_simulator',
    'LINK'  => 'simulator.php',
    'ICON'  => true,
  ),
  // 'menu_rules' => !$config->url_rules ? array() : array(
  'menu_rules' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_game_rules',
    'LINK'  => $config->url_rules,
    'BLANK' => true,
    'ICON'  => true,
    'DISABLED' => empty($config->url_rules),
  ),

/*
  'menu_info' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'navig',
  ),
*/
  'menu_news' => array(
    'LEVEL' => 'submenu',
//    'ITEM'  => $lang['news_title'] . ($user['news_lastread'] < $config->var_news_last ? "&nbsp;<span class=\"important\">{$lang['lm_announce_fresh']}</span>" : ''),
    'ITEM'  => $lang['news_title'],
    'WRAP_END' => ($user['news_lastread'] < $config->var_news_last ? "&nbsp;<span class=\"important\" style='display:inline'>{$lang['lm_announce_fresh']}</span>" : ''),
    'LINK'  => 'announce.php',
    'ICON'  => true,
  ),
  'menu_documentation' => array(
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_game_documentation',
    'LINK'  => 'docs/html/readme.html',
    'BLANK' => true,
    'ICON'  => true,
  ),
  'menu_info_ban' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_banned',
    'LINK'  => 'banned.php',
    'ICON'  => true,
  ),
  'menu_info_server' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_server_info',
    'LINK'  => 'server_info.php',
    'ICON'  => true,
  ),
  'menu_info_admins' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'commun',
    'LINK'  => 'index.php?page=contact',
    'ICON'  => true,
  ),

  'menu_options' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'Options',
    'LINK'  => 'index.php?page=options',
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
  ),

  'menu_logout' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'Logout',
    'LINK'  => 'logout.php',
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
  ),

  'menu_extra' => array(
    'LEVEL' => 'submenu',
    'CLASS' => 'c_c',
    'ITEM'  => $config->advGoogleLeftMenuCode,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
    'DISABLED' => !$config->advGoogleLeftMenuIsOn || empty($config->advGoogleLeftMenuCode),
  ),

  'menu_supernova_logo' => array(
    'LEVEL' => 'submenu',
    'TYPE' => 'image',
    'CLASS' => 'c_c',
    'ITEM' => 'design/images/supernova.png',
    'LINK' => 'http://supernova.ws/index-ru.html',
    'ALT' => 'Powered by \'Project "SuperNova.WS"\' engine',
    'BLANK' => true,
    'MOVEABLE' => 2,
    'HIDEABLE' => 3,
  ),

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
);


$sn_menu_admin = array(
  'menu_admin_server_name' => array(
    'LEVEL' => 'header',
    'TYPE' => 'text',
    'ITEM' => $config->game_name,
  ),
  'menu_admin_server_time' => array(
    'TYPE' => 'text',
    'ITEM' => '',
  ),

  'menu_admin_version_check' => array(
    'LEVEL' => 'header',
    'TYPE' => 'lang',
    'ITEM' => 'adm_opt_ver_check',
  ),
  'menu_admin_version_info' => array(
    'TYPE' => 'text',
    'ITEM'  => ($config->server_updater_check_last ? date(FMT_DATE, $config->server_updater_check_last) : '') . '<div class="' .
      $sn_version_check_class[$config->server_updater_check_result] . '">' . $lang['adm_opt_ver_response_short'][$config->server_updater_check_result] . '</div>',
  ),

  'USER_AUTHLEVEL_NAME' => array(
    'LEVEL' => 'header',
//    'TYPE' => 'lang',
//    'ITEM'  => 'user_level[USER_LEVEL]',
    'ITEM'  => $lang['user_level'][$user['authlevel']],
  ),
  'menu_admin_overview' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_over',
    'LINK' => 'admin/overview.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_quests' => array(
    'TYPE'  => 'lang',
    'ITEM'  => 'qst_quests',
    'LINK'  => 'admin/adm_quest.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_configuration' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_conf',
    'LINK' => 'admin/settings.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_dark_matter' => array(
    'TYPE' => 'lang',
    'ITEM' => 'dark_matter',
    'LINK' => 'admin/admin_darkmatter.php',
    'AUTH_LEVEL' => 3,
  ),

  'menu_admin_metamatter_header' => array(
    'LEVEL' => 'header',
    'TYPE' => 'lang',
    'ITEM' => 'adm_metametter_payment',
    'AUTH_LEVEL' => 3,
    'DISABLED' => !$active_payment_modules,
  ),
  'menu_admin_metamatter' => array(
    'TYPE' => 'lang',
    'ITEM' => 'sys_metamatter',
    'LINK' => 'admin/adm_metamatter.php',
    'AUTH_LEVEL' => 3,
    'DISABLED' => !$active_payment_modules,
  ),
  'menu_admin_metametter_payment' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_pay',
    'LINK' => 'admin/adm_payment.php',
    'AUTH_LEVEL' => 3,
    'DISABLED' => !$active_payment_modules,
  ),

  'menu_admin_player' => array(
    'LEVEL' => 'header',
    'TYPE' => 'lang',
    'ITEM' => 'player',
  ),
  'menu_admin_player_ban' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_ban_unban',
    'LINK' => 'admin/banned.php',
  ),
  'menu_admin_player_list' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_plrlst',
    'LINK' => 'admin/userlist.php',
    'AUTH_LEVEL' => 3,
  ),

  'menu_admin_universe' => array(
    'LEVEL' => 'header',
    'TYPE' => 'lang',
    'ITEM' => 'sys_universe',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_planet_list_active' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_planet_active',
    'LINK' => 'admin/adm_planet_list.php?planet_active=1',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_planet_list_planets' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_pltlst',
    'LINK' => 'admin/adm_planet_list.php?planet_type=' . PT_PLANET,
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_planet_list_moons' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_moonlst',
    'LINK' => 'admin/adm_planet_list.php?planet_type=' . PT_MOON,
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_planet_moon_add' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_addmoon',
    'LINK' => 'admin/add_moon.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_planet_compensate' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_lm_compensate',
    'LINK' => 'admin/planet_compensate.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_fleets' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_fleet',
    'LINK' => 'admin/adm_flying_fleets.php',
    'AUTH_LEVEL' => 3,
  ),

  'menu_admin_utilites' => array(
    'LEVEL' => 'header',
    'TYPE' => 'lang',
    'ITEM' => 'tool',
    'CLASS' => 'link',
    'LINK' => 'admin/tools.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_statbuilder' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_updpt',
    'LINK' => 'admin/statbuilder.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_languages' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_lng_title',
    'LINK' => 'admin/admin_locale.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_maintenance' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_maint',
    'LINK' => 'admin/maintenance.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_backup' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_backup',
    'LINK' => 'admin/sxd/index.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_messages' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_msg',
    'LINK' => 'admin/adm_message_list.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_chat' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_chat',
    'LINK' => 'admin/admin_chat.php',
    'AUTH_LEVEL' => 3,
  ),
  'menu_admin_logs' => array(
    'TYPE' => 'lang',
    'ITEM' => 'adm_log_main',
    'LINK' => 'admin/adm_log_main.php',
    'AUTH_LEVEL' => 3,
  ),

  'menu_admin_exit' => array(
    'LEVEL' => 'header',
    'CLASS' => 'link',
    'TYPE'  => 'lang',
    'ITEM'  => 'adm_back',
    'LINK'  => 'index.php',
  ),
);
