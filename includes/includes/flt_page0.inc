<?php
// fleet.php
// @version 2.1 - Rebuilt to use Slider element from jUI. Also shows preliminary information about fleet: speed, consumption etc
// @version 2.0 - Serious optimizations: remove redundant fields etc Also makes compliant with PCG
// @version 1.0
// @copyright 2008 by Chlorel for XNova

if($fleet_ship_sort = sys_get_param_id('fleet_ship_sort')) {
  if(!empty($lang['player_option_fleet_ship_sort'][$fleet_ship_sort])) {
    // player_save_option($user, PLAYER_OPTION_FLEET_SHIP_SORT, $fleet_ship_sort);
    // player_save_option($user, PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE, sys_get_param_id('fleet_ship_sort_inverse', 0));
    classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT] = $fleet_ship_sort;
    classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE] = sys_get_param_id('fleet_ship_sort_inverse', 0);
  }
  die();
}

if(SN_IN_FLEET !== true) {
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

lng_include('overview');

if(!$planetrow) {
  message($lang['fl_noplanetrow'], $lang['fl_error']);
}

$template = gettemplate('fleet0', true);

$ship_list = array();
foreach(sn_get_groups('fleet') as $n => $unit_id) {
  // $n_name = get_unit_param($unit_id, P_NAME);
  $unit_level = mrc_get_level($user, $planetrow, $unit_id, false, true);
  if($unit_level > 0) {
    $ship_data = get_ship_data($unit_id, $user);
    $ship_list[$unit_id] = array(
      'ID'          => $unit_id,
      'NAME'        => $lang['tech'][$unit_id],
      'AMOUNT'      => $unit_level,
      'AMOUNT_TEXT' => pretty_number($unit_level),
      'CONSUMPTION' => $ship_data['consumption'],
      'SPEED'       => $ship_data['speed'],
      'SPEED_TEXT'  => pretty_number($ship_data['speed']),
      'CAPACITY'    => $ship_data['capacity'],
    );
  }
}

// $fleet_ship_sort = player_load_option($user, PLAYER_OPTION_FLEET_SHIP_SORT);
// $fleet_ship_sort_inverse = player_load_option($user, PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE);
$fleet_ship_sort = classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT];
$fleet_ship_sort_inverse = classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE];
if($fleet_ship_sort && $fleet_ship_sort != PLAYER_OPTION_FLEET_SHIP_SORT_DEFAULT) {
  switch($fleet_ship_sort) {
    case PLAYER_OPTION_FLEET_SHIP_SORT_NAME: $fleet_ship_sort_field = 'NAME'; break;
    case PLAYER_OPTION_FLEET_SHIP_SORT_SPEED: $fleet_ship_sort_field = 'SPEED'; break;
    case PLAYER_OPTION_FLEET_SHIP_SORT_COUNT: $fleet_ship_sort_field = 'AMOUNT'; break;
    case PLAYER_OPTION_FLEET_SHIP_SORT_ID: $fleet_ship_sort_field = 'ID'; break;
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

//// Оригинальный, по ID, по названию, по скорости, по количеству
//define('PLAYER_OPTION_FLEET_SHIP_SORT_DEFAULT', 1);
//define('PLAYER_OPTION_FLEET_SHIP_SORT_NAME', 2);
//define('PLAYER_OPTION_FLEET_SHIP_SORT_SPEED', 3);
//define('PLAYER_OPTION_FLEET_SHIP_SORT_COUNT', 4);
//define('PLAYER_OPTION_FLEET_SHIP_SORT_ID', 5);

$template->assign_vars(array(
  'TIME_NOW'           => SN_TIME_NOW,

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

  'target_mission'     => $target_mission,
  'MISSION_NAME'		  => $lang['type_mission'][$target_mission],
  'TYPE_NAME'		     => $lang['fl_planettype'][$planet_type],

  'ShipList'           => $ShipList,

  'speed_factor'       => flt_server_flight_speed_multiplier(),

  'PLANET_RESOURCES'   => pretty_number($planetrow['metal'] + $planetrow['crystal'] + $planetrow['deuterium']),
  'PLANET_DEUTERIUM'   => pretty_number($planetrow['deuterium']),
));

$template->assign_recursive($template_result);
display($template, $lang['fl_title']);
