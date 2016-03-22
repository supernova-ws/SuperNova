<?php
/**
 * DB ACS functions
 */

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
// USED AS CALLABLE - SEARCH FOR STRING!!!!!!!
function db_fleet_aks_purge() {
  doquery('DELETE FROM `{{aks}}` WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM `{{fleets}}`);');
}

function db_missile_insert($target_row, $target_coord, $user, $planetrow, $arrival, $fleet_ship_count, $target_structure) {
  doquery(
    "INSERT INTO `{{iraks}}` SET
     `fleet_target_owner` = '{$target_row['id_owner']}', `fleet_end_galaxy` = '{$target_coord['galaxy']}', `fleet_end_system` = '{$target_coord['system']}', `fleet_end_planet` = '{$target_coord['planet']}',
     `fleet_owner` = '{$user['id']}', `fleet_start_galaxy` = '{$planetrow['galaxy']}', `fleet_start_system` = '{$planetrow['system']}', `fleet_start_planet` = '{$planetrow['planet']}',
     `fleet_end_time` = '{$arrival}', `fleet_amount` = '{$fleet_ship_count}', `primaer` = '{$target_structure}';"
  );
}
