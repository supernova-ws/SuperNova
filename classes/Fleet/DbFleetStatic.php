<?php
/**
 * Created by Gorlum 26.04.2018 14:00
 */

namespace Fleet;

use DBAL\DbQuery;
use mysqli_result;

/**
 * Class DbFleetStatic
 * @package Fleet
 *
 * deprecated
 */
class DbFleetStatic {

  /* HELPERS ******************************************************************************************************** */
  /**
   * @return DbQuery
   */
  protected static function dbq() {
    return DbQuery::build()->setTable('fleets');
  }



  /* FLEET CRUD ===================================================================================================== */
  /**
   * Inserts fleet record by ID with array
   *
   * @param array $fieldArray - [fieldName => fieldValue]
   *
   * @return int|string - fleet inserted ID or 0 if no fleets inserted
   */
  public static function fleet_insert_set_dbq($fieldArray) {
    if (!empty($fieldArray)) {
      static::dbq()
        ->setValues($fieldArray)
        ->doInsert(DbQuery::DB_INSERT_PLAIN, true);

      $fleet_id = db_insert_id();
    } else {
      $fleet_id = 0;
    }

    return $fleet_id;
  }

  /**
   * Updates fleet record by ID with SET
   *
   * @param int   $fleet_id
   * @param array $set - REPLACE-set, i.e. replacement of existing values
   * @param array $delta - DELTA-set, i.e. changes to existing values
   *
   * @return array|bool|mysqli_result|null
   */
  public static function fleet_update_set($fleet_id, $set, $delta = array()) {
    $result = false;

    $fleet_id_safe = idval($fleet_id);
    $set_string_safe = db_set_make_safe_string($set);
    !empty($delta) ? $set_string_safe = implode(',', array($set_string_safe, db_set_make_safe_string($delta, true))) : false;
    if (!empty($fleet_id_safe) && !empty($set_string_safe)) {
      $result = static::db_fleet_update_set_safe_string($fleet_id, $set_string_safe);
    }

    return $result;
  }

  /**
   * UPDATE - Updates fleet record by ID with SET
   *
   * @param int    $fleet_id
   * @param string $set_safe_string
   *
   * @return array|bool|mysqli_result|null
   */
  protected static function db_fleet_update_set_safe_string($fleet_id, $set_safe_string) {
    $fleet_id_safe = idval($fleet_id);
    if (!empty($fleet_id_safe) && !empty($set_safe_string)) {
      /** @noinspection SqlResolve */
      $result = doquery("UPDATE `{{fleets}}` SET {$set_safe_string} WHERE `fleet_id` = {$fleet_id_safe} LIMIT 1;");
    } else {
      $result = false;
    }

    return $result;
  }


  /**
   * READ - Gets fleet record by ID
   *
   * @param int $fleet_id
   *
   * @return array|false
   */
  public static function db_fleet_get($fleet_id) {
    $fleet_id_safe = idval($fleet_id);
    /** @noinspection SqlResolve */
    $result = doquery("SELECT * FROM `{{fleets}}` WHERE `fleet_id` = {$fleet_id_safe} LIMIT 1 FOR UPDATE;", true);

    return is_array($result) ? $result : false;
  }

  /**
   * DELETE
   *
   * @param $fleet_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_fleet_delete($fleet_id) {
    $fleet_id_safe = idval($fleet_id);
    if (!empty($fleet_id_safe)) {
      /** @noinspection SqlResolve */
      $result = doquery("DELETE FROM `{{fleets}}` WHERE `fleet_id` = {$fleet_id_safe} LIMIT 1;");
    } else {
      $result = false;
    }

    return $result;
  }


  /**
   * LOCK - Lock all records which can be used with mission
   *
   * @param $mission_data
   * @param $fleet_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_fleet_lock_flying($fleet_id, &$mission_data) {
    // Тупо лочим всех юзеров, чьи флоты летят или улетают с координат отбытия/прибытия $fleet_row
    // Что бы делать это умно - надо учитывать fleet_mess во $fleet_row и в таблице fleets
    $fleet_id_safe = idval($fleet_id);

    $query = [];
    /** @noinspection SqlResolve */
    $query[] = "SELECT 1 FROM `{{fleets}}` AS f";
    // Блокировка всех прилетающих и улетающих флотов, если нужно
    $mission_data['dst_fleets'] ? $query[] = 'LEFT JOIN `{{fleets}}` AS fd ON fd.fleet_end_planet_id = f.fleet_end_planet_id OR fd.fleet_start_planet_id = f.fleet_end_planet_id' : false;

    $mission_data['dst_user'] || $mission_data['dst_planet'] ? $query[] = 'LEFT JOIN `{{users}}` AS ud ON ud.id = f.fleet_target_owner' : false;
    $mission_data['dst_planet'] ? $query[] = 'LEFT JOIN `{{planets}}` AS pd ON pd.id = f.fleet_end_planet_id' : false;

    $mission_data['src_user'] || $mission_data['src_planet'] ? $query[] = 'LEFT JOIN `{{users}}` AS us ON us.id = f.fleet_owner' : false;
    $mission_data['src_planet'] ? $query[] = 'LEFT JOIN `{{planets}}` AS ps ON ps.id = f.fleet_start_planet_id' : false;

    $query[] = "WHERE f.fleet_id = {$fleet_id_safe} GROUP BY 1 FOR UPDATE";

    return doquery(implode(' ', $query));
  }



  /* FLEET LIST & COUNT CRUD ===========================================================================================*/
  /**
   * COUNT - Get fleet count by condition
   *
   * @param string $where_safe
   *
   * @return int
   */
  public static function db_fleet_count($where_safe) {
    /** @noinspection SqlResolve */
    $result = doquery("SELECT COUNT(`fleet_id`) as 'fleet_count' FROM `{{fleets}}` WHERE {$where_safe}", true);

    return !empty($result['fleet_count']) ? intval($result['fleet_count']) : 0;
  }


  /**
   * LIST - Get fleet list by condition
   *
   * @param string $where_safe
   *
   * @return array[]
   *
   * TODO - This function should NOT be used to query fleets to all planets - some aggregate function should be used
   */
  public static function db_fleet_list($where_safe, $for_update = DB_SELECT_FOR_UPDATE) {
    $row_list = [];

    $dbq = DbQuery::build()
      ->setTable('fleets');

    if (!empty($where_safe)) {
      $dbq->setWhereArrayDanger([$where_safe]);
    }

    if ($for_update == DB_SELECT_FOR_UPDATE) {
      $dbq->setForUpdate(DbQuery::DB_FOR_UPDATE);
    }

    $query = $dbq->doSelect();

    while ($row = db_fetch($query)) {
      $row_list[$row['fleet_id']] = $row;
    }

    return $row_list;
  }

  /**
   * LIST DELETE
   *
   * @param $owner_id
   *
   * @return array|bool|mysqli_result|null
   * @deprecated
   *
   * TODO - fleets should be deleted by DB itself via InnoDB FOREIHN KEY
   */
  public static function db_fleet_list_delete_by_owner($owner_id) {
    return doquery("DELETE FROM `{{fleets}}` WHERE `fleet_owner` = '{$owner_id}';");
  }

  /**
   * LIST STAT - DEPRECATED
   *
   * @return array|bool|mysqli_result|null
   *
   * TODO - deprecated
   */
  public static function db_fleet_list_query_all_stat() {
    return doquery("SELECT fleet_owner, fleet_array, fleet_resource_metal, fleet_resource_crystal, fleet_resource_deuterium FROM `{{fleets}}`;");
  }



  /* FLEET FUNCTIONS ===================================================================================================*/
  /**
   * Sends fleet back
   *
   * @param $fleet_row
   *
   * @return array|bool|mysqli_result|null
   *
   * TODO - Add another field which mark fleet as processed/completed task on destination point
   * By this flag fleet dispatcher should immediately return fleet (change STATUS/mess flag) to source planet
   */
  public static function fleet_send_back(&$fleet_row) {
    $fleet_id = round(!empty($fleet_row['fleet_id']) ? $fleet_row['fleet_id'] : $fleet_row);
    if (!$fleet_id) {
      return false;
    }

    $result = static::fleet_update_set($fleet_id, array(
      'fleet_mess' => 1,
    ));

    return $result;
  }


  /* FLEET COUNT FUNCTIONS =============================================================================================*/
  /**
   * Get flying fleet count
   *
   * @param int $player_id - Player ID
   * @param int $mission_id - mission ID. "0" means "all"
   *
   * @return int
   *
   * TODO - Player lock should be issued before to prevent fleet number change
   */
  public static function fleet_count_flying($player_id, $mission_id = 0) {
    $player_id_safe = idval($player_id);
    if (!empty($player_id_safe)) {
      $mission_id_safe = intval($mission_id);
      $result = static::db_fleet_count(
        "`fleet_owner` = {$player_id_safe}" .
        ($mission_id_safe ? " AND `fleet_mission` = {$mission_id_safe}" : '')
      );
    } else {
      $result = 0;
    }

    return $result;
  }

  /**
   * Returns amount of incoming fleets to planet
   *
   * @param int $galaxy
   * @param int $system
   * @param int $planet
   *
   * @return int
   *
   * TODO - Через fleet_list_by_planet_coords() ????
   */
  public static function fleet_count_incoming($galaxy, $system, $planet) {
    return static::db_fleet_count(
      "(`fleet_start_galaxy` = {$galaxy} AND `fleet_start_system` = {$system} AND `fleet_start_planet` = {$planet})
    OR
    (`fleet_end_galaxy` = {$galaxy} AND `fleet_end_system` = {$system} AND `fleet_end_planet` = {$planet})"
    );
  }

  /* FLEET LIST FUNCTIONS =============================================================================================*/
  /**
   * Get fleet list by owner
   *
   * @param int $fleet_owner_id - Fleet owner record/ID. Can't be empty
   *
   * @return array[]
   */
  public static function fleet_list_by_owner_id($fleet_owner_id) {
    $fleet_owner_id_safe = idval($fleet_owner_id);

    return $fleet_owner_id_safe ? static::db_fleet_list("`fleet_owner` = {$fleet_owner_id_safe}", DB_SELECT_PLAIN) : array();
  }

  /**
   * Get fleet list flying/returning to planet/system coordinates
   *
   * @param int $galaxy
   * @param int $system
   * @param int $planet - planet position. "0" means "any"
   * @param int $planet_type - planet type. "PT_ALL" means "any type"
   *
   * @return array
   * TODO - safe params
   */
  public static function fleet_list_by_planet_coords($galaxy, $system, $planet = 0, $planet_type = PT_ALL, $for_phalanx = false) {
    return static::db_fleet_list(
      "(
    fleet_start_galaxy = {$galaxy}
    AND fleet_start_system = {$system}" .
      ($planet ? " AND fleet_start_planet = {$planet}" : '') .
      ($planet_type != PT_ALL ? " AND fleet_start_type = {$planet_type}" : '') .
      ($for_phalanx ? '' : " AND fleet_mess = 1") .
      ")
    OR
    (
    fleet_end_galaxy = {$galaxy}
    AND fleet_end_system = {$system}" .
      ($planet ? " AND fleet_end_planet = {$planet}" : '') .
      ($planet_type != PT_ALL ? " AND fleet_end_type = {$planet_type} " : '') .
      ($for_phalanx ? '' : " AND fleet_mess = 0") .
      ")"
      , DB_SELECT_PLAIN
    );
  }

  /**
   * Fleets on hold on planet orbit
   *
   * @param $fleet_row
   * @param $ube_time
   *
   * @return array
   *
   * TODO - safe params
   */
  public static function fleet_list_on_hold($galaxy, $system, $planet, $planet_type, $ube_time) {
    return static::db_fleet_list(
      "`fleet_end_galaxy` = {$galaxy}
    AND `fleet_end_system` = {$system}
    AND `fleet_end_planet` = {$planet}
    AND `fleet_end_type` = {$planet_type}
    AND `fleet_start_time` <= {$ube_time}
    AND `fleet_end_stay` >= {$ube_time}
    AND `fleet_mess` = 0"
      , DB_SELECT_FOR_UPDATE
    );
  }

  /**
   * Get aggressive fleet list of chosen player on selected planet
   *
   * @param $fleet_owner_id
   * @param $planet_row
   *
   * @return array
   */
  public static function fleet_list_bashing($fleet_owner_id, $planet_row) {
    return static::db_fleet_list(
      "`fleet_end_galaxy` = {$planet_row['galaxy']}
    AND `fleet_end_system` = {$planet_row['system']}
    AND `fleet_end_planet` = {$planet_row['planet']}
    AND `fleet_end_type`   = {$planet_row['planet_type']}
    AND `fleet_owner` = {$fleet_owner_id}
    AND `fleet_mission` IN (" . MT_ATTACK . "," . MT_AKS . "," . MT_DESTROY . ")
    AND `fleet_mess` = 0"
      , DB_SELECT_FOR_UPDATE
    );
  }

  /**
   * Get fleets in group
   *
   * @param $group_id
   *
   * @return array
   */
  public static function fleet_list_by_group($group_id) {
    return static::db_fleet_list("`fleet_group` = {$group_id}", DB_SELECT_FOR_UPDATE);
  }



  /* MISSILE CRUD *******************************************************************************************************/
  /* MISSILE LIST & COUNT CRUD =========================================================================================*/
  /**
   * LIST - Get missile attack list by condition
   *
   * @param string $where - WHERE condition - SQL SAFE!
   * @param bool   $for_update - lock record with FOR UPDATE statement
   *
   * @return array - lift of fleet records from DB
   */
  public static function db_missile_list($where, $for_update = DB_SELECT_FOR_UPDATE) {
    $row_list = [];

    /** @noinspection SqlResolve */
    $query = doquery(
      "SELECT * FROM `{{iraks}}`" .
      (!empty($where) ? " WHERE {$where}" : '') .
      ($for_update == DB_SELECT_FOR_UPDATE ? " FOR UPDATE" : '')
    );
    while ($row = db_fetch($query)) {
      $row_list[$row['id']] = $row;
    }

    return $row_list;
  }



  /* FLEET/MISSILES LIST FUNCTIONS =====================================================================================*/
  /**
   * Get fleet and missile list by coordinates
   *
   * @param array $coordinates
   * @param bool  $for_phalanx - If true - this is phalanx scan so limiting output with fleet_mess
   *
   * @return array
   */
  public static function fleet_and_missiles_list_by_coordinates($coordinates, $for_phalanx = false) {
    if (empty($coordinates) || !is_array($coordinates)) {
      return array();
    }

    $fleet_db_list = static::fleet_list_by_planet_coords($coordinates['galaxy'], $coordinates['system'], $coordinates['planet'], $coordinates['planet_type'], $for_phalanx);

    $missile_db_list = static::db_missile_list(
      "(
      fleet_start_galaxy = {$coordinates['galaxy']}
      AND fleet_start_system = {$coordinates['system']}
      AND fleet_start_planet = {$coordinates['planet']}
      AND fleet_start_type = {$coordinates['planet_type']}
    )
    OR
    (
      fleet_end_galaxy = {$coordinates['galaxy']}
      AND fleet_end_system = {$coordinates['system']}
      AND fleet_end_planet = {$coordinates['planet']}
      AND fleet_end_type = {$coordinates['planet_type']}
    )"
      , DB_SELECT_PLAIN
    );

    missile_list_convert_to_fleet($missile_db_list, $fleet_db_list);

    return $fleet_db_list;
  }

  /**
   * Get fleet and missile list by that flies from player's planets OR to player's planets
   *
   * @param int $owner_id
   *
   * @return array
   */
  public static function fleet_and_missiles_list_incoming($owner_id) {
    $owner_id_safe = idval($owner_id);
    if (empty($owner_id_safe)) {
      return array();
    }

    $where = "`fleet_owner` = '{$owner_id_safe}' OR `fleet_target_owner` = '{$owner_id_safe}'";
    $fleet_db_list = static::db_fleet_list($where, DB_SELECT_PLAIN);
    $missile_db_list = static::db_missile_list($where, DB_SELECT_PLAIN);

    missile_list_convert_to_fleet($missile_db_list, $fleet_db_list);

    return $fleet_db_list;
  }



  /* ACS ****************************************************************************************************************/
  /**
   * Purges ACS list
   */
  public static function db_fleet_acs_purge() {
    DbQuery::build()
      ->setTable('aks')
      ->setWhereArrayDanger(['`id` NOT IN (SELECT DISTINCT `fleet_group` FROM `{{fleets}}`)'])
      ->doDelete();
  }

}
