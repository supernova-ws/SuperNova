<?php

namespace Mission;


class Recycle extends Mission {
  /**
   * @var int
   */
  public $type = MT_RECYCLE;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
