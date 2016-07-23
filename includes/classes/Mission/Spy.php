<?php

namespace Mission;


class Spy extends Mission {
  /**
   * @var int
   */
  public $type = MT_SPY;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
