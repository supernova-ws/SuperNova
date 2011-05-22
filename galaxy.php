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

$mode             = intval($_GET['mode']);
$CurrentPlanetID  = intval($_GET['current']);
$galaxy           = intval($_POST['galaxy']);
$system           = intval($_POST['system']);
$planet           = intval($_POST['planet']);
$POST_galaxyLeft  = SYS_mysqlSmartEscape($_POST['galaxyLeft']);
$POST_galaxyRight = SYS_mysqlSmartEscape($_POST['galaxyRight']);
$POST_systemLeft  = SYS_mysqlSmartEscape($_POST['systemLeft']);
$POST_systemRight = SYS_mysqlSmartEscape($_POST['systemRight']);
$GET_galaxy       = intval($_GET['galaxy']);
$GET_system       = intval($_GET['system']);
$GET_planet       = intval($_GET['planet']);

lng_include('universe');

$fleetmax      = GetMaxFleets($user);
$CurrentPlID   = $planetrow['id'];
$CurrentMIP    = $planetrow['interplanetary_misil'];
$HavePhalanx   = $planetrow['phalanx'];
$CurrentSystem = $planetrow['system'];
$CurrentGalaxy = $planetrow['galaxy'];
$CanDestroy    = $planetrow[$sn_data[SHIP_DEATH_STAR]['name']];

$maxfleet       = doquery("SELECT COUNT(*) AS flying_fleet_count FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", '', true);
$maxfleet_count = $maxfleet['flying_fleet_count'];

CheckPlanetUsedFields($planetrow);

if ($mode == 1) {
  if ($POST_galaxyLeft)
    $galaxy--;
  elseif ($POST_galaxyRight)
    $galaxy++;

  if ($POST_systemLeft)
    $system--;
  elseif ($POST_systemRight)
    $system++;
} elseif ($mode == 2 || $mode == 3) {
  $galaxy = $GET_galaxy;
  $system = $GET_system;
  $planet = $GET_planet;
} else {
  $galaxy = $planetrow['galaxy'];
  $system = $planetrow['system'];
  $planet = $planetrow['planet'];
}

if ($galaxy < 1) $galaxy = 1;
if ($galaxy > $config->game_maxGalaxy) $galaxy = $config->game_maxGalaxy;
if ($system < 1) $system = 1;
if ($system > $config->game_maxSystem) $system = $config->game_maxSystem;
if ($planet < 1) $planet = 1;
if ($planet > $config->game_maxPlanet + 1) $planet = $config->game_maxPlanet + 1;

$planetcount = 0;
$lunacount   = 0;
$CurrentRC   = $planetrow['recycler'];
$cached = array('users' => array(), 'allies' => array());


$template = gettemplate('universe', true);

$UserPoints    = doquery("SELECT * FROM `{{statpoints}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."'", '', true);
$CurrentPoints = $UserPoints['total_points'];

$MissileRange  = get_missile_range();
$PhalanxRange  = GetPhalanxRange($HavePhalanx);

$planet_precache_query = doquery("SELECT * FROM {{planets}} WHERE `galaxy` = {$galaxy} AND `system` = {$system};");
while($planet_row = mysql_fetch_assoc($planet_precache_query))
{
  $planet_list[$planet_row['planet']][$planet_row['planet_type']] = $planet_row;
}


$fleet_precache_query = doquery(
  "SELECT * FROM {{fleets}} WHERE
    (fleet_start_galaxy = {$galaxy} AND fleet_start_system = {$system} AND fleet_mess = 1)
    OR
    (fleet_end_galaxy = {$galaxy} AND fleet_end_system = {$system} AND fleet_mess = 0);"
);
while($fleet_row = mysql_fetch_assoc($fleet_precache_query))
{
  $fleet_planet = $fleet_row['fleet_mess'] == 0 ? $fleet_row['fleet_end_planet'] : $fleet_row['fleet_start_planet'];
  $fleet_type   = $fleet_row['fleet_mess'] == 0 ? $fleet_row['fleet_end_type'] : $fleet_row['fleet_start_type'];
  $fleet_list[$fleet_planet][$fleet_type][] = $fleet_row;
}

$fleet_id = 1;
$fleets = array();
$config_game_max_planet = $config->game_maxPlanet + 1;
for ($Planet = 1; $Planet < $config_game_max_planet; $Planet++)
{
  unset($GalaxyRowPlanet);
  unset($GalaxyRowMoon);
  unset($GalaxyRowUser);
  unset($GalaxyRowAlly);
  unset($allyquery);

  $GalaxyRowPlanet = $planet_list[$Planet][PT_PLANET];

  $RowUserPoints = 0;
  $planet_fleet_id = 0;
  if ($GalaxyRowPlanet['destruyed']) {
    CheckAbandonPlanetState ($GalaxyRowPlanet);
  } elseif ($GalaxyRowPlanet['id']) {
    $planetcount++;

    if($cached['users'][$GalaxyRowPlanet['id_owner']]){
      $GalaxyRowUser = $cached['users'][$GalaxyRowPlanet['id_owner']];
    }else{
      $GalaxyRowUser = doquery("SELECT * FROM {{users}} WHERE `id` = '{$GalaxyRowPlanet['id_owner']}' LIMIT 1;", '', true);

      $User2Points   = doquery("SELECT total_rank, total_points FROM `{{statpoints}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '{$GalaxyRowUser['id']}' LIMIT 1;", '', true);
      $GalaxyRowUser['rank']   = intval($User2Points['total_rank']);
      $GalaxyRowUser['points'] = intval($User2Points['total_points']);

      $cached['users'][$GalaxyRowUser['id']] = $GalaxyRowUser;
    }
    if(!$GalaxyRowUser)
    {
      $debug->warning("Planet '{$GalaxyRowPlanet['name']}' [{$galaxy}:{$system}:{$Planet}] has no owner!", 'Userless planet', 503);
      continue;
    }

    $RowUserPoints = $GalaxyRowUser['points'];
    if($GalaxyRowUser['id'])
    {
      if ($GalaxyRowUser['ally_id'])
      {
        if($cached['allies'][$GalaxyRowUser['ally_id']])
        {
          $allyquery = $cached['allies'][$GalaxyRowUser['ally_id']];
        }
        else
        {
          $allyquery = doquery("SELECT * FROM `{{alliance}}` WHERE `id` = '{$GalaxyRowUser['ally_id']}';", '', true);
          $cached['allies'][$GalaxyRowUser['ally_id']] = $allyquery;
        }
      }
    }

    $fleets_to_planet = flt_get_fleets_to_planet(false, $fleet_list[$Planet][PT_PLANET]);
    if($fleets_to_planet['own']['count'])
    {
      $planet_fleet_id = $fleet_id;
      $fleets[] = tpl_parse_fleet_sn($fleets_to_planet['own']['total'], $fleet_id);
      $fleet_id++;
    }

    $recyclers_incoming = 0;
    if($fleet_list[$Planet][PT_DEBRIS])
    {
      foreach($fleet_list[$Planet][PT_DEBRIS] as $fleet_row)
      {
        $fleet_data = flt_expand($fleet_row);
        $recyclers_incoming += $fleet_data[SHIP_RECYCLER];
      }
    }

    $GalaxyRowMoon = $planet_list[$Planet][PT_MOON];
    if ($GalaxyRowMoon['destruyed'])
    {
      CheckAbandonPlanetState($GalaxyRowMoon);
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

  if ($GalaxyRowPlanet["debris_metal"] || $GalaxyRowPlanet["debris_crystal"]) {
    $RecNeeded = ceil(($GalaxyRowPlanet["debris_metal"] + $GalaxyRowPlanet["debris_crystal"]) / $pricelist[SHIP_RECYCLER]['capacity']);
    if ($RecNeeded < $CurrentRC) {
      $recyclers_sent = $RecNeeded;
    }else{
      $recyclers_sent = $CurrentRC;
    }
  }

  $recyclers_need = ceil(($GalaxyRowPlanet['debris_metal'] + $GalaxyRowPlanet['debris_crystal']) / $sn_data[SHIP_RECYCLER]['capacity']);

  $template->assign_block_vars('galaxyrow', array(
     'PLANET_ID'        => $GalaxyRowPlanet['id'],
     'PLANET_NUM'       => $Planet,
     'PLANET_NAME'      => $GalaxyRowPlanet['name'],
     'PLANET_NAME_JS'   => js_safe_string($GalaxyRowPlanet['name']),
     'PLANET_DESTROYED' => $GalaxyRowPlanet["destruyed"],
     'PLANET_TYPE'      => $GalaxyRowPlanet["planet_type"],
     'PLANET_ACTIVITY'  => floor(($time_now - $GalaxyRowPlanet['last_update'])/60),
     'PLANET_IMAGE'     => $GalaxyRowPlanet['image'],
     'PLANET_FLEET_ID'  => $planet_fleet_id,

     'MOON_NAME_JS'   => js_safe_string($GalaxyRowMoon['name']),
     'MOON_DIAMETER'  => number_format($GalaxyRowMoon['diameter'], 0, '', '.'),
     'MOON_TEMP'      => number_format($GalaxyRowMoon['temp_min'], 0, '', '.'),
     'MOON_FLEET_ID'  => $moon_fleet_id,

     'DEBRIS_METAL'   => $GalaxyRowPlanet['debris_metal'],
     'DEBRIS_CRYSTAL' => $GalaxyRowPlanet['debris_crystal'],
     'DEBRIS_RC_INC'  => $recyclers_incoming,
     'DEBRIS_RC_SEND' => $recyclers_sent <= $recyclers_incoming ? 0 : $recyclers_sent - $recyclers_incoming,
     'DEBRIS_RC_NEED' => $recyclers_need,

     'USER_ID'       => $GalaxyRowUser['id'],
     'USER_NAME'     => $GalaxyRowUser['username'],
     'USER_NAME_JS'  => js_safe_string($GalaxyRowUser['username']),
     'USER_RANK'     => $GalaxyRowUser['rank'],
     'USER_BANNED'   => $GalaxyRowUser['bana'],
     'USER_VACATION' => $GalaxyRowUser['vacation'],
     'USER_ACTIVITY' => floor(($time_now - $GalaxyRowUser['onlinetime'])/(60*60*24)),
     'USER_PROTECTED'=> $RowUserPoints <= $config->game_noob_points,
     'USER_NOOB'     => $RowUserPoints * $config->game_noob_factor < $CurrentPoints && $config->game_noob_factor,
     'USER_STRONG'   => $CurrentPoints * $config->game_noob_factor < $RowUserPoints && $config->game_noob_factor,
     'USER_AUTH'     => $GalaxyRowUser['authlevel'],
     'USER_ADMIN'    => $lang['user_level_shortcut'][$GalaxyRowUser['authlevel']],

     'ALLY_ID'       => $allyquery['id'],
     'ALLY_TAG'      => $allyquery['ally_tag'],
  ));
}

tpl_assign_fleet($template, $fleets);

foreach($cached['users'] as $PlanetUser){
  if($PlanetUser)
  {
    $template->assign_block_vars('users', array(
      'ID'   => $PlanetUser['id'],
      'NAME' => $PlanetUser['username'],
      'NAME_JS' => js_safe_string($PlanetUser['username']),
      'RANK' => $PlanetUser['rank'],
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
    ));
  }
}

$template->assign_vars(array(
     'rows'           => $Result,
     'userCount'      => $config->users_amount,
     'EXPIDITION'     => $config->game_maxPlanet + 1,
     'curPlanetID'    => $planetrow['id'],
     'curPlanetG'     => $planetrow['galaxy'],
     'curPlanetS'     => $planetrow['system'],
     'curPlanetP'     => $planetrow['planet'],
     'curPlanetPT'    => $planetrow['planet_type'],
     'deathStars'     => $planetrow[$sn_data[SHIP_DEATH_STAR]['name']],
     'galaxy'         => $galaxy,
     'system'         => $system,
     'planet'         => $planet,
     'MIPs'           => $CurrentMIP,
     'MODE'           => $mode,
     'planets'        => $planetcount ? ($lang['gal_planets'] . $planetcount) : $lang['gal_planetNone'],
     'RCs'            => pretty_number($planetrow['recycler']),
     'SPs'            => pretty_number($planetrow['spy_sonde']),
     'SHOW_ADMIN'     => SHOW_ADMIN,
     'fleet_count'    => $maxfleet_count,
     'fleet_max'      => $fleetmax,
     'ALLY_ID'        => $user['ally_id'],
     'USER_ID'        => $user['id'],
     'ACT_SPY'        => $user['settings_esp'],
     'ACT_SPIO'       => $user['spio_anz'],
     'ACT_WRITE'      => $user['settings_wri'],
     'ACT_FRIEND'     => $user['settings_bud'],
     'ACT_MISSILE'    => $user["settings_mis"] && ($CurrentMIP > 0) && ($galaxy == $CurrentGalaxy) &&
                         ($system >= $CurrentSystem - $MissileRange) && ($system <= $CurrentSystem + $MissileRange),
     'PLANET_PHALANX' => $HavePhalanx && $galaxy == $CurrentGalaxy &&
                         $system >= $CurrentSystem - $PhalanxRange && $system <= $CurrentSystem + $PhalanxRange,
     'PAGE_HINT'      => $lang['gal_sys_hint'],
     'LANG_RECYCLERS' => $lang['tech'][SHIP_RECYCLER],
   )
);

display (parsetemplate($template), $lang['sys_universe'], true, '', false);

?>
