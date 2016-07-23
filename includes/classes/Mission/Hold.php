<?php

namespace Mission;


class Hold extends Mission {
  /**
   * @var int
   */
  public $type = MT_HOLD;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
