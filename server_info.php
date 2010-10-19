<?php

$InLogin = true;

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

includeLang('admin/settings');

$template = gettemplate('server_info', true);

$template->assign_vars(array(
  'game_speed' => get_game_speed(),
  'fleet_speed' => get_fleet_speed(),

/*
    'Defs_Cdr'               => 30,
    'Fleet_Cdr'              => 30,

    'noobprotection'         => 1,
    'noobprotectionmulti'    => 5,
    'noobprotectiontime'     => 5000,

    'urlaubs_modus_erz'      => 0,
    'users_amount'           => 0,

    'game_user_changename' => 0, // Is user allowed to change name after registration?

    //Roleplay system
    'rpg_officer'       =>  3, // Cost per officer level
    'rpg_bonus_divisor' => 10, // Amount of DM referral shoud get for partner have 1 DM bonus

    // Black Market - Scraper rates for ship pre resource
    'rpg_scrape_metal'     => 0.75,
    'rpg_scrape_crystal'   => 0.50,
    'rpg_scrape_deuterium' => 0.25,
*/
));

display(parsetemplate($template, $parse)/*, "{$lang['sys_universe']} &quot;{$config->game_name}&quot;"*/);

?>