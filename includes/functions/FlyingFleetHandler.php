<?php

/**
 * @function RestoreFleetToPlanet
 *
 * @version 1.0
 * @copyright 2008 Chlorel for XNova
 */

// RestoreFleetToPlanet
//
// $FleetRow -> enregistrement de flotte
// $Start    -> true  = planete de depart
//           -> false = planete d'arrivée
function RestoreFleetToPlanet ( &$fleet_row, $start = true )
{
  if(!is_array($fleet_row))
  {
    return false;
  }

  global $sn_data;

  $prefix = $start ? 'start' : 'end';

  $query = 'UPDATE {{planets}} SET ';

  $fleet_strings = explode(';', $fleet_row['fleet_array']);
  foreach ($fleet_strings as $ship_string)
  {
    if ($ship_string != '')
    {
      $ship_record = explode (',', $ship_string);
      $ship_db_name = $sn_data[$ship_record[0]]['name'];
      $query .= "`{$ship_db_name}` = `{$ship_db_name}` + '{$ship_record[1]}', ";
    }
  }

  $query .= "`metal` = `metal` + '{$fleet_row['fleet_resource_metal']}', ";
  $query .= "`crystal` = `crystal` + '{$fleet_row['fleet_resource_crystal']}', ";
  $query .= "`deuterium` = `deuterium` + '{$fleet_row['fleet_resource_deuterium']}' ";
  $query .= "WHERE ";
  $query .= "`galaxy` = '". $fleet_row["fleet_{$prefix}_galaxy"] ."' AND ";
  $query .= "`system` = '". $fleet_row["fleet_{$prefix}_system"] ."' AND ";
  $query .= "`planet` = '". $fleet_row["fleet_{$prefix}_planet"] ."' AND ";
  $query .= "`planet_type` = '". $fleet_row["fleet_{$prefix}_type"] ."' ";
  $query .= "LIMIT 1;";

  doquery($query);

  doquery("DELETE FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1;");
}

// Modified by MadnessRed to support ACS

/**
 * FlyingFleetHandler.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function flt_flyingFleetsSort($a, $b)
{
  return $a['fleet_time'] == $b['fleet_time'] ? 0 : ($a['fleet_time'] > $b['fleet_time'] ? 1 : -1);
}

function FlyingFleetHandler ()
{
  global $resource, $dbg_msg, $time_now, $config, $doNotUpdateFleet;

  if(($time_now - $config->flt_lastUpdate <= 8 ) || ($doNotUpdateFleet)) return;
  $config->db_saveItem('flt_lastUpdate', $time_now);

  doquery('LOCK TABLE {{table}}aks WRITE, {{table}}rw WRITE, {{table}}errors WRITE, {{table}}messages WRITE, {{table}}fleets WRITE, {{table}}planets WRITE, {{table}}users WRITE, {{table}}logs WRITE, {{table}}iraks WRITE, {{table}}statpoints WRITE, {{table}}referrals WRITE, {{table}}counter WRITE');

  // start transaction
  coe_o_missile_calculate();
  // commit

  $fleets = array();
  $_fleets = doquery("SELECT fleet_id, fleet_start_time AS fleet_time FROM {{fleets}} WHERE `fleet_start_time` <= '{$time_now}';");
  while ($row = mysql_fetch_array($_fleets)) $fleets[] = $row;

  $_fleets = doquery("SELECT fleet_id, fleet_end_time AS fleet_time FROM {{fleets}} WHERE `fleet_end_time` <= '{$time_now}';");
  while ($row = mysql_fetch_array($_fleets)) $fleets[] = $row;

  uasort($fleets, 'flt_flyingFleetsSort');
  unset($_fleets);

  foreach($fleets as $fleet){
    $CurrentFleet = doquery( "SELECT * FROM {{fleets}} WHERE fleet_id={$fleet['fleet_id']}", '', true );
    if($CurrentFleet)
      switch ($CurrentFleet["fleet_mission"]) {
        case MT_ATTACK:    MissionCaseAttack ( $CurrentFleet ); break;
        case MT_AKS:       MissionCaseAttack ( $CurrentFleet ); break;
        case MT_TRANSPORT: MissionCaseTransport ( $CurrentFleet ); break;
        case MT_RELOCATE:  MissionCaseStay ( $CurrentFleet ); break;
        case MT_HOLD:      MissionCaseACS ( $CurrentFleet ); break;
        case MT_SPY:       MissionCaseSpy ( $CurrentFleet ); break;
        case MT_COLONIZE:  MissionCaseColonisation ( $CurrentFleet ); break;
        case MT_RECYCLE:   MissionCaseRecycling ( $CurrentFleet ); break;
        case MT_DESTROY:   MissionCaseDestruction ( $CurrentFleet ); break;
        case MT_MISSILE:   break; // Missiles !!
        case MT_EXPLORE:   MissionCaseExpedition ( $CurrentFleet ); break;

        default: doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$CurrentFleet['fleet_id']}';");
      }
  }

  doquery("DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT fleet_group FROM {{fleets}});");
  doquery("UNLOCK TABLES");
}

?>
