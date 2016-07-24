<?php
/**
 * User: Gorlum
 * Date: 23.07.2016
 * Time: 19:18
 */

namespace Mission;

use Fleet;

class Colonize extends Mission {
  /**
   * @var int
   */
  public $type = MT_COLONIZE;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkNotOnlySpies'    => FLIGHT_SHIPS_NOT_ONLY_SPIES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
    //
    'checkHaveColonizer'   => FLIGHT_SHIPS_NO_COLONIZER, // Replaces checkNotOnlySpies
    //
    'checkTargetNotExists' => FLIGHT_MISSION_COLONIZE_NOT_EMPTY,
    //
    'checkTargetIsPlanet'  => FLIGHT_MISSION_COLONIZE_NOT_PLANET,
  );

  public function __construct(Fleet $fleet) {
    parent::__construct($fleet);
  }

}
