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
$POST_galaxy      = intval($_POST["galaxy"]);
$POST_system      = intval($_POST["system"]);
$POST_planet      = intval($_POST["planet"]);
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
  $CurrentRC     = $CurrentPlanet['recycler'];
  $CurrentSP     = $CurrentPlanet['spy_sonde'];
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
    if ($POST_galaxyLeft) {
      $POST_galaxy--;
      if ($POST_galaxy < 1)
        $POST_galaxy = 1;
    } elseif ($POST_galaxyRight) {
      $POST_galaxy++;
      if ($POST_galaxy > $config->game_maxGalaxy)
        $POST_galaxy = $config->game_maxGalaxy;
    }
    $galaxy = $POST_galaxy;

    if ($POST_systemLeft) {
      $POST_system--;
      if ($POST_system < 1)
        $POST_system = 1;
      $system = $POST_system;
    } elseif ($POST_systemRight) {
      $POST_system++;
      if ($POST_system > $config->game_maxSystem)
        $POST_system = $config->game_maxSystem;
    }
    $system = $POST_system;
  } elseif ($mode == 2) {
    $galaxy        = $GET_galaxy;
    $system        = $GET_system;
    $planet        = $GET_planet;
  } elseif ($mode == 3) {
    $galaxy        = $GET_galaxy;
    $system        = $GET_system;
  } else {
    $galaxy = $CurrentPlanet['galaxy'];
    $system = $CurrentPlanet['system'];
    $planet = $CurrentPlanet['planet'];
  }

  $planetcount = 0;
  $lunacount   = 0;

  $parse = $lang;
  $parse['scripts']  = InsertGalaxyScripts ( $CurrentPlanet );
  $parse['selector'] = ShowGalaxySelector ( $galaxy, $system );

  if ($mode == 2)
    $parse['selectorMI'] = ShowGalaxyMISelector ( $galaxy, $system, $planet, $CurrentPlanetID, $CurrentMIP );

  $parse['titles'] = ShowGalaxyTitles ( $galaxy, $system );
  $parse['rows']   = ShowGalaxyRows   ( $galaxy, $system );
  $parse['footer'] = ShowGalaxyFooter ( $galaxy, $system,  $CurrentMIP, $CurrentRC, $CurrentSP);

  display (parsetemplate(gettemplate('gal_main'), $parse), $lang['sys_universe'], true, '', false);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Created by Perberos
// 1.1 - Modified by -MoF- (UGamela germany)
// 1.2 - 1er Nettoyage Chlorel ...
// 1.3 - 2eme Nettoyage Chlorel ... Mise en fonction et debuging complet
?>