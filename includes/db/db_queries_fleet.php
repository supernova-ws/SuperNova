<?php
/**
 * DB missile and ACS functions
 */

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

  $fleet_db_list = FleetList::fleet_list_by_planet_coords($coordinates['galaxy'], $coordinates['system'], $coordinates['planet'], $coordinates['planet_type'], $for_phalanx);

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
  $fleet_db_list = FleetList::db_fleet_list($where);
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
