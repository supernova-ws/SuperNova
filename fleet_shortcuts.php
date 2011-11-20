<?php

/**
 * shortcuts.php
 *
 * @v4 Security checks by Gorlum for http://supernova.ws
 * @v2 (c) copyright 2010 by Gorlum for http://supernova.ws
 *
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = gettemplate('fleet_shortcuts', true);

$mode        = sys_get_param_str('mode');
$shortcut_id = sys_get_param_id('id');
$text        = sys_get_param_str('text');
if (!empty($text))
{
  $galaxy      = sys_get_param_int('galaxy');
  $system      = sys_get_param_int('system');
  $planet      = sys_get_param_int('planet');
  $planet_type = sys_get_param_int('planet_type');

  if ($mode == 'edit')
  {
    doquery( "UPDATE {{shortcut}} SET `shortcut_text`='{$text}', `shortcut_galaxy`='{$galaxy}', shortcut_system = '{$system}', shortcut_planet = '{$planet}', shortcut_planet_type = '{$planet_type}' WHERE `shortcut_id`='{$shortcut_id}' AND `shortcut_user_id` = {$user['id']} LIMIT 1;");
  }
  else
  {
    doquery( "INSERT INTO {{shortcut}} SET `shortcut_user_id` = {$user['id']}, `shortcut_text`='{$text}', `shortcut_galaxy`='{$galaxy}', shortcut_system = '{$system}', shortcut_planet = '{$planet}', shortcut_planet_type = '{$planet_type}';");
  }
  $mode = '';
};

switch($mode)
{
  case 'del':
    doquery( "DELETE FROM {{shortcut}} WHERE `shortcut_id`={$shortcut_id} AND `shortcut_user_id` = {$user['id']} LIMIT 1;");
    $mode = '';
  break;

  case 'edit':
    $template->assign_var('ID', $shortcut_id);

  case 'copy':
    $shortcut = doquery("SELECT * FROM {{shortcut}} WHERE `shortcut_id` = {$shortcut_id} AND `shortcut_user_id` = {$user['id']} LIMIT 1;", '', true);
  break;
}

$query = doquery("SELECT * FROM {{shortcut}} WHERE shortcut_user_id = {$user['id']};");

$template->assign_vars(array(
  'MODE'        => $mode,
  'TEXT'        => $shortcut['shortcut_text'],
  'GALAXY'      => $shortcut['shortcut_galaxy'],
  'SYSTEM'      => $shortcut['shortcut_system'],
  'PLANET'      => $shortcut['shortcut_planet'],
  'PLANET_TYPE' => $shortcut['shortcut_planet_type'],
  "t{$shortcut['shortcut_planet_type']}" => 'SELECTED',
));

while ($shortcut = mysql_fetch_assoc($query))
{
  $template->assign_block_vars('shortcut', array(
    'ID'          => $shortcut['shortcut_id'],
    'TEXT'        => $shortcut['shortcut_text'],
    'COORDINATES' => uni_render_coordinates($shortcut, 'shortcut_'),
    'PLANET_TYPE' => $lang['sys_planet_type'][$shortcut['shortcut_planet_type']],
  ));
}

display($template, $lang['news_title']);

?>
