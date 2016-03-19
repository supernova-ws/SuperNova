<?php

/**
 * DBRow in ILocatedAt that takes his locationType and locationDbId from parent
 */
abstract class DBRowLocatedAtParent extends DBRowLocatedAt {

  public function getLocationType() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationType() : 0;
  }

  public function getLocationDbId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationDbId() : null;
  }

}