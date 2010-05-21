<?php

/**
 * fleet.php
 *
 * Fleet manager
 *
 * @version 1.0s Security checks by Gorlum for http://supernova.ws
 * @version ?
 * @copyright (c) 2009 by Gorlum for http://ogame.triolan.com.ua
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}
includeLang('fleet');

$fleet_page = intval($_GET['fleet_page']);

$MaxFleets = GetMaxFleets(&$user);
$FlyingFleets = doquery("SELECT COUNT(fleet_id) as Number FROM {{table}} WHERE `fleet_owner`='{$user['id']}'", 'fleets', true);
$FlyingFleets = $FlyingFleets["Number"];
if ($MaxFleets <= $FlyingFleets AND $fleet_page) {
  message($lang['fl_noslotfree'], $lang['fl_error'], "fleet." . $phpEx, 5);
}

if($user[$resource[124]]>0){
  $FlyingExpeditions  = doquery("SELECT COUNT(fleet_owner) AS `expedi` FROM {{table}} WHERE `fleet_owner` = {$user['id']} AND `fleet_mission` = '15';", 'fleets', true);
  $FlyingExpeditions  = $FlyingExpeditions['expedi'];
  $MaxExpeditions = GetMaxExpeditions($user);
}else{
  $MaxExpeditions = 0;
  $FlyingExpeditions = 0;
};

check_urlaubmodus ($user);

if(IsVacationMode($user)){
  $parse['vacation'] = '<table width="525" height="25" cellpadding="0" cellspacing="0" border="2" bordercolor="#FF0000"><tr><td><center><font size=2 color=red><b><blink>  '.$lang['VacationMode'].'  </blink></b></font></center></td></tr></table>';
  message($lang['Vacation_mode'], $lang['Error'], "fleet.php", 1);
}

$galaxy = max(intval($_GET['galaxy']), intval($_POST['galaxy']));
if (!$galaxy) {
  $galaxy = $planetrow['galaxy'];
}

$system = max(intval($_GET['system']), intval($_POST['system']));
if (!$system) {
  $system = $planetrow['system'];
}

$planet = max(intval($_GET['planet']), intval($_POST['planet']));
if (!$planet) {
  $planet = $planetrow['planet'];
}

$planet_type = max(intval($_GET['planet_type']), intval($_POST['planet_type']));
if (!$planet_type)
  $planet_type = max(intval($_GET['planettype']), intval($_POST['planettype']));
if (!$planet_type)
  $planet_type = $planetrow['planet_type'];

$target_mission = max(intval($_GET['target_mission']), intval($_POST['target_mission']));
if ($planet > MAX_PLANET_IN_SYSTEM)
  $target_mission = MT_EXPLORE;

$fleet_group_mr = intval($_POST['fleet_group']);

if ($galaxy AND $system AND $planet AND $planet_type){
  $TargetPlanet = doquery("SELECT * FROM {{table}} WHERE galaxy = {$galaxy} and system = {$system} AND planet = {$planet} and planet_type = {$planet_type}", "planets", true);

  if ($TargetPlanet['id_owner']){
    $UsedPlanet = true;
    if ($TargetPlanet['id_owner'] == $user['id']) {
      $YourPlanet = true;
    }else{
      $YourPlanet = false;
    };
  }else{
    $YourPlanet = false;
    $UsedPlanet = false;
  };

  if ($planet > MAX_PLANET_IN_SYSTEM) {
    $missiontype = array(MT_EXPLORE => $lang['type_mission'][MT_EXPLORE]);
  }else{
    $missiontype = array();

    if (intval($_POST['ship209'])>=1){
      $isRecycler = true;
      if ($planet_type == PT_DEBRIS)
        $missiontype[MT_RECYCLE] = $lang['type_mission'][MT_RECYCLE];
    };

    if (($planet_type == PT_PLANET) || ($planet_type == PT_MOON)) {
      if ((intval($_POST['ship210'])>=1)){
        if(!$YourPlanet && $UsedPlanet){
          $missiontype[MT_SPY] = $lang['type_mission'][MT_SPY];
        }elseif ($YourPlanet){
          $missiontype[MT_RELOCATE] = $lang['type_mission'][MT_RELOCATE];
        }
      };

      if (intval($_POST['ship208'])>=1){
        $isColonizer = true;
        if (!$UsedPlanet)
          $missiontype[MT_COLONIZE] = $lang['type_mission'][MT_COLONIZE];
      };

      if ((intval($_POST['ship202']) >= 1) || (intval($_POST['ship203'])>=1) || $isColonizer || $isRecycler){
        $isTransport = true;
      };

      if ($isTransport ||
        $_POST['ship204'] >= 1 ||
        $_POST['ship205'] >= 1 ||
        $_POST['ship206'] >= 1 ||
        $_POST['ship207'] >= 1 ||
        $_POST['ship211'] >= 1 ||
        $_POST['ship213'] >= 1 ||
        $_POST['ship214'] >= 1 ||
        $_POST['ship215'] >= 1 ||
        $_POST['ship216'] >= 1)
      {
        if ($UsedPlanet)
          $missiontype[MT_TRANSPORT] = $lang['type_mission'][MT_TRANSPORT];

        if ($YourPlanet) {
          $missiontype[MT_RELOCATE] = $lang['type_mission'][MT_RELOCATE];
        }else{
          if ($UsedPlanet) {
            $missiontype[MT_ATTACK] = $lang['type_mission'][MT_ATTACK];
            $missiontype[MT_HOLD] = $lang['type_mission'][MT_HOLD];

            if (($planet_type == PT_MOON) AND (intval($_POST['ship214']) >= 1))
              $missiontype[MT_DESTROY] = $lang['type_mission'][MT_DESTROY];

            if ($fleet_group_mr > 0)
              $missiontype[MT_AKS] = $lang['type_mission'][MT_AKS];
          };
        };
      };
    };
  };

  if(!$TargetPlanet AND ($planet <= MAX_PLANET_IN_SYSTEM) AND (!isColonizer)){
    message ("<font color=\"red\"><b>". $lang['fl_no_planettype'] ."</b></font>", $lang['fl_error']);
  }

  if (!$target_mission)
    $target_mission = MT_ATTACK;

  if (!$UsedPlanet and $isColonizer)
    $target_mission = MT_COLONIZE;

  ksort($missiontype);
};

$SpeedFactor = GetGameSpeedFactor ();

$parse = $lang;

$parse['galaxy'] = $galaxy;
$parse['system'] = $system;
$parse['planet'] = $planet;
$parse['planet_type'] = $planet_type;

$parse['thisgalaxy'] = $planetrow['galaxy'];
$parse['thissystem'] = $planetrow['system'];
$parse['thisplanet'] = $planetrow['planet'];
$parse['thisresource1'] = floor($planetrow['metal']);
$parse['thisresource2'] = floor($planetrow['crystal']);
$parse['thisresource3'] = floor($planetrow['deuterium']);
$parse['speedfactor'] = GetGameSpeedFactor();

$time_now = time();

// ----------------------------------------------------------------------------------------------------------------------------
if (!$fleet_page) {
// ----------------------------------------------------------------------------------------------------------------------------
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
// ----------------------------------------------------------------------------------------------------------------------------
}elseif ($fleet_page==1){
// ----------------------------------------------------------------------------------------------------------------------------
  // floten1.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

  // Verifions si nous avons bien tout ce que nous voullons envoyer
  $FleetHiddenBlock  = "";
  foreach ($reslist['fleet'] as $n => $i) {
    if ($i > 200 && $i < 300 && $_POST["ship$i"] > "0") {
      $count = abs(intval($_POST["ship$i"]));
      if ($count > $planetrow[$resource[$i]]) {
        $page .= $lang['fl_noenought'];
        $speedalls[$i]             = GetFleetMaxSpeed ( "", $i, $user );
      } else {
        $fleet['fleetarray'][$i]   = $count;
        // Tableau des vaisseaux avec leur nombre
        $fleet['fleetlist']       .= $i . "," . $count . ";";
        // Nombre total de vaisseaux
        $fleet['amount']          += $count;

        // Tableau des vitesses
        $parse_temp['ShipID'] = $i;
        $parse_temp['ShipNum'] = $count;
        $parse_temp['ShipCapacity'] = $pricelist[$i]['capacity'];
        $parse_temp['ShipConsumption'] = GetShipConsumption ( $i, $user );
        $parse_temp['ShipSpeed'] = intval(GetFleetMaxSpeed ("", $i, $user));
        $FleetHiddenBlock .= parsetemplate(gettemplate('fleet_hidden_row'), $parse_temp);

        $speedalls[$i]             = GetFleetMaxSpeed ( "", $i, $user );
      }
    }
  }
  $parse['FleetHiddenBlock'] = $FleetHiddenBlock;

  if (!$fleet['fleetlist']) {
    message($lang['fl_unselectall'], $lang['fl_error'], "fleet." . $phpEx, 1);
  } else {
    $speedallsmin = min($speedalls);
  }

  // Building list of shortcuts
  $page = '';
  if ($user['fleet_shortcut']) {
    $scarray = explode("\r\n", $user['fleet_shortcut']);
    $i = 0;
    foreach ($scarray as $a => $b) {
      if ($b != "") {
        $c = explode(',', $b);

        if (($i % 2) == 0) {
          $page .= "<tr height=\"20\">";
        }

        $page .= "<th><a href=\"javascript:setTarget(". $c[1] .",". $c[2] .",". $c[3] .",". $c[4] ."); shortInfo();\"";
        $page .= ">". $c[0] ." [". $c[1] .":". $c[2] .":". $c[3] ."] ";
        // Type of shortcuts: planet/debris/moon
        $page .= $lang['fl_shrtcup'][$c[4]];
        $page .= "</a></th>";

        if ($i % 2) {
          $page .= "</tr>";
        }
        $i++;
      }
    }
    if ($i % 2) {
      $page .= "<th></th></tr>";
    }
    $parse['DisplayNoShortcuts'] = 'display: none;';
  };
  $parse['shortcuts'] = $page;

  // Building list of own planets & moons
  $page = '';
  $kolonien      = SortUserPlanets ( $user );
  if (mysql_num_rows($kolonien) > 1) {
    $i = 0;
    while ($row = mysql_fetch_array($kolonien)) {
      if (($i % 2) == 0) {
        $page .= "<tr height=\"20\">";
      }
      if ($row['planet_type'] == 3) {
        $row['name'] .= " ". $lang['fl_shrtcup3'];
      }
      $page .= "<th><a href=\"javascript:setTarget(". $row['galaxy'] .",". $row['system'] .",". $row['planet'] .",". $row['planet_type'] ."); shortInfo();\">". $row['name'] ." [". $row['galaxy'] .":". $row['system'] .":". $row['planet'] ."]</a></th>";
      if ($i % 2) {
        $page .= "</tr>";
      }
      $i++;
    }

    if ($i % 2) {
      $page .= "<th>&nbsp;</th></tr>";
    };
    $parse['DisplayNoColonies'] = 'display: none;';
  };
  $parse['ColoniesList'] = $page;

  //ACS Start
  //Need to look for acs attacks.
  $aks_madnessred = doquery("SELECT * FROM {{table}} ;", 'aks');
  while($row = mysql_fetch_array($aks_madnessred))
  {
    $members = explode(",", $row['eingeladen']);
    foreach($members as $a => $b) {
      if ($b == $user['id']) {
        $parse['aks_id'] = $row['id'];
        $parse['aks_galaxy'] = $row['galaxy'];
        $parse['aks_system'] = $row['system'];
        $parse['aks_planet'] = $row['planet'];
        $parse['aks_planet_type'] = $row['planet_type'];
        $parse['aks_name'] = $row['name'];

        $aks_fleets_mr .= parsetemplate(gettemplate('fleet_aks_row'), $parse);
      }
    }
  }
  $parse['acss'] = $aks_fleets_mr;

  $parse['speedallsmin'] = $speedallsmin;
  $parse['usedfleet'] = str_rot13(base64_encode(serialize($fleet['fleetarray'])));

  $parse['speedfactor'] = $SpeedFactor;
  $parse['t' . $planet_type] = 'SELECTED';
  $parse['maxepedition'] = intval($_POST['maxepedition']);
  $parse['curepedition'] = intval($_POST['curepedition']);
  $parse['target_mission'] = $target_mission;

  $page = parsetemplate(gettemplate('fleet1'), $parse);

  display($page, $lang['fl_title']);
// ----------------------------------------------------------------------------------------------------------------------------
}elseif ($fleet_page==2){
// ----------------------------------------------------------------------------------------------------------------------------
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
  $parse['speedallsmin'] = intval($_POST["speedallsmin"]) ;
  $parse['speed'] = intval($_POST['speed']) ;
  $parse['usedfleet'] = intval($_POST["usedfleet"]) ;
  $parse['maxepedition'] = intval($_POST['maxepedition']) ;
  $parse['curepedition'] = intval($_POST['curepedition']) ;
  $parse['fleet_group'] = intval($_POST['fleet_group']) ;
  $parse['acs_target_mr'] = intval($_POST['acs_target_mr']) ;

  $page = parsetemplate(gettemplate('fleet2'), $parse);
  display($page, $lang['fl_title']);
// ----------------------------------------------------------------------------------------------------------------------------
}elseif ($fleet_page==3){
// ----------------------------------------------------------------------------------------------------------------------------
  // floten3.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

  // Test de coherance de la destination (voir si elle se trouve dans les limites de l'univers connu
  $errorlist = "";
  if (!$galaxy || $galaxy > MAX_GALAXY_IN_WORLD || $galaxy < 1)
    $errorlist .= $lang['fl_limit_galaxy'];
  if (!$system || $system > MAX_SYSTEM_IN_GALAXY || $system < 1)
    $errorlist .= $lang['fl_limit_system'];
  if (!$planet || $planet < 1 || ($planet > MAX_PLANET_IN_SYSTEM AND $target_mission != MT_EXPLORE ))
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

  $TransMetal      = max(0, intval($_POST['resource1']));
  $TransCrystal    = max(0, intval($_POST['resource2']));
  $TransDeuterium  = max(0, intval($_POST['resource3']));
  $StorageNeeded   = $TransMetal + $TransCrystal + $TransDeuterium;

  if ($StorageNeeded < 1 AND $target_mission == MT_TRANSPORT) {
    $errorlist .= $lang['fl_noenoughtgoods'];
  }

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

      if ($TargetPlanet["id_owner"] == $planetrow["id_owner"]){
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
          $debris = doquery("SELECT * FROM {{table}} WHERE galaxy = {$galaxy} and system = {$system} AND planet = {$planet}", "galaxy", true);
          if($debris['metal']+$debris['crystal']==0)
            $errorlist .= $lang['fl_nodebris'];
        }
      }
    }
  }


  $fleetarray  = unserialize(base64_decode(str_rot13($_POST["usedfleet"])));
  if (!is_array($fleetarray))
    $errorlist .= $lang['fl_no_fleetarray'];

  if ($errorlist)
    message ("<font color=\"red\"><ul>" . $errorlist . "</ul></font>", $lang['fl_error'], "fleet." . $phpEx, 2);

//  if (!isset($fleetarray)) {
//    message ("<font color=\"red\"><b>". $lang['fl_no_fleetarray'] ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
//  }
  // On verifie s'il y a assez de vaisseaux sur la planete !
  foreach ($fleetarray as $Ship => $Count) {
    if ($Count > $planetrow[$resource[$Ship]]) {
      message ("<font color=\"red\"><b>". $lang['fl_fleet_err'] ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
    }
  }


  //Normally... unless its acs...
  $fleet_group = max(0, intval($_POST['fleet_group']));
  //But is it acs??
  //Well all acs fleets must have a fleet code.
  if($fleet_group){
    //Also it must be mission type 2
    if($target_mission == MT_AKS){
      //The co-ords must be the same as where the acs fleet is going.
      $target = "g".$galaxy."s".$system."p".$planet."t".$planet_type;
      if($_POST['acs_target_mr'] == $target){
        //ACS attack must exist (if acs fleet has arrived this will also return false (2 checks in 1!!!)
        $aks = doquery("SELECT * FROM {{table}} WHERE id = {$fleet_group}",'aks', true);
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
  }
  //Check that a failed acs attack isn't being sent, if it is, make it an attack fleet.
  if((!$fleet_group) && ($target_mission == MT_AKS)){
    $target_mission = MT_ATTACK;
  }

  CheckPlanetUsedFields($planetrow);

  $protection      = $game_config['noobprotection'];
  $protectiontime  = $game_config['noobprotectiontime'];
  $protectionmulti = $game_config['noobprotectionmulti'];
  if ($protectiontime < 1) {
    $protectiontime = 9999999999999999;
  }

  $MyDBRec = doquery("SELECT * FROM {{table}} WHERE `id` = {$user['id']};", 'users', true);
  if ($TargetPlanet['id_owner'] == '') {
    $HeDBRec = $MyDBRec;
  } elseif ($TargetPlanet['id_owner'] != '') {
    $HeDBRec = doquery("SELECT * FROM {{table}} WHERE `id` = '". $TargetPlanet['id_owner'] ."';", 'users', true);
  }
  if ($HeDBRec['onlinetime'] < ($time_now - 60*60*24*7)) {
    $protectiontime = 0;
  }
  if ($MyDBRec['user_lastip'] == $HeDBRec['user_lastip'] AND $MyDBRec['user_lastip'] > 0 AND $HeDBRec['id'] != $MyDBRec['id']) {
    message ("<font color=\"red\"><b>". $lang['fl_multi_ip_protection'] ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
  }

  $MyGameLevel = doquery("SELECT total_points FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $MyDBRec['id'] ."';", 'statpoints', true);
  $HeGameLevel = doquery("SELECT total_points FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $HeDBRec['id'] ."';", 'statpoints', true);
  $MyGameLevel = $MyGameLevel['total_points'];
  $HeGameLevel = $HeGameLevel['total_points'];

  switch ($target_mission){
    case MT_ATTACK:
    case MT_AKS:
    case MT_HOLD:
    case MT_SPY:
    case MT_DESTROY:
      if ($TargetPlanet['id_level'] > $user['authlevel'])
        message ("<font color=\"red\"><b>". $lang['fl_adm_attak'] ."</b></font>", $lang['fl_warning'], "fleet." . $phpEx, 2);

      if( ($TargetPlanet['id_owner'] AND $protection) AND (
        (($HeGameLevel * $protectionmulti) < $MyGameLevel AND $HeGameLevel < ($protectiontime * 1000))
        OR
        (($MyGameLevel * $protectionmulti) < $HeGameLevel AND $MyGameLevel < ($protectiontime * 1000))
      ) ) message("<font color=\"lime\"><b>".$lang['fl_noob_mess_n']."</b></font>", $lang['fl_noob_title'], "fleet." . $phpEx, 2);
      break;
    default:
  }

  if ($HeDBRec['urlaubs_modus'] AND $target_mission != MT_RECYCLE) {
    message("<font color=\"lime\"><b>".$lang['fl_vacation_pla']."</b></font>", $lang['fl_vacation_ttl'], "fleet." . $phpEx, 2);
  }

  if ($target_mission != MT_EXPLORE) {
    if ($HeDBRec['ally_id'] != $MyDBRec['ally_id'] AND $target_mission == MT_RELOCATE) {
      message ("<font color=\"red\"><b>". $lang['fl_only_stay_at_home'] ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
    }
    if ($TargetPlanet['ally_deposit'] < 1 AND $HeDBRec != $MyDBRec AND $target_mission == MT_HOLD) {
      message ("<font color=\"red\"><b>". $lang['fl_no_allydeposit'] ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
    }
  }

  $speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
  $AllFleetSpeed  = GetFleetMaxSpeed ($fleetarray, 0, $user);
  $GenFleetSpeed  = intval($_POST['speed']);
  $MaxFleetSpeed  = min($AllFleetSpeed);
  if (!in_array($GenFleetSpeed, $speed_possible)) {
    message ("<font color=\"red\"><b>". $lang['fl_cheat_speed'] ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
  }
  if ($MaxFleetSpeed != $_POST['speedallsmin']) {
    message ("<font color=\"red\"><b>". $lang['fl_cheat_speed'] ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
  }

  $distance      = GetTargetDistance ( $planetrow['galaxy'], $galaxy, $planetrow['system'], $system, $planetrow['planet'], $planet );
  $duration      = GetMissionDuration ( $GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor );
  $consumption   = GetFleetConsumption ( $fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $user );

  $fleet['start_time'] = $duration + $time_now;
  if ($target_mission == MT_EXPLORE OR $target_mission == MT_HOLD) {
    $StayDuration = max(0,intval($_POST['missiontime'])) * 3600;
    $StayTime     = $fleet['start_time'] + $StayDuration;
  } else {
    $StayDuration = 0;
    $StayTime     = 0;
  }
  $fleet['end_time']   = $StayDuration + (2 * $duration) + $time_now;

  if ($aks AND $target_mission==MT_AKS){
    if ($fleet['start_time']>$aks['ankunft'])
      message ($lang['fl_aks_too_slow'] . 'Fleet arrival: ' . date(DATE_TIME,$fleet['start_time']) . " AKS arrival: " .date(DATE_TIME,$aks['ankunft']), $lang['fl_error']);
    $fleet['start_time'] = $aks['ankunft'];
    $fleet['end_time'] = $aks['ankunft'] + $duration;
  };

  $FleetStorage        = 0;
  $FleetShipCount      = 0;
  $fleet_array         = "";
  $FleetSubQRY         = "";
  foreach ($fleetarray as $Ship => $Count) {
    $FleetStorage    += $pricelist[$Ship]["capacity"] * $Count;
    $FleetShipCount  += $Count;
    $fleet_array     .= $Ship .",". $Count .";";
    $FleetSubQRY     .= "`{$resource[$Ship]}` = `{$resource[$Ship]}` - {$Count} , ";
  }
  $FleetStorage        -= $consumption;

  if ( $StorageNeeded > $FleetStorage) {
    message ("<font color=\"red\"><b>". $lang['fl_nostoragespa'] . pretty_number($StorageNeeded - $FleetStorage) ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
  }
  if ($planetrow['deuterium'] < $TransDeuterium + $consumption) {
    message ("<font color=\"red\"><b>". $lang['fl_no_deuterium'] . pretty_number($TransDeuterium + $consumption - $planetrow['deuterium']) ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
  }
  if (($planetrow['metal'] < $TransMetal) || ($planetrow['crystal'] < $TransCrystal)) {
    message ("<font color=\"red\"><b>". $lang['fl_no_resources'] ."</b></font>", $lang['fl_error'], "fleet." . $phpEx, 2);
  }

  // ecriture de l'enregistrement de flotte (a partir de lфе_, y a quelque chose qui vole et c'est toujours sur la planete d'origine)
  $QryInsertFleet  = "INSERT INTO {{table}} SET ";
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
  $QryInsertFleet .= "`fleet_target_owner` = '". $TargetPlanet['id_owner'] ."', ";
  $QryInsertFleet .= "`fleet_group` = '". $fleet_group ."', ";
  $QryInsertFleet .= "`start_time` = '". $time_now ."';";
  doquery( $QryInsertFleet, 'fleets');

  $planetrow["metal"]     -= $TransMetal;
  $planetrow["crystal"]   -= $TransCrystal;
  $planetrow["deuterium"] -= $TransDeuterium;
  $planetrow["deuterium"] -= $consumption;

  $QryUpdatePlanet  = "UPDATE {{table}} SET ";
  $QryUpdatePlanet .= $FleetSubQRY;
  // $QryUpdatePlanet .= "`metal` = '". $planetrow["metal"] ."', ";
  // $QryUpdatePlanet .= "`crystal` = '". $planetrow["crystal"] ."', ";
  // $QryUpdatePlanet .= "`deuterium` = '". $planetrow["deuterium"] ."' ";
  $QryUpdatePlanet .= "`metal` = `metal` - {$TransMetal}, ";
  $QryUpdatePlanet .= "`crystal` = `crystal` - {$TransCrystal}, ";
  $QryUpdatePlanet .= "`deuterium` = `deuterium` - {$TransDeuterium} - {$consumption} ";
  $QryUpdatePlanet .= "WHERE ";
  $QryUpdatePlanet .= "`id` = '". $planetrow['id'] ."'";

  // Mise a jours de l'enregistrement de la planete de depart (a partir de l?п, y a quelque chose qui vole et ce n'est plus sur la planete de depart)
  doquery("LOCK TABLE {{table}} WRITE", 'planets');
  doquery ($QryUpdatePlanet, "planets");
  doquery("UNLOCK TABLES", '');

//  if ($fleet_group) {
//    // doquery("UPDATE `{{table}}` SET `p_num` = `p_num` + 1 WHERE `id` = '{$fleet_group}';", 'aks');
//  };

  $parse["mission"] = $lang['type_mission'][$target_mission];
  if ($target_mission == MT_EXPLORE OR $target_mission == MT_HOLD) {
    // $parse["mission"] .= sprintf($lang['fl_duration_time'], $StayDuration);
    $parse["mission"] .= ' ' . pretty_time($StayDuration);
  };
  $parse["dist"] = pretty_number($distance);
  $parse["speed"] = pretty_number($MaxFleetSpeed);
  $parse["deute_need"] = pretty_number($consumption);
  $parse["from"] = $planetrow['galaxy'].":".$planetrow['system'].":".$planetrow['planet'];
  $parse["time_go"] = date(DATE_TIME, $fleet['start_time']);
  $parse["time_back"] = date(DATE_TIME, $fleet['end_time']);

  $parse_temp['DisplayControls'] = 'display: none;';
  $$ShipList = "";
  foreach ($fleetarray as $Ship => $Count) {
    $parse_temp['ShipSpeed'] =
    $parse_temp['ShipName'] = $lang['tech'][$Ship];
    $parse_temp['ShipNumPrint'] = pretty_number($Count);
    $ShipList .= parsetemplate(gettemplate('fleet_ship_row'), $parse_temp);
  }
  $parse['ShipList'] = $ShipList;

  // Provisoire
  sleep (1);

  $planetrow = doquery ("SELECT * FROM {{table}} WHERE `id` = '". $planetrow['id'] ."';", 'planets', true);

  $page = parsetemplate(gettemplate('fleet3'), $parse);
  display($page, $lang['fl_title']);
// ----------------------------------------------------------------------------------------------------------------------------
}elseif ($fleet_page==4){
// ----------------------------------------------------------------------------------------------------------------------------
  // verband.php
  // @version 1.0
  // @copyright 2008 by ??????? for XNova

  $fleetid = intval($_POST['fleetid']);
  $userToAdd = mysql_real_escape_string($_POST['addtogroup']);

  if (!is_numeric($fleetid) || empty($fleetid)) {
    header("Location: fleet.php");
    exit();
  }

  $query = doquery("SELECT * FROM `{{table}}` WHERE fleet_id = '{$fleetid}'", 'fleets');
  if (mysql_num_rows($query) != 1) {
    message($lang['fl_fleet_not_exists'], $lang['fl_error']);
  }

  $fleet = mysql_fetch_array($query);
  if ($fleet['fleet_start_time'] <= $time_now || $fleet['fleet_end_time'] < $time_now || $fleet['fleet_mess'] == 1) {
    message($lang['fl_isback'], $lang['fl_error']);
  }

  if ($fleet['fleet_owner'] != $user['id']) {
    $debug->warning($lang['fl_aks_hack_wrong_fleet'],"Hack attempt");
    message($lang['fl_aks_hack_wrong_fleet'], $lang['fl_error']);
  }

  $aks = doquery("SELECT * from `{{table}}` WHERE `flotten` = {$fleetid};", 'aks', true);
  // If we got a message to add some1 to attack (MadnessRed code)
  if($userToAdd){
    $userToAddID = doquery("SELECT `id` FROM `{{table}}` WHERE `username` like '{$userToAdd}';",'users',true);
    $userToAddID = $userToAddID['id'];

    if($userToAddID){
      if (!$aks){
        // No AKS exists - making one
        // SetSelectedPlanet ( $user );
        // CheckPlanetUsedFields($planetrow);

        if (!$fleet['fleet_group']) {
          doquery("INSERT INTO {{table}} SET
            `name` = 'KV{$fleetid}',
            `teilnehmer` = '" . $user['id'] . "',
            `flotten` = '" . $fleetid . "',
            `ankunft` = '" . $fleet['fleet_start_time'] . "',
            `galaxy` = '" . $fleet['fleet_end_galaxy'] . "',
            `system` = '" . $fleet['fleet_end_system'] . "',
            `planet` = '" . $fleet['fleet_end_planet'] . "',
            `planet_type` = '" . $fleet['fleet_end_type'] . "',
            `eingeladen` = '" . $user['id'] . "',
            `fleet_end_time` = '" . $fleet['fleet_end_time']. "'",'aks');

          $aks = doquery("SELECT * FROM {{table}} WHERE `flotten` = {$fleetid};", 'aks', true);

          doquery("UPDATE {{table}} SET fleet_group = '{$aks['id']}', fleet_mission = '" . MT_AKS . "' WHERE fleet_id = '{$fleetid}'", 'fleets');
          $fleet['fleet_group'] = $aks['id'];
        }else{
          message($lang['fl_aks_already_in_aks'],$lang['fl_error']);
        };
      };

      $isUserExists = false;
      $invited_ar = explode(",", $aks['eingeladen']);
      foreach($invited_ar as $inv){
        if ($userToAddID == $inv)
          $isUserExists = true;
      };

      if(count($invited_ar)>=5){
        message('Нельзя приглашать больше 5 человек','Ошибка');
      };

      if ($isUserExists) {
        $add_user_message_mr = sprintf($lang['fl_aks_player_invited_already'], $userToAdd);
      }else{
        print('adding user');
        $add_user_message_mr = sprintf($lang['fl_aks_player_invited'], $userToAdd);
        doquery("UPDATE `{{table}}` SET `eingeladen` = concat(`eingeladen`, ',{$userToAddID}') WHERE `flotten` = {$fleetid};",'aks')
          or die(sprintf($lang['fl_aks_adding_error'],mysql_error()));
        $aks['eingeladen'] .= ',' . $userToAddID;
      };
      SendSimpleMessage ( $userToAddID, $user['id'], $time_now, 1, $user['username'],
        $lang['fl_aks_invite_message_header'], sprintf( $lang['fl_aks_invite_message'], $user['username']));
    }else{
      $add_user_message_mr = sprintf($lang['fl_aks_player_error'], $userToAdd);
    }
  }

  $parse['fleetid'] = $fleetid;
  $parse['add_user_message_mr'] = $add_user_message_mr;

  $members = explode(",", $aks['eingeladen']);
  foreach($members as $a => $b) {
    if ($b != '') {
      $member_qry_mr = doquery("SELECT `username` FROM `{{table}}` WHERE `id` ='{$b}' ;",'users');
      while($row = mysql_fetch_array($member_qry_mr)){
        $invited .= "<option>".$row['username']."</option>";
      }
    }
  }
  $parse['members'] = $invited;

  $page = parsetemplate(gettemplate('fleet_aks_invite'), $parse);

  display($page, $lang['fl_title']);
}else{
// ----------------------------------------------------------------------------------------------------------------------------
  message($lang['sys_hackattempt'], $lang['sys_error'], "fleet." . $phpEx, 5);
};


// Updated by Gorlum Sep 2009 (extracting template from code)
// Updated by Chlorel. 16 Jan 2008 (String extraction, bug corrections, code uniformisation
// Created by Perberos. All rights reversed (C) 2006
?>