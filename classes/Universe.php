<?php

/**
 * Created by Gorlum 25.03.2023 21:11
 */
class Universe {

  /**
   * @param $from
   * @param $to
   *
   * @return float|int
   */
  public static function distance($from, $to) {
    if ($from['galaxy'] != $to['galaxy']) {
      $distance = abs($from['galaxy'] - $to['galaxy']) * self::getBaseGalaxyDistance();
    } elseif ($from['system'] != $to['system']) {
      $distance = abs($from['system'] - $to['system']) * 5 * 19 + 2700;
    } elseif ($from['planet'] != $to['planet']) {
      $distance = abs($from['planet'] - $to['planet']) * 5 + 1000;
    } else {
      $distance = 5;
    }

    return $distance;
  }

  public static function getBaseGalaxyDistance() {
    return SN::$config->uni_galaxy_distance ? SN::$config->uni_galaxy_distance : UNIVERSE_GALAXY_DISTANCE;
  }

  /**
   * Get fleet flying speed aka... hmph... fleet flying speed
   *
   * @param bool $plain
   *
   * @return float|int
   */
  public static function flt_server_flight_speed_multiplier($plain = false) {
    return getValueFromStorage(UNIT_SERVER_SPEED_FLEET, $plain);
  }

}
