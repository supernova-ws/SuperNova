<?php
/**
 * Created by Gorlum 24.09.2017 18:09
 */

use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;
use Que\DBStaticQue;
use Unit\DBStaticUnit;

/**
 * Class StatCalculator
 *
 * Updates player's stats
 */
class StatCalculator {

  /**
   * @var int
   */
  public static $memoryStart = 0;
  /**
   * @var float
   */
  public static $timeLastOperation = 0.0;

  public static function sta_set_time_limit($sta_update_msg = 'updating something', $next_step = true) {
    global $config, $debug, $sta_update_step;

    $value = $config->stats_minimal_interval ? $config->stats_minimal_interval : STATS_RUN_INTERVAL_MINIMUM;
    set_time_limit($value);
    $config->pass()->var_stat_update_end = time() + $value;

    $sta_update_msg = db_escape($sta_update_msg);

    if ($next_step) {
      $sta_update_step++;
    }

    $nowMicro = microtime(true);
    $sta_update_msg = "Update in progress. Step {$sta_update_step}/14: {$sta_update_msg}.\r\nMemory usage: "
      . number_format(memory_get_usage(true) - static::$memoryStart)
      . "\r\nPrevious operation time: " . number_format($nowMicro - static::$timeLastOperation, 5);

    $config->pass()->var_stat_update_msg = $sta_update_msg;
    if ($next_step) {
      static::$timeLastOperation = $nowMicro;
      $debug->warning($sta_update_msg, 'Stat update', LOG_INFO_STAT_PROCESS);
    }
  }

  public static function sys_stat_calculate_flush(&$data, $force = false) {
    if (count($data) < 25 && !$force) {
      return;
    }

    if (!empty($data)) {
      doquery('REPLACE INTO `{{statpoints}}`
      (`id_owner`, `id_ally`, `stat_type`, `stat_code`, `tech_points`, `tech_count`, `build_points`, `build_count`,
       `defs_points`, `defs_count`, `fleet_points`, `fleet_count`, `res_points`, `res_count`, `total_points`,
       `total_count`, `stat_date`) VALUES ' . implode(',', $data)
      );
    }

    $data = array();
  }


  public static function sys_stat_calculate() {
    global $config, $sta_update_step;

    ini_set('memory_limit', $config->stats_php_memory ? $config->stats_php_memory : '1G');

    static::$memoryStart = memory_get_usage(true);
    static::$timeLastOperation = microtime(true);

    $user_skip_list = sys_stat_get_user_skip_list();

    // $sn_groups_resources_loot = sn_get_groups('resources_loot');
    $rate[RES_METAL] = $config->rpg_exchange_metal;
    $rate[RES_CRYSTAL] = $config->rpg_exchange_crystal / $config->rpg_exchange_metal;
    $rate[RES_DEUTERIUM] = $config->rpg_exchange_deuterium / $config->rpg_exchange_metal;
    $rate[RES_DARK_MATTER] = $config->rpg_exchange_darkMatter / $config->rpg_exchange_metal;

    $sta_update_step = -1;

    static::sta_set_time_limit('starting update');
    $counts = $points = $unit_cost_cache = $user_allies = array();


    static::sta_set_time_limit('calculating players stats');

    sn_db_transaction_start();
    $i = 0;
    // Блокируем всех пользователей
    SN::db_lock_tables('users');
    $user_list = db_user_list('', true, 'id, dark_matter, metal, crystal, deuterium, user_as_ally, ally_id');
    $row_num = count($user_list);
    // while($player = db_fetch($query))
    foreach ($user_list as $player) {
      if ($i++ % 100 == 0) {
        static::sta_set_time_limit("calculating players stats (player {$i}/{$row_num})", false);
      }
      if (array_key_exists($user_id = $player['id'], $user_skip_list)) {
        continue;
      }

      $resources =
        $player['metal'] * $rate[RES_METAL]
        + $player['crystal'] * $rate[RES_CRYSTAL]
        + $player['deuterium'] * $rate[RES_DEUTERIUM]
        + $player['dark_matter'] * $rate[RES_DARK_MATTER];
      ;
      $counts[$user_id][UNIT_RESOURCES] += $resources;
      // $points[$user_id][UNIT_RESOURCES] += $resources;

      // А здесь мы фильтруем пользователей по $user_skip_list - далее не нужно этого делать, потому что
      if (!isset($user_skip_list[$user_id])) {
        $user_allies[$user_id] = $player['ally_id'];
      }
    }
    unset($user_list);
    _SnCacheInternal::cache_clear(LOC_USER, true);

    static::sta_set_time_limit('calculating planets stats');
    $i = 0;
    $query = DBStaticPlanet::db_planet_list_resources_by_owner();
    $row_num = db_num_rows($query);
    while ($planet = db_fetch($query)) {
      if ($i++ % 100 == 0) {
        static::sta_set_time_limit("calculating planets stats (planet {$i}/{$row_num})", false);
      }
      if (array_key_exists($user_id = $planet['id_owner'], $user_skip_list)) {
        continue;
      }

      $resources =
        $planet['metal'] * $rate[RES_METAL] +
        $planet['crystal'] * $rate[RES_CRYSTAL] +
        $planet['deuterium'] * $rate[RES_DEUTERIUM];
      $counts[$user_id][UNIT_RESOURCES] += $resources;
    }

    // Calculation of Fleet-In-Flight
    static::sta_set_time_limit('calculating flying fleets stats');
    $i = 0;
    $query = DbFleetStatic::db_fleet_list_query_all_stat();
    $row_num = db_num_rows($query);
    while ($fleet_row = db_fetch($query)) {
      if ($i++ % 100 == 0) {
        static::sta_set_time_limit("calculating flying fleets stats (fleet {$i}/{$row_num})", false);
      }
      if (array_key_exists($user_id = $fleet_row['fleet_owner'], $user_skip_list)) {
        continue;
      }

      $fleet = sys_unit_str2arr($fleet_row['fleet_array']);
      foreach ($fleet as $unit_id => $unit_amount) {
        $counts[$user_id][UNIT_SHIPS] += $unit_amount;

        if (!isset($unit_cost_cache[$unit_id][0])) {
          $unit_cost_cache[$unit_id][0] = get_unit_param($unit_id, P_COST);
        }
        $unit_cost_data = &$unit_cost_cache[$unit_id][0];
        $points[$user_id][UNIT_SHIPS] += (
            $unit_cost_data[RES_METAL] * $rate[RES_METAL] +
            $unit_cost_data[RES_CRYSTAL] * $rate[RES_CRYSTAL] +
            $unit_cost_data[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]
          ) * $unit_amount;
      }
      $resources =
        $fleet_row['fleet_resource_metal'] * $rate[RES_METAL] +
        $fleet_row['fleet_resource_crystal'] * $rate[RES_CRYSTAL] +
        $fleet_row['fleet_resource_deuterium'] * $rate[RES_DEUTERIUM];

      $counts[$user_id][UNIT_RESOURCES] += $resources;
    }

    static::sta_set_time_limit('calculating ques stats');
    $i = 0;
    $query = DBStaticQue::db_que_list_stat();
    $row_num = db_num_rows($query);
    while ($que_item = db_fetch($query)) {
      if ($i++ % 100 == 0) {
        static::sta_set_time_limit("calculating ques stats (que item {$i}/{$row_num})", false);
      }
      if (array_key_exists($user_id = $que_item['que_player_id'], $user_skip_list)) {
        continue;
      }
      $que_unit_amount = $que_item['que_unit_amount'];
      $que_item = sys_unit_str2arr($que_item['que_unit_price']);
      $resources = (
          $que_item[RES_METAL] * $rate[RES_METAL] +
          $que_item[RES_CRYSTAL] * $rate[RES_CRYSTAL] +
          $que_item[RES_DEUTERIUM] * $rate[RES_DEUTERIUM]
        ) * $que_unit_amount;
      $counts[$user_id][UNIT_RESOURCES] += $resources;
    }

    static::sta_set_time_limit('calculating unit stats');
    $i = 0;
    $query = DBStaticUnit::db_unit_list_stat_calculate();
    $row_num = db_num_rows($query);
    while ($unit = db_fetch($query)) {
      if ($i++ % 100 == 0) {
        static::sta_set_time_limit("calculating unit stats (unit {$i}/{$row_num})", false);
      }
      if (array_key_exists($user_id = $unit['unit_player_id'], $user_skip_list)) {
        continue;
      }

      $counts[$user_id][$unit['unit_type']] += $unit['unit_level'] * $unit['unit_amount'];
      $total_cost = eco_get_total_cost($unit['unit_snid'], $unit['unit_level']);
      $points[$user_id][$unit['unit_type']] += (isset($total_cost['total']) ? $total_cost['total'] : 0) * $unit['unit_amount'];
    }

    static::sta_set_time_limit('archiving old statistic');
    // Statistic rotation
    // doquery("DELETE FROM {{statpoints}} WHERE `stat_code` >= 14;");
    doquery("DELETE FROM `{{statpoints}}` WHERE `stat_date` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$config->stats_history_days} DAY));");
    doquery("UPDATE `{{statpoints}}` SET `stat_code` = `stat_code` + 1;");

    static::sta_set_time_limit('posting new user stats to DB');
    $data = array();
    foreach ($user_allies as $user_id => $ally_id) {
      // $counts[UNIT_RESOURCES] дублирует $points[UNIT_RESOURCES], поэтому $points не заполняем, а берем $counts и делим на 1000
      $points[$user_id][UNIT_RESOURCES] = $counts[$user_id][UNIT_RESOURCES] / 1000;
      $points[$user_id] = array_map('floor', $points[$user_id]);
      $counts[$user_id] = array_map('floor', $counts[$user_id]);

      $ally_id = $ally_id ? $ally_id : 'NULL';
      $user_defence_points = 0 + $points[$user_id][UNIT_DEFENCE] + $points[$user_id][UNIT_DEF_MISSILES];
      $user_defence_counts = 0 + $counts[$user_id][UNIT_DEFENCE] + $counts[$user_id][UNIT_DEF_MISSILES];
      $user_points = array_sum($points[$user_id]);
      $user_counts = array_sum($counts[$user_id]);

      !isset($points[$user_id][UNIT_TECHNOLOGIES]) ? $points[$user_id][UNIT_TECHNOLOGIES] = 0 : false;
      !isset($counts[$user_id][UNIT_TECHNOLOGIES]) ? $counts[$user_id][UNIT_TECHNOLOGIES] = 0 : false;
      !isset($points[$user_id][UNIT_STRUCTURES]) ? $points[$user_id][UNIT_STRUCTURES] = 0 : false;
      !isset($counts[$user_id][UNIT_STRUCTURES]) ? $counts[$user_id][UNIT_STRUCTURES] = 0 : false;
      !isset($points[$user_id][UNIT_SHIPS]) ? $points[$user_id][UNIT_SHIPS] = 0 : false;
      !isset($counts[$user_id][UNIT_SHIPS]) ? $counts[$user_id][UNIT_SHIPS] = 0 : false;
      !isset($points[$user_id][UNIT_RESOURCES]) ? $points[$user_id][UNIT_RESOURCES] = 0 : false;
      !isset($counts[$user_id][UNIT_RESOURCES]) ? $counts[$user_id][UNIT_RESOURCES] = 0 : false;

      $data[] = "({$user_id},{$ally_id},1,1,'{$points[$user_id][UNIT_TECHNOLOGIES]}','{$counts[$user_id][UNIT_TECHNOLOGIES]}'," .
        "'{$points[$user_id][UNIT_STRUCTURES]}','{$counts[$user_id][UNIT_STRUCTURES]}','{$user_defence_points}','{$user_defence_counts}'," .
        "'{$points[$user_id][UNIT_SHIPS]}','{$counts[$user_id][UNIT_SHIPS]}','{$points[$user_id][UNIT_RESOURCES]}','{$counts[$user_id][UNIT_RESOURCES]}'," .
        "{$user_points},{$user_counts}," . SN_TIME_NOW . ")";

      static::sys_stat_calculate_flush($data);
    }
    static::sys_stat_calculate_flush($data, true);


    // Updating Allie's stats
    static::sta_set_time_limit('posting new Alliance stats to DB');
    doquery(
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
    $qryFormat = 'UPDATE `{{statpoints}}` SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = "%2$d" AND `stat_code` = 1 ORDER BY `%1$s_points` DESC, `id_owner` ASC, `id_ally` ASC;';

    $rankNames = array('tech', 'build', 'defs', 'fleet', 'res', 'total');

    // Updating player's ranks
    static::sta_set_time_limit("updating ranks for players");
    foreach ($rankNames as $rankName) {
      static::sta_set_time_limit("updating player rank '{$rankName}'", false);
      doquery($qryResetRowNum);
      doquery(sprintf($qryFormat, $rankName, 1));
    }

    static::sta_set_time_limit("updating ranks for Alliances");
    // --- Updating Allie's ranks
    foreach ($rankNames as $rankName) {
      static::sta_set_time_limit("updating Alliances rank '{$rankName}'", false);
      doquery($qryResetRowNum);
      doquery(sprintf($qryFormat, $rankName, 2));
    }

    static::sta_set_time_limit('setting previous user stats from archive');
    doquery(
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

    static::sta_set_time_limit('setting previous allies stats from archive');
    doquery(
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

    static::sta_set_time_limit('updating players current rank and points');
    doquery("UPDATE `{{users}}` AS u JOIN `{{statpoints}}` AS sp ON sp.id_owner = u.id AND sp.stat_code = 1 AND sp.stat_type = 1 SET u.total_rank = sp.total_rank, u.total_points = sp.total_points WHERE user_as_ally IS NULL;");

    static::sta_set_time_limit('updating Allys current rank and points');
    doquery("UPDATE `{{alliance}}` AS a JOIN `{{statpoints}}` AS sp ON sp.id_ally = a.id AND sp.stat_code = 1 AND sp.stat_type = 2 SET a.total_rank = sp.total_rank, a.total_points = sp.total_points;");

    // Counting real user count and updating values
    dbUpdateUsersCount(db_user_count());

    sn_db_transaction_commit();
  }

}
