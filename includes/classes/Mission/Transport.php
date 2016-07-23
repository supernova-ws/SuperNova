<?php

namespace Mission;


class Transport extends Mission {
  /**
   * @var int
   */
  public $type = MT_TRANSPORT;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
