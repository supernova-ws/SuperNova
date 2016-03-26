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
  $classLocale = classLocale::$lang;

  $unit_data = get_unit_param($unit_id);
  $unit_durability = $unit_data['shield'] + $unit_data['armor'];

  $str_rapid_from = '';
  $str_rapid_to = '';
  foreach(sn_get_groups(array('fleet', 'defense_active')) as $enemy_id) {
    $enemy_data = get_unit_param($enemy_id);
    $enemy_durability = $enemy_data['shield'] + $enemy_data['armor'];

    $rapid = floor($unit_data['attack'] * (isset($unit_data['amplify'][$enemy_id]) ? $unit_data['amplify'][$enemy_id] : 1) / $enemy_durability);
    if($rapid >= 1) {
      $str_rapid_to .= "{$classLocale['nfo_rf_again']} {$classLocale['tech'][$enemy_id]} <font color=\"#00ff00\">{$rapid}</font><br>";
    }

    $rapid = floor($enemy_data['attack'] * (isset($enemy_data['amplify'][$unit_id]) ? $enemy_data['amplify'][$unit_id] : 1) / $unit_durability);
    if($rapid >= 1) {
      $str_rapid_from .= "{$classLocale['tech'][$enemy_id]} {$classLocale['nfo_rf_from']} <font color=\"#ff0000\">{$rapid}</font><br>";
    }
  }

  if($str_rapid_to && $str_rapid_from) {
    $str_rapid_to .= '<hr>';
  }

  return array('to' => $str_rapid_to, 'from' => $str_rapid_from);
}

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
