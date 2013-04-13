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

  static $sn_groups, $sn_group_structures, $sn_group_modifiers, $config_resource_multiplier, $config_eco_scale_storage, $base_storage_size;

  if(!$sn_groups)
  {
    $sn_groups = &$sn_data['groups'];
    $sn_group_structures = &$sn_groups['structures'];
    $sn_group_modifiers = &$sn_groups['modifiers'];
    $config_resource_multiplier = $config->resource_multiplier;
    $config_eco_scale_storage = $config->eco_scale_storage ? $config_resource_multiplier : 1;
    $base_storage_size = BASE_STORAGE_SIZE * $config_eco_scale_storage;
  }

  $caps = array('caps_real' => array(),);
/*
  $caps += array('planet' => array(
    'metal' => $planet_row['metal'],
    'crystal' => $planet_row['crystal'],
    'deuterium' => $planet_row['deuterium'],
    'metal_max' => floor(mrc_modify_value($user, $planet_row, MRC_STOCKMAN, $base_storage_size * pow(1.5, mrc_get_level($user, $planet_row, STRUC_STORE_METAL)))),
    'crystal_max' => floor(mrc_modify_value($user, $planet_row, MRC_STOCKMAN, $base_storage_size * pow(1.5, mrc_get_level($user, $planet_row, STRUC_STORE_CRYSTAL)))),
    'deuterium_max' => floor(mrc_modify_value($user, $planet_row, MRC_STOCKMAN, $base_storage_size * pow(1.5, mrc_get_level($user, $planet_row, STRUC_STORE_DEUTERIUM)))),
  ));
*/
  $caps_real = &$caps['caps_real'];
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
    return $caps;
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
      $caps_real['production'][$resource_id][$unit_id] = $function($unit_level, $unit_load, $user, $planet_row);
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
    if($resource_id != RES_ENERGY)
    {
      foreach($resource_data as $unit_id => &$resource_production)
      {
        if(!($unit_id == STRUC_MINE_FUSION && $resource_id == RES_DEUTERIUM))
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
/*
  // Calcul de production lin�aire des divers types
  $BuildTemp = $planet_row['temp_max'];
  $BuildEnergyTech = mrc_get_level($user, '', TECH_ENERGY);


  $caps['metal_perhour'][0] = $config->metal_basic_income * $config_resource_multiplier;
  $caps['crystal_perhour'][0] = $config->crystal_basic_income * $config_resource_multiplier;
  $caps['deuterium_perhour'][0] = $config->deuterium_basic_income * $config_resource_multiplier;
  $caps['energy'][0] = $config->energy_basic_income * $config_resource_multiplier;
  $caps['planet']['energy_max'] = $caps['energy'][0];

  foreach($sn_groups['factories'] as $ProdID)
  {
    $unit_data = $sn_data[$ProdID];

    $BuildLevel = mrc_get_level($user, $planet_row, $ProdID); // $planet_row[$sn_data[$ProdID]['name']];
    $BuildLevelFactor = $planet_row["{$sn_data[$ProdID]['name']}_porcent"];

    $caps['energy'][$ProdID] = floor(eval($unit_data['energy_perhour']) * $config_resource_multiplier);
    if($ProdID == STRUC_MINE_FUSION)
    {
      if($planet_row['deuterium'] > 0)
      {
        $caps['deuterium_perhour'][$ProdID] = floor(eval($unit_data['deuterium_perhour']));
      }
      else
      {
        $caps['energy'][$ProdID] = 0;
      }
    }
    else
    {
      if(in_array($ProdID, $sn_group_structures))
      {
        $caps['metal_perhour'][$ProdID] += floor(mrc_modify_value($user, $planet_row, MRC_TECHNOLOGIST, eval($unit_data['metal_perhour']) * $config_resource_multiplier));
        $caps['crystal_perhour'][$ProdID] += floor(mrc_modify_value($user, $planet_row, MRC_TECHNOLOGIST, eval($unit_data['crystal_perhour']) * $config_resource_multiplier));
        $caps['deuterium_perhour'][$ProdID] += floor(mrc_modify_value($user, $planet_row, MRC_TECHNOLOGIST, eval($unit_data['deuterium_perhour']) * $config_resource_multiplier));
      }
    };

    if($caps['energy'][$ProdID] > 0)
    {
      $caps['energy'][$ProdID] = floor(mrc_modify_value($user, $planet_row, array(MRC_TECHNOLOGIST), $caps['energy'][$ProdID]));

      $caps['planet']['energy_max'] += floor($caps['energy'][$ProdID]);
    }
    else
    {
      $caps['planet']['energy_used'] -= floor($caps['energy'][$ProdID]);
    };

    $caps['planet']['metal_perhour'] += $caps['metal_perhour'][$ProdID];
    $caps['planet']['crystal_perhour'] += $caps['crystal_perhour'][$ProdID];
    $caps['planet']['deuterium_perhour'] += $caps['deuterium_perhour'][$ProdID];
  };

  if($caps['planet']['energy_used'])
  {
    $caps['production'] = max(0, min(1, $caps['planet']['energy_max'] / $caps['planet']['energy_used']));
  }
  else
  {
    $caps['production'] = 1;
  }

  $caps['real']['metal_perhour'] = floor($caps['planet']['metal_perhour'] * $caps['production'] + $caps['metal_perhour'][0]);
  $caps['real']['crystal_perhour'] = floor($caps['planet']['crystal_perhour'] * $caps['production'] + $caps['crystal_perhour'][0]);
  $caps['real']['deuterium_perhour'] = floor($caps['planet']['deuterium_perhour'] * $caps['production'] + $caps['deuterium_perhour'][0]);

  foreach($caps['energy'] as $element => $production)
  {
    $caps['real']['units']['metal_perhour'][$element] = floor($caps['metal_perhour'][$element] * $caps['production']);
    $caps['real']['units']['crystal_perhour'][$element] = floor($caps['crystal_perhour'][$element] * $caps['production']);
    $caps['real']['units']['deuterium_perhour'][$element] = floor($caps['deuterium_perhour'][$element] * $caps['production']);
  }
*/
  return $caps;
}
