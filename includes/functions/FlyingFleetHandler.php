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
//           -> false = planete d'arrivГ©e
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

  return CACHE_FLEET | CACHE_USER_SRC;
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

function flt_planet_hash($planet_vector, $prefix = '')
{
  $type_prefix = $prefix ? $prefix : 'planet_';
  return 'g' . $planet_vector["{$prefix}galaxy"] . 's' . $planet_vector["{$prefix}system"] . 'p' . $planet_vector["{$prefix}planet"] . 't' . $planet_vector["{$type_prefix}planet_type"];
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
//!!!!!!!!!!!!! ОТКЛЮЧИТЬ СИМУЛЯЦИЮ !!!!!!!!!!!!!!!!!!!!!!!!!!
    $global_data = sys_o_get_updated(false, $planet_vector, $GLOBALS['time_now'], true);
    $flt_planet_cache[$planet_hash] = $global_data['planet'];

    flt_cache_user($global_data['user'], &$flt_user_cache);
  }

  return array('planet_hash' => $planet_hash, 'user_id' => $flt_planet_cache[$target_planet_hash]['id_owner']);
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

  // Checking if we should now to proceed this fleet - does it arrive?
  // By design it should never triggered but let it be
  if ($fleet_row['fleet_start_time'] > $time_now)
  {
    return;
  }

  // Пока возвращение будет обрабатываться процедурой миссии
  // Потому как иногда нужно извещать о возвращении флота в разных форматах
  // А потом надо будет дописать тут, что бы лишний раз не нагружать кэширование
  // И вообще - миссии должны возвращать измененные результаты, что бы второй раз не лезть в базу
/*
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
*/
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

    $destination = flt_cache_planet(array('galaxy' => $fleet_row['fleet_end_galaxy'], 'system' => $fleet_row['fleet_end_system'], 'planet' => $fleet_row['fleet_end_planet'], 'planet_type' => $fleet_end_type), &$flt_user_cache, &$flt_planet_cache);
  }
  else
  {
    $destination = false;
  }

  $source = flt_cache_planet(array('galaxy' => $fleet_row['fleet_start_galaxy'], 'system' => $fleet_row['fleet_start_system'], 'planet' => $fleet_row['fleet_start_planet'], 'planet_type' => $fleet_row['fleet_start_type']), &$flt_user_cache, &$flt_planet_cache);

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
  $flt_event_cache = array();

  $_fleets = doquery("SELECT *, fleet_start_time AS fleet_time FROM `{{fleets}}` WHERE `fleet_start_time` <= '{$time_now}' FOR UPDATE;");
  while ($fleet_row = mysql_fetch_assoc($_fleets))
  {
    flt_cache_fleet($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache, CACHE_EVENT);
  }

  $_fleets = doquery("SELECT *, fleet_end_time AS fleet_time FROM `{{fleets}}` WHERE `fleet_end_time` <= '{$time_now}' FOR UPDATE;");
  while ($fleet_row = mysql_fetch_assoc($_fleets))
  {
    flt_cache_fleet($fleet_row, $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache, CACHE_ALL);
  }

  uasort($flt_event_cache, 'flt_flyingFleetsSort');
  unset($_fleets);

if(!FLT_FALLBACK)
{
pdump(count($flt_user_cache), '$flt_user_row');
pdump(count($flt_planet_cache), '$flt_planet_row');
pdump(count($flt_fleet_cache), '$flt_fleet_cache');
pdump(count($flt_event_cache), '$flt_event_cache');

foreach($flt_event_cache as $index => $data)
{
  pdump($flt_fleet_cache[$data['fleet_id']], "index {$index}, fleet_id {$data['fleet_id']}");
}
die();
}

  foreach($flt_event_cache as $fleet_event)
  {
    if(FLT_FALLBACK)
    {
      $fleet_row = doquery( "SELECT * FROM {{fleets}} WHERE fleet_id={$fleet_event['fleet_id']}", '', true );
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
      continue;
    }

    $fleet_row = $flt_fleet_cache[$fleet_event['fleet_id']];
    if(!$fleet_row)
    {
      continue;
    }

    unset($mission_result);
    switch ($fleet_row['fleet_mission'])
    {
      case MT_EXPLORE:
        $mission_result = flt_mission_expedition($fleet_row);
      break;

      case MT_RELOCATE:
        $mission_result = flt_mission_relocate($fleet_row);
      break;

      /*
      // Для боевых атак нужно обновлять по САБу и по холду - таки надо возвращать данные из обработчика миссий
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

      default:
        doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
      break;
      */
    }

    // Unsetting data that we broken in mission handler
    if($mission_result & CACHE_FLEET)
    {
      unset($flt_fleet_cache[$fleet_event['fleet_id']]);
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

/*
    // Надо предусмотреть случаи, когда мы два раза обрабатываем один и тот же флот - сначала с 0, затем с 1
    // Это может случиться после долгого отключения сервера
    unset($target_planet_row);
    unset($target_user_row);
    $target_data = flt_cache_fleet($fleet_row, $flt_user_cache, $flt_planet_cache);

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
