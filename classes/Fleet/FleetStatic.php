<?php
/**
 * Created by Gorlum 21.03.2018 13:27
 */

namespace Fleet;

use SN;
use Common\EmptyCountableIterator;
use \DBAL\DbMysqliResultIterator;

class FleetStatic {

  /**
   * @param array $planetIds
   * @param int   $time
   *
   * @return DbMysqliResultIterator|EmptyCountableIterator
   */
  public static function dbFleetsOnHoldOnPlanetsByIds($planetIds, $time = SN_TIME_NOW) {
    if(empty($planetIds) || !is_array($planetIds)) {
      return new EmptyCountableIterator();
    }

    return SN::$db->selectIterator("SELECT `{{fleets}}`.*
      FROM `{{fleets}}`
        LEFT JOIN `{{users}}` ON id = fleet_owner
      WHERE
        fleet_end_planet_id IN (" . implode(',', $planetIds) . ")
        AND fleet_mess = 0
        AND fleet_start_time <= " . $time . "
        AND fleet_end_stay >= " . $time . "
      FOR UPDATE");
  }
}
