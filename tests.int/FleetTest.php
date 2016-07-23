<?php

use Vector\Vector;

define('SN_IN_FLEET', true);
define('SN_RENDER_NAVBAR_PLANET', true);

include('../common.' . substr(strrchr(__FILE__, '.'), 1));

// TODO - Переместить это куда-нибудь
$fleet_page = sys_get_param_int('fleet_page', sys_get_param_int('mode'));

global $template_result, $user, $planetrow;
$template_result = !empty($template_result) && is_array($template_result) ? $template_result : array();


require_once('../includes/includes/flt_functions.php');

lng_include('fleet');

$targetVector = new Vector(Vector::READ_PARAMS_FLEET, $planetrow);
$target_mission = sys_get_param_int('target_mission', MT_NONE);
$ships = sys_get_param_array('ships');
$fleet_group_mr = sys_get_param_id('fleet_group');
$speed_percent = sys_get_param_int('speed', 10);

$captainId = sys_get_param_id('captain_id');
// TODO - Missile - targeted unit ID

$resources = array(
  RES_METAL     => max(0, floor(sys_get_param_float('resource0'))),
  RES_CRYSTAL   => max(0, floor(sys_get_param_float('resource1'))),
  RES_DEUTERIUM => max(0, floor(sys_get_param_float('resource2'))),
);


require_once '_tests.php';

$testUser = $user;
$testPlanetRow = $planetrow;
$noResources = array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0);

$testUserVacation = $testUser;
$testUserVacation['vacation'] = SN_TIME_NOW + PERIOD_DAY;

$testData = array(
//array($exceptionCode, $fleet_page,        $user,     $planetrow,    $targetVector,                $target_mission, $ships, $fleet_group_mr, $speed_percent, $missileTarget, $captainId, $resources, $resourcesOnPlanet)

  // Internal Fleet class check for unit type // TODO - may be moved to other class

  // checkOnlyFleetUnits - Unused: Fleet class restricts passing non-ship or non-resource
  array(FLIGHT_SHIPS_UNIT_WRONG, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 20, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 1000, UNIT_DEF_SHIELD_BIG => 1), 0, 10, 0, 0, $noResources, $noResources),

  // checkUnitsPositive - Unused: Unit class does not accept negative unit count
  // TODO - is internal check
  // array(FLIGHT_SHIPS_UNIT_WRONG, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 20, PT_PLANET), MT_NONE, array(SHIP_SMALL_FIGHTER_LIGHT => -1), 0, 10, 0, 0, $noResources, $noResources),

  // checkSpeedPercentOld - Wrong speed_percent (1..10)
  array(FLIGHT_FLEET_SPEED_WRONG, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 1, PT_NONE), MT_EXPLORE, array(), 0, 11, 0, 0, $noResources, $noResources),

  // checkTargetInUniverse
  array(FLIGHT_VECTOR_BEYOND_UNIVERSE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(-PHP_INT_MAX, 1, 1, PT_NONE), MT_EXPLORE, array(), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_VECTOR_BEYOND_UNIVERSE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(PHP_INT_MAX, 1, 1, PT_NONE), MT_EXPLORE, array(), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_VECTOR_BEYOND_UNIVERSE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, -PHP_INT_MAX, 1, PT_NONE), MT_EXPLORE, array(), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_VECTOR_BEYOND_UNIVERSE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, PHP_INT_MAX, 1, PT_NONE), MT_EXPLORE, array(), 0, 10, 0, 0, $noResources, $noResources),

  // checkTargetNotSource
  array(FLIGHT_VECTOR_SAME_SOURCE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector($testPlanetRow['galaxy'], $testPlanetRow['system'], $testPlanetRow['planet'], $testPlanetRow['planet_type']), MT_EXPLORE, array(), 0, 10, 0, 0, $noResources, $noResources),

  // checkSenderNoVacation
  array(FLIGHT_PLAYER_VACATION_OWN, FLEET_PAGE_MISSION, $testUserVacation, $testPlanetRow, new Vector(1, 1, 11, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 1), 0, 10, 0, 0, $noResources, $noResources),


  // checkTargetNoVacation
  array(FLIGHT_PLAYER_VACATION, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 12, PT_PLANET), MT_EXPLORE, array(), 0, 10, 0, 0, $noResources, $noResources),

  // checkFleetNotEmpty
  array(FLIGHT_SHIPS_NO_SHIPS, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 1, PT_NONE), MT_EXPLORE, array(), 0, 10, 0, 0, $noResources, $noResources),

  // checkOnlyFlyingUnits
  array(FLIGHT_SHIPS_UNMOVABLE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 11, PT_PLANET), MT_EXPLORE, array(SHIP_SATTELITE_SOLAR => 1), 0, 10, 0, 0, $noResources, $noResources),

  // 'checkResourcesPositive'     => FLIGHT_RESOURCES_NEGATIVE,
  array(FLIGHT_RESOURCES_NEGATIVE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 11, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 1, RES_DEUTERIUM => -1), 0, 10, 0, 0, $noResources, $noResources),

  // checkNotTooFar
  array(FLIGHT_FLEET_TOO_FAR, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(4, 1, 4, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 1), 0, 10, 0, 0, $noResources, $noResources),

  // checkEnoughCapacity
  array(FLIGHT_FLEET_OVERLOAD, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 11, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 1, RES_DEUTERIUM => 100000), 0, 10, 0, 0, $noResources, $noResources),

  // checkSourceEnoughShips
  array(FLIGHT_SHIPS_NOT_ENOUGH, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 11, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => PHP_INT_MAX), 0, 10, 0, 0, $noResources, $noResources),

  // checkSourceEnoughFuel
  array(FLIGHT_RESOURCES_FUEL_NOT_ENOUGH, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 11, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 10000), 0, 10, 0, 0, $noResources, $noResources),

  // 'checkSourceEnoughResources'
  array(FLIGHT_RESOURCES_NOT_ENOUGH, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 11, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 1000, RES_METAL => $testPlanetRow['metal'] + 10), 0, 10, 0, 0, $noResources, $noResources),

//  TODO 'checkFleetGroupACS'              => array(),


  // TODO 'checkMultiAccountNot'            => FLIGHT_PLAYER_SAME_IP,
  // return $user1['id'] != $user2['id'] && $user1['user_lastip'] == $user2['user_lastip'] && !classSupernova::$config->game_multiaccount_enabled;

  // TODO ''checkEnoughFleetSlots'           => FLIGHT_FLEET_NO_SLOTS,

//  // TODO - THIS CHECKS SHOULD BE ADDED IN UNIT_CAPTAIN MODULE!
//  'checkCaptainSent'                => array(
//    true => array(
//        'checkCaptainExists'        => FLIGHT_CAPTAIN_NOT_HIRED,
//        'checkCaptainOnPlanetType'      => FLIGHT_CAPTAIN_ALREADY_FLYING,
//        'checkCaptainOnPlanetSource' => FLIGHT_CAPTAIN_ON_OTHER_PLANET,
//        'checkCaptainNotRelocating' => FLIGHT_CAPTAIN_RELOCATE_LOCK,
//    ),
//  ),


  // checkNoMissiles
  // Missile
  array(FLIGHT_MISSION_MISSILE_ONLY_ATTACK, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 1, 2, PT_PLANET), MT_MISSILE, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_MISSION_MISSILE_ONLY_ATTACK, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 1, 2, PT_PLANET), MT_MISSILE, array(SHIP_COLONIZER => 1,), 0, 10, 0, 0, $noResources, $noResources),
  // Other missions
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 1, 20, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_COLONIZE, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_RECYCLE, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_TRANSPORT, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_HOLD, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_SPY, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_ATTACK, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_ACS, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),
  array(FLIGHT_SHIPS_NO_MISSILES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_DESTROY, array(SHIP_COLONIZER => 1, UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),

  // MT_EXPLORE checks
  // checkUnKnownSpace
  array(FLIGHT_MISSION_EXPLORE_KNOWN_SPACE, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 1, 2, PT_PLANET), MT_EXPLORE, array(SHIP_SMALL_FIGHTER_LIGHT => 1), 0, 10, 0, 0, $noResources, $noResources),
  // checkNotOnlySpies
  array(FLIGHT_SHIPS_NOT_ONLY_SPIES, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 1, 20, PT_PLANET), MT_EXPLORE, array(SHIP_SPY => 1), 0, 10, 0, 0, $noResources, $noResources),
// TODO 'checkExpeditionsMax'  => FLIGHT_MISSION_EXPLORE_NO_ASTROTECH,
// TODO 'checkExpeditionsFree' => FLIGHT_MISSION_EXPLORE_NO_SLOTS,


  // MT_MISSILE checks
  array(FLIGHT_MISSION_MISSILE_DISABLED, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 1, 2, PT_PLANET), MT_MISSILE, array(UNIT_DEF_MISSILE_INTERPLANET => 1), 0, 10, 0, 0, $noResources, $noResources),

  // MT_COLONIZE checks
  // checkNoMissiles
  array(FLIGHT_SHIPS_NO_COLONIZER, FLEET_PAGE_SEND, $testUser, $testPlanetRow, new Vector(1, 2, 1, PT_PLANET), MT_COLONIZE, array(SHIP_SMALL_FIGHTER_LIGHT => 1), 0, 10, 0, 0, $noResources, $noResources),




//  array(FLIGHT_FLEET_SPEED_WRONG, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 1, PT_NONE), MT_NONE, array(), 0, 10, 0, 0, $noResources, $noResources),

);

// define('DEBUG_FLEET_MISSION_VALIDATE_DUMP_STEPS', true);

function testFleet($exceptionCode, $fleet_page, $user, $planetrow, $targetVector, $target_mission, $ships, $fleet_group_mr, $speed_percent, $missileTarget, $captainId, $resources, $resourcesOnPlanet, $callback = null) {
// Инициализируем объекты значениями по умолчанию
  $objFleet5 = new Fleet();
  try {
    $objFleet5->initDefaults($user, $planetrow, $targetVector, $target_mission, $ships, $fleet_group_mr, $speed_percent, 0, $captainId, $resources);

    switch ($fleet_page) {
//  case FLEET_PAGE_DESTINATION:
//    $objFleet5->fleetPage1();
//  break;
//
      case FLEET_PAGE_MISSION:
        $objFleet5->fleetPage2Prepare($resourcesOnPlanet);
      break;
      case FLEET_PAGE_SEND:
        $objFleet5->fleetPage3Prepare($resourcesOnPlanet);
      break;
//
//  case FLEET_PAGE_SEND:
//    $objFleet5->fleetPage3();
//  break;
//
//  case 4:
//    require('../includes/includes/flt_page4.inc');
//  break;
//
//  case 5:
//    require('../includes/includes/flt_page5.inc');
//  break;
//
//  default:
//    $objFleet5->fleetPage0();
//  break;
    }

    if($exceptionCode !== null) {
      print('<span style="color: red; font-size: 200%;">FAILED! Expected Exception [' . $exceptionCode . ']: "' . classLocale::$lang['fl_attack_error'][$exceptionCode] . '" - FAILED!</span><br />');
      print('<div style="color: red; font-size: 200%;">NO EXCEPTION RAISED</div>');
      pdie();
    } else {
      print('Passed');
    }
  } catch (Exception $e) {
    if ($exceptionCode !== null && $e->getCode() === $exceptionCode) {
//      print('<span style="color: darkgreen;">Exception [' . $exceptionCode . ']: "' . classLocale::$lang['fl_attack_error'][$exceptionCode] . '" - passed</span><br />');
    } else {
      print('<div style="color: red; font-size: 200%;">Expected Exception [' . $exceptionCode . ']: "' . classLocale::$lang['fl_attack_error'][$exceptionCode] . '" - FAILED!</div>');
      print('<div style="color: red; font-size: 200%;">Got Exception [' . $e->getCode() . ']: "' . $e->getMessage() . '"/"' . classLocale::$lang['fl_attack_error'][$e->getCode()] . '" . "</div>');
      throw $e;
    }
  }

  unset($objFleet5);
}

_testProcess('testFleet', $testData);
print('fleet test passed');