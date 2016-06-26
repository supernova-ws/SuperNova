<?php

$allow_anonymous = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('admin');

$template = gettemplate('server_info', true);

$template->assign_vars(array(
  'game_build_and_research' => classSupernova::$config->BuildLabWhileRun,
  'USER_VACATION_DISABLE' => classSupernova::$config->user_vacation_disable,
  'ALLOW_BUFFING' => classSupernova::$config->allow_buffing,
  'ALLY_HELP_WEAK' => classSupernova::$config->ally_help_weak,
  'FLEET_BASHING_ATTACKS' => classSupernova::$config->fleet_bashing_attacks,
  'fleet_bashing_interval' => sys_time_human(classSupernova::$config->fleet_bashing_interval),
  'fleet_bashing_scope' => sys_time_human(classSupernova::$config->fleet_bashing_scope),
  'fleet_bashing_war_delay' => sys_time_human(classSupernova::$config->fleet_bashing_war_delay),
  'EMPIRE_MERCENARY_TEMPORARY' => classSupernova::$config->empire_mercenary_temporary,
  'ALI_BONUS_MEMBERS' => isset($sn_module['ali_ally_player']) ? classSupernova::$config->ali_bonus_members : 0,

  'PLAYER_MAX_COLONIES' => classSupernova::$config->player_max_colonies,

  'GAME_MULTIACCOUNT_ENABLED' => classSupernova::$config->game_multiaccount_enabled,

  'GAME_SPEED' => get_game_speed(),
  'GAME_SPEED_PLAIN' => get_game_speed(true),
  'FLEET_SPEED' => flt_server_flight_speed_multiplier(),
  'FLEET_SPEED_PLAIN' => flt_server_flight_speed_multiplier(true),
  'RESOURCE_MULTIPLIER' => game_resource_multiplier(),
  'RESOURCE_MULTIPLIER_PLAIN' => game_resource_multiplier(true),
));

display(parsetemplate($template));
