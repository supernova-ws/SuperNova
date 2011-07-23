<?php
  // floten1.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

if(SN_IN_FLEET !== true)
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
      if ($ship_count > $planetrow[$sn_data[$ship_id]['name']])
      {
        $page .= $lang['fl_noenought'];
      }
      else
      {
        $fleet['fleetarray'][$ship_id]  = $ship_count;
        $fleet['fleetlist']            .= "{$ship_id},{$ship_count};";
        $fleet['amount']               += $ship_count;
      }
      $fleet['capacity'] += $sn_data[$ship_id]['capacity'] * $ship_count;
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
    message($lang['fl_err_no_ships'], $lang['fl_error'], 'fleet.' . PHP_EX, 5);
  }

  // Building list of shortcuts
  $query = doquery("SELECT * FROM {{shortcut}} WHERE shortcut_user_id = {$user['id']};");
  while($shortcut = mysql_fetch_assoc($query))
  {
    $template->assign_block_vars('shortcut', array(
      'NAME'       => $shortcut['shortcut_text'],
      'GALAXY'     => $shortcut['shortcut_galaxy'],
      'SYSTEM'     => $shortcut['shortcut_system'],
      'PLANET'     => $shortcut['shortcut_planet'],
      'TYPE'       => $shortcut['shortcut_planet_type'],
      'TYPE_PRINT' => $lang['fl_shrtcup'][$shortcut['shortcut_planet_type']],
    ));
  }

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
    foreach($members as $a => $b)
    {
      if ($b == $user['id'])
      {
        $template->assign_block_vars('acss', array(
          'ID'         => $row['id'],
          'GALAXY'     => $row['galaxy'],
          'SYSTEM'     => $row['system'],
          'PLANET'     => $row['planet'],
          'TYPE'       => $row['planet_type'],
          'TYPE_PRINT' => $lang['fl_shrtcup'][$row['planet_type']],
          'NAME'       => $row['name'],
        ));
      }
    }
  }

  $template->assign_vars(array(
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