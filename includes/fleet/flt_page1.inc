<?php
  // floten1.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

if(!defined('SN_IN_FLEET') || SN_IN_FLEET !== true)
{
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

  $template = gettemplate('fleet1', true);

  $ships = $_POST['ships'];
  if(!is_array($ships))
  {
    $ships = array();
  }

  foreach ($ships as $ship_id => $ship_count)
  {
    if ($ship_id > 200 && $ship_id < 300 && $ship_count)
    {
      if ($ship_count > $planetrow[$resource[$ship_id]])
      {
        $page .= $lang['fl_noenought'];
      }
      else
      {
        $fleet['fleetarray'][$ship_id]  = $ship_count;
        $fleet['fleetlist']            .= "{$ship_id},{$ship_count};";
        $fleet['amount']               += $ship_count;
      }
      $fleet['capacity'] += $pricelist[$ship_id]['capacity'] * $ship_count;
      $template->assign_block_vars('ships', array(
        'ID'          => $ship_id,
        'AMOUNT'      => $ship_count,
        'CONSUMPTION' => GetShipConsumption ( $ship_id, $user ),
        'SPEED'       => get_ship_speed($ship_id, $user),
      ));
    }
  }

  if (!$fleet['fleetlist'])
  {
    message($lang['fl_err_no_ships'], $lang['fl_error'], "fleet.{$phpEx}", 5);
  }

  // Building list of shortcuts
  if ($user['fleet_shortcut'])
  {
    $shortcuts = explode("\r\n", $user['fleet_shortcut']);
    foreach ($shortcuts as $shortcut) {
      if (!$shortcut)
      {
        continue;
      }

      $shortcut_parsed = explode(',', $shortcut);

      $template->assign_block_vars('shortcuts', array(
        'NAME'       => $shortcut_parsed[0],
        'GALAXY'     => $shortcut_parsed[1],
        'SYSTEM'     => $shortcut_parsed[2],
        'PLANET'     => $shortcut_parsed[3],
        'TYPE'       => $shortcut_parsed[4],
        'TYPE_PRINT' => $lang['fl_shrtcup'][$shortcut_parsed[4]],
      ));
    }
  };

  // Building list of own planets & moons
  $colonies = SortUserPlanets ( $user );
  if (mysql_num_rows($colonies) > 1)
  {
    while ($row = mysql_fetch_assoc($colonies))
    {
      $template->assign_block_vars('colonies', array(
        'NAME'       => $row['name'],
        'GALAXY'     => $row['galaxy'],
        'SYSTEM'     => $row['system'],
        'PLANET'     => $row['planet'],
        'TYPE'       => $row['planet_type'],
        'TYPE_PRINT' => $lang['fl_shrtcup'][$row['planet_type']],
        // $row['planet_type'] == 3
      ));
    }
  };

  //ACS Start
  //Need to look for acs attacks.
  $aks_madnessred = doquery('SELECT * FROM {{aks}};');
  while($row = mysql_fetch_assoc($aks_madnessred))
  {
    $members = explode(',', $row['eingeladen']);
    foreach($members as $a => $b) {
      if ($b == $user['id']) {
        $template->assign_block_vars('acss', array(
          'ID'         => $row['id'],
          'GALAXY'     => $row['galaxy'],
          'SYSTEM'     => $row['system'],
          'PLANET'     => $row['planet'],
          'TYPE'       => $row['planet_type'],
          'TYPE_PRINT' => $lang['fl_shrtcup'][$row['planet_type']],
          'NAME'       => $row['name'],
        ));
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

  $template->assign_vars(array(
    'acss1'           => $aks_fleets_mr, // just data to print

    'usedfleet'       => str_rot13(base64_encode(serialize($fleet['fleetarray']))),

    'speed_factor'     => $SpeedFactor,
    "t{$planet_type}" => 'SELECTED',

    'target_mission'  => $target_mission,

    'ships'           => str_rot13(base64_encode(serialize($ships))),

    'fleet_speed'     => round(min(GetFleetMaxSpeed($fleet['fleetarray'], false, $user))),
    'fleet_capacity'  => $fleet['capacity'],

    'PAGE_HINT'       => $lang['fl_page1_hint'],
  ));

  display(parsetemplate($template, $parse), $lang['fl_title']);
?>