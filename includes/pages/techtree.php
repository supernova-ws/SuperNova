<?php

/**
 * techtree.php
 *
 * version 2.0 copyright (c) 2012 by Gorlum for http://supernova.ws
 */

function sn_techtree_view($template = null)
{
  global $lang, $user, $planetrow;

  $tech_tree = array();
  foreach(get_unit_param('techtree') as $unit_group_id => $unit_list)
  {
    $tech_tree[] = array(
      'NAME' => $lang['tech'][$unit_group_id],
      'GROUP_ID' => $unit_group_id,
    );

    foreach($unit_list as $unit_id)
    {
      $sn_data_unit = get_unit_param($unit_id);
      $level_basic = $sn_data_unit[P_STACKABLE] ? 0 : mrc_get_level($user, $planetrow, $unit_id, false, true);
      $unit_level = $sn_data_unit[P_STACKABLE] ? 0 : mrc_get_level($user, $planetrow, $unit_id);
      $rendered_info = array(
        'ID' => $unit_id,
        'NAME' => $lang['tech'][$unit_id],
        'LEVEL' => $unit_level,
        'LEVEL_BASIC' => $level_basic,
        'LEVEL_BONUS' => max(0, $unit_level - $level_basic),
        'LEVEL_MAX' => $sn_data_unit['max'],
      );

      $rendered_info['.'][TPL_BLOCK_REQUIRE] = unit_requirements_render($user, $planetrow, $unit_id);
      $rendered_info['.']['grants'] = unit_requirements_render($user, $planetrow, $unit_id, P_UNIT_GRANTS);

      $tech_tree[] = $rendered_info;
    }
  }

  $template = SnTemplate::gettemplate('techtree', $template);
  $template_result['.']['techtree'] = $tech_tree;
  $template->assign_recursive($template_result);

  $template->assign_vars(array(
    'PAGE_HEADER' => $lang['tech'][UNIT_TECHNOLOGIES],
    'PLAYER_OPTION_TECH_TREE_TABLE' => SN::$user_options[PLAYER_OPTION_TECH_TREE_TABLE],
  ));

  return $template;
}
