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
 * property int|string $dbId Entity DB ID
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
 * @property            $features - unit feature list
 *
 * @package V2Unit
 */
class V2UnitContainer extends \Entity\KeyedContainer {

  protected $featureLocator;

  public function setFeatureLocator($featureLocator) {
    $this->featureLocator = $featureLocator;
  }

}
