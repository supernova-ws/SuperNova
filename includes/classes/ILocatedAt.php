<?php

/**
 * Sub-location - location which has parent location
 */
interface ILocatedAt extends ILocation {

  /**
   * Set parent location object to use by location functions
   *
   * @param ILocation|null $location
   */
  public function setLocatedAt($location);

  /**
   * Returns parent object in which this object located
   *
   * @return ILocation|null
   */
  public function getLocatedAt();

}
