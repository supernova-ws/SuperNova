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
$mode = (!$mode || $mode == 'buildings') ? QUE_STRUCTURES : ($mode == 'fleet' ? SUBQUE_FLEET : ($mode == 'defense' ? SUBQUE_DEFENSE : ($mode == 'research' ? QUE_RESEARCH : $mode)));

if($building_sort = sys_get_param_id('sort_elements')) {
  if(!empty($lang['player_option_building_sort'][$building_sort])) {
    classSupernova::$user_options[array(PLAYER_OPTION_BUILDING_SORT, $mode)] = $building_sort;
    classSupernova::$user_options[array(PLAYER_OPTION_BUILDING_SORT_INVERSE, $mode)] = sys_get_param_id('sort_elements_inverse', 0);
  }
  die();
}

lng_include('buildings');
lng_include('infos');

sn_sys_sector_buy('buildings.php?mode=' . $mode);

require_once('includes/includes/eco_bld_structures.php');
switch ($mode) {
//  case UNIT_MERCENARIES:
//    require_once('includes/includes/eco_bld_structures.php');
//    eco_build(QUE_MERCENARY, $user, $planetrow);
//  break;

  case QUE_RESEARCH:
    eco_build(QUE_RESEARCH, $user, $planetrow);
  break;

  case SUBQUE_FLEET:
  case SUBQUE_DEFENSE:
    eco_build($mode, $user, $planetrow);
  break;

  case QUE_STRUCTURES:
  default:
    eco_build(QUE_STRUCTURES, $user, $planetrow);
  break;
}
