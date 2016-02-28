<?php

/**
 * Class FleetList
 *
 * @method Fleet offsetGet($offset)
 * @property Fleet[] $_container
 */
class FleetList extends ArrayAccessV2 {

  public function __construct() {

  }


  /**
   * @return Fleet
   */
  public function _createElement() {
    return new Fleet();
  }

  // STATICS ***********************************************************************************************************
  /* FLEET LIST FUNCTIONS ----------------------------------------------------------------------------------------------*/
  /**
   * LIST - Get fleet list by condition
   *
   * @param string $where_safe
   *
   * @return static
   *
   * @version 41a5.2
   */
  public static function dbGetFleetList($where_safe = '') {
    $fleetList = new static();

    $query = doquery(
      "SELECT * FROM `{{fleets}}`" .
      (!empty($where_safe) ? " WHERE {$where_safe}" : '') .
      " FOR UPDATE;"
    );
    while($row = db_fetch($query)) {
      /**
       * @var Fleet $objFleet
       */
      $objFleet = $fleetList->_createElement();
      $objFleet->parse_db_row($row);

      $fleetList[$objFleet->db_id] = $objFleet;
    }

    return $fleetList;
  }

  /**
   * Get fleets in group
   *
   * @param int $group_id
   *
   * @return static
   *
   * @version 41a5.2
   */
  public static function dbGetFleetListByGroup($group_id) {
    return static::dbGetFleetList("`fleet_group` = {$group_id}");
  }

  /**
   * Fleets on hold on planet orbit
   *
   * @param Fleet $objFleet
   *
   * @return static
   *
   * @version 41a5.2
   */
  public static function dbGetFleetListOnHoldAtTarget(Fleet $objFleet) {
    return static::dbGetFleetList(
      "`fleet_end_galaxy` = {$objFleet->fleet_end_galaxy}
    AND `fleet_end_system` = {$objFleet->fleet_end_system}
    AND `fleet_end_planet` = {$objFleet->fleet_end_planet}
    AND `fleet_end_type` = {$objFleet->fleet_end_type}
    AND `fleet_start_time` <= {$objFleet->time_arrive_to_target}
    AND `fleet_end_stay` >= {$objFleet->time_arrive_to_target}
    AND `fleet_mess` = 0"
    );
  }

  /**
   * Gets active fleets on current tick for Flying Fleet Handler
   *
   * @return static
   *
   * @version 41a5.2
   */
  public static function dbGetFleetListCurrentTick() {
    return static::dbGetFleetList(
      "
    (`fleet_start_time` <= " . SN_TIME_NOW . " AND `fleet_mess` = 0)
    OR
    (`fleet_end_stay` <= " . SN_TIME_NOW . " AND `fleet_end_stay` > 0 AND `fleet_mess` = 0)
    OR
    (`fleet_end_time` <= " . SN_TIME_NOW . ")");
  }

  /**
   *  Get aggressive fleet list of chosen player on selected planet
   *
   * @param int   $fleet_owner_id
   * @param array $planet_row
   *
   * @return static
   *
   * @version 41a5.2
   */
  public static function dbGetFleetListBashing($fleet_owner_id, array $planet_row) {
    return static::dbGetFleetList(
      "`fleet_end_galaxy` = {$planet_row['galaxy']}
    AND `fleet_end_system` = {$planet_row['system']}
    AND `fleet_end_planet` = {$planet_row['planet']}
    AND `fleet_end_type`   = {$planet_row['planet_type']}
    AND `fleet_owner` = {$fleet_owner_id}
    AND `fleet_mission` IN (" . MT_ATTACK . "," . MT_AKS . "," . MT_DESTROY . ")
    AND `fleet_mess` = 0"
    );
  }

  /**
   * @param $fleet_owner_id
   *
   * @return static
   */
  public static function dbGetFleetListByOwnerId($fleet_owner_id) {
    $fleet_owner_id_safe = idval($fleet_owner_id);

    return $fleet_owner_id_safe ? static::dbGetFleetList("`fleet_owner` = {$fleet_owner_id_safe}") : null;
  }














  /**
   * Get fleet list flying/returning to planet/system coordinates
   *
   * @param int $galaxy
   * @param int $system
   * @param int $planet - planet position. "0" means "any"
   * @param int $planet_type - planet type. "PT_ALL" means "any type"
   *
   * @return static
   */
  // TODO - ОСТАВЛЯЕМ НА ВТОРОЙ ЗАХОД - МНОГО ВСЕГО НАДО МЕНЯТЬ В ОКРУГЕ
  public static function dbGetFleetListByCoordinates($galaxy, $system, $planet = 0, $planet_type = PT_ALL, $for_phalanx = false) {
    return static::dbGetFleetList(
      "(
      fleet_start_galaxy = {$galaxy}
      AND fleet_start_system = {$system}" .
      ($planet ? " AND fleet_start_planet = {$planet}" : '') .
      ($planet_type != PT_ALL ? " AND fleet_start_type = {$planet_type}" : '') .
      ($for_phalanx ? '' : " AND fleet_mess = 1") .
      ") OR (
      fleet_end_galaxy = {$galaxy}
      AND fleet_end_system = {$system}" .
      ($planet ? " AND fleet_end_planet = {$planet}" : '') .
      ($planet_type != PT_ALL ? " AND fleet_end_type = {$planet_type} " : '') .
      ($for_phalanx ? '' : " AND fleet_mess = 0") .
      ")"
    );
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
   */
  // TODO - ОСТАВЛЯЕМ НА ВТОРОЙ ЗАХОД - МНОГО ВСЕГО НАДО МЕНЯТЬ В ОКРУГЕ
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
    );
  }


  /**
   * LIST - Get fleet list by condition
   *
   * @param string $where_safe
   *
   * @return array
   */
  public static function db_fleet_list($where_safe = '') {
    $row_list = array();

    $query = doquery(
      "SELECT * FROM `{{fleets}}`" .
      (!empty($where_safe) ? " WHERE {$where_safe}" : '') .
      " FOR UPDATE;"
    );
    while($row = db_fetch($query)) {
      $row_list[$row['fleet_id']] = $row;
    }

    return $row_list;

  }






  /* FLEET LIST & COUNT ***********************************************************************************************/
  /* FLEET LIST & COUNT CRUD =========================================================================================*/

  /**
   * LIST DELETE
   *
   * @param $owner_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_fleet_list_delete_by_owner($owner_id) {
    return doquery("DELETE FROM `{{fleets}}` WHERE `fleet_owner` = '{$owner_id}';");
  }

  /**
   * LIST STAT - DEPRECATED
   *
   * @return array|bool|mysqli_result|null
   */
// TODO - deprecated
  public static function db_fleet_list_query_all_stat() {
    return doquery("SELECT fleet_owner, fleet_array, fleet_resource_metal, fleet_resource_crystal, fleet_resource_deuterium FROM {{fleets}};");
  }


  /* FLEET LIST & COUNT HELPERS ========================================================================================*/
  /* FLEET COUNT FUNCTIONS ---------------------------------------------------------------------------------------------*/
  /**
   * COUNT - Get fleet count by condition
   *
   * @param string $where_safe
   *
   * @return int
   */
  public static function db_fleet_count($where_safe) {
    $result = doquery("SELECT COUNT(`fleet_id`) as 'fleet_count' FROM `{{fleets}}` WHERE {$where_safe} FOR UPDATE", true);

    return !empty($result['fleet_count']) ? intval($result['fleet_count']) : 0;
  }

  /**
   * Get flying fleet count
   *
   * @param int $player_id - Player ID
   * @param int $mission_id - mission ID. "0" means "all"
   *
   * @return int
   */
  public static function fleet_count_flying($player_id, $mission_id = 0) {
    $player_id_safe = idval($player_id);
    if(!empty($player_id_safe)) {
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
   */
  // TODO - Через fleet_list_by_planet_coords() ????
  public static function fleet_count_incoming($galaxy, $system, $planet) {
    return static::db_fleet_count(
      "(`fleet_start_galaxy` = {$galaxy} AND `fleet_start_system` = {$system} AND `fleet_start_planet` = {$planet})
    OR
    (`fleet_end_galaxy` = {$galaxy} AND `fleet_end_system` = {$system} AND `fleet_end_planet` = {$planet})"
    );
  }


}
