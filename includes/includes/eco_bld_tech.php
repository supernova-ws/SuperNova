<?php

function eco_bld_tech_research($user, $planet)
{
  global $lang;

  try
  {
    doquery('START TRANSACTION;');

    $tech_id    = sys_get_param_int('tech');
    $user = doquery("SELECT * FROM {{users}} WHERE `id` ={$user['id']} LIMIT 1 FOR UPDATE;", true);
    $planet = $planet['id'] ? doquery("SELECT * FROM {{planets}} WHERE `id` ={$planet['id']} LIMIT 1 FOR UPDATE;", true) : $planet;
    $build_data = eco_get_build_data($user, $planet, $tech_id, mrc_get_level($user, $planet, $tech_id, false, true));

    if($user['que'])
    {
      throw new exception($lang['eco_bld_msg_err_research_in_progress'], ERR_ERROR);
    }
    if(!in_array($tech_id, sn_get_groups('tech')))
    {
      // TODO: Hack attempt - warning here. Normally non-tech can't be passed from build page
      throw new exception($lang['eco_bld_msg_err_not_research'], ERR_ERROR);
    }
    if(eco_can_build_unit($user, $planet, $tech_id) != BUILD_ALLOWED)
    {
      // TODO: Hack attempt - warning here. Normally requirements check should be done on build page
      throw new exception($lang['eco_bld_msg_err_requirements_not_meet'], ERR_ERROR);
    }
    if(!$build_data['CAN'][BUILD_CREATE])
    {
      throw new exception($lang['eco_bld_resources_not_enough'], ERR_ERROR);
    }

    $que_item_string = "{$tech_id},1,{$build_data[RES_TIME][BUILD_CREATE]}," . BUILD_CREATE . "," . QUE_RESEARCH . ",{$planet['id']}";

    db_change_units($user, $planet, array(
      RES_METAL     => -$build_data[BUILD_CREATE][RES_METAL],
      RES_CRYSTAL   => -$build_data[BUILD_CREATE][RES_CRYSTAL],
      RES_DEUTERIUM => -$build_data[BUILD_CREATE][RES_DEUTERIUM],
    ));

    doquery("UPDATE {{users}} SET `que` = '{$que_item_string}' WHERE `id` = '{$user['id']}' LIMIT 1;");
    doquery('COMMIT;');

    sys_redirect($_SERVER['REQUEST_URI']);
  }
  catch (exception $e)
  {
    doquery('ROLLBACK;');
    $operation_result = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $e->getMessage()
    );
  }

  return $operation_result;
}

function eco_bld_tech_que_clear($user_id, $planet)
{
  doquery('START TRANSACTION');
  $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user_id} LIMIT 1 FOR UPDATE", true);
  $que_item = $user['que'] ? explode(',', $user['que']) : array();

  if(!empty($que_item))
  {
    doquery("UPDATE {{users}} SET `que` = '' WHERE `id` = {$user_id} LIMIT 1");
    if($que_item[QI_PLANET_ID])
    {
      $planet['id'] = $que_item[QI_PLANET_ID];
    }

    $planet = $planet['id'] ? doquery("SELECT * FROM {{planets}} WHERE `id` ={$planet['id']} LIMIT 1 FOR UPDATE", true) : $planet;

    $tech_id = $que_item[QI_UNIT_ID];
    $build_data = eco_get_build_data($user, false, $tech_id, mrc_get_level($user, $planet, $tech_id, false, true), true);

    db_change_units($user, $planet, array(
      RES_METAL     => $build_data[BUILD_CREATE][RES_METAL],
      RES_CRYSTAL   => $build_data[BUILD_CREATE][RES_CRYSTAL],
      RES_DEUTERIUM => $build_data[BUILD_CREATE][RES_DEUTERIUM],
    ));

    doquery('COMMIT;');
  }
  else
  {
    doquery('ROLLBACK;');
  }
  sys_redirect($_SERVER['REQUEST_URI']);
}

function eco_bld_tech(&$user, &$planet, $que = array())
{
  global $config, $lang, $time_now;

  lng_include('buildings');
  lng_include('infos');

  if(!mrc_get_level($user, $planet, STRUC_LABORATORY))
  {
    message($lang['no_laboratory'], $lang['tech'][UNIT_TECHNOLOGIES]);
  }

  switch(sys_get_param_str('action'))
  {
    case 'clear':
    case 'trim':
      eco_bld_tech_que_clear($user['id'], $planet);
    break;

    case 'build':
      $operation_result = eco_bld_tech_research($user, $planet);
    break;
  }

  $template = gettemplate('buildings_research', true);
  if(!empty($operation_result))
  {
    $template->assign_block_vars('result', $operation_result);
  }

  $fleet_list            = flt_get_fleets_to_planet($planet);

  foreach(sn_get_groups('tech') as $Tech)
  {
    if(eco_can_build_unit($user, $planet, $Tech) != BUILD_ALLOWED)
    {
      continue;
    }

    $building_level      = mrc_get_level($user, '' , $Tech, false, true);
    $level_bonus         = max(0, mrc_get_level($user, '' , $Tech) - $building_level);
    $build_data          = eco_get_build_data($user, $planet, $Tech, $building_level);

    $temp[RES_METAL]     = floor($planet['metal'] - $build_data[BUILD_CREATE][RES_METAL]);
    $temp[RES_CRYSTAL]   = floor($planet['crystal'] - $build_data[BUILD_CREATE][RES_CRYSTAL]);
    $temp[RES_DEUTERIUM] = floor($planet['deuterium'] - $build_data[BUILD_CREATE][RES_DEUTERIUM]);

    $template->assign_block_vars('production', array(
      'ID'                 => $Tech,
      'NAME'               => $lang['tech'][$Tech],
      'LEVEL'              => $building_level,
      'LEVEL_NEXT'         => $building_level + 1,
      'LEVEL_BONUS'        => $level_bonus,
      'DESCRIPTION'        => $lang['info'][$Tech]['description_short'],

      'BUILD_CAN'          => $build_data['CAN'][BUILD_CREATE],
      'TIME'               => pretty_time($build_data[RES_TIME][BUILD_CREATE]),
      'METAL'              => $build_data[BUILD_CREATE][RES_METAL],
      'CRYSTAL'            => $build_data[BUILD_CREATE][RES_CRYSTAL],
      'DEUTERIUM'          => $build_data[BUILD_CREATE][RES_DEUTERIUM],
      'ENERGY'             => $build_data[BUILD_CREATE][RES_ENERGY],

      'METAL_PRINT'        => pretty_number($build_data[BUILD_CREATE][RES_METAL], true, $planet['metal']),
      'CRYSTAL_PRINT'      => pretty_number($build_data[BUILD_CREATE][RES_CRYSTAL], true, $planet['crystal']),
      'DEUTERIUM_PRINT'    => pretty_number($build_data[BUILD_CREATE][RES_DEUTERIUM], true, $planet['deuterium']),
      'ENERGY_PRINT'       => pretty_number($build_data[BUILD_CREATE][RES_ENERGY], true, max(1, $planet['energy_max'] - $planet['energy_used'])),

      'METAL_REST'         => pretty_number($temp[RES_METAL], true, true),
      'CRYSTAL_REST'       => pretty_number($temp[RES_CRYSTAL], true, true),
      'DEUTERIUM_REST'     => pretty_number($temp[RES_DEUTERIUM], true, true),
      'METAL_REST_NUM'     => $temp[RES_METAL],
      'CRYSTAL_REST_NUM'   => $temp[RES_CRYSTAL],
      'DEUTERIUM_REST_NUM' => $temp[RES_DEUTERIUM],

      'METAL_FLEET'        => pretty_number($temp[RES_METAL] + $fleet_list['own']['total'][RES_METAL], true, true),
      'CRYSTAL_FLEET'      => pretty_number($temp[RES_CRYSTAL] + $fleet_list['own']['total'][RES_CRYSTAL], true, true),
      'DEUTERIUM_FLEET'    => pretty_number($temp[RES_DEUTERIUM] + $fleet_list['own']['total'][RES_DEUTERIUM], true, true),

      'BUILD_CAN2'         => $build_data['CAN'][BUILD_CREATE],
    ));
  }

  $que_length = 0;
  if($user['que'])
  {
    $que_item = $user['que'] ? explode(',', $user['que']) : array();
    $unit_id = $que_item[QI_UNIT_ID];
    $unit_level = mrc_get_level($user, $planet, $unit_id, false, true);
    $unit_data = eco_get_build_data($user, $planet, $unit_id, $unit_level);

    $template->assign_block_vars('que', array(
      'ID' => $unit_id,
      'QUE' => QUE_RESEARCH,
      'NAME' => $lang['tech'][$unit_id],
      'TIME' => $que_item[QI_TIME],
      'TIME_FULL' => $unit_data[RES_TIME][BUILD_CREATE],
      'AMOUNT' => 1,
      'LEVEL' => $unit_level + 1,
    ));

    $que_length++;
  }

  $template->assign_vars(array(
    'PAGE_HEADER'        => $page_header = $lang['tech'][UNIT_TECHNOLOGIES] . ($user['user_as_ally'] ? "&nbsp;{$lang['sys_of_ally']}&nbsp;{$user['username']}" : ''),
    'FLEET_OWN_COUNT'    => $fleet_list['own']['count'],
    'QUE_ID'             => QUE_RESEARCH,

    'RESEARCH_ONGOING'   => (boolean)$user['que'],
  ));

  display(parsetemplate($template), $page_header);
}

?>
