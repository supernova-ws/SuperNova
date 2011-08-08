<?php
/*
$user - actual user record
$from - actual planet record
$to - actual planet record
$fleet - array of records $unit_id -> $amount
$mission - fleet mission
*/

function flt_t_send_fleet($user, &$from, $to, $fleet, $mission, $options = array())
{
//ini_set('error_reporting', E_ALL);

  //doquery('SET autocommit = 0;');
  //doquery('LOCK TABLES {{users}} READ, {{planets}} WRITE, {{fleet}} WRITE, {{aks}} WRITE, {{statpoints}} READ;');
  doquery('START TRANSACTION;');

  $from = sys_o_get_updated($user, $from['id'], $GLOBALS['time_now']);
  $from = $from['planet'];

  $can_attack = flt_can_attack($from, $to, $fleet, $mission, $options);
  if($can_attack != ATTACK_ALLOWED)
  {
    doquery('ROLLBACK');
    return $can_attack;
  }

  global $time_now, $sn_data;

  $fleet_group = isset($options['fleet_group']) ? intval($options['fleet_group']) : 0;

  $travel_data  = flt_travel_data($user, $from, $to, $fleet, $options['fleet_speed_percent']);

  $fleet_start_time = $time_now + $travel_data['duration'];

  if ($mission == MT_EXPLORE OR $mission == MT_HOLD)
  {
    $stay_duration = $options['stay_time'] * 3600;
    $stay_time     = $fleet_start_time + $stay_duration;
  }
  else
  {
    $stay_duration = 0;
    $stay_time     = 0;
  }
  $fleet_end_time = $fleet_start_time + $travel_data['duration'] + $stay_duration;

  $fleet_ship_count  = 0;
  $fleet_string      = '';
  $planet_sub_query  = '';
  foreach ($fleet as $unit_id => $amount)
  {
    if(!$amount || !$unit_id)
    {
      continue;
    }

    if(in_array($unit_id, $sn_data['groups']['fleet']))
    {
      $fleet_ship_count += $amount;
      $fleet_string     .= "{$unit_id},{$amount};";
    }
    $planet_sub_query .= "`{$sn_data[$unit_id]['name']}` = `{$sn_data[$unit_id]['name']}` - {$amount},";
  }

  $to['id_owner'] = intval($to['id_owner']);

  $QryInsertFleet  = "INSERT INTO {{fleets}} SET ";
  $QryInsertFleet .= "`fleet_owner` = '{$user['id']}', ";
  $QryInsertFleet .= "`fleet_mission` = '{$mission}', ";
  $QryInsertFleet .= "`fleet_amount` = '{$fleet_ship_count}', ";
  $QryInsertFleet .= "`fleet_array` = '{$fleet_string}', ";
  $QryInsertFleet .= "`fleet_start_time` = '{$fleet_start_time}', ";
  $QryInsertFleet .= "`fleet_start_galaxy` = '{$from['galaxy']}', ";
  $QryInsertFleet .= "`fleet_start_system` = '{$from['system']}', ";
  $QryInsertFleet .= "`fleet_start_planet` = '{$from['planet']}', ";
  $QryInsertFleet .= "`fleet_start_type` = '{$from['planet_type']}', ";
  $QryInsertFleet .= "`fleet_end_time` = '{$fleet_end_time}', ";
  $QryInsertFleet .= "`fleet_end_stay` = '{$stay_time}', ";
  $QryInsertFleet .= "`fleet_end_galaxy` = '{$to['galaxy']}', ";
  $QryInsertFleet .= "`fleet_end_system` = '{$to['system']}', ";
  $QryInsertFleet .= "`fleet_end_planet` = '{$to['planet']}', ";
  $QryInsertFleet .= "`fleet_end_type` = '{$to['planet_type']}', ";
  $QryInsertFleet .= "`fleet_resource_metal` = '{$fleet[RES_METAL]}', ";
  $QryInsertFleet .= "`fleet_resource_crystal` = '{$fleet[RES_CRYSTAL]}', ";
  $QryInsertFleet .= "`fleet_resource_deuterium` = '{$fleet[RES_DEUTERIUM]}', ";
  $QryInsertFleet .= "`fleet_target_owner` = '{$to['id_owner']}', ";
  $QryInsertFleet .= "`fleet_group` = '{$fleet_group}', ";
  $QryInsertFleet .= "`start_time` = '{$time_now}';";
  doquery( $QryInsertFleet);

  $QryUpdatePlanet  = "UPDATE {{planets}} SET {$planet_sub_query} `deuterium` = `deuterium` - '{$travel_data['consumption']}' WHERE `id` = '{$from['id']}' LIMIT 1;";
  doquery ($QryUpdatePlanet);

  if(BE_DEBUG)
  {
    debug($QryInsertFleet);
    debug($QryUpdatePlanet);
  }

  doquery("COMMIT;");
  // doquery('SET autocommit = 1;');
  $from = doquery ("SELECT * FROM {{planets}} WHERE `id` = '{$from['id']}' LIMIT 1;", '', true);

  return ATTACK_ALLOWED;
//ini_set('error_reporting', E_ALL ^ E_NOTICE);
}

function flt_expand($target)
{
  $arr_fleet = array();
  if ($target['fleet_array']) // it's a fleet!
  {
    $arr_fleet_lines = explode(';', $target['fleet_array']);
    foreach ($arr_fleet_lines as $str_fleet_line)
    {
      if ($str_fleet_line)
      {
        $arr_ship_data = explode(',', $str_fleet_line);
        $arr_fleet[$arr_ship_data[0]] = $arr_ship_data[1];
      }
    }
    $arr_fleet[RES_METAL] = $target['fleet_resource_metal'];
    $arr_fleet[RES_CRYSTAL] = $target['fleet_resource_crystal'];
    $arr_fleet[RES_DEUTERIUM] = $target['fleet_resource_deuterium'];
  }
  elseif ($target['field_max']) // it's a planet!
  {

  }

  return $arr_fleet;
}

?>
