<?php

// ALLY *************************************************************************************************************
function db_ally_list_recalc_counts() {
  return doquery("UPDATE `{{alliance}}` AS a LEFT JOIN (SELECT ally_id, count(*) AS ally_memeber_count FROM `{{users}}`
      WHERE ally_id IS NOT NULL GROUP BY ally_id) AS u ON u.ally_id = a.id SET a.`ally_members` = u.ally_memeber_count;");
}

function db_ally_request_list($ally_id) {
  return doquery("SELECT {{alliance_requests}}.*, {{users}}.username FROM {{alliance_requests}} LEFT JOIN {{users}} ON {{users}}.id = {{alliance_requests}}.id_user WHERE id_ally='{$ally_id}'");
}

function db_ally_request_get_by_user_id($player_id) {
  return doquery("SELECT * FROM {{alliance_requests}} WHERE `id_user` ='{$player_id}' LIMIT 1;", true);
}

function db_ally_count() {
  $result = doquery('SELECT COUNT(`id`) AS ally_count FROM `{{alliance}}`', true);

  return isset($result['ally_count']) ? $result['ally_count'] : 0;
}

function db_ally_get_by_id($ally_id) {
  return doquery("SELECT * FROM `{{alliance}}` WHERE `id` = '{$ally_id}' LIMIT 1;", true);
}

/**
 * @param $searchtext
 *
 * @return array|bool|mysqli_result|null
 */
function db_ally_list_search($searchtext) {
  $search = doquery("SELECT ally_name, ally_tag, total_rank, ally_members FROM {{alliance}} WHERE ally_tag LIKE '%{$searchtext}%' OR ally_name LIKE '%{$searchtext}%' LIMIT 30");

  return $search;
}
