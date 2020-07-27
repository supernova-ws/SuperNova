<?php
// fleet.php
// @version 2.1 - Rebuilt to use Slider element from jUI. Also shows preliminary information about fleet: speed, consumption etc
// @version 2.0 - Serious optimizations: remove redundant fields etc Also makes compliant with PCG
// @version 1.0
// @copyright 2008 by Chlorel for XNova

if($fleet_ship_sort = sys_get_param_id('sort_elements')) {
  define('IN_AJAX', true);
  if(!empty($lang['player_option_fleet_ship_sort'][$fleet_ship_sort])) {
    // player_save_option($user, PLAYER_OPTION_FLEET_SHIP_SORT, $fleet_ship_sort);
    // player_save_option($user, PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE, sys_get_param_id('fleet_ship_sort_inverse', 0));
    SN::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT] = $fleet_ship_sort;
    SN::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE] = sys_get_param_id('sort_elements_inverse', 0);
  }
  die();
}

if(SN_IN_FLEET !== true) {
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

lng_include('overview');

if(!$planetrow) {
  SnTemplate::messageBox($lang['fl_noplanetrow'], $lang['fl_error']);
}

$template = SnTemplate::gettemplate('fleet0', true);

$record_index = 0;
$ship_list = array();
foreach(sn_get_groups('fleet') as $n => $unit_id) {
  $unit_level = mrc_get_level($user, $planetrow, $unit_id, false, true);
  if($unit_level > 0) {
    $ship_data = get_ship_data($unit_id, $user);
    $ship_list[$unit_id] = array(
      '__INDEX'     => $record_index++,
      'ID'          => $unit_id,
      'NAME'        => $lang['tech'][$unit_id],
      'AMOUNT'      => $unit_level,
      'AMOUNT_TEXT' => HelperString::numberFloorAndFormat($unit_level),
      'CONSUMPTION' => $ship_data['consumption'],
      'CONSUMPTION_TEXT' => HelperString::numberFloorAndFormat($ship_data['consumption']),
      'SPEED'       => $ship_data['speed'],
      'SPEED_TEXT'  => HelperString::numberFloorAndFormat($ship_data['speed']),
      'CAPACITY'    => $ship_data['capacity'],
      'CAPACITY_TEXT'    => HelperString::numberFloorAndFormat($ship_data['capacity']),
    );
  }
}

$fleet_ship_sort = SN::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT];
$fleet_ship_sort_inverse = SN::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE];
if($fleet_ship_sort || $fleet_ship_sort_inverse != PLAYER_OPTION_SORT_ORDER_PLAIN) {
  switch($fleet_ship_sort) {
    case PLAYER_OPTION_SORT_NAME: $fleet_ship_sort_field = 'NAME'; break;
    case PLAYER_OPTION_SORT_SPEED: $fleet_ship_sort_field = 'SPEED'; break;
    case PLAYER_OPTION_SORT_COUNT: $fleet_ship_sort_field = 'AMOUNT'; break;
    case PLAYER_OPTION_SORT_ID: $fleet_ship_sort_field = 'ID'; break;
    default: $fleet_ship_sort_field = '__INDEX'; break;
  }
  $fleet_ship_sort_inverse_a = $fleet_ship_sort_inverse ? -1 : 1;
  usort($ship_list, function($a, $b) use ($fleet_ship_sort_field, $fleet_ship_sort_inverse_a) {
    return $a[$fleet_ship_sort_field] < $b[$fleet_ship_sort_field] ? -1 * $fleet_ship_sort_inverse_a : (
      $a[$fleet_ship_sort_field] > $b[$fleet_ship_sort_field] ? 1 * $fleet_ship_sort_inverse_a : 0
    );
  });
}

foreach($ship_list as $ship_data) {
  $template->assign_block_vars('ships', $ship_data);
}

foreach($lang['player_option_fleet_ship_sort'] as $sort_id => $sort_text) {
  $template->assign_block_vars('ship_sort_list', array(
    'VALUE' => $sort_id,
    'TEXT' => $sort_text,
  ));
}

$template->assign_vars(array(
  'FLEET_SHIP_SORT' => $fleet_ship_sort,
  'FLEET_SHIP_SORT_INVERSE' => $fleet_ship_sort_inverse,

  'FLYING_FLEETS'      => $FlyingFleets,
  'MAX_FLEETS'         => $MaxFleets,
  'FLYING_EXPEDITIONS' => $FlyingExpeditions,
  'MAX_EXPEDITIONS'    => $MaxExpeditions,
  'FREE_EXPEDITIONS'   => $MaxExpeditions - $FlyingExpeditions,

  'COLONIES_CURRENT' => get_player_current_colonies($user),
  'COLONIES_MAX' => get_player_max_colonies($user),
  'IS_COLONIZATION' => $target_mission == MT_COLONIZE,

//  'target_mission'     => $target_mission,
//  'MISSION_NAME'		  => $lang['type_mission'][$target_mission],
  'TYPE_NAME'		     => $lang['fl_planettype'][$planet_type],

  'ShipList'           => $ShipList,

  'speed_factor'       => flt_server_flight_speed_multiplier(),

  'PLANET_RESOURCES'   => HelperString::numberFloorAndFormat($planetrow['metal'] + $planetrow['crystal'] + $planetrow['deuterium']),
  'PLANET_DEUTERIUM'   => HelperString::numberFloorAndFormat($planetrow['deuterium']),

  'PLAYER_OPTION_FLEET_SHIP_SELECT_OLD' => SN::$user_options[PLAYER_OPTION_FLEET_SHIP_SELECT_OLD],
  'PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED' => SN::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED],
  'PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY' => SN::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY],
  'PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION' => SN::$user_options[PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION],
));

$template->assign_recursive($template_result);
SnTemplate::display($template, $lang['fl_title']);
