<?php

function eco_bld_tech(&$user, &$planet, $que = array())
{
  global $config, $lang, $global_que;

  lng_include('buildings');
  lng_include('infos');

  if(!mrc_get_level($user, $planet, STRUC_LABORATORY))
  {
    message($lang['no_laboratory'], $lang['tech'][UNIT_TECHNOLOGIES]);
  }

  if(eco_unit_busy($user, $planet, UNIT_TECHNOLOGIES))
  {
    message($lang['eco_bld_msg_err_laboratory_upgrading'], $lang['tech'][UNIT_TECHNOLOGIES]);
  }

  switch(sys_get_param_str('action'))
  {
    case 'clear':que_delete(QUE_RESEARCH, $user, $planet, true);break;
    case 'trim' :que_delete(QUE_RESEARCH, $user, $planet, false);break;
    case 'build':$operation_result = que_build($user, $planet);break;
    //case 'build':$operation_result = eco_bld_tech_research($user, $planet);break;
  }

  $template = gettemplate('buildings_research', true);
  if(!empty($operation_result))
  {
    $template->assign_block_vars('result', $operation_result);
  }

  $fleet_list = flt_get_fleets_to_planet($planet);

  $ques = que_get($user['id'], QUE_RESEARCH);
  $que = &$ques['ques'][QUE_RESEARCH][$user['id']][0];
  que_tpl_parse($template, QUE_RESEARCH, $user, null, $que);

  $in_que = &$ques['in_que'][QUE_RESEARCH][$user['id']][0];
  foreach(sn_get_groups('tech') as $Tech)
  {
    if(eco_can_build_unit($user, $planet, $Tech) != BUILD_ALLOWED)
    {
      continue;
    }

    $building_level      = mrc_get_level($user, '' , $Tech, false, true);
    $level_bonus         = max(0, mrc_get_level($user, '' , $Tech) - $building_level);
    $level_qued          = $in_que[$Tech];
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

    'ARTIFACT_ID'         => ART_HEURISTIC_CHIP,
    'ARTIFACT_LEVEL'      => mrc_get_level($user, array(), ART_HEURISTIC_CHIP),
    'ARTIFACT_NAME'       => $lang['tech'][ART_HEURISTIC_CHIP],
    'REQUEST_URI'         => $_SERVER['REQUEST_URI'],

    // TODO: Вынести в модуль
    'CONFIG_RESEARCH_QUE' => $config->server_que_length_research,
  ));

  display(parsetemplate($template), $page_header);
}
