<?php

/**
 * DBRow with own location attributes
 */
abstract class DBRowLocation extends DBRow implements ILocation {

  /**
   * Type of this location
   *
   * @var int $locationType
   */
  protected static $locationType = LOC_NONE;

  /**
   * Returns location's player owner ID
   *
   * @return int
   */
  public function getPlayerOwnerId() {
    return $this->playerOwnerId;
  }

  /**
   * Return location type
   *
   * @return int
   */
  public function getLocationType() {
    return static::$locationType;
  }

  /**
   * Return location ID in DB
   *
   * @return int
   */
  public function getLocationDbId() {
    return $this->dbId;
  }

}
