<?php

/**
 * vars.php
 *
 * @version   1.0
 * @copyright 2008 by Chlorel for XNova
 */

use Alliance\Alliance;
use Chat\Chat;
use Pages\Deprecated\PageAdminMining;
use Pages\Deprecated\PageAdminModules;
use Pages\Deprecated\PageAdminPayment;
use Pages\Deprecated\PageAdminUserView;
use Pages\Deprecated\PageImperium;

if (!defined('INSIDE')) {
  die('Hack attempt!');
}

global $sn_menu_extra, $sn_menu_admin_extra, $config;

$sn_menu_extra = array();
$sn_menu_admin_extra = array();

global $sn_mvc;
$sn_mvc = [
  FIELD_MODEL => [
    'ajax' => [AjaxController::class . '::controller'],

    'options'  => ['sn_options_model'],

//    'chat'     => ['sn_chat_model'],
//    'chat_add' => ['sn_chat_add_model'],
    'chat'     => [Chat::class  . '::' . 'chatModel'],
    'chat_add' => [Chat::class  . '::' . 'chatAddModel'],

    'imperium' => [PageImperium::class . '::' . 'modelStatic'],

    'admin/user_view'  => [PageAdminUserView::class . '::' . 'modelStatic'],
    'admin/admin_ally' => ['sn_admin_ally_model'],
  ],
  FIELD_VIEW  => [
    'ajax' => [AjaxController::class . '::view'],

    'options'       => ['sn_options_view'],

//    'chat'          => ['sn_chat_view'],
//    'chat_msg'      => ['sn_chat_msg_view'],
    'chat'          => [Chat::class  . '::' . 'chatView'],
    'chat_msg'      => [Chat::class  . '::' . 'chatMsgView'],
    'chat_frame'      => [Chat::class  . '::' . 'chatFrameView'],

    'battle_report' => ['sn_battle_report_view'],
    'contact'       => ['sn_contact_view'],
    'imperator'     => ['sn_imperator_view'],
    'imperium'      => [PageImperium::class . '::' . 'viewStatic'],
    'techtree'      => ['sn_techtree_view'],

    'admin/user_view'     => [PageAdminUserView::class . '::viewStatic'],
    'admin/admin_ally'    => ['sn_admin_ally_view'],
    'admin/admin_mining'  => [PageAdminMining::class . '::' . 'viewStatic'],
    'admin/admin_modules' => [PageAdminModules::class . '::' . 'viewStatic'],
    'admin/admin_payment' => [PageAdminPayment::class . '::' . 'viewStatic'],
  ],

  // For now - common for same VIEW and MODEL
  MVC_OPTIONS => [],

  'controller' => [],

  'i18n' => [
    'options'             => [
      'options'  => 'options',
      'messages' => 'messages',
    ],
    'imperator'           => [
      'overview' => 'overview',
    ],
    'admin/admin_payment' => [
      'admin' => 'admin',
    ],
    'admin/user_view'     => [
      'admin' => 'admin',
    ],

    'chat' => [
      'chat_advanced' => [
        'file' => 'chat_advanced',
        'path' => '',
      ],
    ],
    'chat_add' => [
      'chat_advanced' => 'chat_advanced',
//      'chat_advanced' => [
//        'file' => 'chat_advanced',
//        'path' => '',
//      ],
    ],
    'chat_msg' => [
      'chat_advanced' => 'chat_advanced',
//      'chat_advanced' => [
//        'file' => 'chat_advanced',
//        'path' => '',
//      ],
    ],
    'chat_frame' => [
      'chat_advanced' => 'chat_advanced',
//      'chat_advanced' => [
//        'file' => 'chat_advanced',
//        'path' => '',
//      ],
    ],

  ],

  'pages' => [
    'admin/user_view' => [
      'filename' => 'admin/user_view',
      'options'  => [
        PAGE_OPTION_FLEET_UPDATE_SKIP => true,
        PAGE_OPTION_ADMIN             => true,
      ],
    ],

    'admin/admin_ally' => [
      'filename' => 'admin/admin_ally',
      'options'  => [
        PAGE_OPTION_FLEET_UPDATE_SKIP => true,
        PAGE_OPTION_ADMIN             => true,
      ],
    ],

    'admin/admin_mining'  => [
      'options' => [
        PAGE_OPTION_ADMIN => true,
      ],
    ],
    'admin/admin_modules' => [
      'options' => [
        PAGE_OPTION_ADMIN => true,
      ],
    ],
    'admin/admin_payment' => [
      'options' => [
        PAGE_OPTION_ADMIN => true,
      ],
    ],

    'chat'     => [
      'filename' => 'chat',
      'options'  => [
        PAGE_OPTION_FLEET_UPDATE_SKIP => true,
      ],
    ],
    'chat_add' => [
      'filename' => 'chat',
      'options'  => [
        PAGE_OPTION_FLEET_UPDATE_SKIP => true,
      ],
    ],
    'chat_msg' => [
      'filename' => 'chat',
      'options'  => [
        PAGE_OPTION_FLEET_UPDATE_SKIP => true,
      ],
    ],
    'chat_frame' => [
      'filename' => 'chat',
      'options'  => [
        PAGE_OPTION_FLEET_UPDATE_SKIP => true,
      ],
    ],

    'contact'       => [
      'allow_anonymous' => true,
      'filename'        => 'contact',
    ],
    'imperator'     => [
      'filename' => 'imperator',
    ],
    'imperium'      => [],
    'options'       => [
      'filename' => 'options',
    ],
    'techtree'      => [
      'filename' => 'techtree',
    ],
    'battle_report' => [
      'filename' => 'battle_report',
    ],
    'ajax'          => [],
    'time_probe'    => [
      'options' => [
        PAGE_OPTION_FLEET_UPDATE_SKIP => true,
      ],
    ],

    'buildings' => [
      // PAGE_OPTION_EARLY_HEADER => true,
      // PAGE_OPTION_TITLE => 'buildings',
    ],

    'overview' => [
      // PAGE_OPTION_EARLY_HEADER => true,
      // PAGE_OPTION_TITLE => 'buildings',
    ],
  ],
];

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
  'mail'      => array(
    'include' => 'alliance/ali_internal_admin_mail.inc',
    'title'   => 'Send_circular_mail'
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

global $ally_rights;
$ally_rights = Alliance::RIGHTS_ALL;

$functions = array(//    'test' => 'sn_test',
);

$sn_message_class_list = array(
  MSG_TYPE_NEW       => array(
    'name'       => 'new_message',
    'switchable' => false,
    'email'      => false,
  ),
  MSG_TYPE_ADMIN     => array(
    'name'       => 'msg_admin',
    'switchable' => false,
    'email'      => true,
  ),
  MSG_TYPE_PLAYER    => array(
    'name'       => 'mnl_joueur',
    'switchable' => false,
    'email'      => true,
  ),
  MSG_TYPE_ALLIANCE  => array(
    'name'       => 'mnl_alliance',
    'switchable' => false,
    'email'      => true,
  ),
  MSG_TYPE_SPY       => array(
    'name'       => 'mnl_spy',
    'switchable' => true,
    'email'      => true,
  ),
  MSG_TYPE_COMBAT    => array(
    'name'       => 'mnl_attaque',
    'switchable' => true,
    'email'      => true,
  ),
  MSG_TYPE_TRANSPORT => array(
    'name'       => 'mnl_transport',
    'switchable' => true,
    'email'      => true,
  ),
  MSG_TYPE_RECYCLE   => array(
    'name'       => 'mnl_exploit',
    'switchable' => true,
    'email'      => true,
  ),
  MSG_TYPE_EXPLORE   => array(
    'name'       => 'mnl_expedition',
    'switchable' => true,
    'email'      => true,
  ),
  //     97 => 'mnl_general',
  MSG_TYPE_QUE       => array(
    'name'       => 'mnl_buildlist',
    'switchable' => true,
    'email'      => true,
  ),
  MSG_TYPE_OUTBOX    => array(
    'name'       => 'mnl_outbox',
    'switchable' => false,
    'email'      => false,
  ),
);

$sn_message_groups = array(
  'switchable' => array(MSG_TYPE_SPY, MSG_TYPE_COMBAT, MSG_TYPE_RECYCLE, MSG_TYPE_TRANSPORT, MSG_TYPE_EXPLORE, MSG_TYPE_QUE),
  'email'      => array(MSG_TYPE_SPY, MSG_TYPE_PLAYER, MSG_TYPE_ALLIANCE, MSG_TYPE_COMBAT, MSG_TYPE_RECYCLE, MSG_TYPE_TRANSPORT,
    MSG_TYPE_ADMIN, MSG_TYPE_EXPLORE, MSG_TYPE_QUE),
);

// Default user option list as 'option_name' => 'option_list'
$user_option_list = array();

$user_option_list[OPT_MESSAGE] = array();
foreach ($sn_message_class_list as $message_class_id => $message_class_data) {
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
  'opt_int_overview_planet_columns' => 'integer',
  'opt_int_overview_planet_rows'    => 'integer',
);

$sn_diplomacy_relation_list = array(
  ALLY_DIPLOMACY_NEUTRAL => array(
    'relation_id' => ALLY_DIPLOMACY_NEUTRAL,
    'enter_delay' => 0,
    'exit_delay'  => 0,
  ),
  ALLY_DIPLOMACY_WAR     => array(
    'relation_id' => ALLY_DIPLOMACY_WAR,
    'enter_delay' => $config->fleet_bashing_war_delay,
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

$sn_data += [
  UNIT_PLAYER_EMPIRE_SPY => [
    'type'     => UNIT_INTERNAL,
    'location' => LOC_USER,
//    P_BONUS_TYPE => BONUS_ADD,
  ],
  UNIT_FLEET_PLANET_SPY  => [
    'type'     => UNIT_INTERNAL,
    'location' => LOC_USER,
//    P_BONUS_TYPE => BONUS_ADD,
  ],


  RES_METAL       => [
    'name'       => 'metal',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_PLANET,
    P_BONUS_TYPE => BONUS_ABILITY,
    P_STACKABLE  => true,
  ],
  RES_CRYSTAL     => [
    'name'       => 'crystal',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_PLANET,
    P_BONUS_TYPE => BONUS_ABILITY,
    P_STACKABLE  => true,
  ],
  RES_DEUTERIUM   => [
    'name'       => 'deuterium',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_PLANET,
    P_BONUS_TYPE => BONUS_ABILITY,
    P_STACKABLE  => true,
  ],
  RES_ENERGY      => [
    'name'       => 'energy',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_PLANET,
    P_BONUS_TYPE => BONUS_ABILITY,
    P_STACKABLE  => true,
  ],
  RES_DARK_MATTER => [
    'name'       => 'dark_matter',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_USER,
    P_BONUS_TYPE => BONUS_ABILITY,
    P_STACKABLE  => true,
  ],
  RES_METAMATTER  => [
    'name'       => 'metamatter',
    'type'       => UNIT_RESOURCES,
    'location'   => LOC_USER,
    P_BONUS_TYPE => BONUS_ABILITY,
    P_STACKABLE  => true,
  ],

  UNIT_SECTOR => [
    'name'       => 'field_max',
    'type'       => UNIT_SECTOR,
    'location'   => LOC_PLANET,
    'cost'       => [
      RES_DARK_MATTER => 1000,
      'factor'        => 1.01,
    ],
    P_BONUS_TYPE => BONUS_ABILITY,
  ],

  UNIT_PLANET_DENSITY => [
    'name'       => 'density',
    'type'       => UNIT_PLANET_DENSITY,
    'location'   => LOC_PLANET,
    'cost'       => [
      RES_DARK_MATTER => 2000,
      'factor'        => 1,
    ],
    P_BONUS_TYPE => BONUS_ABILITY,
  ],

  UNIT_GROUP => [
    // Missions
    /*
    mission = array(
    'DESTINATION' => EMPTY/SAME/PLAYER/ALLY
    'ONE_WAY' => true/false, // Is it mission one-way like Relocate/Colonize?
    'DURATION' => array(duration list  in  second)/false,  //  List  of  possible durations
    'AGGRESIVE' => true/false, // Should aggresive trigger rise?
    'AJAX' => true/false, // Is mission can be launch via ajax?
    'REQUIRE' => array( // requirements for mission. Empty = any unit from sn_get_groups('fleet')
      <any unit_id> => 0 // require any number
      <any unit_id> => <number> // require at least <number>
    ),
    );
    */
    'missions' => [
      MT_ATTACK => [
        'dst_planet' => 1,
        'dst_user'   => 1,
        'dst_fleets' => 1,
        'src_planet' => 1,
        'src_user'   => 1,
        'transport'  => false,
      ],

      MT_AKS => [
        'dst_planet' => 1,
        'dst_user'   => 1,
        'dst_fleets' => 1,
        'src_planet' => 1,
        'src_user'   => 1,
        'transport'  => false,
      ],

      MT_DESTROY => [
        'dst_planet' => 1,
        'dst_user'   => 1,
        'dst_fleets' => 1,
        'src_planet' => 1,
        'src_user'   => 1,
        'transport'  => false,
      ],

      MT_SPY => [
        'dst_user'   => 1,
        'dst_planet' => 1,
        'src_user'   => 1,
        'src_planet' => 1,
        'transport'  => false,
        'AJAX'       => true,
      ],

      MT_HOLD => [
        'dst_planet' => 0,
        'dst_user'   => 0,
        'src_planet' => 0,
        'src_user'   => 0,
        'transport'  => false,
      ],


      MT_TRANSPORT => [
        'dst_planet' => 1,
        'dst_user'   => 0,
        'src_planet' => 1,
        'src_user'   => 0,
        'transport'  => true,
      ],

      MT_RELOCATE => [
        'dst_planet' => 1,
        'dst_user'   => 0,
        'src_planet' => 1,
        'src_user'   => 0,
        'transport'  => true,
      ],

      MT_RECYCLE => [
        'dst_planet' => 1,
        'dst_user'   => 0,
        'src_planet' => 0,
        'src_user'   => 0,
        'transport'  => false,
        'AJAX'       => true,
      ],

      MT_EXPLORE => [
        'dst_planet' => 0,
        'dst_user'   => 0,
        'src_planet' => 0,
        'src_user'   => 1,
        'transport'  => false,
      ],

      MT_COLONIZE => [
        'dst_planet' => 1,
        'dst_user'   => 0,
        'src_planet' => 0,
        'src_user'   => 1,
        'transport'  => true,
      ],

      MT_MISSILE => [
        'src_planet' => 0,
        'src_user'   => 0,
        'dst_planet' => 0,
        'dst_user'   => 0,
        'transport'  => false,
        'AJAX'       => true,
      ],
    ],

    GROUP_MISSION_EXPLORE_OUTCOMES => [
      FLT_EXPEDITION_OUTCOME_NONE            => [
        P_MISSION_EXPEDITION_OUTCOME      => FLT_EXPEDITION_OUTCOME_NONE,
        P_MISSION_EXPEDITION_OUTCOME_TYPE => FLT_EXPEDITION_OUTCOME_TYPE_NEUTRAL,
        P_CHANCE                          => 200,
      ],
      FLT_EXPEDITION_OUTCOME_LOST_FLEET      => [
        P_MISSION_EXPEDITION_OUTCOME      => FLT_EXPEDITION_OUTCOME_LOST_FLEET,
        P_MISSION_EXPEDITION_OUTCOME_TYPE => FLT_EXPEDITION_OUTCOME_TYPE_BAD,
        P_CHANCE                          => 9,
      ],
      FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL  => [
        P_MISSION_EXPEDITION_OUTCOME      => FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL,
        P_MISSION_EXPEDITION_OUTCOME_TYPE => FLT_EXPEDITION_OUTCOME_TYPE_BAD,
        P_CHANCE                          => 3,
      ],
      FLT_EXPEDITION_OUTCOME_FOUND_FLEET     => [
        P_MISSION_EXPEDITION_OUTCOME           => FLT_EXPEDITION_OUTCOME_FOUND_FLEET,
        P_MISSION_EXPEDITION_OUTCOME_TYPE      => FLT_EXPEDITION_OUTCOME_TYPE_GOOD,
        P_CHANCE                               => 200,
        'percent'                              => [0 => 0.1, 1 => 0.02, 2 => 0.01,],
        P_MISSION_EXPEDITION_OUTCOME_SECONDARY => [
          [P_CHANCE => 90, P_MULTIPLIER => 0.01, P_MESSAGE_ID => 2,],
          [P_CHANCE => 9, P_MULTIPLIER => 0.02, P_MESSAGE_ID => 1,],
          [P_CHANCE => 1, P_MULTIPLIER => 0.10, P_MESSAGE_ID => 0,],
        ],
      ],
      FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES => [
        P_MISSION_EXPEDITION_OUTCOME           => FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES,
        P_MISSION_EXPEDITION_OUTCOME_TYPE      => FLT_EXPEDITION_OUTCOME_TYPE_GOOD,
        P_CHANCE                               => 300,
        'percent'                              => [0 => 0.1, 1 => 0.050, 2 => 0.025,],
        P_MISSION_EXPEDITION_OUTCOME_SECONDARY => [
          [P_CHANCE => 90, P_MULTIPLIER => 0.025, P_MESSAGE_ID => 2,],
          [P_CHANCE => 9, P_MULTIPLIER => 0.050, P_MESSAGE_ID => 1,],
          [P_CHANCE => 1, P_MULTIPLIER => 0.100, P_MESSAGE_ID => 0,],
        ],
      ],
      FLT_EXPEDITION_OUTCOME_FOUND_DM        => [
        P_MISSION_EXPEDITION_OUTCOME           => FLT_EXPEDITION_OUTCOME_FOUND_DM,
        P_MISSION_EXPEDITION_OUTCOME_TYPE      => FLT_EXPEDITION_OUTCOME_TYPE_GOOD,
        P_CHANCE                               => 100,
        'percent'                              => [0 => 0.0100, 1 => 0.0040, 2 => 0.0010,],
        P_MISSION_EXPEDITION_OUTCOME_SECONDARY => [
          [P_CHANCE => 90, P_MULTIPLIER => 0.0010, /*P_MESSAGE_ID => 2,*/],
          [P_CHANCE => 9, P_MULTIPLIER => 0.0040, /*P_MESSAGE_ID => 1,*/],
          [P_CHANCE => 1, P_MULTIPLIER => 0.0100, /*P_MESSAGE_ID => 0,*/],
        ],
      ],
      /*
      FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT => array(
        'outcome' => FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT,
        'chance' => 10,
      ),
      */
    ],

    GROUP_DESIGN_OPTION_BLOCKS => [
      GROUP_DESIGN_BLOCK_TUTORIAL      => [],
      GROUP_DESIGN_BLOCK_FLEET_COMPOSE => [],
      GROUP_DESIGN_BLOCK_UNIVERSE      => [],
      GROUP_DESIGN_BLOCK_NAVBAR        => [],
      GROUP_DESIGN_BLOCK_RESOURCEBAR   => [],
      GROUP_DESIGN_BLOCK_PLANET_SORT   => [],
      GROUP_DESIGN_BLOCK_COMMON_ONE    => [],
      GROUP_DESIGN_BLOCK_COMMON_TWO    => [],
    ],

    'planet_images' => [
      'trocken'    => ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'],
      'dschjungel' => ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'],
      'normaltemp' => ['01', '02', '03', '04', '05', '06', '07'],
      'wasser'     => ['01', '02', '03', '04', '05', '06', '07', '08', '09'],
      'eis'        => ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'],
    ],

    'planet_generator' => [
      0  => [ // HomeWorld
        't_max_min'     => 40, // Tmax 40
        't_max_max'     => 40,
        't_delta_min'   => 40, // Tmin 0
        't_delta_max'   => 40,
        'size_min'      => $config->initial_fields,
        'size_max'      => $config->initial_fields,
        'core_types'    => [PLANET_DENSITY_STANDARD,],
        'planet_images' => ['normaltemp'],
      ],
      1  => [
        't_max_min'     => 100,
        't_max_max'     => 150,
        't_delta_min'   => 5,
        't_delta_max'   => 20,
        'size_min'      => 50,
        'size_max'      => 150,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,

          //PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['trocken'],
      ],
      2  => [
        't_max_min'     => 90,
        't_max_max'     => 145,
        't_delta_min'   => 5,
        't_delta_max'   => 25,
        'size_min'      => 80,
        'size_max'      => 180,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,

          // PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['trocken'],
      ],
      3  => [
        't_max_min'     => 70,
        't_max_max'     => 135,
        't_delta_min'   => 5,
        't_delta_max'   => 30,
        'size_min'      => 100,
        'size_max'      => 210,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['trocken'],
      ],
      4  => [
        't_max_min'     => 40,
        't_max_max'     => 110,
        't_delta_min'   => 10,
        't_delta_max'   => 35,
        'size_min'      => 130,
        'size_max'      => 240,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['dschjungel'],
      ],
      5  => [
        't_max_min'     => 25,
        't_max_max'     => 100,
        't_delta_min'   => 10,
        't_delta_max'   => 40,
        'size_min'      => 170,
        'size_max'      => 270,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['dschjungel'],
      ],
      6  => [
        't_max_min'     => 15,
        't_max_max'     => 95,
        't_delta_min'   => 10,
        't_delta_max'   => 45,
        'size_min'      => 220,
        'size_max'      => 300,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['dschjungel'],
      ],
      7  => [
        't_max_min'     => 5,
        't_max_max'     => 90,
        't_delta_min'   => 20,
        't_delta_max'   => 50,
        'size_min'      => 200,
        'size_max'      => 280,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['normaltemp'],
      ],
      8  => [
        't_max_min'     => 0,
        't_max_max'     => 80,
        't_delta_min'   => 20,
        't_delta_max'   => 45,
        'size_min'      => 160,
        'size_max'      => 250,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['normaltemp'],
      ],
      9  => [
        't_max_min'     => -10,
        't_max_max'     => 65,
        't_delta_min'   => 18,
        't_delta_max'   => 40,
        'size_min'      => 120,
        'size_max'      => 210,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['normaltemp'],
      ],
      10 => [
        't_max_min'     => -20,
        't_max_max'     => 50,
        't_delta_min'   => 15,
        't_delta_max'   => 35,
        'size_min'      => 170,
        'size_max'      => 240,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['wasser'],
      ],
      11 => [
        't_max_min'     => -32,
        't_max_max'     => 36,
        't_delta_min'   => 12,
        't_delta_max'   => 30,
        'size_min'      => 110,
        'size_max'      => 230,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['wasser'],
      ],
      12 => [
        't_max_min'     => -45,
        't_max_max'     => 20,
        't_delta_min'   => 10,
        't_delta_max'   => 25,
        'size_min'      => 90,
        'size_max'      => 190,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, // PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, PLANET_DENSITY_CRYSTAL_RAW,
          // PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['wasser'],
      ],
      13 => [
        't_max_min'     => -55,
        't_max_max'     => 5,
        't_delta_min'   => 8,
        't_delta_max'   => 20,
        'size_min'      => 80,
        'size_max'      => 170,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, // PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, PLANET_DENSITY_CRYSTAL_RAW,
          // PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['eis'],
      ],
      14 => [
        't_max_min'     => -60,
        't_max_max'     => 0,
        't_delta_min'   => 5,
        't_delta_max'   => 15,
        'size_min'      => 70,
        'size_max'      => 150,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          // PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['eis'],
      ],
      15 => [
        't_max_min'     => -65,
        't_max_max'     => -5,
        't_delta_min'   => 2,
        't_delta_max'   => 10,
        'size_min'      => 60,
        'size_max'      => 130,
        'core_types'    => [
          PLANET_DENSITY_STANDARD,
          PLANET_DENSITY_ICE_WATER, PLANET_DENSITY_ICE_METHANE, PLANET_DENSITY_ICE_HYDROGEN,
          PLANET_DENSITY_CRYSTAL_STONE, // PLANET_DENSITY_CRYSTAL_SILICATE, // PLANET_DENSITY_CRYSTAL_RAW,
          // PLANET_DENSITY_METAL_ORE, // PLANET_DENSITY_METAL_PERIDOT, // PLANET_DENSITY_METAL_RAW,
        ],
        'planet_images' => ['eis'],
      ],
      16 => [ // Random planet - stranger; -35 avg
        't_max_min'     => -90,
        't_max_max'     => +40,
        't_delta_min'   => 2,
        't_delta_max'   => 50,
        'size_min'      => 30,
        'size_max'      => 330,
        'core_types'    => [
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
        ],
        'planet_images' => ['trocken', 'dschjungel', 'normaltemp', 'wasser', 'eis',],
      ],
    ],

    'planet_density' => [
      PLANET_DENSITY_NONE => [
        UNIT_PLANET_DENSITY               => 250,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_NONE,
        UNIT_PLANET_DENSITY_RARITY        => 0,
        UNIT_RESOURCES                    => [
          RES_METAL     => 0.10,
          RES_CRYSTAL   => 0.10,
          RES_DEUTERIUM => 1.30
        ],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
      ],

      PLANET_DENSITY_ICE_HYDROGEN => [
        UNIT_PLANET_DENSITY               => 750,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_ICE_HYDROGEN,
        UNIT_PLANET_DENSITY_RARITY        => 30, // 1, // 40.00, // * 1/121 0,82645
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_PERFECT,
        UNIT_RESOURCES                    => [RES_METAL => 0.20, RES_CRYSTAL => 0.60, RES_DEUTERIUM => 7.10,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 150,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 11,
      ],
      PLANET_DENSITY_ICE_METHANE  => [
        UNIT_PLANET_DENSITY               => 1250,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_ICE_METHANE,
        UNIT_PLANET_DENSITY_RARITY        => 130, // 6, // 6.67, // * 6,0	4,95868
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_GOOD,
        UNIT_RESOURCES                    => [RES_METAL => 0.55, RES_CRYSTAL => 0.85, RES_DEUTERIUM => 4.60,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 200,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 6,
      ],
      PLANET_DENSITY_ICE_WATER    => [
        UNIT_PLANET_DENSITY               => 2000,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_ICE_WATER,
        UNIT_PLANET_DENSITY_RARITY        => 450, //20, // 2.00, // * 20,0	16,52893
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_AVERAGE,
        UNIT_RESOURCES                    => [RES_METAL => 0.86, RES_CRYSTAL => 0.95, RES_DEUTERIUM => 2.20,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
      ],

      PLANET_DENSITY_CRYSTAL_RAW      => [
        UNIT_PLANET_DENSITY               => 2500,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_CRYSTAL_RAW,
        UNIT_PLANET_DENSITY_RARITY        => 20, // 1, // 40.00, // *1,0	0,82645
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_PERFECT,
        UNIT_RESOURCES                    => [RES_METAL => 0.40, RES_CRYSTAL => 12.37, RES_DEUTERIUM => 0.50,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 150,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 11,
      ],
      PLANET_DENSITY_CRYSTAL_SILICATE => [
        UNIT_PLANET_DENSITY               => 3500,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_CRYSTAL_SILICATE,
        UNIT_PLANET_DENSITY_RARITY        => 140, // 5.71, // * 7,0	5,78512
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_GOOD,
        UNIT_RESOURCES                    => [RES_METAL => 0.67, RES_CRYSTAL => 4.50, RES_DEUTERIUM => 0.85,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 200,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 6,
      ],
      PLANET_DENSITY_CRYSTAL_STONE    => [
        UNIT_PLANET_DENSITY               => 4750,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_CRYSTAL_STONE,
        UNIT_PLANET_DENSITY_RARITY        => 500, // 1.90, // * 21,0	17,35537
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_AVERAGE,
        UNIT_RESOURCES                    => [RES_METAL => 0.80, RES_CRYSTAL => 2.00, RES_DEUTERIUM => 0.95,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
      ],

      PLANET_DENSITY_STANDARD => [
        UNIT_PLANET_DENSITY               => 5750,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_STANDARD,
        UNIT_PLANET_DENSITY_RARITY        => 1000, // 1.0, // * 40,0	33,05785
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_NORMAL,
        UNIT_RESOURCES                    => [RES_METAL => 1.00, RES_CRYSTAL => 1.00, RES_DEUTERIUM => 1.00,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
      ],

      PLANET_DENSITY_METAL_ORE     => [
        UNIT_PLANET_DENSITY               => 7000,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_METAL_ORE,
        UNIT_PLANET_DENSITY_RARITY        => 550, // 2.11, // * 19,0	15,70248
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_AVERAGE,
        UNIT_RESOURCES                    => [RES_METAL => 1.60, RES_CRYSTAL => 0.90, RES_DEUTERIUM => 0.80,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 999,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 0,
      ],
      PLANET_DENSITY_METAL_PERIDOT => [
        UNIT_PLANET_DENSITY               => 8250,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_METAL_PERIDOT,
        UNIT_PLANET_DENSITY_RARITY        => 120, // 8.00, // * 5,0	4,13223
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_GOOD,
        UNIT_RESOURCES                    => [RES_METAL => 4.71, RES_CRYSTAL => 0.80, RES_DEUTERIUM => 0.55,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 200,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 6,
      ],
      PLANET_DENSITY_METAL_RAW     => [
        UNIT_PLANET_DENSITY               => 9500,
        UNIT_PLANET_DENSITY_INDEX         => PLANET_DENSITY_METAL_RAW,
        UNIT_PLANET_DENSITY_RARITY        => 25, // 40.00, // * 1,0	0,82645
        UNIT_PLANET_DENSITY_RICHNESS      => PLANET_DENSITY_RICHNESS_PERFECT,
        UNIT_RESOURCES                    => [RES_METAL => 8.00, RES_CRYSTAL => 0.40, RES_DEUTERIUM => 0.25,],
        UNIT_PLANET_DENSITY_MAX_SECTORS   => 150,
        UNIT_PLANET_DENSITY_MIN_ASTROTECH => 11,
      ],
    ],

    'planet_density_old' => [
      PLANET_DENSITY_NONE             => [
        UNIT_PLANET_DENSITY        => 1000,
        UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_NONE,
        UNIT_PLANET_DENSITY_RARITY => 0,
        UNIT_RESOURCES             => [
          RES_METAL     => 0.10,
          RES_CRYSTAL   => 0.10,
          RES_DEUTERIUM => 1.30
        ],
      ],
      PLANET_DENSITY_ICE_WATER        => [
        UNIT_PLANET_DENSITY        => 2000,
        UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_ICE_WATER,
        UNIT_PLANET_DENSITY_RARITY => 23.4,
        UNIT_RESOURCES             => [
          RES_METAL     => 0.30,
          RES_CRYSTAL   => 0.20,
          RES_DEUTERIUM => 1.20
        ],
      ],
      PLANET_DENSITY_CRYSTAL_SILICATE => [
        UNIT_PLANET_DENSITY        => 3250,
        UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_CRYSTAL_SILICATE,
        UNIT_PLANET_DENSITY_RARITY => 4.1,
        UNIT_RESOURCES             => [
          RES_METAL     => 0.40,
          RES_CRYSTAL   => 1.40,
          RES_DEUTERIUM => 0.90
        ],
      ],
      PLANET_DENSITY_CRYSTAL_STONE    => [
        UNIT_PLANET_DENSITY        => 4500,
        UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_CRYSTAL_STONE,
        UNIT_PLANET_DENSITY_RARITY => 1.4,
        UNIT_RESOURCES             => [
          RES_METAL     => 0.80,
          RES_CRYSTAL   => 1.25,
          RES_DEUTERIUM => 0.80
        ],
      ],
      PLANET_DENSITY_STANDARD         => [
        UNIT_PLANET_DENSITY        => 5750,
        UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_STANDARD,
        UNIT_PLANET_DENSITY_RARITY => 1,
        UNIT_RESOURCES             => [
          RES_METAL     => 1.00,
          RES_CRYSTAL   => 1.00,
          RES_DEUTERIUM => 1.00
        ],
      ],
      PLANET_DENSITY_METAL_ORE        => [
        UNIT_PLANET_DENSITY        => 7000,
        UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_METAL_ORE,
        UNIT_PLANET_DENSITY_RARITY => 1.5,
        UNIT_RESOURCES             => [
          RES_METAL     => 2.00,
          RES_CRYSTAL   => 0.75,
          RES_DEUTERIUM => 0.75
        ],
      ],
      PLANET_DENSITY_METAL_PERIDOT    => [
        UNIT_PLANET_DENSITY        => 8250,
        UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_METAL_PERIDOT,
        UNIT_PLANET_DENSITY_RARITY => 4.9,
        UNIT_RESOURCES             => [
          RES_METAL     => 3.00,
          RES_CRYSTAL   => 0.50,
          RES_DEUTERIUM => 0.50
        ],
      ],
      PLANET_DENSITY_METAL_RAW        => [
        UNIT_PLANET_DENSITY        => 9250,
        UNIT_PLANET_DENSITY_INDEX  => PLANET_DENSITY_METAL_RAW,
        UNIT_PLANET_DENSITY_RARITY => 31.4,
        UNIT_RESOURCES             => [
          RES_METAL     => 4.00,
          RES_CRYSTAL   => 0.25,
          RES_DEUTERIUM => 0.25
        ],
      ],
    ],

    // Planet structures list
    UNIT_STRUCTURES_STR  => [
      STRUC_MINE_METAL    => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
      STRUC_MINE_SOLAR    => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION,
      STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
      STRUC_LABORATORY    => STRUC_LABORATORY, STRUC_LABORATORY_NANO => STRUC_LABORATORY_NANO,
      STRUC_SILO          => STRUC_SILO,
      STRUC_STORE_METAL   => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
      STRUC_ALLY_DEPOSIT  => STRUC_ALLY_DEPOSIT,
      STRUC_TERRAFORMER   => STRUC_TERRAFORMER,
      STRUC_MOON_STATION  => STRUC_MOON_STATION, STRUC_MOON_PHALANX => STRUC_MOON_PHALANX, STRUC_MOON_GATE => STRUC_MOON_GATE,
    ],
    'build_allow'        => [
      PT_PLANET => [
        STRUC_MINE_METAL    => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
        STRUC_MINE_SOLAR    => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION,
        STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
        STRUC_LABORATORY    => STRUC_LABORATORY, STRUC_LABORATORY_NANO => STRUC_LABORATORY_NANO,
        STRUC_SILO          => STRUC_SILO,
        STRUC_STORE_METAL   => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
        STRUC_ALLY_DEPOSIT  => STRUC_ALLY_DEPOSIT,
        STRUC_TERRAFORMER   => STRUC_TERRAFORMER,
      ],
      PT_MOON   => [
        STRUC_FACTORY_ROBOT => STRUC_FACTORY_ROBOT, STRUC_FACTORY_HANGAR => STRUC_FACTORY_HANGAR, STRUC_FACTORY_NANO => STRUC_FACTORY_NANO,
//        STRUC_STORE_METAL => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
        STRUC_ALLY_DEPOSIT  => STRUC_ALLY_DEPOSIT,
        STRUC_MOON_STATION  => STRUC_MOON_STATION, STRUC_MOON_PHALANX => STRUC_MOON_PHALANX, STRUC_MOON_GATE => STRUC_MOON_GATE,
      ],
    ],
    // List of units that can produce resources
    'factories'          => [
      STRUC_MINE_METAL => STRUC_MINE_METAL, STRUC_MINE_CRYSTAL => STRUC_MINE_CRYSTAL, STRUC_MINE_DEUTERIUM => STRUC_MINE_DEUTERIUM,
      STRUC_MINE_SOLAR => STRUC_MINE_SOLAR, STRUC_MINE_FUSION => STRUC_MINE_FUSION, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR,
    ],
    // List of units that can hold resources
    'storages'           => [
      STRUC_STORE_METAL => STRUC_STORE_METAL, STRUC_STORE_CRYSTAL => STRUC_STORE_CRYSTAL, STRUC_STORE_DEUTERIUM => STRUC_STORE_DEUTERIUM,
    ],

    // Tech list
    'tech'               => [
      TECH_ARMOR           => TECH_ARMOR, TECH_WEAPON => TECH_WEAPON, TECH_SHIELD => TECH_SHIELD,
      TECH_SPY             => TECH_SPY, TECH_COMPUTER => TECH_COMPUTER,
      TECH_ENERGY          => TECH_ENERGY, TECH_LASER => TECH_LASER, TECH_ION => TECH_ION, TECH_PLASMA => TECH_PLASMA, TECH_HYPERSPACE => TECH_HYPERSPACE,
      TECH_ENGINE_CHEMICAL => TECH_ENGINE_CHEMICAL, TECH_ENGINE_ION => TECH_ENGINE_ION, TECH_ENGINE_HYPER => TECH_ENGINE_HYPER,
      // TECH_EXPEDITION => TECH_EXPEDITION, TECH_COLONIZATION => TECH_COLONIZATION,
      TECH_ASTROTECH       => TECH_ASTROTECH,
      TECH_GRAVITON        => TECH_GRAVITON, TECH_RESEARCH => TECH_RESEARCH,
    ],

    // Mercenaries
    'mercenaries'        => [
      MRC_STOCKMAN => MRC_STOCKMAN, MRC_SPY => MRC_SPY, MRC_ACADEMIC => MRC_ACADEMIC,
      MRC_ADMIRAL  => MRC_ADMIRAL, MRC_COORDINATOR => MRC_COORDINATOR, MRC_NAVIGATOR => MRC_NAVIGATOR,
    ],
    // Governors
    'governors'          => [
      MRC_TECHNOLOGIST => MRC_TECHNOLOGIST, MRC_ENGINEER => MRC_ENGINEER, MRC_FORTIFIER => MRC_FORTIFIER
    ],
    // Plans
    'plans'              => [
      UNIT_PLAN_STRUC_MINE_FUSION => UNIT_PLAN_STRUC_MINE_FUSION,
      UNIT_PLAN_SHIP_CARGO_SUPER  => UNIT_PLAN_SHIP_CARGO_SUPER, UNIT_PLAN_SHIP_CARGO_HYPER => UNIT_PLAN_SHIP_CARGO_HYPER,
      UNIT_PLAN_SHIP_DEATH_STAR   => UNIT_PLAN_SHIP_DEATH_STAR, UNIT_PLAN_SHIP_SUPERNOVA => UNIT_PLAN_SHIP_SUPERNOVA,
      UNIT_PLAN_DEF_SHIELD_PLANET => UNIT_PLAN_DEF_SHIELD_PLANET,
    ],

    // Spaceships list
    UNIT_SHIPS_STR   => [
      SHIP_SMALL_FIGHTER_LIGHT => SHIP_SMALL_FIGHTER_LIGHT, SHIP_SMALL_FIGHTER_HEAVY => SHIP_SMALL_FIGHTER_HEAVY,
      SHIP_MEDIUM_DESTROYER    => SHIP_MEDIUM_DESTROYER, SHIP_LARGE_CRUISER => SHIP_LARGE_CRUISER,
      SHIP_LARGE_BOMBER        => SHIP_LARGE_BOMBER, SHIP_LARGE_BATTLESHIP => SHIP_LARGE_BATTLESHIP, SHIP_LARGE_DESTRUCTOR => SHIP_LARGE_DESTRUCTOR,
      SHIP_HUGE_DEATH_STAR     => SHIP_HUGE_DEATH_STAR, SHIP_HUGE_SUPERNOVA => SHIP_HUGE_SUPERNOVA,
      SHIP_CARGO_SMALL         => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
      SHIP_RECYCLER            => SHIP_RECYCLER, SHIP_COLONIZER => SHIP_COLONIZER, SHIP_SPY => SHIP_SPY, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR
    ],
    // Defensive building list
    UNIT_DEFENCE_STR => [UNIT_DEF_TURRET_MISSILE   => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL,
                         UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS,
                         UNIT_DEF_TURRET_ION       => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA,

                         UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET,

                         UNIT_DEF_MISSILE_INTERCEPTOR => UNIT_DEF_MISSILE_INTERCEPTOR, UNIT_DEF_MISSILE_INTERPLANET => UNIT_DEF_MISSILE_INTERPLANET,
    ],

    // Missiles list
    UNIT_DEF_MISSILES_STR => [UNIT_DEF_MISSILE_INTERCEPTOR => UNIT_DEF_MISSILE_INTERCEPTOR, UNIT_DEF_MISSILE_INTERPLANET => UNIT_DEF_MISSILE_INTERPLANET,],

    // Combat units list
    'combat'             => [
      SHIP_CARGO_SMALL         => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
      SHIP_SMALL_FIGHTER_LIGHT => SHIP_SMALL_FIGHTER_LIGHT, SHIP_SMALL_FIGHTER_HEAVY => SHIP_SMALL_FIGHTER_HEAVY,
      SHIP_MEDIUM_DESTROYER    => SHIP_MEDIUM_DESTROYER, SHIP_LARGE_CRUISER => SHIP_LARGE_CRUISER, SHIP_COLONIZER => SHIP_COLONIZER, SHIP_RECYCLER => SHIP_RECYCLER,
      SHIP_SPY                 => SHIP_SPY,
      SHIP_LARGE_BOMBER        => SHIP_LARGE_BOMBER, SHIP_SATTELITE_SOLAR => SHIP_SATTELITE_SOLAR, SHIP_LARGE_DESTRUCTOR => SHIP_LARGE_DESTRUCTOR, SHIP_HUGE_DEATH_STAR => SHIP_HUGE_DEATH_STAR,
      SHIP_LARGE_BATTLESHIP    => SHIP_LARGE_BATTLESHIP, SHIP_HUGE_SUPERNOVA => SHIP_HUGE_SUPERNOVA,
      UNIT_DEF_TURRET_MISSILE  => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL, UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS, UNIT_DEF_TURRET_ION => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA, UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET,
    ],
    // Planet active defense list
    'defense_active'     => [
      UNIT_DEF_TURRET_MISSILE => UNIT_DEF_TURRET_MISSILE, UNIT_DEF_TURRET_LASER_SMALL => UNIT_DEF_TURRET_LASER_SMALL, UNIT_DEF_TURRET_LASER_BIG => UNIT_DEF_TURRET_LASER_BIG, UNIT_DEF_TURRET_GAUSS => UNIT_DEF_TURRET_GAUSS, UNIT_DEF_TURRET_ION => UNIT_DEF_TURRET_ION, UNIT_DEF_TURRET_PLASMA => UNIT_DEF_TURRET_PLASMA, UNIT_DEF_SHIELD_SMALL => UNIT_DEF_SHIELD_SMALL, UNIT_DEF_SHIELD_BIG => UNIT_DEF_SHIELD_BIG, UNIT_DEF_SHIELD_PLANET => UNIT_DEF_SHIELD_PLANET,
    ],
    // Transports
    'flt_transports'     => [
      SHIP_CARGO_SMALL => SHIP_CARGO_SMALL, SHIP_CARGO_BIG => SHIP_CARGO_BIG, SHIP_CARGO_SUPER => SHIP_CARGO_SUPER, SHIP_CARGO_HYPER => SHIP_CARGO_HYPER,
    ],
    // Recyclers
    'flt_recyclers'      => [
      SHIP_RECYCLER => SHIP_RECYCLER,
    ],
    // Spies
    'flt_spies'          => [
      SHIP_SPY => SHIP_SPY,
    ],
    // Anti-Spies
    'flt_spies_anti'     => [
    ],
    // Bombers
    'flt_bombers'        => [
      SHIP_LARGE_BOMBER => SHIP_LARGE_BOMBER,
    ],
    // Colonizers
    'flt_colonizers'     => [
      SHIP_COLONIZER => SHIP_COLONIZER,
    ],

    UNIT_ARTIFACTS_STR => [
      ART_LHC            => ART_LHC, ART_HOOK_SMALL => ART_HOOK_SMALL, ART_HOOK_MEDIUM => ART_HOOK_MEDIUM, ART_HOOK_LARGE => ART_HOOK_LARGE,
      ART_RCD_SMALL      => ART_RCD_SMALL, ART_RCD_MEDIUM => ART_RCD_MEDIUM, ART_RCD_LARGE => ART_RCD_LARGE,
      ART_HEURISTIC_CHIP => ART_HEURISTIC_CHIP, ART_NANO_BUILDER => ART_NANO_BUILDER, // ART_DENSITY_CHANGER => ART_DENSITY_CHANGER,
    ],

    // Resource list
    UNIT_RESOURCES_STR      => [0 => 'metal', 1 => 'crystal', 2 => 'deuterium', 3 => 'dark_matter'],
    // Resources all
    'resources_all'         => [RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_ENERGY => RES_ENERGY, RES_DARK_MATTER => RES_DARK_MATTER, RES_METAMATTER => RES_METAMATTER,],
    // Resources can be produced on planet
    'resources_planet'      => [RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_ENERGY => RES_ENERGY],
    // Resources can be looted from planet
    UNIT_RESOURCES_STR_LOOT => [RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM],
    // Resources that can be tradeable in market trader
    UNIT_RESOURCES_STR_TRADER => [RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_DARK_MATTER => RES_DARK_MATTER],

    // List of data modifiers
    GROUP_MODIFIERS_NAME    => [
      MODIFIER_RESOURCE_CAPACITY   => [
        MRC_STOCKMAN => MRC_STOCKMAN,
      ],
      MODIFIER_RESOURCE_PRODUCTION => [
        MRC_TECHNOLOGIST => MRC_TECHNOLOGIST,
      ],
    ],

    // Resources that can be tradeable in market trader AND be a quest_rewards
    'quest_rewards'      => [RES_METAL => RES_METAL, RES_CRYSTAL => RES_CRYSTAL, RES_DEUTERIUM => RES_DEUTERIUM, RES_DARK_MATTER => RES_DARK_MATTER,],

//      // Ques list
//      'ques' => array(QUE_STRUCTURES, QUE_HANGAR, QUE_RESEARCH),

    'STAT_COMMON' => [STAT_TOTAL => STAT_TOTAL, STAT_FLEET => STAT_FLEET, STAT_TECH => STAT_TECH, STAT_BUILDING => STAT_BUILDING, STAT_DEFENSE => STAT_DEFENSE, STAT_RESOURCE => STAT_RESOURCE,],
    'STAT_PLAYER' => [STAT_RAID_TOTAL => STAT_RAID_TOTAL, STAT_RAID_WON => STAT_RAID_WON, STAT_RAID_LOST => STAT_RAID_LOST, STAT_LVL_BUILDING => STAT_LVL_BUILDING, STAT_LVL_TECH => STAT_LVL_TECH, STAT_LVL_RAID => STAT_LVL_RAID,],

    GROUP_GROUP_ID_TO_NAMES => [
      UNIT_STRUCTURES   => 'structures',
      UNIT_TECHNOLOGIES => 'tech',
      UNIT_SHIPS        => 'fleet',
      UNIT_DEFENCE      => 'defense',
      UNIT_MERCENARIES  => 'mercenaries',
      UNIT_GOVERNORS    => 'governors',
      UNIT_RESOURCES    => 'resources_all',
      UNIT_ARTIFACTS    => 'artifacts',
      UNIT_PLANS        => 'plans',
    ],

    GROUP_CAPITAL_BUILDING_BONUS_GROUPS => ['structures', 'defense', 'fleet',],

    GROUP_UNIT_COMBAT_SORT_ORDER => [
      SHIP_SPY,
      SHIP_SATELLITE_SPUTNIK,

      SHIP_SATTELITE_SOLAR,
      SHIP_SATTELITE_SLOTH,

      SHIP_CARGO_SMALL,
      SHIP_CARGO_BIG,
      SHIP_CARGO_SUPER,
      SHIP_CARGO_GREED,
      SHIP_CARGO_FIREFLY,
      SHIP_CARGO_HYPER,

      SHIP_COLONIZER,
      SHIP_RECYCLER,
      SHIP_RECYCLER_GLUTTONY,
      SHIP_RECYCLER_BURAN,

      SHIP_SMALL_FIGHTER_SOYUZ,
      SHIP_SMALL_FIGHTER_LIGHT,
      SHIP_SMALL_FIGHTER_WRATH,
      SHIP_SMALL_FIGHTER_HEAVY,
      SHIP_SMALL_FIGHTER_ASSAULT,
      SHIP_MEDIUM_TORPEDO_SPIRAL,
      SHIP_MEDIUM_DESTROYER,
      SHIP_MEDIUM_FRIGATE,
      SHIP_MEDIUM_BOMBER_ENVY,
      SHIP_LARGE_CRUISER,
      SHIP_LARGE_BOMBER,
      SHIP_LARGE_BATTLESHIP,
      SHIP_LARGE_BATTLESHIP_PRIDE,
      SHIP_LARGE_DESTRUCTOR,
      SHIP_HUGE_DEATH_STAR,
      SHIP_HUGE_SUPERNOVA,

      SHIP_LARGE_ORBITAL_HEAVY,

      UNIT_DEF_TURRET_MISSILE,
      UNIT_DEF_TURRET_LASER_SMALL,
      UNIT_DEF_TURRET_LASER_BIG,
      UNIT_DEF_TURRET_GAUSS,
      UNIT_DEF_TURRET_ION,
      UNIT_DEF_TURRET_PLASMA,

      UNIT_DEF_SHIELD_SMALL,
      UNIT_DEF_SHIELD_BIG,
      UNIT_DEF_SHIELD_PLANET,
    ],

  ],
];

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
$sn_data[UNIT_GROUP]['all'] = array_merge(
  $sn_data[UNIT_GROUP]['structures'],
  $sn_data[UNIT_GROUP]['tech'],
  $sn_data[UNIT_GROUP]['fleet'],
  $sn_data[UNIT_GROUP]['defense'],
  $sn_data[UNIT_GROUP]['mercenaries']
);

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
//  PERIOD_DAY     => 3,
//  PERIOD_DAY_3   => 2.5,
//  PERIOD_WEEK    => 2.0, // 1.5,
//  PERIOD_WEEK_2  => 1.5, // 1.2,
//  PERIOD_MONTH   => 1,
//  PERIOD_MONTH_2 => 0.9,
//  PERIOD_MONTH_3 => 0.8,
);

global $sn_data_bbCodes;
$sn_data_bbCodes = array(
  AUTH_LEVEL_REGISTERED => array(
    // Prefix sn:// resolves to current server URL
    '#sn://#isU'                                                                        => SN_ROOT_VIRTUAL,
    // news://ID resolves to news BBCode
    '#news:\/\/(\d+)#is'                                                                => "[news=$1]",
    // [news=ID] resolves to link to news
    '#\[news\=(\d+)\]#is'                                                               => "<a href=\"announce.php?id=$1\" class=\"link zero\">news://$1</a>",
    // [ube=ID] resolves to link to battle report
    '#\[ube\=([0-9a-zA-Z]{32})\]#isU'                                                   => "<a href=\"index.php?page=battle_report&cypher=$1\"><span class=\"battle_report_link link\">($1)</span></a>",
    // Battle report's URL from current server also resolves to special link
    "#" . SN_ROOT_VIRTUAL . "index.php?page=battle_report&cypher=([0-9a-zA-Z]{32})#isU" => "<a href=\"index.php?page=battle_report&cypher=$1\"><span class=\"battle_report_link link\">($1)</span></a>",

    '#\[(c|color)=(white|cyan|yellow|green|pink|red|lime|maroon|orange)\](.+)\[/\1\]#isU' => "<span style=\"color: $2\">$3</span>",
    '#\[b\](.+)\[/b\]#isU'                                                                => "<b>$1</b>",
    '#\[i\](.+)\[/i\]#isU'                                                                => "<i>$1</i>",
    '#\[u\](.+)\[/u\]#isU'                                                                => '<span class="underline">$1</span>',
    '#\[s\](.+)\[/s\]#isU'                                                                => '<span class="strike">$1</span>',
  ),

  AUTH_LEVEL_ADMINISTRATOR => array(
    // Plain URL on string start
    "#^((?:ftp|https?|sn|faq)://[^\s\[]+)#i"                => "<a href=\"$1$2\" target=\"_blank\" class=\"link_external\">$1$2</a>",
    // Plain URL in the string
    "#([\s\)\]\}])((?:ftp|https?|sn|faq)://[^\s\[]+)#i"     => "$1<a href=\"$2$3\" target=\"_blank\" class=\"link_external\">$2$3</a>",

    // [urlw=URL]DESCRIPTION[urlw] - opens link in current window
    "#\[urlw=(ft|https?://)(.+)\](.+)\[/urlw\]#isU"         => "<a href=\"$1$2\" class=\"link\">$3</a>",
    // [url=URL]DESCRIPTION[url] - opens link in new window
    '#\[url=(ft|https?://)(.+)\](.+)\[/url\]#isU'           => "<a href=\"$1$2\" target=\"_blank\" class=\"link_external\">$3</a>",
    // Admins can use color codes and special PURPLE color
    '#\[(c|color)=(\#[0-9A-Fa-f]+|purple)\](.+)\[/\1\]#isU' => "<span style=\"color: $2\">$3</span>",
  ),
);

global $sn_data_smiles;
$sn_data_smiles = array(
  AUTH_LEVEL_REGISTERED => array(
    ':)'          => 'smile',
    ':p:'         => 'tongue',
//        ':D'          => 'lol',
    'rofl'        => 'rofl',
    ':wink:'      => 'wink',
    ':clap:'      => 'clapping',
    ':good:'      => 'good',
    ':yu:'        => 'yu',
    ':yahoo:'     => 'yahoo',
    ':diablo:'    => 'diablo',
    ':angel:'     => 'angel',
    ':rose:'      => 'give_rose',
    ':blush:'     => 'blush',
    ':sorry:'     => 'sorry',
    ':cool:'      => 'cool',
    ':cool2:'     => 'dirol',
    ':quote:'     => 'pleasantry',
    ':shout:'     => 'shout',
    ':unknw:'     => 'unknw',
    ':ups:'       => 'pardon',
    ':nea:'       => 'nea',
    ':sarcasm:'   => 'sarcasm',
    ':shok:'      => 'shok',
    ':blink:'     => 'blink',
    ':huh:'       => 'huh',
    ':('          => 'mellow',
    ':sad:'       => 'sad',
    ':c:'         => 'cray',
    ':bad:'       => 'bad',
    ':eye:'       => 'blackeye',
    ':bomb:'      => 'bomb',
    ':crz:'       => 'crazy',
    ':fool:'      => 'fool',
    ':tease:'     => 'tease',
    ':spiteful:'  => 'spiteful',
    ':agr:'       => 'aggressive',
//        ':tratata:'   => 'mill',
    ':wall:'      => 'wall',
    ':suicide:'   => 'suicide',
    ':plushit:'   => 'plushit',
    ':fr:'        => 'friends',
    ':dr:'        => 'drinks',
    ':popcorn:'   => 'popcorn',
    ':coctail:'   => 'coctail',
    ':coffee:'    => 'coffee',
    ':accordion:' => 'accordion',
    ':hmm:'       => 'hmm',
    ':facepalm:'  => 'facepalm',
    ':ban:'       => 'ban',
//        ':bayan:'     => 'bayan',
    ':censored:'  => 'censored',
    ':contract:'  => 'contract',
    ':help:'      => 'help',
//        ':maniac:'    => 'maniac',
    ':panic:'     => 'panic',
    ':poke:'      => 'poke',
    ':pray:'      => 'pray',
    ':whistle:'   => 'whistle',
  ),
);
