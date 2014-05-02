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

function sta_set_time_limit($sta_update_msg = 'updating something', $next_step = true)
{
  global $config, $debug, $sta_update_step;

  $value = 60;
  set_time_limit($value);
  $config->db_saveItem('var_stat_update_end', time() + $value);

  $sta_update_msg = mysql_real_escape_string($sta_update_msg);

  if($next_step)
  {
    $sta_update_step++;
  }
  $sta_update_msg = "Update in progress. Step {$sta_update_step}/13: {$sta_update_msg}.";

  $config->db_saveItem('var_stat_update_msg', $sta_update_msg);
  if($next_step)
  {
    $debug->warning($sta_update_msg, 'Stat update', 191);
  }
}

function sys_stat_calculate_flush(&$data, $force = false)
{
  if(count($data) < 25 && !$force) return;

  doquery('REPLACE INTO {{statpoints}}
    (`id_owner`, `id_ally`, `stat_type`, `stat_code`, `tech_points`, `tech_count`, `build_points`, `build_count`,
     `defs_points`, `defs_count`, `fleet_points`, `fleet_count`, `res_points`, `res_count`, `total_points`, `total_count`, `stat_date`) VALUES ' . implode(',', $data));

  $data = array();
}

function sys_stat_calculate()
{
  global $config, $time_now, $sta_update_step;

  $user_skip_list = sys_stat_get_user_skip_list();

  // $sn_groups_resources_loot = sn_get_groups('resources_loot');
  $rate[RES_METAL] = $config->rpg_exchange_metal;
  $rate[RES_CRYSTAL] = $config->rpg_exchange_crystal / $config->rpg_exchange_metal;
  $rate[RES_DEUTERIUM] = $config->rpg_exchange_deuterium / $config->rpg_exchange_metal;
  $rate[RES_DARK_MATTER] = $config->rpg_exchange_darkMatter / $config->rpg_exchange_metal;

  $sta_update_step = -1;

  sta_set_time_limit('starting update');
  $counts = $points = $unit_cost_cache = $users = array();


  sn_db_transaction_start();


  sta_set_time_limit('calculating players stats');
  $i = 0;
  // Блокируем всех пользователей
  $query = doquery("SELECT id, dark_matter, metal, crystal, deuterium, user_as_ally, ally_id FROM  `{{users}}` FOR UPDATE");
  $row_num = mysql_num_rows($query);
  while($player = mysql_fetch_assoc($query))
  {
    if($i++ % 100 == 0) sta_set_time_limit("calculating players stats (player {$i}/{$row_num})", false);
    if(array_key_exists($user_id = $player['id'], $user_skip_list)) continue;

    $resources = $player['metal'] * $rate[RES_METAL] + $player['crystal'] * $rate[RES_CRYSTAL] + $player['deuterium'] * $rate[RES_DEUTERIUM] + $player['dark_matter'] * $rate[RES_DARK_MATTER];
    $counts[$user_id][UNIT_RESOURCES] += $resources;
    // $points[$user_id][UNIT_RESOURCES] += $resources;

    // А здесь мы фильтруем пользователей по $user_skip_list - далее не нужно этого делать, потому что
    if(!isset($user_skip_list[$user_id]))
      $users[$user_id] = $player;
  }


  sta_set_time_limit('calculating planets stats');
  $i = 0;
  $query = doquery("SELECT `id_owner`, sum(metal) AS metal, sum(crystal) AS crystal, sum(deuterium) AS deuterium FROM {{planets}} WHERE id_owner <> 0 /*AND id_owner is not null*/ GROUP BY id_owner;");
  $row_num = mysql_num_rows($query);
  while($planet = mysql_fetch_assoc($query))
  {
    if($i++ % 100 == 0) sta_set_time_limit("calculating planets stats (planet {$i}/{$row_num})", false);
    if(array_key_exists($user_id = $planet['id_owner'], $user_skip_list)) continue;

    $resources = $planet['metal'] * $rate[RES_METAL] + $planet['crystal'] * $rate[RES_CRYSTAL] + $planet['deuterium'] * $rate[RES_DEUTERIUM];
    $counts[$user_id][UNIT_RESOURCES] += $resources;
    // $points[$user_id][UNIT_RESOURCES] += $resources;
  }


  // Calculation of Fleet-In-Flight
  sta_set_time_limit('calculating flying fleets stats');
  $i = 0;
  $query = doquery("SELECT fleet_owner, fleet_array, fleet_resource_metal, fleet_resource_crystal, fleet_resource_deuterium FROM {{fleets}};");
  $row_num = mysql_num_rows($query);
  while($fleet_row = mysql_fetch_assoc($query))
  {
    if($i++ % 100 == 0) sta_set_time_limit("calculating flying fleets stats (fleet {$i}/{$row_num})", false);
    if(array_key_exists($user_id = $fleet_row['fleet_owner'], $user_skip_list)) continue;

    $fleet = sys_unit_str2arr($fleet_row['fleet_array']);
    foreach($fleet as $unit_id => $unit_amount)
    {
      $counts[$user_id][UNIT_SHIPS] += $unit_amount;

      if(!isset($unit_cost_cache[$unit_id][0]))
      {
        $unit_cost_cache[$unit_id][0] = get_unit_param($unit_id, P_COST);
      }
      $unit_cost_data = &$unit_cost_cache[$unit_id][0];
      $points[$user_id][UNIT_SHIPS] += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * $unit_amount;
    }
    $resources = $fleet_row['fleet_resource_metal'] * $rate[RES_METAL] + $fleet_row['fleet_resource_crystal'] * $rate[RES_CRYSTAL] + $fleet_row['fleet_resource_deuterium'] * $rate[RES_DEUTERIUM];

    $counts[$user_id][UNIT_RESOURCES] += $resources;
    // $points[$user_id][UNIT_RESOURCES] += $resources;
  }


  sta_set_time_limit('calculating unit stats');
  $i = 0;
  $query = doquery(
   "SELECT
      unit_player_id, unit_type, unit_snid, unit_level, count(*) AS unit_amount
    FROM
      `{{unit}}`
    WHERE
      (unit_time_start IS NULL OR unit_time_start >= NOW()) AND (unit_time_finish IS NULL OR unit_time_finish <= NOW())
      AND unit_level > 0
    GROUP BY
	    unit_player_id, unit_type, unit_snid, unit_level;");
  $row_num = mysql_num_rows($query);
  while($unit = mysql_fetch_assoc($query))
  {
    if($i++ % 100 == 0) sta_set_time_limit("calculating unit stats (unit {$i}/{$row_num})", false);
    if(array_key_exists($user_id = $unit['unit_player_id'], $user_skip_list)) continue;

    $counts[$user_id][$unit['unit_type']] += $unit['unit_level'] * $unit['unit_amount'];
    $total_cost = eco_get_total_cost($unit['unit_snid'], $unit['unit_level']);
    $points[$user_id][$unit['unit_type']] += $total_cost['total'] * $unit['unit_amount'];
  }


  sta_set_time_limit('calculating ques stats');
  $i = 0;
  $query = doquery("SELECT que_player_id, sum(que_unit_amount) AS que_unit_amount, que_unit_price FROM `lh_que` GROUP BY que_player_id, que_unit_price;");
  $row_num = mysql_num_rows($query);
  while($que_item = mysql_fetch_assoc($query))
  {
    if($i++ % 100 == 0) sta_set_time_limit("calculating ques stats (que item {$i}/{$row_num})", false);
    if(array_key_exists($user_id = $que_item['id_owner'], $user_skip_list)) continue;

    $que_unit_amount = $que_item['que_unit_amount'];
    $que_item = sys_unit_str2arr($que_item['que_unit_price']);
    $resources = ($que_item['metal'] * $rate[RES_METAL] + $que_item['crystal'] * $rate[RES_CRYSTAL] + $que_item['deuterium'] * $rate[RES_DEUTERIUM]) * $que_unit_amount;
    $counts[$user_id][UNIT_RESOURCES] += $resources;
    // $points[$user_id][UNIT_RESOURCES] += $resources;
  }


  sta_set_time_limit('archiving old statistic');
  // Statistic rotation
  doquery("DELETE FROM {{statpoints}} WHERE `stat_code` >= 10;");
  doquery("UPDATE {{statpoints}} SET `stat_code` = `stat_code` + 1;");

  sta_set_time_limit('posting new user stats to DB');
  $data = array();

  foreach($users as $user_id => $player_data)
  {
    /*
    if(!$player_data['user_as_ally'])
    {
      continue;
    }
    pdump($player_data);
    */

    // $counts[UNIT_RESOURCES] дублирует $points[UNIT_RESOURCES], поэтому $points не заполняем, а берем $counts и делим на 1000
    $points[$user_id][UNIT_RESOURCES] = $counts[$user_id][UNIT_RESOURCES] / 1000;
    $points[$user_id] = array_map('floor', $points[$user_id]);
    $counts[$user_id] = array_map('floor', $counts[$user_id]);

    $ally_id = $player_data['ally_id'] ? $player_data['ally_id'] : 'NULL';
    $user_defence_points = $points[$user_id][UNIT_DEFENCE] + $points[$user_id][UNIT_DEF_MISSILES];
    $user_defence_counts = $counts[$user_id][UNIT_DEFENCE] + $counts[$user_id][UNIT_DEF_MISSILES];
    $user_points = array_sum($points[$user_id]);
    $user_counts = array_sum($counts[$user_id]);

    $data[] = "({$user_id},{$ally_id},1,1,'{$points[$user_id][UNIT_TECHNOLOGIES]}','{$counts[$user_id][UNIT_TECHNOLOGIES]}',
      '{$points[$user_id][UNIT_STRUCTURES]}','{$counts[$user_id][UNIT_STRUCTURES]}','{$user_defence_points}','{$user_defence_counts}',
      '{$points[$user_id][UNIT_SHIPS]}','{$counts[$user_id][UNIT_SHIPS]}','{$points[$user_id][UNIT_RESOURCES]}','{$counts[$user_id][UNIT_RESOURCES]}',
      {$user_points},{$user_counts},{$time_now})";

    sys_stat_calculate_flush($data);
  }
  sys_stat_calculate_flush($data, true);


  // Updating Allie's stats
  sta_set_time_limit('posting new Alliance stats to DB');
  doquery(
    "INSERT INTO {{statpoints}}
      (`tech_points`, `tech_count`, `build_points`, `build_count`, `defs_points`, `defs_count`,
        `fleet_points`, `fleet_count`, `res_points`, `res_count`, `total_points`, `total_count`,
        `stat_date`, `id_owner`, `id_ally`, `stat_type`, `stat_code`,
        `tech_old_rank`, `build_old_rank`, `defs_old_rank`, `fleet_old_rank`, `res_old_rank`, `total_old_rank`
      )
      SELECT
        SUM(u.`tech_points`)+aus.`tech_points`, SUM(u.`tech_count`)+aus.`tech_count`, SUM(u.`build_points`)+aus.`build_points`, SUM(u.`build_count`)+aus.`build_count`,
        SUM(u.`defs_points`)+aus.`defs_points`, SUM(u.`defs_count`)+aus.`defs_count`, SUM(u.`fleet_points`)+aus.`fleet_points`, SUM(u.`fleet_count`)+aus.`fleet_count`,
        SUM(u.`res_points`)+aus.`res_points`, SUM(u.`res_count`)+aus.`res_count`, SUM(u.`total_points`)+aus.`total_points`, SUM(u.`total_count`)+aus.`total_count`,
        {$time_now}, NULL, u.`id_ally`, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.res_rank, a.total_rank
      FROM {{statpoints}} as u
        join {{alliance}} as al on al.id = u.id_ally
        left join {{statpoints}} as aus on aus.id_owner = al.ally_user_id and aus.stat_type = 1 AND aus.stat_code = 1
        LEFT JOIN {{statpoints}} as a ON a.id_ally = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
      WHERE u.`stat_type` = 1 AND u.stat_code = 1 AND u.id_ally<>0
      GROUP BY u.`id_ally`"
  );

  // Удаляем больше не нужные записи о достижении игрока-альянса
  // doquery('DELETE {{statpoints}} FROM {{statpoints}} JOIN {{users}} ON id = id_owner AND user_as_ally IS NOT NULL WHERE id_ally IS NULL');
  doquery('DELETE s FROM {{statpoints}} AS s JOIN {{users}} AS u ON u.id = s.id_owner WHERE s.id_ally IS NULL AND u.user_as_ally IS NOT NULL');

  /*
  SUM(u.`tech_points`), SUM(u.`tech_count`), SUM(u.`build_points`), SUM(u.`build_count`), SUM(u.`defs_points`),
        SUM(u.`defs_count`), SUM(u.`fleet_points`), SUM(u.`fleet_count`), SUM(u.`res_points`), SUM(u.`res_count`),
        SUM(u.`total_points`), SUM(u.`total_count`),
  */

  // Some variables we need to update ranks
  $qryResetRowNum = 'SET @rownum=0;';
  $qryFormat = 'UPDATE {{statpoints}} SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 ORDER BY `%1$s_points` DESC, `id_owner` ASC, `id_ally` ASC;';

  $rankNames = array('tech', 'build', 'defs', 'fleet', 'res', 'total');

  // Updating player's ranks
  sta_set_time_limit("updating ranks for players");
  foreach($rankNames as $rankName)
  {
    sta_set_time_limit("updating player rank '{$rankName}'", false);
    doquery($qryResetRowNum);
    doquery(sprintf($qryFormat, $rankName, 1));
  }

  sta_set_time_limit("updating ranks for Alliances");
  // --- Updating Allie's ranks
  foreach($rankNames as $rankName)
  {
    sta_set_time_limit("updating Alliances rank '{$rankName}'", false);
    doquery($qryResetRowNum);
    doquery(sprintf($qryFormat, $rankName, 2));
  }

  sta_set_time_limit('setting previous user stats from archive');
  doquery(
    "UPDATE {{statpoints}} as new
      LEFT JOIN {{statpoints}} as old ON old.id_owner = new.id_owner AND old.stat_code = 2 AND old.stat_type = new.stat_type
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
  doquery(
    "UPDATE {{statpoints}} as new
      LEFT JOIN {{statpoints}} as old ON old.id_ally = new.id_ally AND old.stat_code = 2 AND old.stat_type = new.stat_type
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
  doquery("UPDATE {{users}} AS u JOIN {{statpoints}} AS sp ON sp.id_owner = u.id AND sp.stat_code = 1 AND sp.stat_type = 1 SET u.total_rank = sp.total_rank, u.total_points = sp.total_points WHERE user_as_ally IS NULL;");

  sta_set_time_limit('updating Allys current rank and points');
  doquery("UPDATE {{alliance}} AS a JOIN {{statpoints}} AS sp ON sp.id_ally = a.id AND sp.stat_code = 1 AND sp.stat_type = 2 SET a.total_rank = sp.total_rank, a.total_points = sp.total_points;");

  // Counting real user count and updating values
  $userCount = doquery("SELECT COUNT(*) AS users_online FROM {{users}} WHERE user_as_ally IS NULL;", '', true);
  $config->db_saveItem('users_amount', $userCount['users_online']);

  sn_db_transaction_commit();

//  pdump($points);
  return;
/*
  die();



  sta_set_time_limit('calculating planet stats');

  $UsrPlanets = doquery("SELECT * FROM {{planets}};");
  $row_num = mysql_num_rows($UsrPlanets);
  $i = 0;
  while($planet_row = mysql_fetch_assoc($UsrPlanets))
  {
    if($i % 100 == 0)
    {
      sta_set_time_limit("calculating planets stats (planet {$i}/{$row_num})", false);
    }
    $i++;

    if(array_key_exists($user_id = $planet_row['id_owner'], $user_skip_list))
    {
      continue;
    }

    $planet_points = 0;

    $point_counter = $amount_counter = 0;
    foreach(sn_get_groups('structures') as $unit_id)
    {
      $unit_level = $planet_row[get_unit_param($unit_id, P_NAME)];
      if($unit_level > 0)
      {
        $unit_cost_data = get_unit_param($unit_id, P_COST);
        $f = $unit_cost_data['factor'];
        $point_counter += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * (pow($f, $unit_level) - $f) / ($f - 1);
        $amount_counter += $unit_level;
      }
    }
    $points[$user_id]['structures'] += $point_counter / 1000;
    $planet_points += $point_counter;
    $counts[$user_id]['structures'] += $amount_counter;

    $point_counter = $amount_counter = 0;
    foreach(sn_get_groups('defense') as $unit_id)
    {
      $unit_amount = $planet_row[get_unit_param($unit_id, P_NAME)];
      if($unit_amount > 0)
      {
        $unit_cost_data = get_unit_param($unit_id, P_COST);
        $point_counter += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * $unit_amount;
        $amount_counter += $unit_amount;
      }
    }
    $points[$user_id]['defs'] += $point_counter / 1000;
    $planet_points += $point_counter;
    $counts[$user_id]['defs'] += $amount_counter;

    $point_counter = $amount_counter = 0;
    foreach(sn_get_groups('fleet') as $unit_id)
    {
      $unit_amount = $planet_row[get_unit_param($unit_id, P_NAME)];
      if($unit_amount > 0)
      {
        $unit_cost_data = get_unit_param($unit_id, P_COST);
        $point_counter += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * $unit_amount;
        $amount_counter += $unit_amount;
      }
    }
    $points[$user_id]['fleet'] += $point_counter / 1000;
    $counts[$user_id]['fleet'] += $amount_counter;

    $point_counter = 0;
    foreach($sn_groups_resources_loot as $resource_name)
    {
      $point_counter += ($resource_amount = $planet_row[get_unit_param($resource_name, P_NAME)]) > 0 ? $resource_amount : 0;
    }

    if($planet_row['b_hangar_id'])
    {
//      $ship_list = flt_expand(array('fleet_array' => $planet_row['b_hangar_id']));
      $ship_list = sys_unit_str2arr($planet_row['b_hangar_id']);
      foreach($ship_list as $ship_id => $ship_amount)
      {
        $data = get_unit_param($ship_id, P_COST);
        $point_counter += ($data[RES_METAL] * $rate[RES_METAL] + $data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * $ship_amount;
      }
    }
    // TODO: Also calculate cost of structures and research in ques
    $points[$user_id]['resources'] += $point_counter / 1000;
    $counts[$user_id]['resources'] += $point_counter;

//  Disabled planet point update. Didn't see any use for it
//    $planet_points = floor($planet_points / 1000);
//    doquery("UPDATE {{planets}} SET `points` = '{$planet_points}' WHERE `id` = '{$planet_row['id']}';");
  }

  sta_set_time_limit('calculating tech points');
  $user_techs = doquery("SELECT un.* FROM {{unit}} AS un INNER JOIN {{users}} AS us ON us.id = un.unit_player_id AND us.user_as_ally IS NULL");
  while($tech_row = mysql_fetch_assoc($user_techs))
  {
    if(array_key_exists($user_id = $tech_row['unit_player_id'], $user_skip_list))
    {
      continue;
    }

    if(!in_array($unit_id = $tech_row['unit_snid'], sn_get_groups('tech')))
    {
      continue;
    }

    $TechCounts = 0;
    $TechPoints = 0;
    $unit_level = $tech_row['unit_level'];
    if($unit_level > 0)
    {
      $unit_cost_data = get_unit_param($unit_id, P_COST);
      $f = $unit_cost_data['factor'];
      $TechPoints += ($unit_cost_data[RES_METAL] * $rate[RES_METAL] + $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] + $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]) * (pow($f, $unit_level) - $f) / ($f - 1);
      $TechCounts += $unit_level;
    }
    $points[$user_id]['tech'] += $TechPoints / 1000;
    $counts[$user_id]['tech'] += $TechCounts;
  }
//  pdump($points);die();
















  sta_set_time_limit('posting new user stats to DB');
  $GameUsers = doquery("SELECT * FROM {{users}} where user_as_ally IS NULL;");
  while($user_row = mysql_fetch_assoc($GameUsers))
  {
    $user_id = $user_row['id'];

    if(array_key_exists($user_id = $user_row['id'], $user_skip_list))
    {
      continue;
    }
    $points[$user_id] = array_map('floor', $points[$user_id]);

    $GPoints = array_sum($points[$user_id]);
    $GCount = array_sum($counts[$user_id]);

    $QryInsertStats = "INSERT INTO {{statpoints}} SET ";
    $QryInsertStats .= "`id_owner` = '" . $user_id . "', ";
    $QryInsertStats .= "`id_ally` = " . ($user_row['ally_id'] ? $user_row['ally_id'] : 'NULL') . ", ";
    $QryInsertStats .= "`stat_type` = '1', "; // 1 pour joueur , 2 pour alliance
    $QryInsertStats .= "`stat_code` = '1', ";
    $QryInsertStats .= "`tech_points` = '" . $points[$user_id]['tech'] . "', ";
    $QryInsertStats .= "`tech_count` = '" . $counts[$user_id]['tech'] . "', ";
    $QryInsertStats .= "`build_points` = '" . $points[$user_id]['structures'] . "', ";
    $QryInsertStats .= "`build_count` = '" . $counts[$user_id]['structures'] . "', ";
    $QryInsertStats .= "`defs_points` = '" . $points[$user_id]['defs'] . "', ";
    $QryInsertStats .= "`defs_count` = '" . $counts[$user_id]['defs'] . "', ";
    $QryInsertStats .= "`fleet_points` = '" . $points[$user_id]['fleet'] . "', ";
    $QryInsertStats .= "`fleet_count` = '" . $counts[$user_id]['fleet'] . "', ";
    $QryInsertStats .= "`res_points` = '" . $points[$user_id]['resources'] . "', ";
    $QryInsertStats .= "`res_count` = '" . $counts[$user_id]['resources'] . "', ";
    $QryInsertStats .= "`total_points` = '{$GPoints}', ";
    $QryInsertStats .= "`total_count` = '{$GCount}', ";
    $QryInsertStats .= "`stat_date` = '{$time_now}';";
    doquery($QryInsertStats);
  }

  sta_set_time_limit('setting previous user stats from archive');
  doquery("
    UPDATE {{statpoints}} as new
      LEFT JOIN {{statpoints}} as old ON old.id_owner = new.id_owner AND old.stat_code = 2 AND old.stat_type = 1
    SET
      new.tech_old_rank = old.tech_rank,
      new.build_old_rank = old.build_rank,
      new.defs_old_rank  = old.defs_rank ,
      new.fleet_old_rank = old.fleet_rank,
      new.res_old_rank = old.res_rank,
      new.total_old_rank = old.total_rank
    WHERE
      new.stat_type = 1 AND new.stat_code = 1;
  ");

  // Some variables we need to update ranks
  $qryResetRowNum = 'SET @rownum=0;';
  $qryFormat = 'UPDATE {{statpoints}} SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 ORDER BY `%1$s_points` DESC, `id_owner` ASC, `id_ally` ASC;';
  $rankNames = array('tech', 'build', 'defs', 'fleet', 'res', 'total');

  // Updating player's ranks
  sta_set_time_limit("updating ranks for players");
  foreach($rankNames as $rankName)
  {
    sta_set_time_limit("updating player rank '{$rankName}'", false);
    doquery($qryResetRowNum);
    doquery(sprintf($qryFormat, $rankName, 1));
  }


  // TODO Статы альянса состаят из суммы статов Альянса и суммы статов его игроков - т.е. суммируем по таблице, а затем удаляем всё нафиг
  // Updating Allie's stats
  sta_set_time_limit('posting new Alliance stats to DB');
  $QryInsertStats = "
    INSERT INTO {{statpoints}}
      (`tech_points`, `tech_count`, `build_points`, `build_count`, `defs_points`, `defs_count`,
        `fleet_points`, `fleet_count`, `res_points`, `res_count`, `total_points`, `total_count`,
        `stat_date`, `id_owner`, `id_ally`, `stat_type`, `stat_code`,
        `tech_old_rank`, `build_old_rank`, `defs_old_rank`, `fleet_old_rank`, `res_old_rank`, `total_old_rank`
      )
      SELECT
        SUM(u.`tech_points`), SUM(u.`tech_count`), SUM(u.`build_points`), SUM(u.`build_count`), SUM(u.`defs_points`),
        SUM(u.`defs_count`), SUM(u.`fleet_points`), SUM(u.`fleet_count`), SUM(u.`res_points`), SUM(u.`res_count`),
        SUM(u.`total_points`), SUM(u.`total_count`),
        {$time_now}, NULL, u.`id_ally`, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.res_rank, a.total_rank
      FROM {{statpoints}} as u
        LEFT JOIN {{statpoints}} as a ON a.id_ally = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
      WHERE u.`stat_type` = 1 AND u.stat_code = 1 AND u.id_ally<>0
      GROUP BY u.`id_ally`";
  doquery($QryInsertStats);

  sta_set_time_limit("updating ranks for Alliances");
  // --- Updating Allie's ranks
  foreach($rankNames as $rankName)
  {
    sta_set_time_limit("updating Alliances rank '{$rankName}'", false);
    doquery($qryResetRowNum);
    doquery(sprintf($qryFormat, $rankName, 2));
  }

  sta_set_time_limit('updating player\'s current rank and points');
  doquery("UPDATE {{users}} AS u JOIN {{statpoints}} AS sp ON sp.id_owner = u.id AND sp.stat_code = 1 AND sp.stat_type = 1 SET u.total_rank = sp.total_rank, u.total_points = sp.total_points WHERE user_as_ally IS NULL;");

  sta_set_time_limit('updating Ally\'s current rank and points');
  doquery("UPDATE {{alliance}} AS a JOIN {{statpoints}} AS sp ON sp.id_ally = a.id AND sp.stat_code = 1 AND sp.stat_type = 2 SET a.total_rank = sp.total_rank, a.total_points = sp.total_points;");

  // Counting real user count and updating values
  $userCount = doquery("SELECT COUNT(*) AS users_online FROM {{users}} WHERE user_as_ally IS NULL;", '', true);
  $config->db_saveItem('users_amount', $userCount['users_online']);

  sn_db_transaction_commit();*/
}
