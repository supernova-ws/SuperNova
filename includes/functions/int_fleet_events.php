<?php

/*
 * Get fleets by condition (including missiles)
 * $condition =
 *   false - get ALL fleets (for FlyingFleetsManagerV2); $phalanx ignored
 *   user ID - get all fleets from current owner
 *   array - (presumed planet row) get all fleets coming to selected planet
 */
function flt_get_fleets($condition, $phalanx = false)
{
  global $time_now;
  
  $fleet_db_list = array();

  if(!$condition)
  {
    $missile_query = $condition = 1;
  }
  elseif(is_array($condition))
  {
    $missile_query = "
      (fleet_start_galaxy = {$condition['galaxy']} AND fleet_start_system = {$condition['system']} AND fleet_start_planet = {$condition['planet']} AND fleet_start_type = {$condition['planet_type']})
      OR
      (fleet_end_galaxy = {$condition['galaxy']} AND fleet_end_system = {$condition['system']} AND fleet_end_planet = {$condition['planet']} AND fleet_end_type = {$condition['planet_type']})";
    $condition = "
      (fleet_start_galaxy = {$condition['galaxy']} AND fleet_start_system = {$condition['system']} AND fleet_start_planet = {$condition['planet']} AND fleet_start_type = {$condition['planet_type']}" . ($phalanx ? '' : ' AND fleet_mess = 1') . ")
      OR
      (fleet_end_galaxy = {$condition['galaxy']} AND fleet_end_system = {$condition['system']} AND fleet_end_planet = {$condition['planet']} AND fleet_end_type = {$condition['planet_type']}" . ($phalanx ? '' : ' AND fleet_mess = 0') . ")";
  }
  else
  {
    $missile_query = "`fleet_owner` = '{$condition}' OR `fleet_target_owner` = '{$condition}'";
    $condition = $missile_query;
  }
  $sql_fleets = doquery("SELECT DISTINCT * FROM {{fleets}} WHERE {$condition};");

  while ($fleet = mysql_fetch_assoc($sql_fleets))
  {
    $fleet_db_list[] = $fleet;
  }

  // Missile attack
  $sql_fleets = doquery("SELECT * FROM `{{iraks}}` WHERE {$missile_query};");
  while ($irak = mysql_fetch_assoc($sql_fleets))
  {
    if($irak['fleet_end_time'] >= $time_now)
    {
      $planet_start = doquery("SELECT `name` FROM `{{planets}}` WHERE `galaxy` = '{$irak['fleet_start_galaxy']}' AND `system` = '{$irak['fleet_start_system']}' AND `planet` = '{$irak['fleet_start_planet']}' AND `planet_type` = '1'", true);
      $irak['fleet_id']             = -$irak['id'];
      $irak['fleet_mission']        = MT_MISSILE;
      $irak['fleet_array']          = UNIT_DEF_MISSILE_INTERPLANET . ",{$irak['fleet_amount']};";
//        $irak['fleet_end_type']       = PT_PLANET;
//        $irak['fleet_start_type']     = PT_PLANET;
      $irak['fleet_start_name']     = $planet_start['name'];
    }
    $fleet_db_list[] = $irak;
  }

  return $fleet_db_list;
}

function flt_parse_fleets_to_events($fleet_list, $planet_scanned = false)
{
  global $config, $user, $fleet_number, $lang, $time_now;

  $fleet_events = array();
  $fleet_number = 0;

  if(empty($fleet_list))
  {
    return;
  }

  foreach($fleet_list as $fleet)
  {
    $planet_start_type = $fleet['fleet_start_type'] == PT_MOON ? PT_MOON : PT_PLANET;
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
      $planet_end_type = $fleet['fleet_end_type'] == PT_MOON ? PT_MOON : PT_PLANET;

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

    if($fleet['fleet_start_time'] > $time_now && $fleet['fleet_mess'] == 0 && $fleet['fleet_mission'] != MT_MISSILE &&
      ($planet_scanned === false
        ||
        (
          $planet_scanned !== false
          && $planet_scanned['galaxy'] == $fleet['fleet_end_galaxy'] && $planet_scanned['system'] == $fleet['fleet_end_system'] && $planet_scanned['planet'] == $fleet['fleet_end_planet'] && $planet_scanned['planet_type'] == $planet_end_type
          && $planet_start_type != PT_MOON
          && $fleet['fleet_mission'] != MT_HOLD
        )
      )
    )
    {
      $fleet_events[] = flt_register_fleet_event($fleet, 0, $planet_end_type);
    }

    if($fleet['fleet_end_stay'] > $time_now && $fleet['fleet_mess'] == 0 && $planet_scanned === false && $fleet['fleet_mission'] != MT_MISSILE)
    {
      $fleet_events[] = flt_register_fleet_event($fleet, 1, $planet_end_type);
    }

    if(
      $fleet['fleet_end_time'] > $time_now && $fleet['fleet_mission'] != MT_MISSILE && ($fleet['fleet_mess'] == 1 || ($fleet['fleet_mission'] != MT_RELOCATE && $fleet['fleet_mission'] != MT_COLONIZE)) && 
      (
        ($planet_scanned === false && $fleet['fleet_owner'] == $user['id'])
        ||
        (
          $planet_scanned !== false
          && $fleet['fleet_mission'] != MT_RELOCATE
          && $planet_start_type != PT_MOON
          && $planet_scanned['galaxy'] == $fleet['fleet_start_galaxy'] && $planet_scanned['system'] == $fleet['fleet_start_system'] && $planet_scanned['planet'] == $fleet['fleet_start_planet'] && $planet_scanned['planet_type'] == $planet_start_type
        )
      )
    )
    {
      $fleet_events[] = flt_register_fleet_event($fleet, 2, $planet_end_type);
    }
    
    if($fleet['fleet_mission'] == MT_MISSILE)
    {
      $fleet_events[] = flt_register_fleet_event($fleet, 3, $planet_end_type);
    }
  }
  
  return $fleet_events;
}

function flt_register_fleet_event($fleet, $ov_label, $planet_end_type)
{
  global $user, $planetrow, $fleet_number;

  switch($fleet['ov_label'] = $ov_label)
  {
    case 0:
      $fleet['event_time'] = $fleet['fleet_start_time'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_end_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_end_system']) AND
        ($planetrow['planet'] == $fleet['fleet_end_planet']) AND
        ($planetrow['planet_type'] == $planet_end_type));
    break;

    case 1:
      $fleet['event_time'] = $fleet['fleet_end_stay'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_end_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_end_system']) AND
        ($planetrow['planet'] == $fleet['fleet_end_planet']) AND
        ($planetrow['planet_type'] == $planet_end_type));
    break;

    case 2:
    case 3:
      $fleet['event_time'] = $fleet['fleet_end_time'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_start_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_start_system']) AND
        ($planetrow['planet'] == $fleet['fleet_start_planet']) AND
        ($planetrow['planet_type'] == $fleet['fleet_start_type']));
    break;

  }

  $fleet['ov_this_planet'] = $is_this_planet;// || $planet_scanned != false;

  if($fleet['fleet_owner'] == $user['id'])
  {
    $user_data = $user;
  }
  else
  {
    $user_data = doquery("SELECT * FROM `{{users}}` WHERE `id` = {$fleet['fleet_owner']};", '', true);
  };

  return tpl_parse_fleet_db($fleet, ++$fleet_number, $user_data);
}

function int_planet_pretemplate($planetrow, &$template)
{
  global $lang;

  $governor_id = $planetrow['PLANET_GOVERNOR_ID'];

  $template->assign_vars(array(
    'PLANET_ID'          => $planetrow['id'],
    'PLANET_NAME'        => $planetrow['name'],
    'PLANET_GALAXY'      => $planetrow['galaxy'],
    'PLANET_SYSTEM'      => $planetrow['system'],
    'PLANET_PLANET'      => $planetrow['planet'],
    'PLANET_TYPE'        => $planetrow['planet_type'],
    'PLANET_TYPE_TEXT'   => $lang['sys_planet_type'][$planetrow['planet_type']],

    'PLANET_GOVERNOR_ID'        => $governor_id,
    'PLANET_GOVERNOR_NAME'      => $lang['tech'][$governor_id],
    'PLANET_GOVERNOR_LEVEL'     => $planetrow['PLANET_GOVERNOR_LEVEL'],
    'PLANET_GOVERNOR_LEVEL_MAX' => get_unit_param($governor_id, P_MAX_STACK),
  ));
}

?>
