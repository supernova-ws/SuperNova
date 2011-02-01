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

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($user['authlevel'] < 3)
{
  message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
}

includeLang('admin');

$template = gettemplate('admin/settings', true);

if ($_POST['save'])
{
  $config->game_name               = $_POST['game_name'];
  $config->game_mode               = intval($_POST['game_mode']);
  $config->game_speed              = floatval( $_POST['game_speed'] );
  $config->fleet_speed             = floatval( $_POST['fleet_speed'] );
  $config->resource_multiplier     = floatval( $_POST['resource_multiplier'] );
  $config->user_vacation_disable   = $_POST['user_vacation_disable'] ? 1 : 0;
  $config->url_forum               = $_POST['url_forum'];
  $config->url_rules               = $_POST['url_rules'];
  $config->url_dark_matter         = $_POST['url_dark_matter'];
  $config->game_disable            = $_POST['game_disable'] ? 1 : 0;
  $config->game_disable_reason     = strip_tags($_POST['game_disable_reason']);

  $config->game_default_language   = $_POST['game_default_language'];
  $config->game_default_skin       = $_POST['game_default_skin'];
  $config->game_default_template   = $_POST['game_default_template'];

  $config->game_maxGalaxy          = intval($_POST['game_maxGalaxy']) ? intval($_POST['game_maxGalaxy']) : 5;
  $config->game_maxSystem          = intval($_POST['game_maxSystem']) ? intval($_POST['game_maxSystem']) : 199;
  $config->game_maxPlanet          = intval($_POST['game_maxPlanet']) ? intval($_POST['game_maxPlanet']) : 15;

  $config->player_max_colonies     = intval($_POST['player_max_colonies']);

  $config->rpg_exchange_metal      = intval($_POST['rpg_exchange_metal']) ? intval($_POST['rpg_exchange_metal']) : 1;
  $config->rpg_exchange_crystal    = intval($_POST['rpg_exchange_crystal']) ? intval($_POST['rpg_exchange_crystal']) : 2;
  $config->rpg_exchange_deuterium  = intval($_POST['rpg_exchange_deuterium']) ? intval($_POST['rpg_exchange_deuterium']) : 4;
  $config->rpg_exchange_darkMatter = intval($_POST['rpg_exchange_darkMatter']) ? intval($_POST['rpg_exchange_darkMatter']) : 100000;

  $config->initial_fields          = intval($_POST['initial_fields']);
  $config->metal_basic_income      = intval($_POST['metal_basic_income']);
  $config->crystal_basic_income    = intval($_POST['crystal_basic_income']);
  $config->deuterium_basic_income  = intval($_POST['deuterium_basic_income']);
  $config->energy_basic_income     = intval($_POST['energy_basic_income']);

  $config->chat_timeout            = intval($_POST['chat_timeout']);

  $config->game_news_overview      = intval($_POST['game_news_overview']);
  $config->advGoogleLeftMenuIsOn   = $_POST['advGoogleLeftMenuIsOn'] ? 1 : 0;
  $config->advGoogleLeftMenuCode   = $_POST['advGoogleLeftMenuCode'];
  $config->debug                   = $_POST['debug'] ? 1 : 0;
  $config->game_counter            = $_POST['game_counter'] ? 1 : 0;

  $config->db_saveAll();

  $template->assign_var('MESSAGE', $lang['adm_opt_saved']);
}

$template->assign_vars(array(
  'game_disable' => $config->game_disable ? 'checked' : '',
  'advGoogleLeftMenuIsOn' => $config->advGoogleLeftMenuIsOn ? 'checked' : '',
  'debug' => $config->debug ? 'checked' : '',
  'game_counter' => $config->game_counter ? 'checked' : '',
  'user_vacation_disable' => $config->user_vacation_disable ? 'checked' : '',
  'game_mode' => $config->game_mode,
));

foreach($lang['sys_game_mode'] as $mode_id => $mode_name)
{
  $template->assign_block_vars('game_modes', array(
    'ID'   => $mode_id,
    'NAME' => $mode_name,
  ));
}

display(parsetemplate($template), $lang['adm_opt_title'], false, '', true);
?>