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

$testData = array(
//array($fleet_page,        $user,     $planetrow,    $targetVector,                $target_mission, $ships, $fleet_group_mr, $speed_percent, $missileTarget, $captainId, $resources, $exceptionCode, $resourcesOnPlanet)
  // checkSpeedPercentOld - Wrong speed_percent (1..10)
  array(FLIGHT_FLEET_SPEED_WRONG, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 1, PT_NONE), MT_NONE, array(), 0, 11, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
  // checkTargetInUniverse
  array(FLIGHT_VECTOR_BEYOND_UNIVERSE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(-PHP_INT_MAX, 1, 1, PT_NONE), MT_NONE, array(), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
  array(FLIGHT_VECTOR_BEYOND_UNIVERSE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(PHP_INT_MAX, 1, 1, PT_NONE), MT_NONE, array(), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
  array(FLIGHT_VECTOR_BEYOND_UNIVERSE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, -PHP_INT_MAX, 1, PT_NONE), MT_NONE, array(), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
  array(FLIGHT_VECTOR_BEYOND_UNIVERSE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, PHP_INT_MAX, 1, PT_NONE), MT_NONE, array(), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
  // checkTargetNotSource
  array(FLIGHT_VECTOR_SAME_SOURCE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector($testPlanetRow['galaxy'], $testPlanetRow['system'], $testPlanetRow['planet'], $testPlanetRow['planet_type']), MT_NONE, array(), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
  // TODO 'checkSenderNoVacation'      => FLIGHT_PLAYER_VACATION_OWN,
  // checkTargetNoVacation
  array(FLIGHT_PLAYER_VACATION, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 12, PT_PLANET), MT_NONE, array(), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
  // checkFleetNotEmpty
  array(FLIGHT_SHIPS_NO_SHIPS, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 1, PT_NONE), MT_NONE, array(), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),

//  // 'checkUnitsPositive'
    array(FLIGHT_SHIPS_NEGATIVE, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 11, PT_PLANET), MT_NONE, array(SHIP_CARGO_SMALL => -1), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
//  // 'checkOnlyFleetUnits'        => FLIGHT_SHIPS_UNIT_WRONG,
//  // 'checkOnlyFlyingUnits'       => FLIGHT_SHIPS_UNMOVABLE,
//  // 'checkResourcesPositive'     => FLIGHT_RESOURCES_NEGATIVE,
//  // 'checkNotTooFar'             => FLIGHT_FLEET_TOO_FAR,
//  // 'checkEnoughCapacity'        => FLIGHT_FLEET_OVERLOAD,
//  array(FLIGHT_FLEET_SPEED_WRONG, FLEET_PAGE_MISSION, $testUser, $testPlanetRow, new Vector(1, 1, 1, PT_NONE), MT_NONE, array(), 0, 10, 0, 0, array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0), array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0)),
);

function testFleet($exceptionCode, $fleet_page, $user, $planetrow, $targetVector, $target_mission, $ships, $fleet_group_mr, $speed_percent, $missileTarget, $captainId, $resources, $resourcesOnPlanet) {
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

    print('Passed');
  } catch (Exception $e) {
    if ($exceptionCode !== null && $e->getCode() === $exceptionCode) {
      print('<font color="green">Exception [' . $exceptionCode . ']: "' . classLocale::$lang['fl_attack_error'][$e->getCode()] . '" - passed</font><br />');
    } else {
      print('<font color="red">');
      throw $e;
    }
  }

  unset($objFleet5);
}

_testProcess('testFleet', $testData);
