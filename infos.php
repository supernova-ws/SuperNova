<?php

/**
 * @copyright Copyright (c) 2009-2017 by Gorlum for http://supernova.ws
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$unit_id = sys_get_param_id('gid');
if ($unit_id == RES_DARK_MATTER) {
  sys_redirect('dark_matter.php');
}

if ($unit_id == RES_METAMATTER) {
  sys_redirect('metamatter.php');
}

lng_include('infos');
if (
  !$unit_id
  ||
  (!get_unit_param($unit_id) && !isset($lang['info'][$unit_id]) && !$unit_id == UNIT_CAN_NOT_BE_BUILD)
) {
  sys_redirect('index.php?page=techtree');
}

$template = SnTemplate::gettemplate('novapedia', true);

$unit_data = get_unit_param($unit_id);
$unit_type = $unit_data['type'];

if ($unit_type == UNIT_SHIPS) {
  $template_result['UNIT_IS_SHIP'] = true;

  $ship_data = get_ship_data($unit_id, $user);

  $template_result += array(
    'BASE_SPEED'         => HelperString::numberFloorAndFormat($ship_data['speed_base']),
    'ACTUAL_SPEED'       => HelperString::numberFloorAndFormat($ship_data['speed']),
    'BASE_CONSUMPTION'   => HelperString::numberFloorAndFormat($ship_data['consumption_base']),
    'ACTUAL_CONSUMPTION' => HelperString::numberFloorAndFormat($ship_data['consumption']),

    'BASE_CAPACITY'   => HelperString::numberFloorAndFormat($unit_data['capacity']),
    'ACTUAL_CAPACITY' => HelperString::numberFloorAndFormat($ship_data['capacity']),
  );

  $engine_template_info = array();
  foreach ($unit_data['engine'] as $unit_engine_data) {
    $unit_engine_data = get_engine_data($user, $unit_engine_data);

    $engine_template_info[] = array(
      'NAME'               => $lang['tech'][$unit_engine_data['tech']],
      'MIN_LEVEL'          => $unit_engine_data['min_level'],
      'USER_TECH_LEVEL'    => mrc_get_level($user, null, $unit_engine_data['tech']),
      'BASE_SPEED'         => HelperString::numberFloorAndFormat($unit_engine_data['speed_base']),
      'BASE_CONSUMPTION'   => HelperString::numberFloorAndFormat($unit_engine_data['consumption_base']),
      'ACTUAL_SPEED'       => HelperString::numberFloorAndFormat($unit_engine_data['speed']),
      'ACTUAL_CONSUMPTION' => HelperString::numberFloorAndFormat($unit_engine_data['consumption']),
    );
  }
  $template_result['.']['engine'] = $engine_template_info;

}


$sn_data_group_combat = sn_get_groups('combat');
if (in_array($unit_id, $sn_data_group_combat)) {
  $template_result['UNIT_IS_COMBAT'] = true;

  $unit_durability = $unit_data['shield'] + $unit_data['armor'];

  $volley_arr = $rapid_to = $rapid_from = array();
  $str_rapid_from = '';
  $str_rapid_to = '';
  foreach ($sn_data_group_combat as $enemy_id) {
    $enemy_data = get_unit_param($enemy_id);
    $enemy_durability = $enemy_data['shield'] + $enemy_data['armor'];

    $rapid = $unit_data['attack'] * (isset($unit_data['amplify'][$enemy_id]) ? $unit_data['amplify'][$enemy_id] : 1) / $enemy_durability;
    if ($rapid >= 1) {
      $volley_arr[$enemy_id]['TO'] = floor($rapid);
    }

    $rapid = $enemy_data['attack'] * (isset($enemy_data['amplify'][$unit_id]) ? $enemy_data['amplify'][$unit_id] : 1) / $unit_durability;
    if ($rapid >= 1) {
      $volley_arr[$enemy_id]['FROM'] = floor($rapid);
    }
  }
  foreach ($volley_arr as $enemy_id => &$rapid) {
    $rapid['ENEMY_ID'] = $enemy_id;
    $rapid['ENEMY_NAME'] = $lang['tech'][$enemy_id];
  }
  $template_result['.']['volley'] = $volley_arr;

  $template_result += array(
    'BASE_ARMOR'  => HelperString::numberFloorAndFormat($unit_data['armor']),
    'BASE_SHIELD' => HelperString::numberFloorAndFormat($unit_data['shield']),
    'BASE_WEAPON' => HelperString::numberFloorAndFormat($unit_data['attack']),

    'ACTUAL_ARMOR'  => HelperString::numberFloorAndFormat(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_ARMOR), $unit_data['armor'])),
    'ACTUAL_SHIELD' => HelperString::numberFloorAndFormat(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_SHIELD), $unit_data['shield'])),
    'ACTUAL_WEAPON' => HelperString::numberFloorAndFormat(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_WEAPON), $unit_data['attack'])),
  );

}

if ($lang['info'][$unit_id]['effect']) {
  $template_result['UNIT_EFFECT'] = $lang['info'][$unit_id]['effect'];
}

if ($unit_data[P_BONUS_VALUE]) {
  $unit_bonus = !$unit_data[P_BONUS_VALUE] || $unit_data[P_BONUS_TYPE] == BONUS_ABILITY ? '' : (
    ($unit_data[P_BONUS_VALUE] >= 0 ? '+' : '') . $unit_data[P_BONUS_VALUE] . ($unit_data[P_BONUS_TYPE] == BONUS_PERCENT ? '%' : '')
  );
  $template_result['UNIT_BONUS'] = $unit_bonus;
}

$template_result += array(
  'PAGE_HEADER' => $lang['wiki_title'],

  'UNIT_ID'          => $unit_id,
  'UNIT_NAME'        => $lang['tech'][$unit_id],
  'UNIT_TYPE'        => $unit_type,
  'UNIT_TYPE_NAME'   => $lang['tech'][$unit_type],
  'UNIT_DESCRIPTION' => $lang['info'][$unit_id]['description'],

  'UNIT_IMAGE_LARGE' => SN::$gc->skinModel->isImageFileExists($unit_id . '_large'),
);

$template_result['.'][TPL_BLOCK_REQUIRE] = unit_requirements_render($user, $planetrow, $unit_id);

$template->assign_recursive($template_result);
SnTemplate::display($template);
