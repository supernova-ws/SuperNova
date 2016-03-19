<?php

/**
 * Trivial location - with own playerOwnerId, locationType and locationDbId
 */
interface ILocation {
  /**
   * Returns location's player owner ID
   *
   * @return int
   */
  public function getPlayerOwnerId();

  /**
   * Return location type
   *
   * @return int
   */
  public function getLocationType();

  /**
   * Return location ID in DB
   *
   * @return int
   */
  public function getLocationDbId();

}
