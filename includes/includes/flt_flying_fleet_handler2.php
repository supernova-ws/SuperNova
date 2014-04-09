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
                = false - planete d'arrivГ©e
$only_resources = true - store only resources
                = false - store fleet too
returns         = bitmask for recaching
*/

// ------------------------------------------------------------------
function RestoreFleetToPlanet(&$fleet_row, $start = true, $only_resources = false, $safe_fleet = false){return sn_function_call('RestoreFleetToPlanet', array(&$fleet_row, $start, $only_resources, $safe_fleet, &$result));}
function sn_RestoreFleetToPlanet(&$fleet_row, $start = true, $only_resources = false, $safe_fleet = false, &$result)
{
  $result = CACHE_NOTHING;
  if(!is_array($fleet_row))
  {
    return $result;
  }
  elseif(!$safe_fleet)
  {
    $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1 FOR UPDATE;", true);
    if(!$fleet_row || !is_array($fleet_row) || ($fleet_row['fleet_mess'] == 1 && $only_resources))
    {
      return $result;
    }
  }

  $prefix = $start ? 'start' : 'end';

  $query = '';
  if(!$only_resources)
  {
    flt_destroy($fleet_row);

    $planet_arrival = doquery("SELECT `id_owner` FROM {{planets}}  WHERE `galaxy` = '" . $fleet_row["fleet_{$prefix}_galaxy"] . "' AND `system` = '". $fleet_row["fleet_{$prefix}_system"] ."' AND `planet` = '". $fleet_row["fleet_{$prefix}_planet"] ."' AND `planet_type` = '". $fleet_row["fleet_{$prefix}_type"] ."' LIMIT 1 FOR UPDATE;", true);
    if($fleet_row['fleet_owner'] == $planet_arrival['id_owner'])
    {
      $fleet_array = sys_unit_str2arr($fleet_row['fleet_array']);
      foreach($fleet_array as $ship_id => $ship_count)
      {
        if(!$ship_count)
        {
          continue;
        }
        $ship_db_name = get_unit_param($ship_id, P_NAME);
        $query .= "`{$ship_db_name}` = `{$ship_db_name}` + '{$ship_count}', ";
      }
    }
    else
    {
      // doquery("DELETE FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1;");
      return CACHE_NOTHING;
    }


    /*
    pdump($query);
    $query = '';
    $fleet_strings = explode(';', $fleet_row['fleet_array']);
    foreach($fleet_strings as $ship_string)
    {
      if($ship_string != '')
      {
        $ship_record = explode (',', $ship_string);
        $ship_db_name = get_unit_param($ship_record[0], P_NAME);
        $query .= "`{$ship_db_name}` = `{$ship_db_name}` + '{$ship_record[1]}', ";
      }
    }
    // doquery("DELETE FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1;");
    pdump($query);
    die();
    */
  }
  else
  {
    // flt_send_back($fleet_row);
    doquery("UPDATE {{fleets}} SET fleet_resource_metal = 0, fleet_resource_crystal = 0, fleet_resource_deuterium = 0, fleet_mess = 1 WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1;");
  }

  $query = 'UPDATE {{planets}} SET ' . $query;
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
/*
  // TODO: Вынести в модуль капитанов
  global $sn_module;
  if(!$only_resources && isset($sn_module['unit_captain']) && $sn_module['unit_captain']->manifest['active'])
  {
    $captain = doquery(
      "SELECT *
      FROM {{unit}} AS u
        LEFT JOIN {{captain}} AS c ON c.captain_unit_id = u.unit_id
      WHERE
        u.`unit_player_id` = {$fleet_row['fleet_owner']}
        AND u.`unit_location_type` = " . LOC_FLEET . "
        AND u.`unit_location_id` = {$fleet_row['fleet_id']}
        AND u.`unit_snid` = " . UNIT_CAPTAIN . "
        LIMIT 1 FOR UPDATE"
      , true);

    if(is_array($captain))
    {
      $planet = doquery(
        "SELECT `id`
        FROM {{planets}}
        WHERE
          `system` = '". $fleet_row["fleet_{$prefix}_system"] ."' AND
          `galaxy` = '". $fleet_row["fleet_{$prefix}_galaxy"] ."' AND
          `planet` = '". $fleet_row["fleet_{$prefix}_planet"] ."' AND
          `planet_type` = '". $fleet_row["fleet_{$prefix}_type"] ."' LIMIT 1"
        , true);
      if($planet['id'])
      {
        doquery("UPDATE {{unit}} SET `unit_location_type` = " . LOC_PLANET . ", `unit_location_id` = {$planet['id']} WHERE `unit_id` = {$captain['unit_id']} LIMIT 1");
      }
    }
  }
*/
  $result = CACHE_FLEET | ($start ? CACHE_PLANET_SRC : CACHE_PLANET_DST);

  return $result;
}

// ------------------------------------------------------------------
function flt_planet_hash($planet_vector, $prefix = '')
{
  $type_prefix = $prefix ? $prefix : 'planet_';
  return 'g' . $planet_vector["{$prefix}galaxy"] . 's' . $planet_vector["{$prefix}system"] . 'p' . $planet_vector["{$prefix}planet"] . 't' . $planet_vector["{$type_prefix}type"];
}

// ------------------------------------------------------------------
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

// ------------------------------------------------------------------
function flt_cache_user($flt_user_row, &$flt_user_cache)
{
  if(!$flt_user_row)
  {
    return;
  }

  if(!is_array($flt_user_row))
  {
    $flt_user_row_id = $flt_user_row;
    // Checking if it is cached
    if(isset($flt_user_row[$flt_user_row_id]))
    {
      $flt_user_row = $flt_user_row[$flt_user_row_id];
    }
    else
    {
      $flt_user_row = doquery("SELECT * FROM {{users}} WHERE `id` = '{$flt_user_row_id}' LIMIT 1 FOR UPDATE;", '', true);
    }
  }
  else
  {
    $flt_user_row_id = $flt_user_row['id'];
  }

  if(!isset($flt_user_cache[$flt_user_row_id]))
  {
    $flt_user_cache[$flt_user_row_id] = $flt_user_row;
  }

  return $flt_user_row_id;
}

// ------------------------------------------------------------------
function flt_cache_planet($planet_vector, &$flt_user_cache, &$flt_planet_cache)
{
  if(!$planet_vector)
  {
    return;
  }

  $planet_hash = flt_planet_hash($planet_vector); //"g{$planet_vector['galaxy']}s{$planet_vector['system']}p{$planet_vector['planet']}t{$planet_vector['planet_type']}";
  if(!isset($flt_planet_cache[$planet_hash]))
  {
    $global_data = sys_o_get_updated(false, $planet_vector, $GLOBALS['time_now']);
    $flt_planet_cache[$planet_hash] = $global_data['planet'];

    if($flt_planet_cache[$planet_hash])
    {
      $flt_user_row_id = flt_cache_user($global_data['user'], $flt_user_cache);
    }
    else
    {
      $flt_user_row_id = 0;
    }
  }
  else
  {
    $flt_user_row_id = flt_cache_user($flt_planet_cache[$planet_hash]['id_owner'], $flt_user_cache);
  }

  return array('planet_hash' => $planet_hash, 'user_id' => $flt_user_row_id);
}

// ------------------------------------------------------------------
function flt_cache_fleet($fleet_row, &$flt_user_cache, &$flt_planet_cache, &$flt_fleet_cache, &$flt_event_cache, $cache_mode)
{
  // Empty $fleet_row - no chance to know anything about it. By design it should never triggered but let it be
  if(!$fleet_row)
  {
    return false;
  }

  // $fleet_row is not an array - may be ONLY fleet ID. Getting $fleet_row from DB by ID
  if (!is_array($fleet_row))
  {
    $fleet_row_id = $fleet_row;
    // Checking if it is cached
    if(isset($flt_fleet_cache[$fleet_row_id]))
    {
      $fleet_row = $flt_fleet_cache[$fleet_row_id];
    }
    else
    {
      $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id` = '{$fleet_row_id}' LIMIT 1 FOR UPDATE;", '', true);
    }
  }
  else
  {
    $fleet_row_id = $fleet_row['fleet_id'];
  }

  // $fleet_row is false - not existing DB record
  if(!$fleet_row)
  {
    $flt_fleet_cache[$fleet_row_id] = $fleet_row;
    return false;
  }

  if ($fleet_row['fleet_mess'] != 0)
  { // Fleet is returning to source
    if ($fleet_row['fleet_end_time'] <= SN_TIME_NOW)
    { // Fleet is arrived
      // Restoring fleet to planet
      RestoreFleetToPlanet($fleet_row, true);

      // Tagging record for fleet as not existing in DB
      $flt_fleet_cache[$fleet_row['fleet_id']] = false;

      // Removing fleet source planet record from cache
      unset($flt_planet_cache[flt_planet_hash($fleet_row, 'fleet_start_')]);
      // Changed data will be recached later
    }
    return false;
  }
  // Otherwise fleet still not arriving and will not processed in this timeslot
  // Following code is almost useless - it should never trigger. But let it be just in case
  //      Does fleet even arrive to destination?     OR  Does fleet has timed mission (MT_HOLD/MT_EXPLORE)? If yes - does it complete?
  elseif ($fleet_row['fleet_start_time'] > SN_TIME_NOW || ($fleet_row['fleet_end_stay'] && $fleet_row['fleet_end_stay'] > SN_TIME_NOW))
  {
    return false;
  }

  if(!isset($flt_fleet_cache[$fleet_row_id]))
  {
    $flt_fleet_cache[$fleet_row_id] = $fleet_row;
  }

  if($fleet_row['fleet_mission'] == MT_RECYCLE || $fleet_row['fleet_mission'] == MT_COLONIZE)
  {
    $fleet_row['fleet_end_type'] = PT_PLANET;
  }
  elseif($fleet_row['fleet_mission'] == MT_DESTROY)
  {
    $fleet_row['fleet_end_type'] = PT_MOON;
  }

  // On CACHE_EVENT we will cache only fleet to reduce row lock rate
  if(($cache_mode & CACHE_EVENT) == CACHE_EVENT)
  {
    $flt_event_cache[] = array(
      'fleet_id'        => $fleet_row['fleet_id'],
      'fleet_time'      => $fleet_row['fleet_time'],
      'src_planet_hash' => flt_planet_hash($fleet_row, 'fleet_start_'),
      'src_user_id'     => $fleet_row['fleet_owner'],
      'dst_planet_hash' => flt_planet_hash($fleet_row, 'fleet_end_'),
      'dst_user_id'     => $fleet_row['fleet_target_owner']
    );
  }
  else
  {
    $sn_groups_mission = sn_get_groups('missions');
    $mission_data = $sn_groups_mission[$fleet_row['fleet_mission']];

// А здесь надо проверять, какие нужны данные и кэшировать только их
    $source = array('planet_hash' => '', 'user_id' => 0);
    if($mission_data['src_planet'])
    {
      flt_cache_planet(array('galaxy' => $fleet_row['fleet_start_galaxy'], 'system' => $fleet_row['fleet_start_system'], 'planet' => $fleet_row['fleet_start_planet'], 'planet_type' => $fleet_row['fleet_start_type']), $flt_user_cache, $flt_planet_cache);
    }
    elseif($mission_data['src_user'])
    {
      flt_cache_user($fleet_row['fleet_owner'], $flt_user_cache);
    }

    $destination = array('planet_hash' => '', 'user_id' => 0);
    if($mission_data['dst_planet'])
    {
      $destination = flt_cache_planet(array('galaxy' => $fleet_row['fleet_end_galaxy'], 'system' => $fleet_row['fleet_end_system'], 'planet' => $fleet_row['fleet_end_planet'], 'planet_type' => $fleet_row['fleet_end_type']), $flt_user_cache, $flt_planet_cache);
    }
    elseif($mission_data['dst_user'])
    {
      flt_cache_user($fleet_row['fleet_target_owner'], $flt_user_cache);
    }
  }

  return true;
}

// ------------------------------------------------------------------
function flt_flyingFleetsSort($a, $b)
{
  // Сравниваем время флотов - кто раньше, тот и первый обрабатывается
  return $a['fleet_time'] > $b['fleet_time'] ? 1 : ($a['fleet_time'] < $b['fleet_time'] ? -1 :
    // Если время - одинаковое, сравниваем события флотов
    // Если события - одинаковые, то флоты равны
    ($a['fleet_event'] == $b['fleet_event'] ? 0 :
      // Если события разные - первыми считаем прибывающие флоты
      ($a['fleet_event'] == EVENT_FLT_ARRIVE ? 1 : ($b['fleet_event'] == EVENT_FLT_ARRIVE ? -1 :
        // Если нет прибывающих флотов - дальше считаем флоты, которые закончили миссию
        ($a['fleet_event'] == EVENT_FLT_ACOMPLISH ? 1 : ($b['fleet_event'] == EVENT_FLT_ACOMPLISH ? -1 :
          // Если нет флотов, закончивших задание - остались возвращающиеся флоты, которые равны между собой
          // TODO: Добавить еще проверку по ID флота и/или времени запуска - что бы обсчитывать их в порядке запуска
          (
            0 // Вообще сюда доходить не должно - будет отсекаться на равенстве событий
          )
        ))
      ))
    )
  );

//  return
//    $a['fleet_time'] > $b['fleet_time'] ? 1 :
//      ($a['fleet_time'] < $b['fleet_time'] ? -1 :
//        0
//      )
//    ;
}


// ------------------------------------------------------------------
function flt_flying_fleet_handler(&$config, $skip_fleet_update)
{
  $flt_update_mode = 0;
  // 0 - old
  // 1 - new

  /*
  if(($time_now - $GLOBALS['config']->flt_lastUpdate <= 8 ) || $GLOBALS['skip_fleet_update'])
  {
    return;
  }

  $GLOBALS['config']->db_saveItem('flt_lastUpdate', $time_now);
  doquery('LOCK TABLE {{table}}aks WRITE, {{table}}rw WRITE, {{table}}errors WRITE, {{table}}messages WRITE, {{table}}fleets WRITE, {{table}}planets WRITE, {{table}}users WRITE, {{table}}logs WRITE, {{table}}iraks WRITE, {{table}}statpoints WRITE, {{table}}referrals WRITE, {{table}}counter WRITE');
  */

  if($skip_fleet_update)
  {
    return;
  }

  switch($flt_update_mode)
  {
    case 0:
      if(SN_TIME_NOW - $config->flt_lastUpdate <= 4)
      {
        return;
      }
    break;

    case 1:
      if($config->flt_lastUpdate)
      {
        if(SN_TIME_NOW - $config->flt_lastUpdate <= 15)
        {
          return;
        }
        else
        {
          $GLOBALS['debug']->error('Flying fleet handler is on timeout', 'FFH Error', 504);
        }
      }
    break;
  }

  $config->db_saveItem('flt_lastUpdate', SN_TIME_NOW);

/*

[*] Нужно ли заворачивать ВСЕ в одну транзакцию?
    С одной стороны - да, что бы данные были гарантированно на момент снапшота
    С другой стороны - нет, потому что при большой активности это все будет блокировать слишком много рядов, да и таймаут будет большой для ожидания всего разлоченного
    Стоит завернуть каждую миссию отдельно? Это сильно увеличит количество запросов, зато так же сильно снизит количество блокировок.

    Resume: НЕТ! Надо оставить все в одной транзакции! Так можно будет поддерживать consistency кэша. Там буквально сантисекунды блокировки

[*] Убрать кэшированние данных о пользователях и планета. Офигенно освободит память - проследить!
    НЕТ! Считать, скольким флотам нужна будет инфа и кэшировать только то, что используется больше раза!
    Заодно можно будет исключить перересчет очередей/ресурсов - сильно ускорит дело!
    Особенно будет актуально, когда все бонусы будут в одной таблице
    Ну и никто не заставляет как сейчас брать ВСЕ из таблицы - только по полям. Гемор, но не сильный - сделать запрос по группам sn_data
    И писать в БД только один раз результат

[*] Нужно ли на этом этапе знать полную информацию о флотах?
    Заблокировать флоты можно и неполным запросом. Блокировка флотов - это не страшно. Ну, не пройдет одна-две отмены - так никто и не гарантировал реалтайма!
    С одной стороны - да, уменьшит количество запросов
    С другой стооны - расход памяти
    Все равно надо будет знать полную инфу о флоте в момент обработки

[*] Сделать тестовую БД для расчетов

[*] Но не раньше, чем переписать все миссии

*/

  global $time_now;

  $fleet_list = array();
  $fleet_event_list = array();
  $missions_used = array();


  sn_db_transaction_start();
  coe_o_missile_calculate();
  sn_db_transaction_commit();

  // doquery('START TRANSACTION;');
  sn_db_transaction_start();
  $_fleets = doquery("SELECT * FROM `{{fleets}}` WHERE
    (`fleet_start_time` <= '{$time_now}' AND `fleet_mess` = 0) 
    OR (`fleet_end_stay` <= '{$time_now}' AND fleet_end_stay > 0 AND `fleet_mess` = 0)
    OR (`fleet_end_time` <= '{$time_now}')
  FOR UPDATE;");

  while($fleet_row = mysql_fetch_assoc($_fleets))
  {
    // Унифицировать код с темплейтным разбором эвентов на планете!
    $fleet_list[$fleet_row['fleet_id']] = $fleet_row;
    $missions_used[$fleet_row['fleet_mission']] = 1;
    if($fleet_row['fleet_start_time'] <= SN_TIME_NOW && $fleet_row['fleet_mess'] == 0)
    {
      $fleet_event_list[] = array(
        'fleet_row' => &$fleet_list[$fleet_row['fleet_id']],
        'fleet_time' => $fleet_list[$fleet_row['fleet_id']]['fleet_start_time'],
        'fleet_event' => EVENT_FLT_ARRIVE,
      );
    }

    if($fleet_row['fleet_end_stay'] > 0 && $fleet_row['fleet_end_stay'] <= $time_now && $fleet_row['fleet_mess'] == 0)
    {
      $fleet_event_list[] = array(
        'fleet_row' => &$fleet_list[$fleet_row['fleet_id']],
        'fleet_time' => $fleet_list[$fleet_row['fleet_id']]['fleet_end_stay'],
        'fleet_event' => EVENT_FLT_ACOMPLISH,
      );
    }

    if($fleet_row['fleet_end_time'] <= $time_now)
    {
      $fleet_event_list[] = array(
        'fleet_row' => &$fleet_list[$fleet_row['fleet_id']],
        'fleet_time' => $fleet_list[$fleet_row['fleet_id']]['fleet_end_time'],
        'fleet_event' => EVENT_FLT_RETURN,
      );
    }
  }
  sn_db_transaction_commit();

  uasort($fleet_event_list, 'flt_flyingFleetsSort');
  unset($_fleets);

// TODO: Грузить только используемые модули из $missions_used
  $mission_files = array(
    MT_ATTACK => 'flt_mission_attack.php',
    MT_AKS => 'flt_mission_attack.php',
    // MT_DESTROY => 'flt_mission_destroy.php',
    MT_DESTROY => 'flt_mission_attack.php',

    MT_TRANSPORT => 'flt_mission_transport.php',
    MT_RELOCATE => 'flt_mission_relocate.php',
    MT_HOLD => 'flt_mission_hold.php',
    MT_SPY => 'flt_mission_spy.php',
    MT_COLONIZE => 'flt_mission_colonize.php',
    MT_RECYCLE => 'flt_mission_recycle.php',
//    MT_MISSILE => 'flt_mission_missile.php',
    MT_EXPLORE => 'flt_mission_explore.php',
  );
  foreach($missions_used as $mission_id => $cork)
  {
    require_once("includes/includes/{$mission_files[$mission_id]}");
  }



  $sn_groups_mission = sn_get_groups('missions');
  foreach($fleet_event_list as $fleet_event)
  {
    // TODO: Указатель тут потом сделать
    // TODO: СЕЙЧАС НАДО ПРОВЕРЯТЬ ПО БАЗЕ - А ЖИВОЙ ЛИ ФЛОТ?!
    $fleet_row = $fleet_event['fleet_row'];
    if(!$fleet_row)
    {
      // Fleet was destroyed in course of previous actions
      continue;
    }

    sn_db_transaction_start();

    $mission_data = $sn_groups_mission[$fleet_row['fleet_mission']];
    // Формируем запрос, блокирующий сразу все нужные записи
    doquery($q = "
    SELECT 1
    FROM {{fleets}} AS f" .
      ($mission_data['dst_user'] || $mission_data['dst_planet'] ? " LEFT JOIN {{users}} AS ud ON ud.id = f.fleet_target_owner" : '') .
      ($mission_data['dst_planet'] ? " LEFT JOIN {{planets}} AS pd ON pd.id = f.fleet_end_planet_id" : '') .

      // Блокировка всех прилетающих и улетающих флотов, если нужно
      ($mission_data['dst_fleets'] ? " LEFT JOIN {{fleets}} AS fd ON fd.fleet_end_planet_id = f.fleet_end_planet_id OR fd.fleet_start_planet_id = f.fleet_end_planet_id" : '') .

      ($mission_data['src_user'] || $mission_data['src_planet'] ? " LEFT JOIN {{users}} AS us ON us.id = f.fleet_owner" : '') .
      ($mission_data['src_planet'] ? " LEFT JOIN {{planets}} AS ps ON ps.id = f.fleet_start_planet_id" : '') .

      " WHERE f.fleet_id = {$fleet_row['fleet_id']}
    GROUP BY 1 FOR UPDATE");

    // print($q);

    $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE fleet_id = {$fleet_row['fleet_id']} FOR UPDATE", true);
    if(!$fleet_row || empty($fleet_row))
    {
      // Fleet was destroyed in course of previous actions
      sn_db_transaction_commit();
      continue;
    }

    if($fleet_event['fleet_event'] == EVENT_FLT_RETURN)
    {
      // Fleet returns to planet
      RestoreFleetToPlanet($fleet_row, true, false, true);
      sn_db_transaction_commit();
      continue;
    }


    // TODO: Здесь тоже указатели
    // TODO: Кэширование
    // TODO: Выбирать только нужные поля

    // шпионаж не дает нормальный ID fleet_end_planet_id 'dst_planet'
    $mission_data = array(
      'fleet'      => &$fleet_row,
      'dst_user'   => $mission_data['dst_user'] ? doquery("SELECT * FROM {{users}} WHERE `id` = {$fleet_row['fleet_target_owner']} LIMIT 1 FOR UPDATE;", true) : null,
      // 'dst_planet' => $mission_data['dst_planet'] ? doquery("/* 1 */ SELECT * FROM {{planets}} WHERE `id` = {$fleet_row['fleet_end_planet_id']} LIMIT 1 FOR UPDATE;", true) : null,
      'dst_planet' => $mission_data['dst_planet'] ? doquery("SELECT * FROM {{planets}} WHERE `galaxy` = {$fleet_row['fleet_end_galaxy']} AND `system` = {$fleet_row['fleet_end_system']} AND `planet` = {$fleet_row['fleet_end_planet']} AND `planet_type` = " . ($fleet_row['fleet_end_type'] == PT_DEBRIS ? PT_PLANET : $fleet_row['fleet_end_type']) . " LIMIT 1 FOR UPDATE;", true) : null,
      'src_user'   => $mission_data['src_user'] ? doquery("SELECT * FROM {{users}} WHERE `id` = {$fleet_row['fleet_owner']} LIMIT 1 FOR UPDATE;", true) : null,
      // 'src_planet' => $mission_data['src_planet'] ? doquery("/* 2 */ SELECT * FROM {{planets}} WHERE `id` = {$fleet_row['fleet_start_planet_id']} LIMIT 1 FOR UPDATE;", true) : null,
      'src_planet' => $mission_data['src_planet'] ? doquery("SELECT * FROM {{planets}} WHERE `galaxy` = {$fleet_row['fleet_start_galaxy']} AND `system` = {$fleet_row['fleet_start_system']} AND `planet` = {$fleet_row['fleet_start_planet']} AND `planet_type` = {$fleet_row['fleet_start_type']} LIMIT 1 FOR UPDATE;", true) : null,
      'fleet_event' => $fleet_event['fleet_event'],
    );

    switch($fleet_row['fleet_mission'])
    {
      // Для боевых атак нужно обновлять по САБу и по холду - таки надо возвращать данные из обработчика миссий!
      case MT_AKS:
      case MT_ATTACK:
      case MT_DESTROY:
        $attack_result = flt_mission_attack($mission_data);
        $mission_result = CACHE_COMBAT;
      break;

      /*
      case MT_DESTROY:
        $attack_result = flt_mission_destroy($mission_data);
        $mission_result = CACHE_COMBAT;
      break;
      */

      case MT_TRANSPORT:
        $mission_result = flt_mission_transport($mission_data);
      break;

      case MT_HOLD:
        $mission_result = flt_mission_hold($mission_data);
      break;

      case MT_RELOCATE:
        $mission_result = flt_mission_relocate($mission_data);
      break;

      case MT_EXPLORE:
        $mission_result = flt_mission_explore($mission_data);
      break;

      case MT_RECYCLE:
        $mission_result = flt_mission_recycle($mission_data);
      break;

      case MT_COLONIZE:
        $mission_result = flt_mission_colonize($mission_data);
      break;

      case MT_SPY:
        $mission_result = flt_mission_spy($mission_data);
      break;

      case MT_MISSILE:  // Missiles !!
      break;

//      default:
//        doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
//      break;
    }


    sn_db_transaction_commit();

/*

    // Миссии должны возвращать измененные результаты, что бы второй раз не лезть в базу
    unset($mission_result);
    switch ($fleet_row['fleet_mission'])
    {

      default:
        doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
      break;
    }
// Подчищать массивы по результатам
    if($attack_result)
    {
      // Case for passed attack
      $attack_result = $attack_result['rw'][0];
//TODO: А вот здесь надо переписать соответствующую функцию
      flt_unset_by_attack($attack_result['attackers'], $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache);
      flt_unset_by_attack($attack_result['defenders'], $flt_user_cache, $flt_planet_cache, $flt_fleet_cache, $flt_event_cache);
      unset($attack_result);
      unset($flt_planet_cache[$fleet_event['dst_planet_hash']]);
    }
    else
    {
      // Unsetting data that we broken in mission handler
//TODO: А вот тут надо доставать данные - из кэша ли, из базы ли
      if(($mission_result & CACHE_FLEET) == CACHE_FLEET)
      {
        unset($flt_fleet_cache[$fleet_event['fleet_id']]);
      }
      if(($mission_result & CACHE_USER_SRC) == CACHE_USER_SRC)
      {
        unset($flt_user_cache[$fleet_event['src_user_id']]);
      }
      if(($mission_result & CACHE_USER_DST) == CACHE_USER_DST)
      {
        unset($flt_user_cache[$fleet_event['dst_user_id']]);
      }
      if(($mission_result & CACHE_PLANET_SRC) == CACHE_PLANET_SRC)
      {
        unset($flt_planet_cache[$fleet_event['src_planet_hash']]);
      }
      if(($mission_result & CACHE_PLANET_DST) == CACHE_PLANET_DST)
      {
        unset($flt_planet_cache[$fleet_event['dst_planet_hash']]);
      }
    }
*/
  }
  // doquery('COMMIT;');

//  if($flt_update_mode == 1)
//  {
//    $config->db_saveItem('flt_lastUpdate', 0);
//  }

}
