<?php

/**
 * sys_stat_functions.php
 *
 * @version 7 (c) copyright 2010-2014 by Gorlum for http://supernova.ws
 *   [!] Full rewrite
 * @version 6 (c) copyright 2010-2012 by Gorlum for http://supernova.ws
 *   [~] Made statistic non-transactional again to prevent locks
 * @version 5 (c) copyright 2010-2011 by Gorlum for http://supernova.ws
 *   [-] Rid of all procedures - they only called once per loop and make unneeded overhead
 *   [~] Some optimizations. +6% to script speed on test base
 *   [~] Wrapped update in transaction. Decreased script execution time by 98%
 * @version 4 (c) copyright 2010 by Gorlum for http://supernova.ws
 *   [~] Now setting time limit also update end time in the DB
 *   [+] Logging more information about update process to simplify error detection
 *   [+] Implemented locking mechanic that permits launches of more then one update process
 *   [+] Stat update logging statistic about it progress to DB
 * @version 3 (c) copyright 2010 by Gorlum for http://supernova.ws
 *   [+] Added resource stat
 * @version 2 (c) copyright 2010 by Gorlum for http://supernova.ws
 *   [!] Stats calculation greatly increased to 10x times (and even more in certain configurations)
 *   [!] Now all ranks for players/allies counting here - no unnecessary UPDATEs in /stats.php
 *   [*] All planet funcs merged into GetPlanetPoints
 *   [*] GetPlanetPoints optimized to utilize properties of geometrical progression
 *   [*] Planet-related calculations now made within one SQL-query for all planets - not per user
 *   [*] Fleet-On-Fly-related calculations now made within one SQL-query for all fleets - not per user
 *   [*] Greatly improved insertions of new Allie's stat by moving all calculation to single query
 *   [*] Greatly improved speed of updating Player's rank by moving every rank update to single query
 * StatFunctions.php @version 1 copyright 2008 by Chlorel for XNova
 */

function sta_set_time_limit($sta_update_msg = 'updating something', $next_step = true) {
  global $sta_update_step;

  $value = classSupernova::$config->stats_minimal_interval ? classSupernova::$config->stats_minimal_interval : 600;
  set_time_limit($value);
  classSupernova::$config->db_saveItem('var_stat_update_end', time() + $value);

  $sta_update_msg = db_escape($sta_update_msg);

  if($next_step) {
    $sta_update_step++;
  }
  $sta_update_msg = "Update in progress. Step {$sta_update_step}/14: {$sta_update_msg}.";

  classSupernova::$config->db_saveItem('var_stat_update_msg', $sta_update_msg);
  if($next_step) {
    classSupernova::$debug->warning($sta_update_msg, 'Stat update', LOG_INFO_STAT_PROCESS);
  }
}

function sys_stat_calculate_flush(&$data, $force = false) {
  if(count($data) < 25 && !$force) {
    return;
  }

  if(!empty($data)) {
    classSupernova::$gc->db->doReplaceValuesDeprecated(
      TABLE_STAT_POINTS,
      array(
        'id_owner',
        'id_ally',
        'stat_type',
        'stat_code',
        'tech_points',
        'tech_count',
        'build_points',
        'build_count',
        'defs_points',
        'defs_count',
        'fleet_points',
        'fleet_count',
        'res_points',
        'res_count',
        'total_points',
        'total_count',
        'stat_date',
      ),
      $data
    );

  }

  $data = array();
}

function sys_stat_calculate() {
  global $sta_update_step;

  ini_set('memory_limit', classSupernova::$config->stats_php_memory ? classSupernova::$config->stats_php_memory : '1024M');

  $user_skip_list = sys_stat_get_user_skip_list();

  $rate[RES_METAL] = classSupernova::$config->rpg_exchange_metal;
  $rate[RES_CRYSTAL] = classSupernova::$config->rpg_exchange_crystal / classSupernova::$config->rpg_exchange_metal;
  $rate[RES_DEUTERIUM] = classSupernova::$config->rpg_exchange_deuterium / classSupernova::$config->rpg_exchange_metal;
  $rate[RES_DARK_MATTER] = classSupernova::$config->rpg_exchange_darkMatter / classSupernova::$config->rpg_exchange_metal;

  $sta_update_step = -1;

  sta_set_time_limit('starting update');
  $counts = $points = $unit_cost_cache = $user_allies = array();


  sn_db_transaction_start();


  sta_set_time_limit('calculating players stats');
  $i = 0;
  // Блокируем всех пользователей
  classSupernova::$gc->cacheOperator->db_lock_tables('users');
  $user_list = DBStaticUser::db_user_list('', true, 'id, dark_matter, metal, crystal, deuterium, user_as_ally, ally_id');
  $row_num = count($user_list);
  foreach($user_list as $player) {
    if($i++ % 100 == 0) {
      sta_set_time_limit("calculating players stats (player {$i}/{$row_num})", false);
    }
    if(array_key_exists($user_id = $player['id'], $user_skip_list)) {
      continue;
    }

    $resources = $player['metal'] * $rate[RES_METAL] + $player['crystal'] * $rate[RES_CRYSTAL] +
      $player['deuterium'] * $rate[RES_DEUTERIUM] + $player['dark_matter'] * $rate[RES_DARK_MATTER];
    $counts[$user_id][UNIT_RESOURCES] += $resources;

    // А здесь мы фильтруем пользователей по $user_skip_list - далее не нужно этого делать, потому что
    if(!isset($user_skip_list[$user_id])) {
      $user_allies[$user_id] = $player['ally_id'];
    }
  }
  unset($user_list);
  classSupernova::$gc->snCache->cache_clear(LOC_USER, true);


  sta_set_time_limit('calculating planets stats');
  $i = 0;
  $query = DBStaticPlanet::db_planet_list_resources_by_owner();
  $row_num = classSupernova::$db->db_num_rows($query);
  while($planet = db_fetch($query)) {
    if($i++ % 100 == 0) {
      sta_set_time_limit("calculating planets stats (planet {$i}/{$row_num})", false);
    }
    if(array_key_exists($user_id = $planet['id_owner'], $user_skip_list)) {
      continue;
    }

    $resources = $planet['metal'] * $rate[RES_METAL] + $planet['crystal'] * $rate[RES_CRYSTAL] +
      $planet['deuterium'] * $rate[RES_DEUTERIUM];
    $counts[$user_id][UNIT_RESOURCES] += $resources;
  }

  // Calculation of Fleet-In-Flight
  sta_set_time_limit('calculating flying fleets stats');
  $i = 0;
  $query = FleetList::dbQueryAllId();
  $row_num = classSupernova::$db->db_num_rows($query);
  while($fleet_row = db_fetch($query)) {
    if($i++ % 100 == 0) {
      sta_set_time_limit("calculating flying fleets stats (fleet {$i}/{$row_num})", false);
    }
    $objFleet = new Fleet();
    // TODO - без дополнительной инициализации и перераспределений памяти на каждый new Fleet()/unset($fleet)
    // К тому же при включённом кэшировании это быстро забъёт кэш холодными данными
    $objFleet->dbRowParse($fleet_row);
    if(array_key_exists($user_id = $objFleet->playerOwnerId, $user_skip_list)) {
      continue;
    }

    foreach($objFleet->shipsIterator() as $unit_id => $unit) {
      $counts[$user_id][UNIT_SHIPS] += $unit->count;

      if(!isset($unit_cost_cache[$unit_id][0])) {
        $unit_cost_cache[$unit_id][0] = get_unit_param($unit_id, P_COST);
      }
      $unit_cost_data = &$unit_cost_cache[$unit_id][0];
      $points[$user_id][UNIT_SHIPS] += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * $unit->count;
    }

    $resources = $objFleet->resourcesGetTotalInMetal($rate);

    $counts[$user_id][UNIT_RESOURCES] += $resources;

    unset($objFleet);
  }


  sta_set_time_limit('calculating unit stats');
  $i = 0;
  $query = DBStaticUnit::db_unit_list_stat_calculate();
  $row_num = classSupernova::$db->db_num_rows($query);
  while($unit = db_fetch($query)) {
    if($i++ % 100 == 0) {
      sta_set_time_limit("calculating unit stats (unit {$i}/{$row_num})", false);
    }
    if(array_key_exists($user_id = $unit['unit_player_id'], $user_skip_list)) {
      continue;
    }

    $counts[$user_id][$unit['unit_type']] += $unit['unit_level'] * $unit['unit_amount'];
    $total_cost = eco_get_total_cost($unit['unit_snid'], $unit['unit_level']);
    $points[$user_id][$unit['unit_type']] += (isset($total_cost['total']) ? $total_cost['total'] : 0) * $unit['unit_amount'];
  }


  sta_set_time_limit('calculating ques stats');
  $i = 0;
  $query = DBStaticQue::db_que_list_stat();
  $row_num = classSupernova::$db->db_num_rows($query);
  while($que_item = db_fetch($query)) {
    if($i++ % 100 == 0) {
      sta_set_time_limit("calculating ques stats (que item {$i}/{$row_num})", false);
    }
    if(array_key_exists($user_id = $que_item['que_player_id'], $user_skip_list)) {
      continue;
    }
    $que_unit_amount = $que_item['que_unit_amount'];
    $que_item = sys_unit_str2arr($que_item['que_unit_price']);
    $resources = ($que_item[RES_METAL] * $rate[RES_METAL] + $que_item[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $que_item[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * $que_unit_amount;
    $counts[$user_id][UNIT_RESOURCES] += $resources;
  }

  sta_set_time_limit('archiving old statistic');
  // Statistic rotation
  $classConfig = classSupernova::$config;
  classSupernova::$db->doDeleteComplex("DELETE FROM {{statpoints}} WHERE `stat_date` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$classConfig->stats_history_days} DAY));");
  classSupernova::$db->doUpdate("UPDATE `{{statpoints}}` SET `stat_code` = `stat_code` + 1;");

  sta_set_time_limit('posting new user stats to DB');
  $data = array();
  foreach($user_allies as $user_id => $ally_id) {
    $points[$user_id][UNIT_RESOURCES] = $counts[$user_id][UNIT_RESOURCES] / 1000;
    $points[$user_id] = array_map('floor', $points[$user_id]);
    $counts[$user_id] = array_map('floor', $counts[$user_id]);

    $ally_id = $ally_id ? $ally_id : 'NULL';
    $user_defence_points = $points[$user_id][UNIT_DEFENCE] + $points[$user_id][UNIT_DEF_MISSILES];
    $user_defence_counts = $counts[$user_id][UNIT_DEFENCE] + $counts[$user_id][UNIT_DEF_MISSILES];
    $user_points = array_sum($points[$user_id]);
    $user_counts = array_sum($counts[$user_id]);

    $data[] = $q = "({$user_id},{$ally_id},1,1,'{$points[$user_id][UNIT_TECHNOLOGIES]}','{$counts[$user_id][UNIT_TECHNOLOGIES]}'," .
      "'{$points[$user_id][UNIT_STRUCTURES]}','{$counts[$user_id][UNIT_STRUCTURES]}','{$user_defence_points}','{$user_defence_counts}'," .
      "'{$points[$user_id][UNIT_SHIPS]}','{$counts[$user_id][UNIT_SHIPS]}','{$points[$user_id][UNIT_RESOURCES]}','{$counts[$user_id][UNIT_RESOURCES]}'," .
      "{$user_points},{$user_counts}," . SN_TIME_NOW . ")";

    sys_stat_calculate_flush($data);
  }
  sys_stat_calculate_flush($data, true);


  // Updating Allie's stats
  sta_set_time_limit('posting new Alliance stats to DB');
  classSupernova::$db->doInsertComplex(
    "INSERT INTO `{{statpoints}}`
      (`tech_points`, `tech_count`, `build_points`, `build_count`, `defs_points`, `defs_count`,
        `fleet_points`, `fleet_count`, `res_points`, `res_count`, `total_points`, `total_count`,
        `stat_date`, `id_owner`, `id_ally`, `stat_type`, `stat_code`,
        `tech_old_rank`, `build_old_rank`, `defs_old_rank`, `fleet_old_rank`, `res_old_rank`, `total_old_rank`
      )
      SELECT
        SUM(u.`tech_points`)+aus.`tech_points`, SUM(u.`tech_count`)+aus.`tech_count`, SUM(u.`build_points`)+aus.`build_points`, SUM(u.`build_count`)+aus.`build_count`,
        SUM(u.`defs_points`)+aus.`defs_points`, SUM(u.`defs_count`)+aus.`defs_count`, SUM(u.`fleet_points`)+aus.`fleet_points`, SUM(u.`fleet_count`)+aus.`fleet_count`,
        SUM(u.`res_points`)+aus.`res_points`, SUM(u.`res_count`)+aus.`res_count`, SUM(u.`total_points`)+aus.`total_points`, SUM(u.`total_count`)+aus.`total_count`,
        " . SN_TIME_NOW . ", NULL, u.`id_ally`, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.res_rank, a.total_rank
      FROM `{{statpoints}}` AS u
        JOIN `{{alliance}}` AS al ON al.id = u.id_ally
        LEFT JOIN `{{statpoints}}` AS aus ON aus.id_owner = al.ally_user_id AND aus.stat_type = 1 AND aus.stat_code = 1
        LEFT JOIN `{{statpoints}}` AS a ON a.id_ally = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
      WHERE u.`stat_type` = 1 AND u.stat_code = 1 AND u.id_ally<>0
      GROUP BY u.`id_ally`"
  );

  // Удаляем больше не нужные записи о достижении игрока-альянса
  db_stat_list_delete_ally_player();

  // Some variables we need to update ranks
  $qryResetRowNum = 'SET @rownum=0;';
  $qryFormat = "UPDATE `{{statpoints}}` SET `%1\$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = '%2\$d' AND `stat_code` = 1 ORDER BY `%1\$s_points` DESC, `id_owner` ASC, `id_ally` ASC;";

  $rankNames = array('tech', 'build', 'defs', 'fleet', 'res', 'total');

  // Updating player's ranks
  sta_set_time_limit("updating ranks for players");
  foreach($rankNames as $rankName) {
    sta_set_time_limit("updating player rank '{$rankName}'", false);
    classSupernova::$db->doExecute($qryResetRowNum);
    classSupernova::$db->doUpdate(sprintf($qryFormat, $rankName, 1));
  }

  sta_set_time_limit("updating ranks for Alliances");
  // Updating Allie's ranks
  foreach($rankNames as $rankName) {
    sta_set_time_limit("updating Alliances rank '{$rankName}'", false);
    classSupernova::$db->doExecute($qryResetRowNum);
    classSupernova::$db->doUpdate(sprintf($qryFormat, $rankName, 2));
  }

  sta_set_time_limit('setting previous user stats from archive');
  classSupernova::$db->doUpdate(
    "UPDATE `{{statpoints}}` AS new
      LEFT JOIN `{{statpoints}}` AS old ON old.id_owner = new.id_owner AND old.stat_code = 2 AND old.stat_type = new.stat_type
    SET
      new.tech_old_rank = old.tech_rank,
      new.build_old_rank = old.build_rank,
      new.defs_old_rank  = old.defs_rank ,
      new.fleet_old_rank = old.fleet_rank,
      new.res_old_rank = old.res_rank,
      new.total_old_rank = old.total_rank
    WHERE
      new.stat_type = 1 AND new.stat_code = 1;");

  sta_set_time_limit('setting previous allies stats from archive');
  classSupernova::$db->doUpdate(
    "UPDATE `{{statpoints}}` AS new
      LEFT JOIN `{{statpoints}}` AS old ON old.id_ally = new.id_ally AND old.stat_code = 2 AND old.stat_type = new.stat_type
    SET
      new.tech_old_rank = old.tech_rank,
      new.build_old_rank = old.build_rank,
      new.defs_old_rank  = old.defs_rank ,
      new.fleet_old_rank = old.fleet_rank,
      new.res_old_rank = old.res_rank,
      new.total_old_rank = old.total_rank
    WHERE
      new.stat_type = 2 AND new.stat_code = 1;");

  sta_set_time_limit('updating players current rank and points');
  db_stat_list_update_user_stats();

  sta_set_time_limit('updating Allys current rank and points');
  db_stat_list_update_ally_stats();

  // Counting real user count and updating values
  classSupernova::$config->db_saveItem('users_amount', DBStaticUser::db_user_count());

  sn_db_transaction_commit();
}
