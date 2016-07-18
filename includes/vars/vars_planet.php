<?php

defined('INSIDE') || die();

$sn_data[UNIT_GROUP]['planet_images'] = array(
  'trocken'    => array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10'),
  'dschjungel' => array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10'),
  'normaltemp' => array('01', '02', '03', '04', '05', '06', '07'),
  'wasser'     => array('01', '02', '03', '04', '05', '06', '07', '08', '09'),
  'eis'        => array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10'),
);

$sn_data[UNIT_GROUP]['planet_generator'] = array(
  0  => array( // HomeWorld
    't_max_min'     => 40, // Tmax 40
    't_max_max'     => 40,
    't_delta_min'   => 40, // Tmin 0
    't_delta_max'   => 40,
    'size_min'      => classSupernova::$config->initial_fields,
    'size_max'      => classSupernova::$config->initial_fields,
    'core_types'    => array(PLANET_DENSITY_STANDARD,),
    'planet_images' => array('normaltemp'),
  ),
  1  => array(
    't_max_min'     => 100,
    't_max_max'     => 150,
    't_delta_min'   => 5,
    't_delta_max'   => 20,
    'size_min'      => 50,
    'size_max'      => 150,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,

      //PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('trocken'),
  ),
  2  => array(
    't_max_min'     => 90,
    't_max_max'     => 145,
    't_delta_min'   => 5,
    't_delta_max'   => 25,
    'size_min'      => 80,
    'size_max'      => 180,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,

      // PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('trocken'),
  ),
  3  => array(
    't_max_min'     => 70,
    't_max_max'     => 135,
    't_delta_min'   => 5,
    't_delta_max'   => 30,
    'size_min'      => 100,
    'size_max'      => 210,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('trocken'),
  ),
  4  => array(
    't_max_min'     => 40,
    't_max_max'     => 110,
    't_delta_min'   => 10,
    't_delta_max'   => 35,
    'size_min'      => 130,
    'size_max'      => 240,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('dschjungel'),
  ),
  5  => array(
    't_max_min'     => 25,
    't_max_max'     => 100,
    't_delta_min'   => 10,
    't_delta_max'   => 40,
    'size_min'      => 170,
    'size_max'      => 270,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('dschjungel'),
  ),
  6  => array(
    't_max_min'     => 15,
    't_max_max'     => 95,
    't_delta_min'   => 10,
    't_delta_max'   => 45,
    'size_min'      => 220,
    'size_max'      => 300,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('dschjungel'),
  ),
  7  => array(
    't_max_min'     => 5,
    't_max_max'     => 90,
    't_delta_min'   => 20,
    't_delta_max'   => 50,
    'size_min'      => 200,
    'size_max'      => 280,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('normaltemp'),
  ),
  8  => array(
    't_max_min'     => 0,
    't_max_max'     => 80,
    't_delta_min'   => 20,
    't_delta_max'   => 45,
    'size_min'      => 160,
    'size_max'      => 250,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('normaltemp'),
  ),
  9  => array(
    't_max_min'     => -10,
    't_max_max'     => 65,
    't_delta_min'   => 18,
    't_delta_max'   => 40,
    'size_min'      => 120,
    'size_max'      => 210,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('normaltemp'),
  ),
  10 => array(
    't_max_min'     => -20,
    't_max_max'     => 50,
    't_delta_min'   => 15,
    't_delta_max'   => 35,
    'size_min'      => 170,
    'size_max'      => 240,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('wasser'),
  ),
  11 => array(
    't_max_min'     => -32,
    't_max_max'     => 36,
    't_delta_min'   => 12,
    't_delta_max'   => 30,
    'size_min'      => 110,
    'size_max'      => 230,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('wasser'),
  ),
  12 => array(
    't_max_min'     => -45,
    't_max_max'     => 20,
    't_delta_min'   => 10,
    't_delta_max'   => 25,
    'size_min'      => 90,
    'size_max'      => 190,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, PLANET_DENSITY_CRYSTAL_RAW,
      // PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('wasser'),
  ),
  13 => array(
    't_max_min'     => -55,
    't_max_max'     => 5,
    't_delta_min'   => 8,
    't_delta_max'   => 20,
    'size_min'      => 80,
    'size_max'      => 170,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, PLANET_DENSITY_CRYSTAL_RAW,
      // PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('eis'),
  ),
  14 => array(
    't_max_min'     => -60,
    't_max_max'     => 0,
    't_delta_min'   => 5,
    't_delta_max'   => 15,
    'size_min'      => 70,
    'size_max'      => 150,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      // PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('eis'),
  ),
  15 => array(
    't_max_min'     => -65,
    't_max_max'     => -5,
    't_delta_min'   => 2,
    't_delta_max'   => 10,
    'size_min'      => 60,
    'size_max'      => 130,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,
      PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
      // PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('eis'),
  ),
  16 => array( // Random planet - stranger; -35 avg
    't_max_min'     => -90,
    't_max_max'     => +40,
    't_delta_min'   => 2,
    't_delta_max'   => 50,
    'size_min'      => 30,
    'size_max'      => 330,
    'core_types'    => array(
      PLANET_DENSITY_STANDARD,

      PLANET_DENSITY_ICE_HYDROGEN,
      PLANET_DENSITY_ICE_METHANE,
      PLANET_DENSITY_ICE_WATER,
      PLANET_DENSITY_CRYSTAL_RAW,
      PLANET_DENSITY_CRYSTAL_SILICATE,
      PLANET_DENSITY_CRYSTAL_STONE,
      PLANET_DENSITY_METAL_ORE,
      PLANET_DENSITY_METAL_PERIDOT,
      PLANET_DENSITY_METAL_RAW,
    ),
    'planet_images' => array('trocken', 'dschjungel', 'normaltemp', 'wasser', 'eis',),
  ),
);

$sn_data[UNIT_GROUP]['planet_density'] = array(
  PLANET_DENSITY_NONE => array(
    UNIT_PLANET_DENSITY               => 250,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_NONE,
    UNIT_PLANET_DENSITY_RARITY        => 0,
    UNIT_RESOURCES                    => array(
      RES_METAL     => 0.10,
      RES_CRYSTAL   => 0.10,
      RES_DEUTERIUM => 1.30
    ),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
  ),

  PLANET_DENSITY_ICE_HYDROGEN => array(
    UNIT_PLANET_DENSITY               => 750,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_ICE_HYDROGEN,
    UNIT_PLANET_DENSITY_RARITY        => 30, // 1, // 40.00, // * 1/121 0,82645
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_PERFECT,
    UNIT_RESOURCES                    => array(RES_METAL => 0.20, RES_CRYSTAL => 0.60, RES_DEUTERIUM => 7.10,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 150,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 11,
  ),
  PLANET_DENSITY_ICE_METHANE  => array(
    UNIT_PLANET_DENSITY               => 1250,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_ICE_METHANE,
    UNIT_PLANET_DENSITY_RARITY        => 130, // 6, // 6.67, // * 6,0	4,95868
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_GOOD,
    UNIT_RESOURCES                    => array(RES_METAL => 0.55, RES_CRYSTAL => 0.85, RES_DEUTERIUM => 4.60,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 200,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 6,
  ),
  PLANET_DENSITY_ICE_WATER    => array(
    UNIT_PLANET_DENSITY               => 2000,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_ICE_WATER,
    UNIT_PLANET_DENSITY_RARITY        => 450, //20, // 2.00, // * 20,0	16,52893
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_AVERAGE,
    UNIT_RESOURCES                    => array(RES_METAL => 0.86, RES_CRYSTAL => 0.95, RES_DEUTERIUM => 2.20,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
  ),

  PLANET_DENSITY_CRYSTAL_RAW      => array(
    UNIT_PLANET_DENSITY               => 2500,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_CRYSTAL_RAW,
    UNIT_PLANET_DENSITY_RARITY        => 20, // 1, // 40.00, // *1,0	0,82645
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_PERFECT,
    UNIT_RESOURCES                    => array(RES_METAL => 0.40, RES_CRYSTAL => 12.37, RES_DEUTERIUM => 0.50,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 150,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 11,
  ),
  PLANET_DENSITY_CRYSTAL_SILICATE => array(
    UNIT_PLANET_DENSITY               => 3500,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_CRYSTAL_SILICATE,
    UNIT_PLANET_DENSITY_RARITY        => 140, // 5.71, // * 7,0	5,78512
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_GOOD,
    UNIT_RESOURCES                    => array(RES_METAL => 0.67, RES_CRYSTAL => 4.50, RES_DEUTERIUM => 0.85,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 200,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 6,
  ),
  PLANET_DENSITY_CRYSTAL_STONE    => array(
    UNIT_PLANET_DENSITY               => 4750,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_CRYSTAL_STONE,
    UNIT_PLANET_DENSITY_RARITY        => 500, // 1.90, // * 21,0	17,35537
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_AVERAGE,
    UNIT_RESOURCES                    => array(RES_METAL => 0.80, RES_CRYSTAL => 2.00, RES_DEUTERIUM => 0.95,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
  ),

  PLANET_DENSITY_STANDARD => array(
    UNIT_PLANET_DENSITY               => 5750,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_STANDARD,
    UNIT_PLANET_DENSITY_RARITY        => 1000, // 1.0, // * 40,0	33,05785
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_NORMAL,
    UNIT_RESOURCES                    => array(RES_METAL => 1.00, RES_CRYSTAL => 1.00, RES_DEUTERIUM => 1.00,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
  ),

  PLANET_DENSITY_METAL_ORE     => array(
    UNIT_PLANET_DENSITY               => 7000,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_METAL_ORE,
    UNIT_PLANET_DENSITY_RARITY        => 550, // 2.11, // * 19,0	15,70248
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_AVERAGE,
    UNIT_RESOURCES                    => array(RES_METAL => 1.60, RES_CRYSTAL => 0.90, RES_DEUTERIUM => 0.80,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
  ),
  PLANET_DENSITY_METAL_PERIDOT => array(
    UNIT_PLANET_DENSITY               => 8250,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_METAL_PERIDOT,
    UNIT_PLANET_DENSITY_RARITY        => 120, // 8.00, // * 5,0	4,13223
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_GOOD,
    UNIT_RESOURCES                    => array(RES_METAL => 4.71, RES_CRYSTAL => 0.80, RES_DEUTERIUM => 0.55,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 200,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 6,
  ),
  PLANET_DENSITY_METAL_RAW     => array(
    UNIT_PLANET_DENSITY               => 9500,
    UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_METAL_RAW,
    UNIT_PLANET_DENSITY_RARITY        => 25, // 40.00, // * 1,0	0,82645
    UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_PERFECT,
    UNIT_RESOURCES                    => array(RES_METAL => 8.00, RES_CRYSTAL => 0.40, RES_DEUTERIUM => 0.25,),
    UNIT_PLANET_DENSITY_MAX_SECTORS   => 150,
    UNIT_PLANET_DENSITY_MIN_ASTROTECH => 11,
  ),
);

$sn_data[UNIT_GROUP]['planet_density_old'] = array(
  PLANET_DENSITY_NONE             => array(
    UNIT_PLANET_DENSITY        => 1000,
    UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_NONE,
    UNIT_PLANET_DENSITY_RARITY => 0,
    UNIT_RESOURCES             => array(
      RES_METAL     => 0.10,
      RES_CRYSTAL   => 0.10,
      RES_DEUTERIUM => 1.30
    ),
  ),
  PLANET_DENSITY_ICE_WATER        => array(
    UNIT_PLANET_DENSITY        => 2000,
    UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_ICE_WATER,
    UNIT_PLANET_DENSITY_RARITY => 23.4,
    UNIT_RESOURCES             => array(
      RES_METAL     => 0.30,
      RES_CRYSTAL   => 0.20,
      RES_DEUTERIUM => 1.20
    ),
  ),
  PLANET_DENSITY_CRYSTAL_SILICATE => array(
    UNIT_PLANET_DENSITY        => 3250,
    UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_CRYSTAL_SILICATE,
    UNIT_PLANET_DENSITY_RARITY => 4.1,
    UNIT_RESOURCES             => array(
      RES_METAL     => 0.40,
      RES_CRYSTAL   => 1.40,
      RES_DEUTERIUM => 0.90
    ),
  ),
  PLANET_DENSITY_CRYSTAL_STONE    => array(
    UNIT_PLANET_DENSITY        => 4500,
    UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_CRYSTAL_STONE,
    UNIT_PLANET_DENSITY_RARITY => 1.4,
    UNIT_RESOURCES             => array(
      RES_METAL     => 0.80,
      RES_CRYSTAL   => 1.25,
      RES_DEUTERIUM => 0.80
    ),
  ),
  PLANET_DENSITY_STANDARD         => array(
    UNIT_PLANET_DENSITY        => 5750,
    UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_STANDARD,
    UNIT_PLANET_DENSITY_RARITY => 1,
    UNIT_RESOURCES             => array(
      RES_METAL     => 1.00,
      RES_CRYSTAL   => 1.00,
      RES_DEUTERIUM => 1.00
    ),
  ),
  PLANET_DENSITY_METAL_ORE        => array(
    UNIT_PLANET_DENSITY        => 7000,
    UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_METAL_ORE,
    UNIT_PLANET_DENSITY_RARITY => 1.5,
    UNIT_RESOURCES             => array(
      RES_METAL     => 2.00,
      RES_CRYSTAL   => 0.75,
      RES_DEUTERIUM => 0.75
    ),
  ),
  PLANET_DENSITY_METAL_PERIDOT    => array(
    UNIT_PLANET_DENSITY        => 8250,
    UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_METAL_PERIDOT,
    UNIT_PLANET_DENSITY_RARITY => 4.9,
    UNIT_RESOURCES             => array(
      RES_METAL     => 3.00,
      RES_CRYSTAL   => 0.50,
      RES_DEUTERIUM => 0.50
    ),
  ),
  PLANET_DENSITY_METAL_RAW        => array(
    UNIT_PLANET_DENSITY        => 9250,
    UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_METAL_RAW,
    UNIT_PLANET_DENSITY_RARITY => 31.4,
    UNIT_RESOURCES             => array(
      RES_METAL     => 4.00,
      RES_CRYSTAL   => 0.25,
      RES_DEUTERIUM => 0.25
    ),
  ),
);
