<?php
  // fleet.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

  $template = gettemplate('fleet', true);

  CheckPlanetUsedFields($planetrow);

  // Gestion des flottes du joueur actif
  if ($user['id']) {
    $i  = 0;
    $fq = doquery("SELECT * FROM {{table}} WHERE fleet_owner={$user['id']}", "fleets");

    while ($f = mysql_fetch_array($fq)) {
      // Fleet details (commentaire)
      $fleet = explode(";", $f['fleet_array']);
      $e = 0;

      if ($f['fleet_mess'] == 0 && $f['fleet_mission'] == 2) {
        $aks = doquery("SELECT * FROM {{table}} WHERE id={$f['fleet_group']}", "aks", true);
      };

      $i++;
      $template->assign_block_vars('fleets', array(
        'NUMBER' => $i,
        'ID' => $f['fleet_id'],
        'MESSAGE' => $f['fleet_mess'],
        'MISSION' => $f['fleet_mission'],
        'MISSION_NAME' => $lang['type_mission'][$f['fleet_mission']],
        'ACS' => $aks['name'],
        'AMOUNT' => pretty_number($f['fleet_amount']),

        'METAL' => $f['fleet_resource_metal'],
        'CRYSTAL' => $f['fleet_resource_crystal'],
        'DEUTERIUM' => $f['fleet_resource_deuterium'],


        'START_TYPE' => $lang['fl_shrtcup'][$f['fleet_start_type']],
        'START_PLANET' => "[".$f['fleet_start_galaxy'].":".$f['fleet_start_system'].":".$f['fleet_start_planet']."]",
        'START_TIME' => date("d.m.Y H:i:s", $f['fleet_end_time']),
        'START_LEFT' => floor($f['fleet_end_time'] + 1 - $time_now),

        'END_TYPE' => $lang['fl_shrtcup'][$f['fleet_end_type']],
        'END_PLANET' => "[".$f['fleet_end_galaxy'].":".$f['fleet_end_system'].":".$f['fleet_end_planet']."]",
        'END_TIME' => date("d.m.Y H:i:s", $f['fleet_start_time']),
        'END_LEFT' => floor($f['fleet_start_time'] + 1 - $time_now),
      ));
      foreach ($fleet as $a => $b) {
        if ($b != '') {
          $a = explode(",", $b);
          $template->assign_block_vars('fleets.ships', array(
            'NAME' => $lang['tech'][$a[0]],
            'AMOUNT' => $a[1],
          ));
        }
      }
    }
  }

  $parse_temp['fl_fleetspeed'] = $lang['fl_fleetspeed'];
  $parse_temp['fl_selmax'] = $lang['fl_selmax'];
  $parse_temp['ShipPrefix'] = 'max';
  $ShipList = '';
  foreach ($reslist['fleet'] as $n => $i) {
    if ($planetrow[$resource[$i]] > 0) {
      $parse_temp['ShipNumPrint'] = pretty_number ($planetrow[$resource[$i]]);
      $parse_temp['ShipName'] = $lang['tech'][$i];
      $parse_temp['ShipID'] = $i;
      $parse_temp['ShipNum'] = $planetrow[$resource[$i]];
      $parse_temp['ShipConsumption'] = GetShipConsumption ( $i, $user );
      $parse_temp['ShipSpeed'] = pretty_number(max(0, GetFleetMaxSpeed ("", $i, $user)));
      $parse_temp['ShipCapacity'] = $pricelist[$i]['capacity'];

      // Solar Sattelite
      if ($i == 212) {
        $parse_temp['DisplayControls'] = 'display: none';
      } else {
        $parse_temp['DisplayControls'] = '';
      };
      $ShipList .= parsetemplate(gettemplate('fleet_ship_row'), $parse_temp);
      $ShipList .= parsetemplate(gettemplate('fleet_hidden_row'), $parse_temp);
      $have_ships = true;
    }
  }

  if($ShipList){
    $parse['DisplayNoShips'] = 'display: none';
  } else {
    $parse['DisplayButtons'] = 'display: none';
  };

  if ($MaxFleets > $FlyingFleets)
    $parse['DisplayNoSlotFree'] = 'display: none';

  if (!$planetrow) {
    $parse_err['title'] = $lang['fl_error'];
    $parse_err['mes']   = $lang['fl_noplanetrow'];

    $parse['ErrorNoPlanetRow'] = parsetemplate(gettemplate('message_body'), $parse_err);
  }
/*
  $parse['MaxFlyingFleets'] = $FlyingFleets;
  $parse['MaxFlottes'] = $MaxFleets;
  $parse['ExpeditionEnCours'] = $FlyingExpeditions;
  $parse['EnvoiMaxExpedition'] = $MaxExpeditions;
  $parse['target_mission'] = $target_mission;
*/

  $template->assign_vars(array(
    'TIME_NOW'           => $time_now,

    'MaxFlyingFleets'    => $FlyingFleets,
    'MaxFlottes'         => $MaxFleets,
    'ExpeditionEnCours'  => $FlyingExpeditions,
    'EnvoiMaxExpedition' => $MaxExpeditions,
    'target_mission'     => $target_mission,

    'ShipList'           => $ShipList,
  ));

  $page = parsetemplate($template, $parse);
  display($page, $lang['fl_title']);
?>