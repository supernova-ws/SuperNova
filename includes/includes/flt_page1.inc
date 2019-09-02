<?php

// floten1.php
// @version 1.0
// @copyright 2008 by Chlorel for XNova

use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;

if(SN_IN_FLEET !== true) {
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

$template = SnTemplate::gettemplate('fleet1', true);

$ships = sys_get_param('ships', array());
if(!is_array($ships)) {
  $ships = array();
}

foreach(array(PT_PLANET, PT_DEBRIS, PT_MOON) as $possible_planet_type_id) {
  $template->assign_block_vars('possible_planet_type_id', array(
    'ID' => $possible_planet_type_id,
    'NAME' => $lang['sys_planet_type_sh'][$possible_planet_type_id],
  ));
}

$template_route = array(
  'START_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$planetrow['planet_type']],
  'START_COORDS'       => uni_render_coordinates($planetrow),
  'START_NAME'         => $planetrow['name'],
);

if(!empty($TargetPlanet)) {
  $template_route += array(
    'END_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$TargetPlanet['planet_type']],
    'END_COORDS'       => uni_render_coordinates($TargetPlanet),
    'END_NAME'         => $TargetPlanet['name'],
  );
}

$template->assign_block_vars('fleets', $template_route);

$sn_groups_fleet = sn_get_groups('fleet');
foreach($ships as $ship_id => $ship_count) {
  if(in_array($ship_id, $sn_groups_fleet) && $ship_count) {
    $ship_info = get_unit_param($ship_id);
    if($ship_count > mrc_get_level($user, $planetrow, $ship_id, false, true)) {
      $page .= $lang['fl_noenought'];
    } else {
      $fleet['fleetarray'][$ship_id]  = $ship_count;
      $fleet['amount']               += $ship_count;
    }
    $fleet['capacity'] += $ship_info[P_CAPACITY] * $ship_count;
    $ship_base_data = get_ship_data($ship_id, $user);
    $template->assign_block_vars('fleets.ships', array(
      'ID'          => $ship_id,
      'AMOUNT'      => $ship_count,
      'AMOUNT_TEXT' => HelperString::numberFloorAndFormat($ship_count),
      'CONSUMPTION' => $ship_base_data['consumption'],
      'SPEED'       => $ship_base_data['speed'],
      'NAME'        => $lang['tech'][$ship_id],
    ));
  }
}

if(empty($fleet['fleetarray'])) {
  SnTemplate::messageBox($lang['fl_err_no_ships'], $lang['fl_error'], 'fleet' . DOT_PHP_EX, 5);
}

// Building list of shortcuts
$query = doquery("SELECT * FROM {{notes}} WHERE `owner` = {$user['id']} AND `galaxy` <> 0 AND `system` <> 0 AND `planet` <> 0 ORDER BY `priority` DESC, `galaxy`, `system`, `planet`, `planet_type`;");
while($shortcut = db_fetch($query)) {
  $template->assign_block_vars('shortcut', array(
    'NAME'       => $shortcut['title'],
    'GALAXY'     => $shortcut['galaxy'],
    'SYSTEM'     => $shortcut['system'],
    'PLANET'     => $shortcut['planet'],
    'PRIORITY'   => $shortcut['priority'],
    'PRIORITY_CLASS' => $note_priority_classes[$shortcut['priority']],
    'TYPE'       => $shortcut['planet_type'],
    'TYPE_PRINT' => $lang['fl_shrtcup'][$shortcut['planet_type']],
  ));
}

// Building list of own planets & moons
$colonies = DBStaticPlanet::db_planet_list_sorted ( $user );
if(count($colonies) > 1) {
  // while($row = db_fetch($colonies))
  foreach($colonies as $row) {
    $template->assign_block_vars('colonies', array(
      'NAME'       => $row['name'],
      'GALAXY'     => $row['galaxy'],
      'SYSTEM'     => $row['system'],
      'PLANET'     => $row['planet'],
      'TYPE'       => $row['planet_type'],
      'TYPE_PRINT' => $lang['fl_shrtcup'][$row['planet_type']],
    ));
  }
}

//ACS Start
//Need to look for acs attacks.
$aks_madnessred = DbFleetStatic::dbAcsGetAll();
while($row = db_fetch($aks_madnessred)) {
  $members = explode(',', $row['eingeladen']);
  foreach($members as $a => $b) {
    if ($b == $user['id']) {
      $template->assign_block_vars('acss', array(
        'ID'         => $row['id'],
        'NAME'       => $row['name'],
        'GALAXY'     => $row['galaxy'],
        'SYSTEM'     => $row['system'],
        'PLANET'     => $row['planet'],
        'TYPE'       => $row['planet_type'],
        'TYPE_PRINT' => $lang['fl_shrtcup'][$row['planet_type']],
      ));
    }
  }
}

$template->assign_vars(array(
  'usedfleet'       => str_rot13(base64_encode(json_encode($fleet['fleetarray']))),

  'speed_factor'     => flt_server_flight_speed_multiplier(),
  "t{$planet_type}" => 'SELECTED',

//  'target_mission'  => $target_mission,

//  'ships'           => str_rot13(base64_encode(serialize($ships))),

  'fleet_speed'     => flt_fleet_speed($user, $fleet['fleetarray']),
  'fleet_capacity'  => $fleet['capacity'],

  'PLANET_DEUTERIUM'   => HelperString::numberFloorAndFormat($planetrow['deuterium']),

  'PAGE_HINT'       => $lang['fl_page1_hint'],
));

$template->assign_recursive($template_result);
SnTemplate::display($template, $lang['fl_title']);
