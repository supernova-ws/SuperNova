<?php
  // fleet.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

  CheckPlanetUsedFields($planetrow);

  $parse['MaxFlyingFleets'] = $FlyingFleets;
  $parse['MaxFlottes'] = $MaxFleets;
  $parse['ExpeditionEnCours'] = $FlyingExpeditions;
  $parse['EnvoiMaxExpedition'] = $MaxExpeditions;

  // Gestion des flottes du joueur actif
  if ($user['id']) {
    $fq = doquery("SELECT * FROM {{table}} WHERE fleet_owner={$user['id']}", "fleets");
  }
  $i  = 0;

  $parse_temp['fl_back_to_ttl'] = $lang['fl_back_to_ttl'];
  $parse_temp['fl_associate'] = $lang['fl_associate'];
  $FlyingFleets_array = '';
  while ($f = mysql_fetch_array($fq)) {
    $i++;
    $parse_temp ['FleetNum'] = $i;
    $parse_temp ['MissionType'] = $lang['type_mission'][$f['fleet_mission']];

    if (($f['fleet_start_time'] + 1) == $f['fleet_end_time']) {
      $parse_temp ['U1'] = "<br><span title=\"".$lang['fl_back_to_ttl']."\">".$lang['fl_back_to']."</span>";
    } else {
      $parse_temp ['U1'] = "<br><span title=\"".$lang['fl_get_to_ttl']."\">".$lang['fl_get_to']."</span>";
    }

    // Fleet details (commentaire)
    $fleet = explode(";", $f['fleet_array']);
    $e = 0;
    $fleetTip = '';
    foreach ($fleet as $a => $b) {
      if ($b != '') {
        $e++;
        $a = explode(",", $b);
        $fleetTip .= $lang['tech'][$a[0]] . ":" . $a[1] . " \n";
        if ($e > 1) {
          $fleetTip .= "\t";
        }
      }
    }
    $parse_temp ['FleetTip'] = $fleetTip;

    $parse_temp ['FleetAmount'] = pretty_number($f['fleet_amount']);
    $parse_temp ['FleetStart'] = "[".$f['fleet_start_galaxy'].":".$f['fleet_start_system'].":".$f['fleet_start_planet']."]";
    $parse_temp ['FleetStartTime'] = date("d. M Y H:i:s", $f['fleet_start_time']);
    $parse_temp ['FleetEnd'] = "[".$f['fleet_end_galaxy'].":".$f['fleet_end_system'].":".$f['fleet_end_planet']."]";
    $parse_temp ['FleetEndTime'] = date("d. M Y H:i:s", $f['fleet_end_time']);
    $parse_temp ['FleetTimeLeft'] = pretty_time(floor($f['fleet_end_time'] + 1 - $time_now));

    $parse_temp ['FleetID'] = $f['fleet_id'];
    $parse_temp ['ShowACS'] = 'hidden';
    $parse_temp ['ACSGroup'] = '';
    if ($f['fleet_mess'] == 0) {
      $parse_temp ['ShowFleetBack'] = 'submit';
      if ($f['fleet_mission'] == 1) {
        $parse_temp ['ShowACS'] = 'submit';
        $parse_temp ['ACSGroup'] = $lang['fl_associate'];
      };
      if ($f['fleet_mission'] == 2) {
        $aks = doquery("SELECT * FROM {{table}} WHERE id={$f['fleet_group']}", "aks", true);
        $parse_temp ['ACSGroup'] = $aks['name'];
        if ($aks['teilnehmer']==$user['id']){
          $parse_temp ['ShowACS'] = 'submit';
        }else{
          $parse_temp ['ShowACS'] = 'text';
        };
      };
    } else {
      $parse_temp ['ShowFleetBack'] = 'hidden';
    }

    $FlyingFleets_array .= parsetemplate(gettemplate('fleet_flying_fleets_row'), $parse_temp);
  }
  $parse['FlyingFleets_array'] = $FlyingFleets_array;

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
  $parse['ShipList'] = $ShipList;

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
  $parse['target_mission'] = $target_mission;

  $page = parsetemplate(gettemplate('fleet'), $parse);

  display($page, $lang['fl_title']);
?>