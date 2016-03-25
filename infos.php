<?php

/**
 * infos.php
 *
 * Information about every aspect of in-game objects: buildings, officiers, techs etc
 *
 * @version 1.1st Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1
 * @copyright 2008 By Chlorel for XNova
 */
include('common.' . substr(strrchr(__FILE__, '.'), 1));

$unit_id = sys_get_param_id('gid');
if($unit_id == RES_DARK_MATTER) {
  sys_redirect('dark_matter.php');
}

if($unit_id == RES_METAMATTER) {
  sys_redirect('metamatter.php');
}

lng_include('infos');
if(!$unit_id || (!get_unit_param($unit_id) && !isset(classLocale::$lang['info'][$unit_id]))) {
  sys_redirect('index.php?page=techtree');
}

$template = gettemplate('novapedia', true);

$unit_data = get_unit_param($unit_id);
$unit_type = $unit_data['type'];

if($unit_type == UNIT_SHIPS) {
  $template_result['UNIT_IS_SHIP'] = true;

  $ship_data = get_ship_data($unit_id, $user);

  $template_result += array(
    'BASE_SPEED'         => pretty_number($ship_data['speed_base']),
    'ACTUAL_SPEED'       => pretty_number($ship_data['speed']),
    'BASE_CONSUMPTION'   => pretty_number($ship_data['consumption_base']),
    'ACTUAL_CONSUMPTION' => pretty_number($ship_data['consumption']),

    'BASE_CAPACITY'   => pretty_number($unit_data['capacity']),
    'ACTUAL_CAPACITY' => pretty_number($ship_data['capacity']),
  );

  $engine_template_info = array();
  foreach($unit_data['engine'] as $unit_engine_data) {
    $unit_engine_data = get_engine_data($user, $unit_engine_data);

    $engine_template_info[] = array(
      'NAME'               => classLocale::$lang['tech'][$unit_engine_data['tech']],
      'MIN_LEVEL'          => $unit_engine_data['min_level'],
      'USER_TECH_LEVEL'    => mrc_get_level($user, null, $unit_engine_data['tech']),
      'BASE_SPEED'         => pretty_number($unit_engine_data['speed_base']),
      'BASE_CONSUMPTION'   => pretty_number($unit_engine_data['consumption_base']),
      'ACTUAL_SPEED'       => pretty_number($unit_engine_data['speed']),
      'ACTUAL_CONSUMPTION' => pretty_number($unit_engine_data['consumption']),
    );
  }
  $template_result['.']['engine'] = $engine_template_info;

}


$sn_data_group_combat = sn_get_groups('combat');
if(in_array($unit_id, $sn_data_group_combat)) {
  $template_result['UNIT_IS_COMBAT'] = true;

  $unit_durability = $unit_data['shield'] + $unit_data['armor'];

  $volley_arr = $rapid_to = $rapid_from = array();
  $str_rapid_from = '';
  $str_rapid_to = '';
  foreach($sn_data_group_combat as $enemy_id) {
    $enemy_data = get_unit_param($enemy_id);
    $enemy_durability = $enemy_data['shield'] + $enemy_data['armor'];

    $rapid = $unit_data['attack'] * (isset($unit_data['amplify'][$enemy_id]) ? $unit_data['amplify'][$enemy_id] : 1) / $enemy_durability;
    if($rapid >= 1) {
      $volley_arr[$enemy_id]['TO'] = floor($rapid);
    }

    $rapid = $enemy_data['attack'] * (isset($enemy_data['amplify'][$unit_id]) ? $enemy_data['amplify'][$unit_id] : 1) / $unit_durability;
    if($rapid >= 1) {
      $volley_arr[$enemy_id]['FROM'] = floor($rapid);
    }
  }
  foreach($volley_arr as $enemy_id => &$rapid) {
    $rapid['ENEMY_ID'] = $enemy_id;
    $rapid['ENEMY_NAME'] = classLocale::$lang['tech'][$enemy_id];
  }
  $template_result['.']['volley'] = $volley_arr;

  $template_result += array(
    'BASE_ARMOR'  => pretty_number($unit_data['armor']),
    'BASE_SHIELD' => pretty_number($unit_data['shield']),
    'BASE_WEAPON' => pretty_number($unit_data['attack']),

    'ACTUAL_ARMOR'  => pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_ARMOR), $unit_data['armor'])),
    'ACTUAL_SHIELD' => pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_SHIELD), $unit_data['shield'])),
    'ACTUAL_WEAPON' => pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_WEAPON), $unit_data['attack'])),
  );

}

if(classLocale::$lang['info'][$unit_id]['effect']) {
  $template_result['UNIT_EFFECT'] = classLocale::$lang['info'][$unit_id]['effect'];
}

if($unit_data['bonus']) {
  $unit_bonus = !$unit_data['bonus'] || $unit_data['bonus_type'] == BONUS_ABILITY ? '' : (
    ($unit_data['bonus'] >= 0 ? '+' : '') . $unit_data['bonus'] . ($unit_data['bonus_type'] == BONUS_PERCENT ? '%' : '')
  );
  $template_result['UNIT_BONUS'] = $unit_bonus;
}

$template_result += array(
  'PAGE_HEADER' => classLocale::$lang['wiki_title'],

  'UNIT_ID'          => $unit_id,
  'UNIT_NAME'        => classLocale::$lang['tech'][$unit_id],
  'UNIT_TYPE'        => $unit_type,
  'UNIT_TYPE_NAME'   => classLocale::$lang['tech'][$unit_type],
  'UNIT_DESCRIPTION' => classLocale::$lang['info'][$unit_id]['description'],
);

$template_result['.']['require'] = unit_requirements_render($user, $planetrow, $unit_id);


$template->assign_recursive($template_result);
display($template);


// ----------------------------------------------------------------------------------------------------------
// Creation du tableau de production de ressources
// Tient compte du parametrage de la planete (si la production n'est pas affectée a 100% par exemple
// Tient compte aussi du multiplicateur de ressources
//
function ShowProductionTable($CurrentUser, $CurrentPlanet, $BuildID, $Template) {
  $config_resource_multiplier = game_resource_multiplier();
  $config_resource_multiplier_plain = game_resource_multiplier(true);

  $CurrentBuildtLvl = mrc_get_level($CurrentUser, $CurrentPlanet, $BuildID);

  $BuildLevel = ($CurrentBuildtLvl > 0) ? $CurrentBuildtLvl : 1;

  $modifiers = sn_get_groups('modifiers');

  $Prod[STRUC_MINE_METAL] = floor(mrc_modify_value(
    $CurrentUser,
    $CurrentPlanet,
    $modifiers[MODIFIER_RESOURCE_PRODUCTION],
    $config_resource_multiplier * $unit_data[P_UNIT_PRODUCTION][RES_METAL]($BuildLevel, 100, $CurrentUser, $CurrentPlanet)
  ));
  $Prod[STRUC_MINE_CRYSTAL] = floor(mrc_modify_value(
    $CurrentUser,
    $CurrentPlanet,
    $modifiers[MODIFIER_RESOURCE_PRODUCTION],
    $config_resource_multiplier * $unit_data[P_UNIT_PRODUCTION][RES_CRYSTAL]($BuildLevel, 100, $CurrentUser, $CurrentPlanet)
  ));
  $Prod[STRUC_MINE_DEUTERIUM] = floor(mrc_modify_value(
    $CurrentUser,
    $CurrentPlanet,
    $modifiers[MODIFIER_RESOURCE_PRODUCTION],
    $config_resource_multiplier * $unit_data[P_UNIT_PRODUCTION][RES_DEUTERIUM]($BuildLevel, 100, $CurrentUser, $CurrentPlanet)
  ));
  $Prod[STRUC_MINE_SOLAR] = floor(mrc_modify_value(
    $CurrentUser,
    $CurrentPlanet,
    $modifiers[MODIFIER_RESOURCE_PRODUCTION],
    $config_resource_multiplier_plain * $unit_data[P_UNIT_PRODUCTION][RES_ENERGY]($BuildLevel, 100, $CurrentUser, $CurrentPlanet)
  ));

  $ActualProd = floor($Prod[$BuildID]);
  if($BuildID != STRUC_MINE_FUSION) {
    $ActualNeed = floor($Prod[STRUC_MINE_SOLAR]);
  } else {
    $ActualNeed = floor($Prod[STRUC_MINE_DEUTERIUM]);
  }

  $BuildStartLvl = $CurrentBuildtLvl - 2;
  if($BuildStartLvl < 1) {
    $BuildStartLvl = 1;
  }
  $Table = '';
  $ProdFirst = 0;
  for($BuildLevel = $BuildStartLvl; $BuildLevel < $BuildStartLvl + 10; $BuildLevel++) {
    if($BuildID != STRUC_MOON_PHALANX) {
      $Prod[STRUC_MINE_METAL] = floor(mrc_modify_value(
        $CurrentUser,
        $CurrentPlanet,
        $modifiers[MODIFIER_RESOURCE_PRODUCTION],
        $config_resource_multiplier * $unit_data[P_UNIT_PRODUCTION][RES_METAL]($BuildLevel, 100, $CurrentUser, $CurrentPlanet)
      ));
      $Prod[STRUC_MINE_CRYSTAL] = floor(mrc_modify_value(
        $CurrentUser,
        $CurrentPlanet,
        $modifiers[MODIFIER_RESOURCE_PRODUCTION],
        $config_resource_multiplier * $unit_data[P_UNIT_PRODUCTION][RES_CRYSTAL]($BuildLevel, 100, $CurrentUser, $CurrentPlanet)
      ));
      $Prod[STRUC_MINE_DEUTERIUM] = floor(mrc_modify_value(
        $CurrentUser,
        $CurrentPlanet,
        $modifiers[MODIFIER_RESOURCE_PRODUCTION],
        $config_resource_multiplier * $unit_data[P_UNIT_PRODUCTION][RES_DEUTERIUM]($BuildLevel, 100, $CurrentUser, $CurrentPlanet)
      ));
      $Prod[STRUC_MINE_SOLAR] = floor(mrc_modify_value(
        $CurrentUser,
        $CurrentPlanet,
        $modifiers[MODIFIER_RESOURCE_PRODUCTION],
        $config_resource_multiplier_plain * $unit_data[P_UNIT_PRODUCTION][RES_ENERGY]($BuildLevel, 100, $CurrentUser, $CurrentPlanet)
      ));

      $bloc['build_lvl'] = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;
      if($ProdFirst > 0) {
        if($BuildID != STRUC_MINE_FUSION) {
          $bloc['build_gain'] = "<font color=\"lime\">(" . pretty_number(floor($Prod[$BuildID] - $ProdFirst)) . ")</font>";
        } else {
          $bloc['build_gain'] = "<font color=\"lime\">(" . pretty_number(floor($Prod[STRUC_MINE_SOLAR] - $ProdFirst)) . ")</font>";
        }
      } else {
        $bloc['build_gain'] = '';
      }
      if($BuildID != STRUC_MINE_FUSION) {
        $bloc['build_prod'] = pretty_number(floor($Prod[$BuildID]));
        $bloc['build_prod_diff'] = pretty_number(floor($Prod[$BuildID] - $ActualProd), true, true);
        $bloc['build_need'] = pretty_number(floor($Prod[STRUC_MINE_SOLAR]), true, true);
        $bloc['build_need_diff'] = pretty_number(floor($Prod[STRUC_MINE_SOLAR] - $ActualNeed), true, true);
      } else {
        $bloc['build_prod'] = pretty_number(floor($Prod[STRUC_MINE_SOLAR]));
        $bloc['build_prod_diff'] = pretty_number(floor($Prod[STRUC_MINE_SOLAR] - $ActualProd), true, true);
        $bloc['build_need'] = pretty_number(floor($Prod[STRUC_MINE_DEUTERIUM]), true, true);
        $bloc['build_need_diff'] = pretty_number(floor($Prod[STRUC_MINE_DEUTERIUM] - $ActualNeed), true, true);
      }
      if($ProdFirst == 0) {
        if($BuildID != STRUC_MINE_FUSION) {
          $ProdFirst = floor($Prod[$BuildID]);
        } else {
          $ProdFirst = floor($Prod[STRUC_MINE_SOLAR]);
        }
      }
    } else {
      // Cas particulier de la phalange
      $bloc['build_lvl'] = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;
      $bloc['build_range'] = ($BuildLevel * $BuildLevel) - 1;
    }
    $Table .= parsetemplate($Template, $bloc);
  }

  return $Table;
}

function eco_render_rapid_fire($unit_id) {
  $unit_data = get_unit_param($unit_id);
  $unit_durability = $unit_data['shield'] + $unit_data['armor'];

  $str_rapid_from = '';
  $str_rapid_to = '';
  foreach(sn_get_groups(array('fleet', 'defense_active')) as $enemy_id) {
    $enemy_data = get_unit_param($enemy_id);
    $enemy_durability = $enemy_data['shield'] + $enemy_data['armor'];

    $rapid = floor($unit_data['attack'] * (isset($unit_data['amplify'][$enemy_id]) ? $unit_data['amplify'][$enemy_id] : 1) / $enemy_durability);
    if($rapid >= 1) {
      $classLocale2 = classLocale::$lang;
      $classLocale3 = classLocale::$lang;
      $str_rapid_to .= "{$classLocale2['nfo_rf_again']} {$classLocale3['tech'][$enemy_id]} <font color=\"#00ff00\">{$rapid}</font><br>";
    }

    $rapid = floor($enemy_data['attack'] * (isset($enemy_data['amplify'][$unit_id]) ? $enemy_data['amplify'][$unit_id] : 1) / $unit_durability);
    if($rapid >= 1) {
      $classLocale = classLocale::$lang;
      $classLocale1 = classLocale::$lang;
      $str_rapid_from .= "{$classLocale1['tech'][$enemy_id]} {$classLocale['nfo_rf_from']} <font color=\"#ff0000\">{$rapid}</font><br>";
    }
  }

  if($str_rapid_to && $str_rapid_from) {
    $str_rapid_to .= '<hr>';
  }

  return array('to' => $str_rapid_to, 'from' => $str_rapid_from);
}

// ----------------------------------------------------------------------------------------------------------
// Construit la page par rapport a l'information demandée ...
// Permet de faire la differance entre les divers types et les pages speciales
//
$unit_id = sys_get_param_int('gid');

$unit_data = get_unit_param($unit_id);

lng_include('infos');

$DestroyTPL = '';
$TableHeadTPL = '';

$parse = classLocale::$lang;
// Données de base
$parse['dpath'] = $dpath;
$parse['name'] = classLocale::$lang['tech'][$unit_id];
$parse['image'] = $unit_id;
$parse['description'] = classLocale::$lang['info'][$unit_id]['description'];

$unit_info = get_unit_param($unit_id);

if($unit_id >= 1 && $unit_id <= 3) {
  // Cas des mines
  $PageTPL = gettemplate('info_buildings_table');
  $DestroyTPL = gettemplate('info_buildings_destroy');
  $TableHeadTPL = "<tr><td class=\"c\">{nfo_level}</td><td class=\"c\">{nfo_prod_p_hour}</td><td class=\"c\">{nfo_difference}</td><td class=\"c\">{nfo_used_energy}</td><td class=\"c\">{nfo_difference}</td></tr>";
  $TableTPL = "<tr><th>{build_lvl}</th><th>{build_prod} {build_gain}</th><th>{build_prod_diff}</th><th>{build_need}</th><th>{build_need_diff}</th></tr>";
} elseif($unit_id == 4) {
  // Centrale Solaire
  $PageTPL = gettemplate('info_buildings_table');
  $DestroyTPL = gettemplate('info_buildings_destroy');
  $TableHeadTPL = "<tr><td class=\"c\">{nfo_level}</td><td class=\"c\">{nfo_prod_energy}</td><td class=\"c\">{nfo_difference}</td></tr>";
  $TableTPL = "<tr><th>{build_lvl}</th><th>{build_prod} {build_gain}</th><th>{build_prod_diff}</th></tr>";
} elseif($unit_id == STRUC_MINE_FUSION) {
  // Centrale Fusion
  $PageTPL = gettemplate('info_buildings_table');
  $DestroyTPL = gettemplate('info_buildings_destroy');
  $TableHeadTPL = "<tr><td class=\"c\">{nfo_level}</td><td class=\"c\">{nfo_prod_energy}</td><td class=\"c\">{nfo_difference}</td><td class=\"c\">{nfo_used_deuter}</td><td class=\"c\">{nfo_difference}</td></tr>";
  $TableTPL = "<tr><th>{build_lvl}</th><th>{build_prod} {build_gain}</th><th>{build_prod_diff}</th><th>{build_need}</th><th>{build_need_diff}</th></tr>";
} elseif($unit_id >= STRUC_FACTORY_ROBOT && $unit_id <= 32) {
  // Batiments Generaux
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
} elseif($unit_id == STRUC_TERRAFORMER) {
  // Batiments Terraformer
  $PageTPL = gettemplate('info_buildings_general');
} elseif($unit_id == STRUC_ALLY_DEPOSIT) {
  // Dépot d'alliance
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
} elseif($unit_id == STRUC_LABORATORY_NANO) {
  // nano
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
} elseif($unit_id == STRUC_SILO) {
  // Silo de missiles
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
} elseif($unit_id == STRUC_MOON_STATION) {
  // Batiments lunaires
  $PageTPL = gettemplate('info_buildings_general');
} elseif($unit_id == STRUC_MOON_PHALANX) {
  // Phalange
  $PageTPL = gettemplate('info_buildings_table');
  $TableHeadTPL = "<tr><td class=\"c\">{nfo_level}</td><td class=\"c\">{nfo_range}</td></tr>";
  $TableTPL = "<tr><th>{build_lvl}</th><th>{build_range}</th></tr>";
  $DestroyTPL = gettemplate('info_buildings_destroy');
} elseif($unit_id == STRUC_MOON_GATE) {
  // Porte de Saut
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
} elseif(in_array($unit_id, sn_get_groups('tech'))) {
  // Laboratoire
  $PageTPL = gettemplate('info_buildings_general');
} elseif(in_array($unit_id, sn_get_groups('fleet'))) {
  // Flotte

  $PageTPL = gettemplate('info_buildings_fleet');

  $parse['element_typ'] = classLocale::$lang['tech'][UNIT_SHIPS];
  $rapid_fire = eco_render_rapid_fire($unit_id);
  $parse['rf_info_to'] = $rapid_fire['to'];   // Rapid Fire vers
  $parse['rf_info_fr'] = $rapid_fire['from']; // Rapid Fire de

  $parse['hull_pt'] = pretty_number(($unit_info['metal'] + $unit_info['crystal']) / 10); // Points de Structure
  $parse['shield_pt'] = pretty_number($unit_info['shield']);  // Points de Bouclier
  $parse['attack_pt'] = pretty_number($unit_info['attack']);  // Points d'Attaque
  $parse['capacity_pt'] = pretty_number($unit_info['capacity']); // Capacitée de fret
  $parse['base_speed'] = pretty_number($unit_info['engine'][0]['speed']);    // Vitesse de base
  $parse['base_conso'] = pretty_number($unit_info['engine'][0]['consumption']);  // Consommation de base

  $parse['ACTUAL_ARMOR'] = pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_ARMOR), ($unit_info['metal'] + $unit_info['crystal']) / 10));
  $parse['ACTUAL_SHIELD'] = pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_SHIELD), $unit_info['shield']));
  $parse['ACTUAL_WEAPON'] = pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_WEAPON), $unit_info['attack']));

  $ship_data = get_ship_data($unit_id, $user);
  $parse['ACTUAL_CAPACITY'] = pretty_number($ship_data['capacity']);
  $parse['ACTUAL_SPEED'] = pretty_number($ship_data['speed']);
  $parse['ACTUAL_CONSUMPTION'] = pretty_number($ship_data['consumption']);
  if(count($unit_info['engine']) > 1) {
    $parse['upd_speed'] = "<font color=\"yellow\">(" . pretty_number($unit_info['engine'][1]['speed']) . ")</font>";       // Vitesse rééquipée
    $parse['upd_conso'] = "<font color=\"yellow\">(" . pretty_number($unit_info['engine'][1]['consumption']) . ")</font>"; // Consommation apres rééquipement
  }
} elseif(in_array($unit_id, sn_get_groups('defense_active'))) {
  // Defenses
  $PageTPL = gettemplate('info_buildings_defense');
  $parse['element_typ'] = classLocale::$lang['tech'][UNIT_DEFENCE];

  $rapid_fire = eco_render_rapid_fire($unit_id);
  $parse['rf_info_to'] = $rapid_fire['to'];   // Rapid Fire vers
  $parse['rf_info_fr'] = $rapid_fire['from']; // Rapid Fire de

  $parse['hull_pt'] = pretty_number(($unit_info['metal'] + $unit_info['crystal']) / 10); // Points de Structure
  $parse['shield_pt'] = pretty_number($unit_info['shield']);  // Points de Bouclier
  $parse['attack_pt'] = pretty_number($unit_info['attack']);  // Points d'Attaque
} elseif(in_array($unit_id, sn_get_groups('missile'))) {
  // Misilles
  $PageTPL = gettemplate('info_buildings_defense');
  $parse['element_typ'] = classLocale::$lang['tech'][UNIT_DEFENCE];
  $parse['hull_pt'] = pretty_number($unit_info['metal'] + $unit_info['crystal']); // Points de Structure
  $parse['shield_pt'] = pretty_number($unit_info['shield']);  // Points de Bouclier
  $parse['attack_pt'] = pretty_number($unit_info['attack']);  // Points d'Attaque
} elseif(in_array($unit_id, sn_get_groups(array('mercenaries', 'governors', 'artifacts', 'resources_all')))) {
  // Officiers
  $PageTPL = gettemplate('info_officiers_general');

  $mercenary = $unit_info;
  $mercenary_bonus = $mercenary['bonus'];
  $mercenary_bonus = $mercenary_bonus >= 0 ? "+{$mercenary_bonus}" : "{$mercenary_bonus}";
  switch($mercenary['bonus_type']) {
    case BONUS_PERCENT:
      $mercenary_bonus = "{$mercenary_bonus}%";
      break;

    case BONUS_ADD:
      break;

    case BONUS_ABILITY:
      $mercenary_bonus = '';
      break;

    default:
      break;
  }

  $parse['EFFECT'] = classLocale::$lang['info'][$unit_id]['effect'];
  $parse['mercenary_bonus'] = $mercenary_bonus;
  if(!in_array($unit_id, sn_get_groups(array('artifacts', 'resources_all')))) {
    $parse['max_level'] = classLocale::$lang['sys_level'] . ' ' .
      (in_array($unit_id, sn_get_groups('mercenaries')) ? mrc_get_level($user, $planetrow, $unit_id) : ($mercenary['location'] == LOC_USER ? mrc_get_level($user, null, $unit_id) : ($planetrow['PLANET_GOVERNOR_ID'] == $unit_id ? $planetrow['PLANET_GOVERNOR_LEVEL'] : 0)))
      . (isset($mercenary['max']) ? "/{$mercenary['max']}" : '');
  }
}

// ---- Tableau d'evolution
if($TableHeadTPL != '') {
  $parse['table_head'] = parsetemplate($TableHeadTPL, classLocale::$lang);
  $parse['table_data'] = ShowProductionTable($user, $planetrow, $unit_id, $TableTPL);
}

// La page principale
$page = parsetemplate($PageTPL, $parse);

display($page, classLocale::$lang['nfo_page_title']);

// -----------------------------------------------------------------------------------------------------------
// History version
// 2.0 - Using sn_timer instead of script generated by InsertScriptChronoApplet
// 1.1 - Ajout JumpGate pour la porte de saut comme la présente OGame ... Enfin un peu mieux quand meme !
// 1.0 - Réécriture (réinventation de l'eau tiède)
