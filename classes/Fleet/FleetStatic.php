<?php
/**
 * Created by Gorlum 21.03.2018 13:27
 */

namespace Fleet;

class FleetStatic {

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
