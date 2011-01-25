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
//           -> false = planete d'arriv√©e
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

function flt_cacher_user($flt_user_row, &$flt_user_cache)
{
  $flt_user_row_id = isset($flt_user_row['id']) ? $flt_user_row['id'] : 0;

  if(!isset($flt_user_cache[$flt_user_row_id]))
  {
    $flt_user_cache[$flt_user_row_id] = $flt_user_row;
  }

  return $flt_user_row_id;
}

function flt_cacher_planet($planet_vector, &$flt_user_cache, &$flt_planet_cache)
{
  $planet_hash = "g{$planet_vector['galaxy']}s{$planet_vector['system']}p{$planet_vector['planet']}t{$planet_vector['planet_type']}";
  if(!isset($flt_planet_cache[$planet_hash]))
  {
//!!!!!!!!!!!!! ќ“ Ћё„»“№ —»ћ”Ћя÷»ё !!!!!!!!!!!!!!!!!!!!!!!!!!
    $global_data = sys_o_get_updated(false, $planet_vector, $GLOBALS['time_now'], true);
    $flt_planet_cache[$planet_hash] = $global_data['planet'];

    flt_cacher_user($global_data['user'], &$flt_user_cache);
  }

  return array('planet_hash' => $planet_hash, 'user_id' => $flt_planet_cache[$target_planet_hash]['id_owner']);
}

function flt_cacher($fleet_row, &$flt_user_cache, &$flt_planet_cache, &$flt_fleet_cache, &$flt_event_list)
{
  $time_now = $GLOBALS['time_now'];

  // Dumping invalid fleet records
  // By design it should never triggered but let it be
  if(!$fleet_row || !is_array($fleet_row) || !$fleet_row['fleet_id'])
  {
    return;
  }

  // Checking if we should now to proceed this fleet - does it arrive?
  // By design it should never triggered but let it be
  if ($fleet_row['fleet_start_time'] > $time_now)
  {
    return;
  }

  // Checking fleet message: if not 0 then this fleet just should return to source planet
  if ($fleet_row['fleet_mess'] != 0)
  {
    // Checking fleet end_time: if less then time_now then restoring fleet to planet
    if($fleet_row['fleet_end_time'] <= $time_now)
    {
      RestoreFleetToPlanet($fleet_row);
    }
    return;
  }

  if(!isset($flt_fleet_cache[$fleet_row['fleet_id']]))
  {
    $flt_fleet_cache[$fleet_row['fleet_id']] = $fleet_row;
  }

  if($fleet_row['fleet_mission'] != MT_COLONIZE && $fleet_row['fleet_mission'] != MT_EXPLORE)
  {
    if($fleet_row['fleet_mission'] == MT_RECYCLE)
    {
      $fleet_end_type = PT_PLANET;
    }
    elseif($fleet_row['fleet_mission'] == MT_DESTROY)
    {
      $fleet_end_type = PT_MOON;
    }
    else
    {
      $fleet_end_type = $fleet_row['fleet_end_type'];
    }

    $destination = flt_cacher_planet(array('galaxy' => $fleet_row['fleet_end_galaxy'], 'system' => $fleet_row['fleet_end_system'], 'planet' => $fleet_row['fleet_end_planet'], 'planet_type' => $fleet_end_type), &$flt_user_cache, &$flt_planet_cache);
  }
  else
  {
    $destination = false;
  }

  $source = flt_cacher_planet(array('galaxy' => $fleet_row['fleet_start_galaxy'], 'system' => $fleet_row['fleet_start_system'], 'planet' => $fleet_row['fleet_start_planet'], 'planet_type' => $fleet_row['fleet_start_type']), &$flt_user_cache, &$flt_planet_cache);

  $flt_event_list[] = array(
    'fleet_id'        => $fleet_row['fleet_id'],
    'fleet_time'      => $fleet_row['fleet_time'],
    'src_planet_hash' => $source ? $source['planet_hash'] : 0,
    'src_user_id'     => $source ? $source['user_id'] : 0,
    'dst_planet_hash' => $destination ? $destination['planet_hash'] : 0,
    'dst_user_id'     => $destination ? $destination['user_id'] : 0
  );
}

function flt_t_flying_fleet_handler()
{
  // SAFE FALLBACK TO OLD-STYLE HANDLER
  define('FLT_FALLBACK', true);

  global $resource, $dbg_msg, $time_now, $config, $doNotUpdateFleet;

  if(FLT_FALLBACK)
  {
    if(($time_now - $config->flt_lastUpdate <= 8 ) || ($doNotUpdateFleet)) return;
    $config->db_saveItem('flt_lastUpdate', $time_now);

    doquery('LOCK TABLE {{table}}aks WRITE, {{table}}rw WRITE, {{table}}errors WRITE, {{table}}messages WRITE, {{table}}fleets WRITE, {{table}}planets WRITE, {{table}}users WRITE, {{table}}logs WRITE, {{table}}iraks WRITE, {{table}}statpoints WRITE, {{table}}referrals WRITE, {{table}}counter WRITE');
  }
  else
  {
    $time_now = 1295903553 + 130 * 60;
    pdump(date(FMT_DATE_TIME, 1295903553));
    doquery('START TRANSACTION;');
  }

  coe_o_missile_calculate();

  if(!FLT_FALLBACK)
  {
    doquery('COMMIT');
    doquery('START TRANSACTION;');
  }

  $flt_user_cache = array();
  $flt_planet_cache = array();
  $flt_fleet_cache = array();
  $flt_event_list = array();

  $_fleets = doquery("SELECT *, fleet_start_time AS fleet_time FROM `{{fleets}}` WHERE `fleet_start_time` <= '{$time_now}' FOR UPDATE;");
  while ($fleet_row = mysql_fetch_assoc($_fleets))
  {
    flt_cacher($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_list);
  }

  $_fleets = doquery("SELECT *, fleet_end_time AS fleet_time FROM `{{fleets}}` WHERE `fleet_end_time` <= '{$time_now}' FOR UPDATE;");
  while ($fleet_row = mysql_fetch_assoc($_fleets))
  {
    flt_cacher($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_list);
  }

  uasort($flt_event_list, 'flt_flyingFleetsSort');
  unset($_fleets);

if(!FLT_FALLBACK)
{
pdump(count($flt_user_cache), '$flt_user_row');
pdump(count($flt_planet_cache), '$flt_planet_row');
pdump(count($flt_fleet_cache), '$flt_fleet_cache');
pdump(count($flt_event_list), '$flt_event_list');

foreach($flt_event_list as $index => $data)
{
  pdump($flt_fleet_cache[$data['fleet_id']], "index {$index}, fleet_id {$data['fleet_id']}");
}
die();
}

  foreach($flt_event_list as $fleet_event)
  {
    if(FLT_FALLBACK)
    {
      $fleet_row = doquery( "SELECT * FROM {{fleets}} WHERE fleet_id={$fleet_event['fleet_id']}", '', true );
    }
    else
    {
/*
      // Ќадо предусмотреть случаи, когда мы два раза обрабатываем один и тот же флот - сначала с 0, затем с 1
      // Ёто может случитьс€ после долгого отключени€ сервера
      unset($target_planet_row);
      unset($target_user_row);
      $target_data = flt_cacher($fleet_row, $flt_user_cache, $flt_planet_cache);

      $target_planet_row = $flt_planet_cache[$target_data['destination']['planet_hash']];

      if (!$target_planet_row || !isset($target_planet_row['id']))
      {
        if ($fleet_row['fleet_mission'] == MT_AKS)
        {
          doquery("DELETE FROM {{aks}} WHERE `id` ='{$fleet_row['fleet_group']}' LIMIT 1;");
          // doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_group` = '{$fleet_row['fleet_group']}';");
        }
        doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
        continue;
      }
*/
    }

    switch ($fleet_row['fleet_mission'])
    {
      case MT_ATTACK:
        MissionCaseAttack ( $fleet_row );
      break;

      case MT_AKS:
        MissionCaseAttack ( $fleet_row );
      break;

      case MT_TRANSPORT:
        MissionCaseTransport ( $fleet_row );
      break;

      case MT_RELOCATE:
        MissionCaseStay ( $fleet_row );
      break;

      case MT_HOLD:
        MissionCaseACS ( $fleet_row );
      break;

      case MT_SPY:
        MissionCaseSpy ( $fleet_row );
      break;

      case MT_COLONIZE:
        MissionCaseColonisation ( $fleet_row );
      break;

      case MT_RECYCLE:
        MissionCaseRecycling ( $fleet_row );
      break;

      case MT_DESTROY:
        MissionCaseDestruction ( $fleet_row );
      break;

      case MT_MISSILE:  // Missiles !!
      break;

      case MT_EXPLORE:
        MissionCaseExpedition ( $fleet_row );
      break;

      default:
        doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
      break;
    }
  }

  if(FLT_FALLBACK)
  {
    doquery("DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT fleet_group FROM {{fleets}});");
    doquery("UNLOCK TABLES");
  }
  else
  {
    doquery('COMMIT;');
  }
}

?>
