<?php

define('SN_IN_FLEET', true);
define('SN_RENDER_NAVBAR_PLANET', true);

include('common.' . substr(strrchr(__FILE__, '.'), 1));

// TODO - Переместить это куда-нибудь
$fleet_page = sys_get_param_int('fleet_page', sys_get_param_int('mode'));
if($fleet_ship_sort = sys_get_param_id('sort_elements') && $fleet_page == 0) {
  define('IN_AJAX', true);
  if(!empty(classLocale::$lang['player_option_fleet_ship_sort'][$fleet_ship_sort])) {
    classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT] = $fleet_ship_sort;
    classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE] = sys_get_param_id('sort_elements_inverse', 0);
  }
  die();
}

global $template_result, $user, $planetrow;
$template_result = !empty($template_result) && is_array($template_result) ? $template_result : array();


require_once('includes/includes/flt_functions.php');

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


// Инициализируем объекты значениями по умолчанию
$objFleet5 = new Fleet();
$objFleet5->initDefaults($user, $planetrow, $targetVector, $target_mission, $ships, $fleet_group_mr, $speed_percent, 0, $captainId, $resources);


switch($fleet_page) {
  case 1:
    $objFleet5->fleetPage1();
  break;

  case 2:
    $objFleet5->fleetPage2();
  break;

  case 3:
    $objFleet5->fleetPage3();
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
