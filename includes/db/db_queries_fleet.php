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
  doquery('DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});');
}
