<?php

namespace Mission;

class Destroy extends Mission {
  /**
   * @var int
   */
  public $type = MT_DESTROY;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
