<?php

/**
 * @function RestoreFleetToPlanet
 *
 * @version 1.0
 * @copyright 2008 Chlorel for XNova
 */

/*
@function RestoreFleetToPlanet

$fleet_row      = enregistrement de flotte
$start          = true  - planete de depart
                = false - planete d'arriv√©e
$only_resources = true - store only resources
                = false - store fleet too
returns         = bitmask for recaching
*/
function RestoreFleetToPlanet(&$fleet_row, $start = true, $only_resources = false)
{
  if(!is_array($fleet_row))
  {
    return CACHE_NOTHING;
  }

  global $sn_data;

  $prefix = $start ? 'start' : 'end';

  $query = 'UPDATE {{planets}} SET ';

  if(!$only_resources)
  {
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
    doquery("DELETE FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1;");
  }
  else
  {
    doquery("UPDATE {{fleets}} SET fleet_resource_metal = 0, fleet_resource_crystal = 0, fleet_resource_deuterium = 0, fleet_mess = 1 WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1;");
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

  return CACHE_FLEET | ($start ? CACHE_PLANET_SRC : CACHE_PLANET_DST);
}

/**
 * @function flt_flying_fleet_handler
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 * Modified by MadnessRed to support ACS
 */

function flt_unset_by_attack($attack_result, &$flt_user_cache, &$flt_planet_cache, &$flt_fleet_cache, &$flt_event_cache)
{
  foreach($attack_result as $combat_fleet_id => $combat_record)
  {
    unset($flt_user_cache[$combat_record['user']['id']]);
    if($combat_fleet_id)
    {
      unset($flt_fleet_cache[$combat_fleet_id]);
      $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id` = {$combat_fleet_id} LIMIT 1 FOR UPDATE;", '', true);
      flt_cache_fleet($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache, CACHE_COMBAT);
    }
  }
}

function flt_flyingFleetsSort($a, $b)
{
  return $a['fleet_time'] == $b['fleet_time'] ? 0 : ($a['fleet_time'] > $b['fleet_time'] ? 1 : -1);
}

function flt_planet_hash($planet_vector, $prefix = '')
{
  $type_prefix = $prefix ? $prefix : 'planet_';
  return 'g' . $planet_vector["{$prefix}galaxy"] . 's' . $planet_vector["{$prefix}system"] . 'p' . $planet_vector["{$prefix}planet"] . 't' . $planet_vector["{$type_prefix}type"];
}

function flt_cache_user($flt_user_row, &$flt_user_cache)
{
  $flt_user_row_id = isset($flt_user_row['id']) ? $flt_user_row['id'] : 0;

  if(!isset($flt_user_cache[$flt_user_row_id]))
  {
    $flt_user_cache[$flt_user_row_id] = $flt_user_row;
  }

  return $flt_user_row_id;
}

function flt_cache_planet($planet_vector, &$flt_user_cache, &$flt_planet_cache)
{
  $planet_hash = flt_planet_hash($planet_vector); //"g{$planet_vector['galaxy']}s{$planet_vector['system']}p{$planet_vector['planet']}t{$planet_vector['planet_type']}";
  if(!isset($flt_planet_cache[$planet_hash]))
  {
//!!!!!!!!!!!!! ќ“ Ћё„»“№ —»ћ”Ћя÷»ё !!!!!!!!!!!!!!!!!!!!!!!!!!
    $global_data = sys_o_get_updated(false, $planet_vector, $GLOBALS['time_now']);
    $flt_planet_cache[$planet_hash] = $global_data['planet'];

    flt_cache_user($global_data['user'], &$flt_user_cache);
  }

  return array('planet_hash' => $planet_hash, 'user_id' => $flt_planet_cache[$planet_hash]['id_owner']);
}

function flt_cache_fleet($fleet_row, &$flt_user_cache, &$flt_planet_cache, &$flt_fleet_cache, &$flt_event_cache, $cache_mode)
{
  $time_now = $GLOBALS['time_now'];

  // Dumping invalid fleet records
  // By design it should never triggered but let it be
  if(!$fleet_row || !is_array($fleet_row) || !$fleet_row['fleet_id'])
  {
    return;
  }

  // Checking - is there event for selected fleet in this time slot?

  if ($fleet_row['fleet_mess'] != 0)
  { // Fleet is returning to source
    if ($fleet_row['fleet_end_time'] <= $time_now)
    { // Fleet is arrived
      // Removing fleet source planet record from cache
      unset($flt_planet_cache[flt_planet_hash($fleet_row, 'fleet_start_')]);

      // Restoring fleet to planet
      RestoreFleetToPlanet($fleet_row, true);

      // Recaching changed data
      $source = flt_cache_planet(array('galaxy' => $fleet_row['fleet_start_galaxy'], 'system' => $fleet_row['fleet_start_system'], 'planet' => $fleet_row['fleet_start_planet'], 'planet_type' => $fleet_row['fleet_start_type']), &$flt_user_cache, &$flt_planet_cache);
    } // Otherwise fleet still not arriving and will not in this timeslot
    return;
  }
  else // Following code is almost useless - it should never trigger. But let it be
  { // Fleet is heading to destination or on timed mission (MT_HOLD or MT_EXPLORE)

    // Does fleet even arrive to destination?
    if ($fleet_row['fleet_start_time'] > $time_now)
    { // Fleet didn't arrive to destination yet. Skipping
      return;
    }

    // Does fleet has timed mission? If yes - does it complete?
    if ($fleet_row['fleet_end_stay'] && $fleet_row['fleet_end_stay'] > $time_now)
    {
      return;
    }
  }

  if(!isset($flt_fleet_cache[$fleet_row['fleet_id']]))
  {
    $flt_fleet_cache[$fleet_row['fleet_id']] = $fleet_row;
  }

  $source = flt_cache_planet(array('galaxy' => $fleet_row['fleet_start_galaxy'], 'system' => $fleet_row['fleet_start_system'], 'planet' => $fleet_row['fleet_start_planet'], 'planet_type' => $fleet_row['fleet_start_type']), &$flt_user_cache, &$flt_planet_cache);

  if($fleet_row['fleet_mission'] != MT_EXPLORE)
  {
    if($fleet_row['fleet_mission'] == MT_RECYCLE || $fleet_row['fleet_mission'] == MT_COLONIZE)
    {
      $fleet_row['fleet_end_type'] = PT_PLANET;
    }
    elseif($fleet_row['fleet_mission'] == MT_DESTROY)
    {
      $fleet_row['fleet_end_type'] = PT_MOON;
    }

    $destination = flt_cache_planet(array('galaxy' => $fleet_row['fleet_end_galaxy'], 'system' => $fleet_row['fleet_end_system'], 'planet' => $fleet_row['fleet_end_planet'], 'planet_type' => $fleet_row['fleet_end_type']), &$flt_user_cache, &$flt_planet_cache);
  }
  else
  {
    $destination = false;
  }

  if($cache_mode & CACHE_EVENT)
  {
    $flt_event_cache[] = array(
      'fleet_id'        => $fleet_row['fleet_id'],
      'fleet_time'      => $fleet_row['fleet_time'],
      'src_planet_hash' => $source ? $source['planet_hash'] : 0,
      'src_user_id'     => $source ? $source['user_id'] : 0,
      'dst_planet_hash' => $destination ? $destination['planet_hash'] : 0,
      'dst_user_id'     => $destination ? $destination['user_id'] : 0
    );
  }
}

function flt_t_flying_fleet_handler()
{
  // SAFE FALLBACK TO OLD-STYLE HANDLER
  define('FLT_FALLBACK', false);

  global $time_now;

  if(FLT_FALLBACK)
  {
    if(($time_now - $GLOBALS['config']->flt_lastUpdate <= 8 ) || $GLOBALS['skip_fleet_update'])
    {
      return;
    }

    $GLOBALS['config']->db_saveItem('flt_lastUpdate', $time_now);
    doquery('LOCK TABLE {{table}}aks WRITE, {{table}}rw WRITE, {{table}}errors WRITE, {{table}}messages WRITE, {{table}}fleets WRITE, {{table}}planets WRITE, {{table}}users WRITE, {{table}}logs WRITE, {{table}}iraks WRITE, {{table}}statpoints WRITE, {{table}}referrals WRITE, {{table}}counter WRITE');
  }
  else
  {
//    $time_now = 1295903553 + 130 * 60;
    //pdump(date(FMT_DATE_TIME, $time_now));
    doquery('START TRANSACTION;');
  }

  coe_o_missile_calculate();

  $flt_user_cache   = array();
  $flt_fleet_cache  = array();
  $flt_event_cache  = array();
  $flt_planet_cache = array();

  $_fleets = doquery("SELECT *, fleet_start_time AS fleet_time FROM `{{fleets}}` WHERE `fleet_start_time` <= '{$time_now}' FOR UPDATE;");
  while ($fleet_row = mysql_fetch_assoc($_fleets))
  {
    flt_cache_fleet($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache, CACHE_ALL);
  }

  $_fleets = doquery("SELECT *, fleet_end_stay AS fleet_time FROM `{{fleets}}` WHERE `fleet_end_stay` <= '{$time_now}' AND fleet_end_stay > 0 FOR UPDATE;");
  while ($fleet_row = mysql_fetch_assoc($_fleets))
  {
    flt_cache_fleet($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache, CACHE_ALL);
  }

  $_fleets = doquery("SELECT *, fleet_end_time AS fleet_time FROM `{{fleets}}` WHERE `fleet_end_time` <= '{$time_now}' FOR UPDATE;");
  while ($fleet_row = mysql_fetch_assoc($_fleets))
  {
    flt_cache_fleet($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache, CACHE_ALL);
  }

  uasort($flt_event_cache, 'flt_flyingFleetsSort');
  unset($_fleets);

  if(FLT_FALLBACK)
  {
    flt_fallback($flt_event_cache);
    doquery("UNLOCK TABLES");
    return;
  }
/*
pdump(count($flt_user_cache), '$flt_user_row');
pdump(count($flt_planet_cache), '$flt_planet_row');
pdump(count($flt_fleet_cache), '$flt_fleet_cache');
pdump(count($flt_event_cache), '$flt_event_cache');
/*
foreach($flt_event_cache as $index => $data)
{
  pdump($flt_fleet_cache[$data['fleet_id']]['fleet_id'], "index {$index}, fleet_id {$data['fleet_id']}");
}
*/
//die();

  unset($attack_result);
  foreach($flt_event_cache as $fleet_event)
  {
    $fleet_row = $flt_fleet_cache[$fleet_event['fleet_id']];
    if(!$fleet_row)
    {
      continue;
    }

    $mission_data = array(
      'fleet' => $flt_fleet_cache[$fleet_event['fleet_id']],
      'src_user' => $flt_user_cache[$fleet_event['src_user_id']],
      'src_planet' => $flt_planet_cache[$fleet_event['src_planet_hash']],
      'dst_user' => $flt_user_cache[$fleet_event['dst_user_id']],
      'dst_planet' =>$flt_planet_cache[$fleet_event['dst_planet_hash']]
    );

    // ћиссии должны возвращать измененные результаты, что бы второй раз не лезть в базу
    unset($mission_result);
    switch ($fleet_row['fleet_mission'])
    {
      // ƒл€ боевых атак нужно обновл€ть по —јЅу и по холду - таки надо возвращать данные из обработчика миссий!
      case MT_AKS:
      case MT_ATTACK:
        $attack_result = flt_mission_attack($mission_data);
        $mission_result = CACHE_COMBAT;
      break;

      case MT_DESTROY:
        $attack_result = flt_mission_destroy($mission_data);
        $mission_result = CACHE_COMBAT;
      break;

      case MT_COLONIZE:
        $mission_result = flt_mission_colonize($mission_data);
      break;

      case MT_EXPLORE:
        $mission_result = flt_mission_explore($mission_data);
      break;

      case MT_RELOCATE:
        $mission_result = flt_mission_relocate($mission_data);
      break;

      case MT_TRANSPORT:
        $mission_result = flt_mission_transport($mission_data);
      break;

      case MT_RECYCLE:
        $mission_result = flt_mission_recycle($mission_data);
      break;

      case MT_SPY:
        $mission_result = flt_mission_spy($fleet_row);
      break;

      case MT_HOLD:
        $mission_result = flt_mission_hold($fleet_row);
      break;

      case MT_MISSILE:  // Missiles !!
      break;

      default:
        doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
      break;
    }

    if($attack_result)
    {
      // Case for passed attack
// pdump($attack_result);
      $attack_result = $attack_result['rw'][0];
      flt_unset_by_attack($attack_result['attackers'], $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache);
      flt_unset_by_attack($attack_result['defenders'], $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache);
    }
    else
    {
      // Unsetting data that we broken in mission handler
      if($mission_result & CACHE_FLEET)
      {
        unset($flt_fleet_cache[$fleet_event['fleet_id']]);
        $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id` = {$fleet_event['fleet_id']} LIMIT 1 FOR UPDATE;", '', true);
      }
      if($mission_result & CACHE_USER_SRC)
      {
        unset($flt_user_cache[$fleet_event['src_user_id']]);
      }
      if($mission_result & CACHE_USER_DST)
      {
        unset($flt_user_cache[$fleet_event['dst_user_id']]);
      }
      if($mission_result & CACHE_PLANET_SRC)
      {
        unset($flt_planet_cache[$fleet_event['src_planet_hash']]);
      }
      if($mission_result & CACHE_PLANET_DST)
      {
        unset($flt_planet_cache[$fleet_event['dst_planet_hash']]);
      }

      // Reloading fresh data from DB
      flt_cache_fleet($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache, CACHE_COMBAT);
    }

  }
  doquery('COMMIT;');
}

function flt_fallback($flt_event_cache)
{
  foreach($flt_event_cache as $fleet_event)
  {
    $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE fleet_id = '{$fleet_event['fleet_id']}' LIMIT 1;", '', true);
    switch ($fleet_row['fleet_mission'])
    {
      case MT_ATTACK:
        MissionCaseAttack ( $fleet_row );
      break;

      case MT_AKS:
        MissionCaseAttack ( $fleet_row );
      break;

      case MT_DESTROY:
        MissionCaseDestruction ( $fleet_row );
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
  doquery("DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT fleet_group FROM {{fleets}});");
}

?>
