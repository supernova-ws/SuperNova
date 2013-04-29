<?php

function eco_bld_tech_research($user, $planet)
{
  global $lang, $global_que, $config;

  try
  {
    $tech_id = sys_get_param_int('tech');
    if(!in_array($tech_id, sn_get_groups('tech')))
    {
      // TODO: Hack attempt - warning here. Normally non-tech can't be passed from build page
      throw new exception($lang['eco_bld_msg_err_not_research'], ERR_ERROR);
    }
    sn_db_transaction_start();
    // Это нужно, что бы заблокировать пользователя и работу с очередями
    $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE", true);

    que_get_que($global_que, QUE_RESEARCH, $user['id'], $planet['id'], true);
    if(count($global_que[QUE_RESEARCH][0]) >= $config->server_que_length_research)
    {
      throw new exception($lang['eco_bld_msg_err_research_in_progress'], ERR_ERROR);
    }

    // Это нужно, что бы заблокировать планету от списания ресурсов
    $planet = $planet['id'] ? doquery("SELECT * FROM {{planets}} WHERE `id` = {$planet['id']} LIMIT 1 FOR UPDATE;", true) : $planet;
    $unit_level = mrc_get_level($user, $planet, $tech_id, false, true) + $global_que['in_que'][QUE_RESEARCH][0][$tech_id];
    $build_data = eco_get_build_data($user, $planet, $tech_id, $unit_level);

    if(eco_can_build_unit($user, $planet, $tech_id) != BUILD_ALLOWED)
    {
      // TODO: Hack attempt - warning here. Normally requirements check should be done on build page
      throw new exception($lang['eco_bld_msg_err_requirements_not_meet'], ERR_ERROR);
    }
    if(!$build_data['CAN'][BUILD_CREATE])
    {
      throw new exception($lang['eco_bld_resources_not_enough'], ERR_ERROR);
    }

    que_add_unit(QUE_RESEARCH, $tech_id, $user, $planet, $build_data, $unit_level + 1, 1);
    sn_db_transaction_commit();

    sys_redirect($_SERVER['REQUEST_URI']);
  }
  catch (exception $e)
  {
    sn_db_transaction_rollback();
    $operation_result = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $e->getMessage()
    );
  }

  return $operation_result;
}

function eco_bld_tech(&$user, &$planet, $que = array())
{
  global $config, $lang, $time_now, $global_que;

  lng_include('buildings');
  lng_include('infos');

  if(!mrc_get_level($user, $planet, STRUC_LABORATORY))
  {
    message($lang['no_laboratory'], $lang['tech'][UNIT_TECHNOLOGIES]);
  }

  switch(sys_get_param_str('action'))
  {
    case 'clear':que_delete(QUE_RESEARCH, $user, $planet, true);break;
    case 'trim':que_delete(QUE_RESEARCH, $user, $planet, false);break;
    case 'build':$operation_result = eco_bld_tech_research($user, $planet);break;
  }

  $template = gettemplate('buildings_research', true);
  if(!empty($operation_result))
  {
    $template->assign_block_vars('result', $operation_result);
  }

  $fleet_list = flt_get_fleets_to_planet($planet);

  que_tpl_parse($template, QUE_RESEARCH, $user);

  foreach(sn_get_groups('tech') as $Tech)
  {
    if(eco_can_build_unit($user, $planet, $Tech) != BUILD_ALLOWED)
    {
      continue;
    }

    $building_level      = mrc_get_level($user, '' , $Tech, false, true);
    $level_bonus         = max(0, mrc_get_level($user, '' , $Tech) - $building_level);
    $level_qued          = $global_que['in_que'][QUE_RESEARCH][0][$Tech];
    $build_data          = eco_get_build_data($user, $planet, $Tech, $building_level + $level_qued);

    $temp[RES_METAL]     = floor($planet['metal'] - $build_data[BUILD_CREATE][RES_METAL]);
    $temp[RES_CRYSTAL]   = floor($planet['crystal'] - $build_data[BUILD_CREATE][RES_CRYSTAL]);
    $temp[RES_DEUTERIUM] = floor($planet['deuterium'] - $build_data[BUILD_CREATE][RES_DEUTERIUM]);

    $template->assign_block_vars('production', array(
      'ID'                 => $Tech,
      'NAME'               => $lang['tech'][$Tech],
      'LEVEL'              => $building_level,
      'LEVEL_NEXT'         => $building_level + $level_qued + 1,
      'LEVEL_QUED'         => $level_qued,
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

  $template->assign_vars(array(
    'PAGE_HEADER'         => $page_header = $lang['tech'][UNIT_TECHNOLOGIES] . ($user['user_as_ally'] ? "&nbsp;{$lang['sys_of_ally']}&nbsp;{$user['username']}" : ''),
    'FLEET_OWN_COUNT'     => $fleet_list['own']['count'],
    'QUE_ID'              => QUE_RESEARCH,

    // TODO: Вынести в модуль
    'CONFIG_RESEARCH_QUE' => $config->server_que_length_research,
  ));

  display(parsetemplate($template), $page_header);
}
