<?php

function db_que_list_by_type_location($user_id, $planet_id = null, $que_type = false, $for_update = false)
{
  return classSupernova::db_que_list_by_type_location($user_id, $planet_id, $que_type, $for_update);
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
