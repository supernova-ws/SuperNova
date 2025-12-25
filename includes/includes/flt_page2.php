<?php
// floten2.php
// @version 1.0
// @copyright 2008 by Chlorel for XNova

function sn_fleet_page2() {
  global $missiontype, $target_mission, $fleetarray, $planetrow, $lang;
  global $TargetPlanet, $consumption, $template_result;
  global $user, $config, $is_transport_missions;

  $template = SnTemplate::gettemplate('fleet2', true);

  foreach ($missiontype as $key => $value) {
    $template->assign_block_vars('missions', array(
      'ID' => $key,
//      'NAME' => $value . ($key == MT_COLONIZE ? ' (' . $lang['sys_colonies'] . ' ' . get_player_current_colonies($user) . '/' . get_player_max_colonies($user) . ')' : ''),
      'NAME' => $value,
      'CHECKED' => $target_mission == $key,
    ));
  };

  $fleetarray = is_array($fleetarray) ? $fleetarray : array();

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
  foreach($fleetarray as $ship_id => $ship_count) {
    if(in_array($ship_id, $sn_groups_fleet) && $ship_count) {
//      $ship_base_data = get_ship_data($ship_id, $user);
      $template->assign_block_vars('fleets.ships', array(
        'ID'          => $ship_id,
        'AMOUNT'      => $ship_count,
        'AMOUNT_TEXT' => HelperString::numberFloorAndFormat($ship_count),
//        'CONSUMPTION' => $ship_base_data['consumption'],
//        'SPEED'       => $ship_base_data['speed'],
        'NAME'        => $lang['tech'][$ship_id],
      ));
    }
  }

  $fleet_capacity = 0;
  foreach ($fleetarray as $Ship => $Count) {
    $fleet_capacity += get_unit_param($Ship, P_CAPACITY) * $Count;
  }

  $max_duration = $target_mission == MT_EXPLORE ? get_player_max_expedition_duration($user) :
    (isset($missiontype[MT_HOLD]) ? 12 : 0);
  if($max_duration) {
    $config_game_speed_expedition = ($target_mission == MT_EXPLORE && $config->game_speed_expedition ? $config->game_speed_expedition : 1);
    for($i = 1; $i <= $max_duration; $i++) {
      $template->assign_block_vars('duration', array(
        'ID' => $i,
        'TIME' => pretty_time(ceil($i * 3600 / $config_game_speed_expedition)),
      ));
    }
  }

//  $temp = array(
//    'id' => $TargetPlanet['id'],
//    'galaxy' => $galaxy,
//    'system' => $system,
//    'planet' => $planet,
//    'planet_type' => $planet_type,
//    'name' => $TargetPlanet['name'] ? $TargetPlanet['name'] : $lang['type_mission'][$target_mission],
//  );
  // $TableTitle = uni_render_planet_full($planetrow) . '&nbsp;=&gt;&nbsp;' . uni_render_planet_full($temp);

  $sn_group_resources = sn_get_groups('resources');
  for($i = 0; $i<3; $i++) {
    $amount = $planetrow[$sn_group_resources[$i]] - ($i == 2 ? $consumption : 0);
    $template->assign_block_vars('resources', array(
      'ID' => $i,
      'ON_PLANET' => floor($amount),
      'TEXT' => HelperString::numberFloorAndFormat($amount),
      'NAME' => $lang['sys_' . $sn_group_resources[$i]],
    ));
  }

  if(is_object($captainModule = moduleCaptain()) && ($captain = $captainModule->unit_captain_get($planetrow['id'])) && $captain['unit_location_type'] == LOC_PLANET) {
    $template->assign_vars(array(
      'CAPTAIN_ID' => $captain['unit_id'],
      'CAPTAIN_LEVEL' => $captain['captain_level'],
      'CAPTAIN_SHIELD' => $captain['captain_shield'],
      'CAPTAIN_ARMOR' => $captain['captain_armor'],
      'CAPTAIN_ATTACK' => $captain['captain_attack'],
    ));
  }

  $template->assign_vars(array(
    'planet_metal'     => floor($planetrow['metal']),
    'planet_crystal'   => floor($planetrow['crystal']),
    'planet_deuterium' => floor($planetrow['deuterium'] - $consumption),

    'fleet_capacity'   => $fleet_capacity - $consumption,
    'usedfleet'        => $_POST['usedfleet'],

    // 'TableTitle' => $TableTitle,


    'speedallsmin' => sys_get_param_float('speedallsmin'),
    'speed' => sys_get_param_int('speed') ,

    'fleet_group' => sys_get_param_id('fleet_group') ,
    'acs_target_mr' => sys_get_param_str('acs_target_mr'),

    'MAX_DURATION' => $max_duration,

    'IS_TRANSPORT_MISSIONS' => $is_transport_missions,

    'PLAYER_COLONIES_CURRENT' => get_player_current_colonies($user),
    'PLAYER_COLONIES_MAX' => get_player_max_colonies($user),
  ));

  $template->assign_recursive($template_result);
  SnTemplate::display($template, $lang['fl_title']);
}
