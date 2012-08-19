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

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('universe');

$mode       = sys_get_param_str('mode');
$uni_galaxy = sys_get_param_int('galaxy');
$uni_system = sys_get_param_int('system');

if($mode == 'name')
{
  require_once('includes/includes/uni_rename.php');
}

require_once('includes/includes/flt_functions.php');

$CurrentPlanetID  = sys_get_param_id('current');
$planet           = sys_get_param_int('planet');
$POST_galaxyLeft  = sys_get_param_str('galaxyLeft');
$POST_galaxyRight = sys_get_param_str('galaxyRight');
$POST_systemLeft  = sys_get_param_str('systemLeft');
$POST_systemRight = sys_get_param_str('systemRight');
$GET_galaxy       = sys_get_param_int('galaxy');
$GET_system       = sys_get_param_int('system');
$GET_planet       = sys_get_param_int('planet');

$fleetmax      = GetMaxFleets($user);
$CurrentPlID   = $planetrow['id'];
$CurrentMIP    = $planetrow['interplanetary_misil'];
$HavePhalanx   = $planetrow['phalanx'];
$CurrentSystem = $planetrow['system'];
$CurrentGalaxy = $planetrow['galaxy'];
$CanDestroy    = $planetrow[$sn_data[SHIP_DEATH_STAR]['name']];

$maxfleet       = doquery("SELECT COUNT(*) AS flying_fleet_count FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", '', true);
$maxfleet_count = $maxfleet['flying_fleet_count'];

if ($mode == 1) {
  if ($POST_galaxyLeft)
    $uni_galaxy--;
  elseif ($POST_galaxyRight)
    $uni_galaxy++;

  if ($POST_systemLeft)
    $uni_system--;
  elseif ($POST_systemRight)
    $uni_system++;
} elseif ($mode == 2 || $mode == 3) {
  $uni_galaxy = $GET_galaxy;
  $uni_system = $GET_system;
  $planet = $GET_planet;
} else {
  $uni_galaxy = $planetrow['galaxy'];
  $uni_system = $planetrow['system'];
  $planet = $planetrow['planet'];
}

if ($uni_galaxy < 1) $uni_galaxy = 1;
if ($uni_galaxy > $config->game_maxGalaxy) $uni_galaxy = $config->game_maxGalaxy;
if ($uni_system < 1) $uni_system = 1;
if ($uni_system > $config->game_maxSystem) $uni_system = $config->game_maxSystem;
if ($planet < 1) $planet = 1;
if ($planet > $config->game_maxPlanet + 1) $planet = $config->game_maxPlanet + 1;

$planetcount = 0;
$lunacount   = 0;
$CurrentRC   = $planetrow['recycler'];
$cached = array('users' => array(), 'allies' => array());


$template = gettemplate('universe', true);

// $UserPoints    = doquery("SELECT * FROM `{{statpoints}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."'", '', true);
$CurrentPoints = $user['total_points'];

$MissileRange  = flt_get_missile_range($user);
$PhalanxRange  = GetPhalanxRange($HavePhalanx);

$planet_precache_query = doquery("SELECT * FROM {{planets}} WHERE `galaxy` = {$uni_galaxy} AND `system` = {$uni_system};");
while($planet_row = mysql_fetch_assoc($planet_precache_query))
{
  $planet_list[$planet_row['planet']][$planet_row['planet_type']] = $planet_row;
}


$fleet_precache_query = doquery(
  "SELECT * FROM {{fleets}} WHERE
    (fleet_start_galaxy = {$uni_galaxy} AND fleet_start_system = {$uni_system} AND fleet_mess = 1)
    OR
    (fleet_end_galaxy = {$uni_galaxy} AND fleet_end_system = {$uni_system} AND fleet_mess = 0);"
);
while($fleet_row = mysql_fetch_assoc($fleet_precache_query))
{
  $fleet_planet = $fleet_row['fleet_mess'] == 0 ? $fleet_row['fleet_end_planet'] : $fleet_row['fleet_start_planet'];
  $fleet_type   = $fleet_row['fleet_mess'] == 0 ? $fleet_row['fleet_end_type'] : $fleet_row['fleet_start_type'];
  $fleet_list[$fleet_planet][$fleet_type][] = $fleet_row;
}

$time_now_parsed = getdate($time_now);

$recycler_info = array();
$planet_recyclers_orbiting = 0;
$recyclers_fleet = array();
foreach($sn_data['groups']['flt_recyclers'] as $recycler_id)
{
  $recycler_info[$recycler_id] = get_ship_data($recycler_id, $user);
  $recyclers_fleet[$recycler_id] = mrc_get_level($user, $planetrow, $recycler_id);
  $planet_recyclers_orbiting += $recyclers_fleet[$recycler_id];
}

//debug($recyclers_fleet);
//debug($recycler_info);

$fleet_id = 1;
$fleets = array();
$config_game_max_planet = $config->game_maxPlanet + 1;
for ($Planet = 1; $Planet < $config_game_max_planet; $Planet++)
{
  unset($uni_galaxyRowPlanet);
  unset($uni_galaxyRowMoon);
  unset($uni_galaxyRowUser);
  unset($uni_galaxyRowAlly);
  unset($allyquery);

  $uni_galaxyRowPlanet = $planet_list[$Planet][PT_PLANET];

  $planet_fleet_id = 0;
  if ($uni_galaxyRowPlanet['destruyed'])
  {
    CheckAbandonPlanetState ($uni_galaxyRowPlanet);
  }
  elseif($uni_galaxyRowPlanet['id'])
  {
    if($cached['users'][$uni_galaxyRowPlanet['id_owner']])
    {
      $uni_galaxyRowUser = $cached['users'][$uni_galaxyRowPlanet['id_owner']];
    }
    else
    {
      $uni_galaxyRowUser = doquery("SELECT * FROM {{users}} WHERE `id` = '{$uni_galaxyRowPlanet['id_owner']}' LIMIT 1;", '', true);
      $cached['users'][$uni_galaxyRowUser['id']] = $uni_galaxyRowUser;
    }

    if(!$uni_galaxyRowUser['id'])
    {
      $debug->warning("Planet '{$uni_galaxyRowPlanet['name']}' [{$uni_galaxy}:{$uni_system}:{$Planet}] has no owner!", 'Userless planet', 503);
      $uni_galaxyRowPlanet['destruyed'] = $time_now + 60 * 60 * 24;
      $uni_galaxyRowPlanet['id_owner'] = 0;
      doquery("UPDATE {{planets}} SET id_owner = 0, destruyed = {$uni_galaxyRowPlanet['destruyed']} WHERE `id` = {$uni_galaxyRowPlanet['id']} LIMIT 1;");
    }

    if($uni_galaxyRowUser['id'])
    {
      $planetcount++;
      if($uni_galaxyRowUser['ally_id'])
      {
        if($cached['allies'][$uni_galaxyRowUser['ally_id']])
        {
          $allyquery = $cached['allies'][$uni_galaxyRowUser['ally_id']];
        }
        else
        {
          $allyquery = doquery("SELECT * FROM `{{alliance}}` WHERE `id` = '{$uni_galaxyRowUser['ally_id']}';", '', true);
          $cached['allies'][$uni_galaxyRowUser['ally_id']] = $allyquery;
        }
      }

      $fleets_to_planet = flt_get_fleets_to_planet(false, $fleet_list[$Planet][PT_PLANET]);
      if($fleets_to_planet['own']['count'])
      {
        $planet_fleet_id = $fleet_id;
        $fleets[] = tpl_parse_fleet_sn($fleets_to_planet['own']['total'], $fleet_id);
        $fleet_id++;
      }

      $uni_galaxyRowMoon = $planet_list[$Planet][PT_MOON];
      if ($uni_galaxyRowMoon['destruyed'])
      {
        CheckAbandonPlanetState($uni_galaxyRowMoon);
      }
      else
      {
        $moon_fleet_id = 0;
        $fleets_to_planet = flt_get_fleets_to_planet(false, $fleet_list[$Planet][PT_MOON]);
        if($fleets_to_planet['own']['count'])
        {
          $moon_fleet_id = $fleet_id;
          $fleets[] = tpl_parse_fleet_sn($fleets_to_planet['own']['total'], $fleet_id);
          $fleet_id++;
        }
      }
    }
  }

//  $recyclers_incoming = 0;
  $recyclers_incoming_capacity = 0;
  $uni_galaxyRowPlanet['debris'] = $uni_galaxyRowPlanet['debris_metal'] + $uni_galaxyRowPlanet['debris_crystal'];
  if($uni_galaxyRowPlanet['debris'])
  {
//print('<hr>');
    if($fleet_list[$Planet][PT_DEBRIS])
    {
      foreach($fleet_list[$Planet][PT_DEBRIS] as $fleet_row)
      {
        if($fleet_row['fleet_owner'] == $user['id'])
        {
          $fleet_data = sys_unit_str2arr($fleet_row['fleet_array']);
          foreach($recycler_info as $recycler_id => $recycler_data)
          {
//            $recyclers_incoming += $fleet_data[$recycler_id];
            $recyclers_incoming_capacity += $fleet_data[$recycler_id] * $recycler_data['capacity'];
          }
        }
      }
    }

    $uni_galaxyRowPlanet['debris_reserved'] = $recyclers_incoming_capacity;
    $uni_galaxyRowPlanet['debris_reserved_percent'] = min(100, floor($uni_galaxyRowPlanet['debris_reserved'] / $uni_galaxyRowPlanet['debris'] * 100));

    $uni_galaxyRowPlanet['debris_to_gather'] = max(0, $uni_galaxyRowPlanet['debris'] - $recyclers_incoming_capacity);
    $uni_galaxyRowPlanet['debris_to_gather_percent'] = 100 - $uni_galaxyRowPlanet['debris_reserved_percent'];

    $recyclers_fleet_data = flt_calculate_fleet_to_transport($recyclers_fleet, $uni_galaxyRowPlanet['debris_to_gather'], $planetrow, $uni_galaxyRowPlanet);

    $uni_galaxyRowPlanet['debris_will_gather'] = min($recyclers_fleet_data['capacity'], $uni_galaxyRowPlanet['debris_to_gather']);
    $uni_galaxyRowPlanet['debris_will_gather_percent'] = $uni_galaxyRowPlanet['debris_to_gather'] ? floor($uni_galaxyRowPlanet['debris_will_gather'] / $uni_galaxyRowPlanet['debris_to_gather'] * $uni_galaxyRowPlanet['debris_to_gather_percent']) : 0;

    $uni_galaxyRowPlanet['debris_gather_total'] = $uni_galaxyRowPlanet['debris_will_gather'] + $uni_galaxyRowPlanet['debris_reserved'];
    $uni_galaxyRowPlanet['debris_gather_total_percent'] = min(100, floor($uni_galaxyRowPlanet['debris_gather_total'] / $uni_galaxyRowPlanet['debris'] * 100));

//    $uni_galaxyRowPlanet['debris_can_gather_percent'] = ($recyclers_fleet_data['capacity'] / $uni_galaxyRowPlanet['debris_to_gather']);
/*
debug($uni_galaxyRowPlanet['debris'], '$debris');
//debug($recyclers_incoming, '$recyclers_incoming');
//debug($recyclers_incoming_capacity, '$recyclers_incoming_capacity');
debug($uni_galaxyRowPlanet['debris_reserved'], '$debris_reserved');
debug($uni_galaxyRowPlanet['debris_reserved_percent'] . '%', '$debris_reserved_percent');
debug($uni_galaxyRowPlanet['debris_to_gather'], '$debris_to_gather');
debug($uni_galaxyRowPlanet['debris_to_gather_percent'] . '%', '$debris_to_gather_percent');

debug($recyclers_fleet_data['capacity'], 'can gather resources');

debug($uni_galaxyRowPlanet['debris_will_gather'], 'debris_will_gather');
debug($uni_galaxyRowPlanet['debris_will_gather_percent'], 'debris_will_gather_percent');
//debug($uni_galaxyRowPlanet['debris_can_gather_percent'], 'can gather percent');
//    debug($recyclers_fleet_data);
    // ceil( / $sn_data[SHIP_RECYCLER]['capacity']);
*/
//    $recyclers_need = ceil(($uni_galaxyRowPlanet['debris_metal'] + $uni_galaxyRowPlanet['debris_crystal']) / $sn_data[SHIP_RECYCLER]['capacity']);
//    $recyclers_to_send = min($CurrentRC, max(0, $recyclers_need - $recyclers_incoming));
  }

  $RowUserPoints = $uni_galaxyRowUser['total_points'];
  $birthday_array = $uni_galaxyRowUser['user_birthday'] ? date_parse($uni_galaxyRowUser['user_birthday']) : array();
  $template->assign_block_vars('galaxyrow', array(
     'PLANET_ID'        => $uni_galaxyRowPlanet['id'],
     'PLANET_NUM'       => $Planet,
     'PLANET_NAME'      => $uni_galaxyRowPlanet['name'],
     'PLANET_NAME_JS'   => js_safe_string($uni_galaxyRowPlanet['name']),
     'PLANET_DESTROYED' => $uni_galaxyRowPlanet["destruyed"],
     'PLANET_TYPE'      => $uni_galaxyRowPlanet["planet_type"],
     'PLANET_ACTIVITY'  => floor(($time_now - $uni_galaxyRowPlanet['last_update'])/60),
     'PLANET_IMAGE'     => $uni_galaxyRowPlanet['image'],
     'PLANET_FLEET_ID'  => $planet_fleet_id,

     'MOON_NAME_JS'   => js_safe_string($uni_galaxyRowMoon['name']),
     'MOON_DIAMETER'  => number_format($uni_galaxyRowMoon['diameter'], 0, '', '.'),
     'MOON_TEMP'      => number_format($uni_galaxyRowMoon['temp_min'], 0, '', '.'),
     'MOON_FLEET_ID'  => $moon_fleet_id,

     'DEBRIS'         => $uni_galaxyRowPlanet['debris'],
     'DEBRIS_METAL'   => $uni_galaxyRowPlanet['debris_metal'],
     'DEBRIS_CRYSTAL' => $uni_galaxyRowPlanet['debris_crystal'],
     'DEBRIS_REST_PERCENT' => $uni_galaxyRowPlanet["debris_rest_percent"],

     'DEBRIS_RESERVED' => $uni_galaxyRowPlanet['debris_reserved'],
     'DEBRIS_RESERVED_PERCENT' => $uni_galaxyRowPlanet['debris_reserved_percent'],
     'DEBRIS_WILL_GATHER' => $uni_galaxyRowPlanet['debris_will_gather'],
     'DEBRIS_WILL_GATHER_PERCENT' => $uni_galaxyRowPlanet['debris_will_gather_percent'],
     'DEBRIS_GATHER_TOTAL' => $uni_galaxyRowPlanet['debris_gather_total'],
     'DEBRIS_GATHER_TOTAL_PERCENT' => $uni_galaxyRowPlanet['debris_gather_total_percent'],

     'USER_ID'       => $uni_galaxyRowUser['id'],
     'USER_NAME'     => render_player_nick($uni_galaxyRowUser),
     'USER_NAME_JS'  => js_safe_string(render_player_nick($uni_galaxyRowUser)),
     'USER_RANK'     => $uni_galaxyRowUser['total_rank'],
     'USER_BANNED'   => $uni_galaxyRowUser['banaday'],
     'USER_VACATION' => $uni_galaxyRowUser['vacation'],
     'USER_ACTIVITY' => floor(($time_now - $uni_galaxyRowUser['onlinetime'])/(60*60*24)),
     'USER_PROTECTED'=> $RowUserPoints <= $config->game_noob_points,
     'USER_NOOB'     => $RowUserPoints * $config->game_noob_factor < $CurrentPoints && $config->game_noob_factor,
     'USER_STRONG'   => $CurrentPoints * $config->game_noob_factor < $RowUserPoints && $config->game_noob_factor,
     'USER_AUTH'     => $uni_galaxyRowUser['authlevel'],
     'USER_ADMIN'    => $lang['user_level_shortcut'][$uni_galaxyRowUser['authlevel']],
     'USER_BIRTHDAY' => $birthday_array['month'] == $time_now_parsed['mon'] && $birthday_array['day'] == $time_now_parsed['mday'] ? date(FMT_DATE, $time_now) : 0,

     'ALLY_ID'       => $uni_galaxyRowUser['ally_id'],
     'ALLY_TAG'      => $uni_galaxyRowUser['ally_tag'],
  ));
}

tpl_assign_fleet($template, $fleets);

foreach($sn_data['groups']['defense_active'] as $unit_id)
{
  $template->assign_block_vars('defense_active', array(
    'ID' => $unit_id,
    'NAME' => $lang['tech'][$unit_id],
  ));
}

foreach($cached['users'] as $PlanetUser)
{
  if($PlanetUser)
  {
    $user_ally = $cached['allies'][$PlanetUser['ally_id']];
    if(isset($user_ally))
    {
      if($PlanetUser['id'] == $user_ally['ally_owner'])
      {
        $user_rank_title = $user_ally['ally_owner_range'];
      }
      else
      {
        $ally_ranks = explode(';', $user_ally['ranklist']);
        list($user_rank_title) = explode(',', $ally_ranks[$PlanetUser['ally_rank_id']]);
      }
    }
    else
    {
      $user_rank_title = '';
    }

    $birthday_array = $PlanetUser['user_birthday'] ? date_parse($PlanetUser['user_birthday']) : array();
    $PlanetUser2 = $PlanetUser;
    $PlanetUser2['username'] = js_safe_string($PlanetUser2['username']);
    $template->assign_block_vars('users', array(
      'ID'   => $PlanetUser['id'],
      'NAME' => render_player_nick($PlanetUser, true),
      'NAME_JS' => render_player_nick($PlanetUser2, true),
      'RANK' => $PlanetUser['total_rank'],
//      'SEX'      => $PlanetUser['sex'] == 'F' ? 'female' : 'male',
//      'BIRTHDAY' => $birthday_array['month'] == $time_now_parsed['mon'] && $birthday_array['day'] == $time_now_parsed['mday'] ? 1 : 0, // date(FMT_DATE, $time_now)
      'AVATAR'   => $PlanetUser['avatar'],
      'ALLY_TAG' => js_safe_string($user_ally['ally_tag']),
      'ALLY_TITLE' => str_replace(' ', '&nbsp', js_safe_string($user_rank_title)),
    ));
  }
}

foreach($cached['allies'] as $PlanetAlly)
{
  if($PlanetAlly)
  {
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

$ally_count = doquery("SELECT COUNT(*) AS ally_count FROM {{alliance}};", '', true);
$galaxy_name = doquery("select `universe_name` from `{{universe}}` where `universe_galaxy` = {$uni_galaxy} and `universe_system` = 0 limit 1;", '', true);
$system_name = doquery("select `universe_name` from `{{universe}}` where `universe_galaxy` = {$uni_galaxy} and `universe_system` = {$uni_system} limit 1;", '', true);
$template->assign_vars(array(
     'rows'                => $Result,
     'userCount'           => $config->users_amount,
     'ALLY_COUNT'          => $ally_count['ally_count'],
     'EXPIDITION'          => $config->game_maxPlanet + 1,
     'curPlanetID'         => $planetrow['id'],
     'curPlanetG'          => $planetrow['galaxy'],
     'curPlanetS'          => $planetrow['system'],
     'curPlanetP'          => $planetrow['planet'],
     'curPlanetPT'         => $planetrow['planet_type'],
     'deathStars'          => $planetrow[$sn_data[SHIP_DEATH_STAR]['name']],
     'galaxy'              => $uni_galaxy,
     'system'              => $uni_system,
     'planet'              => $planet,
     'MIPs'                => $CurrentMIP,
     'MODE'                => $mode,
     'planets'             => $planetcount,
     'SPs'                 => pretty_number($planetrow['spy_sonde']),
     'SHOW_ADMIN'          => SHOW_ADMIN,
     'fleet_count'         => $maxfleet_count,
     'fleet_max'           => $fleetmax,
     'ALLY_ID'             => $user['ally_id'],
     'USER_ID'             => $user['id'],
     'ACT_SPY'             => $user['settings_esp'],
     'ACT_SPIO'            => $user['spio_anz'],
     'ACT_WRITE'           => $user['settings_wri'],
     'ACT_FRIEND'          => $user['settings_bud'],
     'opt_uni_avatar_user' => $user['opt_uni_avatar_user'],
     'opt_uni_avatar_ally' => $user['opt_uni_avatar_ally'],
     'ACT_MISSILE'         => $user["settings_mis"] && ($CurrentMIP > 0) && ($uni_galaxy == $CurrentGalaxy) && ($uni_system >= $CurrentSystem - $MissileRange) && ($uni_system <= $CurrentSystem + $MissileRange),
     'PLANET_PHALANX'      => $HavePhalanx && $uni_galaxy == $CurrentGalaxy && $uni_system >= $CurrentSystem - $PhalanxRange && $uni_system <= $CurrentSystem + $PhalanxRange,
     'PAGE_HINT'           => $lang['gal_sys_hint'],
//     'LANG_RECYCLERS'      => $lang['tech'][SHIP_RECYCLER],
     'PLANET_RECYCLERS'    => $planet_recyclers_orbiting,
     'GALAXY_NAME'         => $galaxy_name['universe_name'],
     'SYSTEM_NAME'         => $system_name['universe_name'],
   )
);

display (parsetemplate($template), $lang['sys_universe'], true, '', false);

?>
