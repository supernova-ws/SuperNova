<?php

namespace Mission;

class Acs extends Mission {
  /**
   * @var int
   */
  public $type = MT_ACS;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
