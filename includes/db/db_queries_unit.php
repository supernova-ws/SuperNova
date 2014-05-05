<?php

function db_unit_time_restrictions($date = SN_TIME_NOW)
{
  $date = is_numeric($date) ? "FROM_UNIXTIME({$date})" : "'{$date}'";
  return
    "(unit_time_start IS NULL OR unit_time_start <= {$date}) AND
    (unit_time_finish IS NULL OR unit_time_finish = '1970-01-01 03:00:00' OR unit_time_finish >= {$date})";
}

function db_unit_by_id($unit_id, $for_update = false, $fields = '*')
{
  return ($unit_id = intval($unit_id))
    ? doquery(
        "SELECT {$fields} FROM {{unit}} WHERE `unit_id` = {$unit_id} AND " . db_unit_time_restrictions() .
        ' LIMIT 1' .
        ($for_update ? ' FOR UPDATE' : '')
      , true)
    : false;
}

function db_unit_by_location($user_id = 0, $location_type, $location_id, $unit_snid = 0, $for_update = false, $fields = '*')
{
  return doquery(
    "SELECT {$fields}
    FROM {{unit}}
    WHERE
      `unit_location_type` = {$location_type} AND `unit_location_id` = {$location_id} AND " . db_unit_time_restrictions() .
      // "AND (unit_time_start IS NULL OR unit_time_start <= FROM_UNIXTIME(" . SN_TIME_NOW . ")) AND (unit_time_finish IS NULL OR unit_time_finish >= FROM_UNIXTIME(" . SN_TIME_NOW . "))" .
      ($user_id = intval($user_id) ? " AND `unit_player_id` = {$user_id}" : '') .
      ($unit_snid = intval($unit_snid) ? " AND `unit_snid` = {$unit_snid}" : '') .
    " LIMIT 1" .
    ($for_update ? ' FOR UPDATE' : '')
  , true);
}

function db_unit_in_fleet_by_user($user_id, $location_id, $unit_snid, $for_update)
{
  return doquery(
    "SELECT *
    FROM {{fleets}} AS f
      JOIN {{unit}} AS u ON u.`unit_location_id` = f.fleet_id
    WHERE
      f.fleet_owner = {$user_id}
      AND (f.fleet_start_planet_id = {$location_id} OR f.fleet_end_planet_id = {$location_id})
      AND u.unit_snid = {$unit_snid} AND u.`unit_location_type` = " . LOC_FLEET .
      " AND " . db_unit_time_restrictions() .
    " LIMIT 1" .
    ($for_update ? ' FOR UPDATE' : '')
  , true);
}




function db_unit_list_laboratories($user_id)
{
  return doquery("SELECT DISTINCT unit_location_id AS `id`
    FROM {{unit}}
    WHERE unit_player_id = {$user_id} AND unit_location_type = " . LOC_PLANET . " AND unit_level > 0 AND unit_snid IN (" . STRUC_LABORATORY . ", " . STRUC_LABORATORY_NANO . ");");
}

function db_unit_set_by_id($unit_id, $set)
{
  return doquery("UPDATE {{unit}} SET {$set} WHERE `unit_id` = {$unit_id} LIMIT 1");
}

function db_unit_set_insert($set)
{
  return doquery("INSERT INTO {{unit}} SET {$set}");
}

function db_unit_list_delete($user_id = 0, $unit_location_type, $unit_location_id, $unit_snid = 0)
{
  return doquery(
    "DELETE FROM {{unit}}
    WHERE unit_location_type = {$unit_location_type} AND unit_location_id = {$unit_location_id}" .
    ($user_id = intval($user_id) ? " AND unit_player_id = {$user_id}" : '') .
    ($unit_snid = intval($unit_snid) ? " AND unit_snid = {$unit_snid}" : '')
  );
}



function db_unit_list_stat_calculate()
{
  return doquery(
    "SELECT unit_player_id, unit_type, unit_snid, unit_level, count(*) AS unit_amount
    FROM `{{unit}}`
    WHERE unit_level > 0 AND " . db_unit_time_restrictions() .
    " GROUP BY unit_player_id, unit_type, unit_snid, unit_level"
  );
}




function db_unit_list_admin_delete_mercenaries_finished()
{
  return doquery("DELETE FROM {{unit}} WHERE unit_time_finish IS NOT NULL AND unit_time_finish < FROM_UNIXTIME(" . SN_TIME_NOW . ") AND unit_type = " . UNIT_MERCENARIES);
}

function db_unit_list_admin_set_mercenaries_expire_time($default_length)
{
  return doquery(
    "UPDATE {{unit}}
    SET
      unit_time_start = FROM_UNIXTIME(" . SN_TIME_NOW . "),
      unit_time_finish = FROM_UNIXTIME(" . (SN_TIME_NOW + $default_length) . ")
    WHERE unit_type = " . UNIT_MERCENARIES
  );
}
