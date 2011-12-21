<?php

/**
 * ResearchBuildingPage.php
 *
 * 2.0 - full rewrite
 * @version 1.2s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.2
 * @copyright 2008 by Chlorel for XNova
 */
// History revision
// 1.0 - Release initiale / modularisation / Reecriture / Commentaire / Mise en forme
// 1.1 - BUG affichage de la techno en cours
// 1.2 - Restructuration modification pour permettre d'annuller proprement une techno en cours

function eco_lab_is_building($que)
{
  global $config;

  return $que['in_que_abs'][STRUC_LABORATORY] && !$config->BuildLabWhileRun ? true : false;
}

function ResearchBuildingPage(&$user, &$planet, $que)
{
  global $config, $sn_data, $lang, $time_now;

  if(!$planet[$sn_data[STRUC_LABORATORY]['name']])
  {
    message($lang['no_laboratory'], $lang['tech'][TECH_TECHNOLOGY]);
  }

  $build_planet = !$user['b_tech_planet'] || $user['b_tech_planet'] == $planet['id'] ? $planet : doquery("SELECT * FROM {{planets}} WHERE `id` = {$user['b_tech_planet']} LIMIT 1;", '', true);

  $message = '';
  switch(sys_get_param_str('action'))
  {
    case 'clear':
    case 'trim':
      $tech_id = $build_planet['b_tech_id'];
      if($tech_id)
      {
        $build_data = eco_get_build_data($user, $build_planet, $tech_id, $user[$sn_data[$tech_id]['name']]);
        doquery("UPDATE {{planets}} SET `b_tech_id` = '0', `b_tech` = '0', 
          `metal` = `metal` + {$build_data[BUILD_CREATE][RES_METAL]}, `crystal` = `crystal` + '{$build_data[BUILD_CREATE][RES_CRYSTAL]}', `deuterium` = `deuterium` + '{$build_data[BUILD_CREATE][RES_DEUTERIUM]}' 
          WHERE `id` = '{$build_planet['id']}' LIMIT 1;");
        doquery("UPDATE {{users}} SET `b_tech_planet` = '0' WHERE `id` = '{$user['id']}' LIMIT 1;");
        header("Location: {$_SERVER['PHP_SELF']}?mode=" . QUE_RESEARCH);
        die();
      }
    break;
    
    case 'build':
      $tech_id = sys_get_param_int('tech');
      $build_data            = eco_get_build_data($user, $planet, $tech_id, $user[$sn_data[$tech_id]['name']]);
      if($build_planet['b_tech_id'])
      {
        $message = $lang['build_research_in_progress'];
      }
      elseif(!eco_lab_is_building($que) && in_array($tech_id, $sn_data['groups']['tech']) && eco_can_build_unit($user, $planet, $tech_id) == BUILD_ALLOWED && $build_data['CAN'][BUILD_CREATE])
      {
        $build_time_end        = $build_data[RES_TIME][BUILD_CREATE] + $time_now;
        doquery("UPDATE {{planets}} SET `b_tech_id` = '{$tech_id}', `b_tech` = '{$build_time_end}', 
          `metal` = `metal` - {$build_data[BUILD_CREATE][RES_METAL]}, `crystal` = `crystal` - '{$build_data[BUILD_CREATE][RES_CRYSTAL]}', `deuterium` = `deuterium` - '{$build_data[BUILD_CREATE][RES_DEUTERIUM]}' 
          WHERE `id` = '{$planet['id']}' LIMIT 1;");
        doquery("UPDATE {{users}} SET `b_tech_planet` = '{$planet['id']}' WHERE `id` = '{$user['id']}' LIMIT 1;");

        header("Location: {$_SERVER['PHP_SELF']}?mode=" . QUE_RESEARCH);
        die();
      }
    break;
  }
  $message = $message ? $message : (eco_lab_is_building($que) ? $lang['labo_on_update'] : '');

  $template = gettemplate('buildings_research', true);
  $fleet_list            = flt_get_fleets_to_planet($planet);

  foreach($sn_data['groups']['tech'] as $Tech)
  {
    if(eco_can_build_unit($user, $planet, $Tech) != BUILD_ALLOWED)
    {
      continue;
    }

    $building_level      = $user[$sn_data[$Tech]['name']];
    $build_data          = eco_get_build_data($user, $planet, $Tech, $building_level);

    $temp[RES_METAL]     = floor($planet['metal'] - $build_data[BUILD_CREATE][RES_METAL]); // + $fleet_list['own']['total'][RES_METAL]
    $temp[RES_CRYSTAL]   = floor($planet['crystal'] - $build_data[BUILD_CREATE][RES_CRYSTAL]); // + $fleet_list['own']['total'][RES_CRYSTAL]
    $temp[RES_DEUTERIUM] = floor($planet['deuterium'] - $build_data[BUILD_CREATE][RES_DEUTERIUM]); // + $fleet_list['own']['total'][RES_DEUTERIUM]

    $template->assign_block_vars('production', array(
      'ID'                 => $Tech,
      'NAME'               => $lang['tech'][$Tech],
      'LEVEL'              => $building_level,
      'LEVEL_NEXT'         => $building_level + 1,
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

//      'BUILD_CAN2'         => IsElementBuyable($user, $planet, $Tech) && !eco_lab_is_building($que),
      'BUILD_CAN2'         => $build_data['CAN'][BUILD_CREATE], // ($user, $planet, $Tech) && !eco_lab_is_building($que),
    ));
  }

  $que_length = 0;
  if($build_planet['b_tech_id'])
  {
    $unit_id = $build_planet['b_tech_id'];
    $unit_data = eco_get_build_data($user, $build_planet, $unit_id, $user[$sn_data[$unit_id]['name']]);

    $template->assign_block_vars('que', array(
      'ID' => $unit_id,
      'QUE' => QUE_RESEARCH,
      'NAME' => $lang['tech'][$unit_id],
      'TIME' => $build_planet['b_tech'] - $time_now,
      'TIME_FULL' => $unit_data[RES_TIME][BUILD_CREATE],
      'AMOUNT' => 1,
      'LEVEL' => $user[$sn_data[$unit_id]['name']] + 1,
    ));

    $que_length++;
  }

  $template->assign_vars(array(
    'PAGE_HEADER'        => $lang['tech'][TECH_TECHNOLOGY],
    'MESSAGE'            => $message,
    'FLEET_OWN_COUNT'    => $fleet_list['own']['count'],
    'QUE_ID'             => QUE_RESEARCH,

    'RESEARCH_ONGOING'   => $build_planet['b_tech_id'],
    'RESEARCH_TECH'      => $build_planet['b_tech_id'],
    'RESEARCH_TIME'      => $build_planet['b_tech'] - $time_now,
    'RESEARCH_HOME_ID'   => $build_planet['id'],
    'RESEARCH_HOME_NAME' => $build_planet['id'] != $planet['id'] ? $build_planet['name'] : '',
  ));

  display(parsetemplate($template), $lang['tech'][TECH_TECHNOLOGY]);
}

?>
