<?php

class DBStaticUnit {

  public static function db_unit_time_restrictions($date = SN_TIME_NOW) {
    $date = is_numeric($date) ? "FROM_UNIXTIME({$date})" : "'{$date}'";

    return
      "(unit_time_start IS NULL OR unit_time_start <= {$date}) AND
    (unit_time_finish IS NULL OR unit_time_finish = '1970-01-01 03:00:00' OR unit_time_finish >= {$date})";
  }

  public static function db_unit_by_location($user_id = 0, $location_type, $location_id, $unit_snid = 0, $for_update = false, $fields = '*') {
    // apply time restrictions ????
    return classSupernova::db_get_unit_by_location($user_id, $location_type, $location_id, $unit_snid, $for_update, $fields);
  }

  public static function db_unit_count_by_user_and_type_and_snid($user_id, $unit_type = 0, $unit_snid = 0) {
    $query = doquery(
      "SELECT unit_snid, sum(unit_level) as `qty`  FROM {{unit}} WHERE `unit_player_id` = {$user_id} " .
      ($unit_type ? "AND `unit_type` = {$unit_type} " : '') .
      ($unit_snid ? "AND `unit_snid` = {$unit_snid} " : '') .
      'GROUP BY `unit_snid`'
    );
    $result = array();
    while($row = db_fetch($query)) {
      $result[$row['unit_snid']] = $row;
    }

    return $result;
  }

// Used by UNIT_CAPTAIN module TODO
  public static function db_unit_in_fleet_by_user($user_id, $location_id, $unit_snid, $for_update) {
    return doquery(
      "SELECT *
    FROM {{fleets}} AS f
      JOIN {{unit}} AS u ON u.`unit_location_id` = f.fleet_id
    WHERE
      f.fleet_owner = {$user_id} AND
      (f.fleet_start_planet_id = {$location_id} OR f.fleet_end_planet_id = {$location_id})
      AND u.unit_snid = {$unit_snid} AND u.`unit_location_type` = " . LOC_FLEET . " AND " . self::db_unit_time_restrictions() .
      " LIMIT 1" .
      ($for_update ? ' FOR UPDATE' : '')
      , true);
  }


  public static function db_unit_list_laboratories($user_id) {
    return doquery("SELECT DISTINCT unit_location_id AS `id`
    FROM {{unit}}
    WHERE unit_player_id = {$user_id} AND unit_location_type = " . LOC_PLANET . " AND unit_level > 0 AND unit_snid IN (" . STRUC_LABORATORY . ", " . STRUC_LABORATORY_NANO . ");");
  }

  public static function db_unit_set_by_id($unit_id, $set) {
    return classSupernova::db_upd_record_by_id(LOC_UNIT, $unit_id, $set);
  }

  /**
   * @param string $set
   *
   * @return array|bool|false|mysqli_result|null
   */
  public static function db_unit_set_insert($set) {
    return classSupernova::db_ins_record(LOC_UNIT, $set);
  }

  public static function db_unit_list_delete($user_id = 0, $unit_location_type, $unit_location_id = 0, $unit_snid = 0) {
    return classSupernova::db_del_record_list(LOC_UNIT,
      "`unit_location_type` = {$unit_location_type}" .
      ($unit_location_id = idval($unit_location_id) ? " AND `unit_location_id` = {$unit_location_id}" : '') .
      ($user_id = idval($user_id) ? " AND `unit_player_id` = {$user_id}" : '') .
      ($unit_snid = idval($unit_snid) ? " AND `unit_snid` = {$unit_snid}" : ''));
  }

  public static function db_unit_list_stat_calculate() {
    return doquery(
      "SELECT unit_player_id, unit_type, unit_snid, unit_level, count(*) AS unit_amount
    FROM `{{unit}}`
    WHERE unit_level > 0 AND " . self::db_unit_time_restrictions() .
      " GROUP BY unit_player_id, unit_type, unit_snid, unit_level"
    );
  }


  public static function db_unit_change_owner($location_type, $location_id, $new_owner_id) {
    doquery("UPDATE {{unit}} SET `unit_player_id` = {$new_owner_id} WHERE `unit_location_type` = {$location_type} AND `unit_location_id` = {$location_id}");
  }


  public static function db_unit_list_admin_delete_mercenaries_finished() {
    return doquery("DELETE FROM {{unit}} WHERE unit_time_finish IS NOT NULL AND unit_time_finish < FROM_UNIXTIME(" . SN_TIME_NOW . ") AND unit_type = " . UNIT_MERCENARIES);
  }

  public static function db_unit_list_admin_set_mercenaries_expire_time($default_length) {
    return doquery(
      "UPDATE {{unit}}
    SET
      unit_time_start = FROM_UNIXTIME(" . SN_TIME_NOW . "),
      unit_time_finish = FROM_UNIXTIME(" . (SN_TIME_NOW + $default_length) . ")
    WHERE unit_type = " . UNIT_MERCENARIES
    );
  }

}
