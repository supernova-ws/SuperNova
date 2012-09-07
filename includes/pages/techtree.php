<?php

/**
 * techtree.php
 *
 * version 2.0 copyright (c) 2012 by Gorlum for http://supernova.ws
 */

//$sn_mvc['model']['techtree'][] = 'sn_techtree_model';
$sn_mvc['view']['techtree'][] = 'sn_techtree_view';

function sn_techtree_view($template = null)
{
  global $sn_data, $lang, $user, $planetrow;

  $tech_tree = array();
  foreach($sn_data['techtree'] as $unit_group_id => $unit_list)
  {
    $tech_tree[] = array(
      'NAME' => $lang['tech'][$unit_group_id],
    );

    foreach($unit_list as $unit_id)
    {
      $sn_data_unit = &$sn_data[$unit_id];
      $level_basic = $sn_data_unit['stackable'] ? 0 : mrc_get_level($user, $planetrow, $unit_id, false, true);
      $unit_level = $sn_data_unit['stackable'] ? 0 : mrc_get_level($user, $planetrow, $unit_id);
      $rendered_info = array(
        'ID' => $unit_id,
        'NAME' => $lang['tech'][$unit_id],
        'LEVEL' => $unit_level,
        'LEVEL_BASIC' => $level_basic,
        'LEVEL_BONUS' => max(0, $unit_level - $level_basic),
        'LEVEL_MAX' => $sn_data_unit['max'],
      );

      $rendered_info['.']['require'] = unit_requirements_render($user, $planetrow, $unit_id);

      $tech_tree[] = $rendered_info;
    }
  }

  $template = gettemplate('techtree', $template);
  $template_result['.']['techtree'] = $tech_tree;
  $template->assign_recursive($template_result);

  return $template;
}

?>
