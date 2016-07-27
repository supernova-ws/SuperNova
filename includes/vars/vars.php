<?php

/**
 * vars.php
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

defined('INSIDE') || die();

$sn_menu_extra = array();
$sn_menu_admin_extra = array();

classSupernova::$sn_mvc = array(
  'model'      => array(
    'options'  => array('sn_options_model'),
    'chat'     => array('sn_chat_model'),
    'chat_add' => array('sn_chat_add_model'),
  ),
  'view'       => array(
    'options'  => array('sn_options_view'),
    'chat'     => array('sn_chat_view'),
    'chat_msg' => array('sn_chat_msg_view'),
  ),
  'controller' => array(),
  'i18n'       => array(
    'options' => array(
      'options'  => 'options',
      'messages' => 'messages',
    ),
  ),
);

$note_priority_classes = array(
  4 => 'error',
  3 => 'warning',
  2 => 'notice',
  1 => 'ok',
  0 => 'white',
);

$sn_ali_admin_internal = array(
  'rights'    => array(
    'include' => 'alliance/ali_internal_admin_rights.inc',
    'title'   => 'ali_adm_rights_title'
  ),
  'members'   => array(
    'include' => 'alliance/ali_internal_members.inc',
    'title'   => 'Members_list'
  ),
  'requests'  => array(
    'include' => 'alliance/ali_internal_admin_request.inc',
    'title'   => 'ali_req_check'
  ),
  'diplomacy' => array(
    'include' => 'alliance/ali_internal_admin_diplomacy.inc',
    'title'   => 'ali_dip_title'
  ),
  'default'   => array(
    'include' => 'alliance/ali_internal_admin.inc',
  ),
);

$sn_version_check_class = array(
  SNC_VER_NEVER => 'warning',

  SNC_VER_ERROR_CONNECT => 'error',
  SNC_VER_ERROR_SERVER  => 'error',

  SNC_VER_EXACT  => 'ok',
  SNC_VER_LESS   => 'notice',
  SNC_VER_FUTURE => 'error',

  SNC_VER_RELEASE_EXACT => 'ok',
  SNC_VER_RELEASE_MINOR => 'notice',
  SNC_VER_RELEASE_MAJOR => 'warning',
  SNC_VER_RELEASE_ALPHA => 'ok',

  SNC_VER_MAINTENANCE      => 'notice',
  SNC_VER_UNKNOWN_RESPONSE => 'warning',
  SNC_VER_INVALID          => 'error',
  SNC_VER_STRANGE          => 'error',

  SNC_VER_REGISTER_UNREGISTERED      => 'warning',
  SNC_VER_REGISTER_ERROR_MULTISERVER => 'error',
  SNC_VER_REGISTER_ERROR_REGISTERED  => 'error',
  SNC_VER_REGISTER_ERROR_NO_NAME     => 'error',
  SNC_VER_REGISTER_ERROR_WRONG_URL   => 'error',
  SNC_VER_REGISTER_REGISTERED        => 'ok',

  SNC_VER_ERROR_INCOMPLETE_REQUEST => 'error',
  SNC_VER_ERROR_UNKNOWN_KEY        => 'error',
  SNC_VER_ERROR_MISSMATCH_KEY_ID   => 'error',
);

$tableList = array('aks', 'alliance', 'alliance_requests', 'announce', 'annonce', 'banned', 'buddy', 'chat', 'config', 'counter',
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

classSupernova::$functions = array();

// Default user option list as 'option_name' => 'option_list'
$user_option_list = array();

$user_option_list[OPT_MESSAGE] = array();
foreach (DBStaticMessages::$snMessageClassList as $message_class_id => $message_class_data) {
  if ($message_class_data['switchable']) {
    $user_option_list[OPT_MESSAGE]["opt_{$message_class_data['name']}"] = 1;
  }

  if ($message_class_data['email']) {
    $user_option_list[OPT_MESSAGE]["opt_email_{$message_class_data['name']}"] = 0;
  }
}

$user_option_list[OPT_UNIVERSE] = array(
  'opt_uni_avatar_user' => 1,
  'opt_uni_avatar_ally' => 1,
);

$user_option_list[OPT_INTERFACE] = array(
  'opt_int_navbar_resource_force'   => 0,
  'opt_int_overview_planet_columns' => 0,
  'opt_int_overview_planet_rows'    => 5,
  'opt_int_struc_vertical'          => 0,
);

$user_option_types = array(
  'opt_int_overview_planet_columns' => TYPE_INTEGER,
  'opt_int_overview_planet_rows'    => TYPE_INTEGER,
);

$sn_diplomacy_relation_list = array(
  ALLY_DIPLOMACY_NEUTRAL => array(
    'relation_id' => ALLY_DIPLOMACY_NEUTRAL,
    'enter_delay' => 0,
    'exit_delay'  => 0,
  ),
  ALLY_DIPLOMACY_WAR     => array(
    'relation_id' => ALLY_DIPLOMACY_WAR,
    'enter_delay' => classSupernova::$config->fleet_bashing_war_delay,
    'exit_delay'  => -1,
  ),
  ALLY_DIPLOMACY_PEACE   => array(
    'relation_id' => ALLY_DIPLOMACY_PEACE,
    'enter_delay' => -1,
    'exit_delay'  => 0,
  ),
  /*
  ALLY_DIPLOMACY_CONFEDERATION => array(
    'relation_id' => ALLY_DIPLOMACY_CONFEDERATION,
    'enter_delay' => -1,
    'exit_delay'  => config->fleet_bashing_war_delay,
  ),
  ALLY_DIPLOMACY_FEDERATION    => array(
    'relation_id' => ALLY_DIPLOMACY_FEDERATION,
    'enter_delay' => -1,
    'exit_delay'  => config->fleet_bashing_war_delay,
  ),
  ALLY_DIPLOMACY_UNION         => array(
    'relation_id' => ALLY_DIPLOMACY_UNION,
    'enter_delay' => -1,
    'exit_delay'  => config->fleet_bashing_war_delay,
  ),
  ALLY_DIPLOMACY_MASTER        => array(
    'relation_id' => ALLY_DIPLOMACY_MASTER,
    'enter_delay' => -1,
    'exit_delay'  => 0,
  ),
  ALLY_DIPLOMACY_SLAVE         => array(
    'relation_id' => ALLY_DIPLOMACY_SLAVE,
    'enter_delay' => -1,
    'exit_delay'  => config->fleet_bashing_war_delay,
  )
  */
);

// factor -> price_factor, perhour_factor
$sn_data = array();

require_once 'vars_structures.php';
require_once 'vars_combats.php';
require_once 'vars_powerups.php';

$sn_data += array(
  RES_METAL       => array(
    'name'       => 'metal',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_PLANET,
    'bonus_type' => BONUS_ABILITY,
    P_STACKABLE  => true,
  ),
  RES_CRYSTAL     => array(
    'name'       => 'crystal',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_PLANET,
    'bonus_type' => BONUS_ABILITY,
    P_STACKABLE  => true,
  ),
  RES_DEUTERIUM   => array(
    'name'       => 'deuterium',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_PLANET,
    'bonus_type' => BONUS_ABILITY,
    P_STACKABLE  => true,
  ),
  RES_ENERGY      => array(
    'name'       => 'energy',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_PLANET,
    'bonus_type' => BONUS_ABILITY,
    P_STACKABLE  => true,
  ),
  RES_DARK_MATTER => array(
    'name'       => 'dark_matter',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_USER,
    'bonus_type' => BONUS_ABILITY,
    P_STACKABLE  => true,
  ),
  RES_METAMATTER  => array(
    'name'       => 'metamatter',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_USER,
    'bonus_type' => BONUS_ABILITY,
    P_STACKABLE  => true,
  ),

  UNIT_SECTOR => array(
    'name'       => 'field_max',
    'type'       => UNIT_SECTOR,
    'location'   => LOC_PLANET,
    'cost'       => array(
      RES_DARK_MATTER => 1000,
      'factor'        => 1.01,
    ),
    'bonus_type' => BONUS_ABILITY,
  ),

  UNIT_PLANET_DENSITY => array(
    'name'       => 'density',
    'type'       => UNIT_PLANET_DENSITY,
    'location'   => LOC_PLANET,
    'cost'       => array(
      RES_DARK_MATTER => 2000,
      'factor'        => 1,
    ),
    'bonus_type' => BONUS_ABILITY,
  ),

  UNIT_GROUP => array(
    'mission_explore_outcome_list' => array(
      FLT_EXPEDITION_OUTCOME_NONE            => array(
        'chance' => 110,
      ),
      FLT_EXPEDITION_OUTCOME_LOST_FLEET      => array(
        'chance' => 9,
      ),
      FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL  => array(
        'chance' => 3,
      ),
      FLT_EXPEDITION_OUTCOME_FOUND_FLEET     => array(
        'chance'  => 200,
        'percent' => array(
          0 => 0.1,
          1 => 0.02,
          2 => 0.01,
        ),
      ),
      FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES => array(
        'chance'  => 300,
        'percent' => array(
          0 => 0.1,
          1 => 0.050,
          2 => 0.025,
        ),
      ),
      FLT_EXPEDITION_OUTCOME_FOUND_DM        => array(
        'chance'  => 100,
        'percent' => array(
          0 => 0.0100,
          1 => 0.0040,
          2 => 0.0010,
        ),
      ),
      /*
      FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT => array(
        'chance' => 10,
      ),
      */
    ),

    // Planet structures list
    'structures'         => array(
      STRUC_MINE_METAL    => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
      STRUC_MINE_SOLAR    => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION,
      STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
      STRUC_LABORATORY    => STRUC_LABORATORY, STRUC_LABORATORY_NANO => STRUC_LABORATORY_NANO,
      STRUC_SILO          => STRUC_SILO,
      STRUC_STORE_METAL   => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
      STRUC_ALLY_DEPOSIT  => STRUC_ALLY_DEPOSIT,
      STRUC_TERRAFORMER   => STRUC_TERRAFORMER,
      STRUC_MOON_STATION  => STRUC_MOON_STATION, STRUC_MOON_PHALANX => STRUC_MOON_PHALANX, STRUC_MOON_GATE => STRUC_MOON_GATE,
    ),
    'build_allow'        => array(
      PT_PLANET => array(
        STRUC_MINE_METAL    => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
        STRUC_MINE_SOLAR    => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION,
        STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
        STRUC_LABORATORY    => STRUC_LABORATORY, STRUC_LABORATORY_NANO => STRUC_LABORATORY_NANO,
        STRUC_SILO          => STRUC_SILO,
        STRUC_STORE_METAL   => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
        STRUC_ALLY_DEPOSIT  => STRUC_ALLY_DEPOSIT,
        STRUC_TERRAFORMER   => STRUC_TERRAFORMER,
      ),
      PT_MOON   => array(
        STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
//        STRUC_STORE_METAL => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
        STRUC_ALLY_DEPOSIT  => STRUC_ALLY_DEPOSIT,
        STRUC_MOON_STATION  => STRUC_MOON_STATION, STRUC_MOON_PHALANX => STRUC_MOON_PHALANX, STRUC_MOON_GATE => STRUC_MOON_GATE,
      ),
    ),
    // List of units that can produce resources
    'factories'          => array(
      STRUC_MINE_METAL => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
      STRUC_MINE_SOLAR => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR,
    ),
    // List of units that can hold resources
    'storages'           => array(
      STRUC_STORE_METAL => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
    ),

    // Tech list
    'tech'               => array(
      TECH_ARMOR           => TECH_ARMOR, TECH_WEAPON => TECH_WEAPON, TECH_SHIELD => TECH_SHIELD,
      TECH_SPY             => TECH_SPY, TECH_COMPUTER => TECH_COMPUTER,
      TECH_ENERGY          => TECH_ENERGY, TECH_LASER => TECH_LASER, TECH_ION => TECH_ION, TECH_PLASMA => TECH_PLASMA, TECH_HYPERSPACE => TECH_HYPERSPACE,
      TECH_ENGINE_CHEMICAL => TECH_ENGINE_CHEMICAL, TECH_ENGINE_ION => TECH_ENGINE_ION, TECH_ENGINE_HYPER => TECH_ENGINE_HYPER,
      // TECH_EXPEDITION => TECH_EXPEDITION, TECH_COLONIZATION => TECH_COLONIZATION,
      TECH_ASTROTECH       => TECH_ASTROTECH,
      TECH_GRAVITON        => TECH_GRAVITON, TECH_RESEARCH => TECH_RESEARCH,
    ),

    // Mercenaries
    'mercenaries'        => array(
      MRC_STOCKMAN => MRC_STOCKMAN, MRC_SPY => MRC_SPY, MRC_ACADEMIC => MRC_ACADEMIC,
      MRC_ADMIRAL  => MRC_ADMIRAL, MRC_COORDINATOR => MRC_COORDINATOR, MRC_NAVIGATOR => MRC_NAVIGATOR,
    ),
    // Governors
    'governors'          => array(
      MRC_TECHNOLOGIST => MRC_TECHNOLOGIST, MRC_ENGINEER => MRC_ENGINEER, MRC_FORTIFIER => MRC_FORTIFIER
    ),
    // Plans
    'plans'              => array(
      UNIT_PLAN_STRUC_MINE_FUSION => UNIT_PLAN_STRUC_MINE_FUSION,
      UNIT_PLAN_SHIP_CARGO_SUPER  => UNIT_PLAN_SHIP_CARGO_SUPER, UNIT_PLAN_SHIP_CARGO_HYPER => UNIT_PLAN_SHIP_CARGO_HYPER,
      UNIT_PLAN_SHIP_DEATH_STAR   => UNIT_PLAN_SHIP_DEATH_STAR, UNIT_PLAN_SHIP_SUPERNOVA => UNIT_PLAN_SHIP_SUPERNOVA,
      UNIT_PLAN_DEF_SHIELD_PLANET => UNIT_PLAN_DEF_SHIELD_PLANET,
    ),

    // Spaceships list
    'fleet'              => array(
      SHIP_SMALL_FIGHTER_LIGHT => SHIP_SMALL_FIGHTER_LIGHT, SHIP_SMALL_FIGHTER_HEAVY => SHIP_SMALL_FIGHTER_HEAVY,
      SHIP_MEDIUM_FRIGATE      => SHIP_MEDIUM_FRIGATE, SHIP_LARGE_CRUISER => SHIP_LARGE_CRUISER,
      SHIP_LARGE_BOMBER        => SHIP_LARGE_BOMBER, SHIP_LARGE_BATTLESHIP => SHIP_LARGE_BATTLESHIP, SHIP_LARGE_DESTRUCTOR => SHIP_LARGE_DESTRUCTOR,
      SHIP_HUGE_DEATH_STAR     => SHIP_HUGE_DEATH_STAR, SHIP_HUGE_SUPERNOVA => SHIP_HUGE_SUPERNOVA,
      SHIP_CARGO_SMALL         => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
      SHIP_RECYCLER            => SHIP_RECYCLER, SHIP_COLONIZER => SHIP_COLONIZER, SHIP_SPY => SHIP_SPY, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR
    ),
    // Defensive building list
    'defense'            => array(UNIT_DEF_TURRET_MISSILE   => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL,
                                  UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS,
                                  UNIT_DEF_TURRET_ION       => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA,

                                  UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET,

                                  UNIT_DEF_MISSILE_INTERCEPTOR => UNIT_DEF_MISSILE_INTERCEPTOR, UNIT_DEF_MISSILE_INTERPLANET => UNIT_DEF_MISSILE_INTERPLANET,
    ),

    // Missiles list
    GROUP_STR_MISSILES            => array(UNIT_DEF_MISSILE_INTERCEPTOR => UNIT_DEF_MISSILE_INTERCEPTOR, UNIT_DEF_MISSILE_INTERPLANET => UNIT_DEF_MISSILE_INTERPLANET,),

    // Combat units list
    'combat'             => array(
      SHIP_CARGO_SMALL         => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
      SHIP_SMALL_FIGHTER_LIGHT => SHIP_SMALL_FIGHTER_LIGHT, SHIP_SMALL_FIGHTER_HEAVY => SHIP_SMALL_FIGHTER_HEAVY,
      SHIP_MEDIUM_FRIGATE      => SHIP_MEDIUM_FRIGATE, SHIP_LARGE_CRUISER => SHIP_LARGE_CRUISER, SHIP_COLONIZER => SHIP_COLONIZER, SHIP_RECYCLER => SHIP_RECYCLER,
      SHIP_SPY                 => SHIP_SPY,
      SHIP_LARGE_BOMBER        => SHIP_LARGE_BOMBER, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR, SHIP_LARGE_DESTRUCTOR => SHIP_LARGE_DESTRUCTOR, SHIP_HUGE_DEATH_STAR => SHIP_HUGE_DEATH_STAR,
      SHIP_LARGE_BATTLESHIP    => SHIP_LARGE_BATTLESHIP, SHIP_HUGE_SUPERNOVA => SHIP_HUGE_SUPERNOVA,
      UNIT_DEF_TURRET_MISSILE  => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL, UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS, UNIT_DEF_TURRET_ION => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA, UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET,
    ),
    // Planet active defense list
    'defense_active'     => array(
      UNIT_DEF_TURRET_MISSILE => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL, UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS, UNIT_DEF_TURRET_ION => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA, UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET,
    ),
    // Transports
    'flt_transports'     => array(
      SHIP_CARGO_SMALL => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
    ),
    // Recyclers
    'flt_recyclers'      => array(
      SHIP_RECYCLER => SHIP_RECYCLER,
    ),
    // Recyclers
    'flt_reapers'        => array(
      SHIP_HUGE_DEATH_STAR => SHIP_HUGE_DEATH_STAR,
    ),
    // Spies
    'flt_spies'          => array(
      SHIP_SPY => SHIP_SPY,
    ),
    // Colonizers
    'flt_colonizers'     => array(
      SHIP_COLONIZER => SHIP_COLONIZER,
    ),

    'artifacts'        => array(
      ART_LHC            => ART_LHC, ART_HOOK_SMALL => ART_HOOK_SMALL, ART_HOOK_MEDIUM => ART_HOOK_MEDIUM, ART_HOOK_LARGE => ART_HOOK_LARGE,
      ART_RCD_SMALL      => ART_RCD_SMALL, ART_RCD_MEDIUM => ART_RCD_MEDIUM, ART_RCD_LARGE => ART_RCD_LARGE,
      ART_HEURISTIC_CHIP => ART_HEURISTIC_CHIP, ART_NANO_BUILDER => ART_NANO_BUILDER, // ART_DENSITY_CHANGER => ART_DENSITY_CHANGER,
    ),

    // Resource list
    'resources'        => array(0 => 'metal', 1 => 'crystal', 2 => 'deuterium', 3 => 'dark_matter'),
    // Resources all
    'resources_all'    => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_ENERGY => RES_ENERGY, RES_DARK_MATTER => RES_DARK_MATTER, RES_METAMATTER => RES_METAMATTER,),
    // Resources can be produced on planet
    'resources_planet' => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_ENERGY => RES_ENERGY),
    // Resources can be looted from planet
    'resources_loot'   => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM),
    // Resources that can be tradeable in market trader
    'resources_trader' => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_DARK_MATTER => RES_DARK_MATTER),

    // List of data modifiers
    'modifiers'        => array(
      MODIFIER_RESOURCE_CAPACITY   => array(
        MRC_STOCKMAN => MRC_STOCKMAN,
      ),
      MODIFIER_RESOURCE_PRODUCTION => array(
        MRC_TECHNOLOGIST => MRC_TECHNOLOGIST,
      ),
    ),

    // Resources that can be tradeable in market trader AND be a quest_rewards
    'quest_rewards'    => array(RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_DARK_MATTER => RES_DARK_MATTER,),

//      // Ques list
//      'ques' => array(QUE_STRUCTURES, QUE_HANGAR, QUE_RESEARCH),

    'STAT_COMMON' => array(STAT_TOTAL => STAT_TOTAL, STAT_FLEET => STAT_FLEET, STAT_TECH => STAT_TECH, STAT_BUILDING => STAT_BUILDING, STAT_DEFENSE => STAT_DEFENSE, STAT_RESOURCE => STAT_RESOURCE,),
    'STAT_PLAYER' => array(STAT_RAID_TOTAL => STAT_RAID_TOTAL, STAT_RAID_WON => STAT_RAID_WON, STAT_RAID_LOST => STAT_RAID_LOST, STAT_LVL_BUILDING => STAT_LVL_BUILDING, STAT_LVL_TECH => STAT_LVL_TECH, STAT_LVL_RAID => STAT_LVL_RAID,),

    P_BONUS_VALUE => array(
      P_ATTACK => array(
        TECH_WEAPON   => TECH_WEAPON,
        MRC_ADMIRAL   => MRC_ADMIRAL,
        MRC_FORTIFIER => MRC_FORTIFIER,
      ),
      P_SHIELD => array(
        TECH_SHIELD   => TECH_SHIELD,
        MRC_ADMIRAL   => MRC_ADMIRAL,
        MRC_FORTIFIER => MRC_FORTIFIER,
      ),
      P_ARMOR  => array(
        TECH_ARMOR    => TECH_ARMOR,
        MRC_ADMIRAL   => MRC_ADMIRAL,
        MRC_FORTIFIER => MRC_FORTIFIER,
      ),
    ),
  ),

  'pages' => array(
    'chat'     => array(
      'filename' => 'chat',
      'options'  => array(
        'fleet_update_skip' => true,
      ),
    ),
    'chat_add' => array(
      'filename' => 'chat',
      'options'  => array(
        'fleet_update_skip' => true,
      ),
    ),
    'chat_msg' => array(
      'filename' => 'chat',
      'options'  => array(
        'fleet_update_skip' => true,
      ),
    ),
    // 'chat_frame' => 'modules/chat_advanced/chat_advanced',

    'contact'       => array(
      'allow_anonymous' => true,
      'filename'        => 'contact',
    ),
    'imperator'     => array(
      'filename' => 'imperator',
    ),
    'imperium'      => array(
      'filename' => 'imperium',
    ),
    'options'       => array(
      'filename' => 'options',
    ),
    'techtree'      => array(
      'filename' => 'techtree',
    ),
    'battle_report' => array(
      'filename' => 'battle_report',
    ),
  ),
);

require_once 'vars_missions.php';
require_once 'vars_planet.php';

$sn_data['techtree'] = array(
  UNIT_STRUCTURES         => &$sn_data[UNIT_GROUP]['build_allow'][PT_PLANET],
  UNIT_STRUCTURES_SPECIAL => array_diff($sn_data[UNIT_GROUP]['build_allow'][PT_MOON], $sn_data[UNIT_GROUP]['build_allow'][PT_PLANET]),
  UNIT_TECHNOLOGIES       => &$sn_data[UNIT_GROUP]['tech'],
  UNIT_SHIPS              => &$sn_data[UNIT_GROUP]['fleet'],
  UNIT_DEFENCE            => &$sn_data[UNIT_GROUP]['defense'],
  UNIT_MERCENARIES        => &$sn_data[UNIT_GROUP]['mercenaries'],
  UNIT_GOVERNORS          => &$sn_data[UNIT_GROUP]['governors'],
  UNIT_RESOURCES          => &$sn_data[UNIT_GROUP]['resources_all'],
  UNIT_ARTIFACTS          => &$sn_data[UNIT_GROUP]['artifacts'],
  UNIT_PLANS              => &$sn_data[UNIT_GROUP]['plans'],
);

//All resources
$sn_data[UNIT_GROUP]['all'] = array_merge($sn_data[UNIT_GROUP]['structures'], $sn_data[UNIT_GROUP]['tech'], $sn_data[UNIT_GROUP]['fleet'], $sn_data[UNIT_GROUP]['defense'], $sn_data[UNIT_GROUP]['mercenaries']);

$sn_data[UNIT_GROUP]['ques'] = array(
  QUE_STRUCTURES => array(
    'unit_list' => $sn_data[UNIT_GROUP]['structures'],
    'length'    => 5,
    'mercenary' => MRC_ENGINEER,
    'que'       => QUE_STRUCTURES,
  ),

  QUE_HANGAR => array(
    'unit_list' => $sn_data[UNIT_GROUP]['fleet'],
    'length'    => 5,
    'mercenary' => MRC_ENGINEER,
    'que'       => QUE_HANGAR,
  ),

  SUBQUE_DEFENSE => array(
    'unit_list' => $sn_data[UNIT_GROUP]['defense'],
    'length'    => 5,
    'mercenary' => MRC_FORTIFIER,
    'que'       => QUE_HANGAR,
  ),

  QUE_RESEARCH => array(
    'unit_list' => $sn_data[UNIT_GROUP]['tech'],
    'length'    => 1,
    'mercenary' => MRC_ACADEMIC,
    'que'       => QUE_RESEARCH,
  )
);

$sn_data[UNIT_GROUP]['subques'] = array(
  SUBQUE_PLANET => array(
    'que'       => QUE_STRUCTURES,
    'mercenary' => MRC_ENGINEER,
    'unit_list' => $sn_data[UNIT_GROUP]['build_allow'][PT_PLANET],
  ),

  SUBQUE_MOON => array(
    'que'       => QUE_STRUCTURES,
    'mercenary' => MRC_ENGINEER,
    'unit_list' => $sn_data[UNIT_GROUP]['build_allow'][PT_MOON],
  ),

  SUBQUE_FLEET => array(
    'que'       => QUE_HANGAR,
    'mercenary' => MRC_ENGINEER,
    'unit_list' => $sn_data[UNIT_GROUP]['fleet'],
  ),

  SUBQUE_DEFENSE => array(
    'que'       => QUE_HANGAR,
    'mercenary' => MRC_FORTIFIER,
    'unit_list' => $sn_data[UNIT_GROUP]['defense'],
  ),

  SUBQUE_RESEARCH => array(
    'que'       => QUE_RESEARCH,
    'mercenary' => MRC_ACADEMIC,
    'unit_list' => $sn_data[UNIT_GROUP]['tech'],
  ),
);

$sn_powerup_buy_discounts = array(
//  PERIOD_MINUTE    => 1,
//  PERIOD_MINUTE_3  => 1,
//  PERIOD_MINUTE_5  => 1,
//  PERIOD_MINUTE_10 => 1,
//  PERIOD_DAY       => 3,
//  PERIOD_DAY_3     => 2,
  PERIOD_WEEK    => 1.5,
  PERIOD_WEEK_2  => 1.2,
  PERIOD_MONTH   => 1,
  PERIOD_MONTH_2 => 0.9,
  PERIOD_MONTH_3 => 0.8,
);

/*
foreach($sn_data as $unitID => $unitData)
{
  if(!isset($sn_data[$unitID]['cost']['metal']))
  {
    continue;
  }
  $sn_data[$unitID]['armor'] = ($sn_data[$unitID]['cost']['metal'] + $sn_data[$unitID]['cost']['crystal'])/10;
}
*/