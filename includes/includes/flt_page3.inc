<?php
// floten3.php
// @version 1.0
// @copyright 2008 by Chlorel for XNova

use DBAL\OldDbChangeSet;
use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;
use Unit\DBStaticUnit;

if(SN_IN_FLEET !== true) {
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

function sn_fleet_page3() {
  global $missiontype, $target_mission, $fleetarray, $planetrow, $lang;
  global $galaxy, $system, $planet, $TargetPlanet, $consumption, $template_result;
  global $errorlist, $config, $planet_type, $MaxExpeditions, $FlyingExpeditions;
  global $speed_percent, $distance, $fleet_speed, $user, $debug;
  global $duration;

  $errorlist .= !is_array($fleetarray) ? $lang['fl_no_fleetarray'] : '';

  // Test de coherance de la destination (voir si elle se trouve dans les limites de l'univers connu
  $errorlist = '';
  if (!$galaxy || $galaxy > $config->game_maxGalaxy || $galaxy < 1)
    $errorlist .= $lang['fl_limit_galaxy'];
  if (!$system || $system > $config->game_maxSystem || $system < 1)
    $errorlist .= $lang['fl_limit_system'];
  if (!$planet || $planet < 1 || ($planet > $config->game_maxPlanet && $target_mission != MT_EXPLORE ))
    $errorlist .= $lang['fl_limit_planet'];
  if ($planetrow['galaxy'] == $galaxy && $planetrow['system'] == $system && $planetrow['planet'] == $planet && $planetrow['planet_type'] == $planet_type)
    $errorlist .= $lang['fl_ownpl_err'];
  if (!$planet_type)
    $errorlist .= $lang['fl_no_planettype'];
  if ($planet_type != PT_PLANET && $planet_type != PT_DEBRIS && $planet_type != PT_MOON)
    $errorlist .= $lang['fl_fleet_err_pl'];
  if (empty($missiontype[$target_mission])) {
    $errorlist .= $lang['fl_bad_mission'];
  }

  $TransMetal      = max(0, sys_get_param_float('resource0'));
  $TransCrystal    = max(0, sys_get_param_float('resource1'));
  $TransDeuterium  = max(0, sys_get_param_float('resource2'));
  $StorageNeeded   = $TransMetal + $TransCrystal + $TransDeuterium;

  if (!$StorageNeeded && $target_mission == MT_TRANSPORT) {
    $errorlist .= $lang['fl_noenoughtgoods'];
  }

  sn_db_transaction_start();

  $user = db_user_by_id($user['id'], true);
  $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true);

  if ($target_mission == MT_EXPLORE) {
    if ($MaxExpeditions == 0 ) {
      $errorlist .= $lang['fl_expe_notech'];
    } elseif ($FlyingExpeditions >= $MaxExpeditions ) {
      $errorlist .= $lang['fl_expe_max'];
    }
  } else {
    if ($TargetPlanet['id_owner']){
      if ($target_mission == MT_COLONIZE)
        $errorlist .= $lang['fl_colonized'];

      if ($TargetPlanet['id_owner'] == $planetrow['id_owner']){
        if ($target_mission == MT_ATTACK)
          $errorlist .= $lang['fl_no_self_attack'];

        if ($target_mission == MT_SPY)
          $errorlist .= $lang['fl_no_self_spy'];
      }else{
        if ($target_mission == MT_RELOCATE)
          $errorlist .= $lang['fl_only_stay_at_home'];
      }
    }else{
      if ($target_mission < MT_COLONIZE){
        $errorlist .= $lang['fl_unknow_target'];
      }else{
        if ($target_mission == MT_DESTROY)
          $errorlist .= $lang['fl_nomoon'];

        if ($target_mission == MT_RECYCLE){
          if($TargetPlanet['debris_metal'] + $TargetPlanet['debris_crystal'] == 0)
            $errorlist .= $lang['fl_nodebris'];
        }
      }
    }
  }


  if(is_object($captainModule = moduleCaptain()) && $captain_id = sys_get_param_id('captain_id')) {
    $captain = $captainModule->unit_captain_get($planetrow['id']);
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

  if ($errorlist) {
    sn_db_transaction_rollback();
    SnTemplate::messageBox("<span class='error'><ul>{$errorlist}</ul></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, false);
  }

  // On verifie s'il y a assez de vaisseaux sur la planete !
  foreach ($fleetarray as $Ship => $Count) {
    if ($Count > mrc_get_level($user, $planetrow, $Ship)) {
      SnTemplate::messageBox("<span class='error'><b>{$lang['fl_fleet_err']}</b></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
    }
  }

  //Normally... unless its acs...
  $fleet_group = max(0, intval($_POST['fleet_group']));
  //But is it acs??
  //Well all acs fleets must have a fleet code.
  if($fleet_group) {
    //Also it must be mission type 2
    $target_mission = MT_AKS;

    //The co-ords must be the same as where the acs fleet is going.
    $target = "g{$galaxy}s{$system}p{$planet}t{$planet_type}";
    if($_POST['acs_target_mr'] == $target) {
      //ACS attack must exist (if acs fleet has arrived this will also return false (2 checks in 1!!!)
      $aks = DbFleetStatic::dbAcsGetById($fleet_group);
      if (!$aks) {
        $fleet_group = 0;
      }
      else
      {
        $galaxy = $aks['galaxy'];
        $system = $aks['system'];
        $planet = $aks['planet'];
        $planet_type = $aks['planet_type'];
      }
    }
  }
  //Check that a failed acs attack isn't being sent, if it is, make it an attack fleet.
  if(!$fleet_group && $target_mission == MT_AKS) {
    $target_mission = MT_ATTACK;
  }

  if ($target_mission == MT_COLONIZE || $target_mission == MT_EXPLORE) {
    $TargetPlanet = ['galaxy' => $galaxy, 'system' => $system, 'planet' => $planet, 'id_owner' => 0];
  }
  $options = [P_FLEET_ATTACK_SPEED_PERCENT_TENTH => $speed_percent, P_FLEET_ATTACK_FLEET_GROUP => $fleet_group, P_FLEET_ATTACK_RESOURCES_SUM => $StorageNeeded];
  $cant_attack = flt_can_attack($planetrow, $TargetPlanet, $fleetarray, $target_mission, $options);

  if($cant_attack !== ATTACK_ALLOWED) {
    SnTemplate::messageBox("<span class='error'><b>{$lang['fl_attack_error'][$cant_attack]}</b></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 99);
  }

  $speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
  if (!in_array($speed_percent, $speed_possible)) {
    SnTemplate::messageBox("<span class='error'><b>" . $lang['fl_cheat_speed'] . "</b></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
  }

  $fleet['start_time'] = $duration + SN_TIME_NOW;

  $max_duration = $target_mission == MT_EXPLORE ? get_player_max_expedition_duration($user) :
    ($target_mission == MT_HOLD ? 12 : 0);
  if($max_duration) {
    $StayDuration = sys_get_param_id('missiontime');
    if($StayDuration > $max_duration || $StayDuration < 1) {
      $debug->warning('Supplying wrong mission time', 'Hack attempt', 302, array('base_dump' => true));
      die();
    }
    $StayDuration = ceil($StayDuration * 3600 / ($target_mission == MT_EXPLORE && $config->game_speed_expedition ? $config->game_speed_expedition : 1));
    $StayTime     = $fleet['start_time'] + $StayDuration;
  } else {
    $StayDuration = 0;
    $StayTime     = 0;
  }
  $fleet['end_time']   = $StayDuration + (2 * $duration) + SN_TIME_NOW;

  if($aks && $target_mission == MT_AKS) {
    if ($fleet['start_time']>$aks['ankunft']) {
      SnTemplate::messageBox($lang['fl_aks_too_slow'] . 'Fleet arrival: ' . date(FMT_DATE_TIME, $fleet['start_time']) . " AKS arrival: " . date(FMT_DATE_TIME, $aks['ankunft']), $lang['fl_error']);
    }
    $fleet['start_time'] = $aks['ankunft'];
    $fleet['end_time'] = $aks['ankunft'] + $duration;
  }

  $FleetStorage        = 0;
  $FleetShipCount      = 0;
  $db_changeset = array();
  foreach ($fleetarray as $Ship => $Count) {
    $FleetStorage    += get_unit_param($Ship, P_CAPACITY) * $Count;
    $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($Ship, -$Count, $user, $planetrow['id']);
  }
  $fleet_array = sys_unit_arr2str($fleetarray);
  $FleetShipCount  += array_sum($fleetarray);
  $FleetStorage        -= $consumption;

  if ( $StorageNeeded > $FleetStorage) {
    SnTemplate::messageBox("<span class='error'><b>" . $lang['fl_nostoragespa'] . HelperString::numberFloorAndFormat($StorageNeeded - $FleetStorage) . "</b></span>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
  }
  if ($planetrow['deuterium'] < $TransDeuterium + $consumption) {
    SnTemplate::messageBox("<font color=\"red\"><b>" . $lang['fl_no_deuterium'] . HelperString::numberFloorAndFormat($TransDeuterium + $consumption - $planetrow['deuterium']) . "</b></font>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
  }
  if (($planetrow['metal'] < $TransMetal) || ($planetrow['crystal'] < $TransCrystal)) {
    SnTemplate::messageBox("<font color=\"red\"><b>" . $lang['fl_no_resources'] . "</b></font>", $lang['fl_error'], 'fleet' . DOT_PHP_EX, 2);
  }

  $fleet_set = array(
    'fleet_owner' => $user['id'],
    'fleet_mission' => $target_mission,
    'fleet_amount' => $FleetShipCount,
    'fleet_array' => $fleet_array,
    'fleet_start_time' => $fleet['start_time'],
    'fleet_start_planet_id' => !empty($planetrow['id']) ? $planetrow['id'] : null,
    'fleet_start_galaxy' => $planetrow['galaxy'],
    'fleet_start_system' => $planetrow['system'],
    'fleet_start_planet' => $planetrow['planet'],
    'fleet_start_type'   => $planetrow['planet_type'],
    'fleet_end_time' => $fleet['end_time'],
    'fleet_end_stay' => $StayTime,
    'fleet_end_planet_id' => !empty($TargetPlanet['id']) ? $TargetPlanet['id'] : null,
    'fleet_end_galaxy' => !empty($galaxy) ? $galaxy : 0,
    'fleet_end_system' => !empty($system) ? $system : 0,
    'fleet_end_planet' => !empty($planet) ? $planet : 0,
    'fleet_end_type' => !empty($planet_type) ? $planet_type : 0,
    'fleet_target_owner' => $TargetPlanet['id_owner'],
    'fleet_group' => $fleet_group,
    'fleet_resource_metal' => $TransMetal,
    'fleet_resource_crystal' => $TransCrystal,
    'fleet_resource_deuterium' => $TransDeuterium,
    'start_time' => SN_TIME_NOW,
  );
  $fleet_id = DbFleetStatic::fleet_insert_set_dbq($fleet_set);

  DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`metal` = `metal` - {$TransMetal}, `crystal` = `crystal` - {$TransCrystal}, `deuterium` = `deuterium` - {$TransDeuterium} - {$consumption}");
  OldDbChangeSet::db_changeset_apply($db_changeset);

  $template = SnTemplate::gettemplate('fleet3', true);

//  foreach($fleetarray as $Ship => $Count) {
//    $template->assign_block_vars('ships', array(
//      'NAME'  => $lang['tech'][$Ship],
//      'COUNT' => pretty_number($Count),
//    ));
//  }

  if(is_array($captain)) {
    DBStaticUnit::db_unit_set_by_id($captain['unit_id'], "`unit_location_type` = " . LOC_FLEET . ", `unit_location_id` = {$fleet_id}");
  }

  $template_route = array(
    'ID'                 => 1,
    'START_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$planetrow['planet_type']],
    'START_COORDS'       => uni_render_coordinates($planetrow),
    'START_NAME'         => $planetrow['name'],
    'START_TIME_TEXT'    => date(FMT_DATE_TIME, $fleet['end_time'] + SN_CLIENT_TIME_DIFF),
    'START_LEFT'         => floor($fleet['end_time'] + 1 - SN_TIME_NOW),
  );

  if(!empty($TargetPlanet)) {
    $template_route += array(
      'END_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$TargetPlanet['planet_type']],
      'END_COORDS'       => uni_render_coordinates($TargetPlanet),
      'END_NAME'         => $TargetPlanet['name'],
      'END_TIME_TEXT'    => date(FMT_DATE_TIME, $fleet['start_time'] + SN_CLIENT_TIME_DIFF),
      'END_LEFT'         => floor($fleet['start_time'] + 1 - SN_TIME_NOW),
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

  $template->assign_vars(array(
    'mission' => $lang['type_mission'][$target_mission] . ($target_mission == MT_EXPLORE || $target_mission == MT_HOLD ? ' ' . pretty_time($StayDuration) : ''),
    'dist' => HelperString::numberFloorAndFormat($distance),
    'speed' => HelperString::numberFloorAndFormat($fleet_speed),
    'deute_need' => HelperString::numberFloorAndFormat($consumption),
    'from' => "{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}",
    'time_go' => date(FMT_DATE_TIME, $fleet['start_time']),
    'time_go_local' => date(FMT_DATE_TIME, $fleet['start_time'] + SN_CLIENT_TIME_DIFF),
    'time_back' => date(FMT_DATE_TIME, $fleet['end_time']),
    'time_back_local' => date(FMT_DATE_TIME, $fleet['end_time'] + SN_CLIENT_TIME_DIFF),
  ));

  sn_db_transaction_commit();
  $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id']);

  $template->assign_recursive($template_result);
  SnTemplate::display($template, $lang['fl_title']);
}
