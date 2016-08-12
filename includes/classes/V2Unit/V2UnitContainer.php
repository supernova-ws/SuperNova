<?php
/**
 * Created by Gorlum 10.08.2016 14:25
 */

namespace V2Unit;

/**
 * Class V2UnitContainer
 *
 * @method V2UnitModel getModel()
 *
 * @property int|string $dbId Entity DB ID
 * @property int|string $playerOwnerId
 * @property int        $locationType
 * @property int|string $locationId
 * @property int        $type
 * @property int        $snId
 * @property int        $level - level of unit for DB: $count for stackable units, $level - fon unstackable units
 * @property \DateTime  $timeStart
 * @property \DateTime  $timeFinish
 *
 * @property bool       $isStackable
 * @property string     $locationDefaultType
 * @property int        $bonusType
 * @property array      $unitInfo - full info about unit
 *
 * @package V2Unit
 */
class V2UnitContainer extends \EntityContainer {

  public function isEmpty() {
    return
      empty($this->playerOwnerId)
      ||
      is_null($this->locationType)
      ||
      $this->locationType === LOC_NONE
      ||
      empty($this->locationId)
      ||
      empty($this->type)
      ||
      empty($this->snId)
      ||
      empty($this->level);
  }

}
