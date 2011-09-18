<?php

/**
 * vars.php
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

if (!defined('INSIDE'))
{
  die('Hack attempt!');
}

  $tableList = array( 'aks', 'alliance', 'alliance_requests', 'announce', 'annonce', 'banned', 'buddy', 'chat', 'config', 'counter',
    'errors', 'fleets', 'fleet_log', 'galaxy', 'iraks', 'logs', 'log_dark_matter', 'messages', 'notes', 'planets', 'quest',
    'quest_status', 'referrals', 'rw', 'statpoints', 'users'
  );

  $sn_message_class_list = array(
     MSG_TYPE_NEW => array(
       'name' => 'new_message',
       'switchable' => false,
       'email' => false,
     ),
     MSG_TYPE_ADMIN => array(
       'name' => 'msg_admin',
       'switchable' => false,
       'email' => true,
     ),
     MSG_TYPE_PLAYER => array(
       'name' => 'mnl_joueur',
       'switchable' => false,
       'email' => true,
     ),
     MSG_TYPE_ALLIANCE => array(
       'name' => 'mnl_alliance',
       'switchable' => false,
       'email' => true,
     ),
     MSG_TYPE_SPY => array(
       'name' => 'mnl_spy',
       'switchable' => true,
       'email' => true,
     ),
     MSG_TYPE_COMBAT => array(
       'name' => 'mnl_attaque',
       'switchable' => true,
       'email' => true,
     ),
     MSG_TYPE_TRANSPORT => array(
       'name' => 'mnl_transport',
       'switchable' => true,
       'email' => true,
     ),
     MSG_TYPE_RECYCLE => array(
       'name' => 'mnl_exploit',
       'switchable' => true,
       'email' => true,
     ),
     MSG_TYPE_EXPLORE => array(
       'name' => 'mnl_expedition',
       'switchable' => true,
       'email' => true,
     ),
     //     97 => 'mnl_general',
     MSG_TYPE_QUE => array(
       'name' => 'mnl_buildlist',
       'switchable' => true,
       'email' => true,
     ),
     MSG_TYPE_OUTBOX => array(
       'name' => 'mnl_outbox',
       'switchable' => false,
       'email' => false,
     ),
  );

  $sn_message_groups = array(
    'switchable' => array(MSG_TYPE_SPY, MSG_TYPE_COMBAT, MSG_TYPE_RECYCLE, MSG_TYPE_TRANSPORT, MSG_TYPE_EXPLORE, MSG_TYPE_QUE),
    'email' => array(MSG_TYPE_SPY, MSG_TYPE_PLAYER, MSG_TYPE_ALLIANCE, MSG_TYPE_COMBAT, MSG_TYPE_RECYCLE, MSG_TYPE_TRANSPORT,
      MSG_TYPE_ADMIN, MSG_TYPE_EXPLORE, MSG_TYPE_QUE),
  );

  // Default user option list as 'option_name' => 'option_list'
  $user_option_list = array();

  $user_option_list[OPT_MESSAGE] = array();
  foreach($sn_message_class_list as $message_class_id => $message_class_data)
  {
    if($message_class_data['switchable'])
    {
      $user_option_list[OPT_MESSAGE]["opt_{$message_class_data['name']}"] = 1;
    }

    if($message_class_data['email'])
    {
      $user_option_list[OPT_MESSAGE]["opt_email_{$message_class_data['name']}"] = 0;
    }
  }

/*
  foreach($sn_message_groups['switchable'] as $option_id)
  {
    $user_option_list[OPT_MESSAGE]["opt_{$sn_message_class_list[$option_id]['name']}"] = 1;
  }
*/
  $sn_diplomacy_relation_list = array(
    ALLY_DIPLOMACY_NEUTRAL       => array(
      'relation_id' => ALLY_DIPLOMACY_NEUTRAL,
      'enter_delay' => 0,
      'exit_delay'  => 0,
    ),
    ALLY_DIPLOMACY_WAR           => array(
      'relation_id' => ALLY_DIPLOMACY_WAR,
      'enter_delay' => $config->fleet_bashing_war_delay,
      'exit_delay'  => -1,
    ),
    ALLY_DIPLOMACY_PEACE         => array(
      'relation_id' => ALLY_DIPLOMACY_PEACE,
      'enter_delay' => -1,
      'exit_delay'  => 0,
    ),
    /*
    ALLY_DIPLOMACY_CONFEDERATION => array(
      'relation_id' => ALLY_DIPLOMACY_CONFEDERATION,
      'enter_delay' => -1,
      'exit_delay'  => $config->fleet_bashing_war_delay,
    ),
    ALLY_DIPLOMACY_FEDERATION    => array(
      'relation_id' => ALLY_DIPLOMACY_FEDERATION,
      'enter_delay' => -1,
      'exit_delay'  => $config->fleet_bashing_war_delay,
    ),
    ALLY_DIPLOMACY_UNION         => array(
      'relation_id' => ALLY_DIPLOMACY_UNION,
      'enter_delay' => -1,
      'exit_delay'  => $config->fleet_bashing_war_delay,
    ),
    ALLY_DIPLOMACY_MASTER        => array(
      'relation_id' => ALLY_DIPLOMACY_MASTER,
      'enter_delay' => -1,
      'exit_delay'  => 0,
    ),
    ALLY_DIPLOMACY_SLAVE         => array(
      'relation_id' => ALLY_DIPLOMACY_SLAVE,
      'enter_delay' => -1,
      'exit_delay'  => $config->fleet_bashing_war_delay,
    )
    */
  );

  // factor -> price_factor, perhour_factor
  $sn_data = array(
    STRUC_MINE_METAL => array(
      'name' => 'metal_mine',
      'location' => LOC_PLANET,
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

    STRUC_MINE_CRYSTAL => array(
      'name' => 'crystal_mine',
      'location' => LOC_PLANET,
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

    STRUC_MINE_DEUTERIUM => array(
      'name' => 'deuterium_sintetizer',
      'location' => LOC_PLANET,
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

    STRUC_MINE_SOLAR => array(
      'name' => 'solar_plant',
      'location' => LOC_PLANET,
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
        RES_ENERGY    => create_function ('$level, $production_factor, $temperature', 'return  ($temperature / 5 + 15) * $level * pow(1.1, $level) * (0.1 * $production_factor);'),
      ),
      'metal_perhour'     => 'return 0;',
      'crystal_perhour'   => 'return 0;',
      'deuterium_perhour' => 'return 0;',
      'energy_perhour'    => 'return   (($BuildTemp / 5 + 15) * $BuildLevel * pow(1.1, $BuildLevel)) * (0.1 * $BuildLevelFactor);',
      'type' => UNIT_STRUCTURE,
    ),

    12  => array(
      'name' => 'fusion_plant',
      'location' => LOC_PLANET,
      'require' => array(3 => 5, TECH_ENERGY => 3, MRC_TECHNOLOGIST => 5),
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

    STRUC_FACTORY_ROBOT => array(
      'name' => 'robot_factory',
      'location' => LOC_PLANET,
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

    STRUC_FACTORY_NANO => array(
      'name' => 'nano_factory',
      'location' => LOC_PLANET,
      'require' => array(14 => 10, TECH_COMPUTER => 10),
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
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
      'require' => array(15 => 1, TECH_ENERGY => 12),
      'cost' => array(
        RES_METAL     => 0,
        RES_CRYSTAL   => 50000,
        RES_DEUTERIUM => 100000,
        RES_ENERGY    => 0,
      ),
      'metal' => 0,
      'crystal' => 50000,
      'deuterium' => 100000,
      'energy' => 0,
      'factor' => 2,
      'type' => UNIT_STRUCTURE,
    ),

    34  => array(
      'name' => 'ally_deposit',
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
      'require' => array(41 => 1, TECH_HYPERSPACE => 7),
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
      'location' => LOC_PLANET,
      'require' => array(TECH_ENIGNE_ION => 1),
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

    TECH_COMPUTER => array(
      'name' => 'computer_tech',
      'location' => LOC_USER,
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

    TECH_SPY => array(
      'name' => 'spy_tech',
      'location' => LOC_USER,
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

    TECH_WEAPON => array(
      'name' => 'military_tech',
      'location' => LOC_USER,
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

    TECH_SHIELD => array(
      'name' => 'shield_tech',
      'location' => LOC_USER,
      'require' => array(31 => 6, TECH_ENERGY => 3),
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

    TECH_ARMOR => array(
      'name' => 'defence_tech',
      'location' => LOC_USER,
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
      'location' => LOC_USER,
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

    TECH_HYPERSPACE => array(
      'name' => 'hyperspace_tech',
      'location' => LOC_USER,
      'require' => array(31 => 7, TECH_ENERGY => 10, TECH_SHIELD => 5),
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

    TECH_ENGINE_CHEMICAL => array(
      'name' => 'combustion_tech',
      'location' => LOC_USER,
      'require' => array(31 => 1, TECH_ENERGY => 1),
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

    TECH_ENIGNE_ION => array(
      'name' => 'impulse_motor_tech',
      'location' => LOC_USER,
      'require' => array(31 => 4, TECH_ION => 1),
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

    TECH_LASER => array(
      'name' => 'laser_tech',
      'location' => LOC_USER,
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

    TECH_ION => array(
      'name' => 'ionic_tech',
      'location' => LOC_USER,
      'require' => array(31 => 3, TECH_ENERGY => 4, TECH_LASER => 5),
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

    TECH_PLASMA => array(
      'name' => 'buster_tech',
      'location' => LOC_USER,
      'require' => array(31 => 5, TECH_ENERGY => 8, TECH_LASER => 10, TECH_ION => 5),
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

    TECH_ENGINE_HYPER => array(
      'name' => 'hyperspace_motor_tech',
      'location' => LOC_USER,
      'require' => array(31 => 8, TECH_HYPERSPACE => 3),
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

    TECH_RESEARCH => array(
      'name' => 'intergalactic_tech',
      'location' => LOC_USER,
      'require' => array(31 => 10, TECH_COMPUTER => 8, TECH_HYPERSPACE => 8),
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

    TECH_EXPEDITION => array(
      'name' => 'expedition_tech',
      'location' => LOC_USER,
      'require' => array(31 => 3, TECH_COMPUTER => 4, TECH_ENIGNE_ION => 3),
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

    TECH_COLONIZATION => array(
      'name' => 'colonisation_tech',
      'location' => LOC_USER,
      'require' => array(31 => 3, TECH_ENERGY => 5, TECH_ARMOR => 2),
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

    TECH_ASTROTECH => array(
      'name' => 'tech_astro',
      'location' => LOC_USER,
      'require' => array(31 => 3, TECH_ENERGY => 5, TECH_ARMOR => 2),
      'cost' => array(
        RES_METAL     => 4000,
        RES_CRYSTAL   => 7000,
        RES_DEUTERIUM => 4000,
        RES_ENERGY    => 0,
      ),
      'metal' => 4000,
      'crystal' => 7000,
      'deuterium' => 4000,
      'energy' => 0,
      'factor' => 1.9,
    ),

    TECH_GRAVITON => array(
      'name' => 'graviton_tech',
      'location' => LOC_USER,
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
      'location' => LOC_PLANET,
      'require' => array(21 => 2, TECH_ENGINE_CHEMICAL => 2),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_CHEMICAL,
          'speed' => 5000,
          'consumption' => 20,
          'min_level' => 2,
        ), 
        array(
          'tech' => TECH_ENIGNE_ION,
          'speed' => 10000,
          'consumption' => 40,
          'min_level' => 5,
        ), 
      ),
      'tech' => TECH_ENGINE_CHEMICAL,
      'speed' => 5000,
      'consumption' => 20,
      'tech_level' => 5,
      'tech2' => TECH_ENIGNE_ION,
      'speed2' => 10000,
      'consumption2' => 40,
      'shield' => 10,
      'attack' => 5,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 100, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 250, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 400,
      'stackable' => true,
    ),

    SHIP_CARGO_BIG => array(
      'name' => 'big_ship_cargo',
      'location' => LOC_PLANET,
      'require' => array(21 => 4, TECH_ENGINE_CHEMICAL => 6),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_CHEMICAL,
          'speed' => 7500,
          'consumption' => 50,
          'min_level' => 6,
        ), 
      ), 
      'tech' => TECH_ENGINE_CHEMICAL,
      'speed' => 7500,
      'consumption' => 50,
      'shield' => 25,
      'attack' => 5,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 100, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 250, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1200,
      'stackable' => true,
    ),

    SHIP_CARGO_SUPER => array(
      'name' => 'supercargo',
      'location' => LOC_PLANET,
      'require' => array(21 => 8, TECH_ENIGNE_ION => 5, MRC_STOCKMAN => 1),
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
      'engine' => array(
        array(
          'tech' => TECH_ENIGNE_ION,
          'speed' => 5000,
          'consumption' => 100,
          'min_level' => 5,
        ), 
      ), 
      'tech' => TECH_ENIGNE_ION,
      'speed' => 5000,
      'consumption' => 100,
      'shield' => 50,
      'attack' => 10,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 100, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 250, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 3000,
      'stackable' => true,
    ),

    SHIP_CARGO_HYPER => array(
      'name' => 'planet_cargo_hyper',
      'location' => LOC_PLANET,
      'require' => array(21 => 10, TECH_ENGINE_HYPER => 5, MRC_STOCKMAN => 10),
      'cost' => array(
        RES_METAL     => 500000,
        RES_CRYSTAL   => 200000,
        RES_DEUTERIUM => 100000,
        RES_ENERGY    => 0,
      ),
      'metal' =>     500000,
      'crystal' =>   200000,
      'deuterium' => 100000,
      'energy' => 0,
      'factor' => 1,
      'capacity' => 1000000,
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_HYPER,
          'speed' => 2000,
          'consumption' => 1000,
          'min_level' => 5,
        ), 
      ), 
      'tech' => TECH_ENGINE_HYPER,
      'speed' => 2000,
      'consumption' => 1000,
      'shield' => 200,
      'attack' => 50,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 100, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 250, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 70000,
      'stackable' => true,
    ),

    SHIP_FIGHTER_LIGHT => array(
      'name' => 'light_hunter',
      'location' => LOC_PLANET,
      'require' => array(21 => 1, TECH_ENGINE_CHEMICAL => 1),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_CHEMICAL,
          'speed' => 12500,
          'consumption' => 20,
          'min_level' => 1,
        ), 
      ), 
      'tech' => TECH_ENGINE_CHEMICAL,
      'speed' => 12500,
      'consumption' => 20,
      'shield' => 10,
      'attack' => 50,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 2, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 16.4, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 10.001, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 21, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 400,
      'stackable' => true,
    ),

    SHIP_FIGHTER_HEAVY => array(
      'name' => 'heavy_hunter',
      'location' => LOC_PLANET,
      'require' => array(21 => 3, TECH_ARMOR => 2, TECH_ENIGNE_ION => 2),
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
      'engine' => array(
        array(
          'tech' => TECH_ENIGNE_ION,
          'speed' => 10000,
          'consumption' => 75,
          'min_level' => 2,
        ), 
      ), 
      'tech' => TECH_ENIGNE_ION,
      'speed' => 10000,
      'consumption' => 75,
      'shield' => 25,
      'attack' => 150,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 3, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 8.2, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 3.33367, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 7, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1000,
      'stackable' => true,
    ),

    SHIP_DESTROYER => array(
      'name' => 'crusher',
      'location' => LOC_PLANET,
      'require' => array(21 => 5, TECH_ENIGNE_ION => 4, TECH_ION => 2),
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
      'engine' => array(
        array(
          'tech' => TECH_ENIGNE_ION,
          'speed' => 15000,
          'consumption' => 300,
          'min_level' => 4,
        ), 
      ), 
      'tech' => TECH_ENIGNE_ION,
      'speed' => 15000,
      'consumption' => 300,
      'shield' => 50,
      'attack' => 400,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 6, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 10, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 6.15, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 1.25013, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 2.625, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 5.5, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 2700,
      'stackable' => true,
    ),

    SHIP_CRUISER => array(
      'name' => 'battle_ship',
      'location' => LOC_PLANET,
      'require' => array(21 => 7, TECH_ENGINE_HYPER => 4),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_HYPER,
          'speed' => 10000,
          'consumption' => 500,
          'min_level' => 4,
        ), 
      ), 
      'tech' => TECH_ENGINE_HYPER,
      'speed' => 10000,
      'consumption' => 500,
      'shield' => 200,
      'attack' => 1000,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 8, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.50005, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1.05, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1.76, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 6000,
      'stackable' => true,
    ),

    SHIP_COLONIZER => array(
      'name' => 'colonizer',
      'location' => LOC_PLANET,
      'require' => array(21 => 4, TECH_ENIGNE_ION => 3, TECH_COLONIZATION => 2),
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
      'engine' => array(
        array(
          'tech' => TECH_ENIGNE_ION,
          'speed' => 2500,
          'consumption' => 1000,
          'min_level' => 3,
        ), 
      ), 
      'tech' => TECH_ENIGNE_ION,
      'speed' => 2500,
      'consumption' => 1000,
      'shield' => 100,
      'attack' => 50,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 10.001, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 21, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 3000,
      'stackable' => true,
    ),

    SHIP_RECYCLER => array(
      'name' => 'recycler',
      'location' => LOC_PLANET,
      'require' => array(21 => 4, TECH_ENGINE_CHEMICAL => 6, TECH_SHIELD => 2),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_CHEMICAL,
          'speed' => 2000,
          'consumption' => 300,
          'min_level' => 6,
        ), 
      ), 
      'tech' => TECH_ENGINE_CHEMICAL,
      'speed' => 2000,
      'consumption' => 300,
      'shield' => 10,
      'attack' => 1,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 500.05, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1050, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 1600,
      'stackable' => true,
    ),

    SHIP_SPY => array(
      'name' => 'spy_sonde',
      'location' => LOC_PLANET,
      'require' => array(21 => 3, TECH_ENGINE_CHEMICAL => 3, TECH_SPY => 2),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_CHEMICAL,
          'speed' => 100000000,
          'consumption' => 1,
          'min_level' => 3,
        ), 
      ), 
      'tech' => TECH_ENGINE_CHEMICAL,
      'speed' => 100000000,
      'consumption' => 1,
      'shield' => 0.01,
      'attack' => 0.01,
      'sd' => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SUPER => 0, SHIP_CARGO_SMALL => 0, SHIP_CARGO_BIG => 0, SHIP_FIGHTER_LIGHT => 0, SHIP_FIGHTER_HEAVY => 0, SHIP_DESTROYER => 0, SHIP_CRUISER => 0, SHIP_COLONIZER => 0, SHIP_RECYCLER => 0, SHIP_SPY => 0, SHIP_BOMBER => 0, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 0, SHIP_DEATH_STAR => 0, SHIP_BATTLESHIP => 0, SHIP_SUPERNOVA => 0, 401 => 0, 402 => 0, 403 => 0, 404 => 0, 405 => 0, 406 => 0, 407 => 0, 408 => 0, 409 => 0),
      'amplify' => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 1, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 100,
      'stackable' => true,
    ),

    SHIP_BOMBER => array(
      'name' => 'bomber_ship',
      'location' => LOC_PLANET,
      'require' => array(TECH_ENIGNE_ION => 6, 21 => 8, TECH_PLASMA => 5),
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
      'engine' => array(
        array(
          'tech' => TECH_ENIGNE_ION,
          'speed' => 4000,
          'consumption' => 1000,
          'min_level' => 6,
        ), 
        array(
          'tech' => TECH_ENGINE_HYPER,
          'speed' => 5000,
          'consumption' => 1250,
          'min_level' => 8,
        ), 
      ), 
      'tech' => TECH_ENIGNE_ION,
      'speed' => 4000,
      'consumption' => 1000,
      'tech_level' => 8,
      'tech2' => TECH_ENGINE_HYPER,
      'speed2' => 5000,
      'consumption2' => 1250,
      'shield' => 500,
      'attack' => 1000,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 20, 402 => 20, 403 => 10, 404 => 1, 405 => 10, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.50005, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1.05, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 4.4, 402 => 4.5, 403 => 9, 404 => 1, 405 => 13, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 7500,
      'stackable' => true,
    ),

    SHIP_SATTELITE_SOLAR => array(
      'name' => 'solar_satelit',
      'location' => LOC_PLANET,
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_CHEMICAL,
          'speed' => 0,
          'consumption' => 0,
          'min_level' => 0,
        ), 
      ), 
      'tech' => TECH_ENGINE_CHEMICAL,
      'speed' => 0,
      'consumption' => 0,
      'shield' => 10,
      'attack' => 1,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 1, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 0, 402 => 0, 403 => 0, 404 => 0, 405 => 0, 406 => 0, 407 => 0, 408 => 0, 409 => 0),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 1, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
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
      'location' => LOC_PLANET,
      'require' => array(21 => 9, TECH_HYPERSPACE => 5, TECH_ENGINE_HYPER => 6),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_HYPER,
          'speed' => 5000,
          'consumption' => 1000,
          'min_level' => 6,
        ), 
      ), 
      'tech' => TECH_ENGINE_HYPER,
      'speed' => 5000,
      'consumption' => 1000,
      'shield' => 500,
      'attack' => 2000,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 2, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 10, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.25003, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0.525, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 7.4, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1.125, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'armor' => 11000,
      'stackable' => true,
    ),

    SHIP_DEATH_STAR => array(
      'name' => 'dearth_star',
      'location' => LOC_PLANET,
      'require' => array(21 => 12, TECH_HYPERSPACE => 6, TECH_ENGINE_HYPER => 7, TECH_GRAVITON => 1, MRC_DESTRUCTOR => 1),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_HYPER,
          'speed' => 100,
          'consumption' => 1,
          'min_level' => 7,
        ), 
      ), 
      'tech' => TECH_ENGINE_HYPER,
      'speed' => 100,
      'consumption' => 1,
      'shield' => 50000,
      'attack' => 200000,
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 100, SHIP_CARGO_SMALL => 200, SHIP_CARGO_BIG => 150, SHIP_FIGHTER_LIGHT => 200, SHIP_FIGHTER_HEAVY => 100, SHIP_DESTROYER => 33, SHIP_CRUISER => 30, SHIP_COLONIZER => 250, SHIP_RECYCLER => 250, SHIP_SPY => 1250, SHIP_BOMBER => 25, SHIP_SATTELITE_SOLAR => 1250, SHIP_DESTRUCTOR => 5, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 15, SHIP_SUPERNOVA => 1, 401 => 200, 402 => 200, 403 => 100, 404 => 50, 405 => 100, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SUPER => 2.025, SHIP_CARGO_SMALL => 0.41, SHIP_CARGO_BIG => 0.91875, SHIP_FIGHTER_LIGHT => 0.41, SHIP_FIGHTER_HEAVY => 0.5125, SHIP_DESTROYER => 0.45375, SHIP_CRUISER => 0.93, SHIP_COLONIZER => 3.875, SHIP_RECYCLER => 2.0125, SHIP_SPY => 0.62506, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1.3125, SHIP_DESTRUCTOR => 0.2875, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 0.03, SHIP_SUPERNOVA => 1, 401 => 0.02, 402 => 0.025, 403 => 0.05, 404 => 0.05, 405 => 0.25, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 900000,
      'stackable' => true,
    ),

    SHIP_BATTLESHIP => array(
      'name' => 'battleship',
      'location' => LOC_PLANET,
      'require' => array(21 => 8, TECH_HYPERSPACE => 5, TECH_ENGINE_HYPER => 5, TECH_LASER => 12),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_HYPER,
          'speed' => 10000,
          'consumption' => 250,
          'min_level' => 5,
        ), 
      ), 
      'tech' => TECH_ENGINE_HYPER,
      'speed' => 10000,
      'consumption' => 250,
      'shield' => 400,
      'attack' => 700,
      'sd' => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SMALL => 5, SHIP_CARGO_BIG => 3, SHIP_CARGO_SUPER => 2, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 4, SHIP_DESTROYER => 4, SHIP_CRUISER => 7, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SUPER => 11.57143, SHIP_CARGO_SMALL => 2.92857, SHIP_CARGO_BIG => 5.25, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 5.85714, SHIP_DESTROYER => 15.71429, SHIP_CRUISER => 62, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.71436, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1.5, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1, 401 => 1, 402 => 1, 403 => 1, 404 => 1, 405 => 1, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 7000,
      'stackable' => true,
    ),

    SHIP_SUPERNOVA => array(
      'name' => 'supernova',
      'location' => LOC_PLANET,
      'require' => array(21 => 15, TECH_HYPERSPACE => 7, TECH_ENGINE_HYPER => 9, TECH_GRAVITON => 1, MRC_ASSASIN => 1),
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
      'engine' => array(
        array(
          'tech' => TECH_ENGINE_HYPER,
          'speed' => 150,
          'consumption' => 250,
          'min_level' => 9,
        ), 
      ), 
      'tech' => TECH_ENGINE_HYPER,
      'speed' => 150,
      'consumption' => 250,
      'shield' => 1000000,
      'attack' => 1000000,
      'sd' => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SUPER => 150, SHIP_CARGO_SMALL => 250, SHIP_CARGO_BIG => 200, SHIP_FIGHTER_LIGHT => 200, SHIP_FIGHTER_HEAVY => 100, SHIP_DESTROYER => 33, SHIP_CRUISER => 30, SHIP_COLONIZER => 250, SHIP_RECYCLER => 250, SHIP_SPY => 1250, SHIP_BOMBER => 25, SHIP_SATTELITE_SOLAR => 1250, SHIP_DESTRUCTOR => 5, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 15, SHIP_SUPERNOVA => 1, 401 => 200, 402 => 200, 403 => 100, 404 => 50, 405 => 100, 406 => 1, 407 => 1, 408 => 1, 409 => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SUPER => 0.6075, SHIP_CARGO_SMALL => 0.1025, SHIP_CARGO_BIG => 0.245, SHIP_FIGHTER_LIGHT => 0.082, SHIP_FIGHTER_HEAVY => 0.1025, SHIP_DESTROYER => 0.09075, SHIP_CRUISER => 0.186, SHIP_COLONIZER => 0.775, SHIP_RECYCLER => 0.4025, SHIP_SPY => 0.12501, SHIP_BOMBER => 0.2, SHIP_SATTELITE_SOLAR => 0.2625, SHIP_DESTRUCTOR => 0.0575, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 0.111, SHIP_SUPERNOVA => 1, 401 => 0.004, 402 => 0.005, 403 => 0.01, 404 => 0.01, 405 => 0.05, 406 => 1, 407 => 1, 408 => 1, 409 => 1 ),
      'armor' => 3500000,
      'stackable' => true,
    ),

    SHIP_FIGHTER_ASSAULT => array(
      'name' => 'assault_ship',
      'location' => LOC_PLANET,
    ),

    401 => array(
      'name' => 'misil_launcher',
      'location' => LOC_PLANET,
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
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 6.25063, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'armor' => 200,
      'stackable' => true,
    ),

    402 => array(
      'name' => 'small_laser',
      'location' => LOC_PLANET,
      'require' => array(TECH_ENERGY => 1, 21 => 2, TECH_LASER => 3),
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
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5.0005, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'armor' => 200,
      'stackable' => true,
    ),

    403 => array(
      'name' => 'big_laser',
      'location' => LOC_PLANET,
      'require' => array(TECH_ENERGY => 3, 21 => 4, TECH_LASER => 6),
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
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 2.0002, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'armor' => 800,
      'stackable' => true,
    ),

    404 => array(
      'name' => 'gauss_canyon',
      'location' => LOC_PLANET,
      'require' => array(21 => 6, TECH_ENERGY => 6, TECH_WEAPON => 3, TECH_SHIELD => 1),
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
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.45459, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'armor' => 3500,
      'stackable' => true,
    ),

    405 => array(
      'name' => 'ionic_canyon',
      'location' => LOC_PLANET,
      'require' => array(21 => 4, TECH_ION => 4),
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
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 3.33367, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'armor' => 800,
      'stackable' => true,
    ),

    406 => array(
      'name' => 'buster_canyon',
      'location' => LOC_PLANET,
      'require' => array(21 => 8, TECH_PLASMA => 7),
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
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 0.16668, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'armor' => 10000,
      'stackable' => true,
    ),

    407 => array(
      'name' => 'small_protection_shield',
      'location' => LOC_PLANET,
      'require' => array(TECH_SHIELD => 2, 21 => 1),
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
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 500.05, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'armor' => 2000,
      'stackable' => true,
    ),

    408 => array(
      'name' => 'big_protection_shield',
      'location' => LOC_PLANET,
      'require' => array(TECH_SHIELD => 6, 21 => 6),
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
      'sd' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 5, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 0, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'amplify' => array(SHIP_CARGO_HYPER => 1, SHIP_CARGO_SUPER => 1, SHIP_CARGO_SMALL => 1, SHIP_CARGO_BIG => 1, SHIP_FIGHTER_LIGHT => 1, SHIP_FIGHTER_HEAVY => 1, SHIP_DESTROYER => 1, SHIP_CRUISER => 1, SHIP_COLONIZER => 1, SHIP_RECYCLER => 1, SHIP_SPY => 500.05, SHIP_BOMBER => 1, SHIP_SATTELITE_SOLAR => 1, SHIP_DESTRUCTOR => 1, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 1, SHIP_SUPERNOVA => 1),
      'armor' => 10000,
      'stackable' => true,
    ),

    409 => array(
      'name'      => 'planet_protector',
      'location' => LOC_PLANET,
      'require'   => array(MRC_FORTIFIER => 3),
      'cost' => array(
        RES_METAL     => 10000000,
        RES_CRYSTAL   => 5000000,
        RES_DEUTERIUM => 2500000,
        RES_ENERGY    => 0,
      ),
      'metal'     => 10000000,
      'crystal'   => 5000000,
      'deuterium' => 2500000,
      'energy'    => 0,
      'factor'    => 1,
      'shield'    => 1000000,
      'attack'    => 1000000,
      'sd'        => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SUPER => 50, SHIP_CARGO_SMALL => 100, SHIP_CARGO_BIG => 80, SHIP_FIGHTER_LIGHT => 75, SHIP_FIGHTER_HEAVY => 60, SHIP_DESTROYER => 20, SHIP_CRUISER => 20, SHIP_COLONIZER => 100, SHIP_RECYCLER => 100, SHIP_SPY => 500, SHIP_BOMBER => 10, SHIP_SATTELITE_SOLAR => 500, SHIP_DESTRUCTOR => 2, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 5, SHIP_SUPERNOVA => 1),
      'amplify'   => array(SHIP_CARGO_HYPER => 1,  SHIP_CARGO_SUPER => 0.2025, SHIP_CARGO_SMALL => 0.041, SHIP_CARGO_BIG => 0.098, SHIP_FIGHTER_LIGHT => 0.03075, SHIP_FIGHTER_HEAVY => 0.0615, SHIP_DESTROYER => 0.055, SHIP_CRUISER => 0.124, SHIP_COLONIZER => 0.31, SHIP_RECYCLER => 0.161, SHIP_SPY => 0.05001, SHIP_BOMBER => 0.08, SHIP_SATTELITE_SOLAR => 0.105, SHIP_DESTRUCTOR => 0.023, SHIP_DEATH_STAR => 1, SHIP_BATTLESHIP => 0.037, SHIP_SUPERNOVA => 1 ),
      'armor'     => 1500000,
      'stackable' => true,
    ),

    502 => array(
      'name' => 'interceptor_misil',
      'location' => LOC_PLANET,
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
      'location' => LOC_PLANET,
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


    MRC_TECHNOLOGIST => array(
      'name' => 'rpg_geologue',
      'location' => LOC_PLANET,
      'cost' => array(
        RES_DARK_MATTER => 800,
        'factor' => 1.06,
      ),
//      'dark_matter' => 3000,
//      'factor' => 1,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_ENGINEER => array(
      'name' => 'rpg_constructeur',
      'location' => LOC_PLANET,
      'cost' => array(
        RES_DARK_MATTER => 400,
        'factor' => 1.25,
      ),
//      'dark_matter' => 3000,
//      'factor' => 1,
      'max' => 15,
      'bonus' => -5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_FORTIFIER => array(
      'name' => 'rpg_defenseur',
      'location' => LOC_PLANET,
      'cost' => array(
        RES_DARK_MATTER => 2000,
        'factor' => 1,
      ),
//      'dark_matter' => 3000,
//      'factor' => 1,
      'max' => 8,
      'bonus' => -10,
      'bonus_type' => BONUS_PERCENT,
    ),



    MRC_STOCKMAN => array(
      'name' => 'rpg_stockeur',
      'location' => LOC_USER,
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 20,
      'bonus' => 20,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_SPY => array(
      'name' => 'rpg_espion',
      'location' => LOC_USER,
      'require' => array(MRC_STOCKMAN => 5),
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 5,
      'bonus' => 1,
      'bonus_type' => BONUS_ADD,
    ),

    MRC_ACADEMIC => array(
      'name' => 'mrc_academic',
      'location' => LOC_USER,
      'require' => array(MRC_STOCKMAN => 10, MRC_SPY => 5),
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 10,
      'bonus' => -5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_DESTRUCTOR => array(
      'name' => 'rpg_destructeur',
      'location' => LOC_USER,
      'require' => array(MRC_STOCKMAN => 20, MRC_ACADEMIC => 10, MRC_NAVIGATOR => 1),
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),


    MRC_ADMIRAL => array(
      'name' => 'rpg_amiral',
      'location' => LOC_USER,
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 20,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_COORDINATOR => array(
      'name' => 'rpg_commandant',
      'location' => LOC_USER,
      'require' => array(MRC_ADMIRAL => 5),
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 5,
      'bonus' => 1,
      'bonus_type' => BONUS_ADD,
    ),

    MRC_NAVIGATOR => array(
      'name' => 'rpg_general',
      'location' => LOC_USER,
      'require' => array(MRC_ADMIRAL => 10, MRC_COORDINATOR => 5),
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 10,
      'bonus' => 5,
      'bonus_type' => BONUS_PERCENT,
    ),

    MRC_ASSASIN => array(
      'name' => 'rpg_raideur',
      'location' => LOC_USER,
      'require' => array(MRC_ADMIRAL => 20, MRC_NAVIGATOR => 10, MRC_ACADEMIC => 1),
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),
/*
    MRC_EMPEROR => array(
      'name' => 'rpg_empereur',
      'location' => LOC_USER,
      'require' => array(MRC_ASSASIN => 1, MRC_DEFENDER => 1),
      'cost' => array(
        RES_DARK_MATTER => 3000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
      'max' => 1,
      'bonus_type' => BONUS_ABILITY,
    ),
*/
    RES_METAL => array(
      'name' => 'metal',
      'location' => LOC_PLANET,
    ),

    RES_CRYSTAL => array(
      'name' => 'crystal',
      'location' => LOC_PLANET,
    ),

    RES_DEUTERIUM => array(
      'name' => 'deuterium',
      'location' => LOC_PLANET,
    ),

    RES_ENERGY => array(
      'name' => 'energy',
      'location' => LOC_PLANET,
    ),

    RES_DARK_MATTER => array(
      'name' => 'dark_matter',
      'location' => LOC_USER,
    ),

    ART_LHC => array(
      'name' => 'art_lhc',
      'location' => LOC_USER,
      'cost' => array(
        RES_DARK_MATTER => 25000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
//      'max' => 5,
      'bonus_type' => BONUS_ABILITY,
    ),

    ART_RCD_SMALL => array(
      'name' => 'art_rcd_small',
      'location' => LOC_USER,
      'cost' => array(
        RES_DARK_MATTER => 5000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
//      'max' => 5,
      'bonus_type' => BONUS_ABILITY,
      'deploy' => array(
        STRUC_MINE_METAL => 10,
        STRUC_MINE_CRYSTAL => 10,
        STRUC_MINE_DEUTERIUM => 10,
        STRUC_MINE_SOLAR => 14,
        STRUC_FACTORY_ROBOT => 4,
      ),
    ),

    ART_RCD_MEDIUM => array(
      'name' => 'art_rcd_medium',
      'location' => LOC_USER,
      'cost' => array(
        RES_DARK_MATTER => 25000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
//      'max' => 5,
      'bonus_type' => BONUS_ABILITY,
      'deploy' => array(
        STRUC_MINE_METAL => 15,
        STRUC_MINE_CRYSTAL => 15,
        STRUC_MINE_DEUTERIUM => 15,
        STRUC_MINE_SOLAR => 20,
        STRUC_FACTORY_ROBOT => 8,
      ),
    ),

    ART_RCD_LARGE => array(
      'name' => 'art_rcd_large',
      'location' => LOC_USER,
      'cost' => array(
        RES_DARK_MATTER => 60000,
        'factor' => 1,
      ),
//      'dark_matter' => 3,
//      'factor' => 1,
//      'max' => 5,
      'bonus_type' => BONUS_ABILITY,
      'deploy' => array(
        STRUC_MINE_METAL => 20,
        STRUC_MINE_CRYSTAL => 20,
        STRUC_MINE_DEUTERIUM => 20,
        STRUC_MINE_SOLAR => 25,
        STRUC_FACTORY_ROBOT => 10,
        STRUC_FACTORY_NANO => 1,
      ),
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
      'tech'      => array (
        TECH_ENERGY, TECH_COMPUTER, TECH_ARMOR, TECH_WEAPON, TECH_SHIELD, 
        TECH_ENGINE_CHEMICAL, TECH_ENIGNE_ION, TECH_ENGINE_HYPER, TECH_LASER, TECH_ION, TECH_PLASMA, TECH_HYPERSPACE,
        TECH_SPY, TECH_EXPEDITION, TECH_COLONIZATION, TECH_GRAVITON, TECH_RESEARCH),

      // Mercenary list
      'mercenaries' => array (
        MRC_STOCKMAN, MRC_SPY, MRC_ACADEMIC,
        MRC_ADMIRAL, MRC_COORDINATOR, MRC_NAVIGATOR,
        MRC_DESTRUCTOR, MRC_ASSASIN //, MRC_EMPEROR
      ),
      'governors' => array(
        MRC_TECHNOLOGIST, MRC_ENGINEER, MRC_FORTIFIER
      ),

      // Spaceships list
      'fleet'     => array(
        SHIP_FIGHTER_LIGHT, SHIP_FIGHTER_HEAVY, SHIP_DESTROYER, SHIP_CRUISER,
        SHIP_BOMBER, SHIP_BATTLESHIP, SHIP_DESTRUCTOR, SHIP_DEATH_STAR, SHIP_SUPERNOVA,
        SHIP_CARGO_SMALL, SHIP_CARGO_BIG, SHIP_CARGO_SUPER, SHIP_CARGO_HYPER, 
        SHIP_RECYCLER, SHIP_COLONIZER, SHIP_SPY, SHIP_SATTELITE_SOLAR
       ),
      // Defensive building list
      'defense'   => array ( 401, 402, 403, 404, 405, 406, 407, 408, 409, 502, 503 ),

      // Combat units list
      'combat'    => array(
        SHIP_CARGO_SMALL, SHIP_CARGO_BIG, SHIP_CARGO_SUPER, SHIP_CARGO_HYPER, SHIP_FIGHTER_LIGHT, SHIP_FIGHTER_HEAVY, SHIP_DESTROYER, SHIP_CRUISER, SHIP_COLONIZER, SHIP_RECYCLER, SHIP_SPY, SHIP_BOMBER, SHIP_SATTELITE_SOLAR, SHIP_DESTRUCTOR, SHIP_DEATH_STAR, SHIP_BATTLESHIP, SHIP_SUPERNOVA, 401, 402, 403, 404, 405, 406, 407, 408, 409 ),
      // Planet active defense list
      'defense_active' => array ( 401, 402, 403, 404, 405, 406, 407, 408, 409 ),
      // Transports
      'flt_transports' => array ( SHIP_CARGO_SMALL, SHIP_CARGO_BIG, SHIP_CARGO_SUPER, SHIP_CARGO_HYPER),

      'artifacts' => array(ART_LHC, ART_RCD_SMALL, ART_RCD_MEDIUM, ART_RCD_LARGE),

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
      // Resources that can be tradeable in market trader
      'quest_rewards' => array(RES_METAL, RES_CRYSTAL, RES_DEUTERIUM, RES_DARK_MATTER),

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
      'mercenary' => MRC_ENGINEER,
      'que' => QUE_STRUCTURES,
    ),

    QUE_HANGAR => array(
      'unit_list' => array_merge($sn_data['groups']['fleet'], $sn_data['groups']['defense']),
      'length' => 10,
      'mercenary' => MRC_ENGINEER,
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
      'mercenary' => MRC_ENGINEER,
      'unit_list' => $sn_data['groups']['build_allow'][PT_PLANET],
    ),

    SUBQUE_MOON => array(
      'que' => QUE_STRUCTURES,
      'mercenary' => MRC_ENGINEER,
      'unit_list' => $sn_data['groups']['build_allow'][PT_MOON],
    ),

    SUBQUE_FLEET => array(
      'que' => QUE_HANGAR,
      'mercenary' => MRC_ENGINEER,
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

  foreach ($sn_data as $unitID => $unitData)
  {
    $sn_data[$unitID]['armor'] = ($sn_data[$unitID]['metal'] + $sn_data[$unitID]['crystal'])/10;
/*
    foreach ($unitData['sd'] as $enemyID => $SPD)
    {
      if ($SPD>1)
      {

        // $enemyArmor = ($sn_data[$enemyID]['metal'] + $sn_data[$enemyID]['crystal'])/10;
        // $a1 = ($enemyArmor + $sn_data[$enemyID]['shield']) * $SPD / $unitData['attack'];

        $a1 = ($sn_data[$enemyID]['armor'] + $sn_data[$enemyID]['shield']) * $SPD / $unitData['attack'];
        $sn_data[$unitID]['amplify'][$enemyID] = $a1;
      }
      elseif ($SPD == 1)
      {
        $sn_data[$unitID]['amplify'][$enemyID] = 1;
      }
      elseif ($SPD < 0)
      {
        $sn_data[$unitID]['amplify'][$enemyID] = -$SPD;
      }
      elseif ($SPD == 0 || $SPD<1 || !is_numeric($SPD))
      {
        $sn_data[$unitID]['amplify'][$enemyID] = 0;
      }
    }
*/
  }

/*
  // Procedure to dump new 'amplify' values delivered from rapidfire
  foreach ($sn_data as $unitID => $unitData)
  {
    print("  $"."CombatCaps[" . $unitID . "]['amplify'] = array( ");
    foreach ($unitData['amplify'] as $enemyID => $SPD)
    {
      print($enemyID . ' => ' . round($SPD, 5) . ', ');
    }
    print(" );<br>");
  }
*/

?>
