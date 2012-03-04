<?php

$allow_anonymous = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('admin');

$template = gettemplate('server_info', true);

$template->assign_vars(array(
  'game_build_and_research' => $config->BuildLabWhileRun,
  'USER_VACATION_DISABLE' => $config->user_vacation_disable,
  'ALLOW_BUFFING' => $config->allow_buffing,
  'ALLY_HELP_WEAK' => $config->ally_help_weak,
  'FLEET_BASHING_ATTACKS' => $config->fleet_bashing_attacks,
  'fleet_bashing_interval' => sys_time_human($config->fleet_bashing_interval),
  'fleet_bashing_scope' => sys_time_human($config->fleet_bashing_scope),
  'fleet_bashing_war_delay' => sys_time_human($config->fleet_bashing_war_delay),
  'EMPIRE_MERCENARY_TEMPORARY' => $config->empire_mercenary_temporary,
  'ALI_BONUS_MEMBERS' => isset($sn_module['ali_ally_player']) ? $config->ali_bonus_members : 0,
));

display(parsetemplate($template));

?>