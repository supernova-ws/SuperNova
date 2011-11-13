<?php
  // floten2.php
  // @version 1.0
  // @copyright 2008 by Chlorel for XNova

if(SN_IN_FLEET !== true)
{
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

  $template = gettemplate('fleet2', true);

  foreach ($missiontype as $key => $value) {
    $template->assign_block_vars('missions', array(
      'ID' => $key,
      'NAME' => $value,
      'CHECKED' => $target_mission == $key,
    ));
  };

  $fleet_capacity = 0;
  if(!is_array($fleetarray))
  {
    $fleetarray = array();
  }

  foreach ($fleetarray as $Ship => $Count)
  {
    $fleet_capacity += $sn_data[$Ship]['capacity'] * $Count;
  }

  $TableTitle  = "[{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}] ";
  $TableTitle .= $lang["fl_planettype{$planetrow['planet_type']}"] . ' ' . $planetrow['name'];
  $TableTitle .= '&nbsp;=&gt;&nbsp;';
  $TableTitle .= "[{$galaxy}:{$system}:{$planet}] ";
  switch ($target_mission){
    case MT_COLONIZE:
    case MT_EXPLORE:
      $TableTitle .= $lang['type_mission'][$target_mission];
    break;

    case MT_RECYCLE:
      $TableTitle .= $lang['fl_planettype2'];
    break;

    default:
      $TableTitle .= $lang['fl_planettype' . $TargetPlanet['planet_type']]  . ' ' . $TargetPlanet['name'];
  }
  $parse['TableTitle'] = $TableTitle;

  $parse['speedallsmin'] = floatval($_POST['speedallsmin']) ;
  $parse['speed'] = intval($_POST['speed']) ;

  $parse['fleet_group'] = intval($_POST['fleet_group']) ;
  $parse['acs_target_mr'] = ($_POST['acs_target_mr']) ;

  for($i = 0; $i<3; $i++){
    $template->assign_block_vars('resources', array(
      'ID' => $i,
      'ON_PLANET' => floor($planetrow[$sn_data['groups']['resources'][$i]] - ($i == 2 ? $consumption : 0)),
      'NAME' => $lang['sys_' . $sn_data['groups']['resources'][$i]],
    ));
  }

  $template->assign_vars(array(
    'planet_metal'     => floor($planetrow['metal']),
    'planet_crystal'   => floor($planetrow['crystal']),
    'planet_deuterium' => floor($planetrow['deuterium'] - $consumption),

    'fleet_capacity'   => $fleet_capacity - $consumption,
//    'consumption'		  => $consumption,
    'usedfleet'        => $_POST['usedfleet'],
  ));

  display(parsetemplate($template, $parse), $lang['fl_title']);
?>