<?php
/*
function db_user_by_id($user_id, $for_update = false, $fields = '*')
{
  return ($user_id = intval($user_id)) ? doquery("SELECT {$fields} FROM {{users}} WHERE `id` = {$user_id} LIMIT 1" . ($for_update ? ' FOR UPDATE' : ''), true) : false;
}
function db_user_player_by_id($user_id, $for_update = false, $fields = '*')
{
  return ($user_id = intval($user_id))
    ? doquery("SELECT {$fields} FROM {{users}} WHERE `id` = {$user_id} AND `user_as_ally` IS NULL LIMIT 1" . ($for_update ? ' FOR UPDATE' : ''), true)
    : false;
}


function db_user_by_username_safe($username, $for_update = false, $fields = '*')
{
  return ($username = trim($username))
    ? doquery("SELECT {$fields} FROM {{users}} WHERE `username` = '{$username}' LIMIT 1" . ($for_update ? ' FOR UPDATE' : ''), true)
    : false;
}
function db_user_player_like_name_safe($username, $for_update = false, $fields = '*')
{
  return ($username = trim($username))
    ? doquery("SELECT {$fields} FROM {{users}} WHERE `username` LIKE '{$username}' AND `user_as_ally` IS NULL LIMIT 1" . ($for_update ? ' FOR UPDATE' : ''), true)
    : false;
}


function db_user_by_email_safe($email, $use_both = false, $for_update = false, $fields = '*')
{
  return ($email = trim($email))
    ? doquery("SELECT {$fields} FROM {{users}} WHERE `email_2` = {$email}" .
      ($use_both ? " OR `email` = {$email}" : '') .
      " LIMIT 1" .
      ($for_update ? ' FOR UPDATE' : ''), true)
    : false;
}

function db_user_last_registered_username()
{
  $result = doquery('SELECT username FROM {{users}} WHERE `user_as_ally` IS NULL ORDER BY register_time DESC LIMIT 1;', true);
  return isset($result['username']) ? $result['username'] : 0;
}

function db_user_list_return_query($user_filter = '', $for_update = false, $fields = '*')
{
  $user_filter = trim($user_filter);
  return
    doquery("SELECT {$fields} FROM {{users}}" .
      ($user_filter ? " WHERE {$user_filter}" : '') .
      ($for_update ? ' FOR UPDATE' : ''));
}

function db_user_list_by_ip($ip)
{
  return doquery("SELECT * FROM {{users}} WHERE `user_lastip` = '{$ip}'");
}


function db_user_list_like_name_extra($user_name, $extra = '', $fields = '*')
{
  return doquery("SELECT {$fields} FROM {{users}} WHERE `username` like '{$user_name}'" . $extra);
}

function db_user_list_player_by_ally($ally_id, $ally_rank_id = 0, $for_update = false, $fields = '*')
{
  return ($ally_id = intval($ally_id))
    ? doquery("SELECT {$fields} FROM {{users}} WHERE ally_id = '{$ally_id}'" .
      ($ally_rank_id >= 0 ? " AND ally_rank_id = {$ally_rank_id}" : '')) .
    ($for_update ? ' FOR UPDATE' : '')
    : false;
}
function db_user_list_by_ally($ally_id, $fields = '*', $sort = '', $extra_where = '')
{
  return ($ally_id = intval($ally_id))
    ? doquery("SELECT {$fields} FROM {{users}} WHERE `ally_id`= {$ally_id} " .
        ($extra_where = trim($extra_where) ? $extra_where : '') .
        ($sort = trim($sort) ? 'ORDER BY ' . $sort : '')
      )
    : false;
}

function db_user_list_admin_contacts()
{
  return doquery("SELECT `username`, `email`, `authlevel` FROM {{users}} WHERE `authlevel` != 0 ORDER BY `authlevel` DESC;");
}

function db_user_list_skiplist($user_skip_list)
{
  return doquery("SELECT `id` FROM {{users}} WHERE {$user_skip_list}");
}

function db_user_set_by_id($user_id, $set)
{
  return ($user_id = intval($user_id)) && ($set = trim($set)) ? doquery("UPDATE {{users}} SET {$set} WHERE `id` = {$user_id} LIMIT 1") : false;
}

function db_user_set_by_name($user_name, $set)
{
  return doquery("UPDATE {{users}} SET {$set} WHERE `username` = '{$user_name}';");
}

function db_user_list_set_mass_mail(&$owners_list, $set)
{
  doquery("UPDATE {{users}} SET {$set}" . (!empty($owners_list) ? ' WHERE `id` IN (' . implode(',', $owners_list) . ');' : ''));
}

function db_user_list_set_by_ally_and_rank($ally_id, $ally_rank_id, $set)
{
  return doquery("UPDATE {{users}} SET {$set} WHERE `ally_id`={$ally_id} AND `ally_rank_id` >= {$ally_rank_id}");
}

function db_user_list_set_ally_deprecated_convert_ranks($ally_id, $i, $rank_id)
{
  return doquery("UPDATE {{users}} SET `ally_rank_id` = {$i} WHERE `ally_id` ='{$ally_id}' AND `ally_rank_id`={$rank_id};");
}

function db_user_insert_set($set)
{
  return ($set = trim($set)) ? doquery("INSERT INTO `{{users}}` SET {$set}") : false;
}

function db_user_delete_by_id($user_id)
{
  return ($user_id = intval($user_id)) ? doquery("DELETE FROM `{{users}}` WHERE `id` = {$user_id}") : false;
}

*/


function db_user_by_id($user_id, $for_update = false, $fields = '*')
{
  return classSupernova::db_get_user_by_id($user_id, $for_update, $fields);
}
function db_user_player_by_id($user_id, $for_update = false, $fields = '*')
{
  return classSupernova::db_get_user_by_id($user_id, $for_update, $fields, true);
}

function db_user_by_username($username, $for_update = false, $fields = '*')
{
  return classSupernova::db_get_user_by_username($username, $for_update, $fields);
}
function db_user_player_like_name($username, $for_update = false, $fields = '*')
{
  return classSupernova::db_get_user_by_username($username, $for_update, $fields, true, true);
}

function db_user_by_email($email, $use_both = false, $for_update = false, $fields = '*')
{
  return classSupernova::db_get_user_by_email($email, $use_both, $for_update, $fields);
}


function db_user_list($user_filter = '', $for_update = false, $fields = '*')
{
  return classSupernova::db_get_user_list($user_filter, $for_update, $fields);
}



function db_user_set_by_id($user_id, $set)
{
  return classSupernova::db_set_user_by_id($user_id, $set);
}


function db_user_list_set_mass_mail(&$owners_list, $set)
{
  return classSupernova::db_upd_user_list(!empty($owners_list) ? '`id` IN (' . implode(',', $owners_list) . ');' : '', $set);
}
function db_user_list_set_by_ally_and_rank($ally_id, $ally_rank_id, $set)
{
  return classSupernova::db_upd_user_list("`ally_id`={$ally_id} AND `ally_rank_id` >= {$ally_rank_id}", $set);
}
function db_user_list_set_ally_deprecated_convert_ranks($ally_id, $i, $rank_id)
{
  return classSupernova::db_upd_user_list("`ally_id` ='{$ally_id}' AND `ally_rank_id`={$rank_id}", "`ally_rank_id` = {$i}");
}



function db_user_insert_set($set)
{
  return classSupernova::db_ins_user($set);
}



function db_user_delete_by_id($user_id)
{
  return classSupernova::db_del_user_by_id($user_id);
}




// TODO Внести это всё в $supernova для HyperNova
function db_user_last_registered_username()
{
  return classSupernova::db_get_user_player_username_last_registered();
}

function db_user_count($online = false)
{
  $result = doquery('SELECT COUNT(id) AS user_count FROM {{users}} WHERE user_as_ally IS NULL' . ($online ? ' AND onlinetime > ' . (SN_TIME_NOW - 15 * PERIOD_MINUTE) : ''), true);
  return isset($result['user_count']) ? $result['user_count'] : 0;
}

function db_user_list_to_celebrate($config_user_birthday_range)
{
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

function db_user_list_online_sorted($TypeSort)
{
  return doquery("SELECT `id` AS `ID`, `username` AS `NAME`, `user_agent` AS `BROWSER`, `ally_name` AS `ALLY`, `total_points` AS `STAT_POINTS`, `onlinetime` AS `ACTIVITY` FROM {{users}} WHERE `onlinetime` >= '". (SN_TIME_NOW - 15 * PERIOD_MINUTE) ."' ORDER BY user_as_ally, `". $TypeSort ."` ASC;");
}


function db_user_list_admin_multiaccounts()
{
  return doquery("SELECT COUNT(*) as ip_count, user_lastip FROM {{users}} WHERE user_as_ally IS NULL GROUP BY user_lastip HAVING COUNT(*) > 1;");
}


function db_user_list_admin_sorted($sort)
{
  return doquery("SELECT u.*, COUNT(r.id) AS referral_count, SUM(r.dark_matter) AS referral_dm FROM {{users}} as u
    LEFT JOIN {{referrals}} as r on r.id_partner = u.id
    WHERE user_as_ally IS NULL
    group by u.id
    ORDER BY {$sort} ASC");
}
