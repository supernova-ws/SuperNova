<?php
/**
 * Created by Gorlum 23.07.2016 16:44
 */

namespace Mission;


class Missile extends Mission {
  /**
   * @var int
   */
  public $type = MT_MISSILE;

  protected static $conditionsLocal = array(
    'checkOnlyAttackMissiles'      => FLIGHT_MISSION_MISSILE_ONLY_ATTACK,
    'checkKnownSpace'              => FLIGHT_VECTOR_BEYOND_SYSTEM,
    //
    //
    'alwaysFalse'                  => FLIGHT_MISSION_MISSILE_DISABLED,
    // TODO - check no captain
    // TODO - check no resources
    // TODO - check enough fuel
    // TODO - fixed speed
    'checkSameGalaxy'              => FLIGHT_MISSION_MISSILE_DIFFERENT_GALAXY,
    'checkMissileDistance'         => FLIGHT_MISSION_MISSILE_TOO_FAR,
    'checkTargetExists'            => FLIGHT_VECTOR_TARGET_NOT_EXISTS,
    'checkTargetIsPlanet'          => FLIGHT_MISSION_MISSILE_ONLY_PLANET,
    'checkSiloLevel'               => FLIGHT_MISSION_MISSILE_NO_SILO,
    'checkMissileTarget'           => FLIGHT_MISSION_MISSILE_WRONG_STRUCTURE,
    // Check target player activity
    'checkPlayerInactiveOrNotNoob' => FLIGHT_PLAYER_NOOB,
//    // FLIGHT_MISSION_MISSILE_NO_MISSILES
//    'forceMissionMissile'          => array(
//      true  => FLIGHT_ALLOWED,
//      false => FLIGHT_SHIPS_ONLY_MISSILES,
//    ),

//    'checkNotOnlySpies'    => FLIGHT_SHIPS_NOT_ONLY_SPIES,
//    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
//    'checkExpeditionsMax'  => FLIGHT_MISSION_EXPLORE_NO_ASTROTECH,
//    'checkExpeditionsFree' => FLIGHT_MISSION_EXPLORE_NO_SLOTS,
  );

}
