<?php
  // floten3.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

if(SN_IN_FLEET !== true)
{
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

  $errorlist .= !is_array($fleetarray) ? $lang['fl_no_fleetarray'] : '';

  // Test de coherance de la destination (voir si elle se trouve dans les limites de l'univers connu
  $errorlist = '';
  if (!$galaxy || $galaxy > $config->game_maxGalaxy || $galaxy < 1)
    $errorlist .= $lang['fl_limit_galaxy'];
  if (!$system || $system > $config->game_maxSystem || $system < 1)
    $errorlist .= $lang['fl_limit_system'];
  if (!$planet || $planet < 1 || ($planet > $config->game_maxPlanet AND $target_mission != MT_EXPLORE ))
    $errorlist .= $lang['fl_limit_planet'];
  if ($planetrow['galaxy'] == $galaxy && $planetrow['system'] == $system && $planetrow['planet'] == $planet && $planetrow['planet_type'] == $planet_type)
    $errorlist .= $lang['fl_ownpl_err'];
  if (!$planet_type)
    $errorlist .= $lang['fl_no_planettype'];
  if ($planet_type != PT_PLANET AND $planet_type != PT_DEBRIS AND $planet_type != PT_MOON)
    $errorlist .= $lang['fl_fleet_err_pl'];
  if (empty($missiontype[$target_mission])) {
    $errorlist .= $lang['fl_bad_mission'];
  }

  $TransMetal      = max(0, intval($_POST['resource0']));
  $TransCrystal    = max(0, intval($_POST['resource1']));
  $TransDeuterium  = max(0, intval($_POST['resource2']));
  $StorageNeeded   = $TransMetal + $TransCrystal + $TransDeuterium;

  if (!$StorageNeeded && $target_mission == MT_TRANSPORT) {
    $errorlist .= $lang['fl_noenoughtgoods'];
  }

  doquery('START TRANSACTION;');
  $planetrow = doquery ("SELECT * FROM {{planets}} WHERE `id` = '{$planetrow['id']}' LIMIT 1 FOR UPDATE;", '', true);

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


  if ($errorlist)
  {
    doquery("ROLLBACK;");
    message ("<font color=\"red\"><ul>{$errorlist}</ul></font>", $lang['fl_error'], 'fleet.' . PHP_EX, false);
  }

  // On verifie s'il y a assez de vaisseaux sur la planete !
  foreach ($fleetarray as $Ship => $Count) {
    if ($Count > $planetrow[$sn_data[$Ship]['name']]) {
      message ("<font color=\"red\"><b>{$lang['fl_fleet_err']}</b></font>", $lang['fl_error'], 'fleet.' . PHP_EX, 2);
    }
  }

  //Normally... unless its acs...
  $fleet_group = max(0, intval($_POST['fleet_group']));
  //But is it acs??
  //Well all acs fleets must have a fleet code.
  if($fleet_group){
    //Also it must be mission type 2
    $target_mission = MT_AKS;

    //The co-ords must be the same as where the acs fleet is going.
    $target = "g{$galaxy}s{$system}p{$planet}t{$planet_type}";
    if($_POST['acs_target_mr'] == $target){
      //ACS attack must exist (if acs fleet has arrived this will also return false (2 checks in 1!!!)
      $aks = doquery("SELECT * FROM {{aks}} WHERE id = '{$fleet_group}' LIMIT 1;",'', true);
      if (!$aks){
        $fleet_group = 0;
      }else{
        $galaxy = $aks['galaxy'];
        $system = $aks['system'];
        $planet = $aks['planet'];
        $planet_type = $aks['planet_type'];
      }
    }
  }
  //Check that a failed acs attack isn't being sent, if it is, make it an attack fleet.
  if(!$fleet_group && $target_mission == MT_AKS)
  {
    $target_mission = MT_ATTACK;
  }

  CheckPlanetUsedFields($planetrow);
  if ($target_mission == MT_COLONIZE || $target_mission == MT_EXPLORE)
  {
    $TargetPlanet = array('galaxy' => $galaxy, 'system' => $system, 'planet' => $planet, 'id_owner' => 0);
  }
  $options = array('fleet_speed_percent' => $speed_percent, 'fleet_group' => $fleet_group);
  $cant_attack = flt_can_attack($planetrow, $TargetPlanet, $fleetarray, $target_mission, $options);

  if($cant_attack !== ATTACK_ALLOWED)
  {
    message("<font color=\"red\"><b>{$lang['fl_attack_error'][$cant_attack]}</b></font>", $lang['fl_error'], 'fleet.' . PHP_EX, 99);
  }

  $speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
  if (!in_array($speed_percent, $speed_possible)) {
    message ("123<font color=\"red\"><b>". $lang['fl_cheat_speed'] ."</b></font>", $lang['fl_error'], 'fleet.' . PHP_EX, 2);
  }

  $fleet['start_time'] = $duration + $time_now;
  if ($target_mission == MT_EXPLORE OR $target_mission == MT_HOLD) {
    $StayDuration = intval($_POST['missiontime']);
    if(!in_array($StayDuration, array(1, 2, 4, 8, 16, 32)))
    {
      $debug->warning('Supplying wrong mission time', 'Hack attempt', 302, array('base_dump' => true));
      die();
    }
    $StayDuration = max(0, $StayDuration) * 3600;
    $StayTime     = $fleet['start_time'] + $StayDuration;
  } else {
    $StayDuration = 0;
    $StayTime     = 0;
  }
  $fleet['end_time']   = $StayDuration + (2 * $duration) + $time_now;

  if ($aks && $target_mission == MT_AKS)
  {
    if ($fleet['start_time']>$aks['ankunft'])
    {
      message ($lang['fl_aks_too_slow'] . 'Fleet arrival: ' . date(FMT_DATE_TIME,$fleet['start_time']) . " AKS arrival: " .date(FMT_DATE_TIME,$aks['ankunft']), $lang['fl_error']);
    }
    $fleet['start_time'] = $aks['ankunft'];
    $fleet['end_time'] = $aks['ankunft'] + $duration;
  };

  $FleetStorage        = 0;
  $FleetShipCount      = 0;
  $fleet_array         = "";
  $FleetSubQRY         = "";
  foreach ($fleetarray as $Ship => $Count)
  {
    $FleetStorage    += $sn_data[$Ship]["capacity"] * $Count;
    $FleetShipCount  += $Count;
    $fleet_array     .= "{$Ship},{$Count};";
    $FleetSubQRY     .= "`{$sn_data[$Ship]['name']}` = `{$sn_data[$Ship]['name']}` - {$Count} , ";
  }
  $FleetStorage        -= $consumption;

  if ( $StorageNeeded > $FleetStorage)
  {
    message ("<font color=\"red\"><b>". $lang['fl_nostoragespa'] . pretty_number($StorageNeeded - $FleetStorage) ."</b></font>", $lang['fl_error'], 'fleet.' . PHP_EX, 2);
  }
  if ($planetrow['deuterium'] < $TransDeuterium + $consumption)
  {
    message ("<font color=\"red\"><b>". $lang['fl_no_deuterium'] . pretty_number($TransDeuterium + $consumption - $planetrow['deuterium']) ."</b></font>", $lang['fl_error'], 'fleet.' . PHP_EX, 2);
  }
  if (($planetrow['metal'] < $TransMetal) || ($planetrow['crystal'] < $TransCrystal))
  {
    message ("<font color=\"red\"><b>". $lang['fl_no_resources'] ."</b></font>", $lang['fl_error'], 'fleet.' . PHP_EX, 2);
  }

  // ecriture de l'enregistrement de flotte (a partir de lôå_, y a quelque chose qui vole et c'est toujours sur la planete d'origine)
  $QryInsertFleet  = "INSERT INTO {{fleets}} SET ";
  $QryInsertFleet .= "`fleet_owner` = '". $user['id'] ."', ";
  $QryInsertFleet .= "`fleet_mission` = '". $target_mission ."', ";
  $QryInsertFleet .= "`fleet_amount` = '". $FleetShipCount ."', ";
  $QryInsertFleet .= "`fleet_array` = '". $fleet_array ."', ";
  $QryInsertFleet .= "`fleet_start_time` = '". $fleet['start_time'] ."', ";
  $QryInsertFleet .= "`fleet_start_galaxy` = '". intval($planetrow['galaxy']) ."', ";
  $QryInsertFleet .= "`fleet_start_system` = '". intval($planetrow['system']) ."', ";
  $QryInsertFleet .= "`fleet_start_planet` = '". intval($planetrow['planet']) ."', ";
  $QryInsertFleet .= "`fleet_start_type` = '". intval($planetrow['planet_type']) ."', ";
  $QryInsertFleet .= "`fleet_end_time` = '". $fleet['end_time'] ."', ";
  $QryInsertFleet .= "`fleet_end_stay` = '". $StayTime ."', ";
  $QryInsertFleet .= "`fleet_end_galaxy` = '". $galaxy ."', ";
  $QryInsertFleet .= "`fleet_end_system` = '". $system ."', ";
  $QryInsertFleet .= "`fleet_end_planet` = '". $planet ."', ";
  $QryInsertFleet .= "`fleet_end_type` = '". $planet_type ."', ";
  $QryInsertFleet .= "`fleet_resource_metal` = '". $TransMetal ."', ";
  $QryInsertFleet .= "`fleet_resource_crystal` = '". $TransCrystal ."', ";
  $QryInsertFleet .= "`fleet_resource_deuterium` = '". $TransDeuterium ."', ";
  $QryInsertFleet .= "`fleet_target_owner` = '". intval($TargetPlanet['id_owner']) ."', ";
  $QryInsertFleet .= "`fleet_group` = '". $fleet_group ."', ";
  $QryInsertFleet .= "`start_time` = '". $time_now ."';";
  doquery( $QryInsertFleet);

  $QryUpdatePlanet  = "UPDATE {{planets}} SET {$FleetSubQRY}";
  $QryUpdatePlanet .= "`metal` = `metal` - {$TransMetal}, `crystal` = `crystal` - {$TransCrystal}, `deuterium` = `deuterium` - {$TransDeuterium} - {$consumption} ";
  $QryUpdatePlanet .= "WHERE `id` = '{$planetrow['id']}' LIMIT 1;";
  doquery ($QryUpdatePlanet);

  $parse["mission"] = $lang['type_mission'][$target_mission];
  if ($target_mission == MT_EXPLORE OR $target_mission == MT_HOLD) {
    // $parse["mission"] .= sprintf($lang['fl_duration_time'], $StayDuration);
    $parse["mission"] .= ' ' . pretty_time($StayDuration);
  };
  $parse["dist"] = pretty_number($distance);
  $parse["speed"] = pretty_number($fleet_speed);
  $parse["deute_need"] = pretty_number($consumption);
  $parse["from"] = "{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}";
  $parse["time_go"] = date(FMT_DATE_TIME, $fleet['start_time']);
  $parse["time_back"] = date(FMT_DATE_TIME, $fleet['end_time']);

  $parse_temp['DisplayControls'] = 'display: none;';
  $ShipList = "";
  foreach ($fleetarray as $Ship => $Count) {
    // $parse_temp['ShipSpeed'] =
    $parse_temp['ShipName'] = $lang['tech'][$Ship];
    $parse_temp['ShipNumPrint'] = pretty_number($Count);
    $ShipList .= parsetemplate(gettemplate('fleet_ship_row'), $parse_temp);
  }
  $parse['ShipList'] = $ShipList;

  doquery("COMMIT;");
  $planetrow = doquery ("SELECT * FROM {{planets}} WHERE `id` = '{$planetrow['id']}' LIMIT 1;", '', true);

  $page = parsetemplate(gettemplate('fleet3'), $parse);
  display($page, $lang['fl_title']);

?>
