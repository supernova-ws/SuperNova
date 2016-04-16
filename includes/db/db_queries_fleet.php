<?php
/**
 * DB ACS functions
 */

/* ACS ****************************************************************************************************************/
/**
 * @param $aks_id
 *
 * @return array
 */
function db_acs_get_by_group_id($aks_id) {
  // TODO - safe
  $aks_id = intval($aks_id);
  $result = doquery("SELECT * FROM {{aks}} WHERE id = '{$aks_id}' LIMIT 1 FOR UPDATE;", true);

  return is_array($result) && !empty($result) ? $result : array();
}

/**
 * Purges AKS list
 */
// USED AS CALLABLE - SEARCH FOR STRING!!!!!!!
function db_fleet_aks_purge() {
  doquery('DELETE FROM `{{aks}}` WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM `{{fleets}}`);');
}

function db_missile_insert($target_coord, $user, $planetrow, $arrival, $fleet_ship_count, $target_structure) {
  doquery(
    "INSERT INTO `{{iraks}}` SET
     `fleet_target_owner` = '{$target_coord['id_owner']}', `fleet_end_galaxy` = '{$target_coord['galaxy']}', `fleet_end_system` = '{$target_coord['system']}', `fleet_end_planet` = '{$target_coord['planet']}',
     `fleet_owner` = '{$user['id']}', `fleet_start_galaxy` = '{$planetrow['galaxy']}', `fleet_start_system` = '{$planetrow['system']}', `fleet_start_planet` = '{$planetrow['planet']}',
     `fleet_end_time` = '{$arrival}', `fleet_amount` = '{$fleet_ship_count}', `primaer` = '{$target_structure}';"
  );
}


/**
 * @param $fleetRow
 */
function db_missile_delete($fleetRow) {
  doquery("DELETE FROM {{iraks}} WHERE id = '{$fleetRow['id']}';");
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_missile_list_by_arrival() {
  $iraks = doquery("SELECT * FROM {{iraks}} WHERE `fleet_end_time` <= " . SN_TIME_NOW . " FOR UPDATE;");

  return $iraks;
}


/**
 * @param $user
 * @param $planet_dst
 * @param $time_limit
 *
 * @return array|bool|mysqli_result|null
 */
function db_bashing_list_get($user, $planet_dst, $time_limit) {
  $query = doquery("SELECT bashing_time FROM {{bashing}} WHERE bashing_user_id = {$user['id']} AND bashing_planet_id = {$planet_dst['id']} AND bashing_time >= {$time_limit};");

  return $query;
}

/**
 * @return array|bool|mysqli_result|null
 */
function db_acs_get_list() {
  $aks_madnessred = doquery('SELECT * FROM {{aks}};');

  return $aks_madnessred;
}

/**
 * @param $fleetid
 *
 * @return array|bool|mysqli_result|null
 */
function db_acs_get_by_fleet($fleetid) {
  $aks = doquery("SELECT * from `{{aks}}` WHERE `flotten` = {$fleetid} LIMIT 1;", '', true);

  return $aks;
}

/**
 * @param $fleetid
 * @param $user
 * @param $objFleet
 */
function db_acs_insert($fleetid, $user, $objFleet) {
  doquery("INSERT INTO {{aks}} SET
          `name` = '" . db_escape(classLocale::$lang['flt_acs_prefix'] . $fleetid) . "',
          `teilnehmer` = '" . $user['id'] . "',
          `flotten` = '" . $fleetid . "',
          `ankunft` = '" . $objFleet->time_arrive_to_target . "',
          `galaxy` = '" . $objFleet->fleet_end_galaxy . "',
          `system` = '" . $objFleet->fleet_end_system . "',
          `planet` = '" . $objFleet->fleet_end_planet . "',
          `planet_type` = '" . $objFleet->fleet_end_type . "',
          `eingeladen` = '" . $user['id'] . "',
          `fleet_end_time` = '" . $objFleet->time_return_to_source . "'");
}

/**
 * @param $userToAddID
 * @param $fleetid
 *
 * @return array|bool|mysqli_result|null
 */
function db_acs_update($userToAddID, $fleetid) {
  return doquery("UPDATE `{{aks}}` SET `eingeladen` = concat(`eingeladen`, ',{$userToAddID}') WHERE `flotten` = {$fleetid};");
}


/**
 * @param $fleet_group_id_list
 */
function db_acs_delete_by_list($fleet_group_id_list) {
  doquery("DELETE FROM {{aks}} WHERE `id` IN ({$fleet_group_id_list})");
}

/**
 * @param $bashing_list
 */
function db_bashing_insert($bashing_list) {
  doquery("INSERT INTO {{bashing}} (bashing_user_id, bashing_planet_id, bashing_time) VALUES {$bashing_list};");
}
