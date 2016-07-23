<?php
/**
 * User: Gorlum
 * Date: 23.07.2016
 * Time: 15:14
 */

namespace Mission;


class Explore extends Mission {
  /**
   * @var int
   */
  public $type = MT_EXPLORE;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkNotOnlySpies'    => FLIGHT_SHIPS_NOT_ONLY_SPIES,
    'checkUnKnownSpace'    => FLIGHT_MISSION_EXPLORE_KNOWN_SPACE, // Vector targeting space beyond MaxPlanet forces MT_EXPLORE mission
    //
    'checkExpeditionsMax'  => FLIGHT_MISSION_EXPLORE_NO_ASTROTECH,
    'checkExpeditionsFree' => FLIGHT_MISSION_EXPLORE_NO_SLOTS,
  );

}
