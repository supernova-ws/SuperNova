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
      'deuterium_perhour' => 'return  ((10 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor) * (-0.002 * $BuildTemp + 1.28));',
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
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return  ($temperature / 4 + 20) * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return 0;',
      'crystal_perhour'   => 'return 0;',
      'deuterium_perhour' => 'return 0;',
      'energy_perhour'    => 'return   (($BuildTemp / 20 + 21) * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
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
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return  30 * $level * pow(1.05 + 0.01 * $GLOBALS["user_tech_energy"], $level) * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return 0;',
      'crystal_perhour'   => 'return 0;',
      'deuterium_perhour' => 'return - (10 * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'energy_perhour'    => 'return   (30 * $BuildLevel * pow(1.05 + 0.01 * $BuildEnergyTech, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
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
//      'bonus' => 10,
//      'bonus_type' => BONUS_PERCENT,
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

    SHIP_CARGO_SMALL => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 100.01, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 250, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 400,
      'stackable' => true,
    ),

    SHIP_CARGO_BIG => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 100.01, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 250, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1200,
      'stackable' => true,
    ),

    SHIP_CARGO_SUPER => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 100.01, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 250, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 3000,
      'stackable' => true,
    ),

    SHIP_FIGHTER_LIGHT => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 2, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 16.4, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 10.001, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 21, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 400,
      'stackable' => true,
    ),

    SHIP_FIGHTER_HEAVY => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 3, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 8.2, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 3.33367, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 7, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1000,
      'stackable' => true,
    ),

    SHIP_DESTROYER => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 6, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 10, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 6.15, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 1.25013, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 2.625, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 5.5, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 2700,
      'stackable' => true,
    ),

    SHIP_CRUISER => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 8, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.50005, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1.05, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1.76, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 6000,
      'stackable' => true,
    ),

    SHIP_COLONIZER => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 10.001, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 21, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 3000,
      'stackable' => true,
    ),

    SHIP_RECYCLER => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 500.05, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1050, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1600,
      'stackable' => true,
    ),

    SHIP_SPY => array(
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
      'sd' => array( SHIP_CARGO_SUPER=> 0, SHIP_CARGO_SMALL => 0, SHIP_CARGO_BIG => 0, SHIP_FIGHTER_LIGHT => 0, SHIP_FIGHTER_HEAVY => 0, SHIP_DESTROYER => 0, SHIP_CRUISER => 0, SHIP_COLONIZER => 0, SHIP_RECYCLER => 0, SHIP_SPY => 0, SHIP_BOMBER => 0, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 0, SHIP_DEATH_STAR => 0, SHIP_BATTLESHIP => 0, SHIP_SUPERNOVA => 0, 401 => 0, 402 => 0, 403 => 0, 404 => 0, 405 => 0, 406 => 0, 407 => 0, 408 => 0, 409 => 0),
      'amplify' => array( SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 1, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 100,
      'stackable' => true,
    ),

    SHIP_BOMBER => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 20, 402 => 20, 403 => 10, 404 => 1, 405 => 10, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.50005, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1.05, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 4.4, 402 => 4.5, 403 => 9, 404 => 1, 405 => 13, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 7500,
      'stackable' => true,
    ),

    SHIP_SATTELITE_SOLAR => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 1, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 0, 402 => 0, 403 => 0, 404 => 0, 405 => 0, 406 => 0, 407 => 0, 408 => 0, 409 => 0),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 1, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
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

    SHIP_DESTRUCTOR => array(
      'name' => 'destructor',
      'require' => array(21 => 9, 114 => 5, 118 => 6),
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 2, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 10, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.25003, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0.525, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 7.4, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1.125, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 11000,
      'stackable' => true,
    ),

    SHIP_DEATH_STAR => array(
      'name' => 'dearth_star',
      'require' => array(21 => 12, 114 => 6, 118 => 7, 199 => 1, MRC_DESTRUCTOR => 1),
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
      'sd' => array(SHIP_CARGO_SUPER => 100, SHIP_CARGO_SMALL => 200, SHIP_CARGO_BIG => 150, SHIP_FIGHTER_LIGHT => 200, SHIP_FIGHTER_HEAVY => 100, SHIP_DESTROYER => 33, SHIP_CRUISER => 30, SHIP_COLONIZER => 250, SHIP_RECYCLER => 250, SHIP_SPY => 1250, SHIP_BOMBER => 25, SHIP_SATTELITE_SOLAR => 1250, SHIP_DESTRUCTOR => 5, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 15, SHIP_SUPERNOVA => 1, 401 => 200, 402 => 200, 403 => 100, 404 => 50, 405 => 100, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array( SHIP_CARGO_SUPER => 2.025, SHIP_CARGO_SMALL => 0.41, SHIP_CARGO_BIG => 0.91875, SHIP_FIGHTER_LIGHT => 0.41, SHIP_FIGHTER_HEAVY => 0.5125, SHIP_DESTROYER => 0.45375, SHIP_CRUISER => 0.93, SHIP_COLONIZER => 3.875, SHIP_RECYCLER => 2.0125, SHIP_SPY => 0.62506, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1.3125, SHIP_DESTRUCTOR => 0.2875, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 0.03, SHIP_SUPERNOVA => 1, 401 => 0.02, 402 => 0.025, 403 => 0.05, 404 => 0.05, 405 => 0.25, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 900000,
      'stackable' => true,
    ),

    SHIP_BATTLESHIP => array(
      'name' => 'battleship',
      'require' => array(21 => 8, 114 => 5, 118 => 5, 120 => 12),
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
      'sd' => array( SHIP_CARGO_SMALL => 5, SHIP_CARGO_BIG => 3, SHIP_CARGO_SUPER => 2, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 4, SHIP_DESTROYER => 4, SHIP_CRUISER => 7, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array( SHIP_CARGO_SUPER => 11.57143, SHIP_CARGO_SMALL => 2.92857, SHIP_CARGO_BIG => 5.25, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 5.85714, SHIP_DESTROYER => 15.71429, SHIP_CRUISER => 62, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.71436, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1.5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 7000,
      'stackable' => true,
    ),

    SHIP_SUPERNOVA => array(
      'name' => 'supernova',
      'require' => array(21 => 15, 114 => 7, 118 => 9, 199 => 1, MRC_ASSASIN => 1),
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
      'sd' => array( SHIP_CARGO_SUPER => 150, SHIP_CARGO_SMALL => 250, SHIP_CARGO_BIG => 200, SHIP_FIGHTER_LIGHT => 200, SHIP_FIGHTER_HEAVY => 100, SHIP_DESTROYER => 33, SHIP_CRUISER => 30, SHIP_COLONIZER => 250, SHIP_RECYCLER => 250, SHIP_SPY => 1250, SHIP_BOMBER => 25, SHIP_SATTELITE_SOLAR => 1250, SHIP_DESTRUCTOR => 5, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 15, SHIP_SUPERNOVA => 1, 401 => 200, 402 => 200, 403 => 100, 404 => 50, 405 => 100, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array( SHIP_CARGO_SUPER => 0.6075, SHIP_CARGO_SMALL => 0.1025, SHIP_CARGO_BIG => 0.245, SHIP_FIGHTER_LIGHT => 0.082, SHIP_FIGHTER_HEAVY => 0.1025, SHIP_DESTROYER => 0.09075, SHIP_CRUISER => 0.186, SHIP_COLONIZER => 0.775, SHIP_RECYCLER => 0.4025, SHIP_SPY => 0.12501, SHIP_BOMBER => 0.2, SHIP_SATTELITE_SOLAR => 0.2625, SHIP_DESTRUCTOR => 0.0575, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 0.111, SHIP_SUPERNOVA => 1, 401 => 0.004, 402 => 0.005, 403 => 0.01, 404 => 0.01, 405 => 0.05, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 3500000,
      'stackable' => true,
    ),

    SHIP_FIGHTER_ASSAULT => array(
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 6.25063, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5.0005, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 2.0002, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.45459, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 3.33367, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.16668, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 500.05, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
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
      'sd' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_SUPER=> 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 500.05, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
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
      'sd'        => array( SHIP_CARGO_SUPER=> 50, SHIP_CARGO_SMALL => 100, SHIP_CARGO_BIG => 80, SHIP_FIGHTER_LIGHT => 75, SHIP_FIGHTER_HEAVY => 60, SHIP_DESTROYER => 20, SHIP_CRUISER => 20, SHIP_COLONIZER => 100, SHIP_RECYCLER => 100, SHIP_SPY => 500, SHIP_BOMBER => 10, SHIP_SATTELITE_SOLAR => 500, SHIP_DESTRUCTOR => 2, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 5, SHIP_SUPERNOVA => 1),
      'amplify'   => array( SHIP_CARGO_SUPER => 0.2025, SHIP_CARGO_SMALL => 0.041, SHIP_CARGO_BIG => 0.098, SHIP_FIGHTER_LIGHT => 0.03075, SHIP_FIGHTER_HEAVY => 0.0615, SHIP_DESTROYER => 0.055, SHIP_CRUISER => 0.124, SHIP_COLONIZER => 0.31, SHIP_RECYCLER => 0.161, SHIP_SPY => 0.05001, SHIP_BOMBER => 0.08, SHIP_SATTELITE_SOLAR => 0.105, SHIP_DESTRUCTOR => 0.023, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 0.037, SHIP_SUPERNOVA => 1 ),
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

    MRC_POWERMAN => array(
      'name' => 'rpg_ingenieur',
      'require' => array(MRC_GEOLOGIST => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 10,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_STOCKMAN => array( // MRC_STOCKMAN
      'name' => 'rpg_stockeur',
      'require' => array(MRC_GEOLOGIST => 10, MRC_POWERMAN => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 10,
      'bonus' => 20,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_ARCHITECT => array(
      'name' => 'rpg_constructeur',
      'require' => array(MRC_POWERMAN => 10, MRC_STOCKMAN => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => -5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_SPY => array(
      'name' => 'rpg_espion',
      'require' => array(MRC_STOCKMAN => 10, MRC_ARCHITECT => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => 1,
      'bonus_type' => BONUS_ADD,
    ),

    MRC_COORDINATOR => array(
      'name' => 'rpg_commandant',
      'require' => array(MRC_GEOLOGIST => 15, MRC_SPY => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => 1,
      'bonus_type' => BONUS_ADD,
    ),

    MRC_DESTRUCTOR => array(
      'name' => 'rpg_destructeur',
      'require' => array(MRC_GEOLOGIST => 20, MRC_FORTIFIER => 5, MRC_COORDINATOR => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),

    MRC_ADMIRAL => array(
      'name' => 'rpg_amiral',
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 20,
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

    MRC_ACADEMIC => array(
      'name' => 'rpg_scientifique',
      'require' => array(MRC_ADMIRAL => 10, MRC_CONSTRUCTOR => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 10,
      'bonus' => -5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_FORTIFIER => array(
      'name' => 'rpg_defenseur',
      'require' => array(MRC_CONSTRUCTOR => 10, MRC_ACADEMIC => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 5,
      'bonus' => -10,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_DEFENDER => array(
      'name' => 'rpg_bunker',
      'require' => array(MRC_ACADEMIC => 10, MRC_FORTIFIER => 5),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),

    MRC_NAVIGATOR => array(
      'name' => 'rpg_general',
      'require' => array(MRC_ADMIRAL => 15, MRC_DEFENDER => 1),
      'dark_matter' => 3,
      'factor' => 1,
      'max' => 10,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_ASSASIN => array(
      'name' => 'rpg_raideur',
      'require' => array(MRC_ADMIRAL => 20, MRC_ARCHITECT => 5, MRC_NAVIGATOR => 10),
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
      // Missions
      'missions' => array(
        MT_ATTACK => array(
          'src_planet' => 1,
          'src_user'   => 1,
          'dst_planet' => 1,
          'dst_user'   => 1,
        ),

        MT_AKS => array(
          'src_planet' => 1,
          'src_user'   => 1,
          'dst_planet' => 1,
          'dst_user'   => 1,
        ),

        MT_TRANSPORT => array(
          'src_planet' => 1,
          'src_user'   => 0,
          'dst_planet' => 1,
          'dst_user'   => 0,
        ),

        MT_RELOCATE => array(
          'src_planet' => 0,
          'src_user'   => 0,
          'dst_planet' => 1,
          'dst_user'   => 0,
        ),

        MT_HOLD => array(
          'src_planet' => 0,
          'src_user'   => 0,
          'dst_planet' => 0,
          'dst_user'   => 0,
        ),

        MT_SPY => array(
          'src_planet' => 1,
          'src_user'   => 1,
          'dst_planet' => 1,
          'dst_user'   => 1,
        ),

        MT_COLONIZE => array(
          'src_planet' => 0,
          'src_user'   => 1,
          'dst_planet' => 1,
          'dst_user'   => 0,
        ),

        MT_RECYCLE => array(
          'src_planet' => 0,
          'src_user'   => 0,
          'dst_planet' => 1,
          'dst_user'   => 0,
        ),

        MT_DESTROY => array(
          'src_planet' => 1,
          'src_user'   => 1,
          'dst_planet' => 1,
          'dst_user'   => 1,
        ),

        MT_MISSILE => array(
          'src_planet' => 0,
          'src_user'   => 0,
          'dst_planet' => 0,
          'dst_user'   => 0,
        ),

        MT_EXPLORE => array(
          'src_planet' => 0,
          'src_user'   => 1,
          'dst_planet' => 0,
          'dst_user'   => 0,
        ),
      ),

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
        MRC_GEOLOGIST, MRC_POWERMAN, MRC_STOCKMAN, MRC_ARCHITECT, MRC_SPY, MRC_COORDINATOR, MRC_DESTRUCTOR,
        MRC_ADMIRAL, MRC_CONSTRUCTOR, MRC_ACADEMIC,
        MRC_FORTIFIER, MRC_DEFENDER, MRC_NAVIGATOR,
        MRC_ASSASIN //, MRC_EMPEROR
      ),
      'governors' => array(
        MRC_GEOLOGIST, MRC_POWERMAN, MRC_CONSTRUCTOR, MRC_ARCHITECT, MRC_ACADEMIC, MRC_FORTIFIER
      ),

      // Spaceships list
      'fleet'     => array ( SHIP_CARGO_SMALL, SHIP_CARGO_BIG, SHIP_CARGO_SUPER, SHIP_FIGHTER_LIGHT, SHIP_FIGHTER_HEAVY, SHIP_DESTROYER, SHIP_CRUISER, SHIP_COLONIZER, SHIP_RECYCLER, SHIP_SPY, SHIP_BOMBER, SHIP_SATTELITE_SOLAR, SHIP_DESTRUCTOR, SHIP_DEATH_STAR, SHIP_BATTLESHIP, SHIP_SUPERNOVA ),
      // Defensive building list
      'defense'   => array ( 401, 402, 403, 404, 405, 406, 407, 408, 409, 502, 503 ),

      // Combat units list
      'combat'    => array ( SHIP_CARGO_SMALL, SHIP_CARGO_BIG, SHIP_CARGO_SUPER, SHIP_FIGHTER_LIGHT, SHIP_FIGHTER_HEAVY, SHIP_DESTROYER, SHIP_CRUISER, SHIP_COLONIZER, SHIP_RECYCLER, SHIP_SPY, SHIP_BOMBER, SHIP_SATTELITE_SOLAR, SHIP_DESTRUCTOR, SHIP_DEATH_STAR, SHIP_BATTLESHIP, SHIP_SUPERNOVA, 401, 402, 403, 404, 405, 406, 407, 408, 409 ),
      // Planet active defense list
      'defense_active' => array ( 401, 402, 403, 404, 405, 406, 407, 408, 409 ),
      // Transports
      'flt_transports' => array ( SHIP_CARGO_SMALL, SHIP_CARGO_BIG, SHIP_CARGO_SUPER ),

      // List of units that can produce resources
      'prod'      => array ( 1, 2, 3, 4, 12, SHIP_SATTELITE_SOLAR ),

      // Resource list
      'resources' => array ( 0 => 'metal', 1 => 'crystal', 2 => 'deuterium', 3 => 'dark_matter'),
      // Resources can be produced on planet
      'resources_planet' => array (RES_METAL, RES_CRYSTAL, RES_DEUTERIUM, RES_ENERGY),
      // Resources can be looted from planet
      'resources_loot' => array (RES_METAL, RES_CRYSTAL, RES_DEUTERIUM),
      // Resources that can be tradeable in market trader
      'resources_trader' => array(RES_METAL, RES_CRYSTAL, RES_DEUTERIUM, RES_DARK_MATTER),

//      // Ques list
//      'ques' => array(QUE_STRUCTURES, QUE_HANGAR, QUE_RESEARCH),
    ),
  );

  //All resources
  $sn_data['groups']['all'] = array_merge($sn_data['groups']['structures'], $sn_data['groups']['tech'], $sn_data['groups']['fleet'], $sn_data['groups']['defense'], $sn_data['groups']['mercenaries']);

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
      'que' => QUE_RESEARCH,
      'mercenary' => MRC_ACADEMIC,
      'unit_list' => $sn_data['groups']['tech'],
    ),
  );

  $sn_groups = &$sn_data['groups'];
  $reslist   = &$sn_groups;

  $user_options = array();

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
