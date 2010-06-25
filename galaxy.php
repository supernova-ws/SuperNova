<?php

/**
 * galaxy.php
 *
 * Galaxy view
 *
 * @version 1.3s Security checks by Gorlum for http://supernova.ws
 * @version 1.3
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

$mode             = intval($_GET['mode']);
$CurrentPlanetID  = intval($_GET['current']);
$galaxy           = intval($_POST["galaxy"]);
$system           = intval($_POST["system"]);
$planet           = intval($_POST["planet"]);
$POST_galaxyLeft  = SYS_mysqlSmartEscape($_POST["galaxyLeft"]);
$POST_galaxyRight = SYS_mysqlSmartEscape($_POST["galaxyRight"]);
$POST_systemLeft  = SYS_mysqlSmartEscape($_POST["systemLeft"]);
$POST_systemRight = SYS_mysqlSmartEscape($_POST["systemRight"]);
$GET_galaxy       = intval($_GET['galaxy']);
$GET_system       = intval($_GET['system']);
$GET_planet       = intval($_GET['planet']);

  check_urlaubmodus ($user);
  includeLang('galaxy');

  $CurrentPlanet = doquery("SELECT * FROM {{table}} WHERE `id` = '". $user['current_planet'] ."';", 'planets', true);
  $lunarow       = doquery("SELECT * FROM {{table}} WHERE `id` = '". $user['current_luna'] ."';", 'lunas', true);
  $galaxyrow     = doquery("SELECT * FROM {{table}} WHERE `id_planet` = '". $CurrentPlanet['id'] ."';", 'galaxy', true);

  $dpath         = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];
  $fleetmax      = GetMaxFleets($user);
  $CurrentPlID   = $CurrentPlanet['id'];
  $CurrentMIP    = $CurrentPlanet['interplanetary_misil'];
  $HavePhalanx   = $CurrentPlanet['phalanx'];
  $CurrentSystem = $CurrentPlanet['system'];
  $CurrentGalaxy = $CurrentPlanet['galaxy'];
  $CanDestroy    = $CurrentPlanet[$resource[214]];

  $maxfleet       = doquery("SELECT COUNT(*) FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", 'fleets', true);
  $maxfleet_count = $maxfleet[0];

  CheckPlanetUsedFields($CurrentPlanet);
  CheckPlanetUsedFields($lunarow);

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


  $template = gettemplate('galaxy', true);

  $UserPoints    = doquery("SELECT * FROM `{{table}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."'", 'statpoints', true);
  $CurrentPoints = $UserPoints['total_points'];
  $CurrentLevel  = $CurrentPoints * $config->noobprotectionmulti;

  for ($Planet = 1; $Planet < 16; $Planet++) {
    unset($GalaxyRowPlanet);
    unset($GalaxyRowMoon);
    unset($GalaxyRowava);
    unset($GalaxyRowUser);
    unset($GalaxyRowAlly);
    unset($allyquery);

    $GalaxyRow = doquery("SELECT * FROM {{table}} WHERE `galaxy` = '".$galaxy."' AND `system` = '".$system."' AND `planet` = '".$Planet."';", 'galaxy', true);

    $GalaxyRowPlanet = doquery("SELECT * FROM {{planets}} WHERE `galaxy` = {$galaxy} AND `system` = {$system} AND `planet` = {$Planet} AND `planet_type` = 1;", '', true);
    if ($GalaxyRowPlanet['id']) {
      if ($GalaxyRowPlanet['destruyed']) {
        CheckAbandonPlanetState ($GalaxyRowPlanet);
      } else {
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

        if ($GalaxyRowUser['ally_id']) {
          if($cached['allies'][$GalaxyRowUser['ally_id']])
            $allyquery = $cached['allies'][$GalaxyRowUser['ally_id']];
          else{
            $allyquery = doquery("SELECT * FROM `{{alliance}}` WHERE `id` = '{$GalaxyRowUser['ally_id']}';", '', true);
            $cached['allies'][$GalaxyRowUser['ally_id']] = $allyquery;
          }
        }
      }

      $GalaxyRowMoon = doquery("SELECT * FROM {{planets}} WHERE `galaxy` = {$galaxy} AND `system` = {$system} AND `planet` = {$Planet} AND `planet_type` = 3;", '', true);
      if ($GalaxyRowMoon['destruyed'])
        CheckAbandonMoonState ($GalaxyRowMoon);
    }



    $template->assign_block_vars('galaxyrow', array(
       'PLANETNUM'  => $Planet,
       'PLANET'     => GalaxyRowPlanet     ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $galaxy, $system, $Planet, 1 ),
       'PLANETNAME' => GalaxyRowPlanetName ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $galaxy, $system, $Planet, 1 ),
       'MOON'       => GalaxyRowMoon       ( $GalaxyRow, $GalaxyRowMoon  , $GalaxyRowUser, $galaxy, $system, $Planet, 3 ),
       'DEBRIS'     => GalaxyRowDebris     ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $galaxy, $system, $Planet, 2 ),

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


       'ALLY_ID'    => $allyquery['id'],
       'ALLY_TAG'   => $allyquery['ally_tag'],

       'ACTIONS'    => GalaxyRowActions    ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $galaxy, $system, $Planet, 0 ),
    ));
  }


  foreach($cached['users'] as $PlanetUser){
    $script .= "users[{$PlanetUser['id']}] = new Array('{$PlanetUser['username']}','{$PlanetUser['rank']}');";
  }

  foreach($cached['allies'] as $PlanetAlly){
    $script .= "allies[{$PlanetAlly['id']}] = new Array('{$PlanetAlly['ally_web']}','{$PlanetAlly['ally_name']}','{$PlanetAlly['ally_members']}');";
  }

  $template->assign_vars(array(
       'rows'                => $Result,
       'userCount'           => $config->users_amount,
       'curPlanetG'          => $CurrentPlanet["galaxy"],
       'curPlanetS'          => $CurrentPlanet["system"],
       'curPlanetP'          => $CurrentPlanet["planet"],
       'curPlanetPT'         => $CurrentPlanet["planet_type"],
       'galaxy'              => $galaxy,
       'system'              => $system,
       'planet'              => $planet,
       'curPlanetID'         => $CurrentPlanetID,
       'MIPs'                => $CurrentMIP,
       'MODE'                => $mode,
       'planets'             => $planetcount ? ($lang['gal_planets'] . $planetcount) : $lang['gal_planetNone'],
       'RCs'                 => pretty_number($CurrentPlanet['recycler']),
       'SPs'                 => pretty_number($CurrentPlanet['spy_sonde']),
       'SHOW_ADMIN'          => SHOW_ADMIN,
       'fleet_count'         => $maxfleet_count,
       'fleet_max'           => $fleetmax,
       'ALLY_ID'             => $user['ally_id'],
       'USER_ID'             => $user['id'],
       'script'              => $script,
     )
  );

//  pr();
  display ($template, $lang['sys_universe'], true, '', false);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Created by Perberos
// 1.1 - Modified by -MoF- (UGamela germany)
// 1.2 - 1er Nettoyage Chlorel ...
// 1.3 - 2eme Nettoyage Chlorel ... Mise en fonction et debuging complet
?>