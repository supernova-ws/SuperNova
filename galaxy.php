<?php

/**
 * galaxy.php
 *
 * Galaxy view
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('universe');
lng_include('stat');

$mode = sys_get_param_str('mode');
$uni_galaxy = sys_get_param_int('galaxy', $planetrow['galaxy']);
$uni_system = sys_get_param_int('system', $planetrow['system']);
$planet = sys_get_param_int('planet', $planetrow['planet']);

if($mode == 'name') {
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

$flying_fleet_count = FleetList::fleet_count_flying($user['id']);

if($mode == 1) {
} elseif($mode == 2 || $mode == 3) {
  $planet = $planetrow['planet'];
} else {
  $uni_galaxy = $planetrow['galaxy'];
  $uni_system = $planetrow['system'];
  $planet = $planetrow['planet'];
}

$uni_galaxy = $uni_galaxy < 1 ? 1 : ($uni_galaxy > Vector::$knownGalaxies? Vector::$knownGalaxies: $uni_galaxy);
$uni_system = $uni_system < 1 ? 1 : ($uni_system > Vector::$knownSystems ? Vector::$knownSystems : $uni_system);
$planet = $planet < 1 ? 1 : ($planet > Vector::$knownPlanets + 1 ? Vector::$knownPlanets + 1 : $planet);

$planetcount = 0;
$lunacount = 0;
$CurrentRC = $planetrow['recycler'];
$cached = array('users' => array(), 'allies' => array());


$template = gettemplate('universe', true);

$CurrentPoints = $user['total_points'];

$MissileRange = flt_get_missile_range($user);
$PhalanxRange = GetPhalanxRange($HavePhalanx);

$planet_precache_query = db_planet_list_in_system($uni_galaxy, $uni_system);
if(!empty($planet_precache_query)) {
  foreach($planet_precache_query as $planet_row) {
    $planet_list[$planet_row['planet']][$planet_row['planet_type']] = $planet_row;
  }
}


//$system_fleet_list = FleetList::fleet_list_by_planet_coords($uni_galaxy, $uni_system);
//foreach($system_fleet_list as $fleet_row) {
//  $fleet_planet = $fleet_row['fleet_mess'] == 0 ? $fleet_row['fleet_end_planet'] : $fleet_row['fleet_start_planet'];
//  $fleet_type = $fleet_row['fleet_mess'] == 0 ? $fleet_row['fleet_end_type'] : $fleet_row['fleet_start_type'];
//  $fleet_list[$fleet_planet][$fleet_type][] = $fleet_row;
//}

$system_fleet_list = FleetList::dbGetFleetListByCoordinates($uni_galaxy, $uni_system);
/**
 * @var Fleet[][][] $fleet_list
 */
$fleet_list = array();
foreach($system_fleet_list->_container as $objFleetSystem) {
  if(!$objFleetSystem->isReturning()) {
    $fleet_planet = $objFleetSystem->fleet_end_planet;
    $fleet_type = $objFleetSystem->fleet_end_type;
  } else {
    $fleet_planet = $objFleetSystem->fleet_start_planet;
    $fleet_type = $objFleetSystem->fleet_start_type;
  }
  $fleet_list[$fleet_planet][$fleet_type][] = $objFleetSystem;
}

$time_now_parsed = getdate(SN_TIME_NOW);

$recycler_info = array();
$planet_recyclers_orbiting = 0;
$recyclers_fleet = array();
foreach(sn_get_groups('flt_recyclers') as $recycler_id) {
  $recycler_info[$recycler_id] = get_ship_data($recycler_id, $user);
  $recyclers_fleet[$recycler_id] = mrc_get_level($user, $planetrow, $recycler_id);
  $planet_recyclers_orbiting += $recyclers_fleet[$recycler_id];
}

$user_skip_list = sys_stat_get_user_skip_list();
$fleet_id = 1;
$fleets = array();
$config_game_max_planet = Vector::$knownPlanets + 1;
for($Planet = 1; $Planet < $config_game_max_planet; $Planet++) {
  unset($uni_galaxyRowPlanet);
  unset($uni_galaxyRowMoon);
  unset($uni_galaxyRowUser);
  unset($uni_galaxyRowAlly);
  unset($allyquery);

  $uni_galaxyRowPlanet = $planet_list[$Planet][PT_PLANET];

  $planet_fleet_id = 0;
  if($uni_galaxyRowPlanet['destruyed']) {
    CheckAbandonPlanetState($uni_galaxyRowPlanet);
  } elseif($uni_galaxyRowPlanet['id']) {
    if($cached['users'][$uni_galaxyRowPlanet['id_owner']]) {
      $uni_galaxyRowUser = $cached['users'][$uni_galaxyRowPlanet['id_owner']];
    } else {
      $uni_galaxyRowUser = DBStaticUser::db_user_by_id($uni_galaxyRowPlanet['id_owner']);
      $cached['users'][$uni_galaxyRowUser['id']] = $uni_galaxyRowUser;
    }

    if(!$uni_galaxyRowUser['id']) {
      classSupernova::$debug->warning("Planet '{$uni_galaxyRowPlanet['name']}' [{$uni_galaxy}:{$uni_system}:{$Planet}] has no owner!", 'Userless planet', 503);
      $uni_galaxyRowPlanet['destruyed'] = SN_TIME_NOW + 60 * 60 * 24;
      $uni_galaxyRowPlanet['id_owner'] = 0;
      db_planet_set_by_id($uni_galaxyRowPlanet['id'], "id_owner = 0, destruyed = {$uni_galaxyRowPlanet['destruyed']}");
    }

    if($uni_galaxyRowUser['id']) {
      $planetcount++;
      if($uni_galaxyRowUser['ally_id']) {
        if($cached['allies'][$uni_galaxyRowUser['ally_id']]) {
          $allyquery = $cached['allies'][$uni_galaxyRowUser['ally_id']];
        } else {
          $allyquery = db_ally_get_by_id($uni_galaxyRowUser['ally_id']);
          $cached['allies'][$uni_galaxyRowUser['ally_id']] = $allyquery;
        }
      }

      $fleets_to_planet = flt_get_fleets_to_planet_by_array_of_Fleet($fleet_list[$Planet][PT_PLANET]);
      if(!empty($fleets_to_planet['own']['count'])) {
        $planet_fleet_id = $fleet_id;
        $fleets[] = tpl_parse_fleet_sn($fleets_to_planet['own']['total'], $fleet_id);
        $fleet_id++;
      }

      $uni_galaxyRowMoon = $planet_list[$Planet][PT_MOON];
      if($uni_galaxyRowMoon['destruyed']) {
        CheckAbandonPlanetState($uni_galaxyRowMoon);
      } else {
        $moon_fleet_id = 0;
        $fleets_to_planet = flt_get_fleets_to_planet_by_array_of_Fleet($fleet_list[$Planet][PT_MOON]);
        if(!empty($fleets_to_planet['own']['count'])) {
          $moon_fleet_id = $fleet_id;
          $fleets[] = tpl_parse_fleet_sn($fleets_to_planet['own']['total'], $fleet_id);
          $fleet_id++;
        }
      }
    }
  }

  $recyclers_incoming_capacity = 0;
  $uni_galaxyRowPlanet['debris'] = $uni_galaxyRowPlanet['debris_metal'] + $uni_galaxyRowPlanet['debris_crystal'];
  if($uni_galaxyRowPlanet['debris']) {
    if(!empty($fleet_list[$Planet][PT_DEBRIS])) {
      foreach($fleet_list[$Planet][PT_DEBRIS] as $objFleetToDebris) {
        if($objFleetToDebris->playerOwnerId == $user['id']) {
          $recyclers_incoming_capacity += $objFleetToDebris->shipsGetCapacityRecyclers($recycler_info);
        }
      }
    }

    $uni_galaxyRowPlanet['debris_reserved'] = $recyclers_incoming_capacity;
    $uni_galaxyRowPlanet['debris_reserved_percent'] = min(100, floor($uni_galaxyRowPlanet['debris_reserved'] / $uni_galaxyRowPlanet['debris'] * 100));

    $uni_galaxyRowPlanet['debris_to_gather'] = max(0, $uni_galaxyRowPlanet['debris'] - $recyclers_incoming_capacity);
    $uni_galaxyRowPlanet['debris_to_gather_percent'] = 100 - $uni_galaxyRowPlanet['debris_reserved_percent'];

    $recyclers_fleet_data = flt_calculate_fleet_to_transport($recyclers_fleet, $uni_galaxyRowPlanet['debris_to_gather'], $planetrow, $uni_galaxyRowPlanet);

    $uni_galaxyRowPlanet['debris_will_gather'] = max(0, min($recyclers_fleet_data['capacity'], $uni_galaxyRowPlanet['debris_to_gather']));
    $uni_galaxyRowPlanet['debris_will_gather_percent'] = $uni_galaxyRowPlanet['debris_to_gather'] ? floor($uni_galaxyRowPlanet['debris_will_gather'] / $uni_galaxyRowPlanet['debris_to_gather'] * $uni_galaxyRowPlanet['debris_to_gather_percent']) : 0;

    $uni_galaxyRowPlanet['debris_gather_total'] = max(0, $uni_galaxyRowPlanet['debris_will_gather'] + $uni_galaxyRowPlanet['debris_reserved']);
    $uni_galaxyRowPlanet['debris_gather_total_percent'] = min(100, floor($uni_galaxyRowPlanet['debris_gather_total'] / $uni_galaxyRowPlanet['debris'] * 100));
  }

  $RowUserPoints = $uni_galaxyRowUser['total_points'];
  $birthday_array = $uni_galaxyRowUser['user_birthday'] ? date_parse($uni_galaxyRowUser['user_birthday']) : array();
  $user_activity = floor((SN_TIME_NOW - $uni_galaxyRowUser['onlinetime']) / (60 * 60 * 24));
  $template->assign_block_vars('galaxyrow', array(
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

    'MOON_NAME_JS'  => js_safe_string($uni_galaxyRowMoon['name']),
    'MOON_IMAGE'    => $uni_galaxyRowMoon['image'],
    'MOON_DIAMETER' => number_format($uni_galaxyRowMoon['diameter'], 0, '', '.'),
    'MOON_TEMP'     => number_format($uni_galaxyRowMoon['temp_min'], 0, '', '.'),
    'MOON_FLEET_ID' => $moon_fleet_id,

    'DEBRIS'              => $uni_galaxyRowPlanet['debris'],
    'DEBRIS_METAL'        => $uni_galaxyRowPlanet['debris_metal'],
    'DEBRIS_CRYSTAL'      => $uni_galaxyRowPlanet['debris_crystal'],
    'DEBRIS_REST_PERCENT' => $uni_galaxyRowPlanet["debris_rest_percent"],

    'DEBRIS_RESERVED'             => $uni_galaxyRowPlanet['debris_reserved'],
    'DEBRIS_RESERVED_PERCENT'     => $uni_galaxyRowPlanet['debris_reserved_percent'],
    'DEBRIS_WILL_GATHER'          => $uni_galaxyRowPlanet['debris_will_gather'],
    'DEBRIS_WILL_GATHER_PERCENT'  => $uni_galaxyRowPlanet['debris_will_gather_percent'],
    'DEBRIS_GATHER_TOTAL'         => $uni_galaxyRowPlanet['debris_gather_total'],
    'DEBRIS_GATHER_TOTAL_PERCENT' => $uni_galaxyRowPlanet['debris_gather_total_percent'],

    'USER_ID'         => $uni_galaxyRowUser['id'],
    'USER_NAME'       => player_nick_render_to_html($uni_galaxyRowUser),
    'USER_NAME_JS'    => js_safe_string(player_nick_render_to_html($uni_galaxyRowUser)),
    'USER_RANK'       => in_array($uni_galaxyRowUser['id'], $user_skip_list) ? '-' : $uni_galaxyRowUser['total_rank'],
    'USER_BANNED'     => $uni_galaxyRowUser['banaday'],
    'USER_VACATION'   => $uni_galaxyRowUser['vacation'],
    'USER_ACTIVITY'   => $user_activity,
    'USER_ATTACKABLE' => $user_activity >= 7,
    'USER_INACTIVE'   => $user_activity >= 28,
    'USER_PROTECTED'  => $RowUserPoints <= classSupernova::$config->game_noob_points,
    'USER_NOOB'       => $RowUserPoints * classSupernova::$config->game_noob_factor < $CurrentPoints && classSupernova::$config->game_noob_factor,
    'USER_STRONG'     => $CurrentPoints * classSupernova::$config->game_noob_factor < $RowUserPoints && classSupernova::$config->game_noob_factor,
    'USER_AUTH'       => $uni_galaxyRowUser['authlevel'],
    'USER_ADMIN'      => classLocale::$lang['user_level_shortcut'][$uni_galaxyRowUser['authlevel']],
    'USER_BIRTHDAY'   => $birthday_array['month'] == $time_now_parsed['mon'] && $birthday_array['day'] == $time_now_parsed['mday'] ? date(FMT_DATE, SN_TIME_NOW) : 0,

    'ALLY_ID'  => $uni_galaxyRowUser['ally_id'],
    'ALLY_TAG' => $uni_galaxyRowUser['ally_tag'],
  ));
}

tpl_assign_fleet($template, $fleets);

foreach(sn_get_groups('defense_active') as $unit_id) {
  $template->assign_block_vars('defense_active', array(
    'ID'   => $unit_id,
    'NAME' => classLocale::$lang['tech'][$unit_id],
  ));
}

foreach($cached['users'] as $PlanetUser) {
  if(!$PlanetUser) {
    continue;
  }

  $user_ally = $cached['allies'][$PlanetUser['ally_id']];
  if(isset($user_ally)) {
    if($PlanetUser['id'] == $user_ally['ally_owner']) {
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
    'NAME'       => player_nick_render_to_html($PlanetUser, true),
    'NAME_JS'    => js_safe_string(player_nick_render_to_html($PlanetUser, true)),
    'RANK'       => in_array($PlanetUser['id'], $user_skip_list) ? '-' : $PlanetUser['total_rank'],
    'AVATAR'     => $PlanetUser['avatar'],
    'ALLY_ID'    => $PlanetUser['ally_id'],
    'ALLY_TAG'   => js_safe_string($user_ally['ally_tag']),
    'ALLY_TITLE' => str_replace(' ', '&nbsp', js_safe_string($user_rank_title)),
  ));
}

foreach($cached['allies'] as $PlanetAlly) {
  if($PlanetAlly) {
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

$is_missile = classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_MISSILE] && ($CurrentMIP > 0) && ($uni_galaxy == $CurrentGalaxy) && ($uni_system >= $CurrentSystem - $MissileRange) && ($uni_system <= $CurrentSystem + $MissileRange);
$colspan = classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_SPYING] + classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PM] + classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_BUDDY] + $is_missile;

$template->assign_vars(array(
    'rows'                  => $Result,
    'userCount'             => classSupernova::$config->users_amount,
//    'ALLY_COUNT'            => $ally_count['ally_count'],
    'ALLY_COUNT'            => db_ally_count(),
    'PLANET_EXPEDITION'     => Vector::$knownPlanets + 1,
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
    'SPs'                   => pretty_number(mrc_get_level($user, $planetrow, SHIP_SPY, false, true)),
    'SHOW_ADMIN'            => SHOW_ADMIN,
    'fleet_count'           => $flying_fleet_count,
    'fleet_max'             => $fleetmax,
    'ALLY_ID'               => $user['ally_id'],
    'USER_ID'               => $user['id'],
    'ACT_SPIO'              => classSupernova::$user_options[PLAYER_OPTION_FLEET_SPY_DEFAULT],
    'ACT_SPY'               => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_SPYING],
    'ACT_WRITE'             => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PM],
    'ACT_STATISTICS'        => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_STATS],
    'ACT_INFO'              => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_PROFILE],
    'ACT_FRIEND'            => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_ICON_BUDDY],
    'opt_uni_tooltip_time'  => classSupernova::$user_options[PLAYER_OPTION_TOOLTIP_DELAY],
    'opt_uni_avatar_user'   => $user['opt_uni_avatar_user'],
    'opt_uni_avatar_ally'   => $user['opt_uni_avatar_ally'],
    'ACT_MISSILE'           => $is_missile,
    'PLANET_PHALANX'        => $HavePhalanx && $uni_galaxy == $CurrentGalaxy && $uni_system >= $CurrentSystem - $PhalanxRange && $uni_system <= $CurrentSystem + $PhalanxRange,
    'PAGE_HINT'             => classLocale::$lang['gal_sys_hint'],
    'PLANET_RECYCLERS'      => $planet_recyclers_orbiting,
    'PLANET_RECYCLERS_TEXT' => pretty_number($planet_recyclers_orbiting),
//    'GALAXY_NAME'           => $galaxy_name['universe_name'],
//    'SYSTEM_NAME'           => $system_name['universe_name'],
    'GALAXY_NAME'           => db_universe_get_name($uni_galaxy),
    'SYSTEM_NAME'           => db_universe_get_name($uni_galaxy, $uni_system),
    'COL_SPAN'              => $colspan + 9,
    'COL_SPAN_PLUS'         => $colspan + 3,

    'COL_SPAN_NEW'          => $colspan + 4,
    'COL_SPAN_NEW_COLONIZE' => $colspan - 2,

    'PLAYER_OPTION_UNIVERSE_OLD'              => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_OLD],
    'PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE' => classSupernova::$user_options[PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE],
  )
);

display(parsetemplate($template), classLocale::$lang['sys_universe'], true, '', false);
