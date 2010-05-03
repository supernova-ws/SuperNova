<?php

/**
 * flotenajax.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */
header("Content-type: text/html; charset=windows-1251");
define('INSIDE'  , true);
define('INSTALL' , false);
$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

        includeLang('galaxy');
        includeLang('fleet');

    if(IsVacationMode($user)){
      $ResultMessage = "620;".$lang['gs_c620']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
      die ( $ResultMessage );
    }

        $UserSpyProbes  = $planetrow['spy_sonde'];
        $UserRecycles   = $planetrow['recycler'];
        $UserDeuterium  = $planetrow['deuterium'];
        $UserMissiles   = $planetrow['interplanetary_misil'];

        $fleet          = array();
        $speedalls      = array();
        $PartialFleet   = false; // 610
        $PartialCount   = 0;

        foreach ($reslist['fleet'] as $Node => $ShipID) {
                $TName = "ship".$ShipID;
                if ($ShipID > 200 && $ShipID < 300 && $_POST[$TName] > 0) {
                        if ($_POST[$TName] > $planetrow[$resource[$ShipID]]) {
                                $fleet['fleetarray'][$ShipID]   = $planetrow[$resource[$ShipID]];
                                $fleet['fleetlist']            .= $ShipID .",". $planetrow[$resource[$ShipID]] .";";
                                $fleet['amount']               += $planetrow[$resource[$ShipID]];
                                $PartialCount                  += $planetrow[$resource[$ShipID]];
                                $PartialFleet                   = true;
                        } else {
                                $fleet['fleetarray'][$ShipID]   = $_POST[$TName];
                                $fleet['fleetlist']            .= $ShipID .",". $_POST[$TName] .";";
                                $fleet['amount']               += $_POST[$TName];
                                $speedalls[$ShipID]             = $_POST[$TName];
                        }
                }
        }

        $PrNoob      = $game_config['noobprotection'];
        $PrNoobTime  = $game_config['noobprotectiontime'];
        $PrNoobMulti = $game_config['noobprotectionmulti'];

        // Petit Test de coherance
        $galaxy          = intval($_POST['galaxy']);
        if ($galaxy > 9 || $galaxy < 1) {
                $ResultMessage = "602;".$lang['gs_c602']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        $system = intval($_POST['system']);
        if ($system > 499 || $system < 1) {
                $ResultMessage = "602;".$lang['gs_c602']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        $planet = intval($_POST['planet']);
        if ($planet > 15 || $planet < 1) {
                $ResultMessage = "602;".$lang['gs_c602']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        $FleetArray = $fleet['fleetarray'];

        $CurrentFlyingFleets = doquery("SELECT COUNT(fleet_id) AS `Nbre` FROM {{table}} WHERE `fleet_owner` = '".$user['id']."';", 'fleets', true);
        $CurrentFlyingFleets = $CurrentFlyingFleets["Nbre"];

        $QrySelectEnemy  = "SELECT * FROM {{table}} ";
        $QrySelectEnemy .= "WHERE ";
        $QrySelectEnemy .= "`galaxy` = '". $_POST['galaxy'] ."' AND ";
        $QrySelectEnemy .= "`system` = '". $_POST['system'] ."' AND ";
        $QrySelectEnemy .= "`planet` = '". $_POST['planet'] ."' AND ";
        $QrySelectEnemy .= "`planet_type` = '". $_POST['planettype'] ."';";
        $TargetRow = doquery( $QrySelectEnemy, 'planets', true);

        if       ($TargetRow['id_owner'] == '') {
                $TargetUser = $user;
        } elseif ($TargetRow['id_owner'] != '') {
                $TargetUser = doquery("SELECT * FROM {{table}} WHERE `id` = '". $TargetRow['id_owner'] ."';", 'users', true);
        }
        $UserPoints    = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."';", 'statpoints', true);
        $User2Points   = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $TargetUser['id'] ."';", 'statpoints', true);

        $CurrentPoints = $UserPoints['total_points'];
        $TargetPoints  = $User2Points['total_points'];
        $TargetVacat   = $TargetUser['urlaubs_modus'];

        // Test s'il y a un slot de libre au moins !
        if (GetMaxFleets($user) <= $CurrentFlyingFleets){
                $ResultMessage = "612;".$lang['gs_c612']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

            if(($_POST["mission"] == 6) AND ($UserSpyProbes == 0)) {
        $ResultMessage = "604k;".$lang['gs_c604k']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
        die ( $ResultMessage );
    }

            if(($_POST["mission"] == 8) AND ($UserRecycles == 0)) {
        $ResultMessage = "604r;".$lang['gs_c604r']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
        die ( $ResultMessage );
    }


        // Y a une flotte dans la variable ??
        if (!is_array($FleetArray)) {
                $ResultMessage = "611;".$lang['gs_c611']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        // Faut pas deconner non plus ... c'est Espionnage OU Recyclage .... Pour le café vous repasserez !!
        if (! (($_POST["mission"] == 6) OR
                   ($_POST["mission"] == 8)) ) {
                $ResultMessage = "618;".$lang['gs_c618']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        // On teste une derniere fois s'il nous reste des billes ...
        foreach ($FleetArray as $Ships => $Count) {
                if ($Count > $planetrow[$resource[$Ships]]) {
                        $ResultMessage = "611;".$lang['gs_c611']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                        die ( $ResultMessage );
                }
        }

        if ($PrNoobTime < 1) {
                $PrNoobTime = 9999999999999999;
        }

        if ($TargetUser['onlinetime'] < (time() - 60*60*24*7)) {
                $PrNoobTime = 0;
        }

        if ($TargetVacat && $_POST['mission'] != 8) {
                $ResultMessage = "605;".$lang['gs_c605']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        if ($CurrentPoints          > ($TargetPoints * $PrNoobMulti) AND
                $TargetRow['id_owner'] != '' AND
                $_POST['mission']      == 6  AND
                $PrNoob                == 1  AND
                $TargetPoints           < ($PrNoobTime * 1000)) {
                $ResultMessage = "603;".$lang['gs_c603']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        if ($TargetPoints           > ($CurrentPoints * $PrNoobMulti) AND
                $TargetRow['id_owner'] != '' AND
                $_POST['mission']      == 6  AND
                $PrNoob                == 1  AND
                $CurrentPoints          < ($PrNoobTime * 1000)) {
                $ResultMessage = "604;".$lang['gs_c604']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        if ($TargetRow['id_owner'] == '' AND
                $_POST['mission']      != 8 ) {
                $ResultMessage = "601;".$lang['gs_c601']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        if (($TargetRow["id_owner"] == $planetrow["id_owner"]) AND ($_POST["mission"] == 6)) {
                $ResultMessage = "618;".$lang['gs_c618']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        if ($_POST['thisgalaxy'] != $planetrow['galaxy'] |
                $_POST['thissystem'] != $planetrow['system'] |
                $_POST['thisplanet'] != $planetrow['planet'] |
                $_POST['thisplanettype'] != $planetrow['planet_type']) {
                $ResultMessage = "618;".$lang['gs_c618']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                die ( $ResultMessage );
        }

        $Distance    = GetTargetDistance ($_POST['thisgalaxy'], $_POST['galaxy'], $_POST['thissystem'], $_POST['system'], $_POST['thisplanet'], $_POST['planet']);
        $speedall    = GetFleetMaxSpeed ($FleetArray, 0, $user);
        $SpeedAllMin = min($speedall);
        $Duration    = GetMissionDuration ( 10, $SpeedAllMin, $Distance, GetGameSpeedFactor ());

        $fleet['fly_time']   = $Duration;
        $fleet['start_time'] = $Duration + time();
        $fleet['end_time']   = ($Duration * 2) + time();

        $FleetShipCount      = 0;
        $FleetDBArray        = "";
        $FleetSubQRY         = "";
        $consumption         = 0;
        $SpeedFactor         = GetGameSpeedFactor ();
        foreach ($FleetArray as $Ship => $Count) {
                $ShipSpeed        = $pricelist[$Ship]["speed"];
                $spd              = 35000 / ($Duration * $SpeedFactor - 10) * sqrt($Distance * 10 / $ShipSpeed);
                $basicConsumption = $pricelist[$Ship]["consumption"] * $Count ;
                $consumption     += $basicConsumption * $Distance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
                $FleetShipCount  += $Count;
                $FleetDBArray    .= $Ship .",". $Count .";";
                $FleetSubQRY     .= "`".$resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . " , ";
        }
        $consumption = round($consumption) + 1;

        if ($TargetRow['id_level'] > $user['authlevel']) {
                $Allowed = true;
                switch ($_POST['mission']){
                        case 1:
                        case 2:
                        case 6:
                        case 9:
                                $Allowed = false;
                                break;
                        case 3:
                        case 4:
                        case 5:
                        case 7:
                        case 8:
                        case 15:
                                break;
                        default:
                }
                if ($Allowed == false) {
                        $ResultMessage = "619;".$lang['gs_c619']."|".$CurrentFlyingFleets." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;
                        die ( $ResultMessage );
                }
        }

        $QryInsertFleet  = "INSERT INTO {{table}} SET ";
        $QryInsertFleet .= "`fleet_owner` = '". $user['id'] ."', ";
        $QryInsertFleet .= "`fleet_mission` = '". intval($_POST['mission']) ."', ";
        $QryInsertFleet .= "`fleet_amount` = '". $FleetShipCount ."', ";
        $QryInsertFleet .= "`fleet_array` = '". $FleetDBArray ."', ";
        $QryInsertFleet .= "`fleet_start_time` = '". $fleet['start_time']. "', ";
        $QryInsertFleet .= "`fleet_start_galaxy` = '". intval($_POST['thisgalaxy']) ."', ";
        $QryInsertFleet .= "`fleet_start_system` = '". intval($_POST['thissystem']) ."', ";
        $QryInsertFleet .= "`fleet_start_planet` = '". intval($_POST['thisplanet']) ."', ";
        $QryInsertFleet .= "`fleet_start_type` = '". intval($_POST['thisplanettype']) ."', ";
        $QryInsertFleet .= "`fleet_end_time` = '". $fleet['end_time'] ."', ";
        $QryInsertFleet .= "`fleet_end_galaxy` = '". intval($_POST['galaxy']) ."', ";
        $QryInsertFleet .= "`fleet_end_system` = '". intval($_POST['system']) ."', ";
        $QryInsertFleet .= "`fleet_end_planet` = '". intval($_POST['planet']) ."', ";
        $QryInsertFleet .= "`fleet_end_type` = '". intval($_POST['planettype']) ."', ";
        $QryInsertFleet .= "`fleet_target_owner` = '". $TargetRow['id_owner'] ."', ";
        $QryInsertFleet .= "`start_time` = '" . time() . "';";
        doquery( $QryInsertFleet, 'fleets');

        $UserDeuterium   -= $consumption;
        $QryUpdatePlanet  = "UPDATE {{table}} SET ";
        $QryUpdatePlanet .= $FleetSubQRY;
        $QryUpdatePlanet .= "`deuterium` = '".$UserDeuterium."' " ;
        $QryUpdatePlanet .= "WHERE ";
        $QryUpdatePlanet .= "`id` = '". $planetrow['id'] ."';";
        doquery( $QryUpdatePlanet, 'planets');

        $CurrentFlyingFleets++;

        $planetrow = doquery("SELECT * FROM {{table}} WHERE `id` = '". $user['current_planet'] ."';", 'planets', true);
        $ResultMessage  = "600;". $lang['gs_sending'] ." ". $FleetShipCount  ." ". $lang['tech'][$Ship] ." ". $lang['gs_to'] ." ". $_POST['galaxy'] .":". $_POST['system'] .":". $_POST['planet'] ."...|";
        $ResultMessage .= $CurrentFlyingFleets ." ".$UserSpyProbes." ".$UserRecycles." ".$UserMissiles;

        die ( $ResultMessage );
?>
