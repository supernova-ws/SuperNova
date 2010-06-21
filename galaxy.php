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

  $maxfleet       = doquery("SELECT * FROM {{table}} WHERE `fleet_owner` = '". $user['id'] ."';", 'fleets');
  $maxfleet_count = mysql_num_rows($maxfleet);

  CheckPlanetUsedFields($CurrentPlanet);
  CheckPlanetUsedFields($lunarow);

  // Imperatif, dans quel mode suis-je (pour savoir dans quel etat j'ere)

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

  $parse = $lang;
  $parse['userCount']   = $config->users_amount;
  $parse['curPlanetG']  = $CurrentPlanet["galaxy"];
  $parse['curPlanetS']  = $CurrentPlanet["system"];
  $parse['curPlanetP']  = $CurrentPlanet["planet"];
  $parse['curPlanetPT'] = $CurrentPlanet["planet_type"];

  $parse['galaxy'] = $galaxy;
  $parse['system'] = $system;
  $parse['planet'] = $planet;

  $CurrentRC = $CurrentPlanet['recycler'];

  $parse['curPlanetID'] = $CurrentPlanetID;
  $parse['MIPs']        = $CurrentMIP;
  if ($mode != 2)
    $parse['isShowMISelector'] = 'class="hide"';

  $parse['rows']   = ShowGalaxyRows   ( $galaxy, $system );

  $parse['planets']     = $planetcount ? ($lang['gal_planets'] . $planetcount) : $lang['gal_planetNone'];
  $parse['RCs']         = pretty_number($CurrentPlanet['recycler']);
  $parse['SPs']         = pretty_number($CurrentPlanet['spy_sonde']);
  if(!SHOW_ADMIN)
    $parse['isShowAdmin'] = 'class=hide';
  $parse['fleet_count'] = $maxfleet_count;
  $parse['fleet_max']   = $fleetmax;

  display (parsetemplate(gettemplate('gal_main'), $parse), $lang['sys_universe'], true, '', false);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Created by Perberos
// 1.1 - Modified by -MoF- (UGamela germany)
// 1.2 - 1er Nettoyage Chlorel ...
// 1.3 - 2eme Nettoyage Chlorel ... Mise en fonction et debuging complet
?>