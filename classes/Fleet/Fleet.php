<?php
/**
 * Created by Gorlum 18.04.2018 16:30
 */

namespace Fleet;


use Core\EntityDb;

/**
 * Class Fleet
 * @package Fleet
 *
 * @property int|string $id                       - bigint     -
 * @property int|string $ownerId                  - bigint     - Fleet player owner ID
 * @property int        $fleet_mission            - int        -
 * @property int|string $fleet_amount             - bigint     -
 * @property string     $fleet_array              - mediumtext -
 * @property int        $timeLaunch               - int        - Fleet launched from source planet (unix)
 * @property int        $timeArrive               - int        - Time fleet arrive to destination (unix)
 * @property int        $timeEndStay              - int        - Time when fleet operation on destination complete (if any) (unix)
 * @property int        $timeReturn               - int        - Time fleet would return to source planet (unix)
 * @property int|string $fleet_start_planet_id    - bigint     -
 * @property int        $fleet_start_galaxy       - int        -
 * @property int        $fleet_start_system       - int        -
 * @property int        $fleet_start_planet       - int        -
 * @property int        $fleet_start_type         - int        -
 * @property int|string $fleet_end_planet_id      - bigint     -
 * @property int        $fleet_end_galaxy         - int        -
 * @property int        $fleet_end_system         - int        -
 * @property int        $fleet_end_planet         - int        -
 * @property int        $fleet_end_type           - int        -
 * @property int|string $fleet_resource_metal     - decimal    -
 * @property int|string $fleet_resource_crystal   - decimal    -
 * @property int|string $fleet_resource_deuterium - decimal    -
 * @property int|string $fleet_target_owner       - int        -
 * @property int|string $fleet_group              - varchar    -
 * @property int        $status                   - int        - Current fleet status: flying to destination; returning
 */
class Fleet extends EntityDb {

  /**
   * @var string $_activeClass
   */
  protected $_activeClass = '\\Fleet\\RecordFleet';

  /**
   * @var RecordFleet $_container
   */
  protected $_container;

  protected $_containerTranslateNames = [
    'ownerId' => 'fleet_owner',

    'timeLaunch'  => 'start_time',
    'timeArrive'  => 'fleet_start_time',
    'timeEndStay' => 'fleet_end_stay',
    'timeReturn'  => 'fleet_end_time',

    'status' => 'fleet_mess',
  ];

  /**
   * Information about ships
   *
   * @var array[] $shipInfo
   */
  protected static $shipInfo = [];


  /**
   * Fleet constructor.
   *
   * @throws \Exception
   */
  public function __construct() {
    parent::__construct();
  }

  // Real fleet actions ------------------------------------------------------------------------------------------------

  /**
   * Forced return fleet to source planet
   *
   * @param int|string $byPlayerId
   *
   * @return bool
   */
  public function returnForce($byPlayerId) {
    if ($this->ownerId != $byPlayerId) {
      return false;
    }

    if ($this->status == FLEET_STATUS_RETURNING) {
      return true;
    }

//    $ReturnFlyingTime = ($this->timeEndStay != 0 && $this->timeArrive < SN_TIME_NOW ? $this->timeArrive : SN_TIME_NOW) - $this->timeLaunch + SN_TIME_NOW + 1;
    $timeToReturn = SN_TIME_NOW - $this->timeLaunch + 1;
    $ReturnFlyingTime = (!empty($this->timeEndStay) && $this->timeArrive < SN_TIME_NOW ? $this->timeArrive : SN_TIME_NOW) + $timeToReturn;

    // TODO - Those two lines should be removed - fleet times should be filtered on interface side
    $this->timeArrive = SN_TIME_NOW;
    !empty($this->timeEndStay) ? $this->timeEndStay = SN_TIME_NOW : false;

    $this->timeReturn = $ReturnFlyingTime;
    $this->status = FLEET_STATUS_RETURNING;

    return $this->dbUpdate();
  }

  // Service functions  -----------------------------------------------------------------------------------------------
  /**
   * @return RecordFleet
   */
  public function _getContainer() {
    return $this->_container;
  }

  /**
   * @param int $shipId
   *
   * @return array
   */
  protected static function getUnitInfo($shipId) {
    if (!isset(static::$shipInfo[$shipId])) {
      static::$shipInfo[$shipId] = get_unit_param($shipId);
    }

    return static::$shipInfo[$shipId];
  }

  /**
   * @param int $resourceId
   *
   * @return float[] - [(int)$shipId => (float)costInMetal]
   */
  public function getShipsBasicCosts($resourceId = RES_METAL) {
    $result = [];
    foreach ($this->getShipList() as $shipId => $shipAmount) {
      $result[$shipId] = getStackableUnitsCost([$shipId => 1], $resourceId);
    }

    return $result;
  }

  /**
   * Get cost of single ship in metal
   *
   * @param int $shipId
   *
   * @return int|float
   */
  public function getShipCostInMetal($shipId) {
    return getStackableUnitsCost([$shipId => 1], RES_METAL);
//
//    if(!isset(static::getUnitInfo($shipId)[P_COST_METAL])) {
//      static::$shipInfo[$shipId][P_COST_METAL] = get_unit_cost_in(static::getUnitInfo($shipId)[P_COST], RES_METAL);
//    }
//
//    return static::getUnitInfo($shipId)[P_COST_METAL];
  }

  /**
   * Get fleet cost in metal
   *
   * @return float|int
   */
  public function getCostInMetal() {
    return getStackableUnitsCost($this->getShipList(), RES_METAL);
//
//    $result = 0;
//    foreach($this->getShipList() as $shipId => $amount) {
//      $result += $amount * $this->getShipCostInMetal($shipId);
//    }
//
//    return $result;
  }

  /**
   * Get single ship basic capacity
   *
   * @param int $shipId
   *
   * @return int|mixed
   */
  public function getShipCapacity($shipId) {
    if (!isset(static::getUnitInfo($shipId)[P_CAPACITY])) {
      static::$shipInfo[$shipId][P_CAPACITY] = 0;
    }

    return static::getUnitInfo($shipId)[P_CAPACITY];
  }

  /**
   * Get current fleet capacity counting loaded resources and fuel
   *
   * @return float|int
   */
  public function getCapacityActual() {
    $result = 0;
    foreach ($this->getShipList() as $shipId => $amount) {
      $result += $amount * $this->getShipCapacity($shipId);
    }

    $result = max(0, $result - array_sum($this->getResourceList()));

    return $result;
  }

  public function isEmpty() {
    return parent::isEmpty();
  }

  // Using RecordFleet functions ---------------------------------------------------------------------------------------

  /**
   * @param int   $shipSnId
   * @param float $shipCount
   *
   * @throws \Exception
   */
  public function changeShipCount($shipSnId, $shipCount) {
    $this->_getContainer()->changeShipCount($shipSnId, $shipCount);
  }

  /**
   * @param int   $resourceId
   * @param float $resourceCount
   *
   * @throws \Exception
   */
  public function changeResource($resourceId, $resourceCount) {
    $this->_getContainer()->changeResource($resourceId, $resourceCount);
  }

  /**
   * @return float[] - [shipSnId => $shipAmount]
   */
  public function getShipList() {
    return $this->_getContainer()->getShipList();
  }

  /**
   * @return float[] - [$resourceSnId => $resourceAmount]
   */
  public function getResourceList() {
    return $this->_getContainer()->getResourceList();
  }

  /**
   * @return float|int
   */
  public function getShipCount() {
    return $this->_getContainer()->getShipCount();
  }

  /**
   * @param float $multiplier
   *
   * @return int[]|float[]
   */
  public function calcShipLossByMultiplier($multiplier) {
    $result = [];

    foreach ($this->getShipList() as $unit_id => $unit_amount) {
      $shipsLost = ceil($unit_amount * $multiplier);
      $result[$unit_id] += $shipsLost;
    }

    return $result;
  }

}
