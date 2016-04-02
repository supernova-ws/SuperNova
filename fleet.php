<?php

define('SN_IN_FLEET', true);
define('SN_RENDER_NAVBAR_PLANET', true);

include('common.' . substr(strrchr(__FILE__, '.'), 1));

// TODO - Переместить это куда-нибудь
$fleet_page = sys_get_param_int('fleet_page', sys_get_param_int('mode'));
if($fleet_ship_sort = sys_get_param_id('sort_elements') && $fleet_page == 0) {
  define('IN_AJAX', true);
  if(!empty(classLocale::$lang['player_option_fleet_ship_sort'][$fleet_ship_sort])) {
    // player_save_option($user, PLAYER_OPTION_FLEET_SHIP_SORT, $fleet_ship_sort);
    // player_save_option($user, PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE, sys_get_param_id('fleet_ship_sort_inverse', 0));
    classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT] = $fleet_ship_sort;
    classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE] = sys_get_param_id('sort_elements_inverse', 0);
  }
  die();
}

global $template_result, $user, $planetrow;
$template_result = !empty($template_result) && is_array($template_result) ? $template_result : array();


require_once('includes/includes/flt_functions.php');

lng_include('fleet');

//$galaxy = sys_get_param_int('galaxy', $planetrow['galaxy']);
//$system = sys_get_param_int('system', $planetrow['system']);
//$planet = sys_get_param_int('planet', $planetrow['planet']);

$targetVector = new Vector(VECTOR_READ_PARAMS, $planetrow);
$target_mission = sys_get_param_int('target_mission', MT_NONE);
$ships = sys_get_param_array('ships');
$fleet_group_mr = sys_get_param_id('fleet_group');
$speed_percent = sys_get_param_int('speed', 10);


// Инициализируем объекты значениями по умолчанию
$objFleet5 = new Fleet();
$objFleet5->initDefaults($user, $planetrow, $targetVector, $target_mission, $ships, $fleet_group_mr);


// TODO
//if($target_mission == MT_COLONIZE || $target_mission == MT_EXPLORE) {
//  $planet_type = PT_PLANET;
//} elseif($target_mission == MT_RECYCLE) {
//  $planet_type = PT_DEBRIS;
//} elseif($target_mission == MT_DESTROY) {
//  $planet_type = PT_MOON;
//} else {
//  $planet_type = sys_get_param_int('planet_type');
//  if(!$planet_type) {
//    $planet_type = sys_get_param_int('planettype', $planetrow['planet_type']);
//  }
//}

// TODO
//$options = array();
//$MaxFleets = $options['fleets_max'] = GetMaxFleets($user);
//
//$FlyingFleets = FleetList::fleet_count_flying($user['id']);
//if($MaxFleets <= $FlyingFleets && $fleet_page && $fleet_page != 4) {
//  message(classLocale::$lang['fl_noslotfree'], classLocale::$lang['fl_error'], "fleet." . PHP_EX, 5);
//}
//
//$MaxExpeditions = get_player_max_expeditons($user);
//if($MaxExpeditions) {
//  $FlyingExpeditions = FleetList::fleet_count_flying($user['id'], MT_EXPLORE);
//} else {
//  $FlyingExpeditions = 0;
//}


switch($fleet_page) {
  case 3:

  case 2:
    $objFleet5->restrictMission();
  // No Break

  case 1:
//    if($galaxy && $system && $planet) {
//      $check_type = $planet_type == PT_MOON ? PT_MOON : PT_PLANET;
//
//      $TargetPlanet = db_planet_by_gspt($galaxy, $system, $planet, $check_type);
//    }

}

$objFleet5->fleetPage0Prepare();

switch($fleet_page) {
  case 1:
    $objFleet5->fleetPage1($planet_type);
  break;

  case 2:
    $objFleet5->fleetPage2($speed_percent);
  break;

  case 3:
    require_once('includes/includes/flt_page3.inc');
    sn_fleet_page3($duration);
  break;

  case 4:
    require('includes/includes/flt_page4.inc');
  break;

  case 5:
    require('includes/includes/flt_page5.inc');
  break;

  default:
    $objFleet5->fleetPage0();
  break;
}
