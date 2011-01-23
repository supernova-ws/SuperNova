<?php

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

  COE_missileCalculate();

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
