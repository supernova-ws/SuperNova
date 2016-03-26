<?php

function eco_get_planet_caps_modify_production(&$item, $key, $data) {
  static $modifiers;

  if(!$modifiers) {
    $modifiers = sn_get_groups('modifiers');
  }
  $item = floor(mrc_modify_value($data['user'], $data['planet'], $modifiers[MODIFIER_RESOURCE_PRODUCTION], $item));
}

function eco_get_planet_caps(&$user, &$planet_row, $production_time = 0) {
  // TODO Считать $production_time для термоядерной электростанции
  global $config;

  static $sn_group_modifiers, $config_resource_multiplier, $config_resource_multiplier_plain, $config_eco_scale_storage;

  if(!$sn_group_modifiers) {
    $sn_group_modifiers = sn_get_groups('modifiers');
    $config_resource_multiplier = game_resource_multiplier();
    $config_resource_multiplier_plain = game_resource_multiplier(true);
    $config_eco_scale_storage = classSupernova::$config->eco_scale_storage ? $config_resource_multiplier_plain : 1;
  }

  $caps = array();
  $caps['storage'][RES_METAL][0] = classSupernova::$config->eco_planet_storage_metal;
  $caps['storage'][RES_CRYSTAL][0] = classSupernova::$config->eco_planet_storage_crystal;
  $caps['storage'][RES_DEUTERIUM][0] = classSupernova::$config->eco_planet_storage_deuterium;
  foreach(sn_get_groups('storages') as $unit_id) {
    foreach(get_unit_param($unit_id, P_STORAGE) as $resource_id => $function) {
      $caps['storage'][$resource_id][$unit_id] = floor($config_eco_scale_storage *
        mrc_modify_value($user, $planet_row, $sn_group_modifiers[MODIFIER_RESOURCE_CAPACITY], $function(mrc_get_level($user, $planet_row, $unit_id)))
      );
    }
  }

  if($planet_row['planet_type'] == PT_MOON) {
    return $caps;
  }

  $sn_group_planet_density = sn_get_groups('planet_density');
  $planet_density = $sn_group_planet_density[$planet_row['density_index']][UNIT_RESOURCES];

  $caps['production_full'][RES_METAL][0] = floor(classSupernova::$config->metal_basic_income * $config_resource_multiplier * (isset($planet_density[RES_METAL]) ? $planet_density[RES_METAL] : 1));
  $caps['production_full'][RES_CRYSTAL][0] = floor(classSupernova::$config->crystal_basic_income * $config_resource_multiplier * (isset($planet_density[RES_CRYSTAL]) ? $planet_density[RES_CRYSTAL] : 1));
  $caps['production_full'][RES_DEUTERIUM][0] = floor(classSupernova::$config->deuterium_basic_income * $config_resource_multiplier * (isset($planet_density[RES_DEUTERIUM]) ? $planet_density[RES_DEUTERIUM] : 1));
  $caps['production_full'][RES_ENERGY][0] = floor(classSupernova::$config->energy_basic_income * $config_resource_multiplier_plain * (isset($planet_density[RES_ENERGY]) ? $planet_density[RES_ENERGY] : 1));

  foreach(sn_get_groups('factories') as $unit_id) {
    $unit_data = get_unit_param($unit_id);
    $unit_level = mrc_get_level($user, $planet_row, $unit_id);
    $unit_load = $planet_row[pname_factory_production_field_name($unit_id)];

    foreach($unit_data[P_UNIT_PRODUCTION] as $resource_id => $function) {
      $caps['production_full'][$resource_id][$unit_id] = $function($unit_level, $unit_load, $user, $planet_row)
        * ($resource_id == RES_ENERGY ? $config_resource_multiplier_plain : $config_resource_multiplier)
        * (isset($planet_density[$resource_id]) ? $planet_density[$resource_id] : 1);
    }
  }

  array_walk_recursive($caps['production_full'], 'eco_get_planet_caps_modify_production', array('user' => $user, 'planet' => $planet_row));

  foreach($caps['production_full'] as $resource_id => $resource_data) {
    $caps['total_production_full'][$resource_id] = array_sum($resource_data);
  }

  $caps['production'] = $caps['production_full'];

  if($caps['production'][RES_ENERGY][STRUC_MINE_FUSION]) {
    $deuterium_balance = array_sum($caps['production'][RES_DEUTERIUM]);
    $energy_balance = array_sum($caps['production'][RES_ENERGY]);
    if($deuterium_balance < 0 || $energy_balance < 0) {
      $caps['production'][RES_DEUTERIUM][STRUC_MINE_FUSION] = $caps['production'][RES_ENERGY][STRUC_MINE_FUSION] = 0;
    }
  }

  foreach($caps['production'][RES_ENERGY] as $energy) {
    $caps[RES_ENERGY][$energy >= 0 ? BUILD_CREATE : BUILD_DESTROY] += $energy;
  }

  $caps[RES_ENERGY][BUILD_DESTROY] = -$caps[RES_ENERGY][BUILD_DESTROY];

  $caps['efficiency'] = $caps[RES_ENERGY][BUILD_DESTROY] > $caps[RES_ENERGY][BUILD_CREATE]
    ? $caps[RES_ENERGY][BUILD_CREATE] / $caps[RES_ENERGY][BUILD_DESTROY]
    : 1;

  foreach($caps['production'] as $resource_id => &$resource_data) {
    if($caps['efficiency'] != 1) {
      foreach($resource_data as $unit_id => &$resource_production) {
        if(!($unit_id == STRUC_MINE_FUSION && $resource_id == RES_DEUTERIUM) && $unit_id != 0 && !($resource_id == RES_ENERGY && $resource_production >= 0)) {
          $resource_production = $resource_production * $caps['efficiency'];
        }
      }
    }
    $caps['total'][$resource_id] = array_sum($resource_data);
    $caps['total'][$resource_id] = $caps['total'][$resource_id] >= 0 ? floor($caps['total'][$resource_id]) : ceil($caps['total'][$resource_id]);
  }

  foreach($caps['storage'] as $resource_id => &$resource_data) {
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
