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
//  $CanDestroy    = $CurrentPlanet[$resource[213]] + $CurrentPlanet[$resource[214]];
  $CanDestroy = $CurrentPlanet[$resource[214]];

  $maxfleet       = doquery("SELECT * FROM {{table}} WHERE `fleet_owner` = '". $user['id'] ."';", 'fleets');
  $maxfleet_count = mysql_num_rows($maxfleet);

  CheckPlanetUsedFields($CurrentPlanet);
  CheckPlanetUsedFields($lunarow);

  // Imperatif, dans quel mode suis-je (pour savoir dans quel etat j'ere)

  if ($mode == 0) {
    // On vient du menu
    // Y a pas de parametres de passé
    // On met ce qu'il faut pour commencer là ou l'on se trouve

    $galaxy        = $CurrentPlanet['galaxy'];
    $system        = $CurrentPlanet['system'];
    $planet        = $CurrentPlanet['planet'];
  } elseif ($mode == 1) {
    // On vient du selecteur de galaxie
    // Il nous poste :
    // $_POST['galaxy']      => Galaxie affichée dans la case a saisir
    // $_POST['galaxyLeft']  => <- A ete cliqué
    // $_POST['galaxyRight'] => -> A ete cliqué
    // $_POST['system']      => Systeme affiché dans la case a saisir
    // $_POST['systemLeft']  => <- A ete cliqué
    // $_POST['systemRight'] => -> A ete cliqué

    if ($POST_galaxyLeft) {
      if ($POST_galaxy < 1) {
        $POST_galaxy = 1;
        $galaxy          = 1;
      } elseif ($POST_galaxy == 1) {
        $POST_galaxy = 1;
        $galaxy          = 1;
      } else {
        $galaxy = $POST_galaxy - 1;
      }
    } elseif ($POST_galaxyRight) {
      if ($POST_galaxy      > $config->game_maxGalaxy OR
        $POST_galaxyRight > $config->game_maxGalaxy) {
        $POST_galaxy      = $config->game_maxGalaxy;
        $POST_galaxyRight = $config->game_maxGalaxy;
        $galaxy               = $config->game_maxGalaxy;
      } elseif ($POST_galaxy == $config->game_maxGalaxy) {
        $POST_galaxy      = $config->game_maxGalaxy;
        $galaxy               = $config->game_maxGalaxy;
      } else {
        $galaxy = $POST_galaxy + 1;
      }
    } else {
      $galaxy = $POST_galaxy;
    }

    if ($POST_systemLeft) {
      if ($POST_system < 1) {
        $POST_system = 1;
        $system          = 1;
      } elseif ($POST_system == 1) {
        $POST_system = 1;
        $system          = 1;
      } else {
        $system = $POST_system - 1;
      }
    } elseif ($POST_systemRight) {
      if ($POST_system      > $config->game_maxSystem OR
        $POST_systemRight > $config->game_maxSystem) {
        $POST_system      = $config->game_maxSystem;
        $system               = $config->game_maxSystem;
      } elseif ($POST_system == $config->game_maxSystem) {
        $POST_system      = $config->game_maxSystem;
        $system               = $config->game_maxSystem;
      } else {
        $system = $POST_system + 1;
      }
    } else {
      $system = $POST_system;
    }
  } elseif ($mode == 2) {
    // Mais c'est qu'il mordrait !
    // A t'on idée de vouloir lancer des MIP sur ce pauvre bonhomme !!

    $galaxy        = $GET_galaxy;
    $system        = $GET_system;
    $planet        = $GET_planet;
  } elseif ($mode == 3) {
    // Appel depuis un menu avec uniquement galaxy et system de passé !
    $galaxy        = $GET_galaxy;
    $system        = $GET_system;
  } else {
    // Si j'arrive ici ...
    // C'est qu'il y a vraiment eu un bug
    $galaxy        = 1;
    $system        = 1;
  }

  $planetcount = 0;
  $lunacount   = 0;

  $page  = InsertGalaxyScripts ( $CurrentPlanet );

//  $page .= "<body style=\"overflow: hidden;\" onUnload=\"\"><br><br>";
//  $page .= "<body><br><br>";
  $page .= "<body>";
  $page .= ShowGalaxySelector ( $galaxy, $system );

  if ($mode == 2) {
    $page .= ShowGalaxyMISelector ( $galaxy, $system, $planet, $CurrentPlanetID, $CurrentMIP );
  }

  $page .= "<table width=569><tbody>";

  $page .= ShowGalaxyTitles ( $galaxy, $system );
    $page .= ShowGalaxyRows   ( $galaxy, $system );
    $page .= ShowGalaxyFooter ( $galaxy, $system,  $CurrentMIP, $CurrentRC, $CurrentSP);

  $page .= "</tbody></table></div>";

  display ($page, $lang[''], true, '', false);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Created by Perberos
// 1.1 - Modified by -MoF- (UGamela germany)
// 1.2 - 1er Nettoyage Chlorel ...
// 1.3 - 2eme Nettoyage Chlorel ... Mise en fonction et debuging complet
?>