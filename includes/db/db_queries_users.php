<?php

/**
 * Возвращает информацию о пользователе по его ID
 *
 * @param int|array $user_id_unsafe
 *    <p>int - ID пользователя</p>
 *    <p>array - запись пользователя с установленным полем ['id']</p>
 * @param bool      $for_update @deprecated
 * @param string    $fields @deprecated список полей или '*'/'' для всех полей
 * @param null      $player
 * @param bool|null $player Признак выбора записи пользователь типа "игрок"
 *    <p>null - Можно выбрать запись любого типа</p>
 *    <p>true - Выбирается только запись типа "игрок"</p>
 *    <p>false - Выбирается только запись типа "альянс"</p>
 *
 * @return array|false
 *    <p>false - Нет записи с указанным ID и $player</p>
 *    <p>array - запись типа $user</p>
 * @deprecated
 */
function db_user_by_id($user_id_unsafe, $for_update = false, $fields = '*', $player = null) {
  $user = SN::db_get_record_by_id(LOC_USER, $user_id_unsafe);

  return (is_array($user) &&
    (
      $player === null
      ||
      ($player === true && !$user['user_as_ally'])
      ||
      ($player === false && $user['user_as_ally'])
    )) ? $user : false;
}

/**
 * @param        $where_safe
 *
 * @return array|null
 * @deprecated
 */
function db_get_user_by_where($where_safe) {
  $user = null;

  if (!empty($where_safe)) {
    // Вытаскиваем запись
    $user = SN::db_query_select(
      "SELECT * FROM {{users}} WHERE {$where_safe}",
      true
    );

    _SnCacheInternal::cache_set(LOC_USER, $user['id'], $user); // В кэш-юзер так же заполнять индексы
  }

  return $user;
}

/**
 * @deprecated
 */
function db_user_by_username($username_unsafe, $like = false) {
  if (!($username_unsafe = trim($username_unsafe))) {
    return null;
  }

  $username_safe = db_escape($like ? strtolower($username_unsafe) : $username_unsafe); // тут на самом деле strtolower() лишняя, но пусть будет

  $user = db_get_user_by_where("`username` " . ($like ? 'LIKE' : '=') . " '{$username_safe}'");

  return $user;
}
/**
 * @param        $username_unsafe
 * @param bool   $for_update
 * @param string $fields
 * @param null   $player
 * @param bool   $like
 *
 * @return array|false
 * @deprecated
 */
function dbPlayerByIdOrName($username_unsafe, $player = null, $like = false) {
  $row = db_user_by_id($username_unsafe, false, '*', $player);
  if (empty($row['id'])) {
    $row = db_user_by_username($username_unsafe, $like);
  }

  return !is_array($row) || empty($row['id']) ? false : $row;
}

/**
 * @deprecated
 */
function db_user_list($user_filter = '', $for_update = false, $fields = '*') {
  return SN::db_get_record_list(LOC_USER, $user_filter);
}



/**
 * @deprecated
 */
function db_user_set_by_id($user_id, $set) {
  return SN::db_upd_record_by_id(LOC_USER, $user_id, $set);
}


/**
 * @deprecated
 */
function db_user_list_set_mass_mail(&$owners_list, $set) {
  return SN::db_upd_record_list(LOC_USER, !empty($owners_list) ? '`id` IN (' . implode(',', $owners_list) . ');' : '', $set);
}
/**
 * @deprecated
 */
function db_user_list_set_by_ally_and_rank($ally_id, $ally_rank_id, $set) {
  return SN::db_upd_record_list(LOC_USER, "`ally_id`={$ally_id} AND `ally_rank_id` >= {$ally_rank_id}", $set);
}
/**
 * @deprecated
 */
function db_user_list_set_ally_deprecated_convert_ranks($ally_id, $i, $rank_id) {
  return SN::db_upd_record_list(LOC_USER, "`ally_id` = {$ally_id} AND `ally_rank_id`={$rank_id}", "`ally_rank_id` = {$i}");
}





function db_user_change_active_planet_to_capital($user_id, $captured_planet) {
  return doquery("UPDATE {{users}} SET `current_planet` = `id_planet` WHERE `id` = {$user_id} AND `current_planet` = {$captured_planet};");
}



// TODO Внести это всё в supernova для HyperNova
/**
 * @return string
 * @deprecated
 * TODO - это вообще-то надо хранить в конфигурации
 */
function db_user_last_registered_username() {
  $user = SN::db_query_select(
    'SELECT * FROM `{{users}}` WHERE `user_as_ally` IS NULL ORDER BY `id` DESC',
    true
  );
  _SnCacheInternal::cache_set(LOC_USER, $user['id'], $user);

  return isset($user['username']) ? $user['username'] : '';
}

function db_user_count($online = false) {
  $result = doquery('SELECT COUNT(`id`) AS user_count FROM `{{users}}` WHERE `user_as_ally` IS NULL AND `user_bot` = ' . USER_BOT_PLAYER . ($online ? ' AND onlinetime > ' . (SN_TIME_NOW - SN::$config->game_users_online_timeout) : ''), true);
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
  global $config;

  return doquery(
    "SELECT `id` AS `ID`, `username` AS `NAME`, `ally_name` AS `ALLY`, `total_points` AS `STAT_POINTS`,
      `onlinetime` AS `ACTIVITY`
    FROM `{{users}}`
    WHERE `onlinetime` >= ". (SN_TIME_NOW - $config->game_users_online_timeout) ." ORDER BY user_as_ally, `". $TypeSort ."` ASC;");
}


function db_user_list_admin_multiaccounts() {
  return doquery("SELECT COUNT(*) as ip_count, `user_lastip` FROM `{{users}}` WHERE user_as_ally IS NULL GROUP BY user_lastip HAVING COUNT(*) > 1;");
}


function db_user_list_admin_sorted($sort, $online = false) {
  global $config;

  return doquery("SELECT u.*, COUNT(r.id) AS referral_count, SUM(r.dark_matter) AS referral_dm FROM {{users}} as u
    LEFT JOIN {{referrals}} as r on r.id_partner = u.id
    WHERE" .
    ($online ? " `onlinetime` >= ". (SN_TIME_NOW - $config->game_users_online_timeout) : ' user_as_ally IS NULL') .
    " GROUP BY u.id
    ORDER BY user_as_ally, {$sort} ASC");
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
