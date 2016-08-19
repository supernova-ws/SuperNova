<?php

namespace Mission;


class Spy extends Mission {
  /**
   * @var int
   */
  public $type = MT_SPY;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkSpiesOnly'       => FLIGHT_SHIPS_ONLY_SPIES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
