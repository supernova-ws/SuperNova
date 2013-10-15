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

$sn_menu_extra = array();

$sn_mvc = array(
 'model' => array(),
 'view' => array(),
 'controller' => array(),
 'i18n' => array(),
);

$sn_ali_admin_internal = array(
  'rights' => array(
    'include' => 'alliance/ali_internal_admin_rights.inc',
    'title' => 'ali_adm_rights_title'
  ),
  'members' => array(
    'include' => 'alliance/ali_internal_members.inc',
    'title' => 'Members_list'
  ),
  'requests' => array(
    'include' => 'alliance/ali_internal_admin_request.inc',
    'title' => 'ali_req_check'
  ),
  'diplomacy' => array(
    'include' => 'alliance/ali_internal_admin_diplomacy.inc',
    'title' => 'ali_dip_title'
  ),
  'default' => array(
    'include' => 'alliance/ali_internal_admin.inc',
  ),
);

$sn_version_check_class = array(
  SNC_VER_NEVER => 'warning',

  SNC_VER_ERROR_CONNECT => 'error',
  SNC_VER_ERROR_SERVER => 'error',

  SNC_VER_EXACT => 'ok',
  SNC_VER_LESS => 'notice',
  SNC_VER_FUTURE => 'error',

  SNC_VER_RELEASE_EXACT => 'ok',
  SNC_VER_RELEASE_MINOR => 'notice',
  SNC_VER_RELEASE_MAJOR => 'warning',
  SNC_VER_RELEASE_ALPHA => 'ok',

  SNC_VER_MAINTENANCE => 'notice',
  SNC_VER_UNKNOWN_RESPONSE => 'warning',
  SNC_VER_INVALID => 'error',
  SNC_VER_STRANGE => 'error',

  SNC_VER_REGISTER_UNREGISTERED => 'warning',
  SNC_VER_REGISTER_ERROR_MULTISERVER => 'error',
  SNC_VER_REGISTER_ERROR_REGISTERED => 'error',
  SNC_VER_REGISTER_ERROR_NO_NAME => 'error',
  SNC_VER_REGISTER_ERROR_WRONG_URL => 'error',
  SNC_VER_REGISTER_REGISTERED => 'ok',

  SNC_VER_ERROR_INCOMPLETE_REQUEST => 'error',
  SNC_VER_ERROR_UNKNOWN_KEY => 'error',
  SNC_VER_ERROR_MISSMATCH_KEY_ID => 'error',
);

$tableList = array( 'aks', 'alliance', 'alliance_requests', 'announce', 'annonce', 'banned', 'buddy', 'chat', 'config', 'counter',
  'errors', 'fleets', 'fleet_log', 'galaxy', 'iraks', 'logs', 'log_dark_matter', 'messages', 'notes', 'planets', 'quest',
  'quest_status', 'referrals', 'rw', 'statpoints', 'users'
);

$sn_image_allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');

$ally_rights = array(
  0 => 'name',
  1 => 'mail',
  2 => 'online',
  3 => 'invite',
  4 => 'kick',
  5 => 'admin',
  6 => 'forum',
  7 => 'diplomacy'
);

$functions = array(
//    'test' => 'sn_test',
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

$user_option_list[OPT_UNIVERSE] = array(
  'opt_uni_avatar_user' => 1,
  'opt_uni_avatar_ally' => 1,
);

$user_option_list[OPT_INTERFACE] = array(
  'opt_int_struc_vertical' => 0,
  'opt_int_navbar_resource_force' => 0,
  'opt_int_overview_planet_columns' => 0,
  'opt_int_overview_planet_rows' => 5,
);

$user_option_types = array(
  'opt_int_overview_planet_columns' => 'integer',
  'opt_int_overview_planet_rows' => 'integer',
);
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
$sn_data = array();

require_once('vars_structures.php');
require_once('vars_combats.php');
require_once('vars_powerups.php');

$sn_data += array(
  RES_METAL => array(
    'name' => 'metal',
    'type' => UNIT_RESOURCES,
    'location' => LOC_PLANET,
    'bonus_type' => BONUS_ABILITY,
    'stackable' => true,
  ),
  RES_CRYSTAL => array(
    'name' => 'crystal',
    'type' => UNIT_RESOURCES,
    'location' => LOC_PLANET,
    'bonus_type' => BONUS_ABILITY,
    'stackable' => true,
  ),
  RES_DEUTERIUM => array(
    'name' => 'deuterium',
    'type' => UNIT_RESOURCES,
    'location' => LOC_PLANET,
    'bonus_type' => BONUS_ABILITY,
    'stackable' => true,
  ),
  RES_ENERGY => array(
    'name' => 'energy',
    'type' => UNIT_RESOURCES,
    'location' => LOC_PLANET,
    'bonus_type' => BONUS_ABILITY,
    'stackable' => true,
  ),
  RES_DARK_MATTER => array(
    'name' => 'dark_matter',
    'type' => UNIT_RESOURCES,
    'location' => LOC_USER,
    'bonus_type' => BONUS_ABILITY,
    'stackable' => true,
  ),

  UNIT_SECTOR => array(
    'name' => 'field_max',
    'type' => UNIT_SECTOR,
    'location' => LOC_PLANET,
    'cost' => array(
      RES_DARK_MATTER => 1000,
      'factor' => 1.01,
    ),
    'bonus_type' => BONUS_ABILITY,
  ),

  UNIT_PLANET_DENSITY => array(
    'name' => 'density',
    'type' => UNIT_PLANET_DENSITY,
    'location' => LOC_PLANET,
    'cost' => array(
      RES_DARK_MATTER => 2000,
      'factor' => 1,
    ),
    'bonus_type' => BONUS_ABILITY,
  ),

  'groups' => array(
    // Missions
/*
mission = array(
'DESTINATION' => EMPTY/SAME/PLAYER/ALLY
'ONE_WAY' => true/false, // Is it mission one-way like Relocate/Colonize?
'DURATION' => array(duration list  in  second)/false,  //  List  of  possible durations
'AGGRESIVE' => true/false, // Should aggresive trigger rise?
'AJAX' => true/false, // Is mission can be launch via ajax?
'REQUIRE' => array( // requirements for mission. Empty = any unit from $sn_data['groups']['fleet']
  <any unit_id> => 0 // require any number
  <any unit_id> => <number> // require at least <number>
),
);
*/
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
        'AJAX'       => true,
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
        'AJAX'       => true,
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
        'AJAX'       => true,
      ),

      MT_EXPLORE => array(
        'src_planet' => 0,
        'src_user'   => 1,
        'dst_planet' => 0,
        'dst_user'   => 0,
      ),
    ),

    'planet_density' => array(
      PLANET_DENSITY_NONE => array(
        UNIT_PLANET_DENSITY => 850,
        UNIT_PLANET_DENSITY_INDEX => PLANET_DENSITY_NONE,
        UNIT_PLANET_DENSITY_RARITY => 0,
        UNIT_RESOURCES => array(
          RES_METAL => 0.10,
          RES_CRYSTAL => 0.10,
          RES_DEUTERIUM => 1.30
        ),
      ),
      PLANET_DENSITY_ICE_WATER => array(
        UNIT_PLANET_DENSITY => 2000,
        UNIT_PLANET_DENSITY_INDEX => PLANET_DENSITY_ICE_WATER,
        UNIT_PLANET_DENSITY_RARITY => 23.4,
        UNIT_RESOURCES => array(
          RES_METAL => 0.30,
          RES_CRYSTAL => 0.20,
          RES_DEUTERIUM => 1.20
        ),
      ),
      PLANET_DENSITY_SILICATE => array(
        UNIT_PLANET_DENSITY => 3250,
        UNIT_PLANET_DENSITY_INDEX => PLANET_DENSITY_SILICATE,
        UNIT_PLANET_DENSITY_RARITY => 4.1,
        UNIT_RESOURCES => array(
          RES_METAL => 0.40,
          RES_CRYSTAL => 1.40,
          RES_DEUTERIUM => 0.90
        ),
      ),
      PLANET_DENSITY_STONE => array(
        UNIT_PLANET_DENSITY => 4500,
        UNIT_PLANET_DENSITY_INDEX => PLANET_DENSITY_STONE,
        UNIT_PLANET_DENSITY_RARITY => 1.4,
        UNIT_RESOURCES => array(
          RES_METAL => 0.80,
          RES_CRYSTAL => 1.25,
          RES_DEUTERIUM => 0.80
        ),
      ),
      PLANET_DENSITY_STANDARD => array(
        UNIT_PLANET_DENSITY => 5750,
        UNIT_PLANET_DENSITY_INDEX => PLANET_DENSITY_STANDARD,
        UNIT_PLANET_DENSITY_RARITY => 1,
        UNIT_RESOURCES => array(
          RES_METAL => 1.00,
          RES_CRYSTAL => 1.00,
          RES_DEUTERIUM => 1.00
        ),
      ),
      PLANET_DENSITY_METAL_ORE => array(
        UNIT_PLANET_DENSITY => 7000,
        UNIT_PLANET_DENSITY_INDEX => PLANET_DENSITY_METAL_ORE,
        UNIT_PLANET_DENSITY_RARITY => 1.5,
        UNIT_RESOURCES => array(
          RES_METAL => 2.00,
          RES_CRYSTAL => 0.75,
          RES_DEUTERIUM => 0.75
        ),
      ),
      PLANET_DENSITY_METAL_PRILL => array(
        UNIT_PLANET_DENSITY => 8250,
        UNIT_PLANET_DENSITY_INDEX => PLANET_DENSITY_METAL_PRILL,
        UNIT_PLANET_DENSITY_RARITY => 4.9,
        UNIT_RESOURCES => array(
          RES_METAL => 3.00,
          RES_CRYSTAL => 0.50,
          RES_DEUTERIUM => 0.50
        ),
      ),
      PLANET_DENSITY_METAL_HEAVY => array(
        UNIT_PLANET_DENSITY => 9250,
        UNIT_PLANET_DENSITY_INDEX => PLANET_DENSITY_METAL_HEAVY,
        UNIT_PLANET_DENSITY_RARITY => 31.4,
        UNIT_RESOURCES => array(
          RES_METAL => 4.00,
          RES_CRYSTAL => 0.25,
          RES_DEUTERIUM => 0.25
        ),
      ),
    ),

    // Planet structures list
    'structures' => array(
      STRUC_MINE_METAL => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
      STRUC_MINE_SOLAR => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION,
      STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
      STRUC_LABORATORY => STRUC_LABORATORY, STRUC_LABORATORY_NANO => STRUC_LABORATORY_NANO,
      STRUC_SILO => STRUC_SILO,
      STRUC_STORE_METAL => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
      STRUC_ALLY_DEPOSIT => STRUC_ALLY_DEPOSIT,
      STRUC_TERRAFORMER => STRUC_TERRAFORMER,
      STRUC_MOON_STATION => STRUC_MOON_STATION, STRUC_MOON_PHALANX => STRUC_MOON_PHALANX, STRUC_MOON_GATE => STRUC_MOON_GATE,
    ),
    'build_allow'=> array(
      PT_PLANET => array(
        STRUC_MINE_METAL => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
        STRUC_MINE_SOLAR => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION,
        STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
        STRUC_LABORATORY => STRUC_LABORATORY, STRUC_LABORATORY_NANO => STRUC_LABORATORY_NANO,
        STRUC_SILO => STRUC_SILO,
        STRUC_STORE_METAL => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
        STRUC_ALLY_DEPOSIT => STRUC_ALLY_DEPOSIT,
        STRUC_TERRAFORMER => STRUC_TERRAFORMER,
      ),
      PT_MOON   => array(
        STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
//        STRUC_STORE_METAL => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
        STRUC_ALLY_DEPOSIT => STRUC_ALLY_DEPOSIT,
        STRUC_MOON_STATION => STRUC_MOON_STATION, STRUC_MOON_PHALANX => STRUC_MOON_PHALANX, STRUC_MOON_GATE => STRUC_MOON_GATE,
      ),
    ),
    // List of units that can produce resources
    'factories' => array(
      STRUC_MINE_METAL => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
      STRUC_MINE_SOLAR => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR,
    ),
    // List of units that can hold resources
    'storages' => array(
      STRUC_STORE_METAL => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
    ),

    // Tech list
    'tech'      => array (
      TECH_ARMOR => TECH_ARMOR, TECH_WEAPON => TECH_WEAPON, TECH_SHIELD => TECH_SHIELD,
      TECH_SPY => TECH_SPY, TECH_COMPUTER => TECH_COMPUTER,
      TECH_ENERGY => TECH_ENERGY, TECH_LASER => TECH_LASER, TECH_ION => TECH_ION, TECH_PLASMA => TECH_PLASMA, TECH_HYPERSPACE => TECH_HYPERSPACE,
      TECH_ENGINE_CHEMICAL => TECH_ENGINE_CHEMICAL, TECH_ENGINE_ION => TECH_ENGINE_ION, TECH_ENGINE_HYPER => TECH_ENGINE_HYPER,
      TECH_EXPEDITION => TECH_EXPEDITION, TECH_COLONIZATION => TECH_COLONIZATION, TECH_GRAVITON => TECH_GRAVITON, TECH_RESEARCH => TECH_RESEARCH,
    ),

    // Mercenaries
    'mercenaries' => array (
      MRC_STOCKMAN => MRC_STOCKMAN, MRC_SPY => MRC_SPY, MRC_ACADEMIC => MRC_ACADEMIC,
      MRC_ADMIRAL => MRC_ADMIRAL, MRC_COORDINATOR => MRC_COORDINATOR, MRC_NAVIGATOR => MRC_NAVIGATOR,
    ),
    // Governors
    'governors' => array(
      MRC_TECHNOLOGIST => MRC_TECHNOLOGIST, MRC_ENGINEER => MRC_ENGINEER, MRC_FORTIFIER => MRC_FORTIFIER
    ),
    // Plans
    'plans' => array(
      UNIT_PLAN_STRUC_MINE_FUSION => UNIT_PLAN_STRUC_MINE_FUSION,
      UNIT_PLAN_SHIP_CARGO_SUPER => UNIT_PLAN_SHIP_CARGO_SUPER, UNIT_PLAN_SHIP_CARGO_HYPER => UNIT_PLAN_SHIP_CARGO_HYPER,
      UNIT_PLAN_SHIP_DEATH_STAR => UNIT_PLAN_SHIP_DEATH_STAR, UNIT_PLAN_SHIP_SUPERNOVA => UNIT_PLAN_SHIP_SUPERNOVA,
      UNIT_PLAN_DEF_SHIELD_PLANET => UNIT_PLAN_DEF_SHIELD_PLANET,
    ),

    // Spaceships list
    'fleet'     => array(
      SHIP_FIGHTER_LIGHT => SHIP_FIGHTER_LIGHT, SHIP_FIGHTER_HEAVY => SHIP_FIGHTER_HEAVY,
      SHIP_DESTROYER => SHIP_DESTROYER, SHIP_CRUISER => SHIP_CRUISER,
      SHIP_BOMBER => SHIP_BOMBER, SHIP_BATTLESHIP => SHIP_BATTLESHIP, SHIP_DESTRUCTOR => SHIP_DESTRUCTOR,
      SHIP_DEATH_STAR => SHIP_DEATH_STAR, SHIP_SUPERNOVA => SHIP_SUPERNOVA,
      SHIP_CARGO_SMALL => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
      SHIP_RECYCLER => SHIP_RECYCLER, SHIP_COLONIZER => SHIP_COLONIZER, SHIP_SPY => SHIP_SPY, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR
     ),
    // Defensive building list
    'defense'   => array (UNIT_DEF_TURRET_MISSILE => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL, UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS, UNIT_DEF_TURRET_ION => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA, UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET, UNIT_DEF_MISSILE_INTERCEPTOR => UNIT_DEF_MISSILE_INTERCEPTOR, UNIT_DEF_MISSILE_INTERPLANET => UNIT_DEF_MISSILE_INTERPLANET, ),
    // Missiles list
    'missile'   => array (UNIT_DEF_MISSILE_INTERCEPTOR => UNIT_DEF_MISSILE_INTERCEPTOR, UNIT_DEF_MISSILE_INTERPLANET => UNIT_DEF_MISSILE_INTERPLANET, ),

    // Combat units list
    'combat'    => array(
      SHIP_CARGO_SMALL => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
      SHIP_FIGHTER_LIGHT => SHIP_FIGHTER_LIGHT, SHIP_FIGHTER_HEAVY => SHIP_FIGHTER_HEAVY,
      SHIP_DESTROYER => SHIP_DESTROYER, SHIP_CRUISER => SHIP_CRUISER, SHIP_COLONIZER => SHIP_COLONIZER, SHIP_RECYCLER => SHIP_RECYCLER,
      SHIP_SPY => SHIP_SPY,
      SHIP_BOMBER => SHIP_BOMBER, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR, SHIP_DESTRUCTOR => SHIP_DESTRUCTOR, SHIP_DEATH_STAR => SHIP_DEATH_STAR,
      SHIP_BATTLESHIP => SHIP_BATTLESHIP, SHIP_SUPERNOVA => SHIP_SUPERNOVA,
      UNIT_DEF_TURRET_MISSILE => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL, UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS, UNIT_DEF_TURRET_ION => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA, UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET,
    ),
    // Planet active defense list
    'defense_active' => array(
      UNIT_DEF_TURRET_MISSILE => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL, UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS, UNIT_DEF_TURRET_ION => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA, UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET,
    ),
    // Transports
    'flt_transports' => array(
      SHIP_CARGO_SMALL => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
    ),
    // Recyclers
    'flt_recyclers' => array(
      SHIP_RECYCLER => SHIP_RECYCLER,
    ),
    // Spies
    'flt_spies' => array(
      SHIP_SPY => SHIP_SPY,
    ),
    // Colonizers
    'flt_colonizers' => array(
      SHIP_COLONIZER => SHIP_COLONIZER,
    ),

    'artifacts' => array(
      ART_LHC => ART_LHC, ART_RCD_SMALL => ART_RCD_SMALL, ART_RCD_MEDIUM => ART_RCD_MEDIUM, ART_RCD_LARGE => ART_RCD_LARGE,
      ART_HEURISTIC_CHIP => ART_HEURISTIC_CHIP, ART_NANO_BUILDER => ART_NANO_BUILDER, // ART_DENSITY_CHANGER => ART_DENSITY_CHANGER,
    ),

    // Resource list
    'resources' => array ( 0 => 'metal', 1 => 'crystal', 2 => 'deuterium', 3 => 'dark_matter'),
    // Resources all
    'resources_all' => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_ENERGY => RES_ENERGY, RES_DARK_MATTER => RES_DARK_MATTER),
    // Resources can be produced on planet
    'resources_planet' => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_ENERGY => RES_ENERGY),
    // Resources can be looted from planet
    'resources_loot' => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM),
    // Resources that can be tradeable in market trader
    'resources_trader' => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_DARK_MATTER => RES_DARK_MATTER),

    // List of data modifiers
    'modifiers' => array(
      MODIFIER_RESOURCE_CAPACITY => array(
        MRC_STOCKMAN => MRC_STOCKMAN,
      ),
      MODIFIER_RESOURCE_PRODUCTION => array(
        MRC_TECHNOLOGIST => MRC_TECHNOLOGIST,
      ),
    ),

    // Resources that can be tradeable in market trader AND be a quest_rewards
    'quest_rewards' => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_DARK_MATTER => RES_DARK_MATTER),

//      // Ques list
//      'ques' => array(QUE_STRUCTURES, QUE_HANGAR, QUE_RESEARCH),

    'STAT_COMMON' => array(STAT_TOTAL => STAT_TOTAL, STAT_FLEET => STAT_FLEET, STAT_TECH => STAT_TECH, STAT_BUILDING => STAT_BUILDING, STAT_DEFENSE => STAT_DEFENSE, STAT_RESOURCE => STAT_RESOURCE, ),
    'STAT_PLAYER' => array(STAT_RAID_TOTAL => STAT_RAID_TOTAL, STAT_RAID_WON => STAT_RAID_WON, STAT_RAID_LOST => STAT_RAID_LOST, STAT_LVL_BUILDING => STAT_LVL_BUILDING, STAT_LVL_TECH => STAT_LVL_TECH, STAT_LVL_RAID => STAT_LVL_RAID, ),
  ),

  'pages' => array(
    'chat' => array(
      'filename' => 'chat',
      'options' => array(
        'fleet_update_skip' => true,
      ),
    ),
    'chat_add' => array(
      'filename' => 'chat',
      'options' => array(
        'fleet_update_skip' => true,
      ),
    ),
    'chat_msg' => array(
      'filename' => 'chat',
      'options' => array(
        'fleet_update_skip' => true,
      ),
    ),
    'contact' => array(
      'allow_anonymous' => true,
      'filename' => 'contact',
    ),
    'imperator' => array(
      'filename' => 'imperator',
    ),
    'imperium' => array(
      'filename' => 'imperium',
    ),
    'options' => array(
      'filename' => 'options',
    ),
    'techtree' => array(
      'filename' => 'techtree',
    ),
    'battle_report' => array(
      'filename' => 'battle_report',
    ),
  ),
);

$sn_data['techtree'] = array(
  UNIT_STRUCTURES => &$sn_data['groups']['build_allow'][PT_PLANET],
  UNIT_STRUCTURES_SPECIAL => array_diff($sn_data['groups']['build_allow'][PT_MOON], $sn_data['groups']['build_allow'][PT_PLANET]),
  UNIT_TECHNOLOGIES => &$sn_data['groups']['tech'],
  UNIT_SHIPS => &$sn_data['groups']['fleet'],
  UNIT_DEFENCE => &$sn_data['groups']['defense'],
  UNIT_MERCENARIES => &$sn_data['groups']['mercenaries'],
  UNIT_GOVERNORS => &$sn_data['groups']['governors'],
  UNIT_RESOURCES => &$sn_data['groups']['resources_all'],
  UNIT_ARTIFACTS => &$sn_data['groups']['artifacts'],
  UNIT_PLANS => &$sn_data['groups']['plans'],
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

$sn_pwr_buy_discount = array(
//  PERIOD_MINUTE    => 1,
//  PERIOD_MINUTE_3  => 1,
//  PERIOD_MINUTE_5  => 1,
//  PERIOD_MINUTE_10 => 1,
//  PERIOD_DAY       => 3,
//  PERIOD_DAY_3     => 2,
  PERIOD_WEEK      => 1.5,
  PERIOD_WEEK_2    => 1.2,
  PERIOD_MONTH     => 1,
  PERIOD_MONTH_2   => 0.9,
  PERIOD_MONTH_3   => 0.8,
);

foreach ($sn_data as $unitID => $unitData)
{
  if(!isset($sn_data[$unitID]['cost']['metal']))
  {
    continue;
  }
  $sn_data[$unitID]['armor'] = ($sn_data[$unitID]['cost']['metal'] + $sn_data[$unitID]['cost']['crystal'])/10;
/*
  foreach ($unitData['sd'] as $enemyID => $SPD)
  {
    if ($SPD>1)
    {

      // $enemyArmor = ($sn_data[$enemyID]['metal'] + $sn_data[$enemyID]['crystal'])/10;
      // $a1 = ($enemyArmor + $sn_data[$enemyID]['shield']) * $SPD / $unitData['attack'];

      $a1 = ($sn_data[$enemyID]['armor'] + $sn_data[$enemyID]['shield']) * $SPD / $unitData['attack'];
      $sn_data[$unitID]['amplify'][$enemyID] = $a1;

//        $unitData['attack'] * $sn_data[$unitID]['amplify'][$enemyID] / ($sn_data[$enemyID]['armor'] + $sn_data[$enemyID]['shield']) = $SPD
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
