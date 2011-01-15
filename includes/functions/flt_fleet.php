<?php
/*
$_POST = array(19)
  galaxy => string(1) 1
  system => string(2) 45
  planet => string(1) 5
  planet_type => string(1) 1

  thisgalaxy => string(1) 1
  thissystem => string(1) 1
  thisplanet => string(1) 4
  thisplanet_type => string(1) 1

  usedfleet => string(28) LGbkBagcBwVjZwgmBwR6VwRvB30=
  consumption => string(0)

  speed => string(2) 10
  fleet_group => string(1) 0
  acs_target_mr => string(5) 0:0:0
  speedallsmin => string(1) 0
  speedfactor => string(0)
  target_mission => string(1) 3
  resource0 => string(4) 1000
  resource1 => string(4) 1000
  resource2 => string(4) 1000
*/

function flt_send_fleet($user, &$from, $to, $fleet_array, $mission, $missiontype)
{
  ini_set('error_reporting', E_ALL);

  global $sn_data, $config, $lang;

  doquery('START TRANSACTION;');
  $from = doquery ("SELECT * FROM {{planets}} WHERE `id` = '{$from['id']}' LIMIT 1 FOR UPDATE;", '', true);

  $errorlist = '';

  $errorlist .= !is_array($fleet_array) ? $lang['fl_no_fleetarray'] : '';

  $errorlist .= (!$to['galaxy'] || $to['galaxy'] < 1 ||  $to['galaxy'] > $config->game_maxGalaxy) ? $lang['fl_limit_galaxy'] : '';
  $errorlist .= (!$to['system'] || $to['system'] < 1 ||  $to['system'] > $config->game_maxSystem) ? $lang['fl_limit_system'] : '';
  $errorlist .= (!$to['planet'] || $to['planet'] < 1 || ($to['planet'] > $config->game_maxPlanet AND $mission != MT_EXPLORE )) ? $lang['fl_limit_planet'] : '';
  $errorlist .= (!$to['planet_type']) ? $lang['fl_no_planettype'] : '';
  $errorlist .= ($to['planet_type'] != PT_PLANET && $to['planet_type'] != PT_DEBRIS && $to['planet_type'] != PT_MOON) ? $lang['fl_fleet_err_pl'] : '';

  $errorlist .= ($from['galaxy'] == $to['galaxy'] && $from['system'] == $to['system'] && $from['planet'] == $to['planet'] && $from['planet_type'] == $to['planet_type']) ? $lang['fl_ownpl_err'] : '';
  $errorlist .= (empty($missions_available[$mission])) ? $lang['fl_bad_mission'] : '';


  $TransMetal      = max(0, intval($_POST['resource0']));
  $TransCrystal    = max(0, intval($_POST['resource1']));
  $TransDeuterium  = max(0, intval($_POST['resource2']));
  $StorageNeeded   = $TransMetal + $TransCrystal + $TransDeuterium;

  if (!$StorageNeeded && $mission == MT_TRANSPORT) {
    $errorlist .= $lang['fl_noenoughtgoods'];
  }

  if ($mission == MT_EXPLORE) {
    if ($MaxExpeditions == 0 ) {
      $errorlist .= $lang['fl_expe_notech'];
    } elseif ($FlyingExpeditions >= $MaxExpeditions ) {
      $errorlist .= $lang['fl_expe_max'];
    }
  } else {
    if ($TargetPlanet['id_owner']){
      if ($mission == MT_COLONIZE)
        $errorlist .= $lang['fl_colonized'];

      if ($TargetPlanet['id_owner'] == $from['id_owner']){
        if ($mission == MT_ATTACK)
          $errorlist .= $lang['fl_no_self_attack'];

        if ($mission == MT_SPY)
          $errorlist .= $lang['fl_no_self_spy'];
      }else{
        if ($mission == MT_RELOCATE)
          $errorlist .= $lang['fl_only_stay_at_home'];
      }
    }else{
      if ($mission < MT_COLONIZE){
        $errorlist .= $lang['fl_unknow_target'];
      }else{
        if ($mission == MT_DESTROY)
          $errorlist .= $lang['fl_nomoon'];

        if ($mission == MT_RECYCLE){
          if($TargetPlanet['debris_metal'] + $TargetPlanet['debris_crystal'] == 0)
            $errorlist .= $lang['fl_nodebris'];
        }
      }
    }
  }


  if ($errorlist)
    message ("<font color=\"red\"><ul>{$errorlist}</ul></font>", $lang['fl_error'], "fleet.{$phpEx}", 2);

  // On verifie s'il y a assez de vaisseaux sur la planete !
  foreach ($fleet_array as $Ship => $Count) {
    if ($Count > $from[$resource[$Ship]]) {
      message ("<font color=\"red\"><b>{$lang['fl_fleet_err']}</b></font>", $lang['fl_error'], "fleet.{$phpEx}", 2);
    }
  }

  //Normally... unless its acs...
  $fleet_group = max(0, intval($_POST['fleet_group']));
  //But is it acs??
  //Well all acs fleets must have a fleet code.
  if($fleet_group){
    //Also it must be mission type 2
    $mission = MT_AKS;

    //The co-ords must be the same as where the acs fleet is going.
    $target = "g{$to['galaxy']}s{$to['system']}p{$to['planet']}t{$to['planet_type']}";
    if($_POST['acs_target_mr'] == $target){
      //ACS attack must exist (if acs fleet has arrived this will also return false (2 checks in 1!!!)
      $aks = doquery("SELECT * FROM {{aks}} WHERE id = '{$fleet_group}';",'', true);
      if (!$aks){
        $fleet_group = 0;
      }else{
        $to['galaxy'] = $aks['galaxy'];
        $to['system'] = $aks['system'];
        $to['planet'] = $aks['planet'];
        $to['planet_type'] = $aks['planet_type'];
      }
    }
  }
  //Check that a failed acs attack isn't being sent, if it is, make it an attack fleet.
  if(!$fleet_group && $mission == MT_AKS)
  {
    $mission = MT_ATTACK;
  }

  CheckPlanetUsedFields($from);

  $cant_attack = flt_can_attack($TargetPlanet, $mission, $fleet_array);
  if($cant_attack)
  {
    message("<font color=\"red\"><b>{$lang['fl_attack_error'][$cant_attack]}</b></font>", $lang['fl_error'], "fleet.{$phpEx}", 2);
  }

  $speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
  if (!in_array($speed_percent, $speed_possible)) {
    message ("<font color=\"red\"><b>". $lang['fl_cheat_speed'] ."</b></font>", $lang['fl_error'], "fleet.{$phpEx}", 2);
  }

  $fleet['start_time'] = $duration + $time_now;
  if ($mission == MT_EXPLORE OR $mission == MT_HOLD) {
    $StayDuration = max(0,intval($_POST['missiontime'])) * 3600;
    $StayTime     = $fleet['start_time'] + $StayDuration;
  } else {
    $StayDuration = 0;
    $StayTime     = 0;
  }
  $fleet['end_time']   = $StayDuration + (2 * $duration) + $time_now;

  if ($aks && $mission == MT_AKS)
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
  foreach ($fleet_array as $Ship => $Count)
  {
    $FleetStorage    += $pricelist[$Ship]["capacity"] * $Count;
    $FleetShipCount  += $Count;
    $fleet_array     .= "{$Ship},{$Count};";
    $FleetSubQRY     .= "`{$resource[$Ship]}` = `{$resource[$Ship]}` - {$Count} , ";
  }
  $FleetStorage        -= $consumption;

  if ( $StorageNeeded > $FleetStorage)
  {
    message ("<font color=\"red\"><b>". $lang['fl_nostoragespa'] . pretty_number($StorageNeeded - $FleetStorage) ."</b></font>", $lang['fl_error'], "fleet.{$phpEx}", 2);
  }
  if ($from['deuterium'] < $TransDeuterium + $consumption)
  {
    message ("<font color=\"red\"><b>". $lang['fl_no_deuterium'] . pretty_number($TransDeuterium + $consumption - $from['deuterium']) ."</b></font>", $lang['fl_error'], "fleet.{$phpEx}", 2);
  }
  if (($from['metal'] < $TransMetal) || ($from['crystal'] < $TransCrystal))
  {
    message ("<font color=\"red\"><b>". $lang['fl_no_resources'] ."</b></font>", $lang['fl_error'], "fleet.{$phpEx}", 2);
  }

  // ecriture de l'enregistrement de flotte (a partir de lôå_, y a quelque chose qui vole et c'est toujours sur la planete d'origine)
  $QryInsertFleet  = "INSERT INTO {{fleets}} SET ";
  $QryInsertFleet .= "`fleet_owner` = '". $user['id'] ."', ";
  $QryInsertFleet .= "`fleet_mission` = '". $mission ."', ";
  $QryInsertFleet .= "`fleet_amount` = '". $FleetShipCount ."', ";
  $QryInsertFleet .= "`fleet_array` = '". $fleet_array ."', ";
  $QryInsertFleet .= "`fleet_start_time` = '". $fleet['start_time'] ."', ";
  $QryInsertFleet .= "`fleet_start_galaxy` = '". intval($from['galaxy']) ."', ";
  $QryInsertFleet .= "`fleet_start_system` = '". intval($from['system']) ."', ";
  $QryInsertFleet .= "`fleet_start_planet` = '". intval($from['planet']) ."', ";
  $QryInsertFleet .= "`fleet_start_type` = '". intval($from['planet_type']) ."', ";
  $QryInsertFleet .= "`fleet_end_time` = '". $fleet['end_time'] ."', ";
  $QryInsertFleet .= "`fleet_end_stay` = '". $StayTime ."', ";
  $QryInsertFleet .= "`fleet_end_galaxy` = '". $to['galaxy'] ."', ";
  $QryInsertFleet .= "`fleet_end_system` = '". $to['system'] ."', ";
  $QryInsertFleet .= "`fleet_end_planet` = '". $to['planet'] ."', ";
  $QryInsertFleet .= "`fleet_end_type` = '". $to['planet_type'] ."', ";
  $QryInsertFleet .= "`fleet_resource_metal` = '". $TransMetal ."', ";
  $QryInsertFleet .= "`fleet_resource_crystal` = '". $TransCrystal ."', ";
  $QryInsertFleet .= "`fleet_resource_deuterium` = '". $TransDeuterium ."', ";
  $QryInsertFleet .= "`fleet_target_owner` = '". $TargetPlanet['id_owner'] ."', ";
  $QryInsertFleet .= "`fleet_group` = '". $fleet_group ."', ";
  $QryInsertFleet .= "`start_time` = '". $time_now ."';";
  doquery( $QryInsertFleet);

  $QryUpdatePlanet  = "UPDATE {{planets}} SET {$FleetSubQRY}";
  $QryUpdatePlanet .= "`metal` = `metal` - {$TransMetal}, `crystal` = `crystal` - {$TransCrystal}, `deuterium` = `deuterium` - {$TransDeuterium} - {$consumption} ";
  $QryUpdatePlanet .= "WHERE `id` = '{$from['id']}' LIMIT 1;";
  doquery ($QryUpdatePlanet);

  $parse["mission"] = $lang['type_mission'][$mission];
  if ($mission == MT_EXPLORE OR $mission == MT_HOLD) {
    // $parse["mission"] .= sprintf($lang['fl_duration_time'], $StayDuration);
    $parse["mission"] .= ' ' . pretty_time($StayDuration);
  };
  $parse["dist"] = pretty_number($distance);
  $parse["speed"] = pretty_number($fleet_speed);
  $parse["deute_need"] = pretty_number($consumption);
  $parse["from"] = "{$from['galaxy']}:{$from['system']}:{$from['planet']}";
  $parse["time_go"] = date(FMT_DATE_TIME, $fleet['start_time']);
  $parse["time_back"] = date(FMT_DATE_TIME, $fleet['end_time']);

  $parse_temp['DisplayControls'] = 'display: none;';
  $ShipList = "";
  foreach ($fleet_array as $Ship => $Count) {
    // $parse_temp['ShipSpeed'] =
    $parse_temp['ShipName'] = $lang['tech'][$Ship];
    $parse_temp['ShipNumPrint'] = pretty_number($Count);
    $ShipList .= parsetemplate(gettemplate('fleet_ship_row'), $parse_temp);
  }
  $parse['ShipList'] = $ShipList;

  doquery("COMMIT;");
  $from = doquery ("SELECT * FROM {{planets}} WHERE `id` = '{$from['id']}' LIMIT 1;", '', true);

  $page = parsetemplate(gettemplate('fleet3'), $parse);
  display($page, $lang['fl_title']);

  ini_set('error_reporting', E_ALL ^ E_NOTICE);
}

?>
