<?php

require_once('db_helpers.php');

require_once('db_queries_account.php');
require_once('db_queries_users.php');
require_once('db_queries_planets.php');
require_once('db_queries_unit.php');
require_once('db_queries_que.php');
require_once('db_queries_fleet.php');
require_once('db_queries_news_and_surveys.php');
require_once('db_queries_buddy.php');
require_once('db_queries_notes.php');
require_once('db_queries_ally.php');
require_once('db_queries_chat.php');


function db_planet_list_admin_list($table_parent_columns, $planet_active, $active_time, $planet_type) {
  return doquery(
    "SELECT p.*, u.username" . ($table_parent_columns ? ', p1.name AS parent_name' : '') .
    " FROM {{planets}} AS p
      LEFT JOIN {{users}} AS u ON u.id = p.id_owner" .
    ($table_parent_columns ? ' LEFT JOIN {{planets}} AS p1 ON p1.id = p.parent_planet' : '') .
    " WHERE " . ($planet_active ? "p.last_update >= {$active_time}" : "p.planet_type = {$planet_type}"));
}

function db_planet_list_search($searchtext) {
  return doquery(
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
  return doquery(
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
  return doquery(
    "SELECT unit_player_id, username, sum(unit_level) as unit_level
          FROM {{unit}} JOIN {{users}} AS u ON u.id = unit_player_id
          WHERE unit_player_id != 0 AND unit_snid = {$unit_id} {$user_skip_list_unit}
          GROUP BY unit_player_id
          ORDER BY sum(unit_level) DESC, unit_player_id
          LIMIT 1;"
    , true);
}

function db_unit_records_plain($unit_id, $user_skip_list_unit) {
  return doquery(
    "SELECT unit_player_id, username, unit_level
          FROM {{unit}} JOIN {{users}} AS u ON u.id = unit_player_id
          WHERE unit_player_id != 0 AND unit_snid = {$unit_id} {$user_skip_list_unit}
          ORDER BY unit_level DESC, unit_id
          LIMIT 1;"
    , true);
}

function db_stat_list_statistic($who, $is_common_stat, $Rank, $start, $source = false) {
// pdump($source);
  if(!$source) {
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
  if($who == 1) {
    if($is_common_stat) { // , UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
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

  return doquery($query_str);
}


function db_stat_list_update_user_stats() {
  return doquery("UPDATE `{{users}}` AS u JOIN `{{statpoints}}` AS sp ON sp.id_owner = u.id AND sp.stat_code = 1 AND sp.stat_type = 1 SET u.total_rank = sp.total_rank, u.total_points = sp.total_points WHERE user_as_ally IS NULL;");
}

function db_stat_list_update_ally_stats() {
  return doquery("UPDATE `{{alliance}}` AS a JOIN `{{statpoints}}` AS sp ON sp.id_ally = a.id AND sp.stat_code = 1 AND sp.stat_type = 2 SET a.total_rank = sp.total_rank, a.total_points = sp.total_points;");
}

function db_stat_list_delete_ally_player() {
  return doquery('DELETE s FROM `{{statpoints}}` AS s JOIN `{{users}}` AS u ON u.id = s.id_owner WHERE s.id_ally IS NULL AND u.user_as_ally IS NOT NULL');
}


function db_referrals_list_by_id($user_id) {
  return doquery("SELECT r.*, u.username, u.register_time FROM {{referrals}} AS r LEFT JOIN {{users}} AS u ON u.id = r.id WHERE id_partner = {$user_id}");
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
  $value_id = doquery("SELECT `{$db_id_field_name}` AS id_field FROM {{{$db_table_name}}} WHERE `{$db_value_field_name}` = '{$current_value_safe}' LIMIT 1 FOR UPDATE", true);
  if(!isset($value_id['id_field']) || !$value_id['id_field']) {
    doquery("INSERT INTO {{{$db_table_name}}} (`{$db_value_field_name}`) VALUES ('{$current_value_safe}');");
    $variable_id = db_insert_id();
  } else {
    $variable_id = $value_id['id_field'];
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

  $player_name_exists = classSupernova::$db->doquery("SELECT * FROM `{{player_name_history}}` WHERE `player_name` = '{$player_name_safe}' LIMIT 1 FOR UPDATE", true);

  return !empty($player_name_exists);
}


// ANNONCE *************************************************************************************************************
function db_ANNONCE_insert_set($users, $metalvendre, $cristalvendre, $deutvendre, $metalsouhait, $cristalsouhait, $deutsouhait) {
  return doquery("INSERT INTO {{annonce}} SET 
    `user` ='{$users['username']}', `galaxie` ='{$users['galaxy']}', `systeme` ='{$users['system']}', 
    `metala` ='{$metalvendre}', `cristala` ='{$cristalvendre}', `deuta` ='{$deutvendre}', `metals` ='{$metalsouhait}', `cristals` ='{$cristalsouhait}', `deuts` ='{$deutsouhait}'");
}

function db_ANNONCE_delete_by_id($GET_id) {
  return doquery("DELETE FROM `{{annonce}}` WHERE `id` = {$GET_id}");
}

function db_ANNONCE_LIST_select_all() {
  return doquery("SELECT * FROM `{{annonce}}` ORDER BY `id` DESC");
}


// BANNED *************************************************************************************************************
function db_banned_list_select() {
  return doquery("SELECT * FROM `{{banned}}` ORDER BY `ban_id` DESC;");
}

/**
 * @param $user_row
 *
 * @return array|bool|mysqli_result|null
 */
function db_ban_list_get_details($user_row) {
  $ban_details = doquery("SELECT * FROM {{banned}} WHERE `ban_user_id` = {$user_row['id']} ORDER BY ban_id DESC LIMIT 1", true);

  return $ban_details;
}


// BLITZ ***************************************************************************************************************
function db_blitz_reg_insert($user, $current_round) {
  doquery("INSERT IGNORE INTO {{blitz_registrations}} SET `user_id` = {$user['id']}, `round_number` = {$current_round};");
}

function db_blitz_reg_get_id_by_player_and_round($user, $current_round) {
  return doquery("SELECT `id` FROM `{{blitz_registrations}}` WHERE `user_id` = {$user['id']} AND `round_number` = {$current_round} FOR UPDATE;", true);
}

function db_blitz_reg_count($current_round) {
  return doquery("SELECT count(`id`) AS `count` FROM {{blitz_registrations}} WHERE `round_number` = {$current_round};", true);
}

function db_blitz_reg_get_random_id($current_round) {
  return doquery("SELECT `id` FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY RAND();");
}

function db_blitz_reg_get_player_list($current_round) {
  return doquery("SELECT blitz_name, blitz_password, blitz_online FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY `id`;");
}

function db_blitz_reg_get_player_list_order_by_place($current_round) {
  return doquery("SELECT * FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY `blitz_place` FOR UPDATE;");
}

function db_blitz_reg_get_player_list_and_users($current_round) {
  return doquery(
    "SELECT u.*, br.blitz_name, br.blitz_password, br.blitz_place, br.blitz_status, br.blitz_points, br.blitz_reward_dark_matter
    FROM {{blitz_registrations}} AS br
    JOIN {{users}} AS u ON u.id = br.user_id
  WHERE br.`round_number` = {$current_round}
  order by `blitz_place`, `timestamp`;");
}

function db_blitz_reg_update_with_name_and_password($blitz_name, $blitz_password, $row, $current_round) {
  doquery("UPDATE {{blitz_registrations}} SET blitz_name = '{$blitz_name}', blitz_password = '{$blitz_password}' WHERE `id` = {$row['id']} AND `round_number` = {$current_round};");
}

function db_blitz_reg_update_apply_results($reward, $row, $current_round) {
  doquery("UPDATE {{blitz_registrations}} SET blitz_reward_dark_matter = blitz_reward_dark_matter + ($reward) WHERE id = {$row['id']} AND `round_number` = {$current_round};");
}

function db_blitz_reg_update_results($blitz_result_data, $current_round) {
  doquery(
    "UPDATE `{{blitz_registrations}}` SET
            `blitz_player_id` = '{$blitz_result_data[0]}',
            `blitz_online` = '{$blitz_result_data[2]}',
            `blitz_place` = '{$blitz_result_data[3]}',
            `blitz_points` = '{$blitz_result_data[4]}'
          WHERE `blitz_name` = '{$blitz_result_data[1]}' AND `round_number` = {$current_round};");
}

function db_blitz_reg_delete($user, $current_round) {
  doquery("DELETE FROM {{blitz_registrations}} WHERE `user_id` = {$user['id']} AND `round_number` = {$current_round};");
}


// Universe *************************************************************************************************************
function db_universe_get_name($uni_galaxy, $uni_system = 0) {
  $db_row = doquery("select `universe_name` from `{{universe}}` where `universe_galaxy` = {$uni_galaxy} and `universe_system` = {$uni_system} limit 1;", true);

  return $db_row['universe_name'];
}


// Payment *************************************************************************************************************

function db_payment_get($payment_id) {
  return doquery("SELECT * FROM {{payment}} WHERE `payment_id` = {$payment_id} LIMIT 1;", true);
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
  $query = doquery("SELECT * FROM `{{payment}}` WHERE 1 {$extra_conditions} ORDER BY payment_id DESC;");

  return $query;
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_payment_list_payers() {
  $query = doquery("SELECT payment_user_id, payment_user_name FROM `{{payment}}` GROUP BY payment_user_id ORDER BY payment_user_name");

  return $query;
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_payment_list_modules() {
  $query = doquery("SELECT DISTINCT payment_module_name FROM `{{payment}}` ORDER BY payment_module_name");

  return $query;
}


// Log Online *************************************************************************************************************
function db_log_online_insert() {
  $config = classSupernova::$config;
  doquery("INSERT IGNORE INTO {{log_users_online}} SET online_count = {$config->var_online_user_count};");
}

// Log *************************************************************************************************************

/**
 * @return array|bool|mysqli_result|null
 */
function db_log_list_get_last_100() {
  $query = doquery("SELECT * FROM `{{logs}}` ORDER BY log_id DESC LIMIT 100;");

  return $query;
}

/**
 * @param $delete
 */
function db_log_delete_by_id($delete) {
  doquery("DELETE FROM `{{logs}}` WHERE `log_id` = {$delete} LIMIT 1;");
}

function db_log_delete_update_and_stat_calc() {
  doquery("DELETE FROM `{{logs}}` WHERE `log_code` IN (103, 180, 191);");
}

/**
 * @param $detail
 *
 * @return array|bool|mysqli_result|null
 */
function db_log_get_by_id($detail) {
  $errorInfo = doquery("SELECT * FROM `{{logs}}` WHERE `log_id` = {$detail} LIMIT 1;", true);

  return $errorInfo;
}

/**
 * @param $i
 *
 * @return array|bool|mysqli_result|null
 */
function db_log_count($i) {
  $query = doquery("SELECT COUNT(*) AS LOG_MESSAGES_TOTAL, {$i} AS LOG_MESSAGES_VISIBLE FROM `{{logs}}`;", true);

  return $query;
}

// SYSTEM QUERIES - MOVE TO DB *****************************************************************************************
/**
 * @return array|bool|mysqli_result|null
 */
function db_core_show_status() {
  $result = doquery('SHOW STATUS;');

  return $result;
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_counter_list_by_week() {
  $query = doquery("SELECT `visit_time`, user_id FROM `{{counter}}` where user_id <> 0 and visit_time > UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY)) order by user_id, visit_time;");

  return $query;
}

/**
 * @param $user_last_browser_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_browser_agent_get_by_id($user_last_browser_id) {
  $temp = doquery("SELECT browser_user_agent FROM {{security_browser}} WHERE `browser_id` = {$user_last_browser_id}", true);

  return $temp['browser_user_agent'];
}

