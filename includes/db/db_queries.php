<?php

require_once('db_helpers.php');

function db_planet_list_admin_list($table_parent_columns, $planet_active, $active_time, $planet_type) {
  return classSupernova::$db->doSelect(
    "SELECT p.*, u.username" . ($table_parent_columns ? ', p1.name AS parent_name' : '') .
    " FROM {{planets}} AS p
      LEFT JOIN {{users}} AS u ON u.id = p.id_owner" .
    ($table_parent_columns ? ' LEFT JOIN {{planets}} AS p1 ON p1.id = p.parent_planet' : '') .
    " WHERE " . ($planet_active ? "p.last_update >= {$active_time}" : "p.planet_type = {$planet_type}"));
}

function db_planet_list_search($searchtext) {
  return classSupernova::$db->doSelect(
    "SELECT
      p.galaxy, p.system, p.planet, p.planet_type, p.name as planet_name,
      u.id as uid, u.username, u.ally_id, u.id_planet,
      u.total_points, u.total_rank,
      u.ally_tag, u.ally_name
    FROM
      {{planets}} AS p
      LEFT JOIN {{users}} AS u ON u.id = p.id_owner
    WHERE
      name LIKE '%{$searchtext}%' AND u.user_as_ally IS NULL
    ORDER BY
      ally_tag, username, planet_name
    LIMIT 30;"
  );
}


function db_user_list_search($searchtext) {
  return classSupernova::$db->doSelect(
    "SELECT
      pn.player_name, u.id as uid, u.username, u.ally_id, u.id_planet, u.total_points, u.total_rank,
      p.galaxy, p.system, p.planet, p.planet_type, p.name as planet_name,
      u.ally_tag, u.ally_name
    FROM
      {{player_name_history}} AS pn
      JOIN {{users}} AS u ON u.id = pn.player_id
      LEFT JOIN {{planets}} AS p ON p.id_owner = u.id AND p.id=u.id_planet
    WHERE
      player_name LIKE '%{$searchtext}%' AND u.user_as_ally IS NULL
    ORDER BY
      ally_tag, username, planet_name
    LIMIT 30;"
  );
}


function db_unit_records_sum($unit_id, $user_skip_list_unit) {
  return classSupernova::$db->doSelectFetch(
    "SELECT unit_player_id, username, sum(unit_level) as unit_level
          FROM {{unit}} JOIN {{users}} AS u ON u.id = unit_player_id
          WHERE unit_player_id != 0 AND unit_snid = {$unit_id} {$user_skip_list_unit}
          GROUP BY unit_player_id
          ORDER BY sum(unit_level) DESC, unit_player_id
          LIMIT 1;");
}

function db_unit_records_plain($unit_id, $user_skip_list_unit) {
  return classSupernova::$db->doSelectFetch(
    "SELECT unit_player_id, username, unit_level
          FROM {{unit}} JOIN {{users}} AS u ON u.id = unit_player_id
          WHERE unit_player_id != 0 AND unit_snid = {$unit_id} {$user_skip_list_unit}
          ORDER BY unit_level DESC, unit_id
          LIMIT 1;");
}

function db_stat_list_statistic($who, $is_common_stat, $Rank, $start, $source = false) {
// pdump($source);
  if (!$source) {
    $source = array(
      'statpoints' => 'statpoints',
      'users'      => 'users',
      'id'         => 'id',
      'username'   => 'username',

      'alliance' => 'alliance',

    );
  } else {
    $source = array(
      'statpoints' => 'blitz_statpoints',
      'users'      => 'blitz_registrations',
      'id'         => 'blitz_player_id',
      'username'   => 'blitz_name',

      'alliance' => 'blitz_alliance', // TODO
    );
  }
// pdump($source);
  if ($who == 1) {
    if ($is_common_stat) { // , UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
      $query_str =
        "SELECT
      @rownum:=@rownum+1 rownum, subject.{$source['id']} as `id`, sp.{$Rank}_rank as rank, sp.{$Rank}_old_rank as rank_old, sp.{$Rank}_points as points, subject.{$source['username']} as `name`, subject.*
    FROM
      (SELECT @rownum:={$start}) r,
      {{{$source['statpoints']}}} as sp
      LEFT JOIN {{{$source['users']}}} AS subject ON subject.{$source['id']} = sp.id_owner
      LEFT JOIN {{{$source['statpoints']}}} AS sp_old ON sp_old.id_owner = subject.{$source['id']} AND sp_old.`stat_type` = 1 AND sp_old.`stat_code` = 2
    WHERE
      sp.`stat_type` = 1 AND sp.`stat_code` = 1
    ORDER BY
      sp.`{$Rank}_rank`, subject.{$source['id']}
    LIMIT
      " . $start . ",100;";
    } else { // , UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
      $query_str =
        "SELECT
      @rownum:=@rownum+1 AS rank, subject.{$source['id']} as `id`, @rownum as rank_old, subject.{$Rank} as points, subject.{$source['username']} as name, subject.*
    FROM
      (SELECT @rownum:={$start}) r,
      {{{$source['users']}}} AS subject
    WHERE
      subject.user_as_ally is null
    ORDER BY
      subject.{$Rank} DESC, subject.{$source['id']}
    LIMIT
      " . $start . ",100;";
    }
  } else {
    // TODO
    $query_str =
      "SELECT
    @rownum:=@rownum+1 as rownum, subject.id as `id`, sp.{$Rank}_rank as rank, sp.{$Rank}_old_rank as rank_old, sp.{$Rank}_points as points, subject.ally_name as name, subject.ally_tag, subject.ally_members
  FROM
    (SELECT @rownum:={$start}) r,
    {{{$source['statpoints']}}} AS sp
    LEFT JOIN {{{$source['alliance']}}} AS subject ON subject.id = sp.id_ally
    LEFT JOIN {{{$source['statpoints']}}} AS sp_old ON sp_old.id_ally = subject.id AND sp_old.`stat_type` = 2 AND sp_old.`stat_code` = 2
  WHERE
    sp.`stat_type` = 2 AND sp.`stat_code` = 1
  ORDER BY
    sp.`{$Rank}_rank`, subject.id
  LIMIT
    " . $start . ",100;";
  }

  return classSupernova::$db->doSelect($query_str);
}


function db_stat_list_update_ally_stats() {
  return ;
}

function db_stat_list_delete_ally_player() {
  return classSupernova::$db->doDeleteSql(
    'DELETE s 
    FROM `{{statpoints}}` AS s 
      JOIN `{{users}}` AS u ON u.id = s.id_owner 
    WHERE 
      s.id_ally IS NULL 
      AND u.user_as_ally IS NOT NULL'
  );
}


function db_referrals_list_by_id($user_id) {
  return classSupernova::$db->doSelect("SELECT r.*, u.username, u.register_time FROM {{referrals}} AS r LEFT JOIN {{users}} AS u ON u.id = r.id WHERE id_partner = {$user_id}");
}


/**
 * Хелпер для работы с простыми хэш-таблицами в БД
 *
 * @param string $current_value_unsafe
 * @param string $db_id_field_name
 * @param string $db_table_name
 * @param string $db_value_field_name
 *
 * @return int
 */
// OK v4
// TODO - вынести в отдельный класс
function db_get_set_unique_id_value($current_value_unsafe, $db_id_field_name, $db_table_name, $db_value_field_name) {
  $current_value_safe = db_escape($current_value_unsafe);
  $value_id = classSupernova::$db->doSelectFetch("SELECT `{$db_id_field_name}` FROM {{{$db_table_name}}} WHERE `{$db_value_field_name}` = '{$current_value_safe}' LIMIT 1 FOR UPDATE");
  if (empty($value_id[$db_id_field_name])) {
    classSupernova::$db->doInsertSet($db_table_name, array(
      $db_value_field_name => $current_value_unsafe,
    ));

    $variable_id = classSupernova::$db->db_insert_id();
  } else {
    $variable_id = $value_id[$db_id_field_name];
  }

  return $variable_id;
}

/**
 * Функция проверяет наличие имени игрока в базе
 *
 * @param $player_name_unsafe
 *
 * @return bool
 */
function db_player_name_exists($player_name_unsafe) {
  sn_db_transaction_check(true);

  $player_name_safe = classSupernova::$db->db_escape($player_name_unsafe);

  $player_name_exists = classSupernova::$db->doSelectFetch(
    "SELECT * 
    FROM `{{player_name_history}}` 
    WHERE `player_name` = '{$player_name_safe}' 
    LIMIT 1 
    FOR UPDATE"
  );

  return !empty($player_name_exists);
}

/**
 * @param        $userId
 * @param string $username_unsafe
 */
function db_player_name_history_replace($userId, $username_unsafe) {
  classSupernova::$gc->db->doReplaceSet('player_name_history', array(
    'player_id'   => $userId,
    'player_name' => $username_unsafe,
  ));
}


/**
 * @param $username_safe
 *
 * @return array|bool|mysqli_result|null
 */
function db_player_name_history_get_name_by_name($username_safe) {
  $name_check = classSupernova::$db->doSelectFetch("SELECT * FROM {{player_name_history}} WHERE `player_name` LIKE \"{$username_safe}\" LIMIT 1 FOR UPDATE;");

  return $name_check;
}


// BANNED *************************************************************************************************************
function db_banned_list_select() {
  return classSupernova::$db->doSelect("SELECT * FROM `{{banned}}` ORDER BY `ban_id` DESC;");
}

/**
 * @param $user_row
 *
 * @return array|bool|mysqli_result|null
 */
function db_ban_list_get_details($user_row) {
  $ban_details = classSupernova::$db->doSelectFetch("SELECT * FROM {{banned}} WHERE `ban_user_id` = {$user_row['id']} ORDER BY ban_id DESC LIMIT 1");

  return $ban_details;
}


// BLITZ ***************************************************************************************************************
function db_blitz_reg_insert($userId, $current_round) {
  classSupernova::$db->doInsertSet(TABLE_BLITZ_REGISTRATIONS, array(
    'user_id'      => $userId,
    'round_number' => $current_round,
  ), DB_INSERT_IGNORE);
}

function db_blitz_reg_get_id_by_player_and_round($user, $current_round) {
  return classSupernova::$db->doSelectFetch("SELECT `id` FROM `{{blitz_registrations}}` WHERE `user_id` = {$user['id']} AND `round_number` = {$current_round} FOR UPDATE;");
}

function db_blitz_reg_count($current_round) {
  return classSupernova::$db->doSelectFetch("SELECT count(`id`) AS `count` FROM {{blitz_registrations}} WHERE `round_number` = {$current_round};");
}

function db_blitz_reg_get_random_id($current_round) {
  return classSupernova::$db->doSelect("SELECT `id` FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY RAND();");
}

function db_blitz_reg_get_player_list($current_round) {
  return classSupernova::$db->doSelect("SELECT blitz_name, blitz_password, blitz_online FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY `id`;");
}

function db_blitz_reg_get_player_list_order_by_place($current_round) {
  return classSupernova::$db->doSelect("SELECT * FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY `blitz_place` FOR UPDATE;");
}

function db_blitz_reg_get_player_list_and_users($current_round) {
  return classSupernova::$db->doSelect(
    "SELECT u.*, br.blitz_name, br.blitz_password, br.blitz_place, br.blitz_status, br.blitz_points, br.blitz_reward_dark_matter
    FROM {{blitz_registrations}} AS br
    JOIN {{users}} AS u ON u.id = br.user_id
  WHERE br.`round_number` = {$current_round}
  order by `blitz_place`, `timestamp`;");
}

function db_blitz_reg_update_with_name_and_password($blitz_name_unsafe, $blitz_password_unsafe, $rowId, $current_round) {
  classSupernova::$db->doUpdateTableSet(
    TABLE_BLITZ_REGISTRATIONS,
    array(
      'blitz_name'     => $blitz_name_unsafe,
      'blitz_password' => $blitz_password_unsafe,
    ),
    array(
      'id'           => $rowId,
      'round_number' => $current_round,
    )
  );
}

function db_blitz_reg_update_apply_results($reward, $row, $current_round) {
  $dbQuery = \DBAL\DbQuery::build(classSupernova::$db)
    ->setTable(TABLE_BLITZ_REGISTRATIONS)
    ->setAdjust(array(
      'blitz_reward_dark_matter' => $reward,
    ))
    ->setWhereArray(array(
      'id'           => $row['id'],
      'round_number' => $current_round,
    ));
  classSupernova::$db->doUpdateDbQueryAdjust($dbQuery);
}

function db_blitz_reg_update_results($current_round, $blitz_name_unsafe, $blitz_player_id, $blitz_online, $blitz_place, $blitz_points) {
  classSupernova::$db->doUpdateTableSet(
    TABLE_BLITZ_REGISTRATIONS,
    array(
      'blitz_player_id' => $blitz_player_id,
      'blitz_online'    => $blitz_online,
      'blitz_place'     => $blitz_place,
      'blitz_points'    => $blitz_points,
    ),
    array(
      'blitz_name'   => $blitz_name_unsafe,
      'round_number' => $current_round,
    )
  );
}

function db_blitz_reg_delete($userId, $current_round) {
  classSupernova::$gc->db->doDeleteWhere(TABLE_BLITZ_REGISTRATIONS, array('user_id' => $userId, 'round_number' => $current_round));
}


// Universe *************************************************************************************************************
function db_universe_get_name($uni_galaxy, $uni_system = 0) {
  $db_row = classSupernova::$db->doSelectFetch("select `universe_name` from `{{universe}}` where `universe_galaxy` = {$uni_galaxy} and `universe_system` = {$uni_system} limit 1;");

  return $db_row['universe_name'];
}

/**
 * @param $uni_galaxy
 * @param $uni_system
 *
 * @return array|bool|mysqli_result|null
 */
function db_universe_get($uni_galaxy, $uni_system) {
  $uni_row = classSupernova::$db->doSelectFetch("select * from `{{universe}}` where `universe_galaxy` = {$uni_galaxy} and `universe_system` = {$uni_system} limit 1;");

  return $uni_row;
}

/**
 * @param $uni_galaxy
 * @param $uni_system
 * @param $uni_row
 */
function db_universe_rename($uni_galaxy, $uni_system, $uni_row) {
  classSupernova::$db->doReplaceSet('universe', array(
    'universe_galaxy' => $uni_galaxy,
    'universe_system' => $uni_system,
    'universe_name' => $uni_row['universe_name'],
    'universe_price' => $uni_row['universe_price'],
  ));

}


// Payment *************************************************************************************************************

function db_payment_get($payment_id) {
  return classSupernova::$db->doSelectFetch("SELECT * FROM {{payment}} WHERE `payment_id` = {$payment_id} LIMIT 1;");
}

/**
 * @param $flt_payer
 * @param $flt_status
 * @param $flt_test
 * @param $flt_module
 *
 * @return array|bool|mysqli_result|null
 */
function db_payment_list_get($flt_payer, $flt_status, $flt_test, $flt_module) {
  $extra_conditions =
    ($flt_payer > 0 ? "AND payment_user_id = {$flt_payer} " : '') .
    ($flt_status >= 0 ? "AND payment_status = {$flt_status} " : '') .
    ($flt_test >= 0 ? "AND payment_test = {$flt_test} " : '') .
    ($flt_module ? "AND payment_module_name = '{$flt_module}' " : '');
  $query = classSupernova::$db->doSelect("SELECT * FROM `{{payment}}` WHERE 1 {$extra_conditions} ORDER BY payment_id DESC;");

  return $query;
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_payment_list_payers() {
  $query = classSupernova::$db->doSelect("SELECT payment_user_id, payment_user_name FROM `{{payment}}` GROUP BY payment_user_id ORDER BY payment_user_name");

  return $query;
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_payment_list_modules() {
  $query = classSupernova::$db->doSelect("SELECT DISTINCT payment_module_name FROM `{{payment}}` ORDER BY payment_module_name");

  return $query;
}


// Log Online *************************************************************************************************************
function db_log_online_insert() {
  classSupernova::$db->doInsertSet(TABLE_LOG_USERS_ONLINE, array(
    'online_count' => (int)classSupernova::$config->var_online_user_count,
  ), DB_INSERT_IGNORE);
}

// Log *************************************************************************************************************

/**
 * @return array|bool|mysqli_result|null
 */
function db_log_list_get_last_100() {
  $query = classSupernova::$db->doSelect("SELECT * FROM `{{logs}}` ORDER BY log_id DESC LIMIT 100;");

  return $query;
}

/**
 * @param $delete
 */
function db_log_delete_by_id($delete) {
  classSupernova::$gc->db->doDeleteRow(TABLE_LOGS, array('log_id' => $delete));
}

function db_log_delete_update_and_stat_calc() {
  classSupernova::$db->doDeleteSql(
    'DELETE FROM `{{logs}}` WHERE `log_code` IN ('
    . LOG_INFO_DB_CHANGE . ', '
    . LOG_INFO_MAINTENANCE . ', '
    . LOG_INFO_STAT_PROCESS .
    ')'
  );
}

/**
 * @param $detail
 *
 * @return array|bool|mysqli_result|null
 */
function db_log_get_by_id($detail) {
  $errorInfo = classSupernova::$db->doSelectFetch("SELECT * FROM `{{logs}}` WHERE `log_id` = {$detail} LIMIT 1;");

  return $errorInfo;
}

/**
 * @param $i
 *
 * @return array|bool|mysqli_result|null
 */
function db_log_count($i) {
  $query = classSupernova::$db->doSelectFetch("SELECT COUNT(*) AS LOG_MESSAGES_TOTAL, {$i} AS LOG_MESSAGES_VISIBLE FROM `{{logs}}`;");

  return $query;
}

// SYSTEM QUERIES - MOVE TO DB *****************************************************************************************
/**
 * @return array|bool|mysqli_result|null
 */
function db_core_show_status() {
  $result = classSupernova::$db->doSql('SHOW STATUS;');

  return $result;
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_counter_list_by_week() {
  $query = classSupernova::$db->doSelect("SELECT `visit_time`, user_id FROM `{{counter}}` WHERE user_id <> 0 AND visit_time > UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY)) ORDER BY user_id, visit_time;");

  return $query;
}

/**
 * @param $user_last_browser_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_browser_agent_get_by_id($user_last_browser_id) {
  $temp = classSupernova::$db->doSelectFetch("SELECT browser_user_agent FROM {{security_browser}} WHERE `browser_id` = {$user_last_browser_id}");

  return $temp['browser_user_agent'];
}


/**
 * @param $user_id
 * @param $change_type
 * @param $dark_matter
 * @param $comment_unsafe
 * @param $rowUserNameUnsafe
 * @param $page_url_unsafe
 */
function db_log_dark_matter_insert($user_id, $change_type, $dark_matter, $comment_unsafe, $rowUserNameUnsafe, $page_url_unsafe) {
  return classSupernova::$db->doInsertSet(TABLE_LOG_DARK_MATTER, array(
    'log_dark_matter_username' => $rowUserNameUnsafe,
    'log_dark_matter_reason'   => (int)$change_type,
    'log_dark_matter_amount'   => (float)$dark_matter,
    'log_dark_matter_comment'  => (string)$comment_unsafe,
    'log_dark_matter_page'     => (string)$page_url_unsafe,
    'log_dark_matter_sender'   => $user_id,
  ));
}

// REFERRALS ***********************************************************************************************************
/**
 * @param $user_id_safe
 *
 * @return array|bool|mysqli_result|null
 */
function db_referral_get_by_id($user_id_safe) {
  $old_referral = classSupernova::$db->doSelectFetch("SELECT * FROM {{referrals}} WHERE `id` = {$user_id_safe} LIMIT 1 FOR UPDATE;");

  return $old_referral;
}

/**
 * @param $user_id_safe
 * @param $dark_matter
 */
function db_referral_update_dm($user_id_safe, $dark_matter) {
  classSupernova::$db->doUpdateRowAdjust(
    TABLE_REFERRALS,
    array(),
    array(
      'dark_matter' => $dark_matter,
    ),
    array(
      'id' => $user_id_safe,
    )
  );
}


/**
 * @param $partnerId
 * @param $userId
 */
function db_referral_insert($partnerId, $userId) {
  classSupernova::$db->doInsertSet(TABLE_REFERRALS, array(
    'id'         => $userId,
    'id_partner' => $partnerId,
  ));
}


// Quests ***********************************************************************************************************
/**
 * @param $query_add_select
 * @param $query_add_from
 * @param $query_add_where
 *
 * @return array|bool|mysqli_result|null
 */
function db_quest_list_get($query_add_select, $query_add_from, $query_add_where) {
  $query = classSupernova::$db->doSelect(
    "SELECT q.* {$query_add_select}
      FROM {{quest}} AS q {$query_add_from}
      WHERE 1 {$query_add_where}
    ;"
  );

  return $query;
}


/**
 * @return array|bool|mysqli_result|null
 */
function db_quest_count() {
  $query = classSupernova::$db->doSelectFetch("SELECT count(*) AS count FROM `{{quest}}`;");

  return $query;
}

/**
 * @param $quest_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_quest_get($quest_id) {
  $quest = classSupernova::$db->doSelectFetch("SELECT * FROM {{quest}} WHERE `quest_id` = {$quest_id} LIMIT 1;");

  return $quest;
}

/**
 * @param $quest_id
 */
function db_quest_delete($quest_id) {
  classSupernova::$gc->db->doDeleteRow(TABLE_QUEST, array('quest_id' => $quest_id));
}

/**
 * @param $quest_name_unsafe
 * @param $quest_type
 * @param $quest_description_unsafe
 * @param $quest_conditions
 * @param $quest_rewards
 * @param $quest_id
 */
function db_quest_update($quest_name_unsafe, $quest_type, $quest_description_unsafe, $quest_conditions, $quest_rewards, $quest_id) {
  classSupernova::$db->doUpdateRowSet(
    TABLE_QUEST,
    array(
      'quest_name'        => $quest_name_unsafe,
      'quest_type'        => $quest_type,
      'quest_description' => $quest_description_unsafe,
      'quest_conditions'  => $quest_conditions,
      'quest_rewards'     => $quest_rewards,
    ),
    array(
      'quest_id' => $quest_id,
    )
  );
}

/**
 * @param $user_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_stat_get_by_user($user_id) {
  $StatRecord = classSupernova::$db->doSelectFetch("SELECT * FROM {{statpoints}} WHERE `stat_type` = 1 AND `stat_code` = 1 AND `id_owner` = {$user_id};");

  return $StatRecord;
}

/**
 * @param $user_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_stat_get_by_user2($user_id) {
  $query = classSupernova::$db->doSelect("SELECT * FROM {{statpoints}} WHERE `stat_type` = 1 AND `id_owner` = {$user_id} ORDER BY `stat_code` DESC;");

  return $query;
}

/**
 * @param $options
 *
 * @return array|bool|mysqli_result|null
 */
function db_payment_get_something($options) {
  $payment = classSupernova::$db->doSelectFetch("SELECT * FROM {{payment}} WHERE `payment_module_name` = '{$this->manifest['name']}' AND `payment_external_id` = '{$options['payment_external_id']}' LIMIT 1 FOR UPDATE;");

  return $payment;
}

/**
 * @param $payment_external_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_payment_get_something2($payment_external_id) {
  $payment = classSupernova::$db->doSelectFetch("SELECT * FROM {{payment}} WHERE `payment_module_name` = '{$this->manifest['name']}' AND `payment_external_id` = '{$payment_external_id}' LIMIT 1 FOR UPDATE;");

  return $payment;
}


/**
 * @return array|bool|mysqli_result|null
 */
function db_ube_report_get_best_battles() {
  $query = classSupernova::$db->doSelect("SELECT *
      FROM `{{ube_report}}`
      WHERE `ube_report_time_process` <  DATE(DATE_SUB(NOW(), INTERVAL " . MODULE_INFO_BEST_BATTLES_LOCK_DAYS . " DAY))
      ORDER BY `ube_report_debris_total_in_metal` DESC, `ube_report_id` ASC
      LIMIT " . MODULE_INFO_BEST_BATTLES_REPORT_VIEW . ";");

  return $query;
}

function db_config_get_stockman_fleet() {
  classSupernova::$db->doSelect("SELECT * FROM `{{config}}` WHERE `config_name` = 'eco_stockman_fleet' LIMIT 1 FOR UPDATE;");
}


/**
 * @param $payment_id
 * @param $payment_status
 * @param $comment_unsafe
 */
function db_payment_update($payment_id, $payment_status, $comment_unsafe) {
  classSupernova::$db->doUpdateRowSet(
    TABLE_PAYMENT,
    array(
      'payment_status'  => $payment_status,
      'payment_comment' => $comment_unsafe,
    ),
    array(
      'payment_id' => $payment_id,
    )
  );
}
