<?php
define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$template = gettemplate('admin/planet_compensate', true);

$galaxy_src = sys_get_param_int('galaxy_src');
$system_src = sys_get_param_int('system_src');
$planet_src = sys_get_param_int('planet_src');

$galaxy_dst = sys_get_param_int('galaxy_dst');
$system_dst = sys_get_param_int('system_dst');
$planet_dst = sys_get_param_int('planet_dst');

$bonus = sys_get_param_float('bonus', 1);

$username = sys_get_param_escaped('username');

if($galaxy_src)
{
  $errors = array();

  $owner = doquery("SELECT * FROM {{users}} WHERE username like '{$username}'", true);

  $planet = sys_o_get_updated($owner, array('galaxy' => $galaxy_src, 'system' => $system_src, 'planet' => $planet_src, 'planet_type' => 1), time());
  $que    = $planet['que'];
  $planet = $planet['planet'];
  if(!$planet)
  {
    $errors[] = $lang['adm_pl_comp_err_0'];
  }

  if($planet['destruyed'])
  {
    $errors[] = $lang['adm_pl_comp_err_1'];
  }

  if($planet['id_owner'] != $owner['id'] || !$username)
  {
    $errors[] = $lang['adm_pl_comp_err_4'];
  }

  $destination = sys_o_get_updated($owner, array('galaxy' => $galaxy_dst, 'system' => $system_dst, 'planet' => $planet_dst, 'planet_type' => 1), SN_TIME_NOW);
  $destination = $destination['planet'];
  if(!$destination)
  {
    $errors[] = $lang['adm_pl_comp_err_2'];
  }

  if($planet['id'] == $destination['id'])
  {
    $errors[] = $lang['adm_pl_comp_err_5'];
  }

  if($planet['id_owner'] != $destination['id_owner'])
  {
    $errors[] = $lang['adm_pl_comp_err_3'];
  }

  if(!empty($errors))
  {
    foreach($errors as $error)
    {
      $template->assign_block_vars('error', array(
        'TEXT' => $error,
      ));
    }
  }
  else
  {
    $template->assign_var('CHECK', 1);

    killer_add_planet($planet);

    $moon = doquery("SELECT * FROM {{planets}} WHERE galaxy = '{$galaxy_src}' AND system = '{$system_src}' AND planet = '{$planet_src}' AND planet_type = 3;", true);
    if($moon)
    {
      $moon = sys_o_get_updated($owner, $moon, SN_TIME_NOW);
      $moon = $moon['planet'];
      killer_add_planet($moon);
    }

    foreach(sn_get_groups('resources_loot') as $resource_id)
    {
      $resource_name = $sn_data[$resource_id]['name'];
      $template->assign_var("{$resource_name}_cost", $final_cost[$resource_id]);
      $final_cost[$resource_id] = floor($final_cost[$resource_id] * $bonus);
      $template->assign_var("{$resource_name}_bonus", $final_cost[$resource_id]);
    }

    if($_GET['btn_confirm'])
    {
      doquery("UPDATE {{planets}} SET metal = metal + '{$final_cost[RES_METAL]}', crystal = crystal + '{$final_cost[RES_CRYSTAL]}', deuterium = deuterium + '{$final_cost[RES_DEUTERIUM]}' WHERE id = {$destination['id']};");

      $time = time() + 24 * 60 * 60;
      doquery("UPDATE {{planets}} SET id_owner = 0, destruyed = '{$time}' WHERE id = {$planet['id']};");
      if($moon)
      {
        doquery("UPDATE {{planets}} SET id_owner = 0, destruyed = '{$time}' WHERE id = {$moon['id']};");
      }

      $template->assign_var('CHECK', 2);
    }
  }
}

$template->assign_vars(array(
  'galaxy_src' => $galaxy_src,
  'system_src' => $system_src,
  'planet_src' => $planet_src,

  'galaxy_dst' => $galaxy_dst,
  'system_dst' => $system_dst,
  'planet_dst' => $planet_dst,

  'bonus' => $bonus,

  'username' => $username,
));

display(parsetemplate($template, $parse), $lang['adm_pl_comp_title'], false, '', true );

function killer_add_planet($planet)
{
  global $final_cost;

  $final_cost = array();
  $sn_group_resources_loot = sn_get_groups('resources_loot');
  foreach($sn_group_resources_loot as &$value)
  {
    $value = get_unit_param($value, 'name');
  }

  foreach(sn_get_groups('structures') as $unit_id)
  {
    $build_level = $planet[get_unit_param($unit_id, 'name')];
    if($build_level > 0)
    {
      $unit_cost = get_unit_param($unit_id, 'cost');
      $build_factor = $unit_cost['factor'] != 1 ? (1 - pow($unit_cost['factor'], $build_level)) / (1 - $unit_cost['factor']) : $unit_cost['factor'];
      foreach($sn_group_resources_loot as $resource_id => $resource_name)
      {
        $final_cost[$resource_id] += isset($unit_cost[$resource_id]) && $unit_cost[$resource_id] > 0 ? floor($unit_cost[$resource_id] * $build_factor) : 0;
      }
    }
  }

  foreach(sn_get_groups(array('defense', 'fleet')) as $unit_id)
  {
    $unit_count = $planet[get_unit_param($unit_id, 'name')];
    if($unit_count > 0)
    {
      $unit_cost = get_unit_param($unit_id, 'cost');
      foreach($sn_group_resources_loot as $resource_id => $resource_name)
      {
        $final_cost[$resource_id] += isset($unit_cost[$resource_id]) && $unit_cost[$resource_id] > 0 ? floor($unit_cost[$resource_id] * $unit_count) : 0;
      }
    }
  }

  foreach($sn_group_resources_loot as $resource_id => $resource_name)
  {
    $final_cost[$resource_id] += floor($planet[$resource_name]);
  }
}
