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
// 1.1 - Mise au point, mise en fonction pour linarisation du fonctionnement
// 1.2 - Liste de construction batiments
 * @copyright 2008 by Chlorel for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

define('SN_RENDER_NAVBAR_PLANET', true);

$mode = sys_get_param_escaped('mode');

lng_include('buildings');
lng_include('infos');
$mode = (!$mode || $mode == 'buildings') ? QUE_STRUCTURES : ($mode == 'fleet' ? SUBQUE_FLEET : ($mode == 'defense' ? SUBQUE_DEFENSE : $mode));

sn_sys_sector_buy('buildings.php?mode=' . $mode);

switch ($mode)
{
  case 'research':
  case QUE_RESEARCH:
    require_once('includes/includes/eco_bld_structures.php');
    eco_build(QUE_RESEARCH, $user, $planetrow);
//    require_once('includes/includes/eco_bld_tech.php');
//    eco_bld_tech(QUE_RESEARCH, $user, $planetrow);
  break;

  case SUBQUE_FLEET:
  case SUBQUE_DEFENSE:
    require_once('includes/includes/eco_bld_structures.php');
    eco_build($mode, $user, $planetrow);
//    require_once('includes/includes/eco_bld_hangar.php');
//    eco_bld_hangar($mode, $user, $planetrow);
  break;

  case QUE_STRUCTURES:
  default:
    require_once('includes/includes/eco_bld_structures.php');
    eco_build(QUE_STRUCTURES, $user, $planetrow);
  break;
}
