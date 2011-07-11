<?php

function int_get_fleet_to_planet($fleet_list, $phalanx = false)
{
  global $config, $user, $fleets, $fleet_number;

  $fleets = array();
  $fleet_number = 0;

  if(empty($fleet_list))
  {
    return;
  }

  if(is_string($fleet_list))
  {
    $flying_fleets_mysql = doquery($fleet_list);

    $fleet_list = array();
    while ($fleet = mysql_fetch_assoc($flying_fleets_mysql))
    {
      $fleet_list[] = $fleet;
    }
  }

  foreach($fleet_list as $fleet)
  {
    $planet_start_type = $fleet['fleet_start_type'] == 3 ? 3 : 1;
    $planet_start = doquery(
      "SELECT `name` FROM {{planets}}
        WHERE
          galaxy = {$fleet['fleet_start_galaxy']} AND
          system = {$fleet['fleet_start_system']} AND
          planet = {$fleet['fleet_start_planet']} AND
          planet_type = {$planet_start_type}
      ", '', true);
    $fleet['fleet_start_name'] = $planet_start['name'];

    if($fleet['fleet_end_planet'] > $config->game_maxPlanet)
    {
      $fleet['fleet_end_name'] = $lang['ov_fleet_exploration'];
    }
    elseif($fleet['fleet_mission'] == MT_COLONIZE)
    {
      $fleet['fleet_end_name'] = $lang['ov_fleet_colonization'];
    }
    else
    {
      $planet_end_type = $fleet['fleet_end_type'] == 3 ? 3 : 1;

      $planet_end = doquery(
        "SELECT `name` FROM {{planets}}
          WHERE
            galaxy = {$fleet['fleet_end_galaxy']} AND
            system = {$fleet['fleet_end_system']} AND
            planet = {$fleet['fleet_end_planet']} AND
            planet_type = {$planet_end_type}
        ", '', true);
      $fleet['fleet_end_name'] = $planet_end['name'];
    }

    if($fleet['fleet_start_time'] > $time_now && $fleet['fleet_mess'] == 0)
    {
      int_assign_event($fleet, 0, $phalanx);
    }

    if($fleet['fleet_end_stay'] && $fleet['fleet_end_stay'] > $time_now && $fleet['fleet_mess'] == 0)
    {
      int_assign_event($fleet, 1, $phalanx);
    }

    if($fleet['fleet_end_time'] > $time_now && $fleet['fleet_owner'] == $user['id'] &&
      !($fleet['fleet_mess'] == 0 &&
        ($fleet['fleet_mission'] == MT_RELOCATE || $fleet['fleet_mission'] == MT_COLONIZE)))
    {
      int_assign_event($fleet, 2, $phalanx);
    }
  }
}

function int_get_missile_to_planet($query, $phalanx = false)
{
  global $time_now;

  if(is_string($query))
  {
    $flying_fleets_mysql = doquery($query);

    $fleet_list = array();
    while ($fleet = mysql_fetch_assoc($flying_fleets_mysql))
    {
      $fleet_list[] = $fleet;
    }
  }
/*
  $iraks_query = doquery($query);
  while ($irak = mysql_fetch_assoc ($iraks_query))
*/
  foreach($fleet_list as $irak)
  {
    if($irak['zeit'] >= $time_now)
    {
      $irak['fleet_id']             = -$irak['anzahl'];
      $irak['fleet_owner']          = $irak['owner'];
      $irak['fleet_mission']        = MT_MISSILE;
      $irak['fleet_array']          = "503,{$irak['anzahl']};";
      $irak['fleet_amount']         = $irak['anzahl'];

      $planet_end = doquery("SELECT `name` FROM `{{planets}}` WHERE
        `galaxy` = '{$irak['galaxy']}' AND
        `system` = '{$irak['system']}' AND
        `planet` = '{$irak['planet']}' AND
        `planet_type` = '1'", '', true);
      $irak['fleet_end_galaxy']     = $irak['galaxy'];
      $irak['fleet_end_system']     = $irak['system'];
      $irak['fleet_end_planet']     = $irak['planet'];
      $irak['fleet_end_type']       = 1;
      $irak['fleet_end_time']       = $irak['zeit'];
      $irak['fleet_end_name']       = $planet_end['name'];

      $planet_start = doquery("SELECT `name` FROM `{{planets}}` WHERE
        `galaxy` = '{$irak['galaxy_angreifer']}' AND
        `system` = '{$irak['system_angreifer']}' AND
        `planet` = '{$irak['planet_angreifer']}' AND
        `planet_type` = '1'", '', true);
      $irak['fleet_start_galaxy']   = $irak['galaxy_angreifer'];
      $irak['fleet_start_system']   = $irak['system_angreifer'];
      $irak['fleet_start_planet']   = $irak['planet_angreifer'];
      $irak['fleet_start_type']     = 1;
      $irak['fleet_start_name']     = $planet_start['name'];
      //$irak['fleet_start_time']   = $irak['zeit'];

      int_assign_event($irak, 3, $phalanx);
    }
  }
}

function int_assign_event($fleet, $ov_label, $phalanx = false)
{
  global $user, $planetrow, $fleets, $fleet_number, $planet_end_type;

  switch($fleet['ov_label'] = $ov_label)
  {
    case 0:
      $fleet['ov_time'] = $fleet['fleet_start_time'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_end_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_end_system']) AND
        ($planetrow['planet'] == $fleet['fleet_end_planet']) AND
        ($planetrow['planet_type'] == $planet_end_type));
    break;

    case 1:
      $fleet['ov_time'] = $fleet['fleet_end_stay'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_end_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_end_system']) AND
        ($planetrow['planet'] == $fleet['fleet_end_planet']) AND
        ($planetrow['planet_type'] == $planet_end_type));
    break;

    case 2:
    case 3:
      $fleet['ov_time'] = $fleet['fleet_end_time'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_start_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_start_system']) AND
        ($planetrow['planet'] == $fleet['fleet_start_planet']) AND
        ($planetrow['planet_type'] == $fleet['fleet_start_type']));
    break;

  }

  $fleet['ov_this_planet'] = $is_this_planet || $phalanx;

  if($fleet['fleet_owner'] == $user['id'])
  {
    $user_data = $user;
  }
  else
  {
    $user_data = doquery("SELECT * FROM `{{users}}` WHERE `id` = {$fleet['fleet_owner']};", '', true);
  };

  $fleets[] = tpl_parse_fleet_db($fleet, ++$fleet_number, $user_data);
}

function int_planet_pretemplate($planetrow, &$template)
{
  global $lang, $sn_data;

  $governor_id = $planetrow['governor'];

  $template->assign_vars(array(
    'PLANET_ID'          => $planetrow['id'],
    'PLANET_NAME'        => $planetrow['name'],
    'PLANET_GALAXY'      => $planetrow['galaxy'],
    'PLANET_SYSTEM'      => $planetrow['system'],
    'PLANET_PLANET'      => $planetrow['planet'],
    'PLANET_TYPE'        => $planetrow['planet_type'],
    'PLANET_TYPE_TEXT'   => $lang['sys_planet_type'][$planetrow['planet_type']],

    'GOVERNOR_ID'        => $governor_id,
    'GOVERNOR_NAME'      => $lang['tech'][$governor_id],
    'GOVERNOR_LEVEL'     => $planetrow['governor_level'],
    'GOVERNOR_LEVEL_MAX' => $sn_data[$governor_id]['max'],
  ));
}

?>
