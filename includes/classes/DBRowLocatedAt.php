<?php

/**
 * DBRow which have parent DBLocation row
 * Such objects always got playerOwnerId from parent location
 *
 * Examples: Fleet located on Planet, Planet located on Player, UnitList located on Player/Fleet/Planet
 */

abstract class DBRowLocatedAt extends DBRowLocation implements ILocatedAt {

  // DBRowLocation inheritance *****************************************************************************************

  public function getPlayerOwnerId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getPlayerOwnerId() : null;
  }

//  public function getLocationType() {
//    return is_object($this->locatedAt) ? $this->locatedAt->getLocationType() : 0;
//  }
//
//  public function getLocationDbId() {
//    return is_object($this->locatedAt) ? $this->locatedAt->getLocationDbId() : null;
//  }


  // ILocatedAt implementation *****************************************************************************************

  /**
   * @var ILocation $locatedAt
   */
  protected $locatedAt = null;

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


}
