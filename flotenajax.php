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

header("Content-type: text/html; charset=windows-1251");
define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

includeLang('universe');
includeLang('fleet');

doquery("START TRANSACTION;");
$planetrow = doquery("SELECT * FROM `{{planets}}` WHERE `id` = '{$user['current_planet']}' LIMIT 1 FOR UPDATE;", '',true);

$CurrentFlyingFleets = doquery("SELECT COUNT(fleet_id) AS `flying_fleets` FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}';", '', true);
$CurrentFlyingFleets = $CurrentFlyingFleets["flying_fleets"];
$UserSpyProbes  = $planetrow['spy_sonde'];
$UserRecycles   = $planetrow['recycler'];
$UserDeuterium  = $planetrow['deuterium'];
$UserMissiles   = $planetrow['interplanetary_misil'];

$target_galaxy = intval($_POST['galaxy']);
$target_system = intval($_POST['system']);
$target_planet = intval($_POST['planet']);
if($target_galaxy > $config->game_maxGalaxy || $target_galaxy < 1 ||
   $target_system > $config->game_maxSystem || $target_system < 1 ||
   $target_planet > $config->game_maxPlanet || $target_planet < 1)
{
  $ResultMessage = "02|{$lang['gs_c02']}|{$CurrentFlyingFleets}|{$UserSpyProbes}|{$UserRecycles}|{$UserMissiles}";
  die ( $ResultMessage );
}
$target_planet_type = intval($_POST['planettype']);

$target_mission = intval($_POST['mission']);

$fleet_array    = array();
$FleetDBArray   = '';
$FleetSubQRY    = '';
$fleet_ship_count = 0;
foreach (array_merge($sn_groups['fleet'], array(503)) as $ship_id)
{
  $ship_count = intval($_POST["ship{$ship_id}"]);
  if(!$ship_count)
  {
    continue;
  }
  if ($ship_count > $planetrow[$sn_data[$ship_id]['name']] && $target_mission != MT_MISSILE)
  {
    $ship_count = $planetrow[$sn_data[$ship_id]['name']];
  }
  $fleet_array[$ship_id]  = $ship_count;
  $fleet_ship_count        += $ship_count;
  $FleetDBArray          .= "{$ship_id},{$ship_count};";
  $FleetSubQRY           .= "`{$sn_data[$ship_id]['name']}` = `{$sn_data[$ship_id]['name']}` - {$ship_count}, ";
}
$target_planet_check = $target_planet_type == PT_DEBRIS ? PT_PLANET : $target_planet_type;
$TargetRow = doquery( "SELECT * FROM {{planets}} WHERE `galaxy` = '{$target_galaxy}' AND `system` = '{$target_system}' AND `planet` = '{$target_planet}' AND `planet_type` = '{$target_planet_check}' LIMIT 1;", '', true);

$cant_attack = flt_can_attack($TargetRow, $target_mission, $fleet_array);
if($cant_attack)
{
  die("{$cant_attack}|{$lang['fl_attack_error'][$cant_attack]}|{$CurrentFlyingFleets}|{$UserSpyProbes}|{$UserRecycles}|{$UserMissiles}");
}

$consumption = 0;
if($target_mission == MT_MISSILE)
{
  $target_structure = intval($_POST['structures']);
  if ($target_structure && !in_array($target_structure, $sn_groups['defense_active']))
  {
    $cant_attack = ATTACK_WRONG_STRUCTURE;
    die("{$cant_attack}|{$lang['fl_attack_error'][$cant_attack]}|{$CurrentFlyingFleets}|{$UserSpyProbes}|{$UserRecycles}|{$UserMissiles}");
  };

  $mips_sent = $fleet_array[503];

  $distance = abs($target_system - $planetrow['system']);
  $mipRange = ($user['impulse_motor_tech'] * 5) - 1;

  $arrival = $time_now + round((30 + (60 * $distance)) / get_fleet_speed());

  doquery("INSERT INTO `{{iraks}}` SET
     `zielid` = '{$TargetRow['id_owner']}', `galaxy` = '{$target_galaxy}', `system` = '{$target_system}', `planet` = '{$target_planet}',
     `owner` = '{$user['id']}', `galaxy_angreifer` = '{$planetrow['galaxy']}', `system_angreifer` = '{$planetrow['system']}', `planet_angreifer` = '{$planetrow['planet']}',
     `zeit` = '{$arrival}', `anzahl` = '{$mips_sent}', `primaer` = '{$target_structure}';");

  $FleetSubQRY = "`{$sn_data[503]['name']}` = `{$sn_data[503]['name']}` - '{$mips_sent}', ";
  $Ship = 503;
  //doquery("UPDATE `{{planets}}` SET  WHERE `id` = '{$user['current_planet']}' LIMIT 1;");
}
else
{
  $Distance    = GetTargetDistance ($planetrow['galaxy'], $target_galaxy, $planetrow['system'], $target_system, $planetrow['planet'], $target_planet);
  $speedall    = GetFleetMaxSpeed ($fleet_array, 0, $user);
  $SpeedAllMin = min($speedall);
  $Duration    = GetMissionDuration ( 10, $SpeedAllMin, $Distance, get_fleet_speed());

  $fleet_start_time = $Duration + time();
  $fleet_end_time   = ($Duration * 2) + time();

  $consumption         = 0;
  $SpeedFactor         = get_fleet_speed();
  foreach ($fleet_array as $Ship => $Count)
  {
    $ShipSpeed        = $pricelist[$Ship]["speed"];
    $spd              = 35000 / ($Duration * $SpeedFactor - 10) * sqrt($Distance * 10 / $ShipSpeed);
    $basicConsumption = $pricelist[$Ship]["consumption"] * $Count ;
    $consumption     += $basicConsumption * $Distance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
  }
  $consumption = round($consumption) + 1;

  if($UserDeuterium<$consumption)
  {
    $ResultMessage = "13|{$lang['gs_c13']}|{$CurrentFlyingFleets}|{$UserSpyProbes}|{$UserRecycles}|{$UserMissiles}";
    die ( $ResultMessage );
  }

  $QryInsertFleet  = "INSERT INTO {{fleets}} SET ";
  $QryInsertFleet .= "`fleet_owner` = '{$user['id']}', ";
  $QryInsertFleet .= "`fleet_mission` = '{$target_mission}', ";
  $QryInsertFleet .= "`fleet_amount` = '{$fleet_ship_count}', ";
  $QryInsertFleet .= "`fleet_array` = '{$FleetDBArray}', ";
  $QryInsertFleet .= "`fleet_start_time` = '{$fleet_start_time}', ";
  $QryInsertFleet .= "`fleet_start_galaxy` = '{$planetrow['galaxy']}', ";
  $QryInsertFleet .= "`fleet_start_system` = '{$planetrow['system']}', ";
  $QryInsertFleet .= "`fleet_start_planet` = '{$planetrow['planet']}', ";
  $QryInsertFleet .= "`fleet_start_type`   = '{$planetrow['planet_type']}', ";
  $QryInsertFleet .= "`fleet_end_time` = '{$fleet_end_time}', ";
  $QryInsertFleet .= "`fleet_end_galaxy` = '{$target_galaxy}', ";
  $QryInsertFleet .= "`fleet_end_system` = '{$target_system}', ";
  $QryInsertFleet .= "`fleet_end_planet` = '{$target_planet}', ";
  $QryInsertFleet .= "`fleet_end_type` = '{$target_planet_type}', ";
  $QryInsertFleet .= "`fleet_target_owner` = '{$TargetRow['id_owner']}', ";
  $QryInsertFleet .= "`start_time` = '{$time_now}';";
  doquery( $QryInsertFleet);
}
doquery( "UPDATE {{planets}} SET {$FleetSubQRY} `{$sn_data[903]['name']}` = `{$sn_data[903]['name']}` - {$consumption} WHERE `id` = '{$planetrow['id']}' LIMIT 1;");
doquery("COMMIT;");

$CurrentFlyingFleets++;
$UserSpyProbes -= $fleet_array[210];
$UserRecycles  -= $fleet_array[209];
$UserMissiles  -= $fleet_array[503];

$ResultMessage  = "{$cant_attack}|{$lang['gs_sending']} {$fleet_ship_count} {$lang['tech'][$Ship]} {$lang['gs_to']} {$target_galaxy}:{$target_system}:{$target_planet}...|";
$ResultMessage .= "{$CurrentFlyingFleets}|{$UserSpyProbes}|{$UserRecycles}|{$UserMissiles}";

die ( $ResultMessage );

?>
