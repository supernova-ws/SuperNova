<?php

/**
 * BatimentBuildingPage.php
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
  global $sn_data, $lang, $config, $time_now;

// start transaction here

  $template = gettemplate('buildings_builds_new', true);

  // Caching values that used more then one time into local variables
  $config_resource_multiplier = $config->resource_multiplier;
  $planet_type_structs = $sn_data['groups']['build_allow'][$planet['planet_type']];

  // Getting parameters
  $action     = sys_get_param_escaped('action');
  $unit_id    = sys_get_param_int('unit_id');
  $unit_level = sys_get_param_int('unit_level');
  $GET_listid = $_GET['listid'];

  CheckPlanetUsedFields($planet);
  switch($action)
  {
    case 'create': // Add unit to que for build
//      AddBuildingToQueue ( $planet, $user, $unit_id, true );
    break;

    case 'destroy': // Add unit to que for remove
//      AddBuildingToQueue ( $planet, $user, $unit_id, false );
    break;

    case 'cancel': // Cancel unit from que
//      CancelBuildingFromQueue ( $planet, $user );
    break;

    case 'clear': // Clear que
    break;

    default: // Just build page
    break;
  }

  $que = eco_que_process($user, $planet, $time_now - $planet['last_update']);

  $que_length = count($que['que'][$que_type]);

//  $template->assign_block_vars('que', array)

//  SetNextQueueElementOnTop ( $planet, $user );
//  $Queue = ShowBuildingQueue ( $planet, $user );

  // On enregistre ce que l'on a modifié dans planet !
//  doquery("UPDATE `{{planets}}` SET `b_building_id` = '{$planet['b_building_id']}', `b_building` = '{$planet['b_building']}' WHERE `id` = '{$planet['id']}' LIMIT 1;");
  // On enregistre ce que l'on a eventuellement modifié dans users
//  doquery("UPDATE `{{users}}` SET `xpminier` = '{$user['xpminier']}' WHERE `id` = '{$user['id']}' LIMIT 1;");
//  rpg_level_up($user, RPG_STRUCTURE);


  if ($que_length < MAX_BUILDING_QUEUE_SIZE)
    $can_que_element = true;
  else
    $can_que_element = false;

  if($planet['b_building_id'])
  {
    $now_building = explode(';', $planet['b_building_id']);
    $now_building = explode(',', $now_building[0]);
    $now_working  = $now_building[0];
    if($now_building[4] == destroy)
      $now_building = 0;
    else
      $now_building = 1;
  }
  else
    $now_working = false;


  $fleet_list = flt_get_fleets_to_planet($planet);
  $caps = ECO_getPlanetCaps($user, &$planet);
  $planet_fields_max = eco_planet_fields_max($planet);
  $planet_fields_current = $planet['field_current'];
  $planet_fields_free = $planet_fields_max - $planet_fields_current;
  $planet_temp_max = $planet['temp_max'];

  if ($planet_fields_free > $que_length)
    $RoomIsOk = true;
  else
    $RoomIsOk = false;

  foreach($planet_type_structs as $Element)
  {
    if (IsTechnologieAccessible($user, $planet, $Element))
    {
      $parse = array();

      $element_name    = $lang['tech'][$Element];
      $element_sn_data = &$sn_data[$Element];
      $element_level   = $planet[$sn_data[$Element]['name']];

      // show energy on BuildingPage
      //================================
      if($element_sn_data['production'])
      {
        $element_production_energy = $element_sn_data['production'][RES_ENERGY];
        $energy_balance = floor($element_production_energy($element_level + 1, 10, $planet_temp_max)) - floor($element_production_energy($element_level, 10, $planet_temp_max));
        if ($Element == STRUC_MINE_SOLAR || $Element == STRUC_MINE_FUSION)
        {
          $energy_balance =
            floor(mrc_modify_value($user, $planet, array(TECH_ENERGY, MRC_POWERMAN), $element_production_energy($element_level + 1, 10, $planet_temp_max) * $config_resource_multiplier)) -
            floor(mrc_modify_value($user, $planet, array(TECH_ENERGY, MRC_POWERMAN), $element_production_energy($element_level, 10, $planet_temp_max) * $config_resource_multiplier));
//          $energy_balance = mrc_modify_value($user, $planet, array(TECH_ENERGY, MRC_POWERMAN), $energy_balance * $config_resource_multiplier);
        }
        else
        {
//          $energy_balance = $element_production_energy($element_level + 1, 10, $planet_temp_max) - $element_production_energy($element_level, 10, $planet_temp_max);
//          $energy_balance = floor($energy_balance * $config_resource_multiplier);
        }
        $energy_balance = floor($energy_balance);
/*
        //!!!
        if ($Element >= 1 && $Element <= 3)
          $parse['build_need_diff'] = "<font color=#FF0000>{$energy_balance} {$lang['Energy']}</font>";
        elseif ($Element == 4 || $Element == 12)
          $parse['build_need_diff'] = "<font color=#00FF00>+{$energy_balance} {$lang['Energy']}</font>";
*/
      }

      //================================
      $parse['click'] = '';
      $NextBuildLevel = $element_level + 1;
      $can_build_unit = false;

      if((($Element == 31 || $Element == 35) && $user['b_tech_planet'] && !$config->BuildLabWhileRun)
        || ($Element == 21 && $planet['b_hangar_id']))
      {
        $parse['click'] = "<font color=#FF0000>{$lang['in_working']}</font>";
      }
      elseif(!$RoomIsOk)
      {
        $parse['click'] = "<font color=#FF0000>{$lang['NoMoreSpace']}</font>";
      }
      else
      {
        if ($NextBuildLevel == 1)
        {
          $next_level_msg = $lang['BuildFirstLevel'];
        }
        else
        {
          $next_level_msg = "{$lang['BuildNextLevel']} {$NextBuildLevel}";
        }

        if (!$can_que_element)
        {
          $parse['click'] = "<font color=#FF0000>{$next_level_msg}</font>";
        }
        else
        {
          if ($que_length != 0)
          {
            $next_level_msg = $lang['InBuildQueue'];
          }

          if ( IsElementBuyable ($user, $planet, $Element, true, false) )
          {
            $parse['click'] = "<a href=\"?cmd=insert&building={$Element}\"><font color=#00FF00>{$next_level_msg}</font></a>";
            $can_build_unit = true;
          }
          else
          {
            $parse['click'] = "<font color=#ff0000>{$next_level_msg}</font>";
          }
        }
      }

      $build_price = GetBuildingPrice ($user, $planet, $Element, true);
      $destroy_price = GetBuildingPrice ($user, $planet, $Element, true, true);
      $template->assign_block_vars('production', array(
        'ID'                => $Element,
        'NAME'              => $element_name,
        'DESCRIPTION'       => $lang['info'][$Element]['description_short'],
        'LEVEL'             => ($element_level == 0) ? '' : $element_level,

        'TIME'              => pretty_time(GetBuildingTime($user, $planet, $Element)),
        'DESTROY_TIME'      => pretty_time(GetBuildingTime($user, $planet, $Element) / 2),

        'PRICE'             => GetElementPrice($user, $planet, $Element),
        'RESOURCES_LEFT'    => GetRestPrice($user, $planet, $Element),

        'METAL'             => $build_price['metal'],
        'CRYSTAL'           => $build_price['crystal'],
        'DEUTERIUM'         => $build_price['deuterium'],
        'DESTROY_METAL'     => $destroy_price['metal'],
        'DESTROY_CRYSTAL'   => $destroy_price['crystal'],
        'DESTROY_DEUTERIUM' => $destroy_price['deuterium'],

        'METAL_REST'        => pretty_number($planet['metal']     + $fleet_list['own']['total'][RES_METAL] - $build_price['metal'], false, true),
        'CRYSTAL_REST'      => pretty_number($planet['crystal']   + $fleet_list['own']['total'][RES_CRYSTAL] - $build_price['crystal'], false, true),
        'DEUTERIUM_REST'    => pretty_number($planet['deuterium'] + $fleet_list['own']['total'][RES_DEUTERIUM] - $build_price['deuterium'], false, true),
        'METAL_REST_NUM'    => $planet['metal']     + $fleet_list['own']['total'][RES_METAL] - $build_price['metal'],
        'CRYSTAL_REST_NUM'  => $planet['crystal']   + $fleet_list['own']['total'][RES_CRYSTAL] - $build_price['crystal'],
        'DEUTERIUM_REST_NUM'=> $planet['deuterium'] + $fleet_list['own']['total'][RES_DEUTERIUM] - $build_price['deuterium'],

        'METAL_BALANCE'     => $caps['metal_perhour'][$Element],
        'CRYSTAL_BALANCE'   => $caps['crystal_perhour'][$Element],
        'DEUTERIUM_BALANCE' => $caps['deuterium_perhour'][$Element],
        'ENERGY_BALANCE'    => $energy_balance,

        'BUILD_LINK'        => $parse['click'],
        'CAN_BUILD'         => $can_build_unit,
      ));
    }

  }

  if ($que_length > 0)
  {
    $parse['BuildListScript']  = InsertBuildListScript ('buildings');
    $parse['BuildList']        = $Queue['buildlist'];
  }
  else
  {
    $parse['BuildListScript']  = '';
    $parse['BuildList']        = '';
  }

  $template->assign_vars(array(
    'planet_field_current' => $planet_fields_current,
    'planet_field_max'     => $planet_fields_max,
    'field_libre'          => $planet_fields_max - $planet_fields_current,
    'NOW_WORKING'          => $now_working,
    'NOW_BUILDING'         => $now_building,
    'PAGE_HINT'            => $lang['eco_bld_page_hint'],

    'METAL'                => $planet['metal'],
    'CRYSTAL'              => $planet['crystal'],
    'DEUTERIUM'            => $planet['deuterium'],

    'METAL_INCOMING'       => $fleet_list['own']['total'][RES_METAL],
    'CRYSTAL_INCOMING'     => $fleet_list['own']['total'][RES_CRYSTAL],
    'DEUTERIUM_INCOMING'   => $fleet_list['own']['total'][RES_DEUTERIUM],

    'FLEET_OWN'            => $fleet_list['own']['count'],
  ));

  display(parsetemplate($template, $parse), $lang['Builds']);
}

?>