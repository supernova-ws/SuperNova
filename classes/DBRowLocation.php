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
   * @var ILocation $locatedAt
   */
  protected $locatedAt = null;

  /**
   * Returns location's player owner ID
   *
   * @return int
   */
  public function getPlayerOwnerId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getPlayerOwnerId() : null;
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


  /**
   * @param ILocation $location
   */
  public function setLocatedAt($location) {
    $this->locatedAt = $location;
  }

  /**
   * @return ILocation
   */
  public function getLocatedAt() {
    return $this->locatedAt;
  }

  public function getLocatedAtType() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationType() : LOC_NONE;
  }

  public function getLocatedAtDbId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationDbId() : null;
  }

}
