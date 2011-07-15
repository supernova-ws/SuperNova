<?php

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if ($user['authlevel'] < 2)
{
  AdminMessage($lang['adm_err_denied']);
}

$mode = sys_get_param_str('mode');
$mode = in_array($mode, array('structures', 'fleet', 'defense', 'resources_loot')) ? $mode : 'structures';

$planet_id = sys_get_param_int('planet_id');

$unit_list = sys_get_param('unit_list');
if(sys_get_param('change_data'))
{
  $query_string = array();
  foreach($unit_list as $unit_id => $unit_amount)
  {
    if(!$unit_amount || !in_array($unit_id, $sn_data['groups'][$mode]))
    {
      continue;
    }
    $unit_amount = intval($unit_amount);
    $query_string[] = "{$sn_data[$unit_id]['name']} = GREATEST(0, {$sn_data[$unit_id]['name']} + ($unit_amount))";
  }

  $query_string = implode(', ', $query_string);

  if($query_string)
  {
    doquery("UPDATE {{planets}} SET {$query_string} WHERE id = {$planet_id} LIMIT 1;");
  }
}

$template = gettemplate('admin/admin_planet_edit', true);

if($planet_id)
{
  $edit_planet_row = doquery("SELECT * FROM {{planets}} WHERE `id` = {$planet_id}", '', true);
}

foreach($sn_data['groups'][$mode] as $unit_id)
{
  $template->assign_block_vars('unit', array(
    'ID' => $unit_id, 
    'NAME' => $lang['tech'][$unit_id],
    'AMOUNT' => (int)$edit_planet_row[$sn_data[$unit_id]['name']],
  ));
}

$template->assign_vars(array(
  'MODE' => $mode,
  'PLANET_ID' => $planet_id,
  'PLANET_NAME' => empty($edit_planet_row) ? '' : $lang['sys_planet_type'][$edit_planet_row['planet_type']] . ' ' . uni_render_planet($edit_planet_row),
  'PAGE_HINT' => $lang['adm_planet_edit_hint'],
));

display($template, $lang['adm_am_ttle'], false, '', true);

?>
