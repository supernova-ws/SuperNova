<?php
  // floten2.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

  $fleetarray    = unserialize(base64_decode(str_rot13($_POST["usedfleet"])));
  $AllFleetSpeed = GetFleetMaxSpeed ($fleetarray, 0, $user);
  $GenFleetSpeed = intval($_POST['speed']);
  $MaxFleetSpeed = min($AllFleetSpeed);

  $distance      = GetTargetDistance ( $planetrow['galaxy'], $galaxy, $planetrow['system'], intval($_POST['system']), $planetrow['planet'], intval($_POST['planet']) );
  $duration      = GetMissionDuration ( $GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor );
  $consumption   = GetFleetConsumption ( $fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $user );

  $parse_temp['fl_expe_hours'] = $lang['fl_expe_hours'];
  $MissionSelector  = "";

  if (count($missiontype) > 0) {
    $parse['DisplayBadMission'] = 'display: none;';

    if (!$missiontype[MT_EXPLORE])
      $parse['HideExpedition'] = 'display: none;';

    foreach ($missiontype as $key => $value) {
      $parse_temp['MissionNum'] = $key;
      $parse_temp['MissionName'] = $value;
      $parse_temp['MissionChecked'] = ($target_mission == $key ? "checked":"");

      switch ($key){
        case MT_HOLD:
        case MT_EXPLORE:
          $parse_temp['MissionTime'] = parsetemplate(gettemplate('fleet_mission_time_row'), $parse_temp);
          break;
        default:
          $parse_temp['MissionTime'] = '';
      }

      $MissionSelector .= parsetemplate(gettemplate('fleet_mission_row'), $parse_temp);
    };
  };
  $parse['MissionSelector'] = $MissionSelector;

  $FleetHiddenBlock = '';
  foreach ($fleetarray as $Ship => $Count) {
    $parse_temp['ShipID'] = $Ship;
    $parse_temp['ShipNum'] = $Count;
    $parse_temp['ShipCapacity'] = $pricelist[$Ship]['capacity'];
    $parse_temp['ShipConsumption'] = GetShipConsumption ( $Ship, $user );
    $parse_temp['ShipSpeed'] = GetFleetMaxSpeed ( "", $Ship, $user );
    $FleetHiddenBlock .= parsetemplate(gettemplate('fleet_hidden_row'), $parse_temp);
  }
  $parse['FleetHidden'] = $FleetHiddenBlock;


  $TableTitle = "[" . $planetrow['galaxy'] .":". $planetrow['system'] .":". $planetrow['planet'] ."] ";
  $TableTitle .= $lang['fl_planettype' . $planetrow['planet_type']] . " " . $planetrow['name'];
  $TableTitle .= "&nbsp;=&gt;&nbsp;";
  $TableTitle .= "[" . $galaxy .":". $system .":". $planet ."] ";
  switch ($target_mission){
    case MT_COLONIZE:
    case MT_EXPLORE:
      $TableTitle .= $lang['type_mission'][$target_mission];
      break;
    default:
      $TableTitle .= $lang['fl_planettype' . $TargetPlanet['planet_type']]  . " " . $TargetPlanet['name'];
  }
  $parse['TableTitle'] = $TableTitle;

//  $parse['MsgExpedition'] = $lang['type_mission'][MT_EXPLORE];
//  if ( $missiontype[5] == '' )
//    $parse['HideTransport']  = 'display: none;';

  $parse['consumption'] = $consumption ;
  $parse['dist'] = $distance ;
  $parse['speedallsmin'] = floatval($_POST["speedallsmin"]) ;
  $parse['speed'] = intval($_POST['speed']) ;
  $parse['maxepedition'] = intval($_POST['maxepedition']) ;
  $parse['curepedition'] = intval($_POST['curepedition']) ;
  $parse['fleet_group'] = intval($_POST['fleet_group']) ;
  $parse['acs_target_mr'] = ($_POST['acs_target_mr']) ;
  $parse['usedfleet'] = $_POST["usedfleet"];

  $page = parsetemplate(gettemplate('fleet2', true), $parse);
  display($page, $lang['fl_title']);
?>