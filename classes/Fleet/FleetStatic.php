<?php
/**
 * Created by Gorlum 21.03.2018 13:27
 */

namespace Fleet;

use SN;

class FleetStatic {

  /**
   * @param array $planetIds
   * @param int   $time
   *
   * @return \mysqli_result|false
   */
  public static function dbFleetsOnHoldOnPlanetsByIds($planetIds, $time = SN_TIME_NOW) {
    return SN::$db->doquery(
      "SELECT `{{fleets}}`.*
      FROM `{{fleets}}`
        LEFT JOIN `{{users}}` ON id = fleet_owner
      WHERE
        fleet_end_planet_id IN (" . implode(',', $planetIds) . ")
        AND fleet_mess = 0
        AND fleet_start_time <= " . $time . "
        AND fleet_end_stay >= " . $time . "
      FOR UPDATE"
    );

  }
}