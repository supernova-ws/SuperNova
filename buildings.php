<?php

/**
 * buildings.php
 *
 *  Allow building of... hmm... buildings
 *
 * @version 1.3s Security checks by Gorlum for http://supernova.ws
 * @version 1.3
// History version
// 1.0 - Nettoyage modularisation
// 1.1 - Mise au point, mise en fonction pour linéarisation du fonctionnement
// 1.2 - Liste de construction batiments
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

check_urlaubmodus ($user);

$mode = sys_get_param_escaped('mode');

includeLang('buildings');
includeLang('infos');

// Mise a jour de la liste de construction si necessaire
UpdatePlanetBatimentQueueList ( $planetrow, $user );

$IsWorking = HandleTechnologieBuild ( $planetrow, $user );

PlanetResourceUpdate($user, $planetrow, $time_now);

switch ($mode)
{
  case 'fleet':
    // --------------------------------------------------------------------------------------------------
    FleetBuildingPage ( $planetrow, $user );
  break;

  case 'research':
    // --------------------------------------------------------------------------------------------------
    ResearchBuildingPage ( $planetrow, $user, $IsWorking['OnWork'], $IsWorking['WorkOn'] );
  break;

  case 'defense':
    // --------------------------------------------------------------------------------------------------
    DefensesBuildingPage ( $planetrow, $user );
  break;

  case 'test':
    eco_build(QUE_STRUCTURES, $user, $planetrow);
  break;

  case 'buildings':
  default:
    // --------------------------------------------------------------------------------------------------
    BatimentBuildingPage ( $planetrow, $user );
    // eco_build(QUE_STRUCTURES, $user, $planetrow);
  break;
}

?>
