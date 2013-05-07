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

  $caps = array();
  foreach($sn_groups['storages'] as $unit_id)
  {
    foreach($sn_data[$unit_id]['storage'] as $resource_id => $function)
    {
      $caps['storage'][$resource_id][$unit_id] = floor($config_eco_scale_storage *
        mrc_modify_value($user, $planet_row, $sn_group_modifiers[MODIFIER_RESOURCE_CAPACITY], $function(mrc_get_level($user, $planet_row, $unit_id)))
      );
    }
  }

  if($planet_row['planet_type'] == PT_MOON)
  {
    return $caps;
  }

  $caps['production_full'][RES_METAL][0] = floor($config->metal_basic_income * $config_resource_multiplier);
  $caps['production_full'][RES_CRYSTAL][0] = floor($config->crystal_basic_income * $config_resource_multiplier);
  $caps['production_full'][RES_DEUTERIUM][0] = floor($config->deuterium_basic_income * $config_resource_multiplier);
  $caps['production_full'][RES_ENERGY][0] = floor($config->energy_basic_income * $config_resource_multiplier);

  foreach($sn_groups['factories'] as $unit_id)
  {
    $unit_data = $sn_data[$unit_id];
    $unit_level = mrc_get_level($user, $planet_row, $unit_id);
    $unit_load = $planet_row["{$sn_data[$unit_id]['name']}_porcent"];

    foreach($unit_data['production'] as $resource_id => $function)
    {
      $caps['production_full'][$resource_id][$unit_id] = $function($unit_level, $unit_load, $user, $planet_row) * $config_resource_multiplier;
    }
  }

  array_walk_recursive($caps['production_full'], 'eco_get_planet_caps_modify_production', array('user' => $user, 'planet' => $planet_row));

  foreach($caps['production_full'] as $resource_id => $resource_data)
  {
    $caps['total_production_full'][$resource_id] = array_sum($resource_data);
  }

  $caps['production'] = $caps['production_full'];

  if($caps['production'][RES_ENERGY][STRUC_MINE_FUSION])
  {
    $deuterium_balance = array_sum($caps['production'][RES_DEUTERIUM]);
    $energy_balance = array_sum($caps['production'][RES_ENERGY]);
//    pdump($production_time);
//    pdump($planet_row['deuterium'], 'deuterium');
//    pdump($deuterium_balance , '$deuterium_balance');
//    pdump($energy_balance , '$energy_balance ');
//    pdump(-$deuterium_balance * $production_time / 3600, '$deuterium_balance');
//    pdump(($deuterium_balance < 0 && $planet_row['deuterium'] <= -$deuterium_balance * $production_time / 3600));
    if($deuterium_balance < 0 || $energy_balance < 0)
//    if(($deuterium_balance < 0 || $energy_balance < 0) && (sn_floor($planet_row['deuterium']) <= 0 || $planet_row['deuterium'] <= -$deuterium_balance * $production_time / 3600))
//    if(sn_floor($planet_row['deuterium']) <= 0 || $planet_row['deuterium'] <= -$deuterium_balance * $production_time / 3600)
    {
      $caps['production'][RES_DEUTERIUM][STRUC_MINE_FUSION] = $caps['production'][RES_ENERGY][STRUC_MINE_FUSION] = 0;
    }
  }

  foreach($caps['production'][RES_ENERGY] as $energy)
  {
    $caps[RES_ENERGY][$energy >= 0 ? BUILD_CREATE : BUILD_DESTROY] += $energy;
  }

  $caps[RES_ENERGY][BUILD_DESTROY] = -$caps[RES_ENERGY][BUILD_DESTROY];

  $caps['efficiency'] = $caps[RES_ENERGY][BUILD_DESTROY] > $caps[RES_ENERGY][BUILD_CREATE]
    ? $caps[RES_ENERGY][BUILD_CREATE] / $caps[RES_ENERGY][BUILD_DESTROY]
    : 1;

  foreach($caps['production'] as $resource_id => &$resource_data)
  {
    if($caps['efficiency'] != 1)
    {
      foreach($resource_data as $unit_id => &$resource_production)
      {
        if(!($unit_id == STRUC_MINE_FUSION && $resource_id == RES_DEUTERIUM) && $unit_id != 0 && !($resource_id == RES_ENERGY && $resource_production >= 0))
        {
          $resource_production = $resource_production * $caps['efficiency'];
        }
      }
    }
    $caps['total'][$resource_id] = array_sum($resource_data);
  }

  foreach($caps['storage'] as $resource_id => &$resource_data)
  {
    $caps['total_storage'][$resource_id] = array_sum($resource_data);
  }

  $planet_row['caps'] = $caps;

  $planet_row['metal_max'] = $caps['total_storage'][RES_METAL];
  $planet_row['crystal_max'] = $caps['total_storage'][RES_CRYSTAL];
  $planet_row['deuterium_max'] = $caps['total_storage'][RES_DEUTERIUM];
  $planet_row['energy_max'] = $caps[RES_ENERGY][BUILD_CREATE];
  $planet_row['energy_used'] = $caps[RES_ENERGY][BUILD_DESTROY];

  return $caps;
}
