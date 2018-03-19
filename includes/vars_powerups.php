<?php

if (!defined('INSIDE')) {
  die('Hack attempt!');
}

global $sn_data;
$sn_data += array(
  TECH_COMPUTER => array(
    'name'     => 'computer_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 1),
    'cost'     => array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 400,
      RES_DEUTERIUM => 600,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),

    P_BONUS_VALUE => 1,
    P_BONUS_TYPE  => BONUS_ADD,
  ),

  TECH_SPY => array(
    'name'     => 'spy_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 3),
    'cost'     => array(
      RES_METAL     => 200,
      RES_CRYSTAL   => 1000,
      RES_DEUTERIUM => 200,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),

    P_BONUS_VALUE => 1,
    P_BONUS_TYPE  => BONUS_ADD,
  ),

  TECH_WEAPON => array(
    'name'     => 'military_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 4),
    'cost'     => array(
      RES_METAL     => 800,
      RES_CRYSTAL   => 200,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),

    P_BONUS_VALUE => 10,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),

  TECH_SHIELD => array(
    'name'     => 'shield_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 6, TECH_ENERGY => 3),
    'cost'     => array(
      RES_METAL     => 200,
      RES_CRYSTAL   => 600,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),

    P_BONUS_VALUE => 10,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),

  TECH_ARMOR => array(
    'name'     => 'defence_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 2),
    'cost'     => array(
      RES_METAL     => 1000,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),

    P_BONUS_VALUE => 10,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),

  TECH_ENERGY => array(
    'name'     => 'energy_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 1),
    'cost'     => array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 800,
      RES_DEUTERIUM => 400,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  TECH_HYPERSPACE => array(
    'name'     => 'hyperspace_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 7, TECH_ENERGY => 10, TECH_SHIELD => 5),
    'cost'     => array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 4000,
      RES_DEUTERIUM => 2000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  TECH_ENGINE_CHEMICAL => array(
    'name'           => 'combustion_tech',
    'type'           => UNIT_TECHNOLOGIES,
    'location'       => LOC_USER,
    P_REQUIRE        => array(STRUC_LABORATORY => 1, TECH_ENERGY => 1),
    'cost'           => array(
      RES_METAL     => 400,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 600,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
    P_BONUS_VALUE    => 10,
    P_BONUS_TYPE     => BONUS_PERCENT,
    'speed_increase' => 0.1,
  ),

  TECH_ENGINE_ION => array(
    'name'           => 'impulse_motor_tech',
    'type'           => UNIT_TECHNOLOGIES,
    'location'       => LOC_USER,
    P_REQUIRE        => array(STRUC_LABORATORY => 4, TECH_ION => 1),
    'cost'           => array(
      RES_METAL     => 2000,
      RES_CRYSTAL   => 4000,
      RES_DEUTERIUM => 600,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
    P_BONUS_VALUE    => 20,
    P_BONUS_TYPE     => BONUS_PERCENT,
    'speed_increase' => 0.2,
  ),

  TECH_ENGINE_HYPER => array(
    'name'           => 'hyperspace_motor_tech',
    'type'           => UNIT_TECHNOLOGIES,
    'location'       => LOC_USER,
    P_REQUIRE        => array(STRUC_LABORATORY => 8, TECH_HYPERSPACE => 3),
    'cost'           => array(
      RES_METAL     => 10000,
      RES_CRYSTAL   => 20000,
      RES_DEUTERIUM => 6000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
    P_BONUS_VALUE    => 30,
    P_BONUS_TYPE     => BONUS_PERCENT,
    'speed_increase' => 0.3,
  ),

  TECH_LASER => array(
    'name'     => 'laser_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 1, TECH_ENERGY => 2),
    'cost'     => array(
      RES_METAL     => 200,
      RES_CRYSTAL   => 100,
      RES_DEUTERIUM => 0,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  TECH_ION => array(
    'name'     => 'ionic_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 3, TECH_ENERGY => 4, TECH_LASER => 5),
    'cost'     => array(
      RES_METAL     => 1000,
      RES_CRYSTAL   => 300,
      RES_DEUTERIUM => 100,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  TECH_PLASMA => array(
    'name'     => 'buster_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 5, TECH_ENERGY => 8, TECH_LASER => 10, TECH_ION => 5),
    'cost'     => array(
      RES_METAL     => 2000,
      RES_CRYSTAL   => 4000,
      RES_DEUTERIUM => 1000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  TECH_RESEARCH => array(
    'name'     => 'intergalactic_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 10, TECH_COMPUTER => 8, TECH_HYPERSPACE => 8),
    'cost'     => array(
      RES_METAL     => 240000,
      RES_CRYSTAL   => 400000,
      RES_DEUTERIUM => 160000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  TECH_EXPEDITION => array(
    'name'     => 'expedition_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 3, TECH_COMPUTER => 4, TECH_ENGINE_ION => 3),
    'cost'     => array(
      RES_METAL     => 4000,
      RES_CRYSTAL   => 8000,
      RES_DEUTERIUM => 4000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  TECH_COLONIZATION => array(
    'name'     => 'colonisation_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 3, TECH_ENERGY => 5, TECH_ARMOR => 2),
    'cost'     => array(
      RES_METAL     => 1000,
      RES_CRYSTAL   => 4000,
      RES_DEUTERIUM => 1000,
      RES_ENERGY    => 0,
      'factor'      => 2,
    ),
  ),

  TECH_ASTROTECH => array(
    'name'     => 'tech_astro',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 3, TECH_ENERGY => 5, TECH_ARMOR => 2),
    'cost'     => array(
      RES_METAL     => 1000,
      RES_CRYSTAL   => 3000,
      RES_DEUTERIUM => 900,
      RES_ENERGY    => 0,
      'factor'      => 2.5,
    ),
  ),

  TECH_GRAVITON => array(
    'name'     => 'graviton_tech',
    'type'     => UNIT_TECHNOLOGIES,
    'location' => LOC_USER,
    P_REQUIRE  => array(STRUC_LABORATORY => 12, TECH_ENERGY => 12, TECH_HYPERSPACE => 6),
    'cost'     => array(
      RES_METAL     => 100000000,
      RES_CRYSTAL   => 100000000,
      RES_DEUTERIUM => 50000000,
      //RES_ENERGY    => 300000,   // 100000 satellites
      'factor'      => 3,
    ),
  ),


  MRC_TECHNOLOGIST => array(
    'name'        => 'rpg_geologue',
    'type'        => UNIT_GOVERNORS,
    'location'    => LOC_PLANET,
    'cost'        => array(
      RES_DARK_MATTER => 800,
      'factor'        => 1.06,
    ),
    P_BONUS_VALUE => 5,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),
  MRC_ENGINEER     => array(
    'name'        => 'rpg_constructeur',
    'type'        => UNIT_GOVERNORS,
    'location'    => LOC_PLANET,
    'cost'        => array(
      RES_DARK_MATTER => 500,
      'factor'        => 1.65,
    ),
    P_BONUS_VALUE => 10,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),
  MRC_FORTIFIER    => array(
    'name'        => 'rpg_defenseur',
    'type'        => UNIT_GOVERNORS,
    'location'    => LOC_PLANET,
    'cost'        => array(
      RES_DARK_MATTER => 2000,
      'factor'        => 1.25,
    ),
    P_BONUS_VALUE => 10,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),


  MRC_STOCKMAN => array(
    'name'        => 'rpg_stockeur',
    'type'        => UNIT_MERCENARIES,
    'location'    => LOC_USER,
    'cost'        => array(
      RES_DARK_MATTER => 3000,
      'factor'        => 1,
    ),
    'max'         => 20,
    P_BONUS_VALUE => 20,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),

  MRC_SPY => array(
    'name'        => 'rpg_espion',
    'type'        => UNIT_MERCENARIES,
    'location'    => LOC_USER,
    P_REQUIRE     => array(MRC_STOCKMAN => 5),
    'cost'        => array(
      RES_DARK_MATTER => 3000,
      'factor'        => 1,
    ),
    'max'         => 5,
    P_BONUS_VALUE => 1,
    P_BONUS_TYPE  => BONUS_ADD,
  ),

  MRC_ACADEMIC => array(
    'name'        => 'mrc_academic',
    'type'        => UNIT_MERCENARIES,
    'location'    => LOC_USER,
    P_REQUIRE     => array(MRC_STOCKMAN => 10, MRC_SPY => 5),
    'cost'        => array(
      RES_DARK_MATTER => 3000,
      'factor'        => 1,
    ),
    'max'         => 30,
    P_BONUS_VALUE => 10,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),

  MRC_ADMIRAL => array(
    'name'        => 'rpg_amiral',
    'type'        => UNIT_MERCENARIES,
    'location'    => LOC_USER,
    'cost'        => array(
      RES_DARK_MATTER => 3000,
      'factor'        => 1,
    ),
    'max'         => 20,
    P_BONUS_VALUE => 5,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),

  MRC_COORDINATOR => array(
    'name'        => 'rpg_commandant',
    'type'        => UNIT_MERCENARIES,
    'location'    => LOC_USER,
    P_REQUIRE     => array(MRC_ADMIRAL => 5),
    'cost'        => array(
      RES_DARK_MATTER => 3000,
      'factor'        => 1,
    ),
    'max'         => 5,
    P_BONUS_VALUE => 1,
    P_BONUS_TYPE  => BONUS_ADD,
  ),

  MRC_NAVIGATOR => array(
    'name'        => 'rpg_general',
    'type'        => UNIT_MERCENARIES,
    'location'    => LOC_USER,
    P_REQUIRE     => array(MRC_ADMIRAL => 10, MRC_COORDINATOR => 5),
    'cost'        => array(
      RES_DARK_MATTER => 3000,
      'factor'        => 1,
    ),
    'max'         => 10,
    P_BONUS_VALUE => 5,
    P_BONUS_TYPE  => BONUS_PERCENT,
  ),


  ART_LHC             => array(
    'name'       => 'art_lhc',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 25000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  ART_HOOK_SMALL      => array(
    'name'       => 'art_hook_small',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 100000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  ART_HOOK_MEDIUM     => array(
    'name'       => 'art_hook_medium',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 200000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  ART_HOOK_LARGE      => array(
    'name'       => 'art_hook_large',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 400000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  ART_RCD_SMALL       => array(
    'name'       => 'art_rcd_small',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 5000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
    'deploy'     => array(
      STRUC_MINE_METAL     => 10,
      STRUC_MINE_CRYSTAL   => 10,
      STRUC_MINE_DEUTERIUM => 10,
      STRUC_MINE_SOLAR     => 14,
      STRUC_FACTORY_ROBOT  => 4,
    ),
  ),
  ART_RCD_MEDIUM      => array(
    'name'       => 'art_rcd_medium',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 25000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
    'deploy'     => array(
      STRUC_MINE_METAL     => 15,
      STRUC_MINE_CRYSTAL   => 15,
      STRUC_MINE_DEUTERIUM => 15,
      STRUC_MINE_SOLAR     => 20,
      STRUC_FACTORY_ROBOT  => 8,
    ),
  ),
  ART_RCD_LARGE       => array(
    'name'       => 'art_rcd_large',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 60000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
    'deploy'     => array(
      STRUC_MINE_METAL     => 20,
      STRUC_MINE_CRYSTAL   => 20,
      STRUC_MINE_DEUTERIUM => 20,
      STRUC_MINE_SOLAR     => 25,
      STRUC_FACTORY_ROBOT  => 10,
      STRUC_FACTORY_NANO   => 1,
    ),
  ),
  ART_HEURISTIC_CHIP  => array(
    'name'       => 'art_heuristic_chip',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 20000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  ART_NANO_BUILDER    => array(
    'name'       => 'art_nano_builder',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 5000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  ART_DENSITY_CHANGER => array(
    'name'       => 'art_density_changer',
    'type'       => UNIT_ARTIFACTS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 50000,
      'factor'        => 1,
    ),
    P_BONUS_TYPE => BONUS_ABILITY,
  ),

  UNIT_PLAN_STRUC_MINE_FUSION => array(
    'name'       => 'UNIT_PLAN_STRUC_MINE_FUSION',
    'type'       => UNIT_PLANS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 10000,
      'factor'        => 1,
    ),
    'max'        => 1,
    P_BONUS_TYPE => BONUS_ABILITY,
  ),

  UNIT_PLAN_SHIP_CARGO_SUPER  => array(
    'name'       => 'UNIT_PLAN_SHIP_CARGO_SUPER',
    'type'       => UNIT_PLANS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 10000,
      'factor'        => 1,
    ),
    'max'        => 1,
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  UNIT_PLAN_SHIP_CARGO_HYPER  => array(
    'name'       => 'UNIT_PLAN_SHIP_CARGO_HYPER',
    'type'       => UNIT_PLANS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 25000,
      'factor'        => 1,
    ),
    'max'        => 1,
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  UNIT_PLAN_SHIP_DEATH_STAR   => array(
    'name'       => 'UNIT_PLAN_SHIP_DEATH_STAR',
    'type'       => UNIT_PLANS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 10000,
      'factor'        => 1,
    ),
    'max'        => 1,
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  UNIT_PLAN_SHIP_SUPERNOVA    => array(
    'name'       => 'UNIT_PLAN_SHIP_SUPERNOVA',
    'type'       => UNIT_PLANS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 25000,
      'factor'        => 1,
    ),
    'max'        => 1,
    P_BONUS_TYPE => BONUS_ABILITY,
  ),
  UNIT_PLAN_DEF_SHIELD_PLANET => array(
    'name'       => 'UNIT_PLAN_DEF_SHIELD_PLANET',
    'type'       => UNIT_PLANS,
    'location'   => LOC_USER,
    'cost'       => array(
      RES_DARK_MATTER => 25000,
      'factor'        => 1,
    ),
    'max'        => 1,
    P_BONUS_TYPE => BONUS_ABILITY,
  ),

  /*
    MRC_EMPEROR => array(
      'name' => 'rpg_empereur',
      'type' => UNIT_MERCENARIES,
      'location' => LOC_USER,
      P_REQUIRE => array(MRC_ASSASIN => 1, MRC_DEFENDER => 1),
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
      'max' => 1,
      P_BONUS_TYPE => BONUS_ABILITY,
    ),
  */
);
