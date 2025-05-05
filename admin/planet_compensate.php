<?php

use DBAL\db_mysql;
use Planet\DBStaticPlanet;
use Unit\DBStaticUnit;

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

$template = SnTemplate::gettemplate('admin/planet_compensate', true);

$galaxy_src = sys_get_param_int('galaxy_src');
$system_src = sys_get_param_int('system_src');
$planet_src = sys_get_param_int('planet_src');

$galaxy_dst = sys_get_param_int('galaxy_dst');
$system_dst = sys_get_param_int('system_dst');
$planet_dst = sys_get_param_int('planet_dst');

$bonus = sys_get_param_float('bonus', 1);

$username_unsafe = sys_get_param_str_unsafe('username');
$username = sys_get_param_escaped('username');

if ($galaxy_src) {
  $errors = array();

  $owner = db_user_by_username($username_unsafe, true);
  $planet = DBStaticPlanet::db_planet_by_gspt($galaxy_src, $system_src, $planet_src, PT_PLANET);
  if (empty($planet)) {
    $errors[] = $lang['adm_pl_comp_err_0'];
  }
  if ($planet['destruyed']) {
    $errors[] = $lang['adm_pl_comp_err_1'];
  }
  if (empty($username) || empty($owner) || $planet['id_owner'] != $owner['id']) {
    $errors[] = $lang['adm_pl_comp_err_4'];
  }

  $destination = DBStaticPlanet::db_planet_by_gspt($galaxy_dst, $system_dst, $planet_dst, PT_PLANET);
  if (empty($destination)) {
    $errors[] = $lang['adm_pl_comp_err_2'];
  }
  if ($planet['id'] == $destination['id']) {
    $errors[] = $lang['adm_pl_comp_err_5'];
  }
  if ($planet['id_owner'] != $destination['id_owner']) {
    $errors[] = $lang['adm_pl_comp_err_3'];
  }

  $moon = DBStaticPlanet::db_planet_by_gspt($galaxy_src, $system_src, $planet_src, PT_MOON);
  if (!empty($errors)) {
    foreach ($errors as $error) {
      $template->assign_block_vars('error', array(
        'TEXT' => $error,
      ));
    }
  } else {
  db_mysql::db_transaction_start();
  SN::$gc->db->lockRecords([
    'users'   => [$owner['id'],],
    'planets' => [$planet['id'], $destination['id'], !empty($moon['id']) ? $moon['id'] : 0],
  ]);

  $planet = sys_o_get_updated($owner['id'], $planet['id'], SN_TIME_NOW);
  $que = $planet['que'];
  $planet = $planet['planet'];

  $destination = sys_o_get_updated($owner['id'], $destination['id'], SN_TIME_NOW);
  $destination = $destination['planet'];

    $template->assign_var('CHECK', 1);

    $final_cost = killer_add_planet($planet);

    if (!empty($moon)) {
      $moon = sys_o_get_updated($owner['id'], $moon['id'], SN_TIME_NOW);
      $moon = $moon['planet'];
      $final_cost = killer_add_planet($moon, $final_cost);
    }

    foreach (sn_get_groups('resources_loot') as $resource_id) {
      $resource_name = pname_resource_name($resource_id);
      $template->assign_var("{$resource_name}_cost", $final_cost[$resource_id]);
      $final_cost[$resource_id] = floor($final_cost[$resource_id] * $bonus);
      $template->assign_var("{$resource_name}_bonus", $final_cost[$resource_id]);
    }

    if ($_GET['btn_confirm']) {
      $time = SN_TIME_NOW + PERIOD_DAY;

      DBStaticUnit::db_unit_list_delete($planet['id_owner'], LOC_PLANET, $planet['id']);
      DBStaticPlanet::db_planet_set_by_id($planet['id'], "id_owner = 0, destruyed = {$time}");
      if (!empty($moon)) {
        DBStaticUnit::db_unit_list_delete($planet['id_owner'], LOC_PLANET, $moon['id']);
        DBStaticPlanet::db_planet_set_by_id($moon['id'], "id_owner = 0, destruyed = {$time}");
      }

      DBStaticPlanet::db_planet_set_by_id($destination['id'], "metal = metal + '{$final_cost[RES_METAL]}', crystal = crystal + '{$final_cost[RES_CRYSTAL]}', deuterium = deuterium + '{$final_cost[RES_DEUTERIUM]}'");
      $template->assign_var('CHECK', 2);
    }
    db_mysql::db_transaction_commit();
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

SnTemplate::display($template, $lang['adm_pl_comp_title']);

/**
 * @param array $planet
 * @param array $final_cost
 *
 * @return array|mixed
 */
function killer_add_planet($planet, $final_cost = []) {
  $sn_group_resources_loot = sn_get_groups('resources_loot');

  // Adding structures cost
  foreach (sn_get_groups('structures') as $unit_id) {
    $build_level = mrc_get_level($user, $planet, $unit_id, true, true);
    if ($build_level > 0) {
      $unit_cost = get_unit_param($unit_id, 'cost');
      $build_factor = $unit_cost['factor'] != 1 ? (1 - pow($unit_cost['factor'], $build_level)) / (1 - $unit_cost['factor']) : $unit_cost['factor'];
      foreach ($sn_group_resources_loot as $resource_id) {
        $final_cost[$resource_id] += isset($unit_cost[$resource_id]) && $unit_cost[$resource_id] > 0 ? floor($unit_cost[$resource_id] * $build_factor) : 0;
      }
    }
  }
  // Adding fleet and defense cost
  foreach (sn_get_groups(array('defense', 'fleet')) as $unit_id) {
    $unit_count = mrc_get_level($user, $planet, $unit_id, true, true);
    if ($unit_count > 0) {
      $unit_cost = get_unit_param($unit_id, 'cost');
      foreach ($sn_group_resources_loot as $resource_id) {
        $final_cost[$resource_id] += isset($unit_cost[$resource_id]) && $unit_cost[$resource_id] > 0 ? floor($unit_cost[$resource_id] * $unit_count) : 0;
      }
    }
  }
  // Adding plain resources
  foreach ($sn_group_resources_loot as $resource_id) {
    $final_cost[$resource_id] += floor(mrc_get_level($user, $planet, $resource_id, true, true));
  }

  return $final_cost;
}
