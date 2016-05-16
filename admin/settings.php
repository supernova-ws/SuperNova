<?php

/**
 * settings.php
 *
 * @version 2.0 Full rewrite copyright (c) Gorlum 2009-2010 for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$template = gettemplate('admin/settings', true);

if(sys_get_param('save')) {
  classSupernova::$config->game_name               = sys_get_param_str_unsafe('game_name');
  classSupernova::$config->game_mode               = sys_get_param_int('game_mode');
  classSupernova::$config->game_speed              = sys_get_param_float('game_speed', 1);
  classSupernova::$config->fleet_speed             = sys_get_param_float('fleet_speed', 1);
  classSupernova::$config->resource_multiplier     = sys_get_param_float('resource_multiplier', 1);
  classSupernova::$config->user_vacation_disable   = sys_get_param_int('user_vacation_disable', 0);
  classSupernova::$config->url_faq                 = sys_get_param_str_unsafe('url_faq');
  classSupernova::$config->url_forum               = sys_get_param_str_unsafe('url_forum');
  classSupernova::$config->url_rules               = sys_get_param_str_unsafe('url_rules');
  classSupernova::$config->url_purchase_metamatter         = sys_get_param_str_unsafe('url_purchase_metamatter');
  classSupernova::$config->game_disable            = sys_get_param_int('game_disable');
  classSupernova::$config->game_disable_reason     = sys_get_param_str_unsafe('game_disable_reason');
  classSupernova::$config->server_updater_check_auto = sys_get_param_int('server_updater_check_auto');

  classSupernova::$config->eco_scale_storage       = sys_get_param_int('eco_scale_storage');

  classSupernova::$config->game_default_language   = sys_get_param_str_unsafe('game_default_language', DEFAULT_LANG);
  classSupernova::$config->game_default_skin       = sys_get_param_str_unsafe('game_default_skin', DEFAULT_SKINPATH);
  classSupernova::$config->game_default_template   = sys_get_param_str_unsafe('game_default_template', TEMPLATE_NAME);

  Vector::$knownGalaxies = classSupernova::$config->game_maxGalaxy = sys_get_param_int('game_maxGalaxy', 5);
  Vector::$knownSystems  = classSupernova::$config->game_maxSystem = sys_get_param_int('game_maxSystem', 199);
  Vector::$knownPlanets  = classSupernova::$config->game_maxPlanet = sys_get_param_int('game_maxPlanet', 16);

  classSupernova::$config->player_max_colonies     = sys_get_param_int('player_max_colonies', -1);

  classSupernova::$config->fleet_bashing_attacks   = sys_get_param_int('fleet_bashing_attacks', 3);
  classSupernova::$config->fleet_bashing_interval  = sys_get_param_int('fleet_bashing_interval', 30 * 60);
  classSupernova::$config->fleet_bashing_scope     = sys_get_param_int('fleet_bashing_scope', 24 * 60 * 60);
  classSupernova::$config->fleet_bashing_war_delay = sys_get_param_int('fleet_bashing_war_delay', 12 * 60 * 60);
  classSupernova::$config->fleet_bashing_waves     = sys_get_param_int('fleet_bashing_waves', 3);

  classSupernova::$config->allow_buffing           = sys_get_param_int('allow_buffing');
  classSupernova::$config->ally_help_weak          = sys_get_param_int('ally_help_weak');
  classSupernova::$config->game_email_pm           = sys_get_param_int('game_email_pm');

  classSupernova::$config->rpg_exchange_metal      = sys_get_param_int('rpg_exchange_metal', 1);
  classSupernova::$config->rpg_exchange_crystal    = sys_get_param_int('rpg_exchange_crystal', 2);
  classSupernova::$config->rpg_exchange_deuterium  = sys_get_param_int('rpg_exchange_deuterium', 4);
  classSupernova::$config->rpg_exchange_darkMatter = sys_get_param_int('rpg_exchange_darkMatter', 400);

  classSupernova::$config->tpl_minifier            = sys_get_param_int('tpl_minifier', 0);

  classSupernova::$config->initial_fields          = sys_get_param_int('initial_fields', 200);
  classSupernova::$config->eco_planet_starting_metal = sys_get_param_float('eco_planet_starting_metal', 500);
  classSupernova::$config->eco_planet_starting_crystal = sys_get_param_float('eco_planet_starting_crystal', 500);
  classSupernova::$config->eco_planet_starting_deuterium = sys_get_param_float('eco_planet_starting_deuterium', 0);
  classSupernova::$config->metal_basic_income      = sys_get_param_float('metal_basic_income', 40);
  classSupernova::$config->crystal_basic_income    = sys_get_param_float('crystal_basic_income', 20);
  classSupernova::$config->deuterium_basic_income  = sys_get_param_float('deuterium_basic_income', 10);
  classSupernova::$config->energy_basic_income     = sys_get_param_float('energy_basic_income', 0);
  classSupernova::$config->eco_planet_storage_metal = sys_get_param_float('eco_planet_storage_metal', BASE_STORAGE_SIZE);
  classSupernova::$config->eco_planet_storage_crystal = sys_get_param_float('eco_planet_storage_crystal', BASE_STORAGE_SIZE);
  classSupernova::$config->eco_planet_storage_deuterium = sys_get_param_float('eco_planet_storage_deuterium', BASE_STORAGE_SIZE);

  classSupernova::$config->chat_timeout            = sys_get_param_int('chat_timeout', 5);

  classSupernova::$config->game_news_overview      = sys_get_param_int('game_news_overview', 5);
  classSupernova::$config->advGoogleLeftMenuIsOn   = sys_get_param_int('advGoogleLeftMenuIsOn');
  classSupernova::$config->advGoogleLeftMenuCode   = sys_get_param('advGoogleLeftMenuCode');
  classSupernova::$config->debug                   = sys_get_param_int('debug');
  classSupernova::$config->game_counter            = sys_get_param_int('game_counter');
  classSupernova::$config->geoip_whois_url         = sys_get_param_str('geoip_whois_url');

  classSupernova::$config->uni_price_galaxy        = sys_get_param_float('uni_price_galaxy');
  classSupernova::$config->uni_price_system        = sys_get_param_float('uni_price_system');

  classSupernova::$config->user_birthday_gift      = sys_get_param_float('user_birthday_gift');
  classSupernova::$config->user_birthday_range     = sys_get_param_int('user_birthday_range');

  classSupernova::$config->stats_hide_admins       = sys_get_param_int('stats_hide_admins');
  classSupernova::$config->stats_hide_player_list  = sys_get_param_str('stats_hide_player_list');
  classSupernova::$config->stats_hide_pm_link      = sys_get_param_int('stats_hide_pm_link');
  classSupernova::$config->stats_schedule          = sys_get_param_str('stats_schedule');

  classSupernova::$config->empire_mercenary_base_period = sys_get_param_int('empire_mercenary_base_period');
  if(classSupernova::$config->empire_mercenary_temporary != sys_get_param_int('empire_mercenary_temporary')) {
    if(classSupernova::$config->empire_mercenary_temporary) {
      DBStaticUnit::db_unit_list_admin_delete_mercenaries_finished();
    } else {
      DBStaticUnit::db_unit_list_admin_set_mercenaries_expire_time(classSupernova::$config->empire_mercenary_base_period);
    }

    classSupernova::$config->empire_mercenary_temporary = sys_get_param_int('empire_mercenary_temporary');
  }

  classSupernova::$config->db_saveAll();

  $template->assign_var('MESSAGE', classLocale::$lang['adm_opt_saved']);
}

$template->assign_vars(array(
  'ALLOW_BUFFING' => classSupernova::$config->allow_buffing,
  'ALLY_HELP_WEAK' => classSupernova::$config->ally_help_weak,
  'GAME_EMAIL_PM' => classSupernova::$config->game_email_pm,
  'game_mode' => classSupernova::$config->game_mode,
  'game_language' => classSupernova::$config->game_default_language,
  'ECO_SCALE_STORAGE' => classSupernova::$config->eco_scale_storage,
  'USER_VACATION_DISABLE' => classSupernova::$config->user_vacation_disable,
  'ADV_LEFT_MENU' => classSupernova::$config->advGoogleLeftMenuIsOn,
  'GAME_DISABLE' => classSupernova::$config->game_disable,
  'GAME_DEBUG' => classSupernova::$config->debug,
  'GAME_COUNTER' => classSupernova::$config->game_counter,
  'TPL_MINIFIER' => classSupernova::$config->tpl_minifier,
  'EMPIRE_MERCENARY_TEMPORARY' => classSupernova::$config->empire_mercenary_temporary,

  'SERVER_UPDATE_CHECK_AUTO' => classSupernova::$config->server_updater_check_auto,
  'CHECK_DATE' => classSupernova::$config->server_updater_check_last ? date(FMT_DATE_TIME, classSupernova::$config->server_updater_check_last) : 0,
  'CHECK_RESULT' => isset(classLocale::$lang['adm_opt_ver_response'][classSupernova::$config->server_updater_check_result]) ? classLocale::$lang['adm_opt_ver_response'][classSupernova::$config->server_updater_check_result] : classLocale::$lang['adm_opt_ver_response'][SNC_VER_UNKNOWN_RESPONSE],
  'CHECK_CLASS' => isset($sn_version_check_class[classSupernova::$config->server_updater_check_result]) ? $sn_version_check_class[classSupernova::$config->server_updater_check_result] : $sn_version_check_class[SNC_VER_UNKNOWN_RESPONSE],

  'SERVER_UPDATE_ID' => classSupernova::$config->server_updater_id,
  'SERVER_UPDATE_KEY' => classSupernova::$config->server_updater_key,

  'STATS_HIDE_ADMINS' => classSupernova::$config->stats_hide_admins,
  'STATS_HIDE_PM_LINK' => classSupernova::$config->stats_hide_pm_link,
));

foreach(classLocale::$lang['sys_game_disable_reason'] as $id => $name) {
  $template->assign_block_vars('sys_game_disable_reason', array(
    'ID'   => $id,
    'NAME' => $name,
  ));
}

foreach(classLocale::$lang['sys_game_mode'] as $mode_id => $mode_name) {
  $template->assign_block_vars('game_modes', array(
    'ID'   => $mode_id,
    'NAME' => $mode_name,
  ));
}

foreach(classLocale::$lang['adm_opt_ver_response'] as $ver_id => $ver_response) {
  $template->assign_block_vars('ver_response', array(
    'ID'   => $ver_id,
    'NAME' => js_safe_string($ver_response),
  ));
}

$lang_list = lng_get_list();
foreach($lang_list as $lang_id => $lang_data) {
  $template->assign_block_vars('game_languages', array(
    'ID'   => $lang_id,
    'NAME' => "{$lang_data['LANG_NAME_NATIVE']} ({$lang_data['LANG_NAME_ENGLISH']})",
  ));
}

display(parsetemplate($template), classLocale::$lang['adm_opt_title'], false, '', true);
