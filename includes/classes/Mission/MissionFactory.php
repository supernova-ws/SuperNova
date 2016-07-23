<?php

namespace Mission;

use \Fleet;
use \ExceptionFleetInvalid;

/**
 * User: Gorlum
 * Date: 23.07.2016
 * Time: 14:35
 */
class MissionFactory {
  public static $missions = array(
    // MT_NONE,
    MT_EXPLORE   => 'Explore',
    MT_COLONIZE  => 'Colonize',
    MT_RECYCLE   => 'Recycle',
    MT_RELOCATE  => 'Relocate',
    MT_TRANSPORT => 'Transport',
    MT_HOLD      => 'Hold',
    MT_MISSILE   => 'Missile',
    MT_SPY       => 'Spy',
    MT_ATTACK    => 'Attack',
    MT_ACS       => 'Acs',
    MT_DESTROY   => 'Destroy',
  );

  /**
   * @param int   $missionType
   * @param Fleet $fleet
   *
   * @return Mission
   * @throws ExceptionFleetInvalid
   */
  public static function build($missionType, $fleet) {
    if (!empty(self::$missions[$missionType]) && class_exists($className = __NAMESPACE__ . '\\' . self::$missions[$missionType])) {
      $result = new $className($fleet);
    } else {
      throw new ExceptionFleetInvalid("Mission type {$missionType} unknown", FLIGHT_MISSION_UNKNOWN);
    }

    return $result;
  }

}
