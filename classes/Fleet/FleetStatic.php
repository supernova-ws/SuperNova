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

  public static function flt_fleet_speed($user, $fleet, $shipData = []) {
    if (!is_array($fleet)) {
      $fleet = array($fleet => 1);
    }

    $speeds = array();
    if (!empty($fleet)) {
      foreach ($fleet as $ship_id => $amount) {
        if ($amount && in_array($ship_id, sn_get_groups(['fleet', 'missile',]))) {
          $single_ship_data = !empty($shipData[$ship_id]) ? $shipData[$ship_id] : get_ship_data($ship_id, $user);
          $speeds[]         = $single_ship_data['speed'];
        }
      }
    }

    return empty($speeds) ? 0 : min($speeds);
  }

}
