<?php

function admin_planet_edit_mode(&$template, &$admin_planet_edit_mode_list){return sn_function_call('admin_planet_edit_mode', array(&$template, &$admin_planet_edit_mode_list));}
function sn_admin_planet_edit_mode(&$template, &$admin_planet_edit_mode_list)
{
  global $lang;

  $admin_planet_edit_mode_list = array_merge(isset($admin_planet_edit_mode_list) ? $admin_planet_edit_mode_list : array(), array(
    'structures' => $lang['tech'][UNIT_STRUCTURES],
    'fleet' => $lang['tech'][UNIT_SHIPS],
    'defense' => $lang['tech'][UNIT_DEFENCE],
    'resources_loot' => $lang['tech'][UNIT_RESOURCES],
  ));

  $mode = sys_get_param_str('mode');
  $admin_planet_edit_mode_list_keys = array_keys($admin_planet_edit_mode_list);
  $mode = in_array($mode, $admin_planet_edit_mode_list_keys) ? $mode : $admin_planet_edit_mode_list_keys[0];

  return $mode;
}

function admin_planet_edit_template(&$template, $edit_planet_row, $mode){return sn_function_call('admin_planet_edit_template', array(&$template, $edit_planet_row, $mode));}
/**
 * @param template $template
 * @param $edit_planet_row
 * @param $mode
 */
function sn_admin_planet_edit_template(&$template, $edit_planet_row, $mode)
{
  global $lang;

  $unit_list = sn_get_groups($mode);
  if(empty($unit_list))
  {
    return;
  }
  $name_list = $lang['tech'];

  foreach($unit_list as $unit_id)
  {
    $template->assign_block_vars('unit', array(
      'ID'    => $unit_id,
      'NAME'  => $name_list[$unit_id],
      'TEXT'  => HelperString::numberFloorAndFormat(mrc_get_level($user, $edit_planet_row, $unit_id, false, true)),
      'VALUE' => '',
    ));
  }
}

function admin_planet_edit_query_string($unit_id, $unit_amount, $mode){return sn_function_call('admin_planet_edit_query_string', array($unit_id, $unit_amount, $mode));}
function sn_admin_planet_edit_query_string($unit_id, $unit_amount, $mode)
{
  if($unit_amount && in_array($unit_id, sn_get_groups($mode)))
  {
    $unit_amount = round($unit_amount);
    $unit_name = get_unit_param($unit_id, P_NAME);
    $result = "{$unit_name} = GREATEST(0, {$unit_name} + ({$unit_amount}))";
  }
  else
  {
    $result = '';
  }

  return $result;
}
