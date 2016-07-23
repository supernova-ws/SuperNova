<?php

namespace Mission;


class Attack extends Mission {
  /**
   * @var int
   */
  public $type = MT_ATTACK;

  protected static $conditionsLocal = array(
    'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
    'checkKnownSpace'      => FLIGHT_VECTOR_BEYOND_SYSTEM,
  );

}
