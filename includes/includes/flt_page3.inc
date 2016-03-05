<?php
// floten3.php
// @version 1.0
// @copyright 2008 by Chlorel for XNova

if(SN_IN_FLEET !== true) {
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

function sn_fleet_page3($time_to_travel) {
  global $missiontype, $target_mission, $fleetarray, $planetrow, $lang;
  global $galaxy, $system, $planet, $TargetPlanet, $consumption, $template_result;
  global $errorlist, $config, $planet_type, $MaxExpeditions, $FlyingExpeditions;
  global $speed_percent, $distance, $fleet_speed, $user, $debug;
  global $sn_module;

  $adjusted_travel_time = $time_to_travel;

  $errorlist .= !is_array($fleetarray) ? $lang['fl_no_fleetarray'] : '';

  // Test de coherance de la destination (voir si elle se trouve dans les limites de l'univers connu
  $errorlist = '';
  if(!$galaxy || $galaxy > $config->game_maxGalaxy || $galaxy < 1) {
    $errorlist .= $lang['fl_limit_galaxy'];
  }
  if(!$system || $system > $config->game_maxSystem || $system < 1) {
    $errorlist .= $lang['fl_limit_system'];
  }
  if(!$planet || $planet < 1 || ($planet > $config->game_maxPlanet && $target_mission != MT_EXPLORE)) {
    $errorlist .= $lang['fl_limit_planet'];
  }
  if($planetrow['galaxy'] == $galaxy && $planetrow['system'] == $system && $planetrow['planet'] == $planet && $planetrow['planet_type'] == $planet_type) {
    $errorlist .= $lang['fl_ownpl_err'];
  }
  if(!$planet_type) {
    $errorlist .= $lang['fl_no_planettype'];
  }
  if($planet_type != PT_PLANET && $planet_type != PT_DEBRIS && $planet_type != PT_MOON) {
    $errorlist .= $lang['fl_fleet_err_pl'];
  }
  if(empty($missiontype[$target_mission])) {
    $errorlist .= $lang['fl_bad_mission'];
  }

  $TransMetal = max(0, sys_get_param_float('resource0'));
  $TransCrystal = max(0, sys_get_param_float('resource1'));
  $TransDeuterium = max(0, sys_get_param_float('resource2'));
  $StorageNeeded = $TransMetal + $TransCrystal + $TransDeuterium;

  if(!$StorageNeeded && $target_mission == MT_TRANSPORT) {
    $errorlist .= $lang['fl_noenoughtgoods'];
  }

  sn_db_transaction_start();

  $user = db_user_by_id($user['id'], true);
  $planetrow = db_planet_by_id($planetrow['id'], true);

  if($target_mission == MT_EXPLORE) {
    if($MaxExpeditions == 0) {
      $errorlist .= $lang['fl_expe_notech'];
    } elseif($FlyingExpeditions >= $MaxExpeditions) {
      $errorlist .= $lang['fl_expe_max'];
    }
  } else {
    if($TargetPlanet['id_owner']) {
      if($target_mission == MT_COLONIZE) {
        $errorlist .= $lang['fl_colonized'];
      }

      if($TargetPlanet['id_owner'] == $planetrow['id_owner']) {
        if($target_mission == MT_ATTACK) {
          $errorlist .= $lang['fl_no_self_attack'];
        }

        if($target_mission == MT_SPY) {
          $errorlist .= $lang['fl_no_self_spy'];
        }
      } else {
        if($target_mission == MT_RELOCATE) {
          $errorlist .= $lang['fl_only_stay_at_home'];
        }
      }
    } else {
      if($target_mission < MT_COLONIZE) {
        $errorlist .= $lang['fl_unknow_target'];
      } else {
        if($target_mission == MT_DESTROY) {
          $errorlist .= $lang['fl_nomoon'];
        }

        if($target_mission == MT_RECYCLE) {
          if($TargetPlanet['debris_metal'] + $TargetPlanet['debris_crystal'] == 0) {
            $errorlist .= $lang['fl_nodebris'];
          }
        }
      }
    }
  }


  if($sn_module['unit_captain']->manifest['active'] && $captain_id = sys_get_param_id('captain_id')) {
    $captain = $sn_module['unit_captain']->unit_captain_get($planetrow['id']);
//      mrc_get_level($user, $planetrow, UNIT_CAPTAIN, true);
    if(!$captain) {
      $errorlist .= $lang['module_unit_captain_error_no_captain'];
    } elseif($captain['unit_location_type'] == LOC_PLANET) {
      if($target_mission == MT_RELOCATE && ($arriving_captain = mrc_get_level($user, $TargetPlanet, UNIT_CAPTAIN, true))) {
        $errorlist .= $lang['module_unit_captain_error_captain_already_bound'];
      }
    } else {
      $errorlist .= $lang['module_unit_captain_error_captain_flying'];
    }
  }

  if($errorlist) {
    sn_db_transaction_rollback();
    message("<span class='error'><ul>{$errorlist}</ul></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, false);
  }

  // On verifie s'il y a assez de vaisseaux sur la planete !
  foreach($fleetarray as $Ship => $Count) {
    if($Count > mrc_get_level($user, $planetrow, $Ship)) {
      message("<span class='error'><b>{$lang['fl_fleet_err']}</b></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
    }
  }

  //Normally... unless its acs...
  $aks = 0;
  $fleet_group = sys_get_param_int('fleet_group');
  //But is it acs??
  //Well all acs fleets must have a fleet code.
  //The co-ords must be the same as where the acs fleet is going.
  if($fleet_group && sys_get_param_str('acs_target_mr') == "g{$galaxy}s{$system}p{$planet}t{$planet_type}") {
    //ACS attack must exist (if acs fleet has arrived this will also return false (2 checks in 1!!!)
//    $aks = doquery("SELECT * FROM {{aks}} WHERE id = '{$fleet_group}' LIMIT 1;", true);
    $aks = db_acs_get_by_group_id($fleet_group);
    if(!$aks) {
      $fleet_group = 0;
    } else {
      //Also it must be mission type 2
      $target_mission = MT_AKS;

      $galaxy = $aks['galaxy'];
      $system = $aks['system'];
      $planet = $aks['planet'];
      $planet_type = $aks['planet_type'];
    }
  } elseif($target_mission == MT_AKS) {
    //Check that a failed acs attack isn't being sent, if it is, make it an attack fleet.
    $target_mission = MT_ATTACK;
  }

  if($target_mission == MT_COLONIZE || $target_mission == MT_EXPLORE) {
    $TargetPlanet = array('galaxy' => $galaxy, 'system' => $system, 'planet' => $planet, 'id_owner' => 0);
  }
  $options = array('fleet_speed_percent' => $speed_percent, 'fleet_group' => $fleet_group, 'resources' => $StorageNeeded);
  $cant_attack = flt_can_attack($planetrow, $TargetPlanet, $fleetarray, $target_mission, $options);

  if($cant_attack !== ATTACK_ALLOWED) {
    message("<span class='error'><b>{$lang['fl_attack_error'][$cant_attack]}</b></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 99);
  }

  $speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
  if(!in_array($speed_percent, $speed_possible)) {
    message("<span class='error'><b>" . $lang['fl_cheat_speed'] . "</b></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
  }

  $mission_time_in_seconds = 0;
  $arrival_time = SN_TIME_NOW + $time_to_travel;
  if($target_mission == MT_AKS && $aks) {
//    if($fleet_start_time > $aks['ankunft']) {
    if($arrival_time > $aks['ankunft']) {
      message($lang['fl_aks_too_slow'] . 'Fleet arrival: ' . date(FMT_DATE_TIME, $arrival_time) . " AKS arrival: " . date(FMT_DATE_TIME, $aks['ankunft']), $lang['fl_error']);
    }
    $group_sync_delta_time = $aks['ankunft'] - $arrival_time;
    // Set arrival time to ACS arrival time
    $arrival_time = $aks['ankunft'];
    // Set return time to ACS return time + fleet's time to travel
    $return_time = $aks['ankunft'] + $time_to_travel;
  } else {
    if($target_mission == MT_EXPLORE || $target_mission == MT_HOLD) {
      $max_duration = $target_mission == MT_EXPLORE ? get_player_max_expedition_duration($user) : ($target_mission == MT_HOLD ? 12 : 0);
      if($max_duration) {
        $mission_time_in_hours = sys_get_param_id('missiontime');
        if($mission_time_in_hours > $max_duration || $mission_time_in_hours < 1) {
          $debug->warning('Supplying wrong mission time', 'Hack attempt', 302, array('base_dump' => true));
          die();
        }
        $mission_time_in_seconds = ceil($mission_time_in_hours * 3600 / ($target_mission == MT_EXPLORE && $config->game_speed_expedition ? $config->game_speed_expedition : 1));
      }
    }
    $return_time = $arrival_time + $mission_time_in_seconds + $time_to_travel;
    $group_sync_delta_time = 0;
  }

  $FleetStorage = 0;

  $db_changeset = array();
  foreach($fleetarray as $Ship => $Count) {
    $FleetStorage += get_unit_param($Ship, P_CAPACITY) * $Count;
    $db_changeset['unit'][] = sn_db_unit_changeset_prepare($Ship, -$Count, $user, $planetrow['id']);
  }
  $FleetStorage -= $consumption;

  if($StorageNeeded > $FleetStorage) {
    message("<span class='error'><b>" . $lang['fl_nostoragespa'] . pretty_number($StorageNeeded - $FleetStorage) . "</b></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
  }
  if($planetrow['deuterium'] < $TransDeuterium + $consumption) {
    message("<font color=\"red\"><b>" . $lang['fl_no_deuterium'] . pretty_number($TransDeuterium + $consumption - $planetrow['deuterium']) . "</b></font>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
  }
  if(($planetrow['metal'] < $TransMetal) || ($planetrow['crystal'] < $TransCrystal)) {
    message("<font color=\"red\"><b>" . $lang['fl_no_resources'] . "</b></font>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
  }

  $to = array(
    'id'          => !empty($TargetPlanet['id']) ? $TargetPlanet['id'] : null,
    'galaxy'      => !empty($galaxy) ? $galaxy : 0,
    'system'      => !empty($system) ? $system : 0,
    'planet'      => !empty($planet) ? $planet : 0,
    'planet_type' => !empty($planet_type) ? $planet_type : 0,
    'id_owner'    => $TargetPlanet['id_owner'],
  );

  $fleetarray[RES_METAL] = $TransMetal;
  $fleetarray[RES_CRYSTAL] = $TransCrystal;
  $fleetarray[RES_DEUTERIUM] = $TransDeuterium;

  $objFleet = new Fleet();
  $objFleet->set_times($time_to_travel, $mission_time_in_seconds, $group_sync_delta_time);
  $fleet_id = $objFleet->create_and_send($user['id'], $fleetarray, $target_mission, $planetrow, $to, $fleet_group);
//  $fleet_id = fleet_insert_set_advanced($user['id'], $fleetarray, $target_mission, $planetrow, $to, $fleet_start_time, $fleet_end_time, $StayTime, $fleet_group);

  db_planet_set_by_id($planetrow['id'], "`metal` = `metal` - {$TransMetal}, `crystal` = `crystal` - {$TransCrystal}, `deuterium` = `deuterium` - {$TransDeuterium} - {$consumption}");
  db_changeset_apply($db_changeset);

  $template = gettemplate('fleet3', true);

  if(is_array($captain)) {
    db_unit_set_by_id($captain['unit_id'], "`unit_location_type` = " . LOC_FLEET . ", `unit_location_id` = {$fleet_id}");
  }

  $template_route = array(
    'ID'                 => 1,
    'START_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$planetrow['planet_type']],
    'START_COORDS'       => uni_render_coordinates($planetrow),
    'START_NAME'         => $planetrow['name'],
    'START_TIME_TEXT'    => date(FMT_DATE_TIME, $return_time + SN_CLIENT_TIME_DIFF),
    'START_LEFT'         => floor($return_time + 1 - SN_TIME_NOW),
  );

  if(!empty($TargetPlanet)) {
    $template_route += array(
      'END_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$TargetPlanet['planet_type']],
      'END_COORDS'       => uni_render_coordinates($TargetPlanet),
      'END_NAME'         => $TargetPlanet['name'],
      'END_TIME_TEXT'    => date(FMT_DATE_TIME, $arrival_time + SN_CLIENT_TIME_DIFF),
      'END_LEFT'         => floor($arrival_time + 1 - SN_TIME_NOW),
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
        'AMOUNT_TEXT' => pretty_number($ship_count),
//        'CONSUMPTION' => $ship_base_data['consumption'],
//        'SPEED'       => $ship_base_data['speed'],
        'NAME'        => $lang['tech'][$ship_id],
      ));
    }
  }

  $template->assign_vars(array(
    'mission'         => $lang['type_mission'][$target_mission] . ($target_mission == MT_EXPLORE || $target_mission == MT_HOLD ? ' ' . pretty_time($mission_time_in_seconds) : ''),
    'dist'            => pretty_number($distance),
    'speed'           => pretty_number($fleet_speed),
    'deute_need'      => pretty_number($consumption),
    'from'            => "{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}",
    'time_go'         => date(FMT_DATE_TIME, $arrival_time),
    'time_go_local'   => date(FMT_DATE_TIME, $arrival_time + SN_CLIENT_TIME_DIFF),
    'time_back'       => date(FMT_DATE_TIME, $return_time),
    'time_back_local' => date(FMT_DATE_TIME, $return_time + SN_CLIENT_TIME_DIFF),
  ));

  sn_db_transaction_commit();
  $planetrow = db_planet_by_id($planetrow['id']);

  $template->assign_recursive($template_result);
  display($template, $lang['fl_title']);
}
