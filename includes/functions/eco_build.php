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

function eco_build($que_type, $user, &$planet, $que)
{
  global $sn_data, $lang, $config, $time_now;

// start transaction here

  $template = gettemplate('buildings_builds', true);

  // Caching values that used more then one time into local variables
  $config_resource_multiplier = $config->resource_multiplier;
  $planet_type_structs = $sn_data['groups']['build_allow'][$planet['planet_type']];

  // Getting parameters
  $action     = sys_get_param_escaped('action');
  $unit_id    = sys_get_param_int('unit_id');
  $unit_level = sys_get_param_int('unit_level');
  $GET_listid = $_GET['listid'];

  $que_type = ($que_type == SUBQUE_FLEET || $que_type == SUBQUE_DEFENSE) ? QUE_HANGAR : $que_type;

  CheckPlanetUsedFields($planet);
  if($action)
  {
    switch($action)
    {
      case 'create': // Add unit to que for build
        $que = eco_que_add($user, $planet, $que, QUE_STRUCTURES, $unit_id);
      break;

      case 'destroy': // Add unit to que for remove
        $que = eco_que_add($user, $planet, $que, QUE_STRUCTURES, $unit_id, 1, BUILD_DESTROY);
      break;

      case 'trim': // Cancel unit from que
        $que = eco_que_clear($user, $planet, $que, QUE_STRUCTURES, true);
      break;

      case 'clear': // Clear que
        $que = eco_que_clear($user, $planet, $que, QUE_STRUCTURES);
      break;
    }
    header("Location: {$_SERVER['PHP_SELF']}?mode={$que_type}");
  }

/*
  // Code for fully working new que system
  $hangar_busy = count($que['que'][QUE_HANGAR]);
  $lab_busy    = count($que['que'][QUE_RESEARCH]) && !$config->BuildLabWhileRun;
*/
  $que_length  = count($que['que'][$que_type]);
  $can_que_element = $que_length < MAX_BUILDING_QUEUE_SIZE;


  $fleet_list            = flt_get_fleets_to_planet($planet);
  $caps                  = ECO_getPlanetCaps($user, &$planet);

  $planet_fields_max     = eco_planet_fields_max($planet);
  $planet_fields_current = $planet['field_current'];
  $planet_fields_que     = -$que['amounts'][$que_type];
  $planet_fields_free    = max(0, $planet_fields_max - $planet_fields_current + $planet_fields_que);
  $planet_fields_queable = $planet_fields_free > 0;
  $planet_temp_max       = $planet['temp_max'];
  $GLOBALS['user_tech_energy'] = $user['energy_tech'];

  foreach($planet_type_structs as $Element)
  {
    if (eco_can_build_unit($user, $planet, $Element))
    {
      $element_name    = $lang['tech'][$Element];
      $element_sn_data = &$sn_data[$Element];
      $element_level   = $planet[$sn_data[$Element]['name']] + $que['in_que'][$Element];

      // show energy on BuildingPage
      //================================
      if($element_sn_data['production'])
      {
        $element_production_energy = $element_sn_data['production'][RES_ENERGY];

        $energy_current = floor($element_production_energy($element_level, 10, $planet_temp_max));
        $energy_next    = floor($element_production_energy($element_level + 1, 10, $planet_temp_max));
        if ($Element == STRUC_MINE_SOLAR || $Element == STRUC_MINE_FUSION)
        {
          $energy_current = floor(mrc_modify_value($user, $planet, array(MRC_TECHNOLOGIST), $energy_current));
          $energy_next    = floor(mrc_modify_value($user, $planet, array(MRC_TECHNOLOGIST), $energy_next));
        }
        $energy_balance = floor($energy_next - $energy_current);
      }
      else
      {
        $energy_balance = 0;
      }

      //================================
      $build_data = eco_get_build_data($user, $planet, $Element, $element_level);
      $temp[RES_METAL]     = floor($planet['metal'] + $fleet_list['own']['total'][RES_METAL] - $build_data[BUILD_CREATE][RES_METAL]);
      $temp[RES_CRYSTAL]   = floor($planet['crystal'] + $fleet_list['own']['total'][RES_CRYSTAL] - $build_data[BUILD_CREATE][RES_CRYSTAL]);
      $temp[RES_DEUTERIUM] = floor($planet['deuterium'] + $fleet_list['own']['total'][RES_DEUTERIUM] - $build_data[BUILD_CREATE][RES_DEUTERIUM]);
      $template->assign_block_vars('production', array(
        'ID'                => $Element,
        'NAME'              => $element_name,
        'DESCRIPTION'       => $lang['info'][$Element]['description_short'],
        'LEVEL'             => $element_level,
        'LEVEL_OLD'         => $planet[$sn_data[$Element]['name']],
        'LEVEL_CHANGE'      => $que['in_que'][$Element],

        'BUILD_CAN'         => $build_data['CAN'][BUILD_CREATE],
        'TIME'              => pretty_time($build_data[BUILD_CREATE][RES_TIME]),
        'METAL'             => $build_data[BUILD_CREATE][RES_METAL],
        'CRYSTAL'           => $build_data[BUILD_CREATE][RES_CRYSTAL],
        'DEUTERIUM'         => $build_data[BUILD_CREATE][RES_DEUTERIUM],

        'DESTROY_CAN'       => $build_data['CAN'][BUILD_DESTROY],
        'DESTROY_TIME'      => pretty_time($build_data[BUILD_DESTROY][RES_TIME]),
        'DESTROY_METAL'     => $build_data[BUILD_DESTROY][RES_METAL],
        'DESTROY_CRYSTAL'   => $build_data[BUILD_DESTROY][RES_CRYSTAL],
        'DESTROY_DEUTERIUM' => $build_data[BUILD_DESTROY][RES_DEUTERIUM],

        'METAL_REST'        => pretty_number($temp[RES_METAL], true, true),
        'CRYSTAL_REST'      => pretty_number($temp[RES_CRYSTAL], true, true),
        'DEUTERIUM_REST'    => pretty_number($temp[RES_DEUTERIUM], true, true),
        'METAL_REST_NUM'    => $temp[RES_METAL],
        'CRYSTAL_REST_NUM'  => $temp[RES_CRYSTAL],
        'DEUTERIUM_REST_NUM'=> $temp[RES_DEUTERIUM],

        'METAL_BALANCE'     => $caps['metal_perhour'][$Element],
        'CRYSTAL_BALANCE'   => $caps['crystal_perhour'][$Element],
        'DEUTERIUM_BALANCE' => $caps['deuterium_perhour'][$Element],
        'ENERGY_BALANCE'    => $energy_balance,

        'UNIT_BUSY'         => eco_unit_busy($user, $planet, $que, $Element),
      ));
    }

  }

  if(is_array($que['que'][$que_type]))
  {
    foreach($que['que'][$que_type] as $que_element)
    {
      $template->assign_block_vars('que', $que_element);
    }
  }

  $template->assign_vars(array(
    'TIME_NOW'           => $time_now,

    'QUE_ID'             => $que_type,

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
  ));

  display(parsetemplate($template), $lang['Builds']);
}

?>
