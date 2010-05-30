<?php
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
?>