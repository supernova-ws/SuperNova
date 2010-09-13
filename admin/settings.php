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

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('admin/settings');

if ( $user['authlevel'] < 3 )
{
  AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}

$template                         = gettemplate('admin/admin_settings', true);

if ($_POST['save'])
{
  $config->game_name              = $_POST['game_name'];
  $config->game_speed             = floatval( $_POST['game_speed'] );
  $config->fleet_speed            = floatval( $_POST['fleet_speed'] );
  $config->resource_multiplier    = floatval( $_POST['resource_multiplier'] );
  $config->urlaubs_modus_erz      = $_POST['urlaubs_modus_erz'] ? 1 : 0;
  $config->forum_url              = $_POST['forum_url'];
  $config->game_disable           = $_POST['game_disable'] ? 1 : 0;
  $config->game_disable_reason    = $_POST['game_disable_reason'];

  $config->initial_fields         = intval($_POST['initial_fields']);
  $config->metal_basic_income     = intval($_POST['metal_basic_income']);
  $config->crystal_basic_income   = intval($_POST['crystal_basic_income']);
  $config->deuterium_basic_income = intval($_POST['deuterium_basic_income']);
  $config->energy_basic_income    = intval($_POST['energy_basic_income']);

  $config->game_news_overview     = intval($_POST['game_news_overview']);
  $config->advGoogleLeftMenuIsOn = $_POST['advGoogleLeftMenuIsOn'] ? 1 : 0;
  $config->advGoogleLeftMenuCode = $_POST['advGoogleLeftMenuCode'];
  $config->debug = $_POST['debug'] ? 1 : 0;

  $config->db_saveAll();

  $template->assign_var('MESSAGE', $lang['adm_opt_saved']);
}

$template->assign_vars(array(
  'game_disable' => $config->game_disable ? 'checked' : '',
  'advGoogleLeftMenuIsOn' => $config->advGoogleLeftMenuIsOn ? 'checked' : '',
  'debug' => $config->debug ? 'checked' : '',
  'urlaubs_modus_erz' => $config->urlaubs_modus_erz ? 'checked' : '',
));

display(parsetemplate($template), $lang['adm_opt_title'], false, '', true);
?>