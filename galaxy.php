<?php

/**
 * galaxy.php
 *
 * Galaxy view
 *
 * History version
 *   2.1 - 'galaxy' table replaced with 'planets' by Gorlum for http://supernova.ws
 *   2.0 - Rewrote by Gorlum for http://supernova.ws
 *     [+] Template-related parts cutted from PHP and moved to TPL-code
 *   1.4 - Security checks & tests by Gorlum for http://supernova.ws
 *   1.3 - 2eme Nettoyage Chlorel ... Mise en fonction et debuging complet
 *   1.2 - 1er Nettoyage Chlorel ...
 *   1.1 - Modified by -MoF- (UGamela germany)
 *   1.0 - Created by Perberos
 * @copyright 2008 by Chlorel for XNova
 */

use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

global $config, $template_result, $planetrow, $debug, $lang;

lng_include('universe');
lng_include('stat');

$mode = sys_get_param_str('mode');
$scan = sys_get_param_str('scan');
$uni_galaxy = sys_get_param_int('galaxy', $planetrow['galaxy']);
$uni_system = sys_get_param_int('system', $planetrow['system']);
$planet = sys_get_param_int('planet', $planetrow['planet']);

if ($mode == 'name') {
  require_once('includes/includes/uni_rename.php');
}

require_once('includes/includes/flt_functions.php');

$CurrentPlanetID = sys_get_param_id('current');
$POST_galaxyLeft = sys_get_param_str('galaxyLeft');
$POST_galaxyRight = sys_get_param_str('galaxyRight');
$POST_systemLeft = sys_get_param_str('systemLeft');
$POST_systemRight = sys_get_param_str('systemRight');

$fleetmax = GetMaxFleets($user);
$CurrentPlID = $planetrow['id'];
$CurrentMIP = mrc_get_level($user, $planetrow, UNIT_DEF_MISSILE_INTERPLANET, false, true);
$HavePhalanx = mrc_get_level($user, $planetrow, STRUC_MOON_PHALANX);
$CurrentSystem = $planetrow['system'];
$CurrentGalaxy = $planetrow['galaxy'];

//$maxfleet       = doquery("SELECT COUNT(*) AS flying_fleet_count FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", '', true);
//$maxfleet_count = $maxfleet['flying_fleet_count'];
$flying_fleet_count = DbFleetStatic::fleet_count_flying($user['id']);

if ($mode == 1) {
} elseif ($mode == 2 || $mode == 3) {
  $planet = $planetrow['planet'];
} else {
  $uni_galaxy = $planetrow['galaxy'];
  $uni_system = $planetrow['system'];
  $planet = $planetrow['planet'];
}

$uni_galaxy = $uni_galaxy < 1 ? 1 : ($uni_galaxy > SN::$config->game_maxGalaxy ? SN::$config->game_maxGalaxy : $uni_galaxy);
$uni_system = $uni_system < 1 ? 1 : ($uni_system > SN::$config->game_maxSystem ? SN::$config->game_maxSystem : $uni_system);
$planet = $planet < 1 ? 1 : ($planet > SN::$config->game_maxPlanet + 1 ? SN::$config->game_maxPlanet + 1 : $planet);

$planetcount = 0;
$lunacount = 0;
$CurrentRC = $planetrow['recycler'];
$cached = array('users' => array(), 'allies' => array());


$template = SnTemplate::gettemplate('universe', true);

$CurrentPoints = $user['total_points'];

$MissileRange = flt_get_missile_range($user);
$PhalanxRange = GetPhalanxRange($HavePhalanx);

$planet_precache_query = DBStaticPlanet::db_planet_list_in_system($uni_galaxy, $uni_system);
if (!empty($planet_precache_query)) {
  foreach ($planet_precache_query as $planet_row) {
    if (CheckAbandonPlanetState($planet_row)) {
      continue;
    }
    $planet_list[$planet_row['planet']][$planet_row['planet_type']] = $planet_row;
  }
}

$system_fleet_list = DbFleetStatic::fleet_list_by_planet_coords($uni_galaxy, $uni_system);
foreach ($system_fleet_list as $fleet_row) {
  $fleet_planet = $fleet_row['fleet_mess'] == 0 ? $fleet_row['fleet_end_planet'] : $fleet_row['fleet_start_planet'];
  $fleet_type = $fleet_row['fleet_mess'] == 0 ? $fleet_row['fleet_end_type'] : $fleet_row['fleet_start_type'];
  $fleet_list[$fleet_planet][$fleet_type][] = $fleet_row;
}

$time_now_parsed = getdate(SN_TIME_NOW);

$recycler_info = array();
$planet_recyclers_orbiting = 0;
$recyclers_fleet = array();
foreach (sn_get_groups('flt_recyclers') as $recycler_id) {
  $recycler_info[$recycler_id] = get_ship_data($recycler_id, $user);
  $recyclers_fleet[$recycler_id] = mrc_get_level($user, $planetrow, $recycler_id);
  $planet_recyclers_orbiting += $recyclers_fleet[$recycler_id];
}

$user_skip_list = sys_stat_get_user_skip_list();
$fleetsTotalIncomeOwn = array();
$config_game_max_planet = SN::$config->game_maxPlanet + 1;
for ($Planet = 1; $Planet < $config_game_max_planet; $Planet++) {
  unset($uni_galaxyRowPlanet);
  unset($uni_galaxyRowMoon);
  unset($uni_galaxyRowUser);
  unset($uni_galaxyRowAlly);

  $templatizedMoon = [];

  if (
    empty($planet_list[$Planet][PT_PLANET]['id'])
    ||
    !($uni_galaxyRowPlanet = $planet_list[$Planet][PT_PLANET])
    ||
    (!empty($uni_galaxyRowPlanet['destruyed']) && CheckAbandonPlanetState($uni_galaxyRowPlanet))
  ) {
    $template->assign_block_vars('galaxyrow', ['PLANET_NUM' => $Planet,]);
    continue;
  }

  $planet_fleet_id = 0;
  if (!isset($cached['users'][$uni_galaxyRowPlanet['id_owner']])) {
    $cached['users'][$uni_galaxyRowPlanet['id_owner']] = db_user_by_id($uni_galaxyRowPlanet['id_owner']);
  }
  $uni_galaxyRowUser = $cached['users'][$uni_galaxyRowPlanet['id_owner']];

  // Checking if there is planet owner record
  if (empty($uni_galaxyRowUser)) {
    // If there is planet owner but planet not destroyed - marking as destroyed
    if ($uni_galaxyRowPlanet['id_owner'] && empty($uni_galaxyRowPlanet['destruyed'])) {
      $debug->warning("Planet '{$uni_galaxyRowPlanet['name']}' [{$uni_galaxy}:{$uni_system}:{$Planet}] has no owner!", 'Userless planet', 503);
      $uni_galaxyRowPlanet['destruyed'] = SN_TIME_NOW + 60 * 60 * 24;
      $uni_galaxyRowPlanet['id_owner'] = 0;
      DBStaticPlanet::db_planet_set_by_id($uni_galaxyRowPlanet['id'], "id_owner = 0, destruyed = {$uni_galaxyRowPlanet['destruyed']}");
    }
  } else {
    if ($uni_galaxyRowUser['ally_id'] && !isset($cached['allies'][$uni_galaxyRowUser['ally_id']])) {
      /** @noinspection SqlResolve */
      $cached['allies'][$uni_galaxyRowUser['ally_id']] = doquery("SELECT * FROM `{{alliance}}` WHERE `id` = '{$uni_galaxyRowUser['ally_id']}';", '', true);
    }
  }

  $planetcount++;

  $fleets_to_planet = flt_get_fleets_to_planet(false, $fleet_list[$Planet][PT_PLANET]);
  if (!empty($fleets_to_planet['own']['count'])) {
    $planet_fleet_id = getUniqueFleetId($planet_list[$Planet][PT_PLANET]);
    $fleetsTotalIncomeOwn[$planet_list[$Planet][PT_PLANET]['id']] = tpl_parse_fleet_sn($fleets_to_planet['own']['total'], $planet_fleet_id);
  }

  if (
    !empty($planet_list[$Planet][PT_MOON])
    &&
    ($uni_galaxyRowMoon = $planet_list[$Planet][PT_MOON])
    &&
    (
      empty($uni_galaxyRowMoon['destruyed'])
      ||
      !CheckAbandonPlanetState($uni_galaxyRowMoon)
    )
  ) {
    $fleets_to_planet = flt_get_fleets_to_planet(false, $fleet_list[$Planet][PT_MOON]);
    if (!empty($fleets_to_planet['own']['count'])) {
      $moon_fleet_id = getUniqueFleetId($uni_galaxyRowMoon);
      $fleetsTotalIncomeOwn[$uni_galaxyRowMoon['id']] = tpl_parse_fleet_sn($fleets_to_planet['own']['total'], $moon_fleet_id);
    } else {
      $moon_fleet_id = 0;
    }
    $templatizedMoon = [
      'MOON_NAME_JS'  => js_safe_string($uni_galaxyRowMoon['name']),
      'MOON_IMAGE'    => $uni_galaxyRowMoon['image'],
      'MOON_DIAMETER' => number_format($uni_galaxyRowMoon['diameter'], 0, '', '.'),
      'MOON_TEMP'     => number_format($uni_galaxyRowMoon['temp_min'], 0, '', '.'),
      'MOON_FLEET_ID' => $moon_fleet_id,
    ];
  }

  $debrisTemplatized = [];
  if ($debrisTotal = $uni_galaxyRowPlanet['debris_metal'] + $uni_galaxyRowPlanet['debris_crystal']) {
    $recyclers_incoming_capacity = 0;
    if ($fleet_list[$Planet][PT_DEBRIS]) {
      foreach ($fleet_list[$Planet][PT_DEBRIS] as $fleet_row) {
        if ($fleet_row['fleet_owner'] == $user['id']) {
          $fleet_data = sys_unit_str2arr($fleet_row['fleet_array']);
          foreach ($recycler_info as $recycler_id => $recycler_data) {
            $recyclers_incoming_capacity += $fleet_data[$recycler_id] * $recycler_data['capacity'];
          }
        }
      }
    }

    $debris_reserved = $uni_galaxyRowPlanet['debris_reserved'] = $recyclers_incoming_capacity;
    $debris_reserved_percent = min(100, floor($debris_reserved / $debrisTotal * 100));

    $debris_to_gather = max(0, $debrisTotal - $recyclers_incoming_capacity);

    $recyclers_fleet_data = flt_calculate_fleet_to_transport($recyclers_fleet, $debris_to_gather, $planetrow, $uni_galaxyRowPlanet);

    $debrisTemplatized = [
      'DEBRIS'         => $debrisTotal,
      'DEBRIS_METAL'   => $uni_galaxyRowPlanet['debris_metal'],
      'DEBRIS_CRYSTAL' => $uni_galaxyRowPlanet['debris_crystal'],

      'DEBRIS_RESERVED'             => $debris_reserved,
      'DEBRIS_RESERVED_PERCENT'     => $debris_reserved_percent,
      'DEBRIS_WILL_GATHER'          => $debris_will_gather = max(0, min($recyclers_fleet_data['capacity'], $debris_to_gather)),
      'DEBRIS_WILL_GATHER_PERCENT'  => $debris_to_gather
        ? floor($debris_will_gather / $debris_to_gather * (100 - $debris_reserved_percent))
        : 0,
      'DEBRIS_GATHER_TOTAL'         => $debris_gather_total = max(0, $debris_will_gather + $debris_reserved),
      'DEBRIS_GATHER_TOTAL_PERCENT' => min(100, floor($debris_gather_total / $debrisTotal * 100)),
    ];
  }

  $RowUserPoints = $uni_galaxyRowUser['total_points'];
  $birthday_array = $uni_galaxyRowUser['user_birthday'] ? date_parse($uni_galaxyRowUser['user_birthday']) : array();
  $playerSecondsInactive = SN_TIME_NOW - $uni_galaxyRowUser['onlinetime'];
  $user_activity_days = floor(($playerSecondsInactive) / (60 * 60 * 24));

  $templatizedPlanet = array_merge([
    'PLANET_ID'        => $uni_galaxyRowPlanet['id'],
    'PLANET_NUM'       => $Planet,
    'PLANET_NAME'      => $uni_galaxyRowPlanet['name'],
    'PLANET_NAME_JS'   => js_safe_string($uni_galaxyRowPlanet['name']),
    'PLANET_DESTROYED' => $uni_galaxyRowPlanet["destruyed"],
    'PLANET_TYPE'      => $uni_galaxyRowPlanet["planet_type"],
    'PLANET_ACTIVITY'  => floor((SN_TIME_NOW - $uni_galaxyRowPlanet['last_update']) / 60),
    'PLANET_IMAGE'     => $uni_galaxyRowPlanet['image'],
    'PLANET_FLEET_ID'  => $planet_fleet_id,
    'PLANET_DIAMETER'  => number_format($uni_galaxyRowPlanet['diameter'], 0, '', '.'),

    'IS_CAPITAL'      => $uni_galaxyRowUser['id_planet'] == $uni_galaxyRowPlanet['id'],

    'USER_ID'         => $uni_galaxyRowUser['id'],
    'USER_NAME'       => $renderedNick = player_nick_render_to_html($uni_galaxyRowUser, ['icons' => true,]),
    'USER_NAME_JS'    => js_safe_string($renderedNick),
    'USER_RANK'       => in_array($uni_galaxyRowUser['id'], $user_skip_list) ? '-' : $uni_galaxyRowUser['total_rank'],
    'USER_BANNED'     => $uni_galaxyRowUser['banaday'],
    'USER_VACATION'   => $uni_galaxyRowUser['vacation'],
    'USER_ACTIVITY'   => $user_activity_days,
    'USER_ATTACKABLE' => $playerSecondsInactive >= PLAYER_INACTIVE_TIMEOUT,
    'USER_INACTIVE'   => $playerSecondsInactive >= PLAYER_INACTIVE_TIMEOUT_LONG,
    'USER_PROTECTED'  => SN::$gc->general->playerIsNoobByPoints($RowUserPoints),
    'USER_NOOB'       => SN::$gc->general->playerIs1stStrongerThen2nd($CurrentPoints, $RowUserPoints),
    'USER_STRONG'     => SN::$gc->general->playerIs1stStrongerThen2nd($RowUserPoints, $CurrentPoints),
    'USER_AUTH'       => $uni_galaxyRowUser['authlevel'],
    'USER_ADMIN'      => $lang['user_level_shortcut'][$uni_galaxyRowUser['authlevel']],
    'USER_BIRTHDAY'   => $birthday_array['month'] == $time_now_parsed['mon'] && $birthday_array['day'] == $time_now_parsed['mday'] ? date(FMT_DATE, SN_TIME_NOW) : 0,

    'ALLY_ID'  => $uni_galaxyRowUser['ally_id'],
    'ALLY_TAG' => $uni_galaxyRowUser['ally_tag'],
  ], $templatizedMoon, $debrisTemplatized);
  $template->assign_block_vars('galaxyrow', $templatizedPlanet);
}

tpl_assign_fleet($template, $fleetsTotalIncomeOwn);

foreach (sn_get_groups('defense_active') as $unit_id) {
  $template->assign_block_vars('defense_active', array(
    'ID'   => $unit_id,
    'NAME' => $lang['tech'][$unit_id],
  ));
}

foreach ($cached['users'] as $PlanetUser) {
  if (!$PlanetUser) {
    continue;
  }

  $user_ally = $cached['allies'][$PlanetUser['ally_id']];
  if (isset($user_ally)) {
    if ($PlanetUser['id'] == $user_ally['ally_owner']) {
      $user_rank_title = $user_ally['ally_owner_range'];
    } else {
      $ally_ranks = explode(';', $user_ally['ranklist']);
      list($user_rank_title) = explode(',', $ally_ranks[$PlanetUser['ally_rank_id']]);
    }
  } else {
    $user_rank_title = '';
  }

  $birthday_array = $PlanetUser['user_birthday'] ? date_parse($PlanetUser['user_birthday']) : array();
  $template->assign_block_vars('users', array(
    'ID'         => $PlanetUser['id'],
    'NAME'       => $renderedNick = player_nick_render_to_html($PlanetUser, true),
    'NAME_JS'    => js_safe_string($renderedNick),
    'RANK'       => in_array($PlanetUser['id'], $user_skip_list) ? '-' : $PlanetUser['total_rank'],
    'AVATAR'     => $PlanetUser['avatar'],
    'ALLY_ID'    => $PlanetUser['ally_id'],
    'ALLY_TAG'   => js_safe_string($user_ally['ally_tag']),
    'ALLY_TITLE' => str_replace(' ', '&nbsp', js_safe_string($user_rank_title)),
  ));
}

foreach ($cached['allies'] as $PlanetAlly) {
  if ($PlanetAlly) {
    $template->assign_block_vars('alliances', array(
      'ID'      => $PlanetAlly['id'],
      'NAME_JS' => js_safe_string($PlanetAlly['ally_name']),
      'MEMBERS' => $PlanetAlly['ally_members'],
      'URL'     => $PlanetAlly['ally_web'],
      'RANK'    => $PlanetAlly['total_rank'],
      'AVATAR'  => $PlanetAlly['ally_image'],
    ));
  }
}

$is_missile = SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_MISSILE] && ($CurrentMIP > 0) && ($uni_galaxy == $CurrentGalaxy) && ($uni_system >= $CurrentSystem - $MissileRange) && ($uni_system <= $CurrentSystem + $MissileRange);
$colspan = SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_SPYING] + SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PM] + SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_BUDDY] + $is_missile;

/** @noinspection SqlResolve */
$ally_count = doquery("SELECT COUNT(*) AS ally_count FROM `{{alliance}}`;", '', true);
/** @noinspection SqlResolve */
$galaxy_name = doquery("select `universe_name` from `{{universe}}` where `universe_galaxy` = {$uni_galaxy} and `universe_system` = 0 limit 1;", true);
/** @noinspection SqlResolve */
$system_name = doquery("select `universe_name` from `{{universe}}` where `universe_galaxy` = {$uni_galaxy} and `universe_system` = {$uni_system} limit 1;", true);

$template->assign_vars(array(
//     'rows'                => $Result,
    'userCount'             => SN::$config->users_amount,
    'ALLY_COUNT'            => $ally_count['ally_count'],
    'PLANET_EXPEDITION'     => SN::$config->game_maxPlanet + 1,
    'curPlanetID'           => $planetrow['id'],
    'curPlanetG'            => $planetrow['galaxy'],
    'curPlanetS'            => $planetrow['system'],
    'curPlanetP'            => $planetrow['planet'],
    'curPlanetPT'           => $planetrow['planet_type'],
    'deathStars'            => mrc_get_level($user, $planetrow, SHIP_HUGE_DEATH_STAR, false, true),
    'galaxy'                => $uni_galaxy,
    'system'                => $uni_system,
    'planet'                => $planet,
    'MIPs'                  => round($CurrentMIP),
    'MODE'                  => $mode,
    'planets'               => $planetcount,
    'SPs'                   => HelperString::numberFloorAndFormat(mrc_get_level($user, $planetrow, SHIP_SPY, false, true)),
    'SHOW_ADMIN'            => SHOW_ADMIN,
    'fleet_count'           => $flying_fleet_count,
    'fleet_max'             => $fleetmax,
    'ALLY_ID'               => $user['ally_id'],
    'USER_ID'               => $user['id'],
    'ACT_SPIO'              => SN::$user_options[PLAYER_OPTION_FLEET_SPY_DEFAULT],
    'ACT_SPY'               => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_SPYING],
    'ACT_WRITE'             => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PM],
    'ACT_STATISTICS'        => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_STATS],
    'ACT_INFO'              => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PROFILE],
    'ACT_FRIEND'            => SN::$user_options[PLAYER_OPTION_UNIVERSE_ICON_BUDDY],
    'opt_uni_tooltip_time'  => SN::$user_options[PLAYER_OPTION_TOOLTIP_DELAY],
    'opt_uni_avatar_user'   => $user['opt_uni_avatar_user'],
    'opt_uni_avatar_ally'   => $user['opt_uni_avatar_ally'],
    'ACT_MISSILE'           => $is_missile,
    'PLANET_PHALANX'        => $HavePhalanx && $uni_galaxy == $CurrentGalaxy && $uni_system >= $CurrentSystem - $PhalanxRange && $uni_system <= $CurrentSystem + $PhalanxRange,
    'PAGE_HINT'             => $lang['gal_sys_hint'],
    'PLANET_RECYCLERS'      => $planet_recyclers_orbiting,
    'PLANET_RECYCLERS_TEXT' => HelperString::numberFloorAndFormat($planet_recyclers_orbiting),
    'GALAXY_NAME'           => $galaxy_name['universe_name'],
    'SYSTEM_NAME'           => $system_name['universe_name'],
    'COL_SPAN'              => $colspan + 9,
    'COL_SPAN_PLUS'         => $colspan + 3,

    'COL_SPAN_NEW'          => $colspan + 4,
    'COL_SPAN_NEW_COLONIZE' => $colspan - 2,

    'PLAYER_OPTION_UNIVERSE_OLD'              => SN::$user_options[PLAYER_OPTION_UNIVERSE_OLD],
    'PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE' => SN::$user_options[PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE],
  )
);

if ($scan) {
  $template_result = array_merge($template_result, array(
    'GLOBAL_DISPLAY_MENU'   => false,
    'GLOBAL_DISPLAY_NAVBAR' => false,
    'UNIVERSE_SCAN_MODE'    => true,
  ));

  $template->assign_vars(array(
    'UNIVERSE_SCAN_MODE' => true,
  ));
}

SnTemplate::display($template, $lang['sys_universe']);
