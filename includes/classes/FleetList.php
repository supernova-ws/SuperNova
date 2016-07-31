<?php

/**
 * Class FleetList
 *
 * @method Fleet offsetGet($offset)
 * @property Fleet[] $_container
 */
class FleetList extends ContainerArrayOfObject {

  public function __construct() {

  }

  /**
   * @return Fleet
   */
  public function _createElement() {
    return new Fleet();
  }

  /* FLEET LIST DB ACCESS ============================================================================================*/
  /**
   * LIST - Get fleet list by condition
   *
   * @param string $where_safe
   *
   * @return array - ID of added fleets
   *
   * @version 41a50.102
   */
  public function dbLoadWhere($where_safe = '') {
    $fleets_added = array();

    $query = classSupernova::$db->doSelect(
      "SELECT * FROM `{{fleets}}`" .
      (!empty($where_safe) ? " WHERE {$where_safe}" : '') .
      " FOR UPDATE;"
    );
    while($row = db_fetch($query)) {
      /**
       * @var Fleet $fleet
       */
      $fleet = $this->_createElement();
      $fleet->dbRowParse($row);

      if(isset($this[$fleet->dbId])) {
        // Нужно ли ????
        classSupernova::$debug->error('Fleet list already set');
      }

      $this[$fleet->dbId] = $fleet;
      $fleets_added[$fleet->dbId] = $fleet->dbId;
    }

    return $fleets_added;
  }
  /**
   * LIST - Get fleet list by condition
   *
   * @param string $where_safe
   *
   * @return static
   *
   * @version 41a50.102
   */
  // DEPRECATED
  public static function dbGetFleetList($where_safe = '') {
    $fleetList = new static();
    $fleetList->dbLoadWhere($where_safe);

    return $fleetList;
  }
  /**
   * LIST DELETE
   *
   * @param $owner_id
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_fleet_list_delete_by_owner($owner_id) {
    DBStaticUnit::db_unit_list_delete($owner_id, LOC_FLEET);
    return classSupernova::$db->doDelete("DELETE FROM `{{fleets}}` WHERE `fleet_owner` = '{$owner_id}';");
  }
  /**
   * LIST STAT - DEPRECATED
   *
   * @return array|bool|mysqli_result|null
   */
  // Для потокового чтения данных
  public static function dbQueryAllId() {
    return classSupernova::$db->doSelect("SELECT `fleet_id` FROM `{{fleets}}`;");
  }
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





  // STATICS ***********************************************************************************************************
  /* FLEET LIST FUNCTIONS ----------------------------------------------------------------------------------------------*/

  /**
   * Gets active fleets on current tick for Flying Fleet Handler
   *
   * @return static
   *
   * @version 41a50.102
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
   * @version 41a50.102
   */
  public static function dbGetFleetListBashing($fleet_owner_id, array $planet_row) {
    return static::dbGetFleetList(
      "`fleet_end_galaxy` = {$planet_row['galaxy']}
    AND `fleet_end_system` = {$planet_row['system']}
    AND `fleet_end_planet` = {$planet_row['planet']}
    AND `fleet_end_type`   = {$planet_row['planet_type']}
    AND `fleet_owner` = {$fleet_owner_id}
    AND `fleet_mission` IN (" . MT_ATTACK . "," . MT_ACS . "," . MT_DESTROY . ")
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
   * Get fleet and missile list by coordinates
   *
   * @param array $coordinates
   * @param bool  $for_phalanx - If true - this is phalanx scan so limiting output with fleet_mess
   *
   * @return static|array
   */
  public static function dbGetFleetListAndMissileByCoordinates($coordinates, $for_phalanx = false) {
    if(empty($coordinates) || !is_array($coordinates)) {
      return array();
    }

    $objFleetList = FleetList::dbGetFleetListByCoordinates($coordinates['galaxy'], $coordinates['system'], $coordinates['planet'], $coordinates['planet_type'], $for_phalanx);
    $objFleetList->dbMergeMissileList("(
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
    )");

    return $objFleetList;
  }

  public static function EMULATE_flt_get_fleets_to_planet($planet) {
    $planet_fleets = array();
    $fleet_db_list = FleetList::dbGetFleetListAndMissileByCoordinates($planet);
    /**
     * @var Fleet[] $array_of_Fleet
     */
    $array_of_Fleet = array();
    if(!empty($fleet_db_list) && $fleet_db_list->count()) {
      foreach($fleet_db_list->_container as $fleet_id => $objFleet) {
        $array_of_Fleet[$fleet_id] = $objFleet;
      }
      $planet_fleets = flt_get_fleets_to_planet_by_array_of_Fleet($array_of_Fleet);
    }

    return $planet_fleets;
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
   * LIST - Get missile attack list by condition
   *
   * @param string $where
   */
  public function dbMergeMissileList($where) {
    $query = classSupernova::$db->doSelect(
      "SELECT * FROM `{{iraks}}`" .
      (!empty($where) ? " WHERE {$where}" : '') .
      " FOR UPDATE;");
    while($missile_db_row = db_fetch($query)) {
      /**
       * @var Fleet $objFleet
       */
      $objFleet = $this->_createElement();
      $objFleet->parse_missile_db_row($missile_db_row);

      $this[$objFleet->dbId] = $objFleet;
    }
  }



  /**
   * Get fleet and missile list by coordinates
   *
   * @param int $owner_id
   *
   * @return static
   */
  public static function dbGetFleetListAndMissileINCOMING($owner_id) {
    $owner_id_safe = idval($owner_id);
    if(empty($owner_id_safe)) {
      return array();
    }

    $where = "`fleet_owner` = '{$owner_id_safe}' OR `fleet_target_owner` = '{$owner_id_safe}'";

    $objFleetList = FleetList::dbGetFleetList($where);
    $objFleetList->dbMergeMissileList($where);

    return $objFleetList;
  }




  /* FLEET LIST & COUNT ***********************************************************************************************/
  /* FLEET LIST & COUNT CRUD =========================================================================================*/




  /* FLEET LIST & COUNT HELPERS ========================================================================================*/
  /* FLEET COUNT FUNCTIONS ---------------------------------------------------------------------------------------------*/

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
