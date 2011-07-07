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

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$template = gettemplate('admin/settings', true);

if(sys_get_param('save'))
{
  $config->game_name               = sys_get_param_str_raw('game_name');
  $config->game_mode               = sys_get_param_int('game_mode');
  $config->game_speed              = sys_get_param_float('game_speed', 1);
  $config->fleet_speed             = sys_get_param_float('fleet_speed', 1);
  $config->resource_multiplier     = sys_get_param_float('resource_multiplier', 1);
  $config->user_vacation_disable   = sys_get_param_int('user_vacation_disable', 0);
  $config->url_faq                 = sys_get_param_str_raw('url_faq');
  $config->url_forum               = sys_get_param_str_raw('url_forum');
  $config->url_rules               = sys_get_param_str_raw('url_rules');
  $config->url_dark_matter         = sys_get_param_str_raw('url_dark_matter');
  $config->game_disable            = sys_get_param_int('game_disable');
  $config->game_disable_reason     = sys_get_param_str_raw('game_disable_reason');

  $config->game_default_language   = sys_get_param_str_raw('game_default_language', DEFAULT_LANG);
  $config->game_default_skin       = sys_get_param_str_raw('game_default_skin', DEFAULT_SKINPATH);
  $config->game_default_template   = sys_get_param_str_raw('game_default_template', TEMPLATE_NAME);

  $config->game_maxGalaxy          = sys_get_param_int('game_maxGalaxy', 5);
  $config->game_maxSystem          = sys_get_param_int('game_maxSystem', 199);
  $config->game_maxPlanet          = sys_get_param_int('game_maxPlanet', 15);

  $config->player_max_colonies     = sys_get_param_int('player_max_colonies', 9);

  $config->fleet_bashing_attacks   = sys_get_param_int('fleet_bashing_attacks', 3);
  $config->fleet_bashing_interval  = sys_get_param_int('fleet_bashing_interval', 30 * 60);
  $config->fleet_bashing_scope     = sys_get_param_int('fleet_bashing_scope', 24 * 60 * 60);
  $config->fleet_bashing_war_delay = sys_get_param_int('fleet_bashing_war_delay', 12 * 60 * 60);
  $config->fleet_bashing_waves     = sys_get_param_int('fleet_bashing_waves', 3);

  $config->allow_buffing           = sys_get_param_int('allow_buffing');
  $config->ally_help_weak          = sys_get_param_int('ally_help_weak');
  $config->game_email_pm           = sys_get_param_int('game_email_pm');

  $config->rpg_exchange_metal      = sys_get_param_int('rpg_exchange_metal', 1);
  $config->rpg_exchange_crystal    = sys_get_param_int('rpg_exchange_crystal', 2);
  $config->rpg_exchange_deuterium  = sys_get_param_int('rpg_exchange_deuterium', 4);
  $config->rpg_exchange_darkMatter = sys_get_param_int('rpg_exchange_darkMatter', 1000);

  $config->initial_fields          = sys_get_param_int('initial_fields', 200);
  $config->metal_basic_income      = sys_get_param_int('metal_basic_income', 40);
  $config->crystal_basic_income    = sys_get_param_int('crystal_basic_income', 20);
  $config->deuterium_basic_income  = sys_get_param_int('deuterium_basic_income', 10);
  $config->energy_basic_income     = sys_get_param_int('energy_basic_income');

  $config->chat_timeout            = sys_get_param_int('chat_timeout', 5);

  $config->game_news_overview      = sys_get_param_int('game_news_overview', 5);
  $config->advGoogleLeftMenuIsOn   = sys_get_param_int('advGoogleLeftMenuIsOn');
  $config->advGoogleLeftMenuCode   = sys_get_param('advGoogleLeftMenuCode');
  $config->debug                   = sys_get_param_int('debug');
  $config->game_counter            = sys_get_param_int('game_counter');

  $config->db_saveAll();

  $template->assign_var('MESSAGE', $lang['adm_opt_saved']);
}

$template->assign_vars(array(
  'game_disable' => $config->game_disable ? 'checked' : '',
  'advGoogleLeftMenuIsOn' => $config->advGoogleLeftMenuIsOn ? 'checked' : '',
  'debug' => $config->debug ? 'checked' : '',
  'game_counter' => $config->game_counter ? 'checked' : '',
  'user_vacation_disable' => $config->user_vacation_disable ? 'checked' : '',
  'ALLOW_BUFFING' => $config->allow_buffing,
  'ALLY_HELP_WEAK' => $config->ally_help_weak,
  'GAME_EMAIL_PM' => $config->game_email_pm,
  'game_mode' => $config->game_mode,
  'game_language' => $config->game_default_language,
));

foreach($lang['sys_game_mode'] as $mode_id => $mode_name)
{
  $template->assign_block_vars('game_modes', array(
    'ID'   => $mode_id,
    'NAME' => $mode_name,
  ));
}

$lang_list = lng_get_list();
foreach($lang_list as $lang_id => $lang_data)
{
  $template->assign_block_vars('game_languages', array(
    'ID'   => $lang_id,
    'NAME' => "{$lang_data['LANG_NAME_NATIVE']} ({$lang_data['LANG_NAME_ENGLISH']})",
//    'SELECTED' => $lang_id == $config->game_default_language ? 'SELECTED' : '',
  ));
}

display(parsetemplate($template), $lang['adm_opt_title'], false, '', true);

?>
