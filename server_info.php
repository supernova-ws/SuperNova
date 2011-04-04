<?php

$allow_anonymous = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('admin');

$template = gettemplate('server_info', true);

$template->assign_vars(array(
  'game_speed' => get_game_speed(),
  'fleet_speed' => get_fleet_speed(),
  'game_build_and_research' => $config->BuildLabWhileRun,
  'USER_VACATION_DISABLE' => $config->user_vacation_disable,
  'DB_VERSION' => DB_VERSION,
  'SN_VERSION' => SN_VERSION,
));

display(parsetemplate($template));

?>