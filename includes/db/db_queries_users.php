<?php

function db_user_by_id($user_id_unsafe, $for_update = false, $fields = '*', $player = null) {
  return classSupernova::db_get_user_by_id($user_id_unsafe, $for_update, $fields, $player);
}
function db_user_by_username($username_unsafe, $for_update = false, $fields = '*', $player = null, $like = false) {
  return classSupernova::db_get_user_by_username($username_unsafe, $for_update, $fields, $player, $like);
}
function db_user_by_email($email_unsafe, $use_both = false, $for_update = false, $fields = '*') {
  return classSupernova::db_get_user_by_email($email_unsafe, $use_both, $for_update, $fields);
}

/*
function    db_user_by_account_name($account_name_unsafe, &$result = null) {return sn_function_call(__FUNCTION__, array($account_name_unsafe, &$result));}
function sn_db_user_by_account_name($account_name_unsafe, &$result = null) {
  return empty($result) ? $result = db_user_by_account(db_account_by_name($account_name_unsafe)) : $result;
}
*/
function db_user_list($user_filter = '', $for_update = false, $fields = '*') {
  return classSupernova::db_get_record_list(LOC_USER, $user_filter);
}



function db_user_set_by_id($user_id, $set) {
  return classSupernova::db_upd_record_by_id(LOC_USER, $user_id, $set);
  // return classSupernova::db_set_user_by_id($user_id, $set);
}


function db_user_list_set_mass_mail(&$owners_list, $set) {
  return classSupernova::db_upd_record_list(LOC_USER, !empty($owners_list) ? '`id` IN (' . implode(',', $owners_list) . ');' : '', $set);
}
function db_user_list_set_by_ally_and_rank($ally_id, $ally_rank_id, $set) {
  return classSupernova::db_upd_record_list(LOC_USER, "`ally_id`={$ally_id} AND `ally_rank_id` >= {$ally_rank_id}", $set);
}
function db_user_list_set_ally_deprecated_convert_ranks($ally_id, $i, $rank_id) {
  return classSupernova::db_upd_record_list(LOC_USER, "`ally_id` = {$ally_id} AND `ally_rank_id`={$rank_id}", "`ally_rank_id` = {$i}");
}





function db_user_change_active_planet_to_capital($user_id, $captured_planet) {
  return doquery("UPDATE {{users}} SET `current_planet` = `id_planet` WHERE `id` = {$user_id} AND `current_planet` = {$captured_planet};");
}



// TODO Внести это всё в $supernova для HyperNova
function db_user_last_registered_username() {
  return classSupernova::db_get_user_player_username_last_registered();
}

function db_user_count($online = false) {
  $result = doquery('SELECT COUNT(id) AS user_count FROM {{users}} WHERE user_as_ally IS NULL' . ($online ? ' AND onlinetime > ' . (SN_TIME_NOW - 15 * PERIOD_MINUTE) : ''), true);
  return isset($result['user_count']) ? $result['user_count'] : 0;
}

function db_user_list_to_celebrate($config_user_birthday_range) {
  return doquery(
    "SELECT
      `id`, `username`, `user_birthday`, `user_birthday_celebrated`
      , CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d')) AS `current_birthday`
      , DATEDIFF(CURRENT_DATE, CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `days_after_birthday`
    FROM
      `{{users}}`
    WHERE
      `user_birthday` IS NOT NULL
      AND (`user_birthday_celebrated` IS NULL OR DATE_ADD(`user_birthday_celebrated`, INTERVAL 1 YEAR) < CURRENT_DATE)
      AND `user_as_ally` IS NULL
    HAVING
      `days_after_birthday` >= 0 AND `days_after_birthday` < {$config_user_birthday_range} FOR UPDATE;");
}

function db_user_list_online_sorted($TypeSort) {
  return doquery(
    "SELECT `id` AS `ID`, `username` AS `NAME`, `ally_name` AS `ALLY`, `total_points` AS `STAT_POINTS`,
      `onlinetime` AS `ACTIVITY`
    FROM {{users}}
    WHERE `onlinetime` >= ". (SN_TIME_NOW - 15 * PERIOD_MINUTE) ." ORDER BY user_as_ally, `". $TypeSort ."` ASC;");
}


function db_user_list_admin_multiaccounts() {
  return doquery("SELECT COUNT(*) as ip_count, user_lastip FROM {{users}} WHERE user_as_ally IS NULL GROUP BY user_lastip HAVING COUNT(*) > 1;");
}


function db_user_list_admin_sorted($sort) {
  return doquery("SELECT u.*, COUNT(r.id) AS referral_count, SUM(r.dark_matter) AS referral_dm FROM {{users}} as u
    LEFT JOIN {{referrals}} as r on r.id_partner = u.id
    WHERE user_as_ally IS NULL
    group by u.id
    ORDER BY {$sort} ASC");
}

/**
 * Выбирает записи игроков по списку их ID
 *
 * @param $user_id_list
 *
 * @return array
 */
function db_user_list_by_id($user_id_list) {
  !is_array($user_id_list) ? $user_id_list = array($user_id_list) : false;

  $user_list = array();
  foreach($user_id_list as $user_id_unsafe) {
    $user = db_user_by_id($user_id_unsafe);
    !empty($user) ? $user_list[$user_id_unsafe] = $user : false;
  }

  return $user_list;
}
