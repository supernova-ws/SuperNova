<?php

class DBStaticFleetACS {

  /* ACS ****************************************************************************************************************/
  /**
   * @param $aks_id
   *
   * @return array
   */
  public static function db_acs_get_by_group_id($aks_id) {
    // TODO - safe
    $aks_id = intval($aks_id);
    $result = doquery("SELECT * FROM {{aks}} WHERE id = '{$aks_id}' LIMIT 1 FOR UPDATE;", true);

    return is_array($result) && !empty($result) ? $result : array();
  }

  /**
   * Purges AKS list
   */
// USED AS CALLABLE - SEARCH FOR STRING!!!!!!!
  public static function db_fleet_aks_purge() {
    classSupernova::$db->doDelete("DELETE FROM `{{aks}}` WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM `{{fleets}}`);");
  }

  /**
   * @return array|bool|mysqli_result|null
   */
  public static function db_acs_get_list() {
    $aks_madnessred = doquery('SELECT * FROM {{aks}};');

    return $aks_madnessred;
  }

  /**
   * @param $fleetid
   *
   * @return array|bool|mysqli_result|null
   */
  public static function db_acs_get_by_fleet($fleetid) {
    $aks = doquery("SELECT * from `{{aks}}` WHERE `flotten` = {$fleetid} LIMIT 1;", '', true);

    return $aks;
  }

  /**
   * @param $fleetid
   * @param $user
   * @param $objFleet
   */
  public static function db_acs_insert($fleetid, $user, $objFleet) {
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
  public static function db_acs_update($userToAddID, $fleetid) {
    return doquery("UPDATE `{{aks}}` SET `eingeladen` = concat(`eingeladen`, ',{$userToAddID}') WHERE `flotten` = {$fleetid};");
  }


  /**
   * @param $fleet_group_id_list
   */
  public static function db_acs_delete_by_list($fleet_group_id_list) {
    classSupernova::$db->doDelete("DELETE FROM {{aks}} WHERE `id` IN ({$fleet_group_id_list})");
  }


}
