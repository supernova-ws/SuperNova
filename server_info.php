<?php

$allow_anonymous = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('admin');

$template = SnTemplate::gettemplate('server_info', true);

$template->assign_vars(array(
  'game_build_and_research' => SN::$config->BuildLabWhileRun,
  'USER_VACATION_DISABLE' => SN::$config->user_vacation_disable,
  'ALLOW_BUFFING' => SN::$config->allow_buffing,
  'ALLY_HELP_WEAK' => SN::$config->ally_help_weak,
  'FLEET_BASHING_ATTACKS' => SN::$config->fleet_bashing_attacks,
  'fleet_bashing_interval' => sys_time_human(SN::$config->fleet_bashing_interval),
  'fleet_bashing_scope' => sys_time_human(SN::$config->fleet_bashing_scope),
  'fleet_bashing_war_delay' => sys_time_human(SN::$config->fleet_bashing_war_delay),
  'EMPIRE_MERCENARY_TEMPORARY' => SN::$config->empire_mercenary_temporary,
  'ALI_BONUS_MEMBERS' => !empty(SN::$gc->modules->getModule('ali_ally_player')) ? SN::$config->ali_bonus_members : 0,

  'PLAYER_MAX_COLONIES' => SN::$config->player_max_colonies,

  'GAME_MULTIACCOUNT_ENABLED' => SN::$config->game_multiaccount_enabled,

  'GAME_SPEED' => get_game_speed(),
  'GAME_SPEED_PLAIN' => get_game_speed(true),
  'FLEET_SPEED' => flt_server_flight_speed_multiplier(),
  'FLEET_SPEED_PLAIN' => flt_server_flight_speed_multiplier(true),
  'RESOURCE_MULTIPLIER' => game_resource_multiplier(),
  'RESOURCE_MULTIPLIER_PLAIN' => game_resource_multiplier(true),

  'DB_PATCH_VERSION' => dbPatchGetCurrent(),
));

SnTemplate::display($template);
