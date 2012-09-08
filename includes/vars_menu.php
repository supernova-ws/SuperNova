<?php

if(!defined('INSIDE'))
{
  die('Hack attempt!');
}

lng_include('menu');

$sn_menu = array(
/*
  'menu_triolan' => array(                     // This should be used as ID for both internal submenu insert AND as "id" attribute of Tx HTML-tag (see below)
    'LEVEL'    => 'submenu',                   // Which Tx HTML tag to use. 'header' - would be used TH; 'submenu' - TD
    'TYPE'     => 'image',                     // Menu item type: 'image' (wrapped by IMG tag), 'text' (puts "as-is") or 'lang' for late biding with $lang[ITEM] values. Default is 'text'
    'CLASS'    => 'c_c',                       // Class for TD/TH element. Can be c_c, c_l, c_r or any other custom. 'c_c' default for 'header', 'c_l' default for 'text'
    'TITLE'    => 'Triolan.COM',               // TITLE tag for Tx HTML-element
    'ICON'     => 'menu_icon.png',             // Icon filename - would be searched in $dpath/icons/
    'ITEM'     => 'images/triolan.gif',        // Item: text, relative image URL or lang reference. Lang reference supports constants and multilevel arrays i.e. 'info[STRUC_MINE_METAL][description]'
    'LINK'     => 'http://www.triolan.com/',   // URL
    'BLANK'    => true,                        // Should link open in new window/tab?
    'SPAN'     => 'lm_overview',               // Class for internal SPAN - to override <A> style. NOT COMPATIBLE WITH STYLE!
    'STYLE'    => 'color: white',              // CSS-class for internal SPAN - to override <A> style. NOT COMPATIBLE WITH SPAN!
    'ALT'      => 'Triolan.COM',               // ALT-tag for image

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
    'LEVEL' => 'header',
    'ITEM' => "{$config->game_name}<br />{$lang['sys_from']} {$config->server_start_date}",
  ),
  'menu_server_logo' => array(
    'LEVEL' => 'submenu',
    'TYPE' => 'image',
    'CLASS' => 'c_c',
    'ITEM' => 'design/images/supernova.png',
    'LINK' => '.',
    'ALT' => 'supernova.ws',
  ),

  'menu_admin' => $user['authlevel'] <= 0 ? array() : array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'user_level[USER_LEVEL]',
    'LINK'  => 'admin/overview.php',
  ),
  'menu_impersonator' => !is_array($user_impersonator) ? array() : array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_impersonate_done',
    'LINK'  => 'logout.php',
    'SPAN'  => 'important',
  ),

  'menu_rules' => !$config->url_rules ? array() : array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_game_rules',
    'LINK'  => $config->url_rules,
  ),
  'menu_faq' => !$config->url_faq ? array() : array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_faq',
    'LINK'  => $config->url_faq,
  ),

  'menu_news' => array(
    'LEVEL' => 'header',
    'ITEM'  => $lang['news_title'] . ($user['news_lastread'] < $config->var_news_last ? "&nbsp;<span class=\"important\">{$lang['lm_announce_fresh']}</span>" : ''),
    'LINK'  => 'announce.php',
  ),

  'menu_dark_matter' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_dark_matter',
    'LINK'  => 'dark_matter.php',
  ),

  'menu_affiliates' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_affilates',
    'LINK'  => 'affilates.php',
  ),

  'menu_planet' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_planet',
  ),
  'menu_planet_overview' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Overview',
    'LINK'  => 'overview.php',
  ),
  'menu_planet_resources' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Resources',
    'LINK'  => 'resources.php',
  ),
  'menu_planet_fleets' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_fleet_orbiting',
    'LINK'  => 'fleet.php',
  ),
  'menu_planet_structures' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Buildings',
    'LINK'  => 'buildings.php?mode=' . QUE_STRUCTURES,
  ),
  'menu_planet_shipyard' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Shipyard',
    'LINK'  => 'buildings.php?mode=' . SUBQUE_FLEET,
  ),
  'menu_planet_defense' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Defense',
    'LINK'  => 'buildings.php?mode=' . SUBQUE_DEFENSE,
  ),

  'menu_empire' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_empire',
  ),
  'menu_empire_overview' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'imp_overview',
    'LINK'  => 'imperium.php',
  ),
  'menu_empire_emperor' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'imp_imperator',
    'LINK'  => 'index.php?page=imperator',
  ),
  'menu_empire_fleets' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'imp_fleets',
    'LINK'  => 'flying_fleets.php',
  ),
  'menu_info_research' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Research',
    'LINK'  => 'buildings.php?mode=research',
  ),
  'menu_empire_mercenaries' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_MERCENARIES]',
    'LINK'  => 'officer.php?mode=' . UNIT_MERCENARIES,
  ),
  'menu_empire_schematics' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_PLANS]',
    'LINK'  => 'officer.php?mode=' . UNIT_PLANS,
  ),
  'menu_empire_artifacts' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'tech[UNIT_ARTIFACTS]',
    'LINK'  => 'artifacts.php',
  ),
  'menu_empire_market' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'rinok',
    'LINK'  => 'market.php',
  ),
  'menu_empire_universe' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_universe',
    'LINK'  => 'galaxy.php?mode=0',
  ),

  'menu_ally' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_alliance',
  ),
  'menu_ally_overview' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'sys_alliance',
    'LINK'  => 'alliance.php',
  ),
  'menu_ally_chat' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'AllyChat',
    'LINK'  => 'index.php?page=chat&mode=' . CHAT_MODE_ALLY,
  ),

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
  ),
  'menu_comm_chat' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Chat',
    'LINK'  => 'index.php?page=chat&mode=' . CHAT_MODE_COMMON,
  ),
  'menu_comm_forum' => !$config->url_forum ? array() : array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_forum',
    'LINK'  => $config->url_forum,
  ),

  'menu_utils' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_others',
  ),
  'menu_utils_simulator' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'm_simulator',
    'LINK'  => 'simulator.php',
  ),
  'menu_utils_reports' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_combat_reports',
    'LINK'  => 'viewreport.php',
  ),
  'menu_utils_buddies' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Buddylist',
    'LINK'  => 'buddy.php',
  ),
  'menu_utils_notes' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Notes',
    'LINK'  => 'notes.php',
  ),
  'menu_utils_shortcuts' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_shortcuts',
    'LINK'  => 'fleet_shortcuts.php',
  ),
  'menu_utils_search' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Search',
    'LINK'  => 'search.php',
  ),

  'menu_info' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'navig',
  ),
  'menu_empire_techtree' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Technology',
    'LINK'  => 'index.php?page=techtree',
  ),
  'menu_empire_quests' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'qst_quests',
    'LINK'  => 'quest.php',
  ),
  'menu_info_stats' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Statistics',
    'LINK'  => 'stat.php',
  ),
  'menu_info_records' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'Records',
    'LINK'  => 'records.php',
  ),
  'menu_info_server' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_server_info',
    'LINK'  => 'server_info.php',
  ),
  'menu_info_ban' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'lm_banned',
    'LINK'  => 'banned.php',
  ),
  'menu_info_admins' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'lang',
    'ITEM'  => 'commun',
    'LINK'  => 'index.php?page=contact',
  ),

  'menu_options' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'Options',
    'LINK'  => 'index.php?page=options',
  ),

  'menu_logout' => array(
    'LEVEL' => 'header',
    'TYPE'  => 'lang',
    'ITEM'  => 'Logout',
    'LINK'  => 'logout.php',
  ),

  'menu_extra' => !$config->advGoogleLeftMenuIsOn ? array() : array(
    'LEVEL' => 'submenu',
    'CLASS' => 'c_c',
    'ITEM'  => $config->advGoogleLeftMenuCode,
  ),

  'menu_supernova_logo' => array(
    'LEVEL' => 'submenu',
    'TYPE' => 'image',
    'CLASS' => 'c_c',
    'ITEM' => 'design/images/supernova.png',
    'LINK' => 'http://supernova.ws',
    'ALT' => 'Powered by \'Project "SuperNova.WS"\' engine',
  ),

  'menu_triolan' => array(
    'LEVEL' => 'submenu',
    'TYPE'  => 'image',
    'CLASS' => 'c_c',
    'ITEM'  => 'images/triolan.gif',
    'LINK'  => 'http://www.triolan.com/',
    'BLANK' => true,
    'ALT'   => 'Hosted @ Triolan.COM',
  ),

);

?>
