<?php

class DBStaticFleetMissile {

  public static function db_missile_insert($target_coord, $user, $planetrow, $arrival, $fleet_ship_count, $target_structure) {
    classSupernova::$db->doInsert(
      "INSERT INTO `{{iraks}}` SET
     `fleet_target_owner` = '{$target_coord['id_owner']}', `fleet_end_galaxy` = '{$target_coord['galaxy']}', `fleet_end_system` = '{$target_coord['system']}', `fleet_end_planet` = '{$target_coord['planet']}',
     `fleet_owner` = '{$user['id']}', `fleet_start_galaxy` = '{$planetrow['galaxy']}', `fleet_start_system` = '{$planetrow['system']}', `fleet_start_planet` = '{$planetrow['planet']}',
     `fleet_end_time` = '{$arrival}', `fleet_amount` = '{$fleet_ship_count}', `primaer` = '{$target_structure}';"
    );
  }


  /**
   * @param $fleetRow
   */
  public static function db_missile_delete($fleetRow) {
    classSupernova::$db->doDelete("DELETE FROM {{iraks}} WHERE id = '{$fleetRow['id']}';");
  }

  /**
   * @return array|bool|mysqli_result|null
   */
  public static function db_missile_list_by_arrival() {
    $iraks = doquery("SELECT * FROM `{{iraks}}` WHERE `fleet_end_time` <= " . SN_TIME_NOW . " FOR UPDATE;");

    return $iraks;
  }


}