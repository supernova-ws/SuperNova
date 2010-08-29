<?php
  // fleet.php
  // @version 2.0 - Serious optimizations: remove redundant fields etc Also makes compliant with PCG
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

  if (!$planetrow)
  {
    $parse_err['title'] = $lang['fl_error'];
    $parse_err['mes']   = $lang['fl_noplanetrow'];

    $parse['ErrorNoPlanetRow'] = parsetemplate(gettemplate('message_body'), $parse_err);
  }

  CheckPlanetUsedFields($planetrow);

  $template = gettemplate('fleet', true);

  $i  = 0;
  $fq = doquery("SELECT * FROM {{fleets}} WHERE fleet_owner={$user['id']}");

  while ($f = mysql_fetch_array($fq))
  {
    $i++;
    $fleet_data = flt_parse_for_template($f, $i);
    $template->assign_block_vars('fleets', $fleet_data['fleet']);

    foreach($fleet_data['ships'] as $ship_data)
    {
      $template->assign_block_vars('fleets.ships', $ship_data);
    }

/*
    if ($f['fleet_mess'] == 0 && $f['fleet_mission'] == 2)
    {
      $aks = doquery("SELECT * FROM {{aks}} WHERE id={$f['fleet_group']}", '', true);
    };
    $template->assign_block_vars('fleets', array(
      'NUMBER'       => $i,
      'ID'           => $f['fleet_id'],
      'MESSAGE'      => $f['fleet_mess'],
      'MISSION'      => $f['fleet_mission'],
      'MISSION_NAME' => $lang['type_mission'][$f['fleet_mission']],
      'ACS'          => $aks['name'],
      'AMOUNT'       => pretty_number($f['fleet_amount']),

      'METAL'        => $f['fleet_resource_metal'],
      'CRYSTAL'      => $f['fleet_resource_crystal'],
      'DEUTERIUM'    => $f['fleet_resource_deuterium'],

      'START_TYPE'   => $lang['fl_shrtcup'][$f['fleet_start_type']],
      'START_PLANET' => "[{$f['fleet_start_galaxy']}:{$f['fleet_start_system']}:{$f['fleet_start_planet']}]",
      'START_TIME'   => date('d.m.Y H:i:s', $f['fleet_end_time']),
      'START_LEFT'   => floor($f['fleet_end_time'] + 1 - $time_now),

      'END_TYPE'     => $lang['fl_shrtcup'][$f['fleet_end_type']],
      'END_PLANET'   => "[{$f['fleet_end_galaxy']}:{$f['fleet_end_system']}:{$f['fleet_end_planet']}]",
      'END_TIME'     => date('d.m.Y H:i:s', $f['fleet_start_time']),
      'END_LEFT'     => floor($f['fleet_start_time'] + 1 - $time_now),

      'STAY_TIME'    => date('d.m.Y H:i:s', $f['fleet_end_stay']),
      'STAY_LEFT'    => floor($f['fleet_end_stay'] + 1 - $time_now),
    ));

    $fleet = explode(';', $f['fleet_array']);
    foreach ($fleet as $a => $b)
    {
      if ($b)
      {
        $a = explode(',', $b);
        $template->assign_block_vars('fleets.ships', array(
          'NAME' => $lang['tech'][$a[0]],
          'AMOUNT' => $a[1],
        ));
      }
    }
*/
  }

  foreach ($reslist['fleet'] as $n => $i)
  {
    if ($planetrow[$resource[$i]] > 0)
    {
      $template->assign_block_vars('ships', array(
        'ID'          => $i,
        'NAME'        => $lang['tech'][$i],
  		  'AMOUNT'      => $planetrow[$resource[$i]],
// Reserved for later use - show on page-0 fleed capacity/speed/consumption
        'CONSUMPTION' => GetShipConsumption ( $i, $user ),
        'SPEED'       => get_ship_speed($i, $user),
        'CAPACITY'    => $pricelist[$i]['capacity'],
      ));
    }
  }

  $template->assign_vars(array(
    'TIME_NOW'           => $time_now,

    'FLYING_FLEETS'      => $FlyingFleets,
    'MAX_FLEETS'         => $MaxFleets,
    'ExpeditionEnCours'  => $FlyingExpeditions,
    'EnvoiMaxExpedition' => $MaxExpeditions,
    'target_mission'     => $target_mission,
    'MISSION_NAME'		 => $lang['type_mission'][$target_mission],
    'TYPE_NAME'		    => $lang['fl_planettype'][$planet_type],

    'ShipList'           => $ShipList,
  ));

  display(parsetemplate($template, $parse), $lang['fl_title']);
?>