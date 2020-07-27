<?php

/**
 * eco_build.php
 *
 * @version 1.5 - Using PTE (not everywhere) by Gorlum for http://supernova.ws
 * @version 1.4 - Complying with PCG by Gorlum for http://supernova.ws
 * @version 1.3 - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * // 1.0 Mise en module initiale (creation)
 * // 1.1 FIX interception cheat +1
 * // 1.2 FIX interception cheat destruction a -1
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */

function eco_build($que_type, &$user, &$planet) { return sn_function_call('eco_build', array($que_type, &$user, &$planet)); }

function sn_eco_build($que_type, &$auser, &$planet) {
  global $lang, $config, $template_result;

  if($ally_id = sys_get_param_id('ally_id')) {
    define('SN_IN_ALLY', true);
    $ranks = \Alliance\Alliance::ally_get_ranks($auser['ally']);
    if($ranks[$auser['ally_rank_id']]['admin'] || $auser['ally']['ally_owner'] == $auser['id']) {
      $user = &$auser['ally']['player'];
      $planet = array(
        'metal'     => $user['metal'],
        'crystal'   => $user['crystal'],
        'deuterium' => $user['deuterium'],
      );
    }
  }

  if(!$user) {
    $user = &$auser;
  }

  switch($action = sys_get_param_escaped('action')) {
    case 'create': // Add unit to que for build
    case 'create_autoconvert': // Add unit to que for build
    case 'destroy': // Add unit to que for remove
      $operation_result = que_build($user, $planet, $action == 'destroy' ? BUILD_DESTROY : ($action == 'create' ? BUILD_CREATE : BUILD_AUTOCONVERT));
    break;

    case 'trim':
      que_delete($que_type, $user, $planet, false);
    break;
    case 'clear':
      que_delete($que_type, $user, $planet, true);
    break;
  }

  $group_missile = sn_get_groups('missile');
  $silo_capacity_free = 0;
  if($que_type == QUE_STRUCTURES) {
    $build_unit_list = sn_get_groups('build_allow');
    $build_unit_list = $build_unit_list[$planet['planet_type']];
    $artifact_id = ART_NANO_BUILDER;
    $page_header = $lang['tech'][UNIT_STRUCTURES];
  } elseif($que_type == QUE_RESEARCH) {
    if(!mrc_get_level($user, $planet, STRUC_LABORATORY)) {
      SnTemplate::messageBox($lang['no_laboratory'], $lang['tech'][UNIT_TECHNOLOGIES]);
    }

    if(eco_unit_busy($user, $planet, UNIT_TECHNOLOGIES)) {
      SnTemplate::messageBox($lang['eco_bld_msg_err_laboratory_upgrading'], $lang['tech'][UNIT_TECHNOLOGIES]);
    }
    $build_unit_list = sn_get_groups('tech');
    $artifact_id = ART_HEURISTIC_CHIP;
    $page_header = $lang['eco_bld_research_page_name'] . ($user['user_as_ally'] ? "&nbsp;{$lang['sys_of_ally']}&nbsp;{$user['username']}" : '');
  } elseif($que_type == QUE_MERCENARY) {
//    if(!mrc_get_level($user, $planet, STRUC_LABORATORY)) {
//      messageBox($lang['no_laboratory'], $lang['tech'][UNIT_TECHNOLOGIES]);
//    }

//    if(eco_unit_busy($user, $planet, UNIT_TECHNOLOGIES)) {
//      messageBox($lang['eco_bld_msg_err_laboratory_upgrading'], $lang['tech'][UNIT_TECHNOLOGIES]);
//    }
    $build_unit_list = sn_get_groups('mercenaries');
    $artifact_id = 0;
    $page_header = $lang['tech'][UNIT_MERCENARIES] . ($user['user_as_ally'] ? "&nbsp;{$lang['sys_of_ally']}&nbsp;{$user['username']}" : '');
  } else {
    if(mrc_get_level($user, $planet, STRUC_FACTORY_HANGAR) == 0) {
      SnTemplate::messageBox($lang['need_hangar'], $lang['tech'][STRUC_FACTORY_HANGAR]);
    }

    $build_unit_list = sn_get_groups($page_mode = $que_type == SUBQUE_FLEET ? 'fleet' : 'defense');
    $page_header = $lang[$page_mode];
    $artifact_id = 0;

    $silo_capacity_free = mrc_get_level($user, $planet, STRUC_SILO) * get_unit_param(STRUC_SILO, P_CAPACITY);
    foreach($group_missile as $unit_id) {
      $silo_capacity_free -= (mrc_get_level($user, $planet, $unit_id, false, true) + (isset($in_que[$unit_id]) && $in_que[$unit_id] ? $in_que[$unit_id] : 0)) * get_unit_param($unit_id, P_UNIT_SIZE);
    }
    $silo_capacity_free = max(0, $silo_capacity_free);
  }

  // Caching values that used more then one time into local variables
  $config_resource_multiplier = game_resource_multiplier();
  $config_resource_multiplier_plain = game_resource_multiplier(true);

  /*
  // Code for fully working new que system
  $hangar_busy = count($que['que'][QUE_HANGAR]);
  $lab_busy    = count($que['que'][QUE_RESEARCH]) && !$config->BuildLabWhileRun;
  */

  $template = SnTemplate::gettemplate('buildings_builds', true);
  if(!empty($operation_result)) {
    $template_result['.']['result'][] = $operation_result;
  }

  $planet_id = $que_type == QUE_RESEARCH || $que_type == QUE_MERCENARY ? 0 : $planet['id'];

  $ques = que_get($user['id'], $planet_id, $que_type);
  $in_que = &$ques['in_que'][$que_type][$user['id']][$planet_id];
  $que = &$ques['ques'][$que_type][$user['id']][$planet_id];
  que_tpl_parse($template, $que_type, $user, $planet, $que);

  $que_length = count($que);
  $can_que_element = $que_length < que_get_max_que_length($user, $planet, $que_type);

  $fleet_list = flt_get_fleets_to_planet($planet);

  $planet_fields_max = eco_planet_fields_max($planet);
  $planet_fields_current = $planet['field_current'];
  $planet_fields_que = is_array($in_que) ? -array_sum($in_que) : 0;
  $planet_fields_free = max(0, $planet_fields_max - $planet_fields_current + $planet_fields_que);
  $planet_fields_queable = $que_type != QUE_STRUCTURES || $planet_fields_free > 0;
  $sn_modifiers_resource = sn_get_groups(GROUP_MODIFIERS_NAME);
  $sn_modifiers_resource = $sn_modifiers_resource[MODIFIER_RESOURCE_PRODUCTION];
  $sn_groups_density = sn_get_groups('planet_density');
  $density_info = $sn_groups_density[$planet['density_index']][UNIT_RESOURCES];

  $user_dark_matter = mrc_get_level($user, null, RES_DARK_MATTER);

  $record_index = 0;

  foreach($build_unit_list as $unit_id) {
    $level_base = mrc_get_level($user, $planet, $unit_id, false, true);
    $level_effective = mrc_get_level($user, $planet, $unit_id);
    $level_in_que = $in_que[$unit_id];
    $level_bonus = max(0, $level_effective - $level_base);
    $level_base_and_que = $level_base + $level_in_que;

    $unit_info = get_unit_param($unit_id);
    $unit_stackable = isset($unit_info[P_STACKABLE]) && $unit_info[P_STACKABLE];

    $build_data = eco_get_build_data($user, $planet, $unit_id, $level_base_and_que);
    $temp[RES_METAL] = floor($planet['metal'] + $fleet_list['own']['total'][RES_METAL] - $build_data[BUILD_CREATE][RES_METAL]);
    $temp[RES_CRYSTAL] = floor($planet['crystal'] + $fleet_list['own']['total'][RES_CRYSTAL] - $build_data[BUILD_CREATE][RES_CRYSTAL]);
    $temp[RES_DEUTERIUM] = floor($planet['deuterium'] + $fleet_list['own']['total'][RES_DEUTERIUM] - $build_data[BUILD_CREATE][RES_DEUTERIUM]);
    $temp[RES_DARK_MATTER] = floor($user_dark_matter - $build_data[BUILD_CREATE][RES_DARK_MATTER]);

    $build_data['RESULT'][BUILD_CREATE] = $build_data['RESULT'][BUILD_CREATE] == BUILD_ALLOWED && !$can_que_element ? BUILD_QUE_FULL : $build_data['RESULT'][BUILD_CREATE];


    // Restricting $can_build by resources on planet and (where applicable) with max count per unit
    $can_build = $unit_info[P_MAX_STACK] ? max(0, $unit_info[P_MAX_STACK] - $level_in_que - $level_effective) : $build_data['CAN'][BUILD_CREATE];
    // Restricting $can_build by free silo capacity
    $can_build = ($unit_is_missile = in_array($unit_id, $group_missile)) ? min($can_build, floor($silo_capacity_free / $unit_info[P_UNIT_SIZE])) : $can_build;
    if(!$can_build) {
      if(!$build_data['CAN'][BUILD_CREATE]) {
        $build_data['RESULT'][BUILD_CREATE] = BUILD_NO_RESOURCES;
      } elseif($unit_is_missile && $silo_capacity_free < $unit_info[P_UNIT_SIZE]) {
        $build_data['RESULT'][BUILD_CREATE] = BUILD_SILO_FULL;
      } elseif($unit_info[P_MAX_STACK]) {
        $build_data['RESULT'][BUILD_CREATE] = BUILD_MAX_REACHED;
      }
    }

    $unit_info['type'] == UNIT_STRUCTURES && !$planet_fields_queable ? $build_data['RESULT'][BUILD_CREATE] = BUILD_SECTORS_NONE : false;
    $unit_autoconvert_can = !SN::$user_options[PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE] && $build_data['RESULT'][BUILD_CREATE] == BUILD_NO_RESOURCES && $build_data[BUILD_AUTOCONVERT];


    $unit_autoconvert_can ? $build_data['RESULT'][BUILD_CREATE] = BUILD_AUTOCONVERT_AVAILABLE : false;

    $build_result_text = $lang['sys_build_result'][$build_data['RESULT'][BUILD_CREATE]];
    $build_result_text = !is_array($build_result_text) ? $build_result_text : (isset($build_result_text[$unit_id]) ? $build_result_text[$unit_id] : $build_result_text[0]);

    $production = array(
      '__INDEX'     => $record_index++,
      'ID'          => $unit_id,
      'NAME'        => $lang['tech'][$unit_id],
      'DESCRIPTION' => $lang['info'][$unit_id]['description_short'],
      'UNIT_TYPE'   => $unit_info[P_UNIT_TYPE],

      'LEVEL_OLD'   => $level_base,
      'LEVEL_BONUS' => $level_bonus,
      'LEVEL_NEXT'  => $level_base + $level_in_que + 1,
      'LEVEL_QUED'  => $level_in_que,
      'LEVEL'       => $level_base_and_que,

      'CAN_BUILD'          => $can_build,
      'CAN_AUTOCONVERT'    => $unit_autoconvert_can,
      'AUTOCONVERT_AMOUNT' => $build_data[BUILD_AUTOCONVERT],

      'BUILD_CAN'        => $build_data['CAN'][BUILD_CREATE],
      'TIME'             => pretty_time($build_data[RES_TIME][BUILD_CREATE]),
      'TIME_SECONDS'     => $build_data[RES_TIME][BUILD_CREATE],
      'METAL'            => $build_data[BUILD_CREATE][RES_METAL],
      'METAL_TEXT'       => prettyNumberStyledCompare($build_data[BUILD_CREATE][RES_METAL], $planet['metal']),
      'CRYSTAL'          => $build_data[BUILD_CREATE][RES_CRYSTAL],
      'CRYSTAL_TEXT'     => prettyNumberStyledCompare($build_data[BUILD_CREATE][RES_CRYSTAL], $planet['crystal']),
      'DEUTERIUM'        => $build_data[BUILD_CREATE][RES_DEUTERIUM],
      'DEUTERIUM_TEXT'   => prettyNumberStyledCompare($build_data[BUILD_CREATE][RES_DEUTERIUM], $planet['deuterium']),
      'ENERGY'           => $build_data[BUILD_CREATE][RES_ENERGY],
      'DARK_MATTER'      => $build_data[BUILD_CREATE][RES_DARK_MATTER],
      'DARK_MATTER_ONLY' => $build_data[P_OPTIONS][P_ONLY_DARK_MATTER],

      'BUILD_RESULT'      => $build_data['RESULT'][BUILD_CREATE],
      'BUILD_RESULT_TEXT' => $build_result_text,

      'DESTROY_RESULT'    => $build_data['RESULT'][BUILD_DESTROY],
      'DESTROY_CAN'       => $build_data['CAN'][BUILD_DESTROY],
      'DESTROY_TIME'      => pretty_time($build_data[RES_TIME][BUILD_DESTROY]),
      'DESTROY_METAL'     => $build_data[BUILD_DESTROY][RES_METAL],
      'DESTROY_CRYSTAL'   => $build_data[BUILD_DESTROY][RES_CRYSTAL],
      'DESTROY_DEUTERIUM' => $build_data[BUILD_DESTROY][RES_DEUTERIUM],

//      'METAL_REST'           => prettyNumberStyledDefault($temp[RES_METAL]),
//      'CRYSTAL_REST'         => prettyNumberStyledDefault($temp[RES_CRYSTAL]),
//      'DEUTERIUM_REST'       => prettyNumberStyledDefault($temp[RES_DEUTERIUM]),
//      'DARK_MATTER_REST'     => prettyNumberStyledDefault($temp[RES_DARK_MATTER]),
      'METAL_REST_NUM'       => $temp[RES_METAL],
      'CRYSTAL_REST_NUM'     => $temp[RES_CRYSTAL],
      'DEUTERIUM_REST_NUM'   => $temp[RES_DEUTERIUM],
      'DARK_MATTER_REST_NUM' => $temp[RES_DARK_MATTER],

      'UNIT_BUSY' => eco_unit_busy($user, $planet, $que, $unit_id),

      'MAP_IS_RESOURCE' => !empty($unit_info[P_UNIT_PRODUCTION]),
    );

    if($unit_stackable) {
      $level_production_base = array(
        'ACTUAL_SHIELD' => HelperString::numberFloorAndFormat(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_SHIELD), $unit_info['shield'])),
        'ACTUAL_ARMOR'  => HelperString::numberFloorAndFormat(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_ARMOR), $unit_info['armor'])),
        'ACTUAL_WEAPON' => HelperString::numberFloorAndFormat(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_WEAPON), $unit_info['attack'])),
      );

      if($unit_info[P_UNIT_TYPE] == UNIT_SHIPS) {
        $ship_data = get_ship_data($unit_id, $user);

        $level_production_base += array(
          'ACTUAL_SPEED'       => HelperString::numberFloorAndFormat($ship_data['speed']),
          'ACTUAL_CONSUMPTION' => HelperString::numberFloorAndFormat($ship_data['consumption']),
          'ACTUAL_CAPACITY'    => HelperString::numberFloorAndFormat($ship_data['capacity']),
        );
      }

      if($unit_info[P_UNIT_PRODUCTION]) {
        foreach($unit_info[P_UNIT_PRODUCTION] as $resource_id => $resource_calc) {
          if($resource_income =
            floor(mrc_modify_value($user, $planet, $sn_modifiers_resource, $resource_calc(1, 10, $user, $planet)
              * ($resource_id == RES_ENERGY ? $config_resource_multiplier_plain : $config_resource_multiplier)
              * (isset($density_info[$resource_id]) ? $density_info[$resource_id] : 1)))
          ) {
            $level_production_base['R' . $resource_id] = $resource_income;
          }
        }
      }
      $production['.']['resource'][] = $level_production_base;
    } elseif($unit_info[P_UNIT_PRODUCTION]) {
      $level_production_base = array();
      $element_level_start = $level_effective + $in_que[$unit_id];
      foreach($unit_info[P_UNIT_PRODUCTION] as $resource_id => $resource_calc) {
        if($resource_income =
          floor(mrc_modify_value($user, $planet, $sn_modifiers_resource, $resource_calc($element_level_start, 10, $user, $planet)
            * ($resource_id == RES_ENERGY ? $config_resource_multiplier_plain : $config_resource_multiplier)
            * (isset($density_info[$resource_id]) ? $density_info[$resource_id] : 1)))
        ) {
          $level_production_base[$resource_id] = $resource_income;
        }
      }

      $level_start = $level_base_and_que > 1 ? $level_effective + $level_in_que - 1 : 1;
      for($i = 0; $i < 6; $i++) {
        $level_production = array('LEVEL' => $level_start + $i);
        foreach($unit_info[P_UNIT_PRODUCTION] as $resource_id => $resource_calc) {
          if(
          $resource_income = floor(mrc_modify_value($user, $planet, $sn_modifiers_resource, $resource_calc($level_start + $i, 10, $user, $planet)
            * ($resource_id == RES_ENERGY ? $config_resource_multiplier_plain : $config_resource_multiplier)
            * (isset($density_info[$resource_id]) ? $density_info[$resource_id] : 1)))
          ) {
            $level_production['R' . $resource_id] = $resource_income;
            $level_production['D' . $resource_id] = $resource_income - $level_production_base[$resource_id];
            if($level_production['D' . $resource_id] == 0) {
              $level_production['D' . $resource_id] = '-';
            }
          }
        }
        $production['.']['resource'][] = $level_production;
      }
    } elseif($unit_id == TECH_ASTROTECH) {
      $element_level_start = $level_effective + $in_que[$unit_id];
      $level_production_base = array(
        UNIT_PLAYER_EXPEDITIONS_MAX => get_player_max_expeditons($user, $element_level_start),
        UNIT_PLAYER_COLONIES_MAX    => get_player_max_colonies($user, $element_level_start),
      );

      $level_start = $level_base_and_que > 1 ? $level_effective + $level_in_que - 1 : 1;
      for($i = 0; $i < 6; $i++) {
        $level_production = array('LEVEL' => $level_start + $i);
        $level_production['R' . UNIT_PLAYER_EXPEDITIONS_MAX] = get_player_max_expeditons($user, $level_start + $i);
        $level_production['D' . UNIT_PLAYER_EXPEDITIONS_MAX] = $level_production['R' . UNIT_PLAYER_EXPEDITIONS_MAX] - $level_production_base[UNIT_PLAYER_EXPEDITIONS_MAX];
        $level_production['R' . UNIT_PLAYER_COLONIES_MAX] = get_player_max_colonies($user, $level_start + $i);
        $level_production['D' . UNIT_PLAYER_COLONIES_MAX] = $level_production['R' . UNIT_PLAYER_COLONIES_MAX] - $level_production_base[UNIT_PLAYER_COLONIES_MAX];
        $production['.']['resource'][] = $level_production;

//        $level_production_base = array(
//          UNIT_PLAYER_EXPEDITIONS_MAX => $level_production['R' . UNIT_PLAYER_EXPEDITIONS_MAX],
//          UNIT_PLAYER_COLONIES_MAX    => $level_production['R' . UNIT_PLAYER_COLONIES_MAX],
//        );
      }
    }

    $production['.'][TPL_BLOCK_REQUIRE] = unit_requirements_render($user, $planet, $unit_id);
    $production['.']['grants'] = unit_requirements_render($user, $planet, $unit_id, P_UNIT_GRANTS);

    $template_result['.']['production'][] = $production;
  }

  foreach($lang['player_option_building_sort'] as $sort_id => $sort_text) {
    $template->assign_block_vars('sort_values', array(
      'VALUE' => $sort_id,
      'TEXT'  => $sort_text,
    ));
  }

  $sort_option = SN::$user_options[array(PLAYER_OPTION_BUILDING_SORT, $que_type)];
  $sort_option_inverse = SN::$user_options[array(PLAYER_OPTION_BUILDING_SORT_INVERSE, $que_type)];
  if($sort_option || $sort_option_inverse != PLAYER_OPTION_SORT_ORDER_PLAIN) {
    switch($sort_option) {
      case PLAYER_OPTION_SORT_NAME:
        $sort_option_field = 'NAME';
      break;
      case PLAYER_OPTION_SORT_ID:
        $sort_option_field = 'ID';
      break;
      case PLAYER_OPTION_SORT_CREATE_TIME_LENGTH:
        $sort_option_field = 'TIME_SECONDS';
      break;
      default:
        $sort_option_field = '__INDEX';
      break;
    }
    $sort_option_inverse_closure = $sort_option_inverse ? -1 : 1;
    usort($template_result['.']['production'], function ($a, $b) use ($sort_option_field, $sort_option_inverse_closure) {
      return $a[$sort_option_field] < $b[$sort_option_field] ? -1 * $sort_option_inverse_closure : (
      $a[$sort_option_field] > $b[$sort_option_field] ? 1 * $sort_option_inverse_closure : 0
      );
    });
  }

  $sector_cost = eco_get_build_data($user, $planet, UNIT_SECTOR, mrc_get_level($user, $planet, UNIT_SECTOR), true);
  $sector_cost = $sector_cost[BUILD_CREATE][RES_DARK_MATTER];
  $template_result += array(
    'ALLY_ID' => $user['user_as_ally'],

    'QUE_ID'          => $que_type,
    'SHOW_SECTORS'    => $que_type == QUE_STRUCTURES,
    'FLEET_OWN_COUNT' => $fleet_list['own']['count'],

    'ARTIFACT_ID'    => $artifact_id,
    'ARTIFACT_LEVEL' => mrc_get_level($user, array(), $artifact_id),
    'ARTIFACT_NAME'  => $lang['tech'][$artifact_id],
    'REQUEST_URI'    => urlencode($_SERVER['REQUEST_URI']),

    'PAGE_HEADER' => $page_header,

    'PLN_ID' => $planet['id'],

    'METAL'       => $planet['metal'],
    'CRYSTAL'     => $planet['crystal'],
    'DEUTERIUM'   => $planet['deuterium'],
    'DARK_MATTER' => $user_dark_matter,

    'METAL_INCOMING'     => $fleet_list['own']['total'][RES_METAL],
    'CRYSTAL_INCOMING'   => $fleet_list['own']['total'][RES_CRYSTAL],
    'DEUTERIUM_INCOMING' => $fleet_list['own']['total'][RES_DEUTERIUM],

    'FIELDS_CURRENT' => $planet_fields_current,
    'FIELDS_MAX'     => $planet_fields_max,
    'FIELDS_FREE'    => $planet_fields_free,
    'FIELDS_QUE'     => $planet_fields_que == 0 ? '' : $planet_fields_que > 0 ? "+{$planet_fields_que}" : $planet_fields_que,

    'QUE_HAS_PLACE'  => $can_que_element,
    'QUE_HAS_FIELDS' => $planet_fields_queable,

    'PAGE_HINT'        => $lang['eco_bld_page_hint'],
    'PLANET_TYPE'      => $planet['planet_type'],
    'SECTOR_CAN_BUY'   => $sector_cost <= mrc_get_level($user, null, RES_DARK_MATTER),
    'SECTOR_COST'      => $sector_cost,
    'SECTOR_COST_TEXT' => HelperString::numberFloorAndFormat($sector_cost),

    'STACKABLE' => $unit_stackable,

    'TEMPORARY' => intval($config->empire_mercenary_temporary && $que_type == QUE_MERCENARY),

    'STRING_CREATE'     => $que_type == QUE_MERCENARY ? $lang['bld_hire'] : ($que_type == QUE_RESEARCH ? $lang['bld_research'] : $lang['bld_create']),
    'STRING_BUILD_TIME' => $que_type == QUE_RESEARCH ? $lang['ResearchTime'] : $lang['ConstructionTime'],

    'U_opt_int_struc_vertical' => $user['option_list'][OPT_INTERFACE]['opt_int_struc_vertical'],

    'MARKET_AUTOCONVERT_COST'              => market_get_autoconvert_cost(),
    'MARKET_AUTOCONVERT_COST_TEXT'         => HelperString::numberFloorAndFormat(market_get_autoconvert_cost()),
    'CAN_AUTOCONVERT'                      => $user_dark_matter >= market_get_autoconvert_cost(),
    'BUILD_AUTOCONVERT_AVAILABLE'          => BUILD_AUTOCONVERT_AVAILABLE,
    'PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE' => SN::$user_options[PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE],

    'SORT_OPTION'         => $sort_option,
    'SORT_OPTION_INVERSE' => $sort_option_inverse,

    'QUE_RESEARCH' => QUE_RESEARCH,
  );

  $template->assign_recursive($template_result);

  SnTemplate::display($template);
}
