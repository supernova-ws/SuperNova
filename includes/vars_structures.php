<?php

!defined('INSIDE') ? die('Hack attempt!') : false;

global $sn_data;
$sn_data += array(
  STRUC_MINE_METAL => array(
    'name'              => 'metal_mine',
    'type'              => UNIT_STRUCTURES,
    'location'          => LOC_PLANET,
    'cost'              => array(
      RES_METAL     => 80,
      RES_CRYSTAL   => 20,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 1.5,
    ),
    P_UNIT_PRODUCTION   => array(
      RES_METAL  => function ($level, $production_factor, $user, $planet_row) { return 40 * $level * pow(1.1, $level) * (0.1 * $production_factor); },
      RES_ENERGY => function ($level, $production_factor, $user, $planet_row) { return -13 * $level * pow(1.1, $level) * (0.1 * $production_factor); },
    ),
    P_MINING_IS_MANAGED => true,
  ),

  STRUC_MINE_CRYSTAL => array(
    'name'              => 'crystal_mine',
    'type'              => UNIT_STRUCTURES,
    'location'          => LOC_PLANET,
    'cost'              => array(
      RES_METAL     => 48,
      RES_CRYSTAL   => 24,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 1.6,
    ),
    P_UNIT_PRODUCTION   => array(
      RES_CRYSTAL => function ($level, $production_factor, $user, $planet_row) { return 32 * $level * pow(1.1, $level) * (0.1 * $production_factor); },
      RES_ENERGY  => function ($level, $production_factor, $user, $planet_row) { return -16 * $level * pow(1.1, $level) * (0.1 * $production_factor); },
    ),
    P_MINING_IS_MANAGED => true,
  ),

  STRUC_MINE_DEUTERIUM => array(
    'name'              => 'deuterium_sintetizer',
    'type'              => UNIT_STRUCTURES,
    'location'          => LOC_PLANET,
    'cost'              => array(
      RES_METAL     => 225,
      RES_CRYSTAL   => 75,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 1.5,
    ),
    P_UNIT_PRODUCTION   => array(
      RES_DEUTERIUM => function ($level, $production_factor, $user, $planet_row) { return 10 * $level * pow(1.1, $level) * (0.1 * $production_factor) * (-0.002 * $planet_row["temp_max"] + 1.28); },
      RES_ENERGY    => function ($level, $production_factor, $user, $planet_row) { return -20 * $level * pow(1.1, $level) * (0.1 * $production_factor); },
    ),
    P_MINING_IS_MANAGED => true,
  ),

  STRUC_MINE_SOLAR  => array(
    'name'              => 'solar_plant',
    'type'              => UNIT_STRUCTURES,
    'location'          => LOC_PLANET,
    'cost'              => array(
      RES_METAL     => 75,
      RES_CRYSTAL   => 30,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 1.5,
    ),
    P_UNIT_PRODUCTION   => array(
      RES_ENERGY => function ($level, $production_factor, $user, $planet_row) { return ($planet_row["temp_max"] / 5 + 15) * $level * pow(1.1, $level) * (0.1 * $production_factor); },
    ),
    P_MINING_IS_MANAGED => true,
  ),
// −273,15 °C
  STRUC_MINE_FUSION => array(
    'name'              => 'fusion_plant',
    'type'              => UNIT_STRUCTURES,
    'location'          => LOC_PLANET,
    P_REQUIRE           => array(3 => 5, TECH_ENERGY => 3, UNIT_PLAN_STRUC_MINE_FUSION => 1),
    'cost'              => array(
      RES_METAL     => 900,
      RES_CRYSTAL   => 360,
      RES_DEUTERIUM => 180,
      RES_ENERGY    => 0,
      'factor'      => 1.8,
    ),
    P_UNIT_PRODUCTION   => array(
      RES_DEUTERIUM => function ($level, $production_factor, $user, $planet_row) { return -10 * $level * pow(1.1, $level) * (0.1 * $production_factor); },
      RES_ENERGY    => function ($level, $production_factor, $user, $planet_row) { return 30 * $level * pow(1.05 + 0.01 * mrc_get_level($user, "", TECH_ENERGY), $level) * (0.1 * $production_factor); },
    ),
    P_MINING_IS_MANAGED => true,
  ),

  STRUC_STORE_METAL => array(
    'name'     => 'metal_store',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    'cost'     => array(
      RES_METAL     => 2000,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
    'storage'  => array(
      RES_METAL => function ($level) { return BASE_STORAGE_SIZE * pow(1.5, $level); },
    ),
  ),

  STRUC_STORE_CRYSTAL => array(
    'name'     => 'crystal_store',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    'cost'     => array(
      RES_METAL     => 2000,
      RES_CRYSTAL   => 1000,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
    'storage'  => array(
      RES_CRYSTAL => function ($level) { return BASE_STORAGE_SIZE * pow(1.5, $level); },
    ),
  ),

  STRUC_STORE_DEUTERIUM => array(
    'name'     => 'deuterium_store',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    'cost'     => array(
      RES_METAL     => 2000,
      RES_CRYSTAL   => 2000,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
    'storage'  => array(
      RES_DEUTERIUM => function ($level) { return BASE_STORAGE_SIZE * pow(1.5, $level); },
    ),
  ),

  STRUC_FACTORY_ROBOT => array(
    'name'     => 'robot_factory',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    'cost'     => array(
      RES_METAL     => 400,
      RES_CRYSTAL   => 120,
      RES_DEUTERIUM => 200,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_FACTORY_NANO => array(
    'name'     => 'nano_factory',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    P_REQUIRE  => array(STRUC_FACTORY_ROBOT => 10, TECH_COMPUTER => 10),
    'cost'     => array(
      RES_METAL     => 1000000,
      RES_CRYSTAL   => 500000,
      RES_DEUTERIUM => 100000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_FACTORY_HANGAR => array(
    'name'     => 'hangar',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    P_REQUIRE  => array(STRUC_FACTORY_ROBOT => 2),
    'cost'     => array(
      RES_METAL     => 400,
      RES_CRYSTAL   => 200,
      RES_DEUTERIUM => 100,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_LABORATORY => array(
    'name'     => 'laboratory',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    'cost'     => array(
      RES_METAL     => 200,
      RES_CRYSTAL   => 400,
      RES_DEUTERIUM => 200,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_TERRAFORMER => array(
    'name'     => 'terraformer',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    P_REQUIRE  => array(STRUC_FACTORY_NANO => 1, TECH_ENERGY => 12),
    'cost'     => array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 50000,
      RES_DEUTERIUM => 100000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_ALLY_DEPOSIT => array(
    'name'     => 'ally_deposit',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    'cost'     => array(
      RES_METAL     => 20000,
      RES_CRYSTAL   => 40000,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_LABORATORY_NANO => array(
    'name'     => 'nano',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    P_REQUIRE  => array(STRUC_LABORATORY => 10, TECH_ENERGY => 10),
    'cost'     => array(
      RES_METAL     => 1500000,
      RES_CRYSTAL   => 750000,
      RES_DEUTERIUM => 150000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_MOON_STATION => array(
    'name'     => 'mondbasis',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    'cost'     => array(
      RES_METAL     => 20000,
      RES_CRYSTAL   => 40000,
      RES_DEUTERIUM => 20000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_MOON_PHALANX => array(
    'name'     => 'phalanx',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    P_REQUIRE  => array(STRUC_MOON_STATION => 1),
    'cost'     => array(
      RES_METAL     => 20000,
      RES_CRYSTAL   => 40000,
      RES_DEUTERIUM => 20000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_MOON_GATE => array(
    'name'     => 'sprungtor',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    P_REQUIRE  => array(STRUC_MOON_STATION => 1, TECH_HYPERSPACE => 7),
    'cost'     => array(
      RES_METAL     => 2000000,
      RES_CRYSTAL   => 4000000,
      RES_DEUTERIUM => 2000000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  STRUC_SILO => array(
    'name'     => 'silo',
    'type'     => UNIT_STRUCTURES,
    'location' => LOC_PLANET,
    P_REQUIRE  => array(TECH_ENGINE_ION => 1),
    'cost'     => array(
      RES_METAL     => 20000,
      RES_CRYSTAL   => 20000,
      RES_DEUTERIUM => 1000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
    'capacity' => 12,
  ),
);
