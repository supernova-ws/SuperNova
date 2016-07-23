<?php

namespace Mission;


class Relocate extends Mission {
  /**
   * @var int
   */
  public $type = MT_RELOCATE;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
