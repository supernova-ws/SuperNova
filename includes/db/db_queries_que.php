<?php

function db_que_list_by_type_location($que_type = false, $user_id, $planet_id = null, $for_update = false)
{
  $sql = '';
  $sql .= $user_id ? " AND `que_player_id` = {$user_id}" : '';
  $sql .= $que_type == QUE_RESEARCH || $planet_id === null ? " AND `que_planet_id` IS NULL" :
    ($planet_id ? " AND (`que_planet_id` = {$planet_id}" . ($que_type ? '' : ' OR que_planet_id IS NULL') . ")" : '');
  $sql .= $que_type ? " AND `que_type` = {$que_type}" : '';

  return ($sql = trim($sql))
    ? doquery("SELECT * FROM {{que}} WHERE 1 {$sql} ORDER BY que_id" . ($for_update ? ' FOR UPDATE' : ''))
    : false;
}

function db_que_list_stat()
{
  return doquery("SELECT que_player_id, sum(que_unit_amount) AS que_unit_amount, que_unit_price FROM `{{que}}` GROUP BY que_player_id, que_unit_price;");
}

function db_que_set_time_left_by_id($que_id, $que_time_left)
{
  return doquery("UPDATE {{que}} SET `que_time_left` = {$que_time_left} WHERE `que_id` = {$que_id} LIMIT 1;");
}

function db_que_set_insert($set)
{
  return ($set = trim($set)) ? doquery("INSERT INTO {{que}} SET {$set}") : false;
}

function db_que_delete_by_id($que_id)
{
  return doquery("DELETE FROM {{que}} WHERE que_id = {$que_id} LIMIT 1");
}
