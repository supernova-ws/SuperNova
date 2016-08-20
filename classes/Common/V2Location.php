<?php
/**
 * Created by Gorlum 21.08.2016 1:12
 */

namespace Common;


class V2Location {
  protected $locationType = LOC_NONE;
  protected $locationId = 0;
  protected $playerId = 0;


  /**
   * V2Location constructor.
   *
   * @param int $locationType
   * @param int $locationId
   * @param int $playerId
   */
  public function __construct($locationType = LOC_NONE, $locationId = 0, $playerId = 0) {
    $this->setLocationType($locationType);
    $this->setLocationId($locationId);
    $this->setLocationPlayerId($playerId);
  }

  public function getLocationType() {
    return $this->locationType;
  }

  public function getLocationId() {
    return $this->locationId;
  }

  public function getLocationPlayerId() {
    return $this->playerId;
  }

  public function setLocationType($value) {
    $this->locationType = $value;
  }

  public function setLocationId($value) {
    $this->locationId = $value;
  }

  public function setLocationPlayerId($value) {
    $this->playerId = $value;
  }

}
