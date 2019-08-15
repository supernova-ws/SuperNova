<?php

/** @noinspection SqlResolve */

namespace Unit;

use _SnCacheInternal;
use Exception;
use mysqli_result;
use Planet\Planet;
use SN;

class DBStaticUnit {

  public static function db_unit_time_restrictions($date = SN_TIME_NOW) {
    $date = is_numeric($date) ? "FROM_UNIXTIME({$date})" : "'{$date}'";

    return
      "(unit_time_start IS NULL OR unit_time_start <= {$date}) AND
    (unit_time_finish IS NULL OR unit_time_finish = '1970-01-01 03:00:00' OR unit_time_finish >= {$date})";
  }

  public static function db_unit_by_id($unit_id) {
    $unit = SN::db_get_record_by_id(LOC_UNIT, $unit_id);
    if (is_array($unit)) {
      _SnCacheInternal::unit_linkLocatorToData($unit, $unit_id);
    }

    return $unit;
  }

  public static function db_unit_by_location($user_id, $location_type, $location_id, $unit_snid = 0) {
    // apply time restrictions ????
    SN::db_get_unit_list_by_location($user_id, $location_type, $location_id);

    return
      !$unit_snid
        ? _SnCacheInternal::unit_locatorGetAllFromLocation($location_type, $location_id)
        : _SnCacheInternal::unit_locatorGetUnitFromLocation($location_type, $location_id, $unit_snid);
  }

  public static function db_unit_count_by_user_and_type_and_snid($user_id, $unit_type = 0, $unit_snid = 0) {
    $query  = doquery(
      "SELECT unit_snid, sum(unit_level) as `qty`  FROM {{unit}} WHERE `unit_player_id` = {$user_id} " .
      ($unit_type ? "AND `unit_type` = {$unit_type} " : '') .
      ($unit_snid ? "AND `unit_snid` = {$unit_snid} " : '') .
      'GROUP BY `unit_snid`'
    );
    $result = array();
    while ($row = db_fetch($query)) {
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

  public static function db_unit_set_by_id($unit_record_id, $set) {
    return SN::db_upd_record_by_id(LOC_UNIT, $unit_record_id, $set);
  }

  /**
   * @param string $set
   *
   * @return array|bool|false|mysqli_result|null
   */
  public static function db_unit_set_insert($set) {
    return SN::db_ins_record(LOC_UNIT, $set);
  }

  public static function db_unit_list_delete($user_id, $unit_location_type, $unit_location_id = 0, $unit_snid = 0) {
    return SN::db_del_record_list(LOC_UNIT,
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
    /** @noinspection SqlWithoutWhere */
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

  /**
   * Adjust unit amount
   *
   * @param int   $playerId
   * @param int   $planetId
   * @param int   $unitSnId
   * @param float $amount
   * @param int   $reason
   *
   * @return bool
   *
   * @throws Exception
   */
  public static function dbChangeUnit($playerId, $planetId, $unitSnId, $amount, $reason = RPG_NONE) {
    $result = false;

    // TODO - Lock user
    $userArray = db_user_by_id($playerId);

    if ($unitSnId == RES_DARK_MATTER) {
      // Add dark matter to user
      $result = boolval(rpg_points_change($playerId, $reason, $amount));
    } elseif (in_array($unitSnId, sn_get_groups(UNIT_RESOURCES_STR_LOOT))) {
      // Add resources to user's capital
      if ($userArray['user_as_ally'] == 1) {
        // TODO - If ally - adding resources to user record
      } else {
        // Adding resources to planet
        $planet = new Planet();
        $planet->dbLoadRecord($planetId);
        $planet->changeResource($unitSnId, $amount);
        $planet->save();

        $result = true;
      }
    } elseif (in_array($unitSnId, sn_get_groups(UNIT_ARTIFACTS_STR))) {
      // Add artifacts to player
      $result = self::dbAdd($playerId, 0, $unitSnId, $amount);
    } elseif (!empty($planetId) && in_array($unitSnId, sn_get_groups([UNIT_STRUCTURES_STR, UNIT_SHIPS_STR, UNIT_DEFENCE_STR, ]))) {
      // Add fleet or defense to user's capital
      $result = self::dbAdd($playerId, $planetId, $unitSnId, $amount);
    }

    return $result;
  }


  /**
   * Add unit to player/planet
   *
   * Supports units. DOES NOT support resources
   *
   * DOES NOT autodetect location
   *
   * @param int   $playerId
   * @param int   $planetId 0 - for player units
   * @param int   $unitSnId
   * @param float $amount
   *
   * @return bool
   */
  protected static function dbAdd($playerId, $planetId, $unitSnId, $amount) {
    if (!in_array($unitSnId, sn_get_groups([UNIT_SHIPS_STR, UNIT_DEFENCE_STR, UNIT_ARTIFACTS_STR, UNIT_STRUCTURES_STR,]))) {
      return false;
    }

    if ($planetId == 0) {
      $locationType = LOC_USER;
      $locationId   = $playerId;
    } else {
      $locationType = LOC_PLANET;
      $locationId   = $planetId;
    }

    $fields = [
      'unit_player_id'     => $playerId,
      'unit_location_type' => $locationType,
      'unit_location_id'   => $locationId,
      'unit_snid'          => $unitSnId,
    ];
    if (!($unitRecord = RecordUnit::findFirst($fields))) {
      if ($amount < 0) {
        return false;
      }

      // New unit
//      $unitRecord = RecordUnit::build([
//        'unit_player_id'     => $playerId,
//        'unit_location_type' => $locationType,
//        'unit_location_id'   => $locationId,
//        'unit_snid'          => $unitSnId,
//        'unit_type'          => get_unit_param($unitSnId, P_UNIT_TYPE),
//        'unit_level'         => $level,
//      ]);

      $fields     += [
        'unit_type'  => get_unit_param($unitSnId, P_UNIT_TYPE),
        'unit_level' => $amount,
      ];
      $unitRecord = RecordUnit::build($fields);

      return $unitRecord->insert();
    } else {
      if ($unitRecord->unit_level + $amount < 0) {
        // TODO - Log error or throw Exception
        return false;
      }

      $unitRecord->inc()->unit_level = $amount;

      return $unitRecord->update();
    }
  }

  public static function dbUserAdd($playerId, $unitSnId, $amount) {
//    if (!($unitRecord = RecordUnit::findFirst([
//      'unit_player_id'     => $playerId,
//      'unit_location_type' => LOC_USER,
//      'unit_location_id'   => $playerId,
//      'unit_snid'          => $unitSnId,
//    ]))) {
//      if ($level < 0) {
//        return false;
//      }
//
//      // New unit
//      $unitRecord = RecordUnit::build([
//        'unit_player_id'     => $playerId,
//        'unit_location_type' => LOC_USER,
//        'unit_location_id'   => $playerId,
//        'unit_type'          => get_unit_param($unitSnId, P_UNIT_TYPE),
//        'unit_snid'          => $unitSnId,
//        'unit_level'         => $level,
//      ]);
//
////      var_dump($unitRecord);die();
//
//      return $unitRecord->insert();
//    } else {
//      if ($unitRecord->unit_level + $level < 0) {
//        // TODO - Log error or throw Exception
//        return false;
//      }
//
//      $unitRecord->inc()->unit_level = $level;
//
//      return $unitRecord->update();
//    }
    return self::dbAdd($playerId, 0, $unitSnId, $amount);
  }

}
