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

use DBAL\OldDbChangeSet;
use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

define('IN_AJAX', true);

setHeader("Content-type: text/html; charset=utf-8");
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

sn_db_transaction_start();

$user = db_user_by_id($user['id'], true);
$planetrow = DBStaticPlanet::db_planet_by_id($user['current_planet'], true);

$target_planet_type = sys_get_param_int('planet_type');
$target_planet_check = $target_planet_type == PT_DEBRIS ? PT_PLANET : $target_planet_type;

$target_coord['planet_type'] = $target_planet_check;
$target_row = DBStaticPlanet::db_planet_by_vector($target_coord);

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
    // $fleet_array[SHIP_SPY] = min(mrc_get_level($user, $planetrow, SHIP_SPY), abs($user['spio_anz']));
    $fleet_array[SHIP_SPY] = min(mrc_get_level($user, $planetrow, SHIP_SPY), abs(SN::$user_options[PLAYER_OPTION_FLEET_SPY_DEFAULT]));
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

$options = [P_FLEET_ATTACK_TARGET_STRUCTURE => $target_structure = sys_get_param_int('structures')];
$cant_attack = flt_can_attack($planetrow, $target_row, $fleet_array, $target_mission, $options);


if($cant_attack != ATTACK_ALLOWED)
{
  die($lang['fl_attack_error'][$cant_attack]);
}

$FleetDBArray   = array();
$db_changeset = array();
foreach($fleet_array as $unit_id => $unit_count)
{
  $FleetDBArray[] = "{$unit_id},{$unit_count}";
  $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($unit_id, -$unit_count, $user, $planetrow);
}
$FleetDBArray = implode(';', $FleetDBArray);

$fleet_ship_count = array_sum($fleet_array);

if($target_mission == MT_MISSILE)
{
  $distance = abs($target_coord['system'] - $planetrow['system']);
  $duration = round((30 + (60 * $distance)) / flt_server_flight_speed_multiplier());
  $arrival = SN_TIME_NOW + $duration;
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

  $fleet_start_time = SN_TIME_NOW + $travel_data['duration'];
  $fleet_end_time   = $fleet_start_time + $travel_data['duration'];

  $fleet_set = [
    'fleet_owner' => $user['id'],
    'fleet_mission' => $target_mission,
    'fleet_amount' => $fleet_ship_count,
    'fleet_array' => $FleetDBArray,
    'fleet_start_time' => $fleet_start_time,
    'fleet_start_planet_id' => !empty($planetrow['id']) ? $planetrow['id'] : null,
    'fleet_start_galaxy' => $planetrow['galaxy'],
    'fleet_start_system' => $planetrow['system'],
    'fleet_start_planet' => $planetrow['planet'],
    'fleet_start_type'   => $planetrow['planet_type'],
    'fleet_end_time' => $fleet_end_time,
    'fleet_end_planet_id' => !empty($target_row['id']) ? $target_row['id'] : null,
    'fleet_end_galaxy' => $target_coord['galaxy'],
    'fleet_end_system' => $target_coord['system'],
    'fleet_end_planet' => $target_coord['planet'],
    'fleet_end_type' => $target_planet_type,
    'fleet_target_owner' => $target_row['id_owner'],
    'start_time' => SN_TIME_NOW,
  ];
  DbFleetStatic::fleet_insert_set_dbq($fleet_set);
}

DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`deuterium` = `deuterium` - {$travel_data['consumption']}");
OldDbChangeSet::db_changeset_apply($db_changeset);
sn_db_transaction_commit();

$ships_sent = array();
//$ships_sent_js = array();
$ships_sent_js = 0;
foreach($fleet_array as $unit_id => $unit_count)
{
  $ships_sent[] = "{$unit_count} {$lang['tech'][$unit_id]}";
  $ships_sent_js += mrc_get_level($user, $planetrow, $unit_id, false, true);
}
$ships_sent = implode(', ', $ships_sent);
//$ships_sent_js = implode(',', $ships_sent_js);
$ships_sent_js = "{$unit_group}={$ships_sent_js}";

$ResultMessage  = "{$lang['gs_sending']} {$ships_sent} {$lang['gs_to']} {$target_coord['galaxy']}:{$target_coord['system']}:{$target_coord['planet']}|{$ships_sent_js}";

die($ResultMessage);
