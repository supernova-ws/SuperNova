<?php
/**
 * Created by Gorlum 17.08.2016 22:09
 */

namespace V2Fleet;


use V2Unit\V2UnitList;

/**
 * Class V2FleetContainer
 *
 * @property mixed      $dbId
 * @property mixed      $ownerId
 * @property int        $missionType
 * @property int        $timeDeparture
 * @property int        $timeArrive
 * @property int        $timeComplete
 * @property int        $timeReturn
 *
 * @property V2UnitList $units
 *
 * @package V2Fleet
 */
class V2FleetContainer extends \EntityContainer {

  public function __construct() {
  }

}
