<?php
/**
 * Created by Gorlum 03.12.2017 14:59
 */

namespace Fixtures;

class UnitInfo {

  protected $unitId;

  protected $unitInfo = [];

  public static function build($unitId = 0) {
    return new static($unitId);
  }

  public function __construct($unitId) {
    $this->unitId = $unitId;

    return $this;
  }

  public function type($unitType) {
    $this->unitInfo[P_UNIT_TYPE] = $unitType;

    return $this;
  }

  public function bonus($bonusType, $bonusPower = null) {
    $this->unitInfo[P_BONUS_TYPE] = $bonusType;
    if ($bonusPower !== null) {
      $this->unitInfo[P_BONUS_VALUE] = $bonusPower;
    }

    return $this;
  }

  public function asArray() {
    return $this->unitInfo;
  }

  /**
   * Installs unit into current unit lists
   */
  public function install() {
    global $sn_data;

    $sn_data[$this->unitId] = $this->asArray();

    return $this;
  }

}
