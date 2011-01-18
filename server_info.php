<?php

$allow_anonymous = true;
$skip_ban_check = true;

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

includeLang('admin');

$template = gettemplate('server_info', true);

$template->assign_vars(array(
  'game_speed' => get_game_speed(),
  'fleet_speed' => get_fleet_speed(),
  'game_build_and_research' => $config->BuildLabWhileRun,
  'USER_VACATION_DISABLE' => $config->user_vacation_disable,
));

display(parsetemplate($template));

?>