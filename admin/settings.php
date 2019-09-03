<?php

/**
 * settings.php
 *
 * @version 2.0 Full rewrite copyright (c) Gorlum 2009-2010 for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

use Unit\DBStaticUnit;

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

$template = SnTemplate::gettemplate('admin/settings', true);

if(sys_get_param('save')) {
  SN::$config->game_name               = sys_get_param_str_unsafe('game_name');
  SN::$config->game_mode               = sys_get_param_int('game_mode');
  SN::$config->game_speed              = sys_get_param_float('game_speed', 1);
  SN::$config->fleet_speed             = sys_get_param_float('fleet_speed', 1);
  SN::$config->resource_multiplier     = sys_get_param_float('resource_multiplier', 1);
  SN::$config->user_vacation_disable   = sys_get_param_int('user_vacation_disable', 0);
  SN::$config->url_faq                 = sys_get_param_str_unsafe('url_faq');
  SN::$config->url_forum               = sys_get_param_str_unsafe('url_forum');
  SN::$config->url_rules               = sys_get_param_str_unsafe('url_rules');
  SN::$config->url_purchase_metamatter         = sys_get_param_str_unsafe('url_purchase_metamatter');
  SN::$config->game_disable            = sys_get_param_int('game_disable');
  SN::$config->game_disable_reason     = sys_get_param_str_unsafe('game_disable_reason');
  SN::$config->server_updater_check_auto = sys_get_param_int('server_updater_check_auto');

  SN::$config->game_user_changename      = sys_get_param_int('game_user_changename', SN::$config->game_user_changename);
  SN::$config->game_user_changename_cost = sys_get_param_int('game_user_changename_cost', SN::$config->game_user_changename_cost);

  SN::$config->eco_scale_storage       = sys_get_param_int('eco_scale_storage');

  SN::$config->game_default_language   = sys_get_param_str_unsafe('game_default_language', DEFAULT_LANG);
  SN::$config->game_default_skin       = sys_get_param_str_unsafe('game_default_skin', DEFAULT_SKINPATH);
  SN::$config->game_default_template   = sys_get_param_str_unsafe('game_default_template', SnTemplate::getServerDefaultTemplateName());

  SN::$config->game_maxGalaxy          = sys_get_param_int('game_maxGalaxy', 5);
  SN::$config->game_maxSystem          = sys_get_param_int('game_maxSystem', 199);
  SN::$config->game_maxPlanet          = sys_get_param_int('game_maxPlanet', 16);

  SN::$config->player_max_colonies     = sys_get_param_int('player_max_colonies', -1);

  SN::$config->fleet_bashing_attacks   = sys_get_param_int('fleet_bashing_attacks', 3);
  SN::$config->fleet_bashing_interval  = sys_get_param_int('fleet_bashing_interval', 30 * 60);
  SN::$config->fleet_bashing_scope     = sys_get_param_int('fleet_bashing_scope', 24 * 60 * 60);
  SN::$config->fleet_bashing_war_delay = sys_get_param_int('fleet_bashing_war_delay', 12 * 60 * 60);
  SN::$config->fleet_bashing_waves     = sys_get_param_int('fleet_bashing_waves', 3);

  SN::$config->allow_buffing           = sys_get_param_int('allow_buffing');
  SN::$config->ally_help_weak          = sys_get_param_int('ally_help_weak');
  SN::$config->game_email_pm           = sys_get_param_int('game_email_pm');

  SN::$config->rpg_exchange_metal      = sys_get_param_int('rpg_exchange_metal', 1);
  SN::$config->rpg_exchange_crystal    = sys_get_param_int('rpg_exchange_crystal', 2);
  SN::$config->rpg_exchange_deuterium  = sys_get_param_int('rpg_exchange_deuterium', 4);
  SN::$config->rpg_exchange_darkMatter = sys_get_param_int('rpg_exchange_darkMatter', 400);

  SN::$config->tpl_minifier            = sys_get_param_int('tpl_minifier', 0);

  SN::$config->initial_fields          = sys_get_param_int('initial_fields', 200);
  SN::$config->eco_planet_starting_metal = sys_get_param_float('eco_planet_starting_metal', 500);
  SN::$config->eco_planet_starting_crystal = sys_get_param_float('eco_planet_starting_crystal', 500);
  SN::$config->eco_planet_starting_deuterium = sys_get_param_float('eco_planet_starting_deuterium', 0);
  SN::$config->metal_basic_income      = sys_get_param_float('metal_basic_income', 40);
  SN::$config->crystal_basic_income    = sys_get_param_float('crystal_basic_income', 20);
  SN::$config->deuterium_basic_income  = sys_get_param_float('deuterium_basic_income', 10);
  SN::$config->energy_basic_income     = sys_get_param_float('energy_basic_income', 0);
  SN::$config->eco_planet_storage_metal = sys_get_param_float('eco_planet_storage_metal', BASE_STORAGE_SIZE);
  SN::$config->eco_planet_storage_crystal = sys_get_param_float('eco_planet_storage_crystal', BASE_STORAGE_SIZE);
  SN::$config->eco_planet_storage_deuterium = sys_get_param_float('eco_planet_storage_deuterium', BASE_STORAGE_SIZE);

  SN::$config->chat_timeout            = sys_get_param_int('chat_timeout', 5);

  SN::$config->game_news_overview      = sys_get_param_int('game_news_overview', 5);
  SN::$config->advGoogleLeftMenuIsOn   = sys_get_param_int('advGoogleLeftMenuIsOn');
  SN::$config->advGoogleLeftMenuCode   = sys_get_param('advGoogleLeftMenuCode');
  SN::$config->debug                   = sys_get_param_int('debug');
  SN::$config->game_counter            = sys_get_param_int('game_counter');
  SN::$config->geoip_whois_url         = sys_get_param_str('geoip_whois_url');

  SN::$config->uni_price_galaxy        = sys_get_param_float('uni_price_galaxy');
  SN::$config->uni_price_system        = sys_get_param_float('uni_price_system');

  SN::$config->user_birthday_gift      = sys_get_param_float('user_birthday_gift');
  SN::$config->user_birthday_range     = sys_get_param_int('user_birthday_range');

  SN::$config->stats_hide_admins       = sys_get_param_int('stats_hide_admins');
  SN::$config->stats_hide_player_list  = sys_get_param_str('stats_hide_player_list');
  SN::$config->stats_hide_pm_link      = sys_get_param_int('stats_hide_pm_link');
  SN::$config->stats_schedule          = sys_get_param_str('stats_schedule');

  SN::$config->empire_mercenary_base_period = sys_get_param_int('empire_mercenary_base_period');
  if(SN::$config->empire_mercenary_temporary != sys_get_param_int('empire_mercenary_temporary')) {
    if(SN::$config->empire_mercenary_temporary) {
      DBStaticUnit::db_unit_list_admin_delete_mercenaries_finished();
    } else {
      DBStaticUnit::db_unit_list_admin_set_mercenaries_expire_time(SN::$config->empire_mercenary_base_period);
    }

    SN::$config->empire_mercenary_temporary = sys_get_param_int('empire_mercenary_temporary');
  }

  SN::$config->db_saveAll();

  $template->assign_var('MESSAGE', $lang['adm_opt_saved']);
}

$template->assign_vars([
  'ALLOW_BUFFING' => SN::$config->allow_buffing,
  'ALLY_HELP_WEAK' => SN::$config->ally_help_weak,
  'GAME_EMAIL_PM' => SN::$config->game_email_pm,
  'game_mode' => SN::$config->game_mode,
  'game_language' => SN::$config->game_default_language,
  'ECO_SCALE_STORAGE' => SN::$config->eco_scale_storage,
  'USER_VACATION_DISABLE' => SN::$config->user_vacation_disable,
  'ADV_LEFT_MENU' => SN::$config->advGoogleLeftMenuIsOn,
  'GAME_DISABLE' => SN::$config->game_disable,
  'GAME_DEBUG' => SN::$config->debug,
  'GAME_COUNTER' => SN::$config->game_counter,
  'TPL_MINIFIER' => SN::$config->tpl_minifier,
  'EMPIRE_MERCENARY_TEMPORARY' => SN::$config->empire_mercenary_temporary,

  'SERVER_UPDATE_CHECK_AUTO' => SN::$config->server_updater_check_auto,
  'CHECK_DATE' => SN::$config->server_updater_check_last ? date(FMT_DATE_TIME, SN::$config->server_updater_check_last) : 0,
  'CHECK_RESULT' => isset($lang['adm_opt_ver_response'][SN::$config->server_updater_check_result]) ? $lang['adm_opt_ver_response'][SN::$config->server_updater_check_result] : $lang['adm_opt_ver_response'][SNC_VER_UNKNOWN_RESPONSE],
  'CHECK_CLASS' => isset($sn_version_check_class[SN::$config->server_updater_check_result]) ? $sn_version_check_class[SN::$config->server_updater_check_result] : $sn_version_check_class[SNC_VER_UNKNOWN_RESPONSE],

  'SERVER_UPDATE_ID' => SN::$config->server_updater_id,
  'SERVER_UPDATE_KEY' => SN::$config->server_updater_key,

  'STATS_HIDE_ADMINS' => SN::$config->stats_hide_admins,
  'STATS_HIDE_PM_LINK' => SN::$config->stats_hide_pm_link,

  'GAME_CHANGE_NAME'      => SN::$config->game_user_changename,
  'GAME_CHANGE_NAME_COST' => SN::$config->game_user_changename_cost,
]);

SnTemplate::tpl_assign_select($template, 'change_name_options', SN::$lang['adm_opt_player_change_name_options']);
SnTemplate::tpl_assign_select($template, 'sys_game_disable_reason', SN::$lang['sys_game_disable_reason'], 'ID', 'NAME');
SnTemplate::tpl_assign_select($template, 'game_modes', SN::$lang['sys_game_mode'], 'ID', 'NAME');
SnTemplate::tpl_assign_select($template, 'ver_response', SN::$lang['adm_opt_ver_response'], 'ID', 'NAME');

$lang_list = lng_get_list();
foreach($lang_list as $lang_id => $lang_data) {
  $template->assign_block_vars('game_languages', array(
    'ID'   => $lang_id,
    'NAME' => "{$lang_data['LANG_NAME_NATIVE']} ({$lang_data['LANG_NAME_ENGLISH']})",
  ));
}

SnTemplate::display($template, $lang['adm_opt_title']);
