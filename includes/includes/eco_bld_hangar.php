<?php

function eco_hangar_is_building($que)
{
  return $que['in_que_abs'][STRUC_FACTORY_HANGAR] ? true : false;
}

function eco_bld_hangar_clear($planet, $action)
{
  global $sn_data;

  doquery('START TRANSACTION;');
  $planet = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$planet['id']}' LIMIT 1 FOR UPDATE;", '', true);

  $restored_resources = array();
  $hangar_que = eco_que_str2arr($planet['b_hangar_id']);
  $hangar_loop = count($hangar_que) - 1;
  for($i = $hangar_loop; $i >= ($action == 'trim' ? $hangar_loop : 0); $i--)
  {
    $unit_data = &$sn_data[$hangar_que[$i][0]];
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
    $query[] = "`{$sn_data[$resource_id]['name']}` = `{$sn_data[$resource_id]['name']}` + {$resource_amount}";
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

  global $sn_data, $lang, $time_now;

  if($planet[$sn_data[STRUC_FACTORY_HANGAR]['name']] == 0)
  {
    message($lang['need_hangar'], $lang['tech'][STRUC_FACTORY_HANGAR]);
  }

  $page_error = '';
  $page_mode = $que_type == SUBQUE_FLEET ? 'fleet' : 'defense';
  $sn_data_group = $sn_data['groups'][$page_mode];

  doquery('START TRANSACTION;');
  $planet = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$planet['id']}' LIMIT 1 FOR UPDATE;", '', true);

  $hangar_que = eco_que_str2arr($planet['b_hangar_id']);
  $hangar_que_by_unit = sys_unit_str2arr($planet['b_hangar_id']);
  $silo_capacity_free = max(0,
    $planet[$sn_data[STRUC_SILO]['name']] * $sn_data[STRUC_SILO]['capacity']
    - ($hangar_que_by_unit[502] + $planet[$sn_data[502]['name']]) * $sn_data[502]['size']
    - ($hangar_que_by_unit[503] + $planet[$sn_data[503]['name']]) * $sn_data[503]['size']);

  $POST_fmenge = sys_get_param('fmenge');
  $que_size = count(eco_que_str2arr($planet['b_hangar_id']));
  if(!empty($POST_fmenge) && !eco_hangar_is_building($que) && $que_size < MAX_BUILDING_QUEUE_SIZE)
  {
    $units_cost = array();
    foreach($POST_fmenge as $unit_id => $unit_count)
    {
      if($que_size >= MAX_BUILDING_QUEUE_SIZE)
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
      $unit_count = min($build_data[CAN][BUILD_CREATE], $sn_data[$unit_id]['max'] ? max(0, $sn_data[$unit_id]['max'] - $hangar_que_by_unit[$unit_id] - $planet[$sn_data[$unit_id]['name']]) : $unit_count);
      // Restricting $unit_count by free silo capacity
      $unit_count = ($unit_is_missile = in_array($unit_id, $sn_data['groups']['missile'])) ? min($unit_count, floor($silo_capacity_free / $sn_data[$unit_id]['size'])) : $unit_count;
      if(!$unit_count)
      {
        continue;
      }

      // Restricting $unit_count by MAX_FLEET_OR_DEFS_PER_ROW
      $unit_count = min($unit_count, MAX_FLEET_OR_DEFS_PER_ROW);
      foreach($build_data[BUILD_CREATE] as $resource_id => $resource_amount)
      {
        $units_cost[$resource_id] += $resource_amount * $unit_count;
      }
      $silo_capacity_free -= $unit_is_missile ? $unit_count * $sn_data[$unit_id]['size'] : 0;
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
        $resource_db_name = $sn_data[$resource_id]['name'];
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
    $build_data = eco_get_build_data($user, $planet, $unit_id);
    if($build_data['RESULT'][BUILD_CREATE] == BUILD_REQUIRE_NOT_MEET)
    {
      continue;
    }

    $unit_message = '';
    $ElementCount  = $planet[$sn_data[$unit_id]['name']];
    // Restricting $can_build by resources on planet and (where applicable) with max count per unit
    $can_build     = $sn_data[$unit_id]['max'] ? max(0, $sn_data[$unit_id]['max'] - $hangar_que_by_unit[$unit_id] - $planet[$sn_data[$unit_id]['name']]) : $build_data['CAN'][BUILD_CREATE];
    // Restricting $can_build by free silo capacity
    $can_build     = ($unit_is_missile = in_array($unit_id, $sn_data['groups']['missile'])) ? min($can_build, floor($silo_capacity_free / $sn_data[$unit_id]['size'])) : $can_build;
    if(!$can_build)
    {
      if(!$build_data['CAN'][BUILD_CREATE])
      {
        $unit_message = 'no resources';//$lang['b_no_silo_space'];
      }
      elseif($unit_is_missile && $silo_capacity_free < $sn_data[$unit_id]['size'])
      {
        $unit_message = $lang['b_no_silo_space'];
      }
      elseif($sn_data[$unit_id]['max'])
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
      'LEVEL_OLD'         => $CurentPlanet[$sn_data[$unit_id]['name']],
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

      'ARMOR'  => pretty_number($sn_data[$unit_id]['armor']),
      'SHIELD' => pretty_number($sn_data[$unit_id]['shield']),
      'WEAPON' => pretty_number($sn_data[$unit_id]['attack']),

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
    'TIME_NOW'   => $time_now,
    'HANGAR_BUSY' => eco_hangar_is_building($que),
  ));

  tpl_assign_hangar($que_type, $planet, $template);

  display(parsetemplate($template), $lang[$page_mode]);
}

?>
