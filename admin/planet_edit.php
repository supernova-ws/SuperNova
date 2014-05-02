<?php

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

// if($user['authlevel'] < 2)
if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}
AdminMessage('Временно не работает');

require("includes/admin_planet_edit.inc.{$phpEx}");

$template = gettemplate('admin/admin_planet_edit', true);

$mode = admin_planet_edit_mode($template, $admin_planet_edit_mode_list);
$planet_id = sys_get_param_id('planet_id');

$unit_list = sys_get_param('unit_list');
if(sys_get_param('change_data') && !empty($unit_list))
{
  $query_string = array();
  foreach($unit_list as $unit_id => $unit_amount)
  {
    if($unit_query_string = admin_planet_edit_query_string($unit_id, $unit_amount, $mode))
    {
      $query_string[] = $unit_query_string;
    }
  }

  $query_string = implode(', ', $query_string);

  if($query_string)
  {
    doquery("UPDATE {{planets}} SET {$query_string} WHERE id = {$planet_id} LIMIT 1;");
  }
}

if($planet_id)
{
  $edit_planet_row = doquery("SELECT * FROM {{planets}} WHERE `id` = {$planet_id}", '', true);
  admin_planet_edit_template($template, $edit_planet_row, $mode);
}

foreach($admin_planet_edit_mode_list as $page_mode => $mode_locale)
{
  $template->assign_block_vars('page_menu', array(
    'ID' => $page_mode,
    'TEXT' => $mode_locale,
  ));
}

$template->assign_vars(array(
  'MODE' => $mode,
  'PLANET_ID' => $planet_id,
  'PLANET_NAME' => empty($edit_planet_row) ? '' : $lang['sys_planet_type'][$edit_planet_row['planet_type']] . ' ' . uni_render_planet($edit_planet_row),
  'PAGE_HINT' => $lang['adm_planet_edit_hint'],
));

display($template, $lang['adm_am_ttle'], false, '', true);
