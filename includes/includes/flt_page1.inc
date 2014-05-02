<?php

// floten1.php
// @version 1.0
// @copyright 2008 by Chlorel for XNova

if(SN_IN_FLEET !== true)
{
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

$template = gettemplate('fleet1', true);

$ships = sys_get_param('ships', array());
if(!is_array($ships))
{
  $ships = array();
}

foreach($ships as $ship_id => $ship_count)
{
  if(in_array($ship_id, sn_get_groups('fleet')) && $ship_count)
  {
    $ship_info = get_unit_param($ship_id);
    if($ship_count > mrc_get_level($user, $planetrow, $ship_id, false, true))
    {
      $page .= $lang['fl_noenought'];
    }
    else
    {
      $fleet['fleetarray'][$ship_id]  = $ship_count;
      // $fleet['fleetlist']            .= "{$ship_id},{$ship_count};";
      $fleet['amount']               += $ship_count;
    }
    $fleet['capacity'] += $ship_info[P_CAPACITY] * $ship_count;
    $ship_base_data = get_ship_data($ship_id, $user);
    $template->assign_block_vars('ships', array(
      'ID'          => $ship_id,
      'AMOUNT'      => $ship_count,
      'CONSUMPTION' => $ship_base_data['consumption'],
      'SPEED'       => $ship_base_data['speed'],
    ));
  }
}


if(empty($fleet['fleetarray']))
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
if(mysql_num_rows($colonies) > 1)
{
  while($row = mysql_fetch_assoc($colonies))
  {
    $template->assign_block_vars('colonies', array(
      'NAME'       => $row['name'],
      'GALAXY'     => $row['galaxy'],
      'SYSTEM'     => $row['system'],
      'PLANET'     => $row['planet'],
      'TYPE'       => $row['planet_type'],
      'TYPE_PRINT' => $lang['fl_shrtcup'][$row['planet_type']],
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
        'NAME'       => $row['name'],
        'GALAXY'     => $row['galaxy'],
        'SYSTEM'     => $row['system'],
        'PLANET'     => $row['planet'],
        'TYPE'       => $row['planet_type'],
        'TYPE_PRINT' => $lang['fl_shrtcup'][$row['planet_type']],
      ));
    }
  }
}

$template->assign_vars(array(
  'usedfleet'       => str_rot13(base64_encode(serialize($fleet['fleetarray']))),

  'speed_factor'     => flt_server_flight_speed_multiplier(),
  "t{$planet_type}" => 'SELECTED',

  'target_mission'  => $target_mission,

  'ships'           => str_rot13(base64_encode(serialize($ships))),

  'fleet_speed'     => flt_fleet_speed($user, $fleet['fleetarray']),
  'fleet_capacity'  => $fleet['capacity'],

  'PLANET_DEUTERIUM'   => pretty_number($planetrow['deuterium']),

  'PAGE_HINT'       => $lang['fl_page1_hint'],
));

$template->assign_recursive($template_result);
display($template, $lang['fl_title']);
