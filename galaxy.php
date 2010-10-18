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

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

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

  check_urlaubmodus ($user);
  includeLang('universe');

  $CurrentPlanet = doquery("SELECT * FROM {{table}} WHERE `id` = '". $user['current_planet'] ."';", 'planets', true);

  $dpath         = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];
  $fleetmax      = GetMaxFleets($user);
  $CurrentPlID   = $CurrentPlanet['id'];
  $CurrentMIP    = $CurrentPlanet['interplanetary_misil'];
  $HavePhalanx   = $CurrentPlanet['phalanx'];
  $CurrentSystem = $CurrentPlanet['system'];
  $CurrentGalaxy = $CurrentPlanet['galaxy'];
  $CanDestroy    = $CurrentPlanet[$sn_data[214]['name']];

  $maxfleet       = doquery("SELECT COUNT(*) FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", 'fleets', true);
  $maxfleet_count = $maxfleet[0];

  CheckPlanetUsedFields($CurrentPlanet);

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
    $galaxy = $CurrentPlanet['galaxy'];
    $system = $CurrentPlanet['system'];
    $planet = $CurrentPlanet['planet'];
  }

  if ($galaxy < 1) $galaxy = 1;
  if ($galaxy > $config->game_maxGalaxy) $galaxy = $config->game_maxGalaxy;
  if ($system < 1) $system = 1;
  if ($system > $config->game_maxSystem) $system = $config->game_maxSystem;
  if ($planet < 1) $planet = 1;
  if ($planet > $config->game_maxPlanet + 1) $planet = $config->game_maxPlanet + 1;

  $planetcount = 0;
  $lunacount   = 0;
  $CurrentRC   = $CurrentPlanet['recycler'];
  $cached = array();


  $template = gettemplate('universe', true);

  $UserPoints    = doquery("SELECT * FROM `{{table}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."'", 'statpoints', true);
  $CurrentPoints = $UserPoints['total_points'];
  $CurrentLevel  = $CurrentPoints * $config->noobprotectionmulti;
  $MissileRange  = GetMissileRange();
  $PhalanxRange  = GetPhalanxRange ( $HavePhalanx );

  $fleet_id = 1;
  $fleets = array();
  for ($Planet = 1; $Planet < 16; $Planet++) {
    unset($GalaxyRowPlanet);
    unset($GalaxyRowMoon);
    unset($GalaxyRowava);
    unset($GalaxyRowUser);
    unset($GalaxyRowAlly);
    unset($allyquery);

    $GalaxyRowPlanet = doquery("SELECT * FROM {{planets}} WHERE `galaxy` = {$galaxy} AND `system` = {$system} AND `planet` = {$Planet} AND `planet_type` = 1;", '', true);

    $planet_fleet_id = 0;
    if ($GalaxyRowPlanet['destruyed']) {
      CheckAbandonPlanetState ($GalaxyRowPlanet);
    } elseif ($GalaxyRowPlanet['id']) {
      $planetcount++;

      if($cached['users'][$GalaxyRowPlanet['id_owner']]){
        $GalaxyRowUser = $cached['users'][$GalaxyRowPlanet['id_owner']];
      }else{
        $GalaxyRowUser = doquery("SELECT * FROM {{users}} WHERE `id` = '{$GalaxyRowPlanet['id_owner']}';", '', true);

        $User2Points   = doquery("SELECT * FROM `{{statpoints}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '{$GalaxyRowUser['id']}'", '', true);
        $GalaxyRowUser['rank']   = intval($User2Points['total_rank']);
        $GalaxyRowUser['points'] = intval($User2Points['total_points']);

        $cached['users'][$GalaxyRowUser['id']] = $GalaxyRowUser;
      }
      $RowUserPoints = $GalaxyRowUser['points'];
      $RowUserLevel  = $RowUserPoints * $config->noobprotectionmulti;

      if($GalaxyRowUser['id'])
        if ($GalaxyRowUser['ally_id']) {
          if($cached['allies'][$GalaxyRowUser['ally_id']])
            $allyquery = $cached['allies'][$GalaxyRowUser['ally_id']];
          else{
            $allyquery = doquery("SELECT * FROM `{{alliance}}` WHERE `id` = '{$GalaxyRowUser['ally_id']}';", '', true);
            $cached['allies'][$GalaxyRowUser['ally_id']] = $allyquery;
          }
        }

      $fleet_list = flt_get_fleets_to_planet($GalaxyRowPlanet);
      if($fleet_list['own']['count'])
      {
        $planet_fleet_id = $fleet_id;
        $fleets[] = tpl_parse_fleet_sn($fleet_list['own']['total'], $fleet_id);
        $fleet_id++;
      }

      $recyclers_incoming = 0;
      $sql_fleets = doquery("SELECT * FROM {{fleets}} WHERE `fleet_end_galaxy` = {$galaxy} AND `fleet_end_system` = {$system} AND `fleet_end_planet` = {$Planet} AND `fleet_end_type` = 2 AND fleet_mess = 0 AND fleet_owner = {$user['id']};");
      while ($arr_fleet = mysql_fetch_array($sql_fleets)) {
        $fleet = flt_expand($arr_fleet);
        $recyclers_incoming += $fleet[209];
      }

      $GalaxyRowMoon = doquery("SELECT * FROM {{planets}} WHERE `parent_planet` = {$GalaxyRowPlanet['id']};", '', true);
      if ($GalaxyRowMoon['destruyed'])
        CheckAbandonPlanetState($GalaxyRowMoon);
    }

    if ($GalaxyRowPlanet["debris_metal"] || $GalaxyRowPlanet["debris_crystal"]) {
      $RecNeeded = ceil(($GalaxyRowPlanet["debris_metal"] + $GalaxyRowPlanet["debris_crystal"]) / $pricelist[209]['capacity']);
      if ($RecNeeded < $CurrentRC) {
        $recyclers_sent = $RecNeeded;
      }else{
        $recyclers_sent = $CurrentRC;
      }
    }

    $template->assign_block_vars('galaxyrow', array(
       'PLANET_ID'        => $GalaxyRowPlanet['id'],
       'PLANET_NUM'       => $Planet,
       'PLANET_NAME'      => $GalaxyRowPlanet['name'],
       'PLANET_DESTROYED' => $GalaxyRowPlanet["destruyed"],
       'PLANET_TYPE'      => $GalaxyRowPlanet["planet_type"],
       'PLANET_ACTIVITY'  => floor(($time_now - $GalaxyRowPlanet['last_update'])/60),
       'PLANET_IMAGE'     => $GalaxyRowPlanet['image'],
       'PLANET_FLEET_ID'  => $planet_fleet_id,

       'MOON_NAME'      => $GalaxyRowMoon["name"],
       'MOON_DIAMETER'  => number_format($GalaxyRowMoon['diameter'], 0, '', '.'),
       'MOON_TEMP'      => number_format($GalaxyRowMoon['temp_min'], 0, '', '.'),

       'DEBRIS_METAL'   => $GalaxyRowPlanet['debris_metal'], //number_format( $GalaxyRowPlanet['metal'], 0, '', '.'),
       'DEBRIS_CRYSTAL' => $GalaxyRowPlanet['debris_crystal'], //number_format( $GalaxyRowPlanet['crystal'], 0, '', '.'),
       'DEBRIS_RC_SEND' => $recyclers_sent,
       'DEBRIS_RC_INC'  => $recyclers_incoming,

       'USER_ID'       => $GalaxyRowUser['id'],
       'USER_NAME'     => $GalaxyRowUser['username'],
       'USER_RANK'     => $GalaxyRowUser['rank'],
       'USER_BANNED'   => $GalaxyRowUser['bana'],
       'USER_VACANCY'  => $GalaxyRowUser['urlaubs_modus'],
       'USER_ACTIVITY' => floor(($time_now - $GalaxyRowUser['onlinetime'])/(60*60*24)),
       'USER_NOOB'     => $config->noobprotection && $RowUserLevel < $CurrentPoints && $RowUserPoints < $config->noobprotectiontime * 1000,
       'USER_STRONG'   => $config->noobprotection && $RowUserPoints > $CurrentLevel && $CurrentPoints < $config->noobprotectiontime * 1000,
       'USER_AUTH'     => $GalaxyRowUser['authlevel'],
       'USER_ADMIN'    => $lang['user_level_shortcut'][$GalaxyRowUser['authlevel']],

       'ALLY_ID'       => $allyquery['id'],
       'ALLY_TAG'      => $allyquery['ally_tag'],
    ));
  }

  tpl_assign_fleet($template, $fleets);

  foreach($cached['users'] as $PlanetUser){
    $template->assign_block_vars('users', array(
      'ID'   => $PlanetUser['id'],
      'NAME' => $PlanetUser['username'],
      'RANK' => $PlanetUser['rank'],
    ));
  }

  foreach($cached['allies'] as $PlanetAlly){
    $template->assign_block_vars('alliances', array(
      'ID'      => $PlanetAlly['id'],
      'NAME'    => $PlanetAlly['ally_name'],
      'MEMBERS' => $PlanetAlly['ally_members'],
      'URL'     => $PlanetAlly['ally_web'],
    ));
  }

  $template->assign_vars(array(
       'rows'           => $Result,
       'userCount'      => $config->users_amount,
       'curPlanetID'    => $CurrentPlanet['id'],
       'curPlanetG'     => $CurrentPlanet['galaxy'],
       'curPlanetS'     => $CurrentPlanet['system'],
       'curPlanetP'     => $CurrentPlanet['planet'],
       'curPlanetPT'    => $CurrentPlanet['planet_type'],
       'deathStars'     => $CurrentPlanet[$sn_data[214]['name']],
       'galaxy'         => $galaxy,
       'system'         => $system,
       'planet'         => $planet,
       'MIPs'           => $CurrentMIP,
       'MODE'           => $mode,
       'dpath'          => $dpath,
       'planets'        => $planetcount ? ($lang['gal_planets'] . $planetcount) : $lang['gal_planetNone'],
       'RCs'            => pretty_number($CurrentPlanet['recycler']),
       'SPs'            => pretty_number($CurrentPlanet['spy_sonde']),
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
     )
  );


  display ($template, $lang['sys_universe'], true, '', false);
?>