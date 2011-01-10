<?php

/**
 * vars.php
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

if ( defined('INSIDE')) {
  // Liste de champs pour l'indication des messages en attante
  $messfields = array (
     -1 => 'mnl_outbox',
      0 => 'mnl_spy',
      1 => 'mnl_joueur',
      2 => 'mnl_alliance',
      3 => 'mnl_attaque',
      4 => 'mnl_exploit',
      5 => 'mnl_transport',
     15 => 'mnl_expedition',
//     97 => 'mnl_general',
     99 => 'mnl_buildlist',
    100 => 'new_message'
  );

  // factor -> price_factor, perhour_factor
  $sn_data = array(
    1   => array(
      'name' => 'metal_mine',
      'cost' => array(
        RES_METAL     => 80,
        RES_CRYSTAL   => 20,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 80,
      'crystal' => 20,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1.5,
      'production' => array(
        RES_METAL     => create_function ('$level, $production_factor, $temperature', 'return  40 * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
        RES_CRYSTAL   => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_DEUTERIUM => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return -13 * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return   (40 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'crystal_perhour'   => 'return 0;',
      'deuterium_perhour' => 'return 0;',
      'energy_perhour'    => 'return - (13 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'type' => UNIT_STRUCTURE,
    ),

    2   => array(
      'name' => 'crystal_mine',
      'cost' => array(
        RES_METAL     => 48,
        RES_CRYSTAL   => 24,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 48,
      'crystal' => 24,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1.6,
      'production' => array(
        RES_METAL     => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_CRYSTAL   => create_function ('$level, $production_factor, $temperature', 'return  20 * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
        RES_DEUTERIUM => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return -10 * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return 0;',
      'crystal_perhour'   => 'return   (20 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'deuterium_perhour' => 'return 0;',
      'energy_perhour'    => 'return - (10 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'type' => UNIT_STRUCTURE,
    ),

    3   => array(
      'name' => 'deuterium_sintetizer',
      'cost' => array(
        RES_METAL     => 225,
        RES_CRYSTAL   => 75,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 225,
      'crystal' => 75,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1.5,
      'production' => array(
        RES_METAL     => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_CRYSTAL   => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_DEUTERIUM => create_function ('$level, $production_factor, $temperature', 'return  10 * $level * pow(1.1, $level) * (0.1 * $production_factor) * (-0.002 * $temperature + 1.28);'),
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return -20 * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return 0;',
      'crystal_perhour'   => 'return 0;',
      'deuterium_perhour' => 'return  ((10 * $BuildLevel * pow(1.1, $BuildLevel)) * (-0.002 * $BuildTemp + 1.28)) * (0.1 * $BuildLevelFactor);',
      'energy_perhour'    => 'return - (20 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'type' => UNIT_STRUCTURE,
    ),

    4   => array(
      'name' => 'solar_plant',
      'cost' => array(
        RES_METAL     => 75,
        RES_CRYSTAL   => 30,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 75,
      'crystal' => 30,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1.5,
      'production' => array(
        RES_METAL     => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_CRYSTAL   => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_DEUTERIUM => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return  20 * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return 0;',
      'crystal_perhour'   => 'return 0;',
      'deuterium_perhour' => 'return 0;',
      'energy_perhour'    => 'return   (20 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'type' => UNIT_STRUCTURE,
    ),

    12  => array(
      'name' => 'fusion_plant',
      'require' => array(3 => 5, TECH_ENERGY => 3),
      'cost' => array(
        RES_METAL     => 900,
        RES_CRYSTAL   => 360,
        RES_DEUTERIUM => 180,
        RES_ENERGY    => 0,
      ),
      'metal' => 900,
      'crystal' => 360,
      'deuterium' => 180,
      'energy' => 0,
      'factor' => 1.8,
      'production' => array(
        RES_METAL     => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_CRYSTAL   => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_DEUTERIUM => create_function ('$level, $production_factor, $temperature', 'return -10 * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return  50 * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return 0;',
      'crystal_perhour'   => 'return 0;',
      'deuterium_perhour' => 'return - (10 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'energy_perhour'    => 'return   (50 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'type' => UNIT_STRUCTURE,
    ),

    14  => array(
      'name' => 'robot_factory',
      'cost' => array(
        RES_METAL     => 400,
        RES_CRYSTAL   => 120,
        RES_DEUTERIUM => 200,
        RES_ENERGY    => 0,
      ),
      'metal' => 400,
      'crystal' => 120,
      'deuterium' => 200,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    15  => array(
      'name' => 'nano_factory',
      'require' => array(14 => 10, 108 => 10),
      'cost' => array(
        RES_METAL     => 1000000,
        RES_CRYSTAL   => 500000,
        RES_DEUTERIUM => 100000,
        RES_ENERGY    => 0,
      ),
      'metal' => 1000000,
      'crystal' => 500000,
      'deuterium' => 100000,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    21  => array(
      'name' => 'hangar',
      'require' => array(14 => 2),
      'cost' => array(
        RES_METAL     => 400,
        RES_CRYSTAL   => 200,
        RES_DEUTERIUM => 100,
        RES_ENERGY    => 0,
      ),
      'metal' => 400,
      'crystal' => 200,
      'deuterium' => 100,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    22  => array(
      'name' => 'metal_store',
      'cost' => array(
        RES_METAL     => 2000,
        RES_CRYSTAL   => 0,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000,
      'crystal' => 0,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    23  => array(
      'name' => 'crystal_store',
      'cost' => array(
        RES_METAL     => 2000,
        RES_CRYSTAL   => 1000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000,
      'crystal' => 1000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    24  => array(
      'name' => 'deuterium_store',
      'cost' => array(
        RES_METAL     => 2000,
        RES_CRYSTAL   => 2000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000,
      'crystal' => 2000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    31  => array(
      'name' => 'laboratory',
      'cost' => array(
        RES_METAL     => 200,
        RES_CRYSTAL   => 400,
        RES_DEUTERIUM => 200,
        RES_ENERGY    => 0,
      ),
      'metal' => 200,
      'crystal' => 400,
      'deuterium' => 200,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    33  => array(
      'name' => 'terraformer',
      'require' => array(15 => 1, TECH_ENERGY => 12),
      'cost' => array(
        RES_METAL     => 0,
        RES_CRYSTAL   => 50000,
        RES_DEUTERIUM => 100000,
        RES_ENERGY    => 1000,
      ),
      'metal' => 0,
      'crystal' => 50000,
      'deuterium' => 100000,
      'energy' => 1000,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    34  => array(
      'name' => 'ally_deposit',
      'cost' => array(
        RES_METAL     => 20000,
        RES_CRYSTAL   => 40000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 20000,
      'crystal' => 40000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    35  => array(
      'name' => 'nano',
      'require' => array(31 => 10, TECH_ENERGY => 10),
      'cost' => array(
        RES_METAL     => 1500000,
        RES_CRYSTAL   => 750000,
        RES_DEUTERIUM => 150000,
        RES_ENERGY    => 0,
      ),
      'metal' => 1500000,
      'crystal' => 750000,
      'deuterium' => 150000,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    41  => array(
      'name' => 'mondbasis',
      'cost' => array(
        RES_METAL     => 20000,
        RES_CRYSTAL   => 40000,
        RES_DEUTERIUM => 20000,
        RES_ENERGY    => 0,
      ),
      'metal' => 20000,
      'crystal' => 40000,
      'deuterium' => 20000,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    42  => array(
      'name' => 'phalanx',
      'require' => array(41 => 1),
      'cost' => array(
        RES_METAL     => 20000,
        RES_CRYSTAL   => 40000,
        RES_DEUTERIUM => 20000,
        RES_ENERGY    => 0,
      ),
      'metal' => 20000,
      'crystal' => 40000,
      'deuterium' => 20000,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    43  => array(
      'name' => 'sprungtor',
      'require' => array(41 => 1, 114 => 7),
      'cost' => array(
        RES_METAL     => 2000000,
        RES_CRYSTAL   => 4000000,
        RES_DEUTERIUM => 2000000,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000000,
      'crystal' => 4000000,
      'deuterium' => 2000000,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    44  => array(
      'name' => 'silo',
      'require' => array(117 => 1),
      'cost' => array(
        RES_METAL     => 20000,
        RES_CRYSTAL   => 20000,
        RES_DEUTERIUM => 1000,
        RES_ENERGY    => 0,
      ),
      'metal' => 20000,
      'crystal' => 20000,
      'deuterium' => 1000,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    106 => array(
      'name' => 'spy_tech',
      'require' => array(31 => 3),
      'cost' => array(
        RES_METAL     => 200,
        RES_CRYSTAL   => 1000,
        RES_DEUTERIUM => 200,
        RES_ENERGY    => 0,
      ),
      'metal' => 200,
      'crystal' => 1000,
      'deuterium' => 200,
      'energy' => 0,
      'factor' => 2,
    ),

    108 => array(
      'name' => 'computer_tech',
      'require' => array(31 => 1),
      'cost' => array(
        RES_METAL     => 0,
        RES_CRYSTAL   => 400,
        RES_DEUTERIUM => 600,
        RES_ENERGY    => 0,
      ),
      'metal' => 0,
      'crystal' => 400,
      'deuterium' => 600,
      'energy' => 0,
      'factor' => 2,
    ),

    109 => array(
      'name' => 'military_tech',
      'require' => array(31 => 4),
      'cost' => array(
        RES_METAL     => 800,
        RES_CRYSTAL   => 200,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 800,
      'crystal' => 200,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 2,
    ),

    110 => array(
      'name' => 'shield_tech',
      'require' => array(TECH_ENERGY => 3, 31 => 6),
      'cost' => array(
        RES_METAL     => 200,
        RES_CRYSTAL   => 600,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 200,
      'crystal' => 600,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 2,
    ),

    111 => array(
      'name' => 'defence_tech',
      'require' => array(31 => 2),
      'cost' => array(
        RES_METAL     => 1000,
        RES_CRYSTAL   => 0,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 1000,
      'crystal' => 0,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 2,
    ),

    TECH_ENERGY => array(
      'name' => 'energy_tech',
      'require' => array(31 => 1),
      'cost' => array(
        RES_METAL     => 0,
        RES_CRYSTAL   => 800,
        RES_DEUTERIUM => 400,
        RES_ENERGY    => 0,
      ),
      'metal' => 0,
      'crystal' => 800,
      'deuterium' => 400,
      'energy' => 0,
      'factor' => 2,
      'bonus' => 10,
      'bonus_type' => BONUS_PERCENT,
    ),

    114 => array(
      'name' => 'hyperspace_tech',
      'require' => array(TECH_ENERGY => 5, 110 => 5, 31 => 7),
      'cost' => array(
        RES_METAL     => 0,
        RES_CRYSTAL   => 4000,
        RES_DEUTERIUM => 2000,
        RES_ENERGY    => 0,
      ),
      'metal' => 0,
      'crystal' => 4000,
      'deuterium' => 2000,
      'energy' => 0,
      'factor' => 2,
    ),

    115 => array(
      'name' => 'combustion_tech',
      'require' => array(TECH_ENERGY => 1, 31 => 1),
      'cost' => array(
        RES_METAL     => 400,
        RES_CRYSTAL   => 0,
        RES_DEUTERIUM => 600,
        RES_ENERGY    => 0,
      ),
      'metal' => 400,
      'crystal' => 0,
      'deuterium' => 600,
      'energy' => 0,
      'factor' => 2,
      'speed_increase' => 0.1,
    ),

    117 => array(
      'name' => 'impulse_motor_tech',
      'require' => array(TECH_ENERGY => 1, 31 => 2),
      'cost' => array(
        RES_METAL     => 2000,
        RES_CRYSTAL   => 4000,
        RES_DEUTERIUM => 600,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000,
      'crystal' => 4000,
      'deuterium' => 600,
      'energy' => 0,
      'factor' => 2,
      'speed_increase' => 0.2,
    ),

    118 => array(
      'name' => 'hyperspace_motor_tech',
      'require' => array(114 => 3, 31 => 7),
      'cost' => array(
        RES_METAL     => 10000,
        RES_CRYSTAL   => 20000,
        RES_DEUTERIUM => 6000,
        RES_ENERGY    => 0,
      ),
      'metal' => 10000,
      'crystal' => 20000,
      'deuterium' => 6000,
      'energy' => 0,
      'factor' => 2,
      'speed_increase' => 0.3,
    ),

    120 => array(
      'name' => 'laser_tech',
      'require' => array(31 => 1, TECH_ENERGY => 2),
      'cost' => array(
        RES_METAL     => 200,
        RES_CRYSTAL   => 100,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 200,
      'crystal' => 100,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 2,
    ),

    121 => array(
      'name' => 'ionic_tech',
      'require' => array(31 => 4, 120 => 5, TECH_ENERGY => 4),
      'cost' => array(
        RES_METAL     => 1000,
        RES_CRYSTAL   => 300,
        RES_DEUTERIUM => 100,
        RES_ENERGY    => 0,
      ),
      'metal' => 1000,
      'crystal' => 300,
      'deuterium' => 100,
      'energy' => 0,
      'factor' => 2,
    ),

    122 => array(
      'name' => 'buster_tech',
      'require' => array(31 => 5, TECH_ENERGY => 8, 120 => 10, 121 => 5),
      'cost' => array(
        RES_METAL     => 2000,
        RES_CRYSTAL   => 4000,
        RES_DEUTERIUM => 1000,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000,
      'crystal' => 4000,
      'deuterium' => 1000,
      'energy' => 0,
      'factor' => 2,
    ),

    123 => array(
      'name' => 'intergalactic_tech',
      'require' => array(31 => 10, 108 => 8, 114 => 8),
      'cost' => array(
        RES_METAL     => 240000,
        RES_CRYSTAL   => 400000,
        RES_DEUTERIUM => 160000,
        RES_ENERGY    => 0,
      ),
      'metal' => 240000,
      'crystal' => 400000,
      'deuterium' => 160000,
      'energy' => 0,
      'factor' => 2,
    ),

    124 => array(
      'name' => 'expedition_tech',
      'require' => array(31 => 3, 108 => 4, 117 => 3),
      'cost' => array(
        RES_METAL     => 4000,
        RES_CRYSTAL   => 8000,
        RES_DEUTERIUM => 4000,
        RES_ENERGY    => 0,
      ),
      'metal' => 4000,
      'crystal' => 8000,
      'deuterium' => 4000,
      'energy' => 0,
      'factor' => 2,
    ),

    150 => array(
      'name' => 'colonisation_tech',
      'require' => array(31 => 3, TECH_ENERGY => 5, 111 => 2),
      'cost' => array(
        RES_METAL     => 1000,
        RES_CRYSTAL   => 4000,
        RES_DEUTERIUM => 1000,
        RES_ENERGY    => 0,
      ),
      'metal' => 1000,
      'crystal' => 4000,
      'deuterium' => 1000,
      'energy' => 0,
      'factor' => 2,
    ),

    199 => array(
      'name' => 'graviton_tech',
      'require' => array(31 => 12),
      'cost' => array(
        RES_METAL     => 0,
        RES_CRYSTAL   => 0,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 300000,
      ),
      'metal' => 0,
      'crystal' => 0,
      'deuterium' => 0,
      'energy_max' => 300000,
      'factor' => 3,
    ),

    202 => array(
      'name' => 'small_ship_cargo',
      'require' => array(21 => 2, 115 => 2),
      'cost' => array(
        RES_METAL     => 2000,
        RES_CRYSTAL   => 2000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000,
      'crystal' => 2000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 5000,
      'tech' => 115,
      'speed' => 5000,
      'consumption' => 20,
      'tech_level' => 5,
      'tech2' => 117,
      'speed2' => 10000,
      'consumption2' => 40,
      'shield' => 10,
      'attack' => 5,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 100.01, 211 => 1, 212 => 210, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 400,
      'stackable' => true,
    ),

    203 => array(
      'name' => 'big_ship_cargo',
      'require' => array(21 => 4, 115 => 6),
      'cost' => array(
        RES_METAL     => 6000,
        RES_CRYSTAL   => 6000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 6000,
      'crystal' => 6000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 25000,
      'tech' => 115,
      'speed' => 7500,
      'consumption' => 50,
      'shield' => 25,
      'attack' => 5,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 100.01, 211 => 1, 212 => 210, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1200,
      'stackable' => true,
    ),

    201 => array(
      'name' => 'supercargo',
      'require' => array(21 => 8, 117 => 5, MRC_STOCKMAN => 1),
      'cost' => array(
        RES_METAL     => 25000,
        RES_CRYSTAL   => 15000,
        RES_DEUTERIUM => 5000,
        RES_ENERGY    => 0,
      ),
      'metal' => 25000,
      'crystal' => 15000,
      'deuterium' => 5000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 100000,
      'tech' => 117,
      'speed' => 5000,
      'consumption' => 100,
      'shield' => 50,
      'attack' => 10,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 100.01, 211 => 1, 212 => 210, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 3000,
      'stackable' => true,
    ),

    204 => array(
      'name' => 'light_hunter',
      'require' => array(21 => 1, 115 => 1),
      'cost' => array(
        RES_METAL     => 3000,
        RES_CRYSTAL   => 1000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 3000,
      'crystal' => 1000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 50,
      'tech' => 115,
      'speed' => 12500,
      'consumption' => 20,
      'shield' => 10,
      'attack' => 50,
      'sd' => array(201=> 1, 202 => 2, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 16.4, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 10.001, 211 => 1, 212 => 21, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 400,
      'stackable' => true,
    ),

    205 => array(
      'name' => 'heavy_hunter',
      'require' => array(21 => 3, 111 => 2, 117 => 2),
      'cost' => array(
        RES_METAL     => 6000,
        RES_CRYSTAL   => 4000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 6000,
      'crystal' => 4000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 100,
      'tech' => 117,
      'speed' => 10000,
      'consumption' => 75,
      'shield' => 25,
      'attack' => 150,
      'sd' => array(201=> 1, 202 => 3, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 8.2, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 3.33367, 211 => 1, 212 => 7, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1000,
      'stackable' => true,
    ),

    206 => array(
      'name' => 'crusher',
      'require' => array(21 => 5, 117 => 4, 121 => 2),
      'cost' => array(
        RES_METAL     => 20000,
        RES_CRYSTAL   => 7000,
        RES_DEUTERIUM => 2000,
        RES_ENERGY    => 0,
      ),
      'metal' => 20000,
      'crystal' => 7000,
      'deuterium' => 2000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 800,
      'tech' => 117,
      'speed' => 15000,
      'consumption' => 300,
      'shield' => 50,
      'attack' => 400,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 6, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 10, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 6.15, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 1.25013, 211 => 1, 212 => 2.625, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 5.5, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 2700,
      'stackable' => true,
    ),

    207 => array(
      'name' => 'battle_ship',
      'require' => array(21 => 7, 118 => 4),
      'cost' => array(
        RES_METAL     => 45000,
        RES_CRYSTAL   => 15000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 45000,
      'crystal' => 15000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 1500,
      'tech' => 118,
      'speed' => 10000,
      'consumption' => 500,
      'shield' => 200,
      'attack' => 1000,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 8, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 0.50005, 211 => 1, 212 => 1.05, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1.76, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 6000,
      'stackable' => true,
    ),

    208 => array(
      'name' => 'colonizer',
      'require' => array(21 => 4, 117 => 3, 150 => 2),
      'cost' => array(
        RES_METAL     => 10000,
        RES_CRYSTAL   => 20000,
        RES_DEUTERIUM => 10000,
        RES_ENERGY    => 0,
      ),
      'metal' => 10000,
      'crystal' => 20000,
      'deuterium' => 10000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 7500,
      'tech' => 117,
      'speed' => 2500,
      'consumption' => 1000,
      'shield' => 100,
      'attack' => 50,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 10.001, 211 => 1, 212 => 21, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 3000,
      'stackable' => true,
    ),

    209 => array(
      'name' => 'recycler',
      'require' => array(21 => 4, 115 => 6, 110 => 2),
      'cost' => array(
        RES_METAL     => 10000,
        RES_CRYSTAL   => 6000,
        RES_DEUTERIUM => 2000,
        RES_ENERGY    => 0,
      ),
      'metal' => 10000,
      'crystal' => 6000,
      'deuterium' => 2000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 20000,
      'tech' => 115,
      'speed' => 2000,
      'consumption' => 300,
      'shield' => 10,
      'attack' => 1,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 500.05, 211 => 1, 212 => 1050, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1600,
      'stackable' => true,
    ),

    210 => array(
      'name' => 'spy_sonde',
      'require' => array(21 => 3, 115 => 3, 106 => 2),
      'cost' => array(
        RES_METAL     => 0,
        RES_CRYSTAL   => 1000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 0,
      'crystal' => 1000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 5,
      'tech' => 115,
      'speed' => 100000000,
      'consumption' => 1,
      'shield' => 0.01,
      'attack' => 0.01,
      'sd' => array( 201=> 0, 202 => 0, 203 => 0, 204 => 0, 205 => 0, 206 => 0, 207 => 0, 208 => 0, 209 => 0, 210 => 0, 211 => 0, 212 => 0, 213 => 0, 214 => 0, 215 => 0, 216 => 0, 401 => 0, 402 => 0, 403 => 0, 404 => 0, 405 => 0, 406 => 0, 407 => 0, 408 => 0, 409 => 0),
      'amplify' => array( 201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 1, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 100,
      'stackable' => true,
    ),

    211 => array(
      'name' => 'bomber_ship',
      'require' => array(117 => 6, 21 => 8, 122 => 5),
      'cost' => array(
        RES_METAL     => 50000,
        RES_CRYSTAL   => 25000,
        RES_DEUTERIUM => 15000,
        RES_ENERGY    => 0,
      ),
      'metal' => 50000,
      'crystal' => 25000,
      'deuterium' => 15000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 500,
      'tech' => 117,
      'speed' => 4000,
      'consumption' => 1000,
      'tech_level' => 8,
      'tech2' => 118,
      'speed2' => 5000,
      'consumption2' => 1250,
      'shield' => 500,
      'attack' => 1000,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 20, 402 => 20, 403 => 10, 404 => 1, 405 => 10, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 0.50005, 211 => 1, 212 => 1.05, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 4.4, 402 => 4.5, 403 => 9, 404 => 1, 405 => 13, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 7500,
      'stackable' => true,
    ),

    212 => array(
      'name' => 'solar_satelit',
      'require' => array(21 => 1),
      'cost' => array(
        RES_METAL     => 1500,
        RES_CRYSTAL   => 2000,
        RES_DEUTERIUM => 100,
        RES_ENERGY    => 0,
      ),
      'metal' => 1500,
      'crystal' => 2000,
      'deuterium' => 100,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 0,
      'tech' => 115,
      'speed' => 0,
      'consumption' => 0,
      'shield' => 10,
      'attack' => 1,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 1, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 0, 402 => 0, 403 => 0, 404 => 0, 405 => 0, 406 => 0, 407 => 0, 408 => 0, 409 => 0),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 1, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 200,
      'production' => array(
        RES_METAL     => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_CRYSTAL   => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_DEUTERIUM => create_function ('$level, $production_factor, $temperature', 'return 0;'),
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return ($temperature / 4 + 20) * $level * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return 0;',
      'crystal_perhour'   => 'return 0;',
      'deuterium_perhour' => 'return 0;',
      'energy_perhour'    => 'return  (($BuildTemp / 4) + 20) * $BuildLevel * (0.1 * $BuildLevelFactor);',
      'stackable' => true,
    ),

    213 => array(
      'name' => 'destructor',
      'require' => array(21 => 9, 118 => 6, 114 => 5),
      'cost' => array(
        RES_METAL     => 60000,
        RES_CRYSTAL   => 50000,
        RES_DEUTERIUM => 15000,
        RES_ENERGY    => 0,
      ),
      'metal' => 60000,
      'crystal' => 50000,
      'deuterium' => 15000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 2000,
      'tech' => 118,
      'speed' => 5000,
      'consumption' => 1000,
      'shield' => 500,
      'attack' => 2000,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 2, 216 => 1, 401 => 1, 402 => 10, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 0.25003, 211 => 1, 212 => 0.525, 213 => 1, 214 => 1, 215 => 7.4, 216 => 1, 401 => 1, 402 => 1.125, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 11000,
      'stackable' => true,
    ),

    214 => array(
      'name' => 'dearth_star',
      'require' => array(21 => 12, 118 => 7, 114 => 6, 199 => 1, MRC_DESTRUCTOR => 1),
      'cost' => array(
        RES_METAL     => 5000000,
        RES_CRYSTAL   => 4000000,
        RES_DEUTERIUM => 1000000,
        RES_ENERGY    => 0,
      ),
      'metal' => 5000000,
      'crystal' => 4000000,
      'deuterium' => 1000000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 1000000,
      'tech' => 118,
      'speed' => 100,
      'consumption' => 1,
      'shield' => 50000,
      'attack' => 200000,
      'sd' => array(201 => 100, 202 => 200, 203 => 150, 204 => 200, 205 => 100, 206 => 33, 207 => 30, 208 => 250, 209 => 250, 210 => 1250, 211 => 25, 212 => 1250, 213 => 5, 214 => 1, 215 => 15, 216 => 1, 401 => 200, 402 => 200, 403 => 100, 404 => 50, 405 => 100, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array( 201 => 2.025, 202 => 0.41, 203 => 0.91875, 204 => 0.41, 205 => 0.5125, 206 => 0.45375, 207 => 0.93, 208 => 3.875, 209 => 2.0125, 210 => 0.62506, 211 => 1, 212 => 1.3125, 213 => 0.2875, 214 => 1, 215 => 0.03, 216 => 1, 401 => 0.02, 402 => 0.025, 403 => 0.05, 404 => 0.05, 405 => 0.25, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 900000,
      'stackable' => true,
    ),

    215 => array(
      'name' => 'battleship',
      'require' => array(114 => 5, 120 => 12, 118 => 5, 21 => 8),
      'cost' => array(
        RES_METAL     => 30000,
        RES_CRYSTAL   => 40000,
        RES_DEUTERIUM => 15000,
        RES_ENERGY    => 0,
      ),
      'metal' => 30000,
      'crystal' => 40000,
      'deuterium' => 15000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 750,
      'tech' => 118,
      'speed' => 10000,
      'consumption' => 250,
      'shield' => 400,
      'attack' => 700,
      'sd' => array( 202 => 5, 203 => 3, 201 => 2, 204 => 1, 205 => 4, 206 => 4, 207 => 7, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array( 201 => 11.57143, 202 => 2.92857, 203 => 5.25, 204 => 1, 205 => 5.85714, 206 => 15.71429, 207 => 62, 208 => 1, 209 => 1, 210 => 0.71436, 211 => 1, 212 => 1.5, 213 => 1, 214 => 1, 215 => 1, 216 => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 7000,
      'stackable' => true,
    ),

    216 => array(
      'name' => 'supernova',
      'require' => array(MRC_ASSASIN => 1),
      'cost' => array(
        RES_METAL     => 20000000,
        RES_CRYSTAL   => 15000000,
        RES_DEUTERIUM => 5000000,
        RES_ENERGY    => 0,
      ),
      'metal' => 20000000,
      'crystal' => 15000000,
      'deuterium' => 5000000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 2000000,
      'tech' => 118,
      'speed' => 150,
      'consumption' => 250,
      'shield' => 1000000,
      'attack' => 1000000,
      'sd' => array( 201 => 150, 202 => 250, 203 => 200, 204 => 200, 205 => 100, 206 => 33, 207 => 30, 208 => 250, 209 => 250, 210 => 1250, 211 => 25, 212 => 1250, 213 => 5, 214 => 1, 215 => 15, 216 => 1, 401 => 200, 402 => 200, 403 => 100, 404 => 50, 405 => 100, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array( 201 => 0.6075, 202 => 0.1025, 203 => 0.245, 204 => 0.082, 205 => 0.1025, 206 => 0.09075, 207 => 0.186, 208 => 0.775, 209 => 0.4025, 210 => 0.12501, 211 => 0.2, 212 => 0.2625, 213 => 0.0575, 214 => 1, 215 => 0.111, 216 => 1, 401 => 0.004, 402 => 0.005, 403 => 0.01, 404 => 0.01, 405 => 0.05, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 3500000,
      'stackable' => true,
    ),

    217 => array(
      'name' => 'assault_ship',
    ),

    401 => array(
      'name' => 'misil_launcher',
      'require' => array(21 => 1),
      'cost' => array(
        RES_METAL     => 2000,
        RES_CRYSTAL   => 0,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000,
      'crystal' => 0,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'shield' => 20,
      'attack' => 80,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 6.25063, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'armor' => 200,
      'stackable' => true,
    ),

    402 => array(
      'name' => 'small_laser',
      'require' => array(TECH_ENERGY => 1, 21 => 2, 120 => 3),
      'cost' => array(
        RES_METAL     => 1500,
        RES_CRYSTAL   => 500,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 1500,
      'crystal' => 500,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'shield' => 25,
      'attack' => 100,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5.0005, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'armor' => 200,
      'stackable' => true,
    ),

    403 => array(
      'name' => 'big_laser',
      'require' => array(TECH_ENERGY => 3, 21 => 4, 120 => 6),
      'cost' => array(
        RES_METAL     => 6000,
        RES_CRYSTAL   => 2000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 6000,
      'crystal' => 2000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'shield' => 100,
      'attack' => 250,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 2.0002, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'armor' => 800,
      'stackable' => true,
    ),

    404 => array(
      'name' => 'gauss_canyon',
      'require' => array(21 => 6, TECH_ENERGY => 6, 109 => 3, 110 => 1),
      'cost' => array(
        RES_METAL     => 20000,
        RES_CRYSTAL   => 15000,
        RES_DEUTERIUM => 2000,
        RES_ENERGY    => 0,
      ),
      'metal' => 20000,
      'crystal' => 15000,
      'deuterium' => 2000,
      'energy' => 0,
      'factor' => 1,
      'shield' => 200,
      'attack' => 1100,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 0.45459, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'armor' => 3500,
      'stackable' => true,
    ),

    405 => array(
      'name' => 'ionic_canyon',
      'require' => array(21 => 4, 121 => 4),
      'cost' => array(
        RES_METAL     => 2000,
        RES_CRYSTAL   => 6000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 2000,
      'crystal' => 6000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'shield' => 500,
      'attack' => 150,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 3.33367, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'armor' => 800,
      'stackable' => true,
    ),

    406 => array(
      'name' => 'buster_canyon',
      'require' => array(21 => 8, 122 => 7),
      'cost' => array(
        RES_METAL     => 50000,
        RES_CRYSTAL   => 50000,
        RES_DEUTERIUM => 30000,
        RES_ENERGY    => 0,
      ),
      'metal' => 50000,
      'crystal' => 50000,
      'deuterium' => 30000,
      'energy' => 0,
      'factor' => 1,
      'shield' => 300,
      'attack' => 3000,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 0.16668, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'armor' => 10000,
      'stackable' => true,
    ),

    407 => array(
      'name' => 'small_protection_shield',
      'require' => array(110 => 2, 21 => 1),
      'cost' => array(
        RES_METAL     => 10000,
        RES_CRYSTAL   => 10000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 10000,
      'crystal' => 10000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'shield' => 2000,
      'attack' => 1,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 500.05, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'armor' => 2000,
      'stackable' => true,
    ),

    408 => array(
      'name' => 'big_protection_shield',
      'require' => array(110 => 6, 21 => 6),
      'cost' => array(
        RES_METAL     => 50000,
        RES_CRYSTAL   => 50000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 50000,
      'crystal' => 50000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'shield' => 2000,
      'attack' => 1,
      'sd' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 5, 211 => 1, 212 => 0, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'amplify' => array(201=> 1, 202 => 1, 203 => 1, 204 => 1, 205 => 1, 206 => 1, 207 => 1, 208 => 1, 209 => 1, 210 => 500.05, 211 => 1, 212 => 1, 213 => 1, 214 => 1, 215 => 1, 216 => 1),
      'armor' => 10000,
      'stackable' => true,
    ),

    409 => array(
      'name'      => 'planet_protector',
      'require'   => array(MRC_DEFENDER => 1),
      'metal'     => 10000000,
      'crystal'   => 5000000,
      'deuterium' => 2500000,
      'energy'    => 0,
      'factor'    => 1,
      'shield'    => 1000000,
      'attack'    => 1000000,
      'sd'        => array( 201=> 50, 202 => 100, 203 => 80, 204 => 75, 205 => 60, 206 => 20, 207 => 20, 208 => 100, 209 => 100, 210 => 500, 211 => 10, 212 => 500, 213 => 2, 214 => 1, 215 => 5, 216 => 1),
      'amplify'   => array( 201 => 0.2025, 202 => 0.041, 203 => 0.098, 204 => 0.03075, 205 => 0.0615, 206 => 0.055, 207 => 0.124, 208 => 0.31, 209 => 0.161, 210 => 0.05001, 211 => 0.08, 212 => 0.105, 213 => 0.023, 214 => 1, 215 => 0.037, 216 => 1 ),
      'armor'     => 1500000,
      'stackable' => true,
    ),

    502 => array(
      'name' => 'interceptor_misil',
      'require' => array(44 => 2),
      'cost' => array(
        RES_METAL     => 8000,
        RES_CRYSTAL   => 2000,
        RES_DEUTERIUM => 0,
        RES_ENERGY    => 0,
      ),
      'metal' => 8000,
      'crystal' => 2000,
      'deuterium' => 0,
      'energy' => 0,
      'factor' => 1,
      'shield' => 1,
      'attack' => 1,
      'armor' => 1000,
      'stackable' => true,
    ),

    503 => array(
      'name' => 'interplanetary_misil',
      'require' => array(44 => 4),
      'cost' => array(
        RES_METAL     => 12500,
        RES_CRYSTAL   => 2500,
        RES_DEUTERIUM => 10000,
        RES_ENERGY    => 0,
      ),
      'metal' => 12500,
      'crystal' => 2500,
      'deuterium' => 10000,
      'energy' => 0,
      'factor' => 1,
      'shield' => 1,
      'attack' => 120000,
      'armor' => 1500,
      'stackable' => true,
    ),

    MRC_GEOLOGIST => array(
      'name' => 'rpg_geologue',
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 20,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_ADMIRAL => array(
      'name' => 'rpg_amiral',
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 20,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_POWERMAN => array(
      'name' => 'rpg_ingenieur',
      'require' => array(MRC_GEOLOGIST => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 10,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_CONSTRUCTOR => array(
      'name' => 'rpg_technocrate',
      'require' => array(MRC_ADMIRAL => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 10,
      'bonus' => -5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_ARCHITECT => array(
      'name' => 'rpg_constructeur',
      'require' => array(MRC_GEOLOGIST => 10, MRC_POWERMAN => 2),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => -5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_ACADEMIC => array(
      'name' => 'rpg_scientifique',
      'require' => array(MRC_GEOLOGIST => 10, MRC_POWERMAN => 2),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => -5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_STOCKMAN => array( // MRC_STOCKMAN
      'name' => 'rpg_stockeur',
      'require' => array(MRC_ARCHITECT => 1),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => 20,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_FORTIFIER => array(
      'name' => 'rpg_defenseur',
      'require' => array(MRC_ACADEMIC => 1),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => -20,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_DEFENDER => array(
      'name' => 'rpg_bunker',
      'require' => array(MRC_GEOLOGIST => 20, MRC_POWERMAN => 10, MRC_ARCHITECT => 3, MRC_ACADEMIC => 3, MRC_STOCKMAN => 2, MRC_FORTIFIER => 2),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),

    MRC_SPY => array(
      'name' => 'rpg_espion',
      'require' => array(MRC_ADMIRAL => 10, MRC_CONSTRUCTOR => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 3,
      'bonus' => 1,
      'bonus_type' => BONUS_ADD,
    ),

    MRC_COORDINATOR => array(
      'name' => 'rpg_commandant',
      'require' => array(MRC_ADMIRAL => 10, MRC_CONSTRUCTOR => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => 1,
      'bonus_type' => BONUS_ADD,
    ),

    MRC_DESTRUCTOR => array(
      'name' => 'rpg_destructeur',
      'require' => array(MRC_SPY => 1),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),

    MRC_NAVIGATOR => array(
      'name' => 'rpg_general',
      'require' => array(MRC_COORDINATOR => 1),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 10,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_ASSASIN => array(
      'name' => 'rpg_raideur',
      'require' => array(MRC_ADMIRAL => 20, MRC_CONSTRUCTOR => 10, MRC_SPY => 2, MRC_COORDINATOR => 2, MRC_DESTRUCTOR => 1, MRC_NAVIGATOR => 3),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),
/*
    MRC_EMPEROR => array(
      'name' => 'rpg_empereur',
      'require' => array(MRC_ASSASIN => 1, MRC_DEFENDER => 1),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),
*/
    RES_METAL => array(
      'name' => 'metal',
    ),

    RES_CRYSTAL => array(
      'name' => 'crystal',
    ),

    RES_DEUTERIUM => array(
      'name' => 'deuterium',
    ),

    RES_ENERGY => array(
      'name' => 'energy',
    ),

    RES_DARK_MATTER => array(
      'name' => 'dark_matter',
    ),

    'groups' => array(
      // Planet structures list
      'structures' => array ( 1, 2, 3, 4, 12, 14, 21, 15, 31, 35, 44, 22, 23, 24, 34, 33, 41, 42, 43 ),
      'build'      => array ( 1, 2, 3, 4, 12, 14, 21, 15, 31, 35, 44, 22, 23, 24, 34, 33, 41, 42, 43 ),
      'build_allow'=> array (
          PT_PLANET => array( 1, 2, 3, 4, 12, 14, 21, 15, 31, 35, 44, 22, 23, 24, 34, 33),
          PT_MOON   => array( 12, 14, 21, 22, 23, 24, 34, 41, 42, 43),
      ),

      // Tech list
      'tech'      => array ( 106, 108, 109, 110, 111, TECH_ENERGY, 114, 115, 117, 118, 120, 121, 122, 123, 124, 150, 199),
      // Mercenary list
      'mercenaries' => array (
        MRC_GEOLOGIST, MRC_ADMIRAL, MRC_POWERMAN, MRC_CONSTRUCTOR, MRC_ARCHITECT, MRC_ACADEMIC, MRC_STOCKMAN,
        MRC_FORTIFIER, MRC_DEFENDER, MRC_SPY, MRC_COORDINATOR, MRC_DESTRUCTOR, MRC_NAVIGATOR,
        MRC_ASSASIN //, MRC_EMPEROR
      ),
      'governors' => array(
        MRC_GEOLOGIST, MRC_POWERMAN, MRC_CONSTRUCTOR, MRC_ARCHITECT, MRC_ACADEMIC, MRC_FORTIFIER
      ),
      // Spaceships list
      'fleet'     => array ( 202, 203, 201, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216 ),
      // Defensive building list
      'defense'   => array ( 401, 402, 403, 404, 405, 406, 407, 408, 409, 502, 503 ),

      // List of units that can produce resources
      'prod'      => array ( 1, 2, 3, 4, 12, 212 ),

      // Combat units list
      'combat'    => array ( 202, 203, 201, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 401, 402, 403, 404, 405, 406, 407, 408, 409 ),
      // Planet active defense list
      'defense_active' => array ( 401, 402, 403, 404, 405, 406, 407, 408, 409 ),

      // Resource list
      'resources' => array ( 0 => 'metal', 1 => 'crystal', 2 => 'deuterium', 3 => 'dark_matter'),
      // Resources can be produced on planet
      'resources_planet' => array (RES_METAL, RES_CRYSTAL, RES_DEUTERIUM, RES_ENERGY),
      // Resources can be looted from planet
      'resources_loot' => array (RES_METAL, RES_CRYSTAL, RES_DEUTERIUM),

//      // Ques list
//      'ques' => array(QUE_STRUCTURES, QUE_HANGAR, QUE_RESEARCH),
    ),
  );

  $sn_data['groups']['ques'] = array(
    QUE_STRUCTURES => array(
      'unit_list' => $sn_data['groups']['structures'],
      'length' => 5,
      'mercenary' => MRC_ARCHITECT,
      'que' => QUE_STRUCTURES,
    ),

    QUE_HANGAR => array(
      'unit_list' => array_merge($sn_data['groups']['fleet'], $sn_data['groups']['defense']),
      'length' => 10,
      'mercenary' => MRC_CONSTRUCTOR,
      'que' => QUE_HANGAR,
    ),

    QUE_RESEARCH => array(
      'unit_list' => $sn_data['groups']['tech'],
      'length' => 1,
      'mercenary' => MRC_ACADEMIC,
      'que' => QUE_RESEARCH,
    )
  );

  $sn_data['groups']['subques'] = array(
    SUBQUE_PLANET => array(
      'que' => QUE_STRUCTURES,
      'mercenary' => MRC_ARCHITECT,
      'unit_list' => $sn_data['groups']['build_allow'][PT_PLANET],
    ),

    SUBQUE_MOON => array(
      'que' => QUE_STRUCTURES,
      'mercenary' => MRC_ARCHITECT,
      'unit_list' => $sn_data['groups']['build_allow'][PT_MOON],
    ),

    SUBQUE_FLEET => array(
      'que' => QUE_HANGAR,
      'mercenary' => MRC_CONSTRUCTOR,
      'unit_list' => $sn_data['groups']['fleet'],
    ),

    SUBQUE_DEFENSE => array(
      'que' => QUE_HANGAR,
      'mercenary' => MRC_FORTIFIER,
      'unit_list' => $sn_data['groups']['defense'],
    ),

    SUBQUE_RESEARCH => array(
      'que' => QUE_RESEACH,
      'mercenary' => MRC_ACADEMIC,
      'unit_list' => $sn_data['groups']['tech'],
    ),
  );

  $sn_groups = &$sn_data['groups'];
  $reslist   = &$sn_groups;

  $user_options = array('compat_builds' => 0);

  $tableList = array( 'aks', 'alliance', 'alliance_requests', 'announce', 'annonce', 'banned', 'buddy', 'chat', 'config', 'counter',
    'errors', 'fleets', 'fleet_log', 'galaxy', 'iraks', 'logs', 'messages', 'notes', 'planets', 'referrals', 'rw', 'statpoints',
    'users'
  );

  // Parsing united $sn_data table to old-style tables for compatibility
  // -------------------------------------------------------------------
  $pricelist_fields = array('metal', 'crystal', 'deuterium','energy', 'factor', 'capacity', 'tech', 'speed', 'consumption', 'consumption2',
    'energy_max', 'max', 'speed2', 'speed_increase', 'tech2', 'tech_level');

  $combatcaps_fields = array('shield', 'attack', 'sd', 'amplify');

  foreach($sn_data as $unit_id => $unit_data)
  {
    // Filling $resource table
    $resource[$unit_id] = $unit_data['name'];

    // Filling requeriments table
    if(isset($unit_data['require']))
    {
      $requeriments[$unit_id] = $unit_data['require'];
    }

    // Filling pricelist table
    foreach($pricelist_fields as $price_field_name)
    {
      if(isset($unit_data[$price_field_name]))
      {
        $pricelist[$unit_id][$price_field_name] = $unit_data[$price_field_name];
      }
    }

    // Filling combat caps table
    foreach($combatcaps_fields as $combat_field_name)
    {
      if(isset($unit_data[$combat_field_name]))
      {
        $CombatCaps[$unit_id][$combat_field_name] = $unit_data[$combat_field_name];
      }
    }
  }
  // END parse

  foreach ($CombatCaps as $unitID => $unitData)
  {
    $CombatCaps[$unitID]['armor'] = ($pricelist[$unitID]['metal'] + $pricelist[$unitID]['crystal'])/10;
/*
    foreach ($unitData['sd'] as $enemyID => $SPD)
    {
      if ($SPD>1)
      {

        // $enemyArmor = ($pricelist[$enemyID]['metal'] + $pricelist[$enemyID]['crystal'])/10;
        // $a1 = ($enemyArmor + $CombatCaps[$enemyID]['shield']) * $SPD / $unitData['attack'];

        $a1 = ($CombatCaps[$enemyID]['armor'] + $CombatCaps[$enemyID]['shield']) * $SPD / $unitData['attack'];
        $CombatCaps[$unitID]['amplify'][$enemyID] = $a1;
      }
      elseif ($SPD == 1)
      {
        $CombatCaps[$unitID]['amplify'][$enemyID] = 1;
      }
      elseif ($SPD < 0)
      {
        $CombatCaps[$unitID]['amplify'][$enemyID] = -$SPD;
      }
      elseif ($SPD == 0 || $SPD<1 || !is_numeric($SPD))
      {
        $CombatCaps[$unitID]['amplify'][$enemyID] = 0;
      }
    }
*/
  }

/*
  // Procedure to dump new 'amplify' values delivered from rapidfire
  foreach ($CombatCaps as $unitID => $unitData)
  {
    print("  $"."CombatCaps[" . $unitID . "]['amplify'] = array( ");
    foreach ($unitData['amplify'] as $enemyID => $SPD)
    {
      print($enemyID . ' => ' . round($SPD, 5) . ', ');
    }
    print(" );<br>");
  }
*/
}
?>
