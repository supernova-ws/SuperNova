<?php
/**
 * DB fleet, missile and ACS functions
 */

/* FLEET LIST & COUNT *************************************************************************************************/
/* FLEET LIST & COUNT CRUD ===========================================================================================*/
/**
 * COUNT - Get fleet count by condition
 *
 * @param string $where_safe
 *
 * @return int
 */
function db_fleet_count($where_safe) {
  $result = doquery("SELECT COUNT(`fleet_id`) as 'fleet_count' FROM `{{fleets}}` WHERE {$where_safe} FOR UPDATE", true);

  return !empty($result['fleet_count']) ? intval($result['fleet_count']) : 0;
}

/**
 * LIST - Get fleet list by condition
 *
 * @param string $where_safe
 *
 * @return array
 */
function db_fleet_list($where_safe) {
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

/**
 * LIST DELETE
 *
 * @param $owner_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_fleet_list_delete_by_owner($owner_id) {
  return doquery("DELETE FROM `{{fleets}}` WHERE `fleet_owner` = '{$owner_id}';");
}

/**
 * LIST STAT - DEPRECATED
 *
 * @return array|bool|mysqli_result|null
 */
// TODO - deprecated
function db_fleet_list_query_all_stat() {
  return doquery("SELECT fleet_owner, fleet_array, fleet_resource_metal, fleet_resource_crystal, fleet_resource_deuterium FROM {{fleets}};");
}


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
function fleet_count_flying($player_id, $mission_id = 0) {
  $player_id_safe = idval($player_id);
  if(!empty($player_id_safe)) {
    $mission_id_safe = intval($mission_id);
    $result = db_fleet_count(
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
function fleet_count_incoming($galaxy, $system, $planet) {
  return db_fleet_count(
    "(`fleet_start_galaxy` = {$galaxy} AND `fleet_start_system` = {$system} AND `fleet_start_planet` = {$planet})
    OR
    (`fleet_end_galaxy` = {$galaxy} AND `fleet_end_system` = {$system} AND `fleet_end_planet` = {$planet})"
  );
}


/* FLEET LIST FUNCTIONS ----------------------------------------------------------------------------------------------*/
/**
 * Get fleet list by owner
 *
 * @param int $fleet_owner_id - Fleet owner record/ID. Can't be empty
 *
 * @return array[]
 */
function fleet_list_by_owner_id($fleet_owner_id) {
  $fleet_owner_id_safe = idval($fleet_owner_id);

  return $fleet_owner_id_safe ? db_fleet_list("`fleet_owner` = {$fleet_owner_id_safe}") : array();
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
// TODO - safe params
function fleet_list_by_planet_coords($galaxy, $system, $planet = 0, $planet_type = PT_ALL, $for_phalanx = false) {
  return db_fleet_list(
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
 * Fleets on hold on planet orbit
 *
 * @param $fleet_row
 * @param $ube_time
 *
 * @return array
 */
// TODO - safe params
function fleet_list_on_hold($galaxy, $system, $planet, $planet_type, $ube_time) {
  return db_fleet_list(
    "`fleet_end_galaxy` = {$galaxy}
    AND `fleet_end_system` = {$system}
    AND `fleet_end_planet` = {$planet}
    AND `fleet_end_type` = {$planet_type}
    AND `fleet_start_time` <= {$ube_time}
    AND `fleet_end_stay` >= {$ube_time}
    AND `fleet_mess` = 0"
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
function fleet_list_bashing($fleet_owner_id, $planet_row) {
  return db_fleet_list(
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
 * Gets active fleets on current tick for Flying Fleet Handler
 *
 * @return array
 */
function fleet_list_current_tick() {
  return db_fleet_list(
    "
    (`fleet_start_time` <= " . SN_TIME_NOW . " AND `fleet_mess` = 0)
    OR
    (`fleet_end_stay` <= " . SN_TIME_NOW . " AND `fleet_end_stay` > 0 AND `fleet_mess` = 0)
    OR
    (`fleet_end_time` <= " . SN_TIME_NOW . ")");
}

/**
 * Get fleets in group
 *
 * @param $group_id
 *
 * @return array
 */
function fleet_list_by_group($group_id) {
  return db_fleet_list("`fleet_group` = {$group_id}");
}



/* MISSILE CRUD *******************************************************************************************************/
/* MISSILE LIST & COUNT CRUD =========================================================================================*/
/**
 * LIST - Get missile attack list by condition
 *
 * @param string $where
 *
 * @return array
 */
function db_missile_list($where) {
  $row_list = array();

  $query = doquery(
    "SELECT * FROM `{{iraks}}`" .
    (!empty($where) ? " WHERE {$where}" : '') .
    " FOR UPDATE;");
  while($row = db_fetch($query)) {
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
function fleet_and_missiles_list_by_coordinates($coordinates, $for_phalanx = false) {
  if(empty($coordinates) || !is_array($coordinates)) {
    return array();
  }

  $fleet_db_list = fleet_list_by_planet_coords($coordinates['galaxy'], $coordinates['system'], $coordinates['planet'], $coordinates['planet_type'], $for_phalanx);

  $missile_db_list = db_missile_list(
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
function fleet_and_missiles_list_incoming($owner_id) {
  $owner_id_safe = idval($owner_id);
  if(empty($owner_id_safe)) {
    return array();
  }

  $where = "`fleet_owner` = '{$owner_id_safe}' OR `fleet_target_owner` = '{$owner_id_safe}'";
  $fleet_db_list = db_fleet_list($where);
  $missile_db_list = db_missile_list($where);

  missile_list_convert_to_fleet($missile_db_list, $fleet_db_list);

  return $fleet_db_list;
}


/* ACS ****************************************************************************************************************/
/**
 * @param $aks_id
 *
 * @return array|bool|mysqli_result|null
 */
function db_acs_get_by_group_id($aks_id) {
  // TODO - safe
  return doquery("SELECT * FROM {{aks}} WHERE id = '{$aks_id}' LIMIT 1;", true);
}

/**
 * Purges AKS list
 */
function db_fleet_aks_purge() {
  doquery('DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});');
}