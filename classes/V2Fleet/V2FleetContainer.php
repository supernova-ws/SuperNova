<?php
/**
 * Created by Gorlum 17.08.2016 22:09
 */

namespace V2Fleet;


use Common\V2Location;
use V2Unit\V2UnitList;
use Vector\Vector;

/**
 * Class V2FleetContainer
 *
 * Entity Properties
 * @property mixed      $ownerId
 * @property int|float  $departurePlanetId
 *
 * @property mixed      $arriveOwnerId
 * @property int|float  $arrivePlanetId
 *
 * @property int        $missionType
 * @property int        $status
 * @property int        $groupId
 *
 * @property Vector     $vectorDeparture
 * @property Vector     $vectorArrive
 *
 * @property int        $timeDeparture
 * @property int        $timeArrive
 * @property int        $timeComplete
 * @property int        $timeReturn
 *
 * @property int|float  $shipsCount
 * @property V2UnitList $units
 * @property V2Location $location
 *
 * Functionals
 * @property bool       $isReturning
 *
 * @property array      $owner
 * @property array      $departure
 * @property array      $target
 * @property array      $targetOwner
 *
 * @package V2Fleet
 */
class V2FleetContainer extends \Entity\KeyedContainer {
}
