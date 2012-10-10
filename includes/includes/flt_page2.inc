<?php
// floten2.php
// @version 1.0
// @copyright 2008 by Chlorel for XNova

function sn_fleet_page2()
{
  global $missiontype, $target_mission, $fleetarray, $sn_data, $planetrow, $lang;
  global $galaxy, $system, $planet, $planet_type, $TargetPlanet, $consumption, $template_result;
  global $sn_module, $user;

  $template = gettemplate('fleet2', true);

  foreach ($missiontype as $key => $value)
  {
    $template->assign_block_vars('missions', array(
      'ID' => $key,
      'NAME' => $value,
      'CHECKED' => $target_mission == $key,
    ));
  };

  $fleetarray = is_array($fleetarray) ? $fleetarray : array();
  $fleet_capacity = 0;
  foreach ($fleetarray as $Ship => $Count)
  {
    $fleet_capacity += $sn_data[$Ship]['capacity'] * $Count;
  }

  /*
  //  $TableTitle  = "[{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}] ";
  //  $TableTitle .= $lang["fl_planettype{$planetrow['planet_type']}"] . ' ' . $planetrow['name'];
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
  */
  $temp = array(
    'galaxy' => $galaxy,
    'system' => $system,
    'planet' => $planet,
    'planet_type' => $planet_type,
    'name' => $TargetPlanet['name'] ? $TargetPlanet['name'] : $lang['type_mission'][$target_mission],
  );
  $TableTitle = uni_render_planet_full($planetrow) . '&nbsp;=&gt;&nbsp;' . uni_render_planet_full($temp);

  for($i = 0; $i<3; $i++)
  {
    $amount = $planetrow[$sn_data['groups']['resources'][$i]] - ($i == 2 ? $consumption : 0);
    $template->assign_block_vars('resources', array(
      'ID' => $i,
      'ON_PLANET' => floor($amount),
      'TEXT' => pretty_number($amount),
      'NAME' => $lang['sys_' . $sn_data['groups']['resources'][$i]],
    ));
  }

  if($sn_module['unit_captain']->manifest['active'] && ($captain = mrc_get_level($user, $planetrow, UNIT_CAPTAIN)) && $captain['unit_location_type'] == LOC_PLANET)
  {
    $template->assign_vars(array(
      'CAPTAIN_ID' => $captain['unit_id'],
      'CAPTAIN_SHIELD' => $captain['captain_shield'],
      'CAPTAIN_ARMOR' => $captain['captain_armor'],
      'CAPTAIN_ATTACK' => $captain['captain_attack'],
    ));
  }

  $template->assign_vars(array(
    'planet_metal'     => floor($planetrow['metal']),
    'planet_crystal'   => floor($planetrow['crystal']),
    'planet_deuterium' => floor($planetrow['deuterium'] - $consumption),

    'fleet_capacity'   => $fleet_capacity - $consumption,
    'usedfleet'        => $_POST['usedfleet'],

    'TableTitle' => $TableTitle,

    'speedallsmin' => sys_get_param_float('speedallsmin'),
    'speed' => sys_get_param_int('speed') ,

    'fleet_group' => sys_get_param_id('fleet_group') ,
    'acs_target_mr' => sys_get_param_str('acs_target_mr'),
  ));

  $template->assign_recursive($template_result);
  display($template, $lang['fl_title']);
}

?>
