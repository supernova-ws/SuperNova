<?php

/**
 * eco_build.php
 *
 * @version 1.5 - Using PTE (not everywhere) by Gorlum for http://supernova.ws
 * @version 1.4 - Complying with PCG by Gorlum for http://supernova.ws
 * @version 1.3 - Security checked for SQL-injection by Gorlum for http://supernova.ws
// 1.0 Mise en module initiale (creation)
// 1.1 FIX interception cheat +1
// 1.2 FIX interception cheat destruction a -1
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */

function eco_build($que_type, $user, &$planet)
{
  global $lang, $config;

// start transaction here

  $template = gettemplate('buildings_builds', true);

  // Caching values that used more then one time into local variables
  $config_resource_multiplier = $config->resource_multiplier;
  $planet_type_structs = sn_get_groups('build_allow');
  $planet_type_structs = $planet_type_structs[$planet['planet_type']];

  // Getting parameters
  $action     = sys_get_param_escaped('action');
//  $unit_id    = sys_get_param_int('unit_id');
//  $unit_level = sys_get_param_id('unit_level');
//  $GET_listid = $_GET['listid'];

  $que_type = ($que_type == SUBQUE_FLEET || $que_type == SUBQUE_DEFENSE) ? QUE_HANGAR : $que_type;

  if($action)
  {
    switch($action)
    {
      case 'create': // Add unit to que for build
      case 'destroy': // Add unit to que for remove
        // eco_bld_structure_build($user, $planet);
        que_build($user, $planet, $action == 'destroy' ? BUILD_DESTROY : BUILD_CREATE);
      break;

      //case 'destroy': // Add unit to que for remove
        // eco_bld_structure_build($user, $planet, BUILD_DESTROY);
      //  que_build($user, $planet, BUILD_DESTROY);
      //break;

      case 'trim': // Cancel unit from que
        // $que = eco_que_clear($user, $planet, $que, QUE_STRUCTURES, true);
        que_delete(QUE_STRUCTURES, $user, $planet, false);
      break;

      case 'clear': // Clear que
        // $que = eco_que_clear($user, $planet, $que, QUE_STRUCTURES);
        que_delete(QUE_STRUCTURES, $user, $planet, true);
      break;
    }
    header("Location: {$_SERVER['PHP_SELF']}?mode={$que_type}");
  }

/*
  // Code for fully working new que system
  $hangar_busy = count($que['que'][QUE_HANGAR]);
  $lab_busy    = count($que['que'][QUE_RESEARCH]) && !$config->BuildLabWhileRun;
*/

  $ques = que_get($que_type, $user['id'], $planet['id']);
  $que = &$ques['ques'][$que_type][$user['id']][$planet['id']];

  $in_que = &$ques['in_que'][$que_type][$user['id']][$planet['id']];

  $que_length  = count($que);
  $can_que_element = $que_length < que_get_max_que_length($user, $planet, $que_type);

  $fleet_list            = flt_get_fleets_to_planet($planet);
  // $caps                  = eco_get_planet_caps($user, $planet);

  $planet_fields_max     = eco_planet_fields_max($planet);
  $planet_fields_current = $planet['field_current'];
  $planet_fields_que     = is_array($in_que) ? -array_sum($in_que) : 0;
  $planet_fields_free    = max(0, $planet_fields_max - $planet_fields_current + $planet_fields_que);
  $planet_fields_queable = $planet_fields_free > 0;
  //$planet_temp_max       = $planet['temp_max'];
  $sn_modifiers_resource = sn_get_groups('modifiers');
  $sn_modifiers_resource = $sn_modifiers_resource[MODIFIER_RESOURCE_PRODUCTION];
  $sn_groups_density = sn_get_groups('planet_density');
  $density_info = $sn_groups_density[$planet['density_index']][UNIT_RESOURCES];

  foreach($planet_type_structs as $Element)
  {
    $element_name    = $lang['tech'][$Element];
    $element_sn_data = get_unit_param($Element);
    $element_level   = mrc_get_level($user, $planet, $Element, false, true) + $in_que[$Element];

    $build_data = eco_get_build_data($user, $planet, $Element, $element_level);

    // show energy on BuildingPage
    //================================
    if($element_sn_data['production'])
    {
      $level_production_base = array();
      $element_level_start = mrc_get_level($user, $planet, $Element) + $in_que[$Element];
      foreach($element_sn_data['production'] as $resource_id => $resource_calc)
      {
        if($resource_income = floor(mrc_modify_value($user, $planet, $sn_modifiers_resource, $resource_calc($element_level_start, 10, $user, $planet) * $config_resource_multiplier * (isset($density_info[$resource_id]) ? $density_info[$resource_id] : 1))))
        {
          $level_production_base[strtoupper(pname_resource_name($resource_id))] = $resource_income;
        }
      }

      $level_start = $element_level > 1 ? mrc_get_level($user, $planet, $Element) + $in_que[$Element] - 1 : 1;
      $level_production = array();
      for($i = 0; $i < 6; $i++)
      {
        $level_production[$level_start + $i]['LEVEL'] = $level_start + $i;
        foreach($element_sn_data['production'] as $resource_id => $resource_calc)
        {
          if($resource_income = floor(mrc_modify_value($user, $planet, $sn_modifiers_resource, $resource_calc($level_start + $i, 10, $user, $planet) * $config_resource_multiplier * (isset($density_info[$resource_id]) ? $density_info[$resource_id] : 1))))
          {
            $resource_name = strtoupper(pname_resource_name($resource_id));
            $level_production[$level_start + $i][$resource_name] = $resource_income;
            $level_production[$level_start + $i][$resource_name.'_DIFF'] = $resource_income - $level_production_base[$resource_name];
          }
        }
      }
    }

    //================================
    $temp[RES_METAL]     = floor($planet['metal'] + $fleet_list['own']['total'][RES_METAL] - $build_data[BUILD_CREATE][RES_METAL]);
    $temp[RES_CRYSTAL]   = floor($planet['crystal'] + $fleet_list['own']['total'][RES_CRYSTAL] - $build_data[BUILD_CREATE][RES_CRYSTAL]);
    $temp[RES_DEUTERIUM] = floor($planet['deuterium'] + $fleet_list['own']['total'][RES_DEUTERIUM] - $build_data[BUILD_CREATE][RES_DEUTERIUM]);
    $build_result_text = $lang['sys_build_result'][$build_data['RESULT'][BUILD_CREATE]];
    $build_result_text = !is_array($build_result_text) ? $build_result_text : (isset($build_result_text[$Element]) ? $build_result_text[$Element] : $build_result_text[0]);
    $template->assign_block_vars('production', array(
      'ID'                => $Element,
      'NAME'              => $element_name,
      'DESCRIPTION'       => $lang['info'][$Element]['description_short'],
      'LEVEL'             => $element_level,
      'LEVEL_OLD'         => mrc_get_level($user, $planet, $Element, false, true),
      'LEVEL_BONUS'       => mrc_get_level($user, $planet, $Element) - mrc_get_level($user, $planet, $Element, false, true),
      'LEVEL_CHANGE'      => $in_que[$Element],

      'BUILD_RESULT'      => $build_data['RESULT'][BUILD_CREATE],
      'BUILD_RESULT_TEXT' => $build_result_text,
      'BUILD_CAN'         => $build_data['CAN'][BUILD_CREATE],
      'TIME'              => pretty_time($build_data[RES_TIME][BUILD_CREATE]),
      'METAL'             => $build_data[BUILD_CREATE][RES_METAL],
      'CRYSTAL'           => $build_data[BUILD_CREATE][RES_CRYSTAL],
      'DEUTERIUM'         => $build_data[BUILD_CREATE][RES_DEUTERIUM],

      'DESTROY_RESULT'    => $build_data['RESULT'][BUILD_DESTROY],
      'DESTROY_CAN'       => $build_data['CAN'][BUILD_DESTROY],
      'DESTROY_TIME'      => pretty_time($build_data[RES_TIME][BUILD_DESTROY]),
      'DESTROY_METAL'     => $build_data[BUILD_DESTROY][RES_METAL],
      'DESTROY_CRYSTAL'   => $build_data[BUILD_DESTROY][RES_CRYSTAL],
      'DESTROY_DEUTERIUM' => $build_data[BUILD_DESTROY][RES_DEUTERIUM],

      'METAL_REST'        => pretty_number($temp[RES_METAL], true, true),
      'CRYSTAL_REST'      => pretty_number($temp[RES_CRYSTAL], true, true),
      'DEUTERIUM_REST'    => pretty_number($temp[RES_DEUTERIUM], true, true),
      'METAL_REST_NUM'    => $temp[RES_METAL],
      'CRYSTAL_REST_NUM'  => $temp[RES_CRYSTAL],
      'DEUTERIUM_REST_NUM'=> $temp[RES_DEUTERIUM],

      'UNIT_BUSY'         => eco_unit_busy($user, $planet, $que, $Element),
    ));
    if($element_sn_data['production'])
    {
      foreach($level_production as $level_production_item)
      {
        $template->assign_block_vars('production.resource', $level_production_item);
      }
    }
  }

  /*
  if(is_array($que))
  {
    foreach($que as $que_element)
    {
      $template->assign_block_vars('que', $que_element);
    }
  }
  */

  que_tpl_parse(&$template, $que_type, $user, $planet, $que);

  $sector_cost = eco_get_build_data($user, $planet, UNIT_SECTOR, mrc_get_level($user, $planet, UNIT_SECTOR), true);
  $sector_cost = $sector_cost[BUILD_CREATE][RES_DARK_MATTER];
  $template->assign_vars(array(
    'TIME_NOW'           => SN_TIME_NOW,

    'QUE_ID'             => $que_type,

    'PLN_ID'              => $planet['id'],

    'ARTIFACT_ID'         => ART_NANO_BUILDER,
    'ARTIFACT_LEVEL'      => mrc_get_level($user, array(), ART_NANO_BUILDER),
    'ARTIFACT_NAME'       => $lang['tech'][ART_NANO_BUILDER],
    'REQUEST_URI'         => urlencode($_SERVER['REQUEST_URI']),

    'METAL'              => $planet['metal'],
    'CRYSTAL'            => $planet['crystal'],
    'DEUTERIUM'          => $planet['deuterium'],

    'METAL_INCOMING'     => $fleet_list['own']['total'][RES_METAL],
    'CRYSTAL_INCOMING'   => $fleet_list['own']['total'][RES_CRYSTAL],
    'DEUTERIUM_INCOMING' => $fleet_list['own']['total'][RES_DEUTERIUM],

    'FIELDS_CURRENT'     => $planet_fields_current,
    'FIELDS_MAX'         => $planet_fields_max,
    'FIELDS_FREE'        => $planet_fields_free,
    'FIELDS_QUE'         => $planet_fields_que == 0 ? '' : $planet_fields_que > 0 ? "+{$planet_fields_que}" : $planet_fields_que,

    'QUE_HAS_PLACE'      => $can_que_element,
    'QUE_HAS_FIELDS'     => $planet_fields_queable,

    'FLEET_OWN'          => $fleet_list['own']['count'],

    'PAGE_HINT'          => $lang['eco_bld_page_hint'],
    'PLANET_TYPE'        => $planet['planet_type'],
    'SECTOR_CAN_BUY'     => $sector_cost <= mrc_get_level($user, null, RES_DARK_MATTER),
    'SECTOR_COST'        => $sector_cost,
    'SECTOR_COST_TEXT'   => pretty_number($sector_cost),

    'U_opt_int_struc_vertical' => $user['option_list'][OPT_INTERFACE]['opt_int_struc_vertical'],
  ));

  display(parsetemplate($template), $lang['Builds']);
}
