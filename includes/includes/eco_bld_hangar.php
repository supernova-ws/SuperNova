<?php

function eco_hangar_is_building($que)
{
  return $que['in_que_abs'][STRUC_FACTORY_HANGAR] ? true : false;
}

function eco_bld_hangar_clear($planet, $action)
{
  doquery('START TRANSACTION;');
  $planet = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$planet['id']}' LIMIT 1 FOR UPDATE;", '', true);

  $restored_resources = array();
  $hangar_que = eco_que_str2arr($planet['b_hangar_id']);
  $hangar_loop = count($hangar_que) - 1;
  for($i = $hangar_loop; $i >= ($action == 'trim' ? $hangar_loop : 0); $i--)
  {
    $unit_data = get_unit_param($hangar_que[$i][0]);
    foreach($unit_data['cost'] as $resource_id => $resource_amount)
    {
      if(!$resource_amount || !intval($resource_id))
      {
        continue;
      }
      $restored_resources[$resource_id] += $hangar_que[$i][1] * $resource_amount;
    }
    unset($hangar_que[$i]);
  }

  $query = array();
  foreach($restored_resources as $resource_id => $resource_amount)
  {
    $resource_db_name = get_unit_param($resource_id, P_NAME);
    $query[] = "`{$resource_db_name}` = `{$resource_db_name}` + {$resource_amount}";
  }
  $hangar_que = eco_que_arr2str($hangar_que);
  $query[] = "`b_hangar_id` = '{$hangar_que}'";
  if(!$hangar_que)
  {
    $query[] = "`b_hangar` = 0";
  }
  $query = implode(',', $query);
  doquery("UPDATE `{{planets}}` SET {$query} WHERE `id` = '{$planet['id']}' LIMIT 1;");
  doquery('COMMIT;');

  sys_redirect("{$_SERVER['PHP_SELF']}?mode=" . sys_get_param_str('mode'));
}

function eco_bld_hangar($que_type, $user, &$planet, $que)
{
  if(in_array($action = sys_get_param_str('action'), array('trim', 'clear')))
  {
    eco_bld_hangar_clear($planet, $action);
  }

  global $lang, $config;

  if($planet[get_unit_param(STRUC_FACTORY_HANGAR, P_NAME)] == 0)
  {
    message($lang['need_hangar'], $lang['tech'][STRUC_FACTORY_HANGAR]);
  }

  $page_error = '';
  $page_mode = $que_type == SUBQUE_FLEET ? 'fleet' : 'defense';
  $sn_data_group = sn_get_groups($page_mode);

  doquery('START TRANSACTION;');
  $planet = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$planet['id']}' LIMIT 1 FOR UPDATE;", '', true);

  $hangar_que = eco_que_str2arr($planet['b_hangar_id']);
  $hangar_que_by_unit = sys_unit_str2arr($planet['b_hangar_id']);

  $silo_info = get_unit_param(STRUC_SILO);
  $silo_capacity_free = $planet[$silo_info[P_NAME]] * $silo_info[P_CAPACITY];
  foreach(sn_get_groups('missile') as $silo_unit_id)
  {
    $silo_unit_info = get_unit_param($silo_unit_id);
    $silo_capacity_free -= ($hangar_que_by_unit[$silo_unit_id] + $planet[$silo_unit_info[P_NAME]]) * $silo_unit_info[P_UNIT_SIZE];
  }
  $silo_capacity_free = max(0, $silo_capacity_free);

  $POST_fmenge = sys_get_param('fmenge');
  $que_size = count(eco_que_str2arr($planet['b_hangar_id']));
  $units_cost = array();
  $config_server_que_length_hangar = $config->server_que_length_hangar + mrc_get_level($user, $planet, $que_type == SUBQUE_FLEET ? MRC_ENGINEER : MRC_FORTIFIER);
  if(!empty($POST_fmenge) && !eco_hangar_is_building($que) && $config_server_que_length_hangar)
  {
    foreach($POST_fmenge as $unit_id => $unit_count)
    {
      $unit_info = get_unit_param($unit_id);
      if($que_size >= $config_server_que_length_hangar)
      {
        break;
      }

      $unit_id = intval($unit_id);
      // Restricting $unit_count by positive number
      $unit_count   = max(0, intval($unit_count));
      if (!$unit_count || !$unit_id || !in_array($unit_id, $sn_data_group))
      {
        continue;
      }

      $build_data = eco_get_build_data($user, $planet, $unit_id);
      if($build_data['RESULT'][BUILD_CREATE] != BUILD_ALLOWED)
      {
        continue;
      }
      // Restricting $unit_count by resources on planet and (where applicable) with max count per unit
      $unit_count = min($build_data['CAN'][BUILD_CREATE], $unit_info[P_MAX_STACK] ? max(0, $unit_info[P_MAX_STACK] - $hangar_que_by_unit[$unit_id] - $planet[$unit_info[P_NAME]]) : $unit_count);
      // Restricting $unit_count by free silo capacity
      $unit_count = ($unit_is_missile = in_array($unit_id, sn_get_groups('missile'))) ? min($unit_count, floor($silo_capacity_free / $unit_info[P_UNIT_SIZE])) : $unit_count;
      if(!$unit_count)
      {
        continue;
      }

      // Restricting $unit_count by MAX_FLEET_OR_DEFS_PER_ROW
      $unit_count = min($unit_count, MAX_FLEET_OR_DEFS_PER_ROW);
      // Restricting build by resources
      $units_cost_new = $units_cost;
      foreach($build_data[BUILD_CREATE] as $resource_id => $resource_amount)
      {
        $units_cost_new[$resource_id] += $resource_amount * $unit_count;
        if($units_cost_new[$resource_id] > $planet[get_unit_param($resource_id, P_NAME)])
        {
          continue 2;
        }
      }

      $units_cost = $units_cost_new;
      $silo_capacity_free -= $unit_is_missile ? $unit_count * $unit_info[P_UNIT_SIZE] : 0;
      $hangar_que[] = array($unit_id, $unit_count);
      $hangar_que_by_unit[$unit_id] += $unit_count;
      $que_size++;
    }

    if(!empty($units_cost))
    {
      $planet['b_hangar_id'] = eco_que_arr2str($hangar_que);
      $query = array("`b_hangar_id` = '{$planet['b_hangar_id']}'");
      foreach($units_cost as $resource_id => $resource_amount)
      {
        $resource_db_name = get_unit_param($resource_id, P_NAME);
        $query[] = "`{$resource_db_name}` = `{$resource_db_name}` - {$resource_amount}";
        $planet[$resource_db_name] -= $resource_amount;
      }
      $query = implode(',', $query);
      doquery("UPDATE {{planets}} SET {$query} WHERE `id` = '{$planet['id']}' LIMIT 1;");
    }
  }
  doquery('COMMIT');

  $template = gettemplate("buildings_hangar", true);

  // -------------------------------------------------------------------------------------------------------
  // Construction de la page du Chantier (car si j'arrive ici ... c'est que j'ai tout ce qu'il faut pour ...
  $TabIndex  = 0;
  foreach($sn_data_group as $unit_id)
  {
    $unit_info = get_unit_param($unit_id);
    $build_data = eco_get_build_data($user, $planet, $unit_id);

    if($build_data['RESULT'][BUILD_CREATE] == BUILD_REQUIRE_NOT_MEET)
    {
      continue;
    }

    $unit_message = '';
    $ElementCount  = $planet[$unit_info[P_NAME]];
    // Restricting $can_build by resources on planet and (where applicable) with max count per unit
    $can_build     = $unit_info[P_MAX_STACK] ? max(0, $unit_info[P_MAX_STACK] - $hangar_que_by_unit[$unit_id] - $planet[$unit_info[P_NAME]]) : $build_data['CAN'][BUILD_CREATE];
    // Restricting $can_build by free silo capacity
    $can_build     = ($unit_is_missile = in_array($unit_id, sn_get_groups('missile'))) ? min($can_build, floor($silo_capacity_free / $unit_info[P_UNIT_SIZE])) : $can_build;
    if(!$can_build)
    {
      if(!$build_data['CAN'][BUILD_CREATE])
      {
        $unit_message = $lang['sys_build_result'][BUILD_NO_RESOURCES];
      }
      elseif($unit_is_missile && $silo_capacity_free < $unit_info[P_UNIT_SIZE])
      {
        $unit_message = $lang['b_no_silo_space'];
      }
      elseif($unit_info[P_MAX_STACK])
      {
        $unit_message = $lang['only_one'];
      }
    }
    else
    {
      $TabIndex++;
    }

    $temp[RES_METAL]     = floor($planet['metal'] - $build_data[BUILD_CREATE][RES_METAL]); // + $fleet_list['own']['total'][RES_METAL]
    $temp[RES_CRYSTAL]   = floor($planet['crystal'] - $build_data[BUILD_CREATE][RES_CRYSTAL]); // + $fleet_list['own']['total'][RES_CRYSTAL]
    $temp[RES_DEUTERIUM] = floor($planet['deuterium'] - $build_data[BUILD_CREATE][RES_DEUTERIUM]); // + $fleet_list['own']['total'][RES_DEUTERIUM]

    $template->assign_block_vars('production', array(
      'ID'                => $unit_id,
      'NAME'              => $lang['tech'][$unit_id],
      'DESCRIPTION'       => $lang['info'][$unit_id]['description_short'],
      'LEVEL'             => $ElementCount,
      'LEVEL_OLD'         => $CurentPlanet[$unit_info[P_NAME]],
      'LEVEL_CHANGE'      => $que['in_que'][$unit_id],

      'BUILD_CAN'         => $can_build,
      'TIME'              => pretty_time($build_data[RES_TIME][BUILD_CREATE]),
      'METAL'             => $build_data[BUILD_CREATE][RES_METAL],
      'CRYSTAL'           => $build_data[BUILD_CREATE][RES_CRYSTAL],
      'DEUTERIUM'         => $build_data[BUILD_CREATE][RES_DEUTERIUM],

      'METAL_PRINT'       => pretty_number($build_data[BUILD_CREATE][RES_METAL], true, $planet['metal']),
      'CRYSTAL_PRINT'     => pretty_number($build_data[BUILD_CREATE][RES_CRYSTAL], true, $planet['crystal']),
      'DEUTERIUM_PRINT'   => pretty_number($build_data[BUILD_CREATE][RES_DEUTERIUM], true, $planet['deuterium']),

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

      'ARMOR'  => pretty_number($unit_info[P_ARMOR]),
      'SHIELD' => pretty_number($unit_info[P_SHIELD]),
      'WEAPON' => pretty_number($unit_info[P_ATTACK]),

      'TABINDEX' => $TabIndex,

      'MESSAGE' => $unit_message,

//        'UNIT_BUSY'         => eco_unit_busy($user, $CurentPlanet, $que, $unit_id),
    ));
  }
//$hangar_busy
  $template->assign_vars(array(
    'noresearch' => $NoFleetMessage,
    'error_msg'  => $page_error,
    'MODE'       => $que_type,

    'QUE_ID'     => $que_type,
    'TIME_NOW'   => SN_TIME_NOW,
    'HANGAR_BUSY' => eco_hangar_is_building($que),
    'QUE_HAS_PLACE' => $que_size < $config_server_que_length_hangar,
  ));

  tpl_assign_hangar($que_type, $planet, $template);

  display(parsetemplate($template), $lang[$page_mode]);
}
