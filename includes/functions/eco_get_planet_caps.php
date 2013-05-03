<?php

function eco_get_planet_caps_modify_production(&$item, $key, $data)
{
  static $modifiers;

  if(!$modifiers)
  {
    $modifiers = sn_get_groups('modifiers');
  }
  $item = floor(mrc_modify_value($data['user'], $data['planet'], $modifiers[MODIFIER_RESOURCE_PRODUCTION], $item));
}

function eco_get_planet_caps(&$user, &$planet_row, $production_time = 0)
  // TODO Считать $production_time для термоядерной электростанции
{
  global $sn_data, $config;

  static $sn_groups, $sn_group_modifiers, $config_resource_multiplier, $config_eco_scale_storage;//, $sn_group_structures, $base_storage_size;

  if(!$sn_groups)
  {
    $sn_groups = &$sn_data['groups'];
    $sn_group_modifiers = &$sn_groups['modifiers'];
    $config_resource_multiplier = $config->resource_multiplier;
    $config_eco_scale_storage = $config->eco_scale_storage ? $config_resource_multiplier : 1;
  }

  $caps_real = array();
  foreach($sn_groups['storages'] as $unit_id)
  {
    foreach($sn_data[$unit_id]['storage'] as $resource_id => $function)
    {
      $caps_real['storage'][$resource_id][$unit_id] = floor($config_eco_scale_storage *
        mrc_modify_value($user, $planet_row, $sn_group_modifiers[MODIFIER_RESOURCE_CAPACITY], $function(mrc_get_level($user, $planet_row, $unit_id)))
      );
    }
  }

  if($planet_row['planet_type'] == PT_MOON)
  {
    return $caps_real;
  }

  $caps_real['production'][RES_METAL][0] = floor($config->metal_basic_income * $config_resource_multiplier);
  $caps_real['production'][RES_CRYSTAL][0] = floor($config->crystal_basic_income * $config_resource_multiplier);
  $caps_real['production'][RES_DEUTERIUM][0] = floor($config->deuterium_basic_income * $config_resource_multiplier);
  $caps_real['production'][RES_ENERGY][0] = floor($config->energy_basic_income * $config_resource_multiplier);

  foreach($sn_groups['factories'] as $unit_id)
  {
    $unit_data = $sn_data[$unit_id];
    $unit_level = mrc_get_level($user, $planet_row, $unit_id);
    $unit_load = $planet_row["{$sn_data[$unit_id]['name']}_porcent"];

    foreach($unit_data['production'] as $resource_id => $function)
    {
      $caps_real['production'][$resource_id][$unit_id] = $function($unit_level, $unit_load, $user, $planet_row) * $config_resource_multiplier;
    }
  }

  if($caps_real['production'][RES_ENERGY][STRUC_MINE_FUSION])
  {
    if($planet_row['deuterium'] < -$caps_real['production'][RES_DEUTERIUM][STRUC_MINE_FUSION])
    {
      $caps_real['production'][RES_DEUTERIUM][STRUC_MINE_FUSION] = $caps_real['production'][RES_ENERGY][STRUC_MINE_FUSION] = 0;
    }
  }

  array_walk_recursive($caps_real['production'], 'eco_get_planet_caps_modify_production', array('user' => $user, 'planet' => $planet_row));

  foreach($caps_real['production'][RES_ENERGY] as $energy)
  {
    $caps_real[RES_ENERGY][$energy >= 0 ? BUILD_CREATE : BUILD_DESTROY] += $energy;
  }

  $caps_real[RES_ENERGY][BUILD_DESTROY] = -$caps_real[RES_ENERGY][BUILD_DESTROY];

  $caps_real['efficiency'] = $caps_real[RES_ENERGY][BUILD_DESTROY] > $caps_real[RES_ENERGY][BUILD_CREATE]
    ? $caps_real[RES_ENERGY][BUILD_CREATE] / $caps_real[RES_ENERGY][BUILD_DESTROY]
    : 1;

  foreach($caps_real['production'] as $resource_id => &$resource_data)
  {
    if($resource_id != RES_ENERGY && $caps_real['efficiency'] != 1)
    {
      foreach($resource_data as $unit_id => &$resource_production)
      {
        if(!($unit_id == STRUC_MINE_FUSION && $resource_id == RES_DEUTERIUM) && $unit_id != 0)
        {
          $resource_production = floor($resource_production * $caps_real['efficiency']);
        }
      }
    }
    $caps_real['total'][$resource_id] = array_sum($resource_data);
  }

  foreach($caps_real['storage'] as $resource_id => &$resource_data)
  {
    $caps_real['total_storage'][$resource_id] = array_sum($resource_data);
  }

  $planet_row['caps'] = $caps_real;

  $planet_row['metal_max'] = $caps_real['total_storage'][RES_METAL];
  $planet_row['crystal_max'] = $caps_real['total_storage'][RES_CRYSTAL];
  $planet_row['deuterium_max'] = $caps_real['total_storage'][RES_DEUTERIUM];
  $planet_row['energy_max'] = $caps_real[RES_ENERGY][BUILD_CREATE];
  $planet_row['energy_used'] = $caps_real[RES_ENERGY][BUILD_DESTROY];

  return $caps_real;
}
