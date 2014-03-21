<?php

/**
 * flotenajax.php
 *
 * Fleet manager on Ajax (to work in Galaxy view)
 *
 * @version 2.0 Security checks by Gorlum for http://supernova.ws
 *  [!] Full rewrite
 *  [+] Added missile attack launch sequience
 *  [-] Moved almost all check code to flt_can_attack
 * @version 1.1 Security checks by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
**/

include('common.' . substr(strrchr(__FILE__, '.'), 1));
header("Content-type: text/html; charset=utf-8");
lng_include('universe');
lng_include('fleet');
require_once('includes/includes/flt_functions.php');

$target_coord = array(
  'galaxy' => $target_galaxy = sys_get_param_int('galaxy'),
  'system' => $target_system = sys_get_param_int('system'),
  'planet' => $target_planet = sys_get_param_int('planet'),
);

if(!uni_coordinates_valid($target_coord))
{
  die($lang['gs_c02']);
}

$target_mission = sys_get_param_int('mission');
$sn_group_missions = sn_get_groups('missions');
if(!isset($sn_group_missions[$target_mission]['AJAX']) || !$sn_group_missions[$target_mission]['AJAX'])
{
  die($lang['gs_c00']);
}

doquery("START TRANSACTION;");

$user = doquery ("SELECT * FROM {{users}} WHERE `id` = '{$user['id']}' LIMIT 1 FOR UPDATE;", true);
$planetrow = doquery("SELECT * FROM `{{planets}}` WHERE `id` = '{$user['current_planet']}' LIMIT 1 FOR UPDATE;", true);

$target_planet_type = sys_get_param_int('planet_type');
$target_planet_check = $target_planet_type == PT_DEBRIS ? PT_PLANET : $target_planet_type;
$target_row = doquery( "SELECT * FROM {{planets}} WHERE `galaxy` = '{$target_coord['galaxy']}' AND `system` = '{$target_coord['system']}' AND `planet` = '{$target_coord['planet']}' AND `planet_type` = '{$target_planet_check}' LIMIT 1;", true);
if(empty($target_row))
{
  $target_row = array(
    'galaxy' => $target_coord['galaxy'],
    'system' => $target_coord['system'],
    'planet' => $target_coord['planet'],
    'planet_type' => $target_planet_check,
    'id_owner' => 0
  );
}

$fleet_array = array();
switch($target_mission)
{
  case MT_SPY:
    $fleet_array[SHIP_SPY] = min(mrc_get_level($user, $planetrow, SHIP_SPY), abs($user['spio_anz']));
    $unit_group = 'flt_spies';
  break;

  case MT_RECYCLE:
    foreach(sn_get_groups('flt_recyclers') as $unit_id)
    {
      if($unit_count = mrc_get_level($user, $planetrow, $unit_id))
      {
        $fleet_array[$unit_id] = $unit_count;
      }
    }
    $transport_data = flt_calculate_fleet_to_transport($fleet_array, $target_row['debris_metal'] + $target_row['debris_crystal'], $planetrow, $target_row);
    $fleet_array = $transport_data['fleet'];
    $unit_group = 'flt_recyclers';
  break;

  case MT_MISSILE:
    $fleet_array[UNIT_DEF_MISSILE_INTERPLANET] = min(mrc_get_level($user, $planetrow, UNIT_DEF_MISSILE_INTERPLANET), abs(sys_get_param_float('missiles')));
    $unit_group = 'missile';
  break;

}

$options = array('target_structure' => $target_structure = sys_get_param_int('structures'));
$cant_attack = flt_can_attack($planetrow, $target_row, $fleet_array, $target_mission, $options);


if($cant_attack != ATTACK_ALLOWED)
{
  die($lang['fl_attack_error'][$cant_attack]);
}

$FleetDBArray   = array();
$FleetSubQRY    = array();
foreach($fleet_array as $unit_id => $unit_count)
{
  $FleetDBArray[] = "{$unit_id},{$unit_count}";
  $FleetSubQRY[]  = "`{$sn_data[$unit_id]['name']}` = `{$sn_data[$unit_id]['name']}` - {$unit_count}";
}
$FleetDBArray = implode(';', $FleetDBArray);
$FleetSubQRY  = implode(',', $FleetSubQRY);

$fleet_ship_count = array_sum($fleet_array);

if($target_mission == MT_MISSILE)
{
  $distance = abs($target_coord['system'] - $planetrow['system']);
  $duration = round((30 + (60 * $distance)) / flt_server_flight_speed_multiplier());
  $arrival = $time_now + $duration;
  $travel_data['consumption'] = 0;

  doquery(
    "INSERT INTO `{{iraks}}` SET
     `fleet_target_owner` = '{$target_row['id_owner']}', `fleet_end_galaxy` = '{$target_coord['galaxy']}', `fleet_end_system` = '{$target_coord['system']}', `fleet_end_planet` = '{$target_coord['planet']}',
     `fleet_owner` = '{$user['id']}', `fleet_start_galaxy` = '{$planetrow['galaxy']}', `fleet_start_system` = '{$planetrow['system']}', `fleet_start_planet` = '{$planetrow['planet']}',
     `fleet_end_time` = '{$arrival}', `fleet_amount` = '{$fleet_ship_count}', `primaer` = '{$target_structure}';"
  );
}
else
{
  $travel_data = flt_travel_data($user, $planetrow, $target_coord, $fleet_array, 10);

  if($planetrow['deuterium'] < $travel_data['consumption'])
  {
    die($lang['gs_c13']);
  }

  $fleet_start_time = $time_now + $travel_data['duration'];
  $fleet_end_time   = $fleet_start_time + $travel_data['duration'];

  $QryInsertFleet  = "INSERT INTO {{fleets}} SET ";
  $QryInsertFleet .= "`fleet_owner` = '{$user['id']}', ";
  $QryInsertFleet .= "`fleet_mission` = '{$target_mission}', ";
  $QryInsertFleet .= "`fleet_amount` = '{$fleet_ship_count}', ";
  $QryInsertFleet .= "`fleet_array` = '{$FleetDBArray}', ";
  $QryInsertFleet .= "`fleet_start_time` = '{$fleet_start_time}', ";
  if($planetrow['id'])
  {
    $QryInsertFleet .= "`fleet_start_planet_id` = '{$planetrow['id']}', ";
  }
  $QryInsertFleet .= "`fleet_start_galaxy` = '{$planetrow['galaxy']}', `fleet_start_system` = '{$planetrow['system']}', `fleet_start_planet` = '{$planetrow['planet']}', `fleet_start_type`   = '{$planetrow['planet_type']}', ";
  $QryInsertFleet .= "`fleet_end_time` = '{$fleet_end_time}', ";
  if($target_row['id'])
  {
    $QryInsertFleet .= "`fleet_end_planet_id` = '{$target_row['id']}', ";
  }
  $QryInsertFleet .= "`fleet_end_galaxy` = '{$target_coord['galaxy']}', `fleet_end_system` = '{$target_coord['system']}', `fleet_end_planet` = '{$target_coord['planet']}', `fleet_end_type` = '{$target_planet_type}', ";
  $QryInsertFleet .= "`fleet_target_owner` = '{$target_row['id_owner']}', ";
  $QryInsertFleet .= "`start_time` = '{$time_now}';";
  doquery($QryInsertFleet);
}

doquery("UPDATE {{planets}} SET `{$sn_data[RES_DEUTERIUM]['name']}` = `{$sn_data[RES_DEUTERIUM]['name']}` - {$travel_data['consumption']}, {$FleetSubQRY} WHERE `id` = '{$planetrow['id']}' LIMIT 1;");
doquery("COMMIT;");

$ships_sent = array();
//$ships_sent_js = array();
$ships_sent_js = 0;
foreach($fleet_array as $unit_id => $unit_count)
{
  $ships_sent[] = "{$unit_count} {$lang['tech'][$unit_id]}";
//  $ships_sent_js[] = "{$unit_id}=" . ($planetrow[$sn_data[$unit_id]['name']] - $unit_count);
  $ships_sent_js += $planetrow[$sn_data[$unit_id]['name']] - $unit_count;
}
$ships_sent = implode(', ', $ships_sent);
//$ships_sent_js = implode(',', $ships_sent_js);
$ships_sent_js = "{$unit_group}={$ships_sent_js}";

$ResultMessage  = "{$lang['gs_sending']} {$ships_sent} {$lang['gs_to']} {$target_coord['galaxy']}:{$target_coord['system']}:{$target_coord['planet']}|{$ships_sent_js}";

die($ResultMessage);

?>
